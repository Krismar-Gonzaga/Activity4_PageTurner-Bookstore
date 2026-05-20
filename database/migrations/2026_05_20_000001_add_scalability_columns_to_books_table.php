<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the columns required for the advanced scalability feature-set
 *  - is_active  : soft activation flag (used by Scout's shouldBeSearchable)
 *  - format     : hardcover / paperback / ebook / audiobook
 *  - published_at: timestamp – used for range partitioning by year
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('language');
            }
            if (!Schema::hasColumn('books', 'format')) {
                $table->string('format', 32)->default('paperback')->after('language');
            }
            if (!Schema::hasColumn('books', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('published_year');
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('books', 'format')) {
                $table->dropColumn('format');
            }
            if (Schema::hasColumn('books', 'published_at')) {
                $table->dropColumn('published_at');
            }
        });
    }
};
