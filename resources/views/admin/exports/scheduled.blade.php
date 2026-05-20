@extends('layouts.app')

@section('title', 'Scheduled Exports')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8 space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Scheduled Exports</h1>
            <p class="text-sm text-gray-600">Set up daily sales reports emailed to administrators.</p>
        </div>
        <a href="{{ route('admin.orders.export.index') }}" class="text-[#8B4513] hover:underline">Back to Export Dashboard</a>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-md bg-red-50 text-red-700 border border-red-200">
            {{ $errors->first() }}
        </div>
    @endif

    @if(session('success'))
        <div class="p-4 rounded-md bg-green-50 text-green-700 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border rounded-xl p-6 shadow-sm">
        <h2 class="text-lg font-semibold mb-4">Create Scheduled Daily Sales Export</h2>
        <form method="POST" action="{{ route('admin.exports.scheduled.create') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="type" value="daily_sales">
            <input type="hidden" name="format" value="csv">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Export Name</label>
                <input type="text" name="name" class="w-full border rounded-md px-3 py-2" placeholder="Daily Sales Report">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Schedule Frequency</label>
                <select name="schedule" class="w-full border rounded-md px-3 py-2">
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Recipient Emails</label>
                <input type="text" name="recipients" class="w-full border rounded-md px-3 py-2" placeholder="admin1@example.com, admin2@example.com">
                <p class="text-xs text-gray-500 mt-1">Separate multiple emails using commas.</p>
            </div>

            <button type="submit" class="px-4 py-2 bg-[#8B4513] text-white rounded-md hover:bg-[#6f340e]">
                Save Scheduled Export
            </button>
        </form>
    </div>

    <div class="bg-white border rounded-xl p-6 shadow-sm">
        <h2 class="text-lg font-semibold mb-4">Current Scheduled Exports</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-600 border-b">
                        <th class="py-2">Name</th>
                        <th class="py-2">Type</th>
                        <th class="py-2">Schedule</th>
                        <th class="py-2">Next Run</th>
                        <th class="py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($scheduledExports as $scheduled)
                        <tr class="border-b">
                            <td class="py-2">{{ $scheduled->name }}</td>
                            <td class="py-2">{{ ucfirst(str_replace('_', ' ', $scheduled->type)) }}</td>
                            <td class="py-2">{{ ucfirst($scheduled->schedule) }}</td>
                            <td class="py-2">{{ optional($scheduled->next_run_at)->format('M d, Y h:i A') ?? 'Not set' }}</td>
                            <td class="py-2">{{ $scheduled->is_active ? 'Active' : 'Inactive' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-3 text-gray-500">No scheduled exports configured.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
