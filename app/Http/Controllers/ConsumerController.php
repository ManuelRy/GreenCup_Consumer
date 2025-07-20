<?php

namespace App\Http\Controllers;

use App\Models\Consumer;
use Illuminate\Http\Request;

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

        // Create & hash password via model mutator
        $consumer = Consumer::create($data);

        // Debug: log newly created record
        logger()->info('Created consumer:', $consumer->toArray());

        // Redirect to login page with success message and email pre-filled
        return redirect()
            ->route('login') // Change this to your actual login route name
            ->with([
                'registration_success' => 'Welcome to GreenCup! Please sign in with your new account.',
                'registration_email' => $consumer->email
            ]);
    }
}