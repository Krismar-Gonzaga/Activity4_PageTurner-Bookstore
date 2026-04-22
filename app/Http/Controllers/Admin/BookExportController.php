<?php
// app/Http/Controllers/Admin/BookExportController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExportJob;
use App\Services\BookExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ProcessBookExport;

class BookExportController extends Controller
{
    protected $exportService;

    public function __construct(BookExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:xlsx,csv,pdf',
            'fields' => 'required|array|min:1',
            'search' => 'nullable|string',
            'category' => 'nullable|exists:categories,id',
            'price_range' => 'nullable|string',
            'in_stock' => 'nullable|boolean',
            'low_stock' => 'nullable|boolean',
            'out_of_stock' => 'nullable|boolean',
            'min_rating' => 'nullable|integer',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'stock_min' => 'nullable|integer',
            'stock_max' => 'nullable|integer',
            'price_min' => 'nullable|numeric',
            'price_max' => 'nullable|numeric',
            'sort' => 'nullable|string',
            'direction' => 'nullable|in:asc,desc'
        ]);

        // Collect filters
        $filters = $request->only([
            'search', 'category', 'price_range', 'in_stock', 'low_stock', 
            'out_of_stock', 'min_rating', 'date_from', 'date_to', 
            'stock_min', 'stock_max', 'price_min', 'price_max', 'sort', 'direction'
        ]);

        // Create export job
        $exportJob = ExportJob::create([
            'user_id' => auth()->id(),
            'format' => $request->format,
            'filters' => $filters,
            'selected_fields' => $request->fields,
            'status' => 'pending'
        ]);

        // Dispatch job for async processing
        ProcessBookExport::dispatch($exportJob->id);

        return response()->json([
            'success' => true,
            'export_id' => $exportJob->id,
            'message' => 'Export started. You will be notified when it\'s ready.'
        ]);
    }

    public function getStatus($id)
    {
        $export = ExportJob::where('user_id', auth()->id())
            ->findOrFail($id);

        return response()->json([
            'id' => $export->id,
            'status' => $export->status,
            'progress' => $export->progress,
            'total_records' => $export->total_records,
            'processed_records' => $export->processed_records,
            'file_path' => $export->status === 'completed' ? $export->file_path : null,
            'error_message' => $export->error_message,
            'completed_at' => $export->completed_at
        ]);
    }

    public function download($id)
    {
        $export = ExportJob::where('user_id', auth()->id())
            ->where('status', 'completed')
            ->findOrFail($id);

        if (!file_exists($export->file_path)) {
            abort(404, 'Export file not found');
        }

        return response()->download($export->file_path, $export->filename ?? 'export.' . $export->format);
    }

    public function getExports()
    {
        $exports = ExportJob::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($exports);
    }

    public function deleteExport($id)
    {
        $export = ExportJob::where('user_id', auth()->id())->findOrFail($id);
        
        if ($export->file_path && file_exists($export->file_path)) {
            unlink($export->file_path);
        }
        
        $export->delete();
        
        return response()->json(['success' => true]);
    }
}