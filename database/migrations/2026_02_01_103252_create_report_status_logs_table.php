<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('report_status_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('report_id')
                ->constrained('reports')
                ->cascadeOnDelete();

            $table->enum('old_status', ['pending', 'verified', 'rejected']);
            $table->enum('new_status', ['pending', 'verified', 'rejected']);

            $table->foreignId('changed_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_status_logs');
    }
};
