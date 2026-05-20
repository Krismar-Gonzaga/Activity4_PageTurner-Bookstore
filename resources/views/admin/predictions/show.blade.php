@extends('layouts.app')

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Merriweather:ital,wght@0,300;0,400;0,700;1,400&display=swap" rel="stylesheet">

@section('header')
<div class="max-w-7xl mx-auto">
    <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Prediction Detail</h1>
    <p class="text-white/70 text-sm md:text-base">
        "{{ $prediction->book?->title ?? 'Unknown Book' }}" — Demand &amp; Inventory Forecast
    </p>
</div>
@endsection

@php
    $isCritical = $prediction->status === 'critical';
    $isReorder  = $prediction->status === 'reorder_now';
    $statusColor = \App\Models\SalesPrediction::find($prediction->id)?->getStatusBadgeClass() ?? 'bg-green-100 text-green-800';
    $stockPct   = min(100, (int) round(($prediction->current_stock / max(1, $prediction->predicted_demand)) * 100));

    // Build monotone labels from history
    $historyLabels  = $history->keys()->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->format('M Y'))->implode(', ');
    $historyValues  = $history->values()->implode(', ');

    // Next 4-week projected demand line
    $weeklyDemand   = round($prediction->predicted_demand / 4, 1);
    $projections    = collect(range(1, 4))->map(fn($w) => max(0, $weeklyDemand + rand(-2, 2)))->implode(', ');
@endphp

@section('content')
<div class="space-y-6">
    {{-- ══════════════════════════════════════════════════════════════════════
         BOOK SUMMARY CARD
    ═══════════════════════════════════════════════════════════════════════ --}}
    @php $book = $prediction->book; @endphp
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 md:p-6">
        <div class="flex flex-col md:flex-row gap-5">
            <div class="w-28 h-40 flex-shrink-0 rounded-lg overflow-hidden shadow-inner bg-gray-100 border border-gray-200">
                @if($book && $book->cover_image)
                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-3 flex-wrap">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold" style="color: var(--pageturner-dark);">
                            {{ $book?->title ?? 'Unknown' }}
                        </h2>
                        <p class="text-gray-500 text-sm mt-0.5">{{ $book?->author ?? '' }}</p>
                        @if($book?->category)
                            <span class="inline-block mt-1 px-2.5 py-0.5 bg-[var(--pageturner-light)] text-[var(--pageturner-primary)] text-xs font-medium rounded-full">
                                {{ $book->category->name }}
                            </span>
                        @endif
                    </div>
                    <span class="inline-block px-3 py-1 text-sm font-bold rounded-full border {{ $prediction->getStatusBadgeClass() }}">
                        {{ $prediction->getStatusLabel() }}
                    </span>
                </div>
                <p class="text-xs text-gray-400 mt-3">
                    Period: {{ $prediction->period_from?->format('M d, Y') }} — {{ $prediction->period_to?->format('M d, Y') }}
                </p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         PREDICTION METRICS
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-1">Predicted Demand</p>
            <p class="text-3xl font-bold" style="color: var(--pageturner-primary);">
                {{ number_format($prediction->predicted_demand) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">units in next 30 days</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-1">Current Stock</p>
            <p class="text-3xl font-bold @if($isCritical) text-red-600 @else text-gray-800 @endif">
                {{ number_format($prediction->current_stock) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">units on hand</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-1">Reorder Point</p>
            <p class="text-3xl font-bold text-amber-600">{{ number_format($prediction->reorder_point) }}</p>
            <p class="text-xs text-gray-400 mt-1">units</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-1">Suggested Reorder</p>
            <p class="text-3xl font-bold @if($prediction->suggested_reorder_qty > 0) text-[var(--pageturner-primary)] @else text-green-600 @endif">
                {{ number_format($prediction->suggested_reorder_qty) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">units to restock</p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         STOCK PROGRESS BAR + CONFIDENCE + DAYS TO OUT
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Stock coverage bar --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 md:col-span-1">
            <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-3">Stock Coverage</p>
            <div class="w-full h-4 bg-gray-200 rounded-full overflow-hidden mb-2">
                <div class="h-full rounded-full transition-all duration-300"
                     style="width: {{ $stockPct }}%;
                            @if($isCritical) background-color: #ef4444;
                            @elseif($isReorder) background-color: #f97316;
                            @elseif($stockPct < 40) background-color: #f59e0b;
                            @else background-color: #2E7D32; @endif">
                </div>
            </div>
            <p class="text-xs text-gray-500">
                {{ $prediction->current_stock }} / {{ number_format($prediction->predicted_demand) }} units
                ({{ $stockPct }}% coverage)
            </p>
        </div>

        {{-- Confidence --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-3">Forecast Confidence</p>
            <div class="flex items-center gap-3">
                <div class="flex-1">
                    <div class="w-full h-4 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-[var(--pageturner-primary)]"
                             style="width: {{ $prediction->confidence }}%"></div>
                    </div>
                </div>
                <span class="text-lg font-bold text-gray-800">{{ $prediction->confidence }}%</span>
            </div>
            <p class="text-xs text-gray-400 mt-2">
                @if($prediction->confidence >= 85) High confidence (strong historical data)
                @elseif($prediction->confidence >= 60) Moderate confidence
                @else Low confidence — insufficient history @endif
            </p>
        </div>

        {{-- Days until stockout --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 @if($isCritical) border-red-300 bg-red-50/40 @endif">
            <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-3">Days Until Stockout</p>
            @if($prediction->days_until_stockout !== null)
                <p class="text-3xl font-bold
                    @if($prediction->days_until_stockout <= $prediction->lead_time_days) text-red-600
                    @elseif($prediction->days_until_stockout <= $prediction->lead_time_days * 2) text-amber-600
                    @else text-green-600 @endif">
                    {{ $prediction->days_until_stockout }} days
                </p>
                @if($isCritical || $isReorder)
                    <p class="text-xs font-semibold text-red-600 mt-1">⚠ Lead time: {{ $prediction->lead_time_days }} days</p>
                @else
                    <p class="text-xs text-gray-400 mt-1">Lead time: {{ $prediction->lead_time_days }} days</p>
                @endif
            @else
                <p class="text-3xl font-bold text-gray-400">—</p>
                <p class="text-xs text-gray-400 mt-1">Not enough data to estimate</p>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         SALES HISTORY CHART
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 md:p-6">
        <h3 class="page-turner-font text-lg font-bold mb-4" style="color: var(--pageturner-dark);">
            Sales History — Last 6 Months
        </h3>

        @if($history->isNotEmpty())
            @php
                $maxVal = $history->max();
                $chartBars = $history->map(function ($val) use ($maxVal) {
                    return $maxVal > 0 ? (int) round(($val / $maxVal) * 100) : 40;
                });
            @endphp
            <div class="flex items-end gap-3 h-40">
                @foreach($history as $month => $total)
                    <div class="flex flex-col items-center gap-1 flex-1">
                        <span class="text-[10px] text-gray-400 font-semibold">{{ $total }}</span>
                        <div class="w-full rounded-t-md bg-[var(--pageturner-accent)] transition-all"
                             style="height: {{ $chartBars[$month] }}%; min-height: 4px;"
                             title="{{ $month }}: {{ $total }} units">
                        </div>
                        <span class="text-[10px] text-gray-500 whitespace-nowrap mt-1">
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M') }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-8 text-gray-400">
                <svg class="w-10 h-10 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <p class="text-sm">No historical sales data available.</p>
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         RECOMMENDATIONS
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 md:p-6">
        <h3 class="page-turner-font text-lg font-bold mb-4" style="color: var(--pageturner-dark);">
            Recommended Actions
        </h3>
        <ul class="space-y-3">
            <li class="flex items-start gap-3 p-3 rounded-lg
                @if($isCritical) bg-red-50 border border-red-100
                @elseif($isReorder) bg-orange-50 border border-orange-100
                @else bg-gray-50 border border-gray-100 @endif">
                <span class="mt-0.5">
                    @if($isCritical)
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    @elseif($isReorder)
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @else
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                </span>
                @if($prediction->current_stock <= 0)
                    <p class="text-sm text-red-800"><strong>Stock is zero.</strong> Place a reorder of <strong>{{ number_format($prediction->suggested_reorder_qty ?? 0) }} units</strong> immediately to avoid lost sales.</p>
                @elseif($prediction->suggested_reorder_qty > 0)
                    <p class="text-sm">
                        @if($isCritical)
                            <span class="font-semibold text-red-800">Urgent:</span>
                        @else
                            Reorder:
                        @endif
                        Place a purchase order for
                        <strong>{{ number_format($prediction->suggested_reorder_qty) }} units</strong>
                        to cover next 30-day demand with 30% safety buffer.
                        Expected lead time is
                        <strong>{{ $prediction->lead_time_days }} days</strong>.
                    </p>
                @else
                    <p class="text-sm text-green-700">Stock levels are healthy. No reorder needed at this time.</p>
                @endif
            </li>

            @if($prediction->days_until_stockout !== null && $prediction->days_until_stockout <= $prediction->lead_time_days)
                <li class="flex items-start gap-3 p-3 rounded-lg bg-red-50 border border-red-100">
                    <span class="mt-0.5">
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10zm-1-7.414V5a1 1 0 00-2 0v9.586l-2.293-2.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l4-4a1 1 0 00-1.414-1.414L11 14.586Z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                    <p class="text-sm text-red-800">
                        <strong>Stock will run out before the next shipment arrives.</strong>
                        Days to stockout <strong>({{ $prediction->days_until_stockout }}d)</strong>
                        is less than the supplier lead time <strong>({{ $prediction->lead_time_days }}d)</strong>.
                        Prioritise this reorder to prevent lost revenue.
                    </p>
                </li>
            @endif
        </ul>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         INFO / METHODOLOGY
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="bg-[var(--pageturner-light)] rounded-xl border border-[var(--pageturner-accent)]/20 p-5">
        <h3 class="page-turner-font font-semibold text-sm mb-2" style="color: var(--pageturner-dark);">
            How This Forecast Is Calculated
        </h3>
        <p class="text-xs text-gray-600 leading-relaxed">
            Demand for the next 30 days is derived from a
            <strong>weighted blend</strong> of the last 30-day sales velocity (70 % weight)
            and the last 180-day daily average (30 % weight).
            <br><br>
            The <strong>reorder point</strong> is set to the demand that will arrive during the
            supplier lead time plus a 30 % safety buffer.
            <br><br>
            The <strong>suggested reorder quantity</strong> brings current stock up to
            predicted demand + 30 % safety buffer.
            <br><br>
            The <strong>Confidence</strong> score reflects how much historical data exists.
            Adjust <strong>lead time</strong> in the dashboard for your supplier to fine-tune alerts.
        </p>
    </div>

    <div class="pt-2">
        <a href="{{ route('admin.predictions.index') }}"
            class="text-sm text-[var(--pageturner-primary)] hover:underline hover:text-[var(--pageturner-dark)]">
            &#8592; Back to all predictions
        </a>
    </div>
</div>
@endsection
