{{-- resources/views/admin/ai/usage.blade.php --}}
@extends('layouts.app')

@section('header')
<div class="max-w-7xl mx-auto">
  <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">🤖 AI Usage &amp; Cost Dashboard</h1>
  <p class="text-white/70 text-sm md:text-base">Track API calls, tokens, and dollar spend across all AI providers</p>
</div>
@endsection

@section('content')
<div class="space-y-8">

  {{-- ── KPI Cards ──────────────────────────────────────────────────── --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
      <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-1">Total API Calls</p>
      <p class="text-2xl font-bold" style="color:var(--pageturner-dark);">{{ number_format($totalCalls) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
      <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-1">Total Tokens</p>
      <p class="text-2xl font-bold" style="color:var(--pageturner-primary);">{{ number_format($totalTokens) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
      <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-1">Estimated Cost</p>
      <p class="text-2xl font-bold {{ $totalCost > 1 ? 'text-red-600' : 'text-green-600' }}">
        ${{ number_format($totalCost, 4) }}
      </p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
      <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-1">Providers Active</p>
      <p class="text-2xl font-bold" style="color:var(--pageturner-secondary);">{{ $byProvider->count() }}</p>
    </div>
  </div>

  {{-- ── Breakdown By Provider ──────────────────────────────────────── --}}
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <h2 class="text-lg font-bold" style="color:var(--pageturner-dark); margin-bottom:1rem;">By Provider</h2>
    @if($byProvider->isEmpty())
      <p class="text-gray-500 text-sm">No usage data yet. Calls will appear here as AI features are used.</p>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b">
              <th class="text-left py-2 px-3 font-semibold text-gray-600">Provider</th>
              <th class="text-right py-2 px-3 font-semibold text-gray-600">Calls</th>
              <th class="text-right py-2 px-3 font-semibold text-gray-600">Tokens</th>
              <th class="text-right py-2 px-3 font-semibold text-gray-600">Cost</th>
            </tr>
          </thead>
          <tbody>
            @foreach($byProvider as $row)
            <tr class="border-b last:border-0 hover:bg-gray-50">
              <td class="py-2 px-3 font-medium flex items-center gap-2">
                @php $providers=['openai'=>'🔵','gemini'=>'🟢','huggingface'=>'🟠','ollama'=>'🦙']; @endphp
                {{ $providers[$row->provider] ?? '🤖' }} {{ ucfirst($row->provider) }}
              </td>
              <td class="text-right py-2 px-3">{{ number_format($row->calls) }}</td>
              <td class="text-right py-2 px-3">{{ number_format($row->tokens) }}</td>
              <td class="text-right py-2 px-3 font-semibold" style="color:{{ $row->cost > 0.5 ? 'var(--pageturner-error)' : 'var(--pageturner-success)' }};">
                ${{ number_format($row->cost, 4) }}
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  {{-- ── Breakdown By Feature ───────────────────────────────────────── --}}
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <h2 class="text-lg font-bold" style="color:var(--pageturner-dark); margin-bottom:1rem;">By Feature</h2>
    @if($byFeature->isEmpty())
      <p class="text-gray-500 text-sm">No per-feature data yet.</p>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b">
              <th class="text-left py-2 px-3 font-semibold text-gray-600">Feature</th>
              <th class="text-right py-2 px-3 font-semibold text-gray-600">Calls</th>
              <th class="text-right py-2 px-3 font-semibold text-gray-600">Tokens</th>
              <th class="text-right py-2 px-3 font-semibold text-gray-600">Cost</th>
            </tr>
          </thead>
          <tbody>
            @foreach($byFeature as $row)
            <tr class="border-b last:border-0 hover:bg-gray-50">
              <td class="py-2 px-3 font-medium">{{ $row->feature }}</td>
              <td class="text-right py-2 px-3">{{ number_format($row->calls) }}</td>
              <td class="text-right py-2 px-3">{{ number_format($row->tokens) }}</td>
              <td class="text-right py-2 px-3 font-semibold" style="color:var(--pageturner-primary);">
                ${{ number_format($row->cost, 4) }}
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  {{-- ── Recent Log Entries ─────────────────────────────────────────── --}}
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <h2 class="text-lg font-bold" style="color:var(--pageturner-dark); margin-bottom:1rem;">Recent Log Entries</h2>
    @if($recent->isEmpty())
      <p class="text-gray-500 text-sm">No log entries yet.</p>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-xs">
          <thead>
            <tr class="border-b text-gray-500">
              <th class="text-left py-2 px-2 font-semibold">When</th>
              <th class="text-left py-2 px-2 font-semibold">Provider</th>
              <th class="text-left py-2 px-2 font-semibold">Feature</th>
              <th class="text-right py-2 px-2 font-semibold">Tokens</th>
              <th class="text-right py-2 px-2 font-semibold">Cost</th>
              <th class="text-left py-2 px-2 font-semibold">Metadata</th>
            </tr>
          </thead>
          <tbody>
            @foreach($recent as $log)
            <tr class="border-b last:border-0 hover:bg-gray-50">
              <td class="py-2 px-2 whitespace-nowrap">{{ $log->created_at->diffForHumans() }}</td>
              <td class="py-2 px-2">{{ $log->provider }}</td>
              <td class="py-2 px-2">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700">
                  {{ $log->feature }}
                </span>
              </td>
              <td class="text-right py-2 px-2">{{ number_format($log->tokens_used) }}</td>
              <td class="text-right py-2 px-2 font-semibold"
                  style="color:{{ $log->cost_estimate > 0.5 ? 'var(--pageturner-error)' : 'inherit' }};">
                ${{ number_format($log->cost_estimate, 4) }}
              </td>
              <td class="py-2 px-2 max-w-[200px] truncate" title="{{ $log->metadata }}">
                {{ \Illuminate\Support\Str::limit($log->metadata ?? '—', 50) }}
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  {{-- ── Actions ───────────────────────────────────────────────────── --}}
  <div class="flex flex-wrap gap-3">
    <form method="POST" action="{{ route('admin.ai.usage.clear') }}">
      @csrf
      <input type="hidden" name="days" value="90">
      <button type="submit"
        class="px-5 py-2.5 rounded-lg text-sm font-semibold
               bg-red-50 text-red-700 border border-red-200
               hover:bg-red-100 transition-colors">
        🗑 Delete logs older than 90 days
      </button>
    </form>
  </div>

</div>
@endsection
