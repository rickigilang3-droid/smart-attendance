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
        Schema::create('teaching_journals', function (Blueprint $table) {
            $table->id();
            
            // Foreign key ke tabel teachers
            $table->unsignedBigInteger('teacher_id');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            
            // Kolom class_id tanpa constraint kaku
            $table->unsignedBigInteger('class_id');
            
            $table->date('date');
            $table->string('subject')->nullable(); // Mata Pelajaran
            $table->text('topic')->nullable(); // Materi / Pokok Pembahasan
            $table->text('activities_summary')->nullable(); // Catatan Berita Acara / Ringkasan Kegiatan
            $table->string('photo_evidence')->nullable(); // Path foto bukti mengajar
            $table->timestamps();

            // Mencegah duplikasi jurnal mengajar untuk guru & kelas di tanggal yang sama
            $table->unique(['teacher_id', 'class_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teaching_journals');
    }
};