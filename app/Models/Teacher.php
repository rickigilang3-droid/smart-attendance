<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'nip',
        'name',
        'subject',
        'phone',
        'photo', // Menampung path foto profil guru
        'user_id',
    ];

    /**
     * Accessor untuk mendapatkan URL Foto / Avatar Default
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
                return $this->photo;
            }

            if (function_exists('storage_media_url') && (config('services.supabase.url') || env('SUPABASE_URL'))) {
                return storage_media_url($this->photo);
            }

            if (Storage::disk('public')->exists($this->photo)) {
                return asset('storage/'.$this->photo);
            }
        }

        // Default Avatar menggunakan inisial nama guru
        return 'https://ui-avatars.com/api/?name='.urlencode($this->name ?? 'Guru').'&color=4F46E5&background=EEF2FF';
    }

    /**
     * Relasi balik ke Model User (Akun Login)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Jadwal Pelajaran
     */
    public function lessonSchedules(): HasMany
    {
        return $this->hasMany(LessonSchedule::class, 'teacher_id');
    }

    /**
     * Relasi ke Jurnal Mengajar / Berita Acara KBM
     */
    public function teachingJournals(): HasMany
    {
        return $this->hasMany(TeachingJournal::class, 'teacher_id');
    }

    /**
     * Relasi sebagai Wali Kelas dari satu Kelas (HasOne)
     */
    public function homeroomClass(): HasOne
    {
        return $this->hasOne(SchoolClass::class, 'teacher_id');
    }

    /**
     * @return Collection<int, int>
     */
    public function assignedClassIds(): Collection
    {
        $classIds = $this->lessonSchedules()->pluck('class_id');
        $homeroomClassId = $this->homeroomClass()->value('id');

        if ($homeroomClassId !== null) {
            $classIds->push($homeroomClassId);
        }

        return $classIds->unique()->values();
    }
}
