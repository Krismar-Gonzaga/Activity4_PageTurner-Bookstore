<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

/**
 * AIServiceManager — provider abstraction & multi-provider fallback layer.
 *
 * Usage:
 *   $ai   = app(AIServiceManager::class);
 *   $text = $ai->generate($prompt);                              // default provider
 *   $text = $ai->generate($prompt, 'voice_search');              // feature-mapped provider
 *   $text = $ai->generateWithFallback($prompt);                  // tries all providers
 *   $text = $ai->generate($prompt, 'voice_search', null, true);  // with fallback for feature
 */
class AIServiceManager
{
    protected array $providers;
    protected array $fallbackChain;
    protected bool $fallbackEnabled;

    public function __construct()
    {
        $this->providers      = Config::get('ai.providers', []);
        $this->fallbackChain  = Config::get('ai.fallback_chain', ['openai', 'gemini', 'ollama']);
        $this->fallbackEnabled = (bool) Config::get('ai.fallback_chain_override', env('AI_FALLBACK_ENABLED', true));
    }

    /**
     * Generate text using the provider mapped to $feature (or the default).
     *
     * @param  string  $prompt
     * @param  string|null  $feature   maps to config('ai.feature_providers.*')
     * @param  array   $options      ['temperature' => 0.7, 'max_tokens' => 512, ...]
     * @param  bool    $useFallback  if true, fall back to the next provider on failure
     * @return string
     * @throws Exception
     */
    public function generate(
        string $prompt,
        ?string $feature = null,
        array $options = [],
        bool $useFallback = false
    ): string {
        $providerName = $this->resolveProvider($feature);
        $driver       = $providerName;

        if ($useFallback && $this->fallbackEnabled) {
            return $this->generateWithFallback($prompt, $feature, $options);
        }

        return $this->callProvider($providerName, $prompt, $options);
    }

    /**
     * Generate text, trying each provider in the fallback chain until one succeeds.
     *
     * @param  string|null  $feature
     * @return string
     * @throws Exception  when every provider in the chain fails
     */
    public function generateWithFallback(
        string $prompt,
        ?string $feature = null,
        array $options = []
    ): string {
        $chain = $this->fallbackChain;

        // Start with the feature-mapped provider so it is at the head of the queue
        $headProvider = $this->resolveProvider($feature);
        $chain        = array_values(array_unique(array_merge([$headProvider], $chain)));

        $featureName = $feature ?? 'default';
        $lastException = null;

        foreach ($chain as $provider) {
            if (! $this->isAvailable($provider)) {
                Log::warning("AI provider [{$provider}] is not configured; skipping.");
                continue;
            }

            try {
                $result = $this->callProvider($provider, $prompt, $options);
                Log::info("AI provider [{$provider}] succeeded for feature [{$featureName}].");
                return $result;
            } catch (Exception $e) {
                $lastException = $e;
                Log::warning("AI provider [{$provider}] failed for feature [{$featureName}]: " . $e->getMessage());
                // continue to next provider in chain
            }
        }

        Log::error('All AI providers exhausted and failed.');
        throw new Exception(
            $lastException
                ? 'All AI providers are currently unavailable: ' . $lastException->getMessage()
                : 'All AI providers are currently unavailable.'
        );
    }

    /**
     * Resolve which provider should handle a given feature.
     */
    protected function resolveProvider(?string $feature): string
    {
        if ($feature && Config::has("ai.feature_providers.{$feature}")) {
            return Config::get("ai.feature_providers.{$feature}");
        }
        return Config::get('ai.default', 'openai');
    }

    /**
     * Retrieve a required config value with a default.
     */
    protected function cfg(string $provider, string $key, mixed $default = ''): mixed
    {
        return $this->providers[$provider][$key] ?? $default;
    }

    /**
     * Check whether a provider has the minimum configuration to be called.
     */
    protected function isAvailable(string $provider): bool
    {
        if (! isset($this->providers[$provider])) {
            return false;
        }

        $config = $this->providers[$provider];

        // Ollama is always considered "available" (no API key required)
        if ($provider === 'ollama') {
            return true;
        }

        return ! empty($config['api_key']);
    }

    /**
     * Dispatch the prompt to a specific provider.
     *
     * Each driver returns the plain response text.
     *
     * @throws Exception
     */
    protected function callProvider(string $provider, string $prompt, array $options = []): string
    {
        return match ($provider) {

            'openai'      => $this->callOpenAI($prompt, $options),
            'gemini'      => $this->callGemini($prompt, $options),
            'huggingface' => $this->callHuggingFace($prompt, $options),
            'ollama'      => $this->callOllama($prompt, $options),

            default => throw new Exception("Unknown AI provider [{$provider}]."),
        };
    }

    // ── OPENAI ─────────────────────────────────────────────────────────────

    protected function callOpenAI(string $prompt, array $options = []): string
    {
        $key   = $this->cfg('openai', 'api_key');
        $model = $options['model'] ?? $this->cfg('openai', 'model') ?? 'gpt-4o-mini';

        if (empty($key)) {
            throw new Exception('OpenAI API key is not configured.');
        }

        $response = HttpClient::withHeaders([
            'Authorization' => "Bearer {$key}",
            'Content-Type'  => 'application/json',
        ])
        ->timeout(60)
        ->asJson()
        ->post($this->cfg('openai', 'base_url') . '/chat/completions', [
            'model'       => $model,
            'messages'    => [
                ['role' => 'system', 'content' => 'You are a helpful bookstore assistant.'],
                ['role' => 'user',   'content' => $prompt],
            ],
            'temperature' => $options['temperature'] ?? 0.7,
            'max_tokens'  => $options['max_tokens']  ?? 1024,
        ]);

        if (! $response->successful()) {
            throw new Exception('OpenAI API error ' . $response->status() . ': ' . $response->body());
        }

        return trim((string) $response->json('choices.0.message.content', ''));
    }

    // ── GEMINI ─────────────────────────────────────────────────────────────

    protected function callGemini(string $prompt, array $options = []): string
    {
        $model = $options['model'] ?? $this->cfg('gemini', 'model') ?? 'gemini-2.0-flash';
        $key   = $this->cfg('gemini', 'api_key');
        $url   = $this->cfg('gemini', 'base_url') . "/models/{$model}:generateContent?key={$key}";

        $body = [
            'contents' => [
                ['parts' => [['text' => $prompt]]],
            ],
            'generationConfig' => [
                'temperature'    => $options['temperature'] ?? 0.7,
                'maxOutputTokens'=> $options['max_tokens'] ?? 1024,
            ],
        ];

        $response = HttpClient::asJson()->post($url, $body);

        if (! $response->successful()) {
            throw new Exception('Gemini API error ' . $response->status() . ': ' . $response->body());
        }

        $firstCandidate = $response->json('candidates.0.content.parts.0.text', '');
        return trim((string) $firstCandidate)
            ?? throw new Exception('Gemini returned an empty response.');
    }

    // ── HUGGING FACE ───────────────────────────────────────────────────────

    protected function callHuggingFace(string $prompt, array $options = []): string
    {
        $model = $options['model'] ?? $this->cfg('huggingface', 'model');
        $url   = $this->cfg('huggingface', 'base_url') . "/models/{$model}";

        $response = HttpClient::withHeaders([
            'Authorization' => "Bearer " . $this->cfg('huggingface', 'api_key'),
        ])->asJson()->post($url, ['inputs' => $prompt]);

        if (! $response->successful()) {
            throw new Exception('Hugging Face API error ' . $response->status() . ': ' . $response->body());
        }

        $data = $response->json();

        return is_array($data) ? ($data[0]['label'] ?? json_encode($data)) : (string) $data;
    }

    // ── OLLAMA (LOCAL) ─────────────────────────────────────────────────────

    protected function callOllama(string $prompt, array $options = []): string
    {
        $cfg   = $this->providers['ollama'];
        $model = $options['model'] ?? $cfg['model'];
        $url   = rtrim($cfg['base_url'], '/') . '/api/generate';

        $body = [
            'model'  => $model,
            'prompt' => $prompt,
            'stream' => false,
            'options' => [
                'temperature' => $options['temperature'] ?? 0.7,
                'num_predict' => $options['max_tokens'] ?? 1024,
            ],
        ];

        try {
            $response = HttpClient::timeout(120)->asJson()->post($url, $body);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new Exception("Ollama is not reachable at {$url}. Is it running? " . $e->getMessage(), 0, $e);
        }

        if (! $response->successful()) {
            throw new Exception('Ollama returned HTTP ' . $response->status() . ': ' . $response->body());
        }

        return trim((string) ($response->json('response') ?? ''))
            ?? throw new Exception('Ollama returned an empty response.');
    }

    /**
     * Embed text using the configured embedding model (Ollama preferred).
     */
    public function embed(string $text, ?string $provider = null): array
    {
        $provider = $provider ?? 'ollama';

        if ($provider === 'ollama') {
            $cfg   = $this->providers['ollama'];
            $url   = rtrim($cfg['base_url'], '/') . '/api/embeddings';
            $model = $cfg['embed_model'];

            $response = HttpClient::asJson()->post($url, [
                'model' => $model, 'prompt' => $text,
            ]);

            return $response->json('embedding', []);
        }

        if ($provider === 'openai') {
            $cfg    = $this->providers['openai'];
            $client = new \OpenAI\Client($cfg['api_key']);

            $response = $client->embeddings()->create([
                'model' => 'text-embedding-3-small',
                'input' => $text,
            ]);

            return $response->embeddings[0]->embedding;
        }

        throw new Exception("Embedding not supported for provider [{$provider}] in this build.");
    }

    /**
     * Transcribe audio (Speech-to-Text) via OpenAI Whisper.
     *
     * @param  string  $audioPath  absolute path to the captured audio file
     * @return string  transcribed text
     * @throws Exception
     */
    public function transcribe(string $audioPath): string
    {
        $cfg = $this->providers['openai'];

        if (empty($cfg['api_key']) || $cfg['api_key'] === 'sk-your-openai-key-here') {
            throw new Exception(
                'OpenAI API key is not configured. Set OPENAI_API_KEY in your .env file to enable voice search.'
            );
        }

        $response = HttpClient::withHeaders([
            'Authorization' => "Bearer {$cfg['api_key']}",
        ])
        ->attach(
            'file',
            file_get_contents($audioPath),
            basename($audioPath)
        )
        ->asMultipart()
        ->timeout(120)
        ->post($cfg['base_url'] . '/audio/transcriptions', [
            'model' => $cfg['stt_model'],
            'response_format' => 'text',
        ]);

        if (! $response->successful()) {
            throw new Exception('OpenAI Whisper error ' . $response->status() . ': ' . $response->body());
        }

        return trim((string) $response->body());
    }

    /**
     * Generate synthetic speech from text (Text-to-Speech) via OpenAI.
     *
     * @param  string  $text
     * @return string  path to saved .mp3 file
     * @throws Exception
     */
    public function textToSpeech(string $text): string
    {
        $cfg = $this->providers['openai'];

        if (empty($cfg['api_key']) || $cfg['api_key'] === 'sk-your-openai-key-here') {
            throw new Exception('OpenAI API key is not configured for Text-to-Speech.');
        }

        $response = HttpClient::withHeaders([
            'Authorization' => "Bearer {$cfg['api_key']}",
        ])
        ->timeout(120)
        ->asJson()
        ->post($cfg['base_url'] . '/audio/speech', [
            'model' => $cfg['tts_model'],
            'input' => $text,
            'voice' => 'nova',
        ]);

        if (! $response->successful()) {
            throw new Exception('OpenAI TTS error ' . $response->status() . ': ' . $response->body());
        }

        $dir     = storage_path('app/public/ai-tts');
        @mkdir($dir, 0755, true);

        $filename = 'tts_' . now()->format('Ymd_His') . uniqid() . '.mp3';
        $filepath = "{$dir}/{$filename}";
        file_put_contents($filepath, $response->body());

        return "storage/ai-tts/{$filename}";
    }
}
