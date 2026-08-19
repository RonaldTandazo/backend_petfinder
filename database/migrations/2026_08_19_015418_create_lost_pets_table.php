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
        Schema::create('lost_pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_id')->constrained('tutors')->cascadeOnDelete();
            $table->foreignId('pet_id')->nullable()->constrained('pets')->nullOnDelete();
            $table->foreignId('report_type_id')->constrained('report_types')->cascadeOnDelete();
            $table->string('name');
            $table->foreignId('species_id')->constrained('species')->cascadeOnDelete();
            $table->string('race')->nullable();
            $table->string('color')->nullable();
            $table->text('description')->nullable();
            $table->string('city');
            $table->string('event_address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamp('event_date');
            $table->foreignId('report_status_id')->constrained('report_statuses')->cascadeOnDelete();
            $table->timestamp('closing_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lost_pets');
    }
};
