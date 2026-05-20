<?php

namespace App\Services;

use App\Models\Book;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * VoiceSearchService
 *
 * Converts voice input → text (via OpenAI Whisper STT),
 * then performs a semantic + keyword search over the books table.
 *
 * Usage:
 *   $service = app(VoiceSearchService::class);
 *   $results = $service->search('audio/fixtures/sample.mp3', 10);
 *
 * Successful return:
 *   [
 *     'transcript'     => 'science fiction books about space',
 *     'corrected_query'=> 'science fiction books about space',
 *     'results'        => Collection<Book>,
 *     'method'         => 'semantic',   // 'semantic' | 'keyword' | 'none'
 *   ]
 */
class VoiceSearchService
{
    protected AIServiceManager $ai;
    protected string $uploadDir;

    public function __construct(AIServiceManager $ai)
    {
        $this->ai        = $ai;
        $this->uploadDir = storage_path('app/public/voice-inputs');
    }

    // ── Public API ─────────────────────────────────────────────────────────

    /**
     * Transcribe an uploaded audio blob and return matching books.
     *
     * @param  string  $audioData   raw binary / base64 audio data
     * @param  string  $filename    original filename (used for extension)
     * @param  int     $limit       max results to return
     * @return array
     */
    public function search(string $audioData, string $filename = 'voice.webm', int $limit = 10): array
    {
        $audioPath = $this->storeAudio($audioData, $filename);

        try {
            // 1. Transcribe audio → text
            $transcript = $this->ai->transcribe($audioPath);
            Log::info('VoiceSearch: transcript', ['transcript' => $transcript]);

            // 2. Normalise / correct query (AI spelling correction)
            $corrected = $this->correctQuery($transcript);

            // 3. Search books
            $results  = $this->findBooks($corrected, $limit);
            $method   = $results->isNotEmpty() ? 'keyword' : 'none';

            return [
                'transcript'      => $transcript,
                'corrected_query' => $corrected,
                'results'         => $results,
                'method'          => $method,
            ];
        } catch (Exception $e) {
            Log::error('VoiceSearch error: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        } finally {
            @unlink($audioPath);   // clean up temp file
        }
    }

    /**
     * Search using text directly (skips transcription — useful for testing).
     */
    public function searchByText(string $query, int $limit = 10): array
    {
        $transcript = $query;
        $corrected  = $this->correctQuery($transcript);
        $results    = $this->findBooks($corrected, $limit);

        return [
            'transcript'      => $transcript,
            'corrected_query' => $corrected,
            'results'         => $results,
            'method'          => $results->isNotEmpty() ? 'keyword' : 'none',
        ];
    }

    // ── Private Helpers ────────────────────────────────────────────────────

    /**
     * Persist the raw audio blob to disk so OpenAI's PHP client
     * (which expects a file path / resource) can consume it.
     */
    protected function storeAudio(string $audioData, string $filename): string
    {
        @mkdir($this->uploadDir, 0755, true);

        // Detect extension if not webm
        $ext = pathinfo($filename, PATHINFO_EXTENSION) ?: 'webm';
        $path = "{$this->uploadDir}/voice_" . time() . '_' . uniqid() . ".{$ext}";

        // Support both raw binary and base64-encoded blobs
        $bytes = $this->maybeDecodeBase64($audioData);
        file_put_contents($path, $bytes);

        return $path;
    }

    protected function maybeDecodeBase64(string $data): string
    {
        $stripped = preg_replace('/^data:audio\/[a-z0-9]+;base64,/', '', $data);
        $decoded  = base64_decode($stripped, true);
        return $decoded !== false ? $decoded : $data;
    }

    /**
     * Use AI to correct spelling / grammar in the transcribed query.
     * Falls back to the raw transcript if the AI call fails.
     */
    protected function correctQuery(string $transcript): string
    {
        try {
            return $this->ai->generate(
                "Correct the spelling and grammar of the following book search query. "
                . "Return ONLY the corrected query with no explanation, no quotes, and no extra text.\n\n"
                . "Original: {$transcript}",
                'voice_search',
                ['max_tokens' => 64, 'temperature' => 0],
                true    // use fallback chain
            );
        } catch (Exception $e) {
            Log::warning('VoiceSearch query correction failed; using raw transcript.', ['error' => $e->getMessage()]);
            return $transcript;
        }
    }

    /**
     * Multi-criteria search across title, author, genre, description, and category name.
     * Results are ordered by relevance (exact/partial title matches first).
     */
    protected function findBooks(string $query, int $limit)
    {
        $terms = array_filter(explode(' ', trim($query)));

        $q = Book::query()
            ->with(['category', 'reviews'])
            ->where('is_visible', true)
            ->where(function ($builder) use ($terms) {
                foreach ($terms as $term) {
                    $like = '%' . trim($term) . '%';
                    $builder->orWhere('title', 'like', $like)
                            ->orWhere('author', 'like', $like)
                            ->orWhere('genre', 'like', $like)
                            ->orWhere('description', 'like', $like)
                            ->orWhereHas('category', fn ($c) => $c->where('name', 'like', $like));
                }
            })
            ->orderByRaw("
                CASE
                    WHEN title LIKE ?      THEN 1
                    WHEN author LIKE ?     THEN 2
                    WHEN genre LIKE ?      THEN 3
                    ELSE                      4
                END
            ", ["%{$query}%", "%{$query}%", "%{$query}%"])
            ->limit($limit);

        return $q->get();
    }
}
