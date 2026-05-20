@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Audit Logs</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.audit-logs.export') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                Export CSV
            </a>
            <button onclick="runBackup()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Run Backup
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4 border-b">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Event Type</label>
                    <select name="event" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">All Events</option>
                        @foreach($events as $event)
                            <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $event)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded w-full">
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Auditable</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($logs as $log)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ $log->user->name ?? 'System' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($log->event === 'login_failed') bg-red-100 text-red-800
                                @elseif(str_contains($log->event, 'delete')) bg-red-100 text-red-800
                                @elseif(str_contains($log->event, 'create')) bg-green-100 text-green-800
                                @else bg-blue-100 text-blue-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $log->event)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ $log->auditable_type ? class_basename($log->auditable_type) : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ $log->created_at->format('M d, Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('admin.audit-logs.show', $log->id) }}" 
                               class="text-blue-600 hover:text-blue-900">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No audit logs found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>

<script>
function runBackup() {
    fetch('{{ route('admin.audit-logs.backup') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
    });
}
</script>
@endsection