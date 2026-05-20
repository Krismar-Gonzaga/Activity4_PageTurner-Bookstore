{{-- resources/views/admin/books/import.blade.php --}}
@extends('layouts.app')

@section('title', 'Bulk Import Books - PageTurner')

@section('header')
<div class="inventory-header">
    <div>
        <h1 class="inventory-title">Bulk Import Books</h1>
        <p class="inventory-subtitle">Upload multiple books at once using CSV or Excel files</p>
    </div>
</div>
@endsection

@section('content')
<style>
    .import-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .upload-area {
        background: var(--pageturner-very-light);
        border: 2px dashed var(--pageturner-accent);
        border-radius: 1rem;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s;
        cursor: pointer;
    }
    
    .upload-area:hover {
        border-color: var(--pageturner-primary);
        background: var(--pageturner-light);
    }
    
    .upload-area.dragover {
        border-color: var(--pageturner-primary);
        background: rgba(139, 69, 19, 0.1);
    }
    
    .progress-bar {
        height: 8px;
        background: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--pageturner-primary), var(--pageturner-accent));
        transition: width 0.3s ease;
    }
    
    .import-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-processing { background: #dbeafe; color: #1e40af; }
    .status-completed { background: #d1fae5; color: #065f46; }
    .status-failed { background: #fee2e2; color: #991b1b; }
</style>

<div class="import-container" x-data="bookImport()" x-init="init()">
    <!-- Import Form -->
    <div class="import-card">
        <h3 class="text-lg font-semibold mb-4">Import Books</h3>
        
        <div class="mb-4">
            <a href="{{ route('admin.books.import.template') }}" class="text-pageturner-primary hover:underline">
                ↓ Download Template CSV
            </a>
        </div>
        
        <div class="upload-area" 
             @dragover.prevent="dragover = true" 
             @dragleave.prevent="dragover = false"
             @drop.prevent="handleDrop($event)"
             @click="$refs.fileInput.click()">
            
            <input type="file" 
                   ref="fileInput"
                   accept=".csv,.txt,.xlsx"
                   class="hidden"
                   @change="handleFileSelect($event)">
            
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            
            <p class="mt-2 text-sm text-gray-600">
                <span class="font-semibold">Click to upload</span> or drag and drop
            </p>
            <p class="text-xs text-gray-500">CSV, TXT, or Excel (XLSX) files, max 10MB</p>
        </div>
        
        <div class="mt-4" x-show="selectedFile">
            <div class="bg-gray-50 rounded p-3">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-medium" x-text="selectedFileName"></p>
                        <p class="text-sm text-gray-500" x-text="formattedFileSize"></p>
                    </div>
                    <div class="flex gap-2">
                        <select x-model="duplicateAction" class="px-3 py-1 border rounded text-sm">
                            <option value="skip">Skip duplicates</option>
                            <option value="update">Update existing books</option>
                        </select>
                        <button @click="uploadFile()" 
                                :disabled="uploading"
                                class="bg-pageturner-primary text-white px-4 py-1 rounded hover:bg-pageturner-secondary transition">
                            <span x-show="!uploading">Upload</span>
                            <span x-show="uploading">Uploading...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Progress Tracking -->
    <div class="import-card" x-show="currentImport">
        <h3 class="text-lg font-semibold mb-4">Import Progress</h3>
        
        <div class="space-y-3">
            <div class="flex justify-between text-sm">
                <span>Status: <span class="font-semibold" x-text="importStatus"></span></span>
                <span x-show="importTotal > 0">
                    <span x-text="importProcessed"></span> / <span x-text="importTotal"></span> rows
                </span>
            </div>
            
            <div class="progress-bar">
                <div class="progress-fill" :style="{ width: progressPercentage + '%' }"></div>
            </div>
            
            <div class="flex justify-between text-sm text-gray-600">
                <span>✅ Successful: <span x-text="importSuccessful"></span></span>
                <span>❌ Failed: <span x-text="importFailed"></span></span>
            </div>
            
            <div x-show="importCompleted && importFailed > 0" class="mt-3">
                <a :href="errorReportUrl" 
                   class="text-pageturner-primary hover:underline text-sm">
                    Download Error Report
                </a>
            </div>
        </div>
    </div>
    
    <!-- Recent Imports -->
    <div class="import-card">
        <h3 class="text-lg font-semibold mb-4">Recent Imports</h3>
        
        <div class="space-y-3">
            @foreach($recentImports as $import)
            <div class="border rounded p-3">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-medium">{{ $import->original_name }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $import->created_at->format('M d, Y H:i') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="status-badge status-{{ $import->status }}">
                            {{ ucfirst($import->status) }}
                        </span>
                        @if($import->status === 'completed')
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $import->successful_rows }} imported, 
                                {{ $import->failed_rows }} failed
                            </div>
                            @if($import->failed_rows > 0)
                                <a href="{{ route('admin.books.import.errors', $import->id) }}" 
                                   class="text-xs text-pageturner-primary hover:underline">
                                    Download errors
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
function bookImport() {
    return {
        selectedFile: null,
        selectedFileName: '',
        formattedFileSize: '',
        duplicateAction: 'skip',
        uploading: false,
        currentImport: null,
        importStatus: '',
        importTotal: 0,
        importProcessed: 0,
        importSuccessful: 0,
        importFailed: 0,
        importCompleted: false,
        importId: null,
        progressInterval: null,
        
        init() {
            // Any initialization
        },
        
        handleDrop(event) {
            this.dragover = false;
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                this.processFile(files[0]);
            }
        },
        
        handleFileSelect(event) {
            const files = event.target.files;
            if (files.length > 0) {
                this.processFile(files[0]);
            }
        },
        
        processFile(file) {
            const fileName = file.name ? file.name.toLowerCase() : '';
            const hasValidExtension = fileName.endsWith('.csv') || fileName.endsWith('.txt') || fileName.endsWith('.xlsx');
            const validTypes = [
                'text/csv',
                'application/csv',
                'text/plain',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ''
            ];

            if (!hasValidExtension && !validTypes.includes(file.type)) {
                alert('Please upload a CSV, TXT, or XLSX file');
                return;
            }
            
            if (file.size > 10 * 1024 * 1024) {
                alert('File size must be less than 10MB');
                return;
            }
            
            this.selectedFile = file;
            this.selectedFileName = file.name;
            this.formattedFileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
        },
        
        async uploadFile() {
            if (!this.selectedFile) return;
            
            this.uploading = true;
            const formData = new FormData();
            formData.append('file', this.selectedFile);
            formData.append('duplicate_action', this.duplicateAction);
            
            try {
                const response = await fetch('{{ route("admin.books.import.upload") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.importId = data.import_id;
                    this.currentImport = true;
                    this.startPolling();
                } else {
                    alert('Upload failed: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Upload error:', error);
                alert('Upload failed. Please try again.');
            } finally {
                this.uploading = false;
                this.selectedFile = null;
                this.selectedFileName = '';
            }
        },
        
        startPolling() {
            this.progressInterval = setInterval(async () => {
                await this.fetchProgress();
                
                if (this.importCompleted) {
                    clearInterval(this.progressInterval);
                }
            }, 2000);
        },
        
        async fetchProgress() {
            if (!this.importId) return;
            
            try {
                const response = await fetch(`{{ url("admin/books/import/status") }}/${this.importId}`);
                const data = await response.json();
                
                this.importStatus = data.status;
                this.importTotal = data.total_rows;
                this.importProcessed = data.processed_rows;
                this.importSuccessful = data.successful_rows;
                this.importFailed = data.failed_rows;
                this.importCompleted = data.status === 'completed' || data.status === 'failed';
                
                if (this.importCompleted) {
                    if (data.status === 'completed') {
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                }
            } catch (error) {
                console.error('Error fetching progress:', error);
            }
        },
        
        get progressPercentage() {
            if (this.importTotal === 0) return 0;
            return (this.importProcessed / this.importTotal) * 100;
        },
        
        get errorReportUrl() {
            return `{{ url("admin/books/import/errors") }}/${this.importId}`;
        }
    }
}
</script>
@endsection