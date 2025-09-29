<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EnvironmentalImpactController extends Controller
{
    public function index()
    {
        return view('environmental-impact.index');
    }
}
