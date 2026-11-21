<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'teacher_id',  // Terikat spesifik per guru
        'date',
        'subject_name',
        'status',
        'notes',
        'recorded_by',
        'permit_id',   // Relasi ke surat izin
    ];

    /**
     * Relasi ke Siswa
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Relasi ke Kelas
     */
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Relasi ke Guru
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    /**
     * Relasi ke User/Guru yang mencatat
     */
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Relasi ke Surat Izin/Sakit dari Portal Parent (Model Permission)
     */
    public function permit()
    {
        return $this->belongsTo(Permission::class, 'permit_id');
    }
}