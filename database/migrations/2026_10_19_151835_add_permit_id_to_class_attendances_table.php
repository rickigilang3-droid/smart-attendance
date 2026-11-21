<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('class_attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('class_attendances', 'permit_id')) {
                // Relasi ke tabel permissions
                $table->foreignId('permit_id')
                      ->nullable()
                      ->after('status')
                      ->constrained('permissions')
                      ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_attendances', function (Blueprint $table) {
            if (Schema::hasColumn('class_attendances', 'permit_id')) {
                // Hapus foreign key jika ada, lalu hapus kolomnya
                $table->dropForeign(['permit_id']);
                $table->dropColumn('permit_id');
            }
        });
    }
};