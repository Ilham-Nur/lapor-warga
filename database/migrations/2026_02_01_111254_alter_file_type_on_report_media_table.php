<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_media', function (Blueprint $table) {
            $table->string('file_type', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('report_media', function (Blueprint $table) {
            $table->string('file_type', 10)->change();
        });
    }
};
