<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'grade_level',
        'teacher_id',          // Disimpan untuk mencatat wali kelas
        'homeroom_teacher_id', // Alternatif nama kolom wali kelas di DB
    ];

    /**
     * Relasi satu kelas memiliki banyak siswa (HasMany)
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    /**
     * Relasi kelas terhubung ke satu Wali Kelas / Guru (BelongsTo)
     */
    public function homeroomTeacher()
    {
        $foreignKey = 'teacher_id';

        if (Schema::hasColumn('classes', 'homeroom_teacher_id')) {
            $foreignKey = 'homeroom_teacher_id';
        }

        return $this->belongsTo(Teacher::class, $foreignKey);
    }

    /**
     * Alias relasi teacher() jika dibutuhkan di modul lain
     */
    public function teacher()
    {
        return $this->homeroomTeacher();
    }
}