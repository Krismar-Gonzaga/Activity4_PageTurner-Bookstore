<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use App\Services\AuditLogService;
use App\Models\Order;
use App\Models\Session;
use App\Models\Notification;
use App\Models\Review;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ScheduledTasks extends Command
{
    protected $signature = 'tasks:run {task? : Specific task to run}';
    protected $description = 'Run scheduled maintenance tasks';

    protected $backupService;

    public function __construct(BackupService $backupService)
    {
        parent::__construct();
        $this->backupService = $backupService;
    }

    public function handle()
    {
        $task = $this->argument('task');

        if ($task) {
            $this->runSpecificTask($task);
            return;
        }

        $this->runAllTasks();
    }

    protected function runAllTasks()
    {
        $this->info('Running all scheduled tasks...');

        $this->backupDatabase();
        $this->cleanOldBackups();
        $this->cleanupPendingOrders();
        $this->cleanupExpiredSessions();
        $this->rotateLogs();
        $this->generateDailyReport();
        $this->pruneOldNotifications();
        $this->archiveAuditLogs();

        $this->info('All scheduled tasks completed.');
    }

    protected function runSpecificTask($task)
    {
        $method = 'task' . ucfirst($task);
        
        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            $this->error("Task {$task} not found.");
        }
    }

    public function backupDatabase()
    {
        $this->info('Running database backup at ' . now());
        
        try {
            $result = $this->backupService->performBackup(false);
            
            if ($result['success']) {
                $this->info('Database backup completed successfully.');
            } else {
                $this->error('Database backup failed: ' . $result['message']);
            }
        } catch (\Exception $e) {
            $this->error('Backup error: ' . $e->getMessage());
        }
    }

    public function cleanOldBackups()
    {
        $this->info('Running backup:clean at ' . now());
        
        try {
            $result = $this->backupService->cleanOldBackups();
            
            if ($result) {
                $this->info('Old backups cleaned successfully.');
            }
        } catch (\Exception $e) {
            $this->error('Cleanup error: ' . $e->getMessage());
        }
    }

    public function cleanupPendingOrders()
    {
        $this->info('Running order:cleanup-pending at ' . now());
        
        try {
            $cutoffTime = now()->subHours(24);
            
            $cancelled = Order::where('status', 'pending')
                ->where('created_at', '<', $cutoffTime)
                ->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                    'cancellation_reason' => 'Auto-cancelled after 24 hours'
                ]);

            AuditLogService::log('pending_orders_cancelled', null, null, null, [
                'count' => $cancelled,
                'cutoff_time' => $cutoffTime->toDateTimeString(),
            ]);

            $this->info("Cancelled {$cancelled} pending orders.");
        } catch (\Exception $e) {
            $this->error('Order cleanup error: ' . $e->getMessage());
        }
    }

    public function cleanupExpiredSessions()
    {
        $this->info('Running session:cleanup at ' . now());
        
        try {
            $expired = DB::table('sessions')
                ->where('last_activity', '<', now()->subDays(7)->timestamp)
                ->delete();

            AuditLogService::log('sessions_cleaned', null, null, null, [
                'count' => $expired,
            ]);

            $this->info("Deleted {$expired} expired sessions.");
        } catch (\Exception $e) {
            $this->error('Session cleanup error: ' . $e->getMessage());
        }
    }

    public function rotateLogs()
    {
        $this->info('Running log:rotate at ' . now());
        
        try {
            $logPath = storage_path('logs');
            $files = glob($logPath . '/*.log');
            
            foreach ($files as $file) {
                if (filemtime($file) < now()->subWeek()->timestamp) {
                    $newName = $file . '.' . now()->format('Y_m_d') . '.gz';
                    $this->compressFile($file, $newName);
                }
            }

            $this->info('Log rotation completed.');
        } catch (\Exception $e) {
            $this->error('Log rotation error: ' . $e->getMessage());
        }
    }

    protected function compressFile($source, $dest)
    {
        $mode = 'wb' . "\x1f\x8b\x08\x00\x00\x00\x00\x00";
        $fp = gzopen($dest, $mode);
        if ($fp) {
            $fd = fopen($source, 'rb');
            while (!feof($fd)) {
                $buffer = fread($fd, 65536);
                gzwrite($fp, $buffer);
            }
            fclose($fd);
            gzclose($fp);
            unlink($source);
        }
    }

    public function generateDailyReport()
    {
        $this->info('Running report:generate-daily at ' . now());
        
        try {
            $yesterday = now()->subDay();
            
            $report = [
                'date' => $yesterday->toDateString(),
                'total_orders' => Order::whereDate('created_at', $yesterday)->count(),
                'total_revenue' => Order::whereDate('created_at', $yesterday)
                    ->where('status', 'delivered')
                    ->sum('total_amount'),
                'new_customers' => \App\Models\User::whereDate('created_at', $yesterday)
                    ->where('role', 'customer')
                    ->count(),
                'books_sold' => \App\Models\OrderItem::whereHas('order', function ($q) use ($yesterday) {
                    $q->whereDate('created_at', $yesterday)->where('status', 'delivered');
                })->sum('quantity'),
            ];

            AuditLogService::log('daily_report_generated', null, null, null, $report);

            $this->info('Daily report generated: ' . json_encode($report));
        } catch (\Exception $e) {
            $this->error('Report generation error: ' . $e->getMessage());
        }
    }

    public function pruneOldNotifications()
    {
        $this->info('Running notification:prune at ' . now());
        
        try {
            $cutoffTime = now()->subDays(90);
            
            $deleted = Notification::where('created_at', '<', $cutoffTime)->delete();
            
            AuditLogService::log('notifications_pruned', null, null, null, [
                'count' => $deleted,
                'cutoff_days' => 90,
            ]);

            $this->info("Deleted {$deleted} old notifications.");
        } catch (\Exception $e) {
            $this->error('Notification prune error: ' . $e->getMessage());
        }
    }

    public function archiveAuditLogs()
    {
        $this->info('Running audit:archive at ' . now());
        
        try {
            $cutoffTime = now()->subYear();
            
            $archived = DB::table('audit_logs')
                ->where('created_at', '<', $cutoffTime)
                ->update(['archived' => true]);

            AuditLogService::log('audit_logs_archived', null, null, null, [
                'count' => $archived,
                'cutoff_time' => $cutoffTime->toDateTimeString(),
            ]);

            $this->info("Archived {$archived} audit logs older than 1 year.");
        } catch (\Exception $e) {
            $this->error('Audit archive error: ' . $e->getMessage());
        }
    }

    public function performWeeklyBackup()
    {
        $this->info('Running weekly backup at ' . now());
        
        try {
            $result = $this->backupService->performWeeklyBackup();
            
            if ($result['success']) {
                $this->info('Weekly backup completed successfully.');
            } else {
                $this->error('Weekly backup failed: ' . $result['message']);
            }
        } catch (\Exception $e) {
            $this->error('Weekly backup error: ' . $e->getMessage());
        }
    }
}