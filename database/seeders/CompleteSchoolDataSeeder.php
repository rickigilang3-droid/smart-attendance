<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Role;

class CompleteSchoolDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tahun Ajaran
        $academicYear = DB::table('academic_years')->where('year', '2025/2026')->where('semester', 'ganjil')->first();
        if (!$academicYear) {
            $ayId = DB::table('academic_years')->insertGetId([
                'year' => '2025/2026',
                'semester' => 'ganjil',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $ayId = $academicYear->id;
        }

        // 2. Data Kelas (10, 11, 12 MIPA/IPS)
        $classesData = [
            ['name' => 'X MIPA 1', 'grade_level' => '10'],
            ['name' => 'X MIPA 2', 'grade_level' => '10'],
            ['name' => 'X IPS 1', 'grade_level' => '10'],
            ['name' => 'XI MIPA 1', 'grade_level' => '11'],
            ['name' => 'XI IPS 1', 'grade_level' => '11'],
            ['name' => 'XII MIPA 1', 'grade_level' => '12'],
            ['name' => 'XII IPS 1', 'grade_level' => '12'],
        ];

        $teachers = Teacher::all();
        $teacherIndex = 0;

        $createdClasses = [];
        foreach ($classesData as $c) {
            $waliKelasId = isset($teachers[$teacherIndex]) ? $teachers[$teacherIndex]->id : null;
            $teacherIndex++;

            $class = SchoolClass::firstOrCreate(
                ['name' => $c['name']],
                [
                    'grade_level' => $c['grade_level'],
                    'teacher_id' => $waliKelasId,
                ]
            );
            $createdClasses[] = $class;
        }

        // 3. Hubungkan Akun Orang Tua yang sudah ada (parent@school.com / ID 5)
        $parentUser = User::where('email', 'parent@school.com')->first();

        // Ambil daftar foto siswa yang sudah ada di storage
        $photoFiles = glob(storage_path('app/public/students-photos/*.jpg'));
        $photos = array_map(function($p) {
            return 'students-photos/' . basename($p);
        }, $photoFiles);

        $firstClass = $createdClasses[0];

        // Buat Siswa Utama yang terhubung ke Akun Orang Tua (parent@school.com)
        $primaryStudent = Student::firstOrCreate(
            ['nisn' => '0061234567'],
            [
                'name' => 'Ahmad Rizki Pratama',
                'class_id' => $firstClass->id,
                'registration_type' => 'Siswa Baru',
                'status' => 'aktif',
                'gender' => 'L',
                'nis' => '102501',
                'nik' => '3171012345670001',
                'pob' => 'Jakarta',
                'dob' => '2008-05-15',
                'religion' => 'Islam',
                'nationality' => 'Indonesia',
                'address' => 'Jl. Kebon Jeruk No. 12, Jakarta Barat',
                'father_name' => 'H. Pratama Wijaya',
                'mother_name' => 'Siti Rahmawati',
                'parent_phone' => '081234567890',
                'photo' => $photos[0] ?? null,
            ]
        );

        // Pastikan tabel parents memetakan parentUser ke primaryStudent
        if ($parentUser) {
            DB::table('parents')->updateOrInsert(
                ['user_id' => $parentUser->id, 'student_id' => $primaryStudent->id],
                ['pin_code' => '123456', 'updated_at' => now(), 'created_at' => now()]
            );
        }

        // 4. Tambahkan Siswa Lainnya sesuai foto-foto yang ada di storage (100+ siswa)
        $sampleNames = [
            'Aditya Nugraha', 'Annisa Zahra', 'Bintang Ramadhan', 'Citra Lestari', 'Dimas Anggara',
            'Fadhil Maulana', 'Gita Gutawa', 'Hafiz Syahrin', 'Indah Permata', 'Joko Susilo',
            'Kartika Putri', 'Lukman Hakim', 'Maulana Malik', 'Nabila Syakieb', 'Oki Setiana',
            'Panji Petualang', 'Qonita Rahma', 'Rian D’Masiv', 'Salma Salsabil', 'Tegar Septian',
            'Umar Bakri', 'Vina Panduwinata', 'Wawan Hendrawan', 'Yanti Yuliana', 'Zack Lee',
            'Alfi Syahrin', 'Bella Shofie', 'Cakra Khan', 'Denny Caknan', 'Ernando Ari'
        ];

        $studentCount = count($photos);
        for ($i = 1; $i < min(40, $studentCount); $i++) {
            $assignedClass = $createdClasses[$i % count($createdClasses)];
            $name = $sampleNames[$i % count($sampleNames)] . ' ' . chr(65 + ($i % 26));
            $nisn = str_pad((string)(61234567 + $i), 10, '0', STR_PAD_LEFT);
            $nis = (string)(102501 + $i);

            Student::firstOrCreate(
                ['nisn' => $nisn],
                [
                    'name' => $name,
                    'class_id' => $assignedClass->id,
                    'registration_type' => 'Siswa Baru',
                    'status' => 'aktif',
                    'gender' => ($i % 2 === 0) ? 'L' : 'P',
                    'nis' => $nis,
                    'nik' => '3171012345' . str_pad((string)$i, 6, '0', STR_PAD_LEFT),
                    'pob' => 'Jakarta',
                    'dob' => '2008-01-10',
                    'religion' => 'Islam',
                    'nationality' => 'Indonesia',
                    'address' => 'Jakarta',
                    'father_name' => 'Wali Siswa ' . $i,
                    'mother_name' => 'Ibu Siswa ' . $i,
                    'parent_phone' => '08129876' . str_pad((string)$i, 4, '0', STR_PAD_LEFT),
                    'photo' => $photos[$i] ?? null,
                ]
            );
        }

        // 5. Struktur Organisasi Sekolah (menggunakan 8 foto organization di storage)
        $orgPhotos = glob(storage_path('app/public/organization/*.jpg'));
        $orgPhotoNames = array_map(function($p) {
            return 'organization/' . basename($p);
        }, $orgPhotos);

        $orgPositions = [
            ['name' => 'Dr. H. Ahmad Dahlan, M.Pd', 'position_title' => 'Kepala Sekolah', 'level' => 'kepala_sekolah', 'order_priority' => 1],
            ['name' => 'Drs. H. Mulyadi, M.Pd.I', 'position_title' => 'Komite Sekolah', 'level' => 'komite', 'order_priority' => 2],
            ['name' => 'Hj. Farida Hanum, S.Pd', 'position_title' => 'Kepala Tata Usaha', 'level' => 'sub_pimpinan', 'order_priority' => 3],
            ['name' => 'Budi Santoso, S.Pd., M.Si', 'position_title' => 'Wakasek Kurikulum', 'level' => 'waka', 'order_priority' => 4],
            ['name' => 'Drs. H. Ahmad Fauzi, M.Pd.I', 'position_title' => 'Wakasek Kesiswaan', 'level' => 'waka', 'order_priority' => 5],
            ['name' => 'Agus Prasetyo, S.Si., M.Pd', 'position_title' => 'Wakasek Sarana Prasarana', 'level' => 'waka', 'order_priority' => 6],
            ['name' => 'Dra. Rina Marlina, M.Pd', 'position_title' => 'Wakasek Humas', 'level' => 'waka', 'order_priority' => 7],
            ['name' => 'Dewi Anggraini, S.Pd., M.A', 'position_title' => 'Koordinator BK', 'level' => 'bk', 'order_priority' => 8],
        ];

        foreach ($orgPositions as $idx => $org) {
            DB::table('organization_structures')->updateOrInsert(
                ['position_title' => $org['position_title']],
                [
                    'name' => $org['name'],
                    'level' => $org['level'],
                    'photo' => $orgPhotoNames[$idx] ?? null,
                    'order_priority' => $org['order_priority'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
