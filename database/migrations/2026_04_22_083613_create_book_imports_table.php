<?php
// database/migrations/xxxx_create_book_imports_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('book_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('filename');
            $table->string('original_name');
            $table->integer('total_rows')->default(0);
            $table->integer('processed_rows')->default(0);
            $table->integer('successful_rows')->default(0);
            $table->integer('failed_rows')->default(0);
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->json('errors')->nullable();
            $table->json('duplicate_handling')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('book_import_errors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_import_id')->constrained()->onDelete('cascade');
            $table->integer('row_number');
            $table->json('row_data');
            $table->json('errors');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('book_import_errors');
        Schema::dropIfExists('book_imports');
    }
};