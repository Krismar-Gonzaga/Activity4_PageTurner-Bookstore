@extends('layouts.app')

@section('header')
<div class="max-w-7xl mx-auto">
    <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Database Backup Management</h1>
    <p class="text-white/70 text-sm md:text-base">Automated backups, retention policies, and manual triggers</p>
</div>
@endsection

@section('content')
<div class="space-y-6" x-data="backupManager()">
    <!-- Backup Health Status -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-gray-500 text-sm">Backup Status</h3>
            <p class="text-xl font-bold" :class="health.health_status === 'healthy' ? 'text-green-600' : 'text-yellow-600'">
                <span x-text="health.health_status === 'healthy' ? 'Healthy' : 'Warning'"></span>
            </p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-gray-500 text-sm">Total Backups</h3>
            <p class="text-xl font-bold" x-text="health.total_backups"></p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-gray-500 text-sm">Next Scheduled</h3>
            <p class="text-xl font-bold" x-text="health.next_scheduled"></p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-gray-500 text-sm">Last Backup</h3>
            <p class="text-sm" x-text="health.last_backup ? new Date(health.last_backup.last_modified * 1000).toLocaleString() : 'Never'"></p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
        <div class="flex space-x-4">
            <button @click="createBackup" 
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                <i class="fas fa-play mr-2"></i>Create Backup Now
            </button>
            <button @click="cleanBackups" 
                    class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 transition">
                <i class="fas fa-broom mr-2"></i>Clean Old Backups
            </button>
        </div>
        <div class="mt-4 text-sm text-gray-600">
            <p><strong>Retention Policy:</strong> 7 daily, 4 weekly, 12 monthly backups</p>
            <p><strong>Schedule:</strong> Daily at 02:00 AM | Weekly full backup Sundays at 02:30 AM</p>
        </div>
    </div>

    <!-- Backup List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h2 class="text-xl font-bold">Available Backups</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Filename</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Size</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="backup in backups" :key="backup.filename">
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium" x-text="backup.filename"></td>
                            <td class="px-6 py-4 text-sm" x-text="formatSize(backup.size)"></td>
                            <td class="px-6 py-4 text-sm" x-text="new Date(backup.last_modified * 1000).toLocaleString()"></td>
                            <td class="px-6 py-4 text-sm" x-text="backup.disk"></td>
                            <td class="px-6 py-4 text-right text-sm">
                                <button @click="deleteBackup(backup.filename)" 
                                        class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="backups.length === 0">
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No backups available</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function backupManager() {
    return {
        backups: [],
        health: { health_status: 'unknown', total_backups: 0, next_scheduled: '02:00 AM daily', last_backup: null },
        
        init() {
            this.fetchBackups();
            this.fetchHealth();
        },
        
        async fetchBackups() {
            try {
                const response = await fetch('{{ route('admin.backups.index') }}');
                const data = await response.json();
                if (data.backups) {
                    this.backups = data.backups;
                    this.health.total_backups = data.backups.length;
                }
            } catch (e) {
                console.error('Failed to fetch backups:', e);
            }
        },
        
        async fetchHealth() {
            try {
                const response = await fetch('{{ route('admin.backups.health') }}');
                const data = await response.json();
                this.health = data;
            } catch (e) {
                console.error('Failed to fetch health:', e);
            }
        },
        
        async createBackup() {
            try {
                const response = await fetch('{{ route('admin.backups.store') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                });
                const data = await response.json();
                if (data.success) {
                    alert('Backup created successfully: ' + data.filename);
                    this.fetchBackups();
                } else {
                    alert('Backup failed: ' + data.message);
                }
            } catch (e) {
                alert('Backup failed: ' + (e.message || 'Unknown error occurred'));
            }
        },
        
        async cleanBackups() {
            if (!confirm('Are you sure you want to clean old backups?')) return;
            try {
                const response = await fetch('{{ route('admin.backups.clean') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                });
                const data = await response.json();
                alert(data.message);
                this.fetchBackups();
            } catch (e) {
                alert('Error cleaning backups');
            }
        },
        
        async deleteBackup(filename) {
            if (!confirm('Are you sure you want to delete this backup?')) return;
            try {
                const response = await fetch(`/admin/backups/${filename}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                });
                const data = await response.json();
                if (data.success) {
                    this.fetchBackups();
                } else {
                    alert(data.message);
                }
            } catch (e) {
                alert('Error deleting backup');
            }
        },
        
        formatSize(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    }
}
</script>
@endsection