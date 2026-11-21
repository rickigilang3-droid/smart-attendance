<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Roles
        $adminRole     = Role::firstOrCreate(['name' => 'Admin']);
        $teacherRole   = Role::firstOrCreate(['name' => 'Teacher']);
        $principalRole = Role::firstOrCreate(['name' => 'Principal']);
        $parentRole    = Role::firstOrCreate(['name' => 'Parent']);

        // 2. Buat Akun Admin
        User::updateOrCreate(
            ['email' => 'admin@school.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password123'),
                'role_id'  => $adminRole->id,
            ]
        );

        // 3. Buat Akun Guru (Teacher)
        User::updateOrCreate(
            ['email' => 'teacher@school.com'],
            [
                'name'     => 'Budi Santoso, S.Pd',
                'password' => Hash::make('password123'),
                'role_id'  => $teacherRole->id,
            ]
        );

        // 4. Buat Akun Kepala Sekolah (Principal)
        User::updateOrCreate(
            ['email' => 'principal@school.com'],
            [
                'name'     => 'Dr. H. Ahmad Dahlan, M.Pd',
                'password' => Hash::make('password123'),
                'role_id'  => $principalRole->id,
            ]
        );

        // 5. Buat Akun Orang Tua (Parent)
        User::updateOrCreate(
            ['email' => 'parent@school.com'],
            [
                'name'     => 'Siti Rahmawati (Orang Tua)',
                'password' => Hash::make('password123'),
                'role_id'  => $parentRole->id,
            ]
        );
    }
}