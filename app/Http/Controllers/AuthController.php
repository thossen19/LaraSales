<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Show the registration form.
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request): RedirectResponse
    {
        // Clear the session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }
}
