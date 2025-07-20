<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GalleryController extends Controller
{
    /**
     * Show what sellers are posting - simple feed for consumers
     */
    public function index()
    {
        $consumer = Auth::guard('consumer')->user();
        
        if (!$consumer) {
            return redirect()->route('login')
                ->with('error', 'Please login to view posts');
        }

        // Get all stores with photos (what they're posting)
        $stores = DB::table('sellers')
            ->select([
                'id',
                'business_name',
                'description', 
                'photo_url',
                'address'
            ])
            ->where('is_active', true)
            ->whereNotNull('photo_url') // Only stores that have posted photos
            ->orderBy('updated_at', 'desc') // Show newest posts first
            ->get();

        return view('gallery.index', [
            'stores' => $stores,
            'consumer' => $consumer
        ]);
    }
}