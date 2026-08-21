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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_id')->constrained('tutors')->cascadeOnDelete();
            $table->string('name');
            $table->foreignId('species_id')->constrained('species')->cascadeOnDelete();
            $table->string('race')->nullable();
            $table->string('color')->nullable();
            $table->date('born_date')->nullable();
            $table->foreignId('animal_gender_id')->constrained('animal_genders')->cascadeOnDelete();
            $table->foreignId('size_id')->constrained('sizes')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->foreignId('pet_status_id')->default(1)->constrained('pet_statuses')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
