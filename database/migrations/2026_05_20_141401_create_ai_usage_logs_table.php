<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 64);
            $table->string('feature', 64);
            $table->nullableMorphs('loggable');
            $table->unsignedInteger('tokens_used')->default(0);
            $table->decimal('cost_estimate', 8, 6)->default(0);
            $table->string('response_hash', 16)->nullable();
            $table->text('metadata')->nullable();
            $table->timestamps();

            $table->index(['provider', 'feature', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_usage_logs');
    }
};
