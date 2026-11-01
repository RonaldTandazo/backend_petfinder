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
        Schema::create('legal_document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legal_document_id')->constrained('legal_documents')->cascadeOnDelete();
            $table->string('version', 50);
            $table->text('md_path')->nullable();
            $table->text('pdf_path')->nullable();
            $table->timestamp('published_at');
            $table->timestamps();

            $table->unique(
                ['legal_document_id', 'version'],
                'legal_document_version_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_document_versions');
    }
};
