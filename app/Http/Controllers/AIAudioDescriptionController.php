<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Services\AIServiceManager;
use App\Services\AudioDescriptionService;
use App\Models\AIUsageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * AI Audio Description Controller
 *
 * Generates and serves AI-narrated accessible audio descriptions for books.
 *
 * POST /api/ai/audio-description/{book}       — generate description (+ TTS)
 * GET  /api/ai/audio/tts/{book}               — fetch cached TTS MP3 (or generate)
 * GET  /api/ai/audio/{book}                   — return description text as JSON
 * GET  /api/ai/browse                          — browse books with their audio descriptions
 */
class AIAudioDescriptionController extends Controller
{
    public function __construct(
        protected AudioDescriptionService $descService,
        protected AIServiceManager        $ai,
    ) {}

    /**
     * POST /api/ai/audio-description/{book}
     * Generate (or refresh) a fresh AI accessible description for a book.
     *
     * Body: { with_audio: true|false }
     */
    public function generate(Request $request, int $book)
    {
        $validated = $request->validate([
            'with_audio' => ['nullable', 'boolean'],
        ]);

        $withAudio = (bool) ($validated['with_audio'] ?? false);

        $startMs = microtime(true) * 1000;

        try {
            $data = $this->descService->generateForBook($book, $withAudio);
        } catch (\Throwable $e) {
            $latencyMs = (int) (microtime(true) * 1000 - $startMs);
            Log::error('AIAudioDescription: generate failed', ['book_id' => $book, 'error' => $e->getMessage()]);

            AIUsageLog::create([
                'provider'      => 'openai',
                'feature'       => 'audio_description',
                'loggable_type' => Book::class,
                'loggable_id'   => $book,
                'tokens_used'   => 0,
                'cost_estimate' => 0,
                'metadata'      => json_encode(['error' => $e->getMessage(), 'latency_ms' => $latencyMs]),
            ]);

            throw ValidationException::withMessages([
                'description' => 'Could not generate description: ' . $e->getMessage(),
            ]);
        }

        $latencyMs = (int) round(microtime(true) * 1000 - $startMs);
        $tokens    = max(1, (int) ceil(strlen($data['ai_description']) / 4));

        AIUsageLog::create([
            'provider'      => 'openai',
            'feature'       => 'audio_description',
            'loggable_type' => Book::class,
            'loggable_id'   => $book,
            'tokens_used'   => $tokens,
            'cost_estimate' => round($tokens / 1000 * 0.00060, 6),
            'metadata'      => json_encode([
                'has_audio'  => ! empty($data['audio_path']),
                'latency_ms' => $latencyMs,
                'desc_len'   => strlen($data['ai_description']),
            ]),
        ]);

        return response()->json(array_merge($data, ['latency_ms' => $latencyMs]));
    }

    /**
     * GET /api/ai/audio/{book}
     * Return the existing cached description (or generate one on the fly).
     */
    public function show(int $book)
    {
        $text = $this->descService->getOrFallback($book);

        return response()->json([
            'book_id'        => $book,
            'ai_description' => $text,
            'cached'         => (bool) Book::find($book)?->ai_audio_description,
        ]);
    }

    /**
     * GET /api/ai/audio/tts/{book}
     * Serve the cached TTS MP3 or redirect to a fresh generation.
     */
    public function tts(int $book)
    {
        $bookModel = Book::find($book);
        if (! $bookModel) {
            abort(404, 'Book not found');
        }

        $file = storage_path('app/public/ai-tts/tts_' . $book . '.mp3');

        if (file_exists($file)) {
            return response()->file($file, [
                'Content-Type'        => 'audio/mpeg',
                'Cache-Control'       => 'public, max-age=86400',
                'Content-Disposition' => 'inline; filename="book-' . $book . '-tts.mp3"',
                'Access-Control-Allow-Origin' => '*',
            ]);
        }

        // Generate on demand
        try {
            $audioPath = $this->ai->textToSpeech($bookModel->ai_audio_description
                ?? $this->descService->getOrFallback($book));

            $absPath = storage_path('app/' . ltrim($audioPath, 'storage/'));

            if (file_exists($absPath)) {
                return response()->file($absPath, [
                    'Content-Type'  => 'audio/mpeg',
                    'Cache-Control' => 'public, max-age=86400',
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('AIAudioDescription: TTS generation failed', ['book_id' => $book, 'error' => $e->getMessage()]);
        }

        abort(503, 'Audio description could not be generated. An OpenAI API key is required for voice synthesis.');
    }

    /**
     * GET /api/ai/browse?category=…&q=…&page=1
     *
     * Paginated browse view of all books that already have an AI description
     * (so audio playback is instant).  Falls back to description-less books
     * when the page is empty.
     */
    public function browse(Request $request)
    {
        $categoryId = $request->filled('category') ? (int) $request->category : null;
        $search     = $request->filled('q') ? trim($request->q) : null;
        $perPage    = min((int) ($request->get('per_page', 24)), 96);

        $query = Book::query()
            ->with(['category'])
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $q2 = "%{$search}%";
                    $w->where('title', 'like', $q2)
                      ->orWhere('author', 'like', $q2)
                      ->orWhere('genre', 'like', $q2)
                      ->orWhereHas('category', fn ($c) => $c->where('name', 'like', $q2));
                });
            })
            ->orderBy('ai_description_at', 'desc')
            ->orderBy('title');

        $books   = $query->paginate($perPage)->withQueryString();
        $hasAudio = $books->every(fn ($b) => ! empty($b->ai_audio_description));

        $categories = Category::orderBy('name')->pluck('name', 'id');

        return response()->json([
            'books'          => $books->items(),
            'pagination'     => [
                'current_page' => $books->currentPage(),
                'last_page'    => $books->lastPage(),
                'total'        => $books->total(),
                'per_page'     => $books->perPage(),
            ],
            'categories'     => $categories,
            'openai_configured' => $this->isOpenAIConfigured(),
        ]);
    }

    protected function isOpenAIConfigured(): bool
    {
        $key = env('OPENAI_API_KEY', '');
        return ! empty($key) && $key !== 'sk-your-openai-key-here';
    }
}
