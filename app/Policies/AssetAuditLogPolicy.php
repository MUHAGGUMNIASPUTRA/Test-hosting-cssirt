<?php

namespace App\Policies;

use App\Models\AssetAuditLog;
use App\Models\User;

class AssetAuditLogPolicy
{
    public function update(User $user, AssetAuditLog $auditLog): bool
    {
        return $user->id === $auditLog->user_id || $user->role->value === 'admin';
    }

    public function delete(User $user, AssetAuditLog $auditLog): bool
    {
        return $user->id === $auditLog->user_id || $user->role->value === 'admin';
    }
}
