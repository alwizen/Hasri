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
        Schema::create('attendance_students', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('attendance_date');

            $table->dateTime('check_in_at');
            $table->dateTime('check_out_at')->nullable();

            $table->enum('status', ['masuk', 'izin', 'absen', 'terlambat'])
                ->default('masuk');

            $table->string('permission_note')->nullable();

            $table->unique(['student_id', 'attendance_date']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_students');
    }
};
