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
        Schema::create('account_deletions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_id')->nullable()->constrained('tutors')->nullOnDelete();
            $table->timestamp('requested_at');
            $table->timestamp('schedule_deletion_at');
            $table->foreignId('deletion_status_id')->default(1)->constrained('deletion_statuses')->cascadeOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'scheduled_deletion_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_deletions');
    }
};
