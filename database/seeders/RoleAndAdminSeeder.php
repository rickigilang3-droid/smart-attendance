<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RoleAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Insert Data Roles
        $roles = [
            ['id' => 1, 'name' => 'Admin', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Teacher', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Principal', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Parent', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('roles')->insertOrIgnore($roles);

        // 2. Insert Akun Super Admin Pertama
        DB::table('users')->insertOrIgnore([
            'role_id'      => 1, // ID Role Admin
            'name'         => 'Super Admin',
            'email'        => 'admin@school.com',
            'phone_number' => '081234567890',
            'password'     => Hash::make('password123'), // Password default: password123
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }
}