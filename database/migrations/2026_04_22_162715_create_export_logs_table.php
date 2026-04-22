<?php
// database/migrations/xxxx_xx_xx_create_export_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('export_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('export_type'); // orders, customer_orders, financial, revenue_summary, tax_report
            $table->string('format'); // csv, xlsx, pdf
            $table->json('filters')->nullable();
            $table->string('file_path')->nullable();
            $table->integer('total_records')->default(0);
            $table->string('status')->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('export_logs');
    }
};