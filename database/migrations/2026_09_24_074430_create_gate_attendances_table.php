<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gate_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->date('date');
            $table->time('scan_in')->nullable();
            $table->time('scan_out')->nullable();
            $table->enum('status', ['present', 'late', 'permit', 'sick', 'alpha'])->default('present');
            $table->string('device_id')->nullable(); // ID/IP Mesin Fingerprint
            $table->boolean('notified_parent')->default(false); // Status terkirim ke WA Ortu
            $table->timestamps();

            $table->unique(['student_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gate_attendances');
    }
};