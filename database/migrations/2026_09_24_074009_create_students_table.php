<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            
            // Registrasi & Kelas
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->string('registration_type')->default('Siswa Baru'); // Siswa Baru, Pindahan
            $table->string('previous_school')->nullable();
            $table->string('ijazah_number')->nullable();
            $table->string('status')->default('aktif'); // aktif, mutasi, lulus, non-aktif

            // 1. Data Pribadi
            $table->string('name');
            $table->string('nickname')->nullable();
            $table->enum('gender', ['L', 'P']);
            $table->string('nisn')->unique()->nullable();
            $table->string('nis')->nullable();
            $table->string('nik')->nullable();
            $table->string('no_kk')->nullable();
            $table->string('pob')->nullable(); // Tempat lahir
            $table->date('dob')->nullable(); // Tanggal lahir
            $table->string('religion')->nullable();
            $table->string('nationality')->default('Indonesia');
            $table->text('address')->nullable();
            $table->string('rt_rw')->nullable();
            $table->string('subdistrict')->nullable(); // Kelurahan
            $table->string('district')->nullable(); // Kecamatan
            $table->string('city')->nullable(); // Kabupaten/Kota
            $table->string('postal_code')->nullable();
            $table->string('coordinates')->nullable(); // Lintang/Bujur
            $table->string('residence_type')->default('Bersama Orang Tua');
            $table->string('transportation')->nullable();
            $table->integer('child_order')->nullable();
            $table->integer('siblings_count')->nullable();
            $table->string('special_needs')->nullable();
            $table->string('photo')->nullable();

            // 2. Data Periodik
            $table->integer('height')->nullable(); // cm
            $table->integer('weight')->nullable(); // kg
            $table->integer('head_circumference')->nullable(); // cm
            $table->decimal('distance_to_school', 5, 2)->nullable(); // km
            $table->integer('travel_time_minutes')->nullable();

            // 3. Data Orang Tua & Wali
            // Ayah
            $table->string('father_name')->nullable();
            $table->string('father_nik')->nullable();
            $table->string('father_birth_year')->nullable();
            $table->string('father_education')->nullable();
            $table->string('father_job')->nullable();
            $table->string('father_income')->nullable();
            $table->string('father_status')->default('Hidup');
            // Ibu
            $table->string('mother_name')->nullable();
            $table->string('mother_nik')->nullable();
            $table->string('mother_birth_year')->nullable();
            $table->string('mother_education')->nullable();
            $table->string('mother_job')->nullable();
            $table->string('mother_income')->nullable();
            $table->string('mother_status')->default('Hidup');
            // Wali (Opsional)
            $table->string('guardian_name')->nullable();
            $table->string('guardian_nik')->nullable();
            $table->string('guardian_job')->nullable();

            // 4. Data Kontak
            $table->string('parent_phone')->nullable();
            $table->string('student_phone')->nullable();
            $table->string('email')->nullable();

            // 6. Data Kesejahteraan & Prestasi
            $table->string('kip_number')->nullable();
            $table->string('pkh_kps_number')->nullable();
            $table->text('achievements')->nullable();
            $table->text('scholarships')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};