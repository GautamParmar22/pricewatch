<?php

namespace App\Http\Middleware;

use App\Models\Workspace;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ScopeWorkspace
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // GP - 19-08-2026 code comment - Resolve active tenant workspace scope
        
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $workspaceId = session('current_workspace_id');
        $workspace = null;

        // 1. Verify session workspace
        if ($workspaceId) {
            $workspace = $user->workspaces()->where('workspaces.id', $workspaceId)->first();
        }

        // 2. Fallback to first available workspace
        if (!$workspace) {
            $workspace = $user->workspaces()->first();
            
            // 3. Create a default workspace if the user has none
            if (!$workspace) {
                $companyName = $user->name . "'s Workspace";
                $workspace = Workspace::create([
                    'name' => $companyName,
                    'slug' => \Illuminate\Support\Str::slug($companyName) . '-' . time(),
                    'owner_id' => $user->id,
                    'timezone' => 'UTC'
                ]);

                // Associate user to workspace
                $workspace->users()->attach($user->id, ['role' => 'owner']);
            }

            session(['current_workspace_id' => $workspace->id]);
        }

        // 4. Set global context
        TenantContext::setWorkspace($workspace);

        return $next($request);
    }
}
