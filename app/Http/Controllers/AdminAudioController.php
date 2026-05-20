<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\AudioDescriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * AdminAudioController
 *
 * Admin management endpoints for the AI Audio Description and Voice Search
 * features.
 *
 * POST /admin/ai/audio-descriptions/regenerate  — bulk regenerate for all books
 * GET  /admin/ai/usage                            — usage/cost dashboard
 * POST /admin/ai/usage/clear                      — trim old ai_usage_logs rows
 */
class AdminAudioController extends Controller
{
    public function __construct(
        protected AudioDescriptionService $descService,
    ) {}

    /**
     * POST /admin/ai/audio-descriptions/regenerate
     * Regenerate AI audio descriptions for all books missing one.
     * Sends each task to the queue for background processing.
     *
     * Body: { count: 50 }  how many books to queue this batch
     */
    public function regenerate(Request $request)
    {
        $batchSize = min((int) ($request->input('count', 100)), 500);

        // Queue using the job instead of in-process processing
        \App\Jobs\ProcessAITask::dispatch([
            'type'      => 'audio_description_batch',
            'count'     => $batchSize,
            'with_audio'=> false,
        ])->delay(now()->addMinutes(2));

        return redirect()->back()
            ->with('success', "Queued AI description regeneration for up to {$batchSize} books. Processing in background.");
    }

    /**
     * GET /admin/ai/usage
     * Return basic cost dashboard data.
     */
    public function usage()
    {
        $totalCalls  = \App\Models\AIUsageLog::count();
        $totalTokens = \App\Models\AIUsageLog::sum('tokens_used');
        $totalCost   = \App\Models\AIUsageLog::sum('cost_estimate');

        $byProvider = \App\Models\AIUsageLog::selectRaw('provider, COUNT(*) as calls, SUM(tokens_used) as tokens, SUM(cost_estimate) as cost')
            ->groupBy('provider')
            ->orderByDesc('calls')
            ->get();

        $byFeature = \App\Models\AIUsageLog::selectRaw('feature, COUNT(*) as calls, SUM(tokens_used) as tokens, SUM(cost_estimate) as cost')
            ->groupBy('feature')
            ->orderByDesc('calls')
            ->get();

        $recent = \App\Models\AIUsageLog::latest()->limit(50)->get();

        return view('admin.ai.usage', compact(
            'totalCalls', 'totalTokens', 'totalCost', 'byProvider', 'byFeature', 'recent'
        ));
    }

    /**
     * POST /admin/ai/usage/clear
     * Delete logs older than 90 days (keeps recent for audit).
     */
    public function clearOldLogs(Request $request)
    {
        $days = (int) ($request->input('days', 90));
        $cutoff = now()->subDays($days);
        $deleted = \App\Models\AIUsageLog::where('created_at', '<', $cutoff)->delete();

        return redirect()->back()
            ->with('success', "Deleted {$deleted} AI usage log entries older than {$days} days.");
    }
}
