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
        Schema::create('pet_health_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->nullable()->constrained('pets')->cascadeOnDelete();
            $table->foreignId('health_condition_id')->nullable()->constrained('health_conditions')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pet_health_conditions');
    }
};
