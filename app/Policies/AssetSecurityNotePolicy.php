<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\AssetSecurityNote;
use App\Models\User;

class AssetSecurityNotePolicy
{
    public function update(User $user, AssetSecurityNote $securityNote): bool
    {
        /** @var UserRole $userRole */
        $userRole = $user->role;

        return $user->id === $securityNote->user_id || $userRole === UserRole::Admin;
    }

    public function delete(User $user, AssetSecurityNote $securityNote): bool
    {
        /** @var UserRole $userRole */
        $userRole = $user->role;

        return $user->id === $securityNote->user_id || $userRole === UserRole::Admin;
    }
}
