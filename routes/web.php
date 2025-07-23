<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{ConsumerController, StoreController, ReceiptController};

/*
|--------------------------------------------------------------------------
| GreenCup Consumer Web Routes - SIMPLIFIED VERSION
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', fn() => redirect()->route('login'))->name('home');

/*
|--------------------------------------------------------------------------
| Public Routes (Guest Only) - Authentication & Registration
|--------------------------------------------------------------------------
*/
Route::middleware(['guest:consumer'])->group(function () {
    // Authentication
    Route::get('/login', [ConsumerController::class, 'showLogin'])->name('login');
    Route::post('/login', [ConsumerController::class, 'login'])->name('login.store');

    // Registration
    Route::get('/register', [ConsumerController::class, 'showRegister'])->name('register');
    Route::post('/register', [ConsumerController::class, 'register'])->name('register.store');

    // Legacy routes for backward compatibility
    Route::get('/consumers/create', [ConsumerController::class, 'showRegister'])->name('consumers.create');
    Route::post('/consumers', [ConsumerController::class, 'register'])->name('consumers.store');
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Authenticated Consumers Only)
|--------------------------------------------------------------------------
*/
Route::middleware(['consumer.auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Consumer Pages - Dashboard, Account, Profile, QR Code
    |--------------------------------------------------------------------------
    */
    // Core consumer pages
    Route::get('/dashboard', [ConsumerController::class, 'dashboard'])->name('dashboard');
    Route::get('/account', [ConsumerController::class, 'account'])->name('account');
    Route::get('/account/edit', [ConsumerController::class, 'showEditAccount'])->name('account.edit');
    
    // Profile management
    Route::put('/account/profile', [ConsumerController::class, 'updateProfile'])->name('account.profile.update');
    Route::put('/account/password', [ConsumerController::class, 'updatePassword'])->name('account.password.update');
    Route::get('/account/transactions', [ConsumerController::class, 'transactionHistory'])->name('account.transactions');
    
    // QR Code
    Route::get('/qr-code', [ConsumerController::class, 'showQrCode'])->name('consumer.qr-code');
    
    // Receipt scanning
    Route::get('/scan-receipt', [ConsumerController::class, 'showScanReceipt'])->name('scan.receipt');
    Route::get('/scan', [ConsumerController::class, 'showScanReceipt'])->name('scan'); // Alternative

    /*
    |--------------------------------------------------------------------------
    | Store Pages - Gallery, Map, Profiles, Search
    |--------------------------------------------------------------------------
    */
    // Gallery & Feed
    Route::get('/gallery', [StoreController::class, 'gallery'])->name('gallery');
    Route::get('/products', [StoreController::class, 'gallery'])->name('products'); // Alternative name
    
    // Map & Store Locator
    Route::get('/map', [StoreController::class, 'map'])->name('map');
    
    // Individual store profiles
    Route::get('/seller/{id}', [StoreController::class, 'show'])->name('seller.show');
    Route::get('/store/{id}', [StoreController::class, 'show'])->name('store.show');

    /*
    |--------------------------------------------------------------------------
    | API Routes for AJAX/Frontend
    |--------------------------------------------------------------------------
    */
    Route::prefix('api')->name('api.')->group(function () {
        
        // Consumer APIs
        Route::get('/consumer/points', [ConsumerController::class, 'getPoints'])->name('consumer.points');
        
        // Store APIs
        Route::get('/stores', [StoreController::class, 'getStores'])->name('stores');
        Route::get('/store/{id}/details', [StoreController::class, 'getStoreDetails'])->name('store.details');
        Route::get('/store/{id}/transactions', [StoreController::class, 'getTransactions'])->name('store.transactions');
        Route::post('/stores/search', [StoreController::class, 'search'])->name('stores.search');
        Route::post('/stores/distance', [StoreController::class, 'calculateDistance'])->name('stores.distance');
        
        // Gallery APIs
        Route::get('/gallery/feed', [StoreController::class, 'getFeed'])->name('gallery.feed');
        Route::get('/gallery/search', [StoreController::class, 'gallerySearch'])->name('gallery.search');
        Route::get('/gallery/stats', [StoreController::class, 'getPhotoStats'])->name('gallery.stats');
        
        // Receipt APIs (kept separate as specialized functionality)
        Route::prefix('receipt')->name('receipt.')->group(function () {
            Route::post('/check', [ReceiptController::class, 'check'])->name('check');
            Route::post('/claim', [ReceiptController::class, 'claim'])->name('claim');
            Route::get('/history', [ReceiptController::class, 'history'])->name('history');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Logout Routes
    |--------------------------------------------------------------------------
    */
    Route::post('/logout', [ConsumerController::class, 'logout'])->name('logout');
    Route::get('/logout', [ConsumerController::class, 'logout'])->name('logout.get');
});

/*
|--------------------------------------------------------------------------
| Error Handling
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    if (!Auth::guard('consumer')->check()) {
        return redirect()->route('login')->with('error', 'Please log in to access this page.');
    }
    return response()->view('errors.404', [], 404);
});