<?php
// app/Console/Commands/ProcessScheduledExports.php

namespace App\Console\Commands;

use App\Models\ScheduledExport;
use App\Services\OrderExportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\ScheduledExportMail;

class ProcessScheduledExports extends Command
{
    protected $signature = 'exports:process-scheduled';
    protected $description = 'Process scheduled exports';
    
    protected $exportService;
    
    public function __construct(OrderExportService $exportService)
    {
        parent::__construct();
        $this->exportService = $exportService;
    }
    
    public function handle()
    {
        $scheduledExports = ScheduledExport::where('is_active', true)
            ->where('next_run_at', '<=', now())
            ->get();
            
        foreach ($scheduledExports as $scheduled) {
            try {
                $filePath = $this->generateExport($scheduled);
                
                // Send email to recipients
                foreach ($scheduled->recipients as $recipient) {
                    Mail::to($recipient)->send(new ScheduledExportMail($scheduled, $filePath));
                }
                
                $scheduled->update([
                    'last_run_at' => now(),
                    'next_run_at' => $this->calculateNextRun($scheduled->schedule)
                ]);
                
                $this->info("Processed scheduled export: {$scheduled->name}");
            } catch (\Exception $e) {
                $this->error("Failed to process {$scheduled->name}: {$e->getMessage()}");
            }
        }
    }
    
    protected function generateExport($scheduled)
    {
        switch ($scheduled->type) {
            case 'daily_sales':
                return $this->exportService->exportRevenueSummary([
                    'date_from' => now()->subDay()->format('Y-m-d'),
                    'date_to' => now()->format('Y-m-d')
                ], $scheduled->format);
            case 'weekly_summary':
                return $this->exportService->exportRevenueSummary([
                    'date_from' => now()->subWeek()->format('Y-m-d'),
                    'date_to' => now()->format('Y-m-d')
                ], $scheduled->format);
            case 'monthly_report':
                return $this->exportService->exportRevenueSummary([
                    'date_from' => now()->subMonth()->format('Y-m-d'),
                    'date_to' => now()->format('Y-m-d')
                ], $scheduled->format);
        }
    }
    
    protected function calculateNextRun($schedule)
    {
        switch ($schedule) {
            case 'daily':
                return now()->addDay()->startOfDay();
            case 'weekly':
                return now()->addWeek()->startOfDay();
            case 'monthly':
                return now()->addMonth()->startOfDay();
            default:
                return now()->addDay();
        }
    }
}