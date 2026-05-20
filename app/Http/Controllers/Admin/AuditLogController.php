<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AuditLogController extends Controller
{
    protected $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('auditable_type')) {
            $query->where('auditable_type', $request->auditable_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('event', 'like', "%{$request->search}%")
                  ->orWhere('auditable_type', 'like', "%{$request->search}%");
            });
        }

        $logs = $query->paginate(50);

        $events = AuditLog::distinct('event')->pluck('event');

        return view('admin.audit-logs.index', compact('logs', 'events'));
    }

    public function show($id)
    {
        $log = AuditLog::with('user')->findOrFail($id);

        return view('admin.audit-logs.show', compact('log'));
    }

    public function export(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->get();

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            
            fputcsv($file, ['ID', 'User', 'Event', 'Auditable Type', 'Auditable ID', 'Old Values', 'New Values', 'Created At']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user ? $log->user->name : 'System',
                    $log->event,
                    class_basename($log->auditable_type),
                    $log->auditable_id,
                    json_encode($log->old_values),
                    json_encode($log->new_values),
                    $log->created_at->toDateTimeString(),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="audit_logs_' . date('Ymd') . '.csv"',
        ]);
    }

    public function backup()
    {
        try {
            $result = $this->backupService->performBackup();

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function verifyIntegrity()
    {
        $logs = AuditLog::all();
        $invalidCount = $logs->filter(function ($log) {
            return !$log->verifyIntegrity();
        })->count();

        return response()->json([
            'invalid_count' => $invalidCount,
            'status' => $invalidCount === 0 ? 'valid' : 'invalid',
        ]);
    }
}