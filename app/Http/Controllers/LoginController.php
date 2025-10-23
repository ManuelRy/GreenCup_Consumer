<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    //
    public function index()
    {
        if (Auth::guard('consumer')->check()) {
            return redirect()->route('dashboard');
        }
        return view('consumers.login');
    }

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

                // Check if user is newly registered (within last 5 minutes)
                $user = Auth::guard('consumer')->user();
                $isNewlyRegistered = $user->created_at->diffInMinutes(now()) < 5;

                $message = $isNewlyRegistered ? 'Welcome to GreenCup!' : 'Welcome back!';
                $flashData = ['success' => $message];

                // Set flag to show onboarding for new users
                if ($isNewlyRegistered) {
                    $flashData['show_onboarding'] = true;
                }

                return redirect()->intended(route('dashboard'))->with($flashData);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'remember_token')) {
                if (Auth::guard('consumer')->attempt($credentials, false)) {
                    $request->session()->regenerate();

                    // Check if user is newly registered (within last 5 minutes)
                    $user = Auth::guard('consumer')->user();
                    $isNewlyRegistered = $user->created_at->diffInMinutes(now()) < 5;

                    $message = $isNewlyRegistered ? 'Welcome to GreenCup!' : 'Welcome back!';
                    $flashData = ['success' => $message];

                    // Set flag to show onboarding for new users
                    if ($isNewlyRegistered) {
                        $flashData['show_onboarding'] = true;
                    }

                    return redirect()->intended(route('dashboard'))->with($flashData);
                }
            }
            throw $e;
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.'])->onlyInput('email');
    }
}
