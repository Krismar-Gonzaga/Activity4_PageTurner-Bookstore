<?php

namespace App\Http\Controllers;

use App\Services\AIServiceManager;
use App\Services\VoiceSearchService;
use App\Models\AIUsageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

/**
 * AI Voice Search Controller
 *
 * Endpoints consumed by the frontend voice-search widget.
 *
 * POST /api/ai/voice-search
 *   Body: { audio_data: base64-or-raw-bytes, filename: string, limit: int }
 *   Returns: { transcript, corrected_query, results: [...], method, provider_used }
 *
 * GET  /api/ai/voice-search/test?q=...
 *   Back-compat text-only search for testing without a microphone.
 */
class AIVoiceSearchController extends Controller
{
    public function __construct(
        protected VoiceSearchService $voiceSearch,
        protected AIServiceManager  $ai,
    ) {}

    /**
     * Receive an audio blob (from the browser's MediaRecorder), run it through
     * the STT → AI query-correction → book search pipeline, and return JSON.
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'audio_data' => ['required', 'string'],
            'filename'   => ['nullable', 'string', 'max:255'],
            'limit'      => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $limit = (int) ($validated['limit'] ?? 12);

        $startMs   = microtime(true) * 1000;
        $provider  = 'ollama';   // resolved below after call
        $transcript = '';
        $results     = collect();

        try {
            $resp = $this->voiceSearch->search(
                $validated['audio_data'],
                $validated['filename'] ?? 'voice.webm',
                $limit
            );

            $transcript = $resp['transcript'];
            $results    = $resp['results'];
            $method     = $resp['method'];

        } catch (\Throwable $e) {
            $latencyMs = (int) (microtime(true) * 1000 - $startMs);
            Log::error('AIVoiceSearch error', ['error' => $e->getMessage()]);

            AIUsageLog::create([
                'provider'     => 'openai',
                'feature'      => 'voice_search',
                'tokens_used'  => 0,
                'cost_estimate'=> 0,
                'metadata'     => json_encode(['error' => $e->getMessage(), 'latency_ms' => $latencyMs]),
            ]);

            return response()->json([
                'transcript'      => '',
                'corrected_query' => '',
                'results'         => [],
                'method'          => 'none',
                'error'           => 'Voice search failed: ' . $e->getMessage(),
                'latency_ms'      => $latencyMs,
            ], 500);
        }

        $latencyMs = (int) (round(microtime(true) * 1000 - $startMs));

        // Log usage — tokens are approximate
        $tokenEstimate = max(1, (int) ceil(strlen($transcript) / 4));
        AIUsageLog::create([
            'provider'      => 'openai',
            'feature'       => 'voice_search',
            'tokens_used'   => $tokenEstimate,
            'cost_estimate' => round($tokenEstimate / 1000 * 0.00015, 6),
            'metadata'      => json_encode([
                'transcript'    => $transcript,
                'latency_ms'    => $latencyMs,
                'results_count' => $results->count(),
                'method'        => $method,
            ]),
        ]);

        return response()->json([
            'transcript'      => $transcript,
            'corrected_query' => $transcript,
            'results'         => $results,
            'method'          => $method,
            'provider_used'   => 'openai',
            'latency_ms'      => $latencyMs,
        ]);
    }

    /**
     * Text-only search — useful for frontend testing and users with keyboard.
     */
    public function searchText(Request $request)
    {
        $validated = $request->validate([
            'q'    => ['required', 'string', 'min:1', 'max:500'],
            'limit'=> ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $limit = (int) ($validated['limit'] ?? 12);

        $startMs  = microtime(true) * 1000;

        try {
            $resp = $this->voiceSearch->searchByText($validated['q'], $limit);
        } catch (\Throwable $e) {
            throw ValidationException::withMessages([
                'q' => 'Search failed: ' . $e->getMessage(),
            ]);
        }

        $latencyMs = (int) round(microtime(true) * 1000 - $startMs);

        AIUsageLog::create([
            'provider'      => 'openai',
            'feature'       => 'voice_search',
            'tokens_used'   => 0,
            'cost_estimate' => 0,
            'metadata'      => json_encode([
                'query'         => $validated['q'],
                'latency_ms'    => $latencyMs,
                'results_count' => $resp['results']->count(),
            ]),
        ]);

        return response()->json(array_merge($resp, [
            'latency_ms'  => $latencyMs,
            'provider_used' => '',
        ]));
    }
}
