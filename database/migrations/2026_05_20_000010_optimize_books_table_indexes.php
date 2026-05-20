<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Step 1 – Database Schema Optimization
 *
 * Adds the performance-critical indexes used by the high-volume catalog
 * queries. Each index is added in its own conditional block so the migration
 * is idempotent and individually rollback-able.
 *
 *   • idx_books_catalog_filter  – composite filtering index
 *   • idx_books_price_stock     – covering index for price / stock range
 *   • idx_books_fulltext        – MySQL / PostgreSQL fulltext index
 *   • idx_books_active          – active-book filter
 *   • idx_books_isbn_lookup     – ISBN look-up
 */
return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        Schema::table('books', function (Blueprint $table) use ($driver) {
            // Composite index for catalog filtering (category + publication + active)
            $table->index(
                ['category_id', 'published_at', 'is_active'],
                'idx_books_catalog_filter'
            );

            // Covering index for price / stock range queries.
            //  Includes "id" so MySQL can satisfy id-based pagination from the
            //  index alone (index-only scan).
            $table->index(
                ['price', 'stock_quantity', 'id'],
                'idx_books_price_stock'
            );

            $table->index('is_active', 'idx_books_active');
            $table->index('isbn',      'idx_books_isbn_lookup');
        });

        // Full-text indexes are only supported on MySQL 5.7+ and PostgreSQL.
        // SQLite (used in dev / tests) will silently skip this.
        if (in_array($driver, ['mysql', 'mariadb', 'pgsql'], true)) {
            Schema::table('books', function (Blueprint $table) {
                $table->fullText(['title', 'description'], 'idx_books_fulltext');
            });
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex('idx_books_catalog_filter');
            $table->dropIndex('idx_books_price_stock');
            $table->dropIndex('idx_books_active');
            $table->dropIndex('idx_books_isbn_lookup');
        });

        if (in_array($driver, ['mysql', 'mariadb', 'pgsql'], true)) {
            Schema::table('books', function (Blueprint $table) {
                $table->dropFullText('idx_books_fulltext');
            });
        }
    }
};
