<?php

namespace App\Http\Controllers;

use App\Models\Consumer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsumerController extends Controller
{
    /**
     * Show the registration form.
     */
    public function create()
    {
        return view('consumers.create');
    }

    /**
     * Handle the form submission and persist a new consumer.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:consumers,email',
            'phone_number'  => 'nullable|string|max:20',
            'gender'        => 'required|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'password'      => 'required|string|min:8|confirmed',
        ]);

        // Debug: log validated data
        logger()->info('Validated consumer data:', $data);

        // Create consumer (QR code will be auto-generated via model boot method)
        $consumer = Consumer::create($data);

        // Debug: log newly created record
        logger()->info('Created consumer:', $consumer->toArray());
        logger()->info('Generated QR code:', $consumer->qrCode ? $consumer->qrCode->toArray() : ['status' => 'not_generated_yet']);

        // Redirect to login page with success message and email pre-filled
        return redirect()
            ->route('login')
            ->with([
                'registration_success' => 'Welcome to GreenCup! Please sign in with your new account.',
                'registration_email' => $consumer->email
            ]);
    }

    /**
     * Show consumer's QR code
     */
    public function showQrCode()
    {
        // Get the authenticated consumer using your existing auth guard
        $consumer = Auth::guard('consumer')->user();
        
        if (!$consumer) {
            return redirect()->route('login')
                ->with('error', 'Please log in to view your QR code.');
        }

        // Load relationships
        $consumer->load(['qrCode', 'pointTransactions']);

        // Make sure consumer has a QR code (generate if not exists)
        if (!$consumer->qrCode) {
            $consumer->generateQrCode();
            $consumer->refresh(); // Reload to get the QR code relationship
        }

        return view('consumers.qr-code', compact('consumer'));
    }

    /**
     * Show consumer profile
     */
    public function showProfile()
    {
        $consumer = Auth::guard('consumer')->user();
        
        if (!$consumer) {
            return redirect()->route('login');
        }

        return view('consumers.profile', compact('consumer'));
    }

    /**
     * Update consumer profile
     */
    public function updateProfile(Request $request)
    {
        $consumer = Auth::guard('consumer')->user();
        
        if (!$consumer) {
            return redirect()->route('login');
        }

        $data = $request->validate([
            'full_name'     => 'required|string|max:255',
            'phone_number'  => 'nullable|string|max:20',
            'gender'        => 'required|in:male,female,other',
            'date_of_birth' => 'nullable|date',
        ]);

        $consumer->update($data);

        return redirect()
            ->route('consumer.profile')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Handle consumer login (if needed)
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::guard('consumer')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Handle consumer logout (if needed)
     */
    public function logout(Request $request)
    {
        Auth::guard('consumer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}