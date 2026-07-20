<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\AssetAuditLog;
use App\Models\User;

class AssetAuditLogPolicy
{
    public function update(User $user, AssetAuditLog $auditLog): bool
    {
        /** @phpstan-ignore-next-line */
        return $user->id === $auditLog->user_id || $user->role === UserRole::Admin;
    }

    public function delete(User $user, AssetAuditLog $auditLog): bool
    {
        /** @phpstan-ignore-next-line */
        return $user->id === $auditLog->user_id || $user->role === UserRole::Admin;
    }
}
