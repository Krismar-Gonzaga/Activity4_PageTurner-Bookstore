<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogObserver
{
    protected $auditableEvents = ['created', 'updated', 'deleted'];
    protected $sensitiveFields = ['password', 'password_confirmation', 'token', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes', 'payment_info', 'card_number'];

    public function saved($model)
    {
        if (!in_array('updated', $this->auditableEvents)) {
            return;
        }

        $this->logChange($model, 'updated');
    }

    public function created($model)
    {
        if (!in_array('created', $this->auditableEvents)) {
            return;
        }

        $this->logChange($model, 'created');
    }

    public function deleted($model)
    {
        if (!in_array('deleted', $this->auditableEvents)) {
            return;
        }

        $this->logChange($model, 'deleted');
    }

    protected function logChange($model, $event)
    {
        $userId = Auth::id();
        if (!$userId) {
            return;
        }

        $oldValues = $event === 'created' ? null : $model->getOriginal();
        $newValues = $event === 'deleted' ? null : $model->getAttributes();

        $oldValues = $this->removeSensitiveFields($oldValues);
        $newValues = $this->removeSensitiveFields($newValues);

        AuditLog::create([
            'id' => (string) \Str::uuid(),
            'user_id' => $userId,
            'event' => $event,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'metadata' => [
                'ip_address' => Request::ip(),
                'user_agent' => Request::header('User-Agent'),
                'url' => Request::fullUrl(),
                'method' => Request::method(),
            ],
        ]);
    }

    protected function removeSensitiveFields($values)
    {
        if (!$values) {
            return null;
        }

        return array_diff_key($values, array_flip($this->sensitiveFields));
    }
}