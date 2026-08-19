<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show login page.
     */
    public function showLoginForm()
    {
        // GP - 19-08-2026 code comment - Show login page view
        return view('auth.login');
    }

    /**
     * Handle login post.
     */
    public function login(Request $request)
    {
        // GP - 19-08-2026 code comment - Handle user session login
        
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Set current workspace session
            $user = Auth::user();
            $workspace = $user->workspaces()->first();
            if ($workspace) {
                session(['current_workspace_id' => $workspace->id]);
            }

            return redirect()->intended(route('dashboard'))->with('success', 'Logged in successfully!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        // GP - 19-08-2026 code comment - Handle session logout
        
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(url('/'))->with('info', 'Signed out successfully!');
    }
}
