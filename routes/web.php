<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ConsumerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\QRController;
use App\Http\Controllers\MapController;  // ← Make sure this is imported
use Illuminate\Support\Facades\Auth;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Public routes (no authentication required)
Route::get('/consumers/create', [ConsumerController::class, 'create'])
    ->name('consumers.create');
Route::post('/consumers', [ConsumerController::class, 'store'])
    ->name('consumers.store');

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');

// Protected routes (requires consumer authentication)
Route::middleware(['consumer.auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Account management
    Route::get('/account', [AccountController::class, 'index'])->name('account');
    
    // QR Scanner page
    Route::get('/scan', function () {
        $consumer = Auth::guard('consumer')->user();
        return view('scan.index', compact('consumer'));
    })->name('scan');
    
    // ← ADD THIS: Map route
    Route::get('/map', [MapController::class, 'index'])->name('map');
    
    // QR Code processing routes
    Route::post('/qr/process-and-confirm', [QRController::class, 'processAndConfirm'])
        ->name('qr.process.confirm');
    Route::post('/qr/process', [QRController::class, 'processScan'])
        ->name('qr.process');
    Route::post('/qr/confirm-transaction', [QRController::class, 'confirmTransaction'])
        ->name('qr.confirm');
    
    // Seller details page
    Route::get('/seller/{id}', [QRController::class, 'showSellerDetails'])
        ->name('seller.details');
    
    // Map API routes
    Route::get('/api/stores', [MapController::class, 'getStores'])->name('stores.list');
    Route::get('/api/store/{id}/details', [MapController::class, 'getStoreDetails'])->name('store.details');
    Route::post('/api/calculate-distance', [MapController::class, 'calculateDistance'])->name('calculate.distance');
    Route::post('/api/search-stores', [MapController::class, 'searchStores'])->name('search.stores');
});

// Logout route
Route::post('/logout', function () {
    Auth::guard('consumer')->logout();
    return redirect()->route('login')->with('success', 'Logged out successfully');
})->name('logout');

// Fallback for 404 errors
Route::fallback(function () {
    if (!Auth::guard('consumer')->check()) {
        return redirect()->route('login')
            ->with('error', 'Please log in to access this page.');
    }
    
    abort(404);
});