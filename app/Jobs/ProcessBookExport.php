<?php
// app/Jobs/ProcessBookExport.php

namespace App\Jobs;

use App\Models\ExportJob;
use App\Services\BookExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessBookExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $exportId;

    public function __construct($exportId)
    {
        $this->exportId = $exportId;
    }

    public function handle(BookExportService $exportService)
    {
        $export = ExportJob::find($this->exportId);
        
        if (!$export) {
            Log::error("Export job {$this->exportId} not found");
            return;
        }

        try {
            $exportService->export(
                $this->exportId,
                $export->filters,
                $export->selected_fields,
                $export->format,
                $export->user_id
            );
            
        } catch (\Exception $e) {
            Log::error("Export failed for job {$this->exportId}: {$e->getMessage()}");
            $export->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
        }
    }
}