@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Audit Log Details</h1>
        <a href="{{ route('admin.audit-logs.index') }}" class="text-blue-600 hover:text-blue-900">
            ← Back to Logs
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">User</h3>
                    <p class="text-lg">{{ $log->user->name ?? 'System' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Event</h3>
                    <p class="text-lg">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($log->event === 'login_failed') bg-red-100 text-red-800
                            @elseif(str_contains($log->event, 'delete')) bg-red-100 text-red-800
                            @elseif(str_contains($log->event, 'create')) bg-green-100 text-green-800
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ ucfirst(str_replace('_', ' ', $log->event)) }}
                        </span>
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Auditable Type</h3>
                    <p class="text-lg">{{ $log->auditable_type ? class_basename($log->auditable_type) : 'N/A' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Timestamp</h3>
                    <p class="text-lg">{{ $log->created_at->format('M d, Y H:i:s') }}</p>
                </div>
            </div>

            @if($log->old_values || $log->new_values)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">Changes</h3>
                    <div class="grid grid-cols-2 gap-6">
                        @if($log->old_values)
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Old Values</h4>
                                <pre class="bg-gray-50 p-4 rounded overflow-x-auto">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        @endif
                        @if($log->new_values)
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">New Values</h4>
                                <pre class="bg-gray-50 p-4 rounded overflow-x-auto">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if($log->metadata)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">Metadata</h3>
                    <div class="bg-gray-50 p-4 rounded">
                        <table class="min-w-full">
                            <tbody>
                                @if(isset($log->metadata['ip_address']))
                                    <tr>
                                        <td class="pr-4 font-medium">IP Address:</td>
                                        <td>{{ $log->metadata['ip_address'] }}</td>
                                    </tr>
                                @endif
                                @if(isset($log->metadata['user_agent']))
                                    <tr>
                                        <td class="pr-4 font-medium">User Agent:</td>
                                        <td class="break-all">{{ \Illuminate\Support\Str::limit($log->metadata['user_agent'], 100) }}</td>
                                    </tr>
                                @endif
                                @if(isset($log->metadata['url']))
                                    <tr>
                                        <td class="pr-4 font-medium">URL:</td>
                                        <td>{{ $log->metadata['url'] }}</td>
                                    </tr>
                                @endif
                                @if(isset($log->metadata['method']))
                                    <tr>
                                        <td class="pr-4 font-medium">Method:</td>
                                        <td>{{ $log->metadata['method'] }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection