<?php

namespace App\Support;

use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;

class TenantContext
{
    protected static ?Workspace $workspace = null;

    /**
     * Set the current active workspace.
     */
    public static function setWorkspace(Workspace $workspace): void
    {
        // GP - 19-08-2026 code comment - Set workspace context
        self::$workspace = $workspace;
    }

    /**
     * Get the current active workspace.
     * Falls back to session lookup if the static property is not yet set
     * (e.g. when called from a Livewire AJAX request before middleware fires).
     */
    public static function getWorkspace(): ?Workspace
    {
        // GP - 19-08-2026 code comment - Get workspace context with session fallback
        if (self::$workspace) {
            return self::$workspace;
        }

        $id = session('current_workspace_id');
        if ($id && Auth::check()) {
            $workspace = Auth::user()->workspaces()->where('workspaces.id', $id)->first();
            if ($workspace) {
                self::$workspace = $workspace;
                return $workspace;
            }
        }

        return null;
    }

    /**
     * Get the current workspace ID.
     * Falls back to session if TenantContext has not been hydrated yet.
     */
    public static function getWorkspaceId(): ?int
    {
        // GP - 19-08-2026 code comment - Get active workspace ID with session fallback
        return self::getWorkspace()?->id
            ?? (session('current_workspace_id') ? (int) session('current_workspace_id') : null);
    }
}
