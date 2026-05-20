<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class BackupController extends Controller
{
    protected $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    public function index()
    {
        $backups = $this->backupService->getBackupList();
        $health = $this->backupService->getBackupHealth();

        if (request()->expectsJson()) {
            return response()->json(compact('backups', 'health'));
        }

        return view('admin.backups.index', compact('backups', 'health'));
    }

    public function store(Request $request)
    {
        $result = $this->backupService->performBackup();

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'filename' => $result['filename'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ], 500);
    }

    public function destroy($filename)
    {
        $result = $this->backupService->deleteBackup($filename);

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Backup deleted successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Backup not found',
        ], 404);
    }

    public function clean()
    {
        $result = $this->backupService->cleanOldBackups();

        return response()->json([
            'success' => $result,
            'message' => $result ? 'Old backups cleaned successfully' : 'Cleanup failed',
        ]);
    }

    public function health()
    {
        return response()->json($this->backupService->getBackupHealth());
    }
}