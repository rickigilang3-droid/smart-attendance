<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->date('date');
            $table->string('subject_name')->nullable(); // Nama Mata Pelajaran
            $table->string('status')->default('Hadir'); // Menggunakan string agar menampung Hadir, Sakit, Izin, Alpa, TDK
            $table->text('notes')->nullable(); // Keterangan / Alasan TDK
            $table->foreignId('recorded_by')->nullable()->constrained('users'); // ID Guru
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_attendances');
    }
};