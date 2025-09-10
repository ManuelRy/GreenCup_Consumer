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
        $data = $request->validate([
            'full_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:consumers,email',
            'phone_number'  => 'nullable|string|max:20',
            'gender'        => 'required|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'password'      => 'required|string|min:8|confirmed',
        ]);

        $consumer = $this->repo->create($data);

        return redirect()->route('login')->with([
            'registration_success' => 'Welcome to GreenCup! Please sign in with your new account.',
            'registration_email' => $consumer->email
        ]);
    }
}
