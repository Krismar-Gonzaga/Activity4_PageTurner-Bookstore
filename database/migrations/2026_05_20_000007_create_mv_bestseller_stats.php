<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Step 8 – Materialized View for Reporting
 *
 *   mv_bestseller_stats pre-computes aggregate statistics per category so that
 *   bestseller / inventory dashboards do not need to scan the full 1M-row
 *   books table on every request.
 *
 * Refresh strategy
 * ----------------
 *   Refreshed via the artisan command `app:refresh-materialized-views`
 *   (scheduled hourly in routes/console.php).
 *
 *   • PostgreSQL: REFRESH MATERIALIZED VIEW CONCURRENTLY mv_bestseller_stats;
 *   • MySQL:      MySQL does not natively support materialized views – this
 *                 migration creates a regular table that is rebuilt with a
 *                 TRUNCATE + INSERT … SELECT … inside the refresh command.
 *   • SQLite:     identical strategy as MySQL (regular table).
 */
return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement(<<<'SQL'
                CREATE MATERIALIZED VIEW IF NOT EXISTS mv_bestseller_stats AS
                SELECT
                    category_id,
                    COUNT(*)                                            AS total_books,
                    AVG(price)                                          AS avg_price,
                    SUM(stock_quantity)                                 AS total_inventory,
                    COUNT(CASE WHEN stock_quantity > 500 THEN 1 END)    AS bestseller_count,
                    MAX(published_at)                                   AS latest_publication
                FROM books
                WHERE is_active = TRUE
                GROUP BY category_id
            SQL);

            return;
        }

        // Fallback: ordinary table used as a "poor-man's materialized view".
        Schema::create('mv_bestseller_stats', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->primary();
            $table->unsignedBigInteger('total_books')->default(0);
            $table->decimal('avg_price', 10, 2)->default(0);
            $table->unsignedBigInteger('total_inventory')->default(0);
            $table->unsignedBigInteger('bestseller_count')->default(0);
            $table->timestamp('latest_publication')->nullable();
            $table->timestamp('refreshed_at')->nullable();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP MATERIALIZED VIEW IF EXISTS mv_bestseller_stats');
            return;
        }

        Schema::dropIfExists('mv_bestseller_stats');
    }
};
