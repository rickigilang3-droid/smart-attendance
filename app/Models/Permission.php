<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function parentUser()
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }

    /**
     * Relasi ke Absensi Kelas
     */
    public function classAttendance()
    {
        return $this->hasOne(ClassAttendance::class, 'permit_id');
    }
}