<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->string('subject_name');
            $table->string('academic_year')->default('2025/2026');
            $table->string('semester')->default('Ganjil'); // Ganjil / Genap
            
            // Komponen Nilai (0 - 100)
            $table->decimal('assignment_score', 5, 2)->default(0); // Nilai Tugas
            $table->decimal('quiz_score', 5, 2)->default(0);       // Nilai Formatif / UH
            $table->decimal('mid_score', 5, 2)->default(0);        // Nilai UTS / PTS
            $table->decimal('final_score', 5, 2)->default(0);      // Nilai UAS / PAS
            
            // Kalkulasi Hasil
            $table->decimal('final_grade', 5, 2)->default(0);      // Nilai Akhir Terhitung
            $table->string('letter_grade', 2)->nullable();         // A, B, C, D
            $table->text('teacher_notes')->nullable();             // Catatan Capaian Kompetensi
            
            // Status Pengiriman
            $table->enum('status', ['draft', 'submitted'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};