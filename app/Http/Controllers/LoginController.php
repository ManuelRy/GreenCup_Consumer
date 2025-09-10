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
                return redirect()->intended(route('dashboard'))->with('success', 'Welcome back!');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'remember_token')) {
                if (Auth::guard('consumer')->attempt($credentials, false)) {
                    $request->session()->regenerate();
                    return redirect()->intended(route('dashboard'))->with('success', 'Welcome back!');
                }
            }
            throw $e;
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.'])->onlyInput('email');
    }
}
