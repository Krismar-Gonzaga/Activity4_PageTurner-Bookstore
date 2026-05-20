@component('mail::message')
# {{ $details['status'] === 'failed' ? 'Backup Failed' : 'Backup Completed' }}

@if($details['status'] === 'failed')
The backup process has failed. Please check the system immediately.

**Error:** {{ $details['error'] ?? 'Unknown error' }}

**Time:** {{ $details['timestamp'] }}
@else
The weekly backup has been completed successfully.

**Backup Type:** {{ $details['type'] ?? 'daily' }}

**Time:** {{ $details['timestamp'] }}
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent