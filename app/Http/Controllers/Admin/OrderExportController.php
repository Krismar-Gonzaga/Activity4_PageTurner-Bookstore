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

class OrderExportController extends Controller
{
    protected $exportService;
    
    public function __construct(OrderExportService $exportService)
    {
        $this->exportService = $exportService;
    }
    
    /**
     * Admin Order Export
     */
    public function exportOrders(Request $request)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        
        $request->validate([
            'format' => 'required|in:csv',
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
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'export_id' => $exportLog->id,
                'message' => 'Export started. You will be notified when ready.'
            ]);
        }

        return back()->with('success', 'Order export started. Check Recent Export Jobs for status.');
    }
    
    /**
     * Customer Order Export (Personal Orders)
     */
    public function exportMyOrders(Request $request)
    {
        $request->validate([
            'format' => 'required|in:pdf'
        ]);
        
        try {
            // Personal customer order-history export in PDF invoice format only.
            $filePath = $this->exportService->exportCustomerOrders(auth()->id(), 'pdf');
            
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
        abort_unless(auth()->user()?->isAdmin(), 403);
        
        $request->validate([
            'format' => 'required|in:csv',
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
        abort_unless(auth()->user()?->isAdmin(), 403);
        
        $request->validate([
            'format' => 'required|in:csv',
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
        abort_unless(auth()->user()?->isAdmin(), 403);
        
        $scheduledExports = ScheduledExport::latest()->get();
        return view('admin.exports.scheduled', compact('scheduledExports'));
    }

    /**
     * Export management dashboard UI.
     */
    public function index()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        $customers = User::where('role', 'customer')->orderBy('name')->get(['id', 'name', 'email']);
        $recentExports = ExportLog::with('user')->latest()->limit(10)->get();
        $scheduledExports = ScheduledExport::latest()->limit(5)->get();

        return view('admin.exports.index', compact('statuses', 'customers', 'recentExports', 'scheduledExports'));
    }
    
    public function createScheduledExport(Request $request)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:daily_sales',
            'format' => 'required|in:csv',
            'schedule' => 'required|in:daily,weekly,monthly',
            'recipients' => 'required|string'
        ]);

        $recipients = collect(explode(',', $request->recipients))
            ->map(fn ($email) => trim($email))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($recipients)) {
            return back()->withErrors(['recipients' => 'Please provide at least one recipient email.']);
        }
        
        $scheduledExport = ScheduledExport::create([
            'name' => $request->name,
            'type' => $request->type,
            'format' => $request->format,
            'filters' => $request->filters ?? [],
            'schedule' => $request->schedule,
            'recipients' => $recipients,
            'next_run_at' => $this->calculateNextRun($request->schedule)
        ]);
        
        return back()->with('success', "Scheduled export '{$scheduledExport->name}' has been created.");
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