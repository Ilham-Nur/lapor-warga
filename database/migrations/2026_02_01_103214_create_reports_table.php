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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('report_type_id')
                ->constrained('report_types')
                ->cascadeOnDelete();

            $table->dateTime('occurred_at');
            $table->text('description')->nullable();

            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('address_text')->nullable();

            $table->enum('status', ['pending', 'verified', 'rejected'])
                ->default('pending');

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('reviewed_at')->nullable();
            $table->text('review_note')->nullable();

            $table->string('reporter_ip')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
