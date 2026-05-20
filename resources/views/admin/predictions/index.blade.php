@extends('layouts.app')

@section('header')
<div class="max-w-7xl mx-auto">
    <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">AI Sales Prediction &amp; Inventory</h1>
    <p class="text-white/70 text-sm md:text-base">Demand forecasting · Reorder suggestions · Stock-out alerts</p>
</div>
@endsection

@section('content')
<div class="space-y-8">

    {{-- ─── Summary KPI Cards ──────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-1">Books Tracked</p>
            <p class="text-2xl font-bold" style="color: var(--pageturner-dark);">{{ $summary['total_tracked'] }}</p>
            <p class="text-xs text-gray-400 mt-1">of {{ $summary['books_without_prediction'] + $summary['total_tracked'] }} total</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-1">Critical</p>
            <p class="text-2xl font-bold text-red-600">{{ $summary['critical_count'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Immediate action</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-1">Reorder Now</p>
            <p class="text-2xl font-bold text-orange-600">{{ $summary['reorder_now_count'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Awaiting reorder</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-1">Watch List</p>
            <p class="text-2xl font-bold text-amber-600">{{ $summary['watch_count'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Monitor closely</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-1">Needs Attention</p>
            <p class="text-2xl font-bold" style="color: var(--pageturner-primary);">{{ $summary['needs_attention'] }}</p>
            <p class="text-xs text-gray-400 mt-1">
                Pred. value
                <span class="font-semibold">${{ number_format($summary['total_predicted_value'] - $summary['total_stock_value'], 2) }}</span>
            </p>
        </div>
    </div>

    {{-- ─── Action Bar ────────────────────────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <form method="GET" action="{{ route('admin.predictions.index') }}" class="flex flex-wrap gap-2 items-center">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search book title or author…"
                class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-[var(--pageturner-accent)] focus:border-[var(--pageturner-accent)] outline-none w-56">

            <select name="category" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--pageturner-accent)] outline-none">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>
                        {{ $cat->name }} ({{ $cat->books_count }})
                    </option>
                @endforeach
            </select>

            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--pageturner-accent)] outline-none">
                <option value="">All Statuses</option>
                <option value="critical"    @selected(request('status') === 'critical')>Critical</option>
                <option value="reorder_now" @selected(request('status') === 'reorder_now')>Reorder Now</option>
                <option value="watch"       @selected(request('status') === 'watch')>Watch</option>
                <option value="ok"          @selected(request('status') === 'ok')>OK</option>
            </select>

            <button type="submit" class="px-5 py-2 rounded-lg text-sm font-medium border border-[var(--pageturner-primary)] text-[var(--pageturner-primary)] hover:bg-[var(--pageturner-primary)] hover:text-white transition-colors">
                Filter
            </button>
        </form>

        <form method="POST" action="{{ route('admin.predictions.refresh') }}">
            @csrf
            <button type="submit"
                onclick="return confirm('Recalculate predictions for all books based on the latest 180-day sales data?')"
                class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-white shadow-sm transition-colors"
                style="background-color: var(--pageturner-primary);"
                onmouseover="this.style.backgroundColor='var(--pageturner-dark)'"
                onmouseout="this.style.backgroundColor='var(--pageturner-primary)'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh Predictions
            </button>
        </form>
    </div>

    {{-- ─── Predictions Table ───────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-[var(--pageturner-light)] border-b border-gray-200">
                        <th class="text-left px-5 py-3 font-semibold text-gray-700 uppercase tracking-wider text-xs">Book</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-700 uppercase tracking-wider text-xs">Predicted Demand</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-700 uppercase tracking-wider text-xs">Current Stock</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-700 uppercase tracking-wider text-xs">Reorder Point</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-700 uppercase tracking-wider text-xs">Suggested Reorder</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-700 uppercase tracking-wider text-xs">Days Until Out</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-700 uppercase tracking-wider text-xs">Confidence</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-700 uppercase tracking-wider text-xs">Status</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-700 uppercase tracking-wider text-xs"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($predictions as $prediction)
                        @php
                            $stockBar = min(100, (int) round(
                                ($prediction->current_stock / max(1, $prediction->predicted_demand)) * 100
                            ));
                            $barColor = match($prediction->status) {
                                'critical'    => 'red',
                                'reorder_now' => 'orange',
                                'watch'       => 'amber',
                                default       => 'green',
                            };
                        @endphp
                        <tr class="hover:bg-[var(--pageturner-light)]/50 transition-colors">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-gray-800">{{ $prediction->book->title ?? 'Unknown Book' }}</p>
                                <p class="text-xs text-gray-500">{{ $prediction->book->author ?? '' }}</p>
                            </td>

                            <td class="px-5 py-4">
                                <span class="font-bold text-base" style="color: var(--pageturner-primary);">
                                    {{ number_format($prediction->predicted_demand) }}
                                </span>
                                <span class="text-xs text-gray-400 block">next 30 days</span>
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold">{{ $prediction->current_stock }}</span>
                                    <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full bg-{{ $barColor }}-500 transition-all"
                                             style="width: {{ $stockBar }}%; background-color: var(--pageturner-{{ $barColor }});">
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-4 text-gray-700">{{ number_format($prediction->reorder_point) }}</td>

                            <td class="px-5 py-4">
                                @if($prediction->suggested_reorder_qty > 0)
                                    <span class="inline-flex items-center gap-1 font-semibold text-[var(--pageturner-primary)]">
                                        @if($prediction->status === 'reorder_now' || $prediction->status === 'critical')
                                            <svg class="w-3.5 h-3.5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                        Order {{ number_format($prediction->suggested_reorder_qty) }}
                                    </span>
                                @else
                                    <span class="text-green-600 font-medium">No reorder needed</span>
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                @if($prediction->days_until_stockout !== null)
                                    <span class="font-semibold
                                        @if($prediction->days_until_stockout <= $prediction->lead_time_days) text-red-600
                                        @elseif($prediction->days_until_stockout <= $prediction->lead_time_days * 2) text-amber-600
                                        @else text-green-600 @endif">
                                        {{ $prediction->days_until_stockout }} days
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-20 h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full bg-[var(--pageturner-primary)]"
                                             style="width: {{ $prediction->confidence }}%;"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-600">{{ $prediction->confidence }}%</span>
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-full border {{ $prediction->getStatusBadgeClass() }}">
                                    {{ $prediction->getStatusLabel() }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.predictions.show', $prediction) }}"
                                    class="text-[var(--pageturner-primary)] text-xs font-medium hover:underline">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <p class="font-medium mb-1">No predictions yet</p>
                                <p class="text-xs text-gray-400 mb-3">Click <strong>Refresh Predictions</strong> to generate forecasts.</p>
                                <form method="POST" action="{{ route('admin.predictions.refresh') }}" class="inline-block">
                                    @csrf
                                    <button type="submit" class="text-sm px-4 py-2 rounded-lg text-white font-medium"
                                            style="background-color: var(--pageturner-primary);">Generate Now</button>
                                </form>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-5 py-4 border-t border-gray-200">
            {{ $predictions->links() }}
        </div>
    </div>
</div>
@endsection
