<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_attendances', function (Blueprint $table) {
            // Mengubah status menjadi string agar mendukung nilai 'TDK'
            $table->string('status')->change();
        });
    }

    public function down(): void
    {
        Schema::table('class_attendances', function (Blueprint $table) {
            $table->string('status')->change();
        });
    }
};