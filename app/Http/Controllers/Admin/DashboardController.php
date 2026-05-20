<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Book;
use App\Models\Order;
use App\Models\User;
use App\Models\Category;
use App\Models\Review;
use App\Services\BackupService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(BackupService $backupService)
    {
        $stats = [
            'total_books' => Book::count(),
            'total_orders' => Order::count(),
            'total_users' => User::count(),
            'total_categories' => Category::count(),
            'recent_orders' => Order::with('user')
                                ->latest()
                                ->take(5)
                                ->get(),
            'recent_reviews' => Review::with('user', 'book')
                                ->latest()
                                ->take(5)
                                ->get(),
            'recent_users' => User::latest()
                                ->take(5)
                                ->get(),
            'recent_audit_logs' => AuditLog::with('user')
                                ->latest()
                                ->take(5)
                                ->get(),
            'backup_health' => $backupService->getBackupHealth(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}