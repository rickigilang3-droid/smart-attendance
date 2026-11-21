<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            // Nilai target: 'all' (Semua), 'teacher' (Khusus Guru), 'parent' (Khusus Orang Tua)
            $table->string('target')->default('all')->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn('target');
        });
    }
};