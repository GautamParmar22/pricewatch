<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;

class WorkspacePolicy
{
    /**
     * Determine whether the user can view the workspace.
     */
    public function view(User $user, Workspace $workspace): bool
    {
        // GP - 19-08-2026 code comment - Check if user is member of workspace
        return $workspace->users()->where('users.id', $user->id)->exists();
    }

    /**
     * Determine whether the user can update the workspace.
     */
    public function update(User $user, Workspace $workspace): bool
    {
        // GP - 19-08-2026 code comment - Only owners and admins can update workspace details
        $membership = $workspace->users()->where('users.id', $user->id)->first();
        return $membership && in_array($membership->pivot->role, ['owner', 'admin']);
    }

    /**
     * Determine whether the user can delete the workspace.
     */
    public function delete(User $user, Workspace $workspace): bool
    {
        // GP - 19-08-2026 code comment - Only workspace owner can delete it
        return $workspace->owner_id === $user->id;
    }

    /**
     * Determine whether the user can manage billing for the workspace.
     */
    public function manageBilling(User $user, Workspace $workspace): bool
    {
        // GP - 19-08-2026 code comment - Only owners and admins can manage billing
        $membership = $workspace->users()->where('users.id', $user->id)->first();
        return $membership && in_array($membership->pivot->role, ['owner', 'admin']);
    }
}
