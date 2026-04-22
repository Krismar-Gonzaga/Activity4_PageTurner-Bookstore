<?php
// app/Http/Controllers/Admin/OrderExportController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ExportLog;
use App\Models\ScheduledExport;
use App\Models\User;
use App\Services\OrderExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExportReadyMail;

class OrderExportController extends Controller
{
    protected $exportService;
    
    public function __construct(OrderExportService $exportService)
    {
        $this->exportService = $exportService;
        $this->middleware('auth');
    }
    
    /**
     * Admin Order Export
     */
    public function exportOrders(Request $request)
    {
        $this->authorize('admin-access');
        
        $request->validate([
            'format' => 'required|in:csv,xlsx,pdf',
            'status' => 'nullable|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'customer_id' => 'nullable|exists:users,id',
            'payment_status' => 'nullable|string',
            'amount_min' => 'nullable|numeric',
            'amount_max' => 'nullable|numeric'
        ]);
        
        $exportLog = ExportLog::create([
            'user_id' => auth()->id(),
            'export_type' => 'orders',
            'format' => $request->format,
            'filters' => $request->all(),
            'status' => 'pending'
        ]);
        
        // Process export in background
        dispatch(new \App\Jobs\ProcessOrderExport($exportLog->id, $request->all(), $request->format));
        
        return response()->json([
            'success' => true,
            'export_id' => $exportLog->id,
            'message' => 'Export started. You will be notified when ready.'
        ]);
    }
    
    /**
     * Customer Order Export (Personal Orders)
     */
    public function exportMyOrders(Request $request)
    {
        $request->validate([
            'format' => 'required|in:csv,pdf'
        ]);
        
        try {
            $filePath = $this->exportService->exportCustomerOrders(auth()->id(), $request->format);
            
            return response()->download($filePath);
        } catch (\Exception $e) {
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Financial Report - Revenue Summary
     */
    public function exportRevenueSummary(Request $request)
    {
        $this->authorize('admin-access');
        
        $request->validate([
            'format' => 'required|in:csv,xlsx',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date'
        ]);
        
        try {
            $filePath = $this->exportService->exportRevenueSummary($request->all(), $request->format);
            
            return response()->download($filePath);
        } catch (\Exception $e) {
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Tax Report Export
     */
    public function exportTaxReport(Request $request)
    {
        $this->authorize('admin-access');
        
        $request->validate([
            'format' => 'required|in:csv,xlsx',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date'
        ]);
        
        try {
            $filePath = $this->exportService->exportTaxReport($request->all(), $request->format);
            
            return response()->download($filePath);
        } catch (\Exception $e) {
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Get Export Status
     */
    public function getExportStatus($id)
    {
        $export = ExportLog::where('user_id', auth()->id())->findOrFail($id);
        
        return response()->json([
            'status' => $export->status,
            'total_records' => $export->total_records,
            'file_path' => $export->status === 'completed' ? $export->file_path : null,
            'error_message' => $export->error_message
        ]);
    }
    
    /**
     * Download Export File
     */
    public function downloadExport($id)
    {
        $export = ExportLog::where('user_id', auth()->id())
            ->where('status', 'completed')
            ->findOrFail($id);
            
        if (!file_exists($export->file_path)) {
            abort(404, 'File not found');
        }
        
        return response()->download($export->file_path);
    }
    
    /**
     * Scheduled Exports Management
     */
    public function listScheduledExports()
    {
        $this->authorize('admin-access');
        
        $scheduledExports = ScheduledExport::all();
        return view('admin.exports.scheduled', compact('scheduledExports'));
    }
    
    public function createScheduledExport(Request $request)
    {
        $this->authorize('admin-access');
        
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:daily_sales,weekly_summary,monthly_report',
            'format' => 'required|in:csv,xlsx',
            'schedule' => 'required|in:daily,weekly,monthly',
            'recipients' => 'required|array',
            'recipients.*' => 'email'
        ]);
        
        $scheduledExport = ScheduledExport::create([
            'name' => $request->name,
            'type' => $request->type,
            'format' => $request->format,
            'filters' => $request->filters ?? [],
            'schedule' => $request->schedule,
            'recipients' => $request->recipients,
            'next_run_at' => $this->calculateNextRun($request->schedule)
        ]);
        
        return response()->json([
            'success' => true,
            'scheduled_export' => $scheduledExport
        ]);
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