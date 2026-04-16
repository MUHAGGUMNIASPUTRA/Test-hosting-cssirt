<?php

namespace App\Policies;

use App\Models\AssetSecurityNote;
use App\Models\User;

class AssetSecurityNotePolicy
{
    public function update(User $user, AssetSecurityNote $securityNote): bool
    {
        return $user->id === $securityNote->user_id || $user->role->value === 'admin';
    }

    public function delete(User $user, AssetSecurityNote $securityNote): bool
    {
        return $user->id === $securityNote->user_id || $user->role->value === 'admin';
    }
}
