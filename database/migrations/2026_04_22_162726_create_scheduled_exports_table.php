<?php
// database/migrations/xxxx_xx_xx_create_scheduled_exports_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('scheduled_exports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // daily_sales, weekly_summary, monthly_report
            $table->string('format')->default('csv');
            $table->json('filters')->nullable();
            $table->string('schedule'); // daily, weekly, monthly
            $table->json('recipients'); // email addresses
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('scheduled_exports');
    }
};