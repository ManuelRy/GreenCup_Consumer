<?php

namespace App\Http\Controllers;

use App\Repository\ConsumerRepository;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    private ConsumerRepository $repo;

    public function __construct(ConsumerRepository $repo)
    {
        $this->repo = $repo;
    }
    public function index()
    {
        return view('consumers.create');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:consumers,email',
            'phone_number'  => 'nullable|string|max:20',
            'password'      => 'required|string|min:8|confirmed',
        ]);

        // Combine first_name and last_name into full_name
        $data = [
            'full_name'     => $validated['first_name'] . ' ' . $validated['last_name'],
            'email'         => $validated['email'],
            'phone_number'  => $validated['phone_number'] ?? null,
            'password'      => $validated['password'],
            'gender'        => 'other', // Default value since form doesn't have it
            'date_of_birth' => null, // Default value since form doesn't have it
        ];

        $consumer = $this->repo->create($data);

        return redirect()->route('login')->with([
            'registration_success' => 'Welcome to GreenCup! Please sign in with your new account.',
            'registration_email' => $consumer->email,
            'show_onboarding' => true // Flag to trigger onboarding tour
        ]);
    }
}
