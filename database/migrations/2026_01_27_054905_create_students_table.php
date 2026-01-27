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
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->string('nis')->unique();
            $table->string('full_name');

            // Relation
            $table->foreignId('class_room_id')
                ->constrained('class_rooms')
                ->cascadeOnDelete()
                ->index();

            // Contact
            $table->text('address')->nullable();
            $table->string('phone')->nullable();

            // Status
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
