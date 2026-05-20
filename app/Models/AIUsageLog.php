<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * AIUsageLog
 *
 * Records every AI API call for cost-tracking, audit, and debugging purposes.
 *
 * Columns:
 *  provider         — which AI service handled the call (openai, gemini, ollama …)
 *  feature          — feature key that triggered the call (voice_search, audio_description …)
 *  loggable_id/_type — optional relation to the book, user, or other entity involved
 *  tokens_used      — approximate tokens consumed by this call
 *  cost_estimate    — calculated USD cost based on provider pricing
 *  response_hash    — short hash of the raw response (for dedup/bug tracing)
 *  metadata         — free-form JSON: {model, prompt_len, response_len, latency_ms, …}
 */
class AIUsageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'feature',
        'loggable_type',
        'loggable_id',
        'tokens_used',
        'cost_estimate',
        'response_hash',
        'metadata',
    ];

    protected $casts = [
        'cost_estimate' => 'decimal:6',
        'metadata'      => 'array',
    ];

    /**
     * Polymorphic relation to the model this log entry belongs to
     * (e.g. Book, User, Order).
     */
    public function loggable()
    {
        return $this->morphTo();
    }
}
