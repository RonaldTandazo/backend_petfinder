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
        Schema::create('lost_pet_event_pictures', function (Blueprint $table) {
            $table->id();
             $table->foreignId('lost_pet_event_id')->constrained('lost_pet_events')->cascadeOnDelete();
            $table->string('picture');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lost_pet_event_pictures');
    }
};
