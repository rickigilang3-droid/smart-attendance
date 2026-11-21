<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    // Izinkan semua kolom diisi secara otomatis kecuali ID
    protected $guarded = ['id'];

    /**
     * Relasi ke Kelas
     */
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Relasi ke User (Akun Login Orang Tua)
     */
    public function parentUser()
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }
}