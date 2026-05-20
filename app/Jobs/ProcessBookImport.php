<?php

namespace App\Jobs;

use App\Models\BookImport;
use App\Services\BookImportService;
use App\Services\AuditLogService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessBookImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $importId;
    protected $filePath;
    protected $duplicateAction;

    public function __construct($importId, $filePath, $duplicateAction)
    {
        $this->importId = $importId;
        $this->filePath = $filePath;
        $this->duplicateAction = $duplicateAction;
    }

    public function handle(BookImportService $importService)
    {
        try {
            $importService->processImport($this->importId, $this->filePath, $this->duplicateAction);
        } catch (\Exception $e) {
            Log::error("Import job failed for import {$this->importId}: " . $e->getMessage());
            $import = BookImport::find($this->importId);
            if ($import) {
                $import->update([
                    'status' => 'failed',
                    'errors' => ['message' => $e->getMessage()]
                ]);
            }
        }
    }
}