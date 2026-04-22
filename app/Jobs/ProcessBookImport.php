<?php
// app/Jobs/ProcessBookImport.php

namespace App\Jobs;

use App\Models\BookImport;
use App\Services\BookImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        $importService->processImport($this->importId, $this->filePath, $this->duplicateAction);
    }
}