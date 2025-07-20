<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function create()
    {
        // If already logged in as consumer, send to dashboard
        if (Auth::guard('consumer')->check()) {
            return redirect()->route('dashboard');
        }

        return view('consumers.login');
    }

    /**
     * Handle login submission.
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->filled('remember_me');

        try {
            if (Auth::guard('consumer')->attempt($credentials, $remember)) {
                $request->session()->regenerate();

                return redirect()
                    ->intended(route('dashboard'))
                    ->with('success', 'Welcome back!');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            // If remember_token column issue, try without remember
            if (str_contains($e->getMessage(), 'remember_token')) {
                if (Auth::guard('consumer')->attempt($credentials, false)) {
                    $request->session()->regenerate();

                    return redirect()
                        ->intended(route('dashboard'))
                        ->with('success', 'Welcome back!');
                }
            }

            // Re-throw if it's a different error
            throw $e;
        }

        return back()
            ->withErrors(['email' => 'The provided credentials do not match our records.'])
            ->onlyInput('email');
    }

    /**
     * Log the consumer out.
     */
    public function destroy(Request $request)
    {
        Auth::guard('consumer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'You have been logged out successfully.');
    }
}
