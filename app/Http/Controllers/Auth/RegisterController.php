<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /**
     * Show registration page.
     */
    public function showRegistrationForm()
    {
        // GP - 19-08-2026 code comment - Show registration page view
        return view('auth.register');
    }

    /**
     * Handle registration post.
     */
    public function register(Request $request)
    {
        // GP - 19-08-2026 code comment - Handle new tenant registration
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Create default workspace
        $workspace = Workspace::create([
            'name' => $request->company,
            'slug' => Str::slug($request->company) . '-' . time(),
            'owner_id' => $user->id,
            'timezone' => 'UTC'
        ]);

        // Associate user to workspace as owner
        $workspace->users()->attach($user->id, ['role' => 'owner']);

        // Log the user in
        Auth::login($user);
        
        // Store current workspace in session
        session(['current_workspace_id' => $workspace->id]);

        return redirect()->route('dashboard')->with('success', 'Account created successfully!');
    }
}
