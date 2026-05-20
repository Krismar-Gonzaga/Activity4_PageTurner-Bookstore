<?php

namespace App\Jobs;

use App\Models\Book;
use App\Models\AIUsageLog;
use App\Services\AudioDescriptionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * ProcessAITask
 *
 * Handles one chunk of work off the request thread so the user gets
 * an instant HTTP 202 / "queued" response and the AI work finishes in
 * the background.
 *
 * Dispatch:
 *   ProcessAITask::dispatch($taskData)->delay(now()->addMinutes(5));
 */
class ProcessAITask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  array  $task   {type, book_id, model?, user_id?}
     */
    public function __construct(public array $task) {}

    public function handle(AudioDescriptionService $descService): void
    {
        $task = $this->task;
        $type = $task['type'] ?? 'unknown';

        match ($type) {
            'audio_description'       => $this->handleAudioDescription($descService, $task),
            'audio_description_batch'  => $this->handleBatchDescription($descService, $task),
            default                    => Log::warning("ProcessAITask: unknown type [{$type}]."),
        };
    }

    protected function handleAudioDescription(AudioDescriptionService $descService, array $task): void
    {
        $bookId = (int) ($task['book_id'] ?? 0);
        if ($bookId <= 0) return;

        $startMs = microtime(true) * 1000;

        try {
            $result = $descService->generateForBook($bookId, $task['with_audio'] ?? false);
            $latencyMs = (int) round(microtime(true) * 1000 - $startMs);

            AIUsageLog::create([
                'provider'      => 'openai',
                'feature'       => 'audio_description_queue',
                'loggable_type' => Book::class,
                'loggable_id'   => $bookId,
                'tokens_used'   => max(1, (int) ceil(strlen($result['ai_description']) / 4)),
                'cost_estimate' => 0,
                'metadata'      => json_encode(['latency_ms' => $latencyMs, 'queued' => true]),
            ]);

            Log::info("ProcessAITask: audio description queued for book [{$bookId}] (latency {$latencyMs}ms).");
        } catch (Throwable $e) {
            $latencyMs = (int) round(microtime(true) * 1000 - $startMs);
            Log::error("ProcessAITask: description failed for book [{$bookId}]: {$e->getMessage()}");

            AIUsageLog::create([
                'provider'      => 'openai',
                'feature'       => 'audio_description_queue',
                'loggable_type' => Book::class,
                'loggable_id'   => $bookId,
                'tokens_used'   => 0,
                'cost_estimate' => 0,
                'metadata'      => json_encode([
                    'error'      => $e->getMessage(),
                    'latency_ms' => $latencyMs,
                ]),
            ]);

            $this->fail($e);
        }
    }

    protected function handleBatchDescription(AudioDescriptionService $descService, array $task): void
    {
        $count = $task['count'] ?? 100;
        $limit = min($count, 200);

        $summary = $descService->regenerateMissing($limit);
        Log::info('ProcessAITask: batch description completed', $summary);
    }
}
