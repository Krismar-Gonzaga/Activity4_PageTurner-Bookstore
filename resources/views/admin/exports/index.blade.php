@extends('layouts.app')

@section('title', 'Order Exports - Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Order Export Module</h1>
            <p class="text-sm text-gray-600">Admin order exports plus financial reporting exports (Revenue Summary and Tax Report), with scheduled daily sales emails.</p>
        </div>
        <a href="{{ route('admin.exports.scheduled') }}" class="px-4 py-2 bg-[#8B4513] text-white rounded-md hover:bg-[#6f340e]">
            Scheduled Exports
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-md bg-green-50 text-green-700 border border-green-200">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white border rounded-xl p-6 shadow-sm">
            <h2 class="text-lg font-semibold mb-4">Admin Order Exports</h2>
            <form method="POST" action="{{ route('admin.orders.export') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="format" value="csv">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Status</label>
                    <select name="status" class="w-full border rounded-md px-3 py-2">
                        <option value="">All statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                        <input type="date" name="date_from" class="w-full border rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                        <input type="date" name="date_to" class="w-full border rounded-md px-3 py-2">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                    <select name="customer_id" class="w-full border rounded-md px-3 py-2">
                        <option value="">All customers</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->email }})</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="w-full px-4 py-2 bg-[#8B4513] text-white rounded-md hover:bg-[#6f340e]">
                    Export Orders (CSV)
                </button>
            </form>
        </div>

        <div class="bg-white border rounded-xl p-6 shadow-sm space-y-4">
            <h2 class="text-lg font-semibold">Financial Reporting Exports</h2>
            <form method="GET" action="{{ route('admin.reports.revenue.export') }}" class="space-y-3">
                <input type="hidden" name="format" value="csv">
                <p class="text-sm text-gray-600">Revenue summaries</p>
                <div class="grid grid-cols-2 gap-3">
                    <input type="date" name="date_from" class="border rounded-md px-3 py-2">
                    <input type="date" name="date_to" class="border rounded-md px-3 py-2">
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Export Revenue CSV</button>
            </form>

            <form method="GET" action="{{ route('admin.reports.tax.export') }}" class="space-y-3">
                <input type="hidden" name="format" value="csv">
                <p class="text-sm text-gray-600">Tax reports</p>
                <div class="grid grid-cols-2 gap-3">
                    <input type="date" name="date_from" class="border rounded-md px-3 py-2">
                    <input type="date" name="date_to" class="border rounded-md px-3 py-2">
                </div>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Export Tax CSV</button>
            </form>
        </div>
    </div>

    <div class="bg-white border rounded-xl p-6 shadow-sm">
        <h2 class="text-lg font-semibold mb-4">Recent Export Jobs</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-600 border-b">
                        <th class="py-2">Type</th>
                        <th class="py-2">Requested By</th>
                        <th class="py-2">Format</th>
                        <th class="py-2">Status</th>
                        <th class="py-2">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentExports as $export)
                        <tr class="border-b">
                            <td class="py-2">{{ ucfirst(str_replace('_', ' ', $export->export_type)) }}</td>
                            <td class="py-2">{{ $export->user?->name ?? 'N/A' }}</td>
                            <td class="py-2 uppercase">{{ $export->format }}</td>
                            <td class="py-2">{{ ucfirst($export->status) }}</td>
                            <td class="py-2">{{ $export->created_at->format('M d, Y h:i A') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-3 text-gray-500">No export logs yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
