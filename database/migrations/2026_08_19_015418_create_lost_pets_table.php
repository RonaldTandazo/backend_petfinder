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
            $table->string('name');
            $table->string('race')->nullable();
            $table->string('color')->nullable();
            $table->text('description')->nullable();
            $table->string('phone_home', 10)->nullable();
            $table->string('phone_mobile', 15)->nullable();
            $table->foreignId('report_type_id')->default(1)->constrained('report_types')->cascadeOnDelete();
            $table->foreignId('species_id')->constrained('species')->cascadeOnDelete();
            $table->foreignId('animal_gender_id')->constrained('animal_genders')->cascadeOnDelete();
            $table->foreignId('size_id')->constrained('sizes')->cascadeOnDelete();
            $table->boolean('has_reward')->default(false);
            $table->decimal('reward_amount', 10,2)->nullable();
            $table->string('city');
            $table->string('event_address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamp('event_date');
            $table->foreignId('report_status_id')->default(1)->constrained('report_statuses')->cascadeOnDelete();
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
