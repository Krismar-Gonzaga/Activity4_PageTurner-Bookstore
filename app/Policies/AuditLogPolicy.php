<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AuditLog;

class AuditLogPolicy
{
    public function viewAny(User $user)
    {
        return $user->role === 'admin';
    }

    public function view(User $user, AuditLog $log)
    {
        return $user->role === 'admin';
    }

    public function export(User $user)
    {
        return $user->role === 'admin';
    }

    public function backup(User $user)
    {
        return $user->role === 'admin';
    }
}