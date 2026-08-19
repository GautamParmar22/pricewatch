<?php

namespace App\Http\Controllers;

use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    /**
     * Show configurations panel.
     */
    public function index()
    {
        // GP - 19-08-2026 code comment - Show configurations profile and workspace forms
        
        $workspace = TenantContext::getWorkspace();
        $user = auth()->user();

        return view('settings.index', [
            'workspace' => $workspace,
            'user' => $user
        ]);
    }

    /**
     * Update user profile settings.
     */
    public function updateProfile(Request $request)
    {
        // GP - 19-08-2026 code comment - Save user profile changes
        
        $user = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        return redirect()->route('settings.index')->with('success', 'Profile details updated.');
    }

    /**
     * Update workspace configurations.
     */
    public function updateWorkspace(Request $request)
    {
        // GP - 19-08-2026 code comment - Save tenant workspace configurations
        
        $workspace = TenantContext::getWorkspace();

        $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
        ]);

        $workspace->update([
            'name' => $request->company_name,
            'slug' => Str::slug($request->company_name) . '-' . $workspace->id
        ]);

        return redirect()->route('settings.index')->with('success', 'Workspace configurations updated.');
    }
}
