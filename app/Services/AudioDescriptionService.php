<?php

namespace App\Services;

use App\Models\Book;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * AudioDescriptionService
 *
 * Generates AI-written accessible audio descriptions for books so visually
 * impaired users can hear contextual summaries without needing screen-readers
 * or reading the full description.
 *
 * Usage:
 *   $service = app(AudioDescriptionService::class);
 *   $data = $service->generateForBook($bookId);
 *
 *   // With TTS:
 *   $data = $service->generateWithAudio($bookId, true);
 *
 * Return shape:
 *   [
 *     'book_id'         => int,
 *     'book_title'      => string,
 *     'ai_description'  => string,   // AI-generated accessible text (~120 words)
 *     'audio_path'      => string|null,  // 'storage/ai-tts/...'  (only when $withAudio = true)
 *     'provider_used'   => string,   // e.g. 'openai', 'ollama'
 *     'generated_at'    => string,   // ISO datetime
 *   ]
 *
 * Graceful degradation:
 *   If all AI providers fail the description falls back to the book's own
 *   description (truncated).  If TTS also fails, audio_path is null.
 */
class AudioDescriptionService
{
    protected AIServiceManager $ai;

    public function __construct(AIServiceManager $ai)
    {
        $this->ai = $ai;
    }

    // ── Public API ─────────────────────────────────────────────────────────

    /**
     * Generate (and optionally narrate) an accessible description for a book.
     *
     * @param  int     $bookId
     * @param  bool    $withAudio   generate spoken audio via TTS
     * @return array
     * @throws Exception  escalated only when every AI provider AND the fallback description both fail
     */
    public function generateForBook(int $bookId, bool $withAudio = false): array
    {
        $book = Book::with('category')->find($bookId);
        if (! $book) {
            throw new Exception("Book [{$bookId}] not found.");
        }

        Log::info('AudioDescription: generating description', ['book_id' => $bookId]);

        // 1. Generate accessible text via AI
        $aiDescription = $this->generateDescriptionText($book);

        // 2. Optionally generate spoken audio
        $audioPath = null;
        if ($withAudio) {
            try {
                $audioPath = $this->ai->textToSpeech($aiDescription);
                Log::info('AudioDescription: TTS generated', ['book_id' => $bookId, 'audio' => $audioPath]);
            } catch (Exception $e) {
                Log::warning('AudioDescription: TTS failed, continuing without audio.', ['error' => $e->getMessage()]);
            }
        }

        // 3. Persist description back to the book
        $book->ai_audio_description = $aiDescription;
        $book->ai_description_at    = now();
        $book->saveQuietly();

        return [
            'book_id'        => $bookId,
            'book_title'     => $book->title,
            'author'         => $book->author,
            'ai_description' => $aiDescription,
            'audio_path'     => $audioPath,
            'generated_at'   => now()->toIso8601String(),
        ];
    }

    /**
     * Batch-generate descriptions for all books that don't yet have one.
     *
     * @param  int  $batchSize   how many books to process per chunk
     * @return array  ['processed' => int, 'failed' => int]
     */
    public function regenerateMissing(int $batchSize = 50): array
    {
        $processed = 0;
        $failed    = 0;

        Book::whereNull('ai_audio_description')
            ->orWhere('ai_audio_description', '')
            ->orderBy('id')
            ->chunk($batchSize, function ($books) use (&$processed, &$failed) {
                foreach ($books as $book) {
                    try {
                        $this->generateForBook($book->id);
                        $processed++;
                    } catch (Exception $e) {
                        $failed++;
                        Log::warning("AudioDescription: failed for book [{$book->id}]: {$e->getMessage()}");
                    }
                }
            });

        return ['processed' => $processed, 'failed' => $failed];
    }

    /**
     * Return just the text even when TTS / AI is unavailable.
     *
     * @param  int  $bookId
     * @return string
     */
    public function getOrFallback(int $bookId): string
    {
        $book = Book::find($bookId);
        if (! $book) {
            return '';
        }

        if (! empty($book->ai_audio_description)) {
            return $book->ai_audio_description;
        }

        try {
            $data    = $this->generateForBook($bookId);
            return $data['ai_description'];
        } catch (Exception $e) {
            Log::warning("AudioDescription: AI failed for book [{$bookId}]; using raw description.", ['error' => $e->getMessage()]);
            // Truncate raw description to ~150 words
            return Str::words(strip_tags((string) $book->description), 150);
        }
    }

    // ── Private Helpers ────────────────────────────────────────────────────

    /**
     * Ask AI to write an ~120-word accessible audio description.
     * Tries all providers via fallback chain.
     */
    protected function generateDescriptionText(Book $book): string
    {
        $category = $book->category ? $book->category->name : 'general reading';

        $prompt = <<<PROMPT
You are an audiobook narrator and accessibility aide for an online bookstore called PageTurner.

Write a vivid, accessible audio description for the following book.  The description
will be read aloud, so it must be conversational, vivid, and free of visual references
that cannot be described (e.g. do not say "as seen in the cover"; instead describe what
the cover art depicts).  Keep it to approximately 120 words.

Book details:
  Title     : {$book->title}
  Author    : {$book->author}
  Genre     : {$book->genre}
  Category  : {$category}
  Price     : \${$book->price}
  Published : {$book->published_year}

  Current description: {($book->description ?: 'No description available.')}

Instructions:
1. Begin with the book title and author.
2. Describe the book's theme, setting, and who would enjoy it.
3. Mention price and category naturally.
4. Conclude with a gentle call to action (e.g. "Add this book to your cart today").
5. Do NOT include any markdown.  Output plain text only.
PROMPT;

        try {
            $text = $this->ai->generate(
                $prompt,
                'audio_description',
                ['max_tokens' => 512, 'temperature' => 0.7],
                true    // use fallback chain
            );

            $text = trim($text);
            if (strlen($text) < 20) {
                throw new Exception('AI response was too short; treating as failure.');
            }

            return $text;
        } catch (Exception $e) {
            Log::warning('AudioDescription: AI generation failed; using fallback.', ['error' => $e->getMessage()]);

            // Ultimate fallback: concise manual description
            $genreWord  = strtolower($book->genre);
            $cardinal   = 'a';
            if (in_array(substr($genreWord, 0, 1), ['a', 'e', 'i', 'o', 'u'])) {
                $cardinal = 'an';
            }

            return "{$book->title} by {$book->author} — {$cardinal} {$book->genre} book "
                . ($book->description ? ('available for $' . number_format($book->price, 2) . '. ') : '')
                . ($book->description ? Str::words(strip_tags((string) $book->description), 80) : 'Add it to your cart today.');
        }
    }
}
