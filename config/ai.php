<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    |
    | The default provider used when no feature-specific mapping is provided
    | via $ai->generate($prompt, $feature).
    |
    */
    'default' => env('AI_DEFAULT_PROVIDER', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | Fallback Chain
    |--------------------------------------------------------------------------
    |
    | Providers are tried in the order listed here when AI_FALLBACK_ENABLED
    | is true.  The first responding provider wins.  "ollama" is always the
    | last resort (unlimited, local, no API key needed).
    |
    */
    'fallback_chain' => array_filter(
        explode(',', env('AI_FALLBACK_CHAIN', 'openai,gemini,huggingface,ollama')),
        fn($p) => trim($p) !== ''
    ),

    /*
    |--------------------------------------------------------------------------
    | Feature → Provider Mapping
    |--------------------------------------------------------------------------
    |
    | Map any feature key passed as the second argument of
    | $ai->generate($prompt, $feature) to a specific provider so you can
    | offload cheap tasks (category classification) to a local model while
    | keeping expensive tasks on OpenAI.
    |
    */
    'feature_providers' => [
        'voice_search'      => env('AI_VOICE_SEARCH_PROVIDER', 'openai'),
        'audio_description' => env('AI_AUDIO_DESC_PROVIDER',  'openai'),
        'text_to_speech'    => env('AI_TTS_PROVIDER',         'openai'),
        'category_classify' => env('AI_CLASSIFY_PROVIDER',    'ollama'),
        'semantic_search'   => env('AI_SEMANTIC_PROVIDER',    'ollama'),
        'chat'              => env('AI_CHAT_PROVIDER',         'openai'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Alert Thresholds
    |--------------------------------------------------------------------------
    */
    'daily_token_limit'  => (int) env('AI_DAILY_TOKEN_LIMIT', 180000),
    'daily_cost_alert'   => (float) env('AI_DAILY_COST_ALERT_USD', 1.00),

    /*
    |--------------------------------------------------------------------------
    | Provider Definitions
    |--------------------------------------------------------------------------
    */
    'providers' => [

        'openai' => [
            'name'   => 'OpenAI',
            'driver' => 'openai',
            'api_key'     => env('OPENAI_API_KEY'),
            'base_url'    => 'https://api.openai.com/v1',
            'model'       => 'gpt-4o-mini',
            'tts_model'   => 'tts-1',
            'stt_model'   => 'whisper-1',
            'rate_limit'  => [
                'max_requests_per_minute' => 60,
                'max_tokens_per_day'      => 200000,
            ],
            'cost_per_1k_input_tokens'  => 0.00015,
            'cost_per_1k_output_tokens' => 0.00060,
        ],

        'gemini' => [
            'name'   => 'Google Gemini Flash',
            'driver' => 'gemini',
            'api_key'     => env('GEMINI_API_KEY'),
            'base_url'    => 'https://generativelanguage.googleapis.com/v1beta',
            'model'       => 'gemini-2.0-flash',
            'rate_limit'  => [
                'max_requests_per_day' => 1500,
            ],
            'cost_per_1k_input_tokens'  => 0.00000, // free tier
            'cost_per_1k_output_tokens' => 0.00000,
        ],

        'huggingface' => [
            'name'   => 'Hugging Face',
            'driver' => 'huggingface',
            'api_key' => env('HF_API_KEY'),
            'base_url' => 'https://api-inference.huggingface.co',
            // Default model — overridden per task
            'model'   => 'facebook/bart-large-mnli',
            'rate_limit' => [
                'max_requests_per_minute' => 300,
            ],
            'cost_per_1k_input_tokens'  => 0.00000,
            'cost_per_1k_output_tokens' => 0.00000,
        ],

        'ollama' => [
            'name'   => 'Ollama (local)',
            'driver' => 'ollama',
            'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
            'model'   => env('OLLAMA_MODEL', 'llama3.2'),
            'vision_model' => env('OLLAMA_VISION_MODEL', 'llava'),
            'embed_model'  => env('OLLAMA_EMBED_MODEL', 'nomic-embed-text'),
            'rate_limit'  => [
                'max_requests_per_minute' => 9999,   // effectively unlimited
                'max_tokens_per_day'      => 999999,
            ],
            'cost_per_1k_input_tokens'  => 0.00000, // free — local
            'cost_per_1k_output_tokens' => 0.00000,
        ],

    ],

];
