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
    Schema::create('permissions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
        $table->foreignId('parent_user_id')->constrained('users')->onDelete('cascade');
        $table->enum('type', ['Sakit', 'Izin']);
        $table->date('start_date');
        $table->date('end_date');
        $table->text('reason');
        $table->string('letter_file');
        $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
