<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_structures', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Lengkap & Gelar Personalia
            $table->string('position_title'); // Nama Jabatan (contoh: Kepala Sekolah, Waka Kurikulum, dll)
            $table->enum('level', [
                'komite', 
                'kepala_sekolah', 
                'sub_pimpinan', // Kepala TU / Penjaminan Mutu
                'waka', // Waka Urusan (Kesiswaan/Kurikulum/Sarpras/Humas)
                'bk', // Bimbingan Konseling
                'wali_kelas', 
                'guru_mapel'
            ])->default('guru_mapel');
            $table->string('photo')->nullable(); // Foto Personalia
            $table->integer('order_priority')->default(0); // Urutan Tampilan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_structures');
    }
};