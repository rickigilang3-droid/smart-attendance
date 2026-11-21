<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $table = 'announcements';

    protected $fillable = [
        'title',
        'category',
        'target', // Targets: 'all' (Semua), 'teacher' (Khusus Guru), 'parent' (Khusus Orang Tua)
        'content',
        'date',
        'is_active',
    ];
}