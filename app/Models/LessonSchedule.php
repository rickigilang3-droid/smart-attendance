<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Teacher;
use App\Models\SchoolClass;

class LessonSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'school_class_id',
        'teacher_id',
        'subject',
        'day',
        'start_time',
        'end_time',
    ];

    /**
     * Relasi ke Model SchoolClass (Kelas)
     */
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Relasi ke Model Teacher (Guru Pengajar)
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }
}