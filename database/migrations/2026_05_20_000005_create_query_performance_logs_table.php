<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Stores slow-query telemetry for ongoing monitoring.
 * Populated by App\Listeners\LogSlowQueries (DB::listen in AppServiceProvider).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('query_performance_logs', function (Blueprint $table) {
            $table->id();
            $table->text('sql');
            $table->json('bindings')->nullable();
            $table->float('time_ms'); // milliseconds
            $table->string('connection_name', 64)->nullable();
            $table->string('route', 191)->nullable();
            $table->timestamp('logged_at')->useCurrent();

            $table->index('time_ms');
            $table->index('logged_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('query_performance_logs');
    }
};
