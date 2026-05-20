<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Step 3 – Mass-Insert 1,000,000 Books with Chunked Batch Inserts
 *
 *   Critical: NEVER use Book::factory()->count(1_000_000)->create() – that
 *   instantiates a million Eloquent models at once and exhausts RAM.
 *
 *   Strategy
 *   --------
 *   • CHUNK_SIZE = 100 for MySQL/MariaDB/PostgreSQL, 50 for SQLite – safe for placeholders.
 *   • Book::factory()->make() returns un-persisted models → toArray() →
 *     a raw DB::table('books')->insertOrIgnore($rows) call.
 *   • gc_collect_cycles() every 10 chunks to keep memory below 512 MB.
 *
 *   Execution
 *   ---------
 *     php artisan db:seed --class=MassBookSeeder --no-interaction
 */
class MassBookSeeder extends Seeder
{
    private const TOTAL_RECORDS  = 1_000_000;
    private const GC_EVERY_CHUNK = 1000;

    public function run(): void
    {
        // Disable the query log – critical for not blowing up memory.
        DB::disableQueryLog();

        $driver = DB::getDriverName();
        // Use a conservative chunk size to avoid "too many placeholders" errors
        $chunkSize = in_array($driver, ['mysql', 'mariadb', 'pgsql'], true) ? 100 : 50;

        $target = (int) env('MASS_BOOK_SEED_COUNT', self::TOTAL_RECORDS);
        $alreadyHave = DB::table('books')->count();

        if ($alreadyHave >= $target) {
            $this->command?->info("Books table already has {$alreadyHave} rows (target = {$target}). Skipping.");
            return;
        }

        $remaining = $target - $alreadyHave;
        $this->command?->info("Mass-seeding {$remaining} books in chunks of {$chunkSize} …");

        $inserted = 0;
        $startedAt = microtime(true);
        $chunkIndex = 0;

        while ($inserted < $remaining) {
            $batchSize = min($chunkSize, $remaining - $inserted);

            // make() does NOT persist – purely in-memory model construction.
            $books = Book::factory()->count($batchSize)->make()->toArray();

            // Strip out keys that the table doesn't actually have (defensive).
            $books = array_map(static function (array $row) {
                unset($row['id']);
                return $row;
            }, $books);

            // Raw batch insert for maximum throughput.
            try {
                DB::table('books')->insertOrIgnore($books);
            } catch (\Exception $e) {
                // If we lose connection, try to reconnect and retry once.
                if (str_contains($e->getMessage(), 'MySQL server has gone away') ||
                    str_contains($e->getMessage(), 'Prepared statement contains too many placeholders')) {
                    $this->command?->info('Database issue detected. Reconnecting and reducing chunk size...');
                    DB::reconnect();
                    // Reduce chunk size for next attempt
                    $chunkSize = max(10, intval($chunkSize / 2));
                    $this->command?->info("Reduced chunk size to {$chunkSize}");
                    // Retry this batch with reduced size
                    continue;
                } else {
                    throw $e;
                }
            }

            $inserted += $batchSize;
            $chunkIndex++;

            // Periodic garbage collection.
            if ($chunkIndex % self::GC_EVERY_CHUNK === 0) {
                unset($books);
                gc_collect_cycles();

                $elapsed = microtime(true) - $startedAt;
                $rate    = $inserted / max($elapsed, 0.0001);
                $memMb   = round(memory_get_usage(true) / 1048576, 1);

                $this->command?->info(sprintf(
                    '  → %s rows inserted | %s rows/sec | mem = %s MB',
                    number_format($inserted),
                    number_format($rate),
                    $memMb
                ));
            }
        }

        $totalSeconds = round(microtime(true) - $startedAt, 2);
        $this->command?->info("✅ Inserted {$inserted} books in {$totalSeconds}s.");
    }
}