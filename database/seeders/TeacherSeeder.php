<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teachersData = [
            // GURU KELAS 10
            ['nip' => '197803122005011003', 'name' => 'Drs. H. Ahmad Fauzi, M.Pd.I', 'subject' => 'Pendidikan Agama', 'phone' => '081234567801'],
            ['nip' => '198205202008012004', 'name' => 'Siti Nurhayati, S.Pd., M.Pd', 'subject' => 'PPKn', 'phone' => '081234567802'],
            ['nip' => '198510142010012005', 'name' => 'Dra. Rina Marlina, M.Pd', 'subject' => 'Bahasa Indonesia', 'phone' => '081234567803'],
            ['nip' => '198001082006041006', 'name' => 'Budi Santoso, S.Pd., M.Si', 'subject' => 'Matematika', 'phone' => '081234567804'],
            ['nip' => '199003252015022007', 'name' => 'Dewi Anggraini, S.Pd., M.A', 'subject' => 'Bahasa Inggris', 'phone' => '081234567805'],
            ['nip' => '198307172009011008', 'name' => 'Agus Prasetyo, S.Si., M.Pd', 'subject' => 'Fisika', 'phone' => '081234567806'],
            ['nip' => '198711032011012009', 'name' => 'Lestari Wulandari, S.Pd., M.Si', 'subject' => 'Kimia', 'phone' => '081234567807'],
            ['nip' => '198409232009032010', 'name' => 'Dra. Maya Puspitasari, M.Pd', 'subject' => 'Biologi', 'phone' => '081234567808'],
            ['nip' => '198606112012011011', 'name' => 'Rudi Hartono, S.Pd., M.Hum', 'subject' => 'Sejarah', 'phone' => '081234567809'],
            ['nip' => '199201302019031012', 'name' => 'Eko Wahyudi, S.Pd., M.Si', 'subject' => 'Geografi', 'phone' => '081234567810'],
            ['nip' => '198902142014022013', 'name' => 'Ratna Sari, S.E., M.Pd', 'subject' => 'Ekonomi', 'phone' => '081234567811'],
            ['nip' => '199105062018012014', 'name' => 'Nurul Hidayati, S.Sos., M.Pd', 'subject' => 'Sosiologi', 'phone' => '081234567812'],
            ['nip' => '199306182020121015', 'name' => 'Fajar Nugroho, S.Kom., M.T', 'subject' => 'Informatika', 'phone' => '081234567813'],
            ['nip' => '198108092007011016', 'name' => 'Bambang Setiawan, S.Pd., M.Or', 'subject' => 'PJOK', 'phone' => '081234567814'],
            ['nip' => '198812272013012017', 'name' => 'Anisa Rahmawati, S.Sn., M.Pd', 'subject' => 'Seni Budaya', 'phone' => '081234567815'],

            // GURU KELAS 11
            ['nip' => '197605142003121001', 'name' => 'Drs. H. Mulyadi, M.Pd.I', 'subject' => 'Pendidikan Agama', 'phone' => '081234567816'],
            ['nip' => '198104172006042002', 'name' => 'Sri Wahyuni, S.Pd., M.Pd', 'subject' => 'PPKn', 'phone' => '081234567817'],
            ['nip' => '197909282005012003', 'name' => 'Dra. Endang Sulistyowati, M.Hum', 'subject' => 'Bahasa Indonesia', 'phone' => '081234567818'],
            ['nip' => '198402112009011004', 'name' => 'Hendra Gunawan, S.Pd., M.Pd', 'subject' => 'Matematika', 'phone' => '081234567819'],
            ['nip' => '198208192008011005', 'name' => 'Dr. Irfan Maulana, S.Si., M.Si', 'subject' => 'Matematika Tingkat Lanjut', 'phone' => '081234567820'],
            ['nip' => '198706052012022006', 'name' => 'Maria Susanti, S.S., M.Pd', 'subject' => 'Bahasa Inggris', 'phone' => '081234567821'],
            ['nip' => '198511302010011007', 'name' => 'Yusuf Ramadhan, S.Si., M.Pd', 'subject' => 'Fisika', 'phone' => '081234567822'],
            ['nip' => '198803222013022008', 'name' => 'Putri Handayani, S.Si., M.Sc', 'subject' => 'Kimia', 'phone' => '081234567823'],
            ['nip' => '198112082006042009', 'name' => 'Dian Kusumawati, S.Pd., M.Si', 'subject' => 'Biologi', 'phone' => '081234567824'],
            ['nip' => '198307252008011010', 'name' => 'Taufik Hidayat, S.Pd., M.Pd', 'subject' => 'Sejarah', 'phone' => '081234567825'],
            ['nip' => '199111142019031011', 'name' => 'Reza Pratama, S.T., M.Kom', 'subject' => 'Informatika', 'phone' => '081234567826'],
            ['nip' => '198609172011011012', 'name' => 'Wahyu Kurniawan, S.Pd., M.Pd', 'subject' => 'PJOK', 'phone' => '081234567827'],
            ['nip' => '199004032015022013', 'name' => 'Indah Permatasari, S.Sn., M.Sn', 'subject' => 'Seni Budaya', 'phone' => '081234567828'],

            // GURU KELAS 12
            ['nip' => '197412092000031001', 'name' => 'Drs. Hasan Basri, M.Pd.I', 'subject' => 'Pendidikan Agama', 'phone' => '081234567829'],
            ['nip' => '198003152005012002', 'name' => 'Yuliana Kartika, S.Pd., M.Pd', 'subject' => 'PPKn', 'phone' => '081234567830'],
            ['nip' => '197808222003122003', 'name' => 'Dra. Hj. Lilis Suryani, M.Pd', 'subject' => 'Bahasa Indonesia', 'phone' => '081234567831'],
            ['nip' => '198205062007011004', 'name' => 'Joko Susilo, S.Pd., M.Pd', 'subject' => 'Matematika', 'phone' => '081234567832'],
            ['nip' => '198101242006041005', 'name' => 'Dr. Andi Wijaya, S.Si., M.Si', 'subject' => 'Matematika Tingkat Lanjut', 'phone' => '081234567833'],
            ['nip' => '198909122014022006', 'name' => 'Clara Anindita, S.Pd., M.A', 'subject' => 'Bahasa Inggris', 'phone' => '081234567834'],
            ['nip' => '198304182008011007', 'name' => 'Slamet Riyadi, S.Si., M.Pd', 'subject' => 'Fisika', 'phone' => '081234567835'],
            ['nip' => '198507292010012008', 'name' => 'Fitriani Lubis, S.Si., M.Pd', 'subject' => 'Kimia', 'phone' => '081234567836'],
            ['nip' => '198206102007022009', 'name' => 'Dr. Ratih Kumalasari, M.Si', 'subject' => 'Biologi', 'phone' => '081234567837'],
            ['nip' => '198412032009011010', 'name' => 'Hendri Saputra, S.Pd., M.Hum', 'subject' => 'Sejarah', 'phone' => '081234567838'],
            ['nip' => '199207212018011011', 'name' => 'Galih Permana, S.Kom., M.T', 'subject' => 'Informatika', 'phone' => '081234567839'],
            ['nip' => '198710252012011012', 'name' => 'Doni Firmansyah, S.Pd., M.Pd', 'subject' => 'PJOK', 'phone' => '081234567840'],
            ['nip' => '198602082011012013', 'name' => 'Retno Palupi, S.Pd., M.M', 'subject' => 'Prakarya & Kewirausahaan', 'phone' => '081234567841'],
        ];

        foreach ($teachersData as $item) {
            // 1. Buat/Update Akun User untuk Login
            $user = User::updateOrCreate(
                ['email' => $item['nip'] . '@sekolah.sch.id'],
                [
                    'name'     => $item['name'],
                    'password' => Hash::make('password'),
                    'role_id'  => 2, // Menggunakan role_id untuk Teacher
                ]
            );

            // 2. Buat/Update Data Guru
            Teacher::updateOrCreate(
                ['nip' => $item['nip']],
                [
                    'name'    => $item['name'],
                    'subject' => $item['subject'],
                    'phone'   => $item['phone'],
                    'user_id' => $user->id,
                ]
            );
        }
    }
}