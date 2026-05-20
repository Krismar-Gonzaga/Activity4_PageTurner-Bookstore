<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Middleware\TwoFactorPartial;
use App\Http\Middleware\AdminMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('exports:process-scheduled')->dailyAt('01:00');
        
        // Backup: Daily at 02:00 AM
        $schedule->command('tasks:run backupDatabase')
            ->dailyAt('02:00')
            ->withoutOverlapping()
            ->onSuccess(function () {
                AuditLogService::log('backup_scheduled_success', null, null, null, ['task' => 'backupDatabase']);
            })
            ->onFailure(function () {
                AuditLogService::log('backup_scheduled_failure', null, null, null, ['task' => 'backupDatabase']);
            });
        
        // Backup: Weekly full backup - Sunday at 02:30 AM
        $schedule->command('tasks:run performWeeklyBackup')
            ->sundays()
            ->at('02:30')
            ->withoutOverlapping();
        
        // Backup:clean Daily at 03:00
        $schedule->command('tasks:run cleanOldBackups')
            ->dailyAt('03:00')
            ->withoutOverlapping();
        
        // Backup: Health check - Hourly
        $schedule->call(function () {
            $backupService = new \App\Services\BackupService();
            $health = $backupService->getBackupHealth();
            
            if ($health['health_status'] !== 'healthy') {
                \App\Services\BackupService::notifyFailure('Backup health check: No backup in last 24 hours');
            }
        })->hourly()->name('backup-health-check');
        
        // Order cleanup - Hourly
        $schedule->command('tasks:run cleanupPendingOrders')
            ->hourly()
            ->withoutOverlapping();
        
        // Session cleanup - Daily
        $schedule->command('tasks:run cleanupExpiredSessions')
            ->daily()
            ->withoutOverlapping();
        
        // Log rotation - Weekly
        $schedule->command('tasks:run rotateLogs')
            ->weekly()
            ->withoutOverlapping();
        
        // Daily report - Daily at 06:00
        $schedule->command('tasks:run generateDailyReport')
            ->dailyAt('06:00')
            ->withoutOverlapping();
        
        // Notification prune - Weekly
        $schedule->command('tasks:run pruneOldNotifications')
            ->weekly()
            ->withoutOverlapping();
        
        // Audit archive - Monthly
        $schedule->command('tasks:run archiveAuditLogs')
            ->monthly()
            ->withoutOverlapping();
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'two-factor.partial' => TwoFactorPartial::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
