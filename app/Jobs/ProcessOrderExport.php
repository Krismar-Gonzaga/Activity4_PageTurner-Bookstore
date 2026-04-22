<?php
// app/Jobs/ProcessOrderExport.php

namespace App\Jobs;

use App\Models\ExportLog;
use App\Services\OrderExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExportReadyMail;

class ProcessOrderExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $exportId;
    protected $filters;
    protected $format;
    
    public function __construct($exportId, $filters, $format)
    {
        $this->exportId = $exportId;
        $this->filters = $filters;
        $this->format = $format;
    }
    
    public function handle(OrderExportService $exportService)
    {
        $filePath = $exportService->exportOrders(
            $this->exportId,
            $this->filters,
            $this->format,
            auth()->id()
        );
        
        // Send email notification
        $export = ExportLog::find($this->exportId);
        if ($export && $export->user) {
            Mail::to($export->user->email)->send(new ExportReadyMail($export));
        }
    }
}