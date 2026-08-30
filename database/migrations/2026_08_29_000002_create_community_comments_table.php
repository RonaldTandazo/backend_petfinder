<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MongoDB\Laravel\Schema\Blueprint;

return new class extends Migration
{
    protected $connection = 'mongodb';

    public function up(): void
    {
        Schema::connection('mongodb')->create('community_comments', function (Blueprint $collection) {
            $collection->index(['post_id' => 1, 'created_at' => 1]);
            $collection->index(['parent_id' => 1]);
            $collection->index(['author.tutor_id' => 1]);
        });
    }

    public function down(): void
    {
        Schema::connection('mongodb')->drop('community_comments');
    }
};
