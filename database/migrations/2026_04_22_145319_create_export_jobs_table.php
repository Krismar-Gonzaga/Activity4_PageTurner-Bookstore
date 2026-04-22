<?php
// database/migrations/xxxx_create_export_jobs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('export_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('filename')->nullable();
            $table->string('file_path')->nullable();
            $table->string('format');
            $table->json('filters')->nullable();
            $table->json('selected_fields');
            $table->integer('total_records')->default(0);
            $table->integer('processed_records')->default(0);
            $table->float('progress')->default(0);
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->text('error_message')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('export_jobs');
    }
};