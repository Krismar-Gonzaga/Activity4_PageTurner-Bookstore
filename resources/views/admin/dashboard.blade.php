@extends('layouts.app')

@section('header')
<div class="max-w-7xl mx-auto">
    <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Admin Dashboard</h1>
    <p class="text-white/70 text-sm md:text-base">Overview of your bookstore at a glance</p>
</div>
@endsection

@section('content')
<div class="space-y-8">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <!-- Stats Cards -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm">Total Books</h3>
            <p class="text-3xl font-bold">{{ $stats['total_books'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm">Total Orders</h3>
            <p class="text-3xl font-bold">{{ $stats['total_orders'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm">Total Users</h3>
            <p class="text-3xl font-bold">{{ $stats['total_users'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm">Categories</h3>
            <p class="text-3xl font-bold">{{ $stats['total_categories'] }}</p>
        </div>

        <!-- Backup Health Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm">Backup Status</h3>
            <p class="text-lg font-bold {{ $stats['backup_health']['health_status'] === 'healthy' ? 'text-green-600' : 'text-yellow-600' }}">
                {{ ucfirst($stats['backup_health']['health_status'] ?? 'Unknown') }}
            </p>
            <a href="{{ route('admin.backups.index') }}" class="text-xs text-blue-600 hover:underline">Manage Backups</a>
        </div>
    </div>

    {{-- ── AI Sales Prediction Quick Preview ─────────────────────────────────-- --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('admin.predictions.index', ['status' => 'critical']) }}"
            class="bg-white rounded-xl shadow-sm border border-red-200 p-5 hover:shadow-md transition-all group">
            <div class="flex items-center justify-between mb-1">
                <p class="text-xs uppercase tracking-wider text-red-500 font-semibold">Critical</p>
                <svg class="w-4 h-4 text-red-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-red-600">
                @foreach(\App\Models\SalesPrediction::critical()->get() as $p){{ $loop->index }}@endforeach
            </p>
            <p class="text-xs text-gray-400 mt-1">
                @php $criticalCount = \App\Models\SalesPrediction::critical()->count(); @endphp
                {{ $criticalCount }} {{ \Illuminate\Support\Str::plural('book', $criticalCount) }} out of stock or below lead time
            </p>
        </a>

        <a href="{{ route('admin.predictions.index', ['status' => 'reorder_now']) }}"
            class="bg-white rounded-xl shadow-sm border border-orange-200 p-5 hover:shadow-md transition-all group">
            <div class="flex items-center justify-between mb-1">
                <p class="text-xs uppercase tracking-wider text-orange-500 font-semibold">Reorder Now</p>
                <svg class="w-4 h-4 text-orange-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-orange-600">
                @php $rnCount = \App\Models\SalesPrediction::reorderNow()->count(); @endphp
                {{ $rnCount }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Books below reorder point</p>
        </a>

        <a href="{{ route('admin.predictions.index', ['status' => 'watch']) }}"
            class="bg-white rounded-xl shadow-sm border border-amber-200 p-5 hover:shadow-md transition-all group">
            <div class="flex items-center justify-between mb-1">
                <p class="text-xs uppercase tracking-wider text-amber-600 font-semibold">Watch List</p>
                <svg class="w-4 h-4 text-amber-500 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-amber-600">
                @php $wCount = \App\Models\SalesPrediction::watch()->count(); @endphp
                {{ $wCount }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Books approaching threshold</p>
        </a>

        <a href="{{ route('admin.predictions.index') }}"
            class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all group">
            <div class="flex items-center justify-between mb-1">
                <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold">AI Predictions</p>
                <svg class="w-4 h-4 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-2xl font-bold" style="color: var(--pageturner-primary);">
                @php $tracked = \App\Models\SalesPrediction::count(); @endphp
                {{ $tracked }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Books tracked · View full report</p>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Recent Orders</h2>
            <div class="space-y-4">
                @foreach($stats['recent_orders'] as $order)
                    <div class="flex justify-between items-center border-b pb-2">
                        <div>
                            <p class="font-medium">Order #{{ $order->id }}</p>
                            <p class="text-sm text-gray-600">{{ $order->user->name }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded 
                            @if($order->status === 'completed') bg-green-100 text-green-800
                            @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Users -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Recent Users</h2>
            <div class="space-y-4">
                @foreach($stats['recent_users'] as $user)
                    <div class="flex justify-between items-center border-b pb-2">
                        <div>
                            <p class="font-medium">{{ $user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $user->email }}</p>
                        </div>
                        <span class="text-sm text-gray-500">
                            {{ $user->created_at->diffForHumans() }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
        <!-- Recent Reviews -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Recent Reviews</h2>
            <div class="space-y-4">
                @forelse($stats['recent_reviews'] as $review)
                    <div class="border-b pb-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium">{{ $review->user->name }}</p>
                                <p class="text-sm text-gray-600">on "{{ $review->book->title }}"</p>
                            </div>
                            <div class="flex items-center">
                                <!-- Star rating display -->
                                <div class="flex text-yellow-400 mr-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 fill-current text-gray-300" viewBox="0 0 20 20">
                                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                            </svg>
                                        @endif
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-500">
                                    {{ $review->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                        @if($review->comment)
                            <p class="text-sm text-gray-700 mt-2 italic">
                                {{ \Illuminate\Support\Str::limit($review->comment, 100) }}
                            </p>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No reviews yet</p>
                @endforelse
            </div>
        </div>
        
        <!-- Recent Audit Logs -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Recent Activity</h2>
                <a href="{{ route('admin.audit-logs.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">
                    View All →
                </a>
            </div>
            <div class="space-y-4">
                @forelse($stats['recent_audit_logs'] as $log)
                    <div class="flex justify-between items-center border-b pb-2">
                        <div>
                            <p class="font-medium">
                                {{ $log->user->name ?? 'System' }}
                            </p>
                            <p class="text-sm text-gray-600">
                                {{ ucfirst(str_replace('_', ' ', $log->event)) }}
                            </p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">
                            {{ $log->created_at->diffForHumans() }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No activity yet</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection