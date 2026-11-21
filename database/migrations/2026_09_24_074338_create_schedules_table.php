<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('day')->comment('Senin, Selasa, dll.');
            $table->time('time_in')->default('07:00:00');
            $table->time('time_late')->default('07:15:00'); // Batas toleransi keterlambatan
            $table->time('time_out')->default('15:00:00');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};