<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            // Menambahkan relasi wali kelas (teacher_id) yang terhubung ke tabel teachers
            $table->foreignId('teacher_id')->nullable()->after('grade_level')->constrained('teachers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropColumn('teacher_id');
        });
    }
};