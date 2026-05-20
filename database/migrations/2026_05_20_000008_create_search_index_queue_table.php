<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pending Scout indexing jobs (database queue driver fallback).
 *
 *   Tracks the model id, action (index / delete) and number of attempts.
 *   Allows visibility into the indexing pipeline outside of Redis.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('search_index_queue', function (Blueprint $table) {
            $table->id();
            $table->string('model_class', 191);
            $table->unsignedBigInteger('model_id');
            $table->string('action', 16)->default('index'); // index | delete
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('available_at')->nullable();
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['model_class', 'model_id']);
            $table->index('available_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_index_queue');
    }
};
