<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Mail\BackupStatusMail;
use PDO;

class BackupService
{
    protected $backupConfig;

    public function __construct()
    {
        $this->backupConfig = config('backup');
    }

    public function performBackup($includeFiles = true)
    {
        try {
            Log::info('Starting database backup');

            $fileName = now()->format('Ymd_His');
            $storageDir = storage_path('backups');
            $backupPath = $storageDir . '/' . $fileName;

            // Create with full permissions
            if (!File::exists($storageDir)) {
                File::makeDirectory($storageDir, 0777, true, true);
            }

            // Double-check it's writable
            if (!is_writable($storageDir)) {
                throw new \Exception("Backup directory is not writable: {$storageDir}");
            }

            File::ensureDirectoryExists($backupPath);
            
            // Create database dump
            $dbPath = $backupPath . '/database.sql';
            
            if (env('DB_CONNECTION') === 'sqlite') {
                // For SQLite, copy the file directly
                copy(database_path('database.sqlite'), $dbPath);
            } else {
                // PHP-based database dump (no external mysqldump binary required)
                $this->dumpMySQLDatabase($dbPath);
            }
            
            // Include files if requested
            if ($includeFiles) {
                $this->copyFiles($backupPath . '/files', storage_path('app/public'));
            }
            
            // Include config
            $this->copyFiles($backupPath . '/config', config_path());
            
            // Create zip archive
            $zipPath = $backupPath . '.zip';
            $this->createZip($backupPath, $zipPath);
            
            // Clean up temp directory
            File::deleteDirectory($backupPath);
            
            $size = filesize($zipPath);

            AuditLogService::logBackup('completed', [
                'filename' => $fileName . '.zip',
                'size' => $size,
            ]);

            return [
                'success' => true,
                'message' => 'Backup completed successfully',
                'filename' => $fileName . '.zip',
            ];
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error('Backup failed: ' . $errorMessage);
            
            // Clean up temp backup directory on failure
            try {
                if (isset($backupPath) && File::exists($backupPath)) {
                    File::deleteDirectory($backupPath);
                }
            } catch (\Exception $cleanupEx) {
                Log::warning('Failed to cleanup backup directory: ' . $cleanupEx->getMessage());
            }
            
            // Try to log to audit log, but don't let it crash the backup process
            try {
                AuditLogService::logBackup('failed', [
                    'error' => $errorMessage,
                ]);
            } catch (\Exception $auditEx) {
                Log::error('Failed to log backup audit entry: ' . $auditEx->getMessage());
            }

            try {
                $this->sendFailureNotification($e);
            } catch (\Exception $notifEx) {
                Log::error('Failed to send backup failure notification: ' . $notifEx->getMessage());
            }

            return [
                'success' => false,
                'message' => $errorMessage,
            ];
        }
    }

    /**
     * Dump MySQL database to SQL file using PHP PDO (no mysqldump binary needed)
     */
    protected function dumpMySQLDatabase($dbPath)
    {
        $dbHost = env('DB_HOST', '127.0.0.1');
        $dbPort = env('DB_PORT', '3306');
        $dbUser = env('DB_USERNAME', 'root');
        $dbPass = env('DB_PASSWORD', '');
        $dbName = env('DB_DATABASE', 'pageturner_bookstore');

        try {
            $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
            $pdo = new PDO($dsn, $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            $sql = "-- Database Backup: {$dbName}\n";
            $sql .= "-- Generated: " . now()->toDateTimeString() . "\n";
            $sql .= "-- Host: {$dbHost}:{$dbPort}\n\n";
            $sql .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
            $sql .= "SET AUTOCOMMIT = 0;\n";
            $sql .= "START TRANSACTION;\n";
            $sql .= "SET time_zone = '+00:00';\n\n";
            $sql .= "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n";
            $sql .= "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n";
            $sql .= "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n";
            $sql .= "/*!40101 SET NAMES utf8mb4 */;\n\n";

            // Get all tables
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

            foreach ($tables as $table) {
                Log::info("Dumping table: {$table}");

                // Drop table if exists
                $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";

                // Get CREATE TABLE statement
                $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch();
                $createSql = $createStmt['Create Table'] ?? '';
                $sql .= $createSql . ";\n\n";

                // Get table data
                $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll();

                if (count($rows) > 0) {
                    $columns = array_keys($rows[0]);
                    $columnList = '`' . implode('`, `', $columns) . '`';

                    $sql .= "LOCK TABLES `{$table}` WRITE;\n";
                    $sql .= "INSERT INTO `{$table}` ({$columnList}) VALUES\n";

                    $valueLines = [];
                    foreach ($rows as $row) {
                        $values = [];
                        foreach ($row as $value) {
                            if ($value === null) {
                                $values[] = 'NULL';
                            } elseif (is_numeric($value)) {
                                $values[] = $value;
                            } else {
                                $values[] = $pdo->quote($value);
                            }
                        }
                        $valueLines[] = '(' . implode(', ', $values) . ')';
                    }
                    $sql .= implode(",\n", $valueLines) . ";\n";
                    $sql .= "UNLOCK TABLES;\n\n";
                }
            }

            $sql .= "COMMIT;\n";
            $sql .= "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n";
            $sql .= "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n";
            $sql .= "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n";

            file_put_contents($dbPath, $sql);
            Log::info("Database dump completed: {$dbPath}");

        } catch (\Exception $e) {
            throw new \Exception("PHP database dump failed: " . $e->getMessage());
        }
    }

    protected function copyFiles($destination, $source)
    {
        File::ensureDirectoryExists($destination);
        if (!File::exists($source)) {
            Log::warning("Backup source directory does not exist: {$source}");
            return;
        }
        $files = File::allFiles($source);
        foreach ($files as $file) {
            $relativePath = $file->getRelativePathname();
            $destPath = $destination . '/' . $relativePath;
            File::ensureDirectoryExists(dirname($destPath));
            if (!copy($file->getRealPath(), $destPath)) {
                Log::warning("Failed to copy file: {$file->getRealPath()} to {$destPath}");
            }
        }
    }

    protected function createZip($source, $destination)
    {
        $parentDir = dirname($destination);
        if (!File::isDirectory($parentDir)) {
            File::makeDirectory($parentDir, 0755, true, true);
        }

        // Write directly to destination to avoid Windows rename issues
        $tempZipPath = $parentDir . DIRECTORY_SEPARATOR . uniqid('backup_tmp_', true) . '.zip';

        try {
            $zip = new \ZipArchive();
            $openResult = $zip->open($tempZipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

            if ($openResult !== true) {
                $errorMessages = [
                    \ZipArchive::ER_EXISTS => 'File already exists',
                    \ZipArchive::ER_INCONS => 'Zip archive inconsistent',
                    \ZipArchive::ER_MEMORY => 'Malloc failure',
                    \ZipArchive::ER_NOENT => 'No such file or directory',
                    \ZipArchive::ER_OPEN   => 'Cannot open file - check permissions',
                    \ZipArchive::ER_READ   => 'Read error',
                    \ZipArchive::ER_SEEK   => 'Seek error',
                ];
                $errorMsg = $errorMessages[$openResult] ?? "Unknown error (code: {$openResult})";
                throw new \Exception("Cannot create zip archive: {$errorMsg}");
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY  // Only files, not dirs
            );

            foreach ($iterator as $file) {
                if (!$file->isFile() || !$file->isReadable()) {
                    Log::warning("Skipping unreadable file: {$file->getRealPath()}");
                    continue;
                }

                // Read file contents into memory to avoid Windows file locking
                $fileContents = file_get_contents($file->getRealPath());
                if ($fileContents === false) {
                    Log::warning("Could not read file: {$file->getRealPath()}");
                    continue;
                }

                $localPath = str_replace('\\', '/', 
                    substr($file->getRealPath(), strlen(realpath($source)) + 1)
                );

                // addFromString avoids file handle locking on Windows
                $zip->addFromString($localPath, $fileContents);
            }

            // Close BEFORE moving
            $closeResult = $zip->close();
            if ($closeResult !== true) {
                throw new \Exception("Failed to finalize zip archive");
            }

            // Rename within same directory (avoids cross-drive issues)
            if (file_exists($destination)) {
                unlink($destination);
            }

            if (!rename($tempZipPath, $destination)) {
                // Fallback: copy then delete
                if (!copy($tempZipPath, $destination)) {
                    throw new \Exception("Failed to move zip to final destination: {$destination}");
                }
                unlink($tempZipPath);
            }

        } catch (\Exception $e) {
            if (file_exists($tempZipPath)) {
                @unlink($tempZipPath);
            }
            throw $e;
        }
    }

    public function performWeeklyBackup()
    {
        return $this->performBackup(true);
    }

    public function cleanOldBackups()
    {
        try {
            Log::info('Cleaning old backups');
            
            $backupDir = storage_path('backups');
            $threshold = now()->subDays(30)->timestamp;
            
            $cleaned = 0;
            if (File::exists($backupDir)) {
                $files = File::files($backupDir);
                foreach ($files as $file) {
                    if ($file->getMTime() < $threshold) {
                        File::delete($file->getRealPath());
                        $cleaned++;
                    }
                }
            }

            Log::info("Cleaned {$cleaned} old backups");

            AuditLogService::logBackup('cleaned', [
                'count' => $cleaned,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Backup cleanup failed: ' . $e->getMessage());
            return false;
        }
    }

    public static function notifyFailure($message)
    {
        try {
            $admins = \App\Models\User::where('role', 'admin')->get();
            
            foreach ($admins as $admin) {
                try {
                    Mail::to($admin->email)->send(new BackupStatusMail([
                        'status' => 'failed',
                        'error' => $message,
                        'timestamp' => now()->toDateTimeString(),
                    ]));
                } catch (\Exception $e) {
                    Log::error('Failed to send backup failure notification: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error('Backup notification error: ' . $e->getMessage());
        }
    }

    public function getBackupList()
    {
        $backups = [];
        $backupDir = storage_path('backups');
        
        try {
            if (File::exists($backupDir)) {
                $files = File::files($backupDir);
                foreach ($files as $file) {
                    if ($file->getExtension() === 'zip') {
                        $backups[] = [
                            'filename' => $file->getFilename(),
                            'path' => $file->getRealPath(),
                            'size' => $file->getSize(),
                            'last_modified' => $file->getMTime(),
                            'disk' => 'local',
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning("Could not access backup directory: " . $e->getMessage());
        }
        
        return collect($backups)->sortByDesc('last_modified')->values();
    }

    public function deleteBackup($filename, $disk = 'local')
    {
        try {
            $backupPath = storage_path('backups/' . $filename);
            if (File::exists($backupPath)) {
                File::delete($backupPath);
                
                AuditLogService::logBackup('deleted', [
                    'filename' => $filename,
                    'disk' => $disk,
                ]);
                
                return true;
            }
        } catch (\Exception $e) {
            Log::error("Failed to delete backup {$filename}: " . $e->getMessage());
        }
        
        return false;
    }

    public function getBackupHealth()
    {
        $backups = $this->getBackupList();
        $lastBackup = $backups->first();
        
        $lastModified = $lastBackup ? ($lastBackup['last_modified'] ?? 0) : 0;
        
        return [
            'total_backups' => $backups->count(),
            'last_backup' => $lastBackup,
            'health_status' => $lastBackup && $lastModified > now()->subDay()->timestamp 
                ? 'healthy' : 'warning',
            'next_scheduled' => '02:00 AM daily',
        ];
    }

    protected function sendFailureNotification($exception)
    {
        $admins = \App\Models\User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new BackupStatusMail([
                'status' => 'failed',
                'error' => $exception->getMessage(),
                'timestamp' => now()->toDateTimeString(),
            ]));
        }
    }

    protected function sendWeeklySummary()
    {
        $admins = \App\Models\User::where('role', 'admin')->get();
        $backups = $this->getBackupList();
        
        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new BackupStatusMail([
                'status' => 'completed',
                'type' => 'weekly',
                'timestamp' => now()->toDateTimeString(),
                'total_backups' => $backups->count(),
            ]));
        }
    }
}