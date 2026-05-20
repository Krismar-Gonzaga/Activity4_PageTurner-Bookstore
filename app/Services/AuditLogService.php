<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class AuditLogService
{
    public static function log($event, $auditableType = null, $auditableId = null, $oldValues = null, $newValues = null, $metadata = [])
    {
        $user = Auth::user();

        $data = [
            'id' => (string) Str::uuid(),
            'user_id' => $user ? $user->id : null,
            'event' => $event,
            'auditable_type' => $auditableType,
            'auditable_id' => $auditableId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'metadata' => array_merge($metadata, [
                'ip_address' => Request::ip(),
                'user_agent' => Request::header('User-Agent'),
                'url' => Request::fullUrl(),
                'method' => Request::method(),
            ]),
            'created_at' => now(),
        ];

        $data['checksum'] = hash('sha256', json_encode([
            'id' => $data['id'],
            'user_id' => $data['user_id'],
            'event' => $data['event'],
            'auditable_type' => $data['auditable_type'],
            'auditable_id' => $data['auditable_id'],
            'old_values' => $data['old_values'],
            'new_values' => $data['new_values'],
            'created_at' => now()->toIso8601String(),
        ]));

        return AuditLog::create($data);
    }

    public static function logLogin($user, $success = true)
    {
        self::log(
            $success ? 'login' : 'login_failed',
            $user ? get_class($user) : null,
            $user ? $user->id : null,
            null,
            $user ? ['email' => $user->email] : []
        );
    }

    public static function logLogout($user)
    {
        self::log('logout', $user ? get_class($user) : null, $user ? $user->id : null);
    }

    public static function logPasswordChange($user)
    {
        self::log('password_change', $user ? get_class($user) : null, $user ? $user->id : null);
    }

    public static function logTwoFactorEnable($user, $method = 'app')
    {
        self::log('2fa_enabled', $user ? get_class($user) : null, $user ? $user->id : null, null, ['method' => $method]);
    }

    public static function logTwoFactorDisable($user)
    {
        self::log('2fa_disabled', $user ? get_class($user) : null, $user ? $user->id : null);
    }

    public static function logPermissionChange($user, $permission, $action)
    {
        self::log('permission_change', $user ? get_class($user) : null, $user ? $user->id : null, null, [
            'permission' => $permission,
            'action' => $action
        ]);
    }

    public static function logRoleAssignment($user, $role)
    {
        self::log('role_assigned', $user ? get_class($user) : null, $user ? $user->id : null, null, ['role' => $role]);
    }

    public static function logBackup($status, $details = [])
    {
        self::log('backup_' . $status, null, null, null, $details);
    }

    public static function logImport($import, $details = [])
    {
        self::log('import', get_class($import), $import->id, null, $details);
    }

    public static function logExport($export, $details = [])
    {
        self::log('export', get_class($export), $export->id, null, $details);
    }

    public static function logSettingsChange($key, $oldValue, $newValue)
    {
        self::log('settings_change', null, null, ['key' => $key, 'old_value' => $oldValue], ['key' => $key, 'new_value' => $newValue]);
    }
}