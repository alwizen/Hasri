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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            // Relasi ke teacher
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');

            // Absen
            $table->date('date');                     
            $table->time('check_in')->nullable();     
            $table->time('check_out')->nullable();   

            // Keterangan
            $table->boolean('is_late')->default(false);
            $table->integer('late_minutes')->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
