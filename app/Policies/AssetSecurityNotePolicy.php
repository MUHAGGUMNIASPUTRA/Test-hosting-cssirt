<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\AssetSecurityNote;
use App\Models\User;

class AssetSecurityNotePolicy
{
    public function update(User $user, AssetSecurityNote $securityNote): bool
    {
        return $user->id === $securityNote->user_id || $user->role === UserRole::Admin;
    }

    public function delete(User $user, AssetSecurityNote $securityNote): bool
    {
        return $user->id === $securityNote->user_id || $user->role === UserRole::Admin;
    }
}
