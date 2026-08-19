<?php

namespace App\Policies;

use App\Models\Competitor;
use App\Models\User;
use App\Models\Workspace;

class CompetitorPolicy
{
    /**
     * Determine whether the user can view the competitor.
     */
    public function view(User $user, Competitor $competitor): bool
    {
        // GP - 19-08-2026 code comment - Verify user has access to competitor's workspace
        return $user->workspaces()->where('workspaces.id', $competitor->workspace_id)->exists();
    }

    /**
     * Determine whether the user can create competitors in the workspace.
     */
    public function create(User $user, Workspace $workspace): bool
    {
        // GP - 19-08-2026 code comment - Verify user is member and not viewer
        $membership = $workspace->users()->where('users.id', $user->id)->first();
        return $membership && in_array($membership->pivot->role, ['owner', 'admin', 'member']);
    }

    /**
     * Determine whether the user can update the competitor.
     */
    public function update(User $user, Competitor $competitor): bool
    {
        // GP - 19-08-2026 code comment - Verify user is member and not viewer
        $membership = $competitor->workspace->users()->where('users.id', $user->id)->first();
        return $membership && in_array($membership->pivot->role, ['owner', 'admin', 'member']);
    }

    /**
     * Determine whether the user can delete the competitor.
     */
    public function delete(User $user, Competitor $competitor): bool
    {
        // GP - 19-08-2026 code comment - Only owners and admins can delete competitor trackers
        $membership = $competitor->workspace->users()->where('users.id', $user->id)->first();
        return $membership && in_array($membership->pivot->role, ['owner', 'admin']);
    }
}
