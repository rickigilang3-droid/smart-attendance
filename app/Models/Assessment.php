<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'teacher_id',
        'class_id',
        'subject_name',
        'academic_year',
        'semester',
        'assignment_score',
        'quiz_score',
        'mid_score',
        'final_score',
        'final_grade',
        'letter_grade',
        'teacher_notes',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
}