<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{ConsumerController, StoreController, ReceiptController};

/*
|--------------------------------------------------------------------------
| GreenCup Consumer Web Routes - FIXED VERSION
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
// FIXED: Changed from 'consumer.auth' to 'auth:consumer'
Route::middleware(['auth:consumer'])->group(function () {

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
    // Add this to your consumer web.php in the public section:
    Route::get('/seller/{id}', [StoreController::class, 'show'])->name('seller.public.show');
    Route::get('/store/{id}', [StoreController::class, 'show'])->name('store.public.show');

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

        // FIXED: Changed POST routes to GET for better compatibility
        Route::get('/stores/search', [StoreController::class, 'search'])->name('stores.search');
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
| Public API Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/
// ADDED: Public API routes for gallery browser and external access
Route::prefix('public-api')->name('public.api.')->group(function () {
    // Store discovery (for gallery browser)
    Route::get('/stores', [StoreController::class, 'getStores'])->name('stores');
    Route::get('/store/{id}', [StoreController::class, 'getStoreDetails'])->name('store.details');
    Route::get('/stores/search', [StoreController::class, 'search'])->name('stores.search');

    // Gallery feed (for gallery browser)
    Route::get('/gallery/feed', [StoreController::class, 'getFeed'])->name('gallery.feed');
    Route::get('/gallery/search', [StoreController::class, 'gallerySearch'])->name('gallery.search');
    Route::get('/gallery/stats', [StoreController::class, 'getPhotoStats'])->name('gallery.stats');

    // Store profiles (for gallery browser)
    Route::get('/seller/{id}', [StoreController::class, 'show'])->name('seller.show');
    Route::get('/store/{id}', [StoreController::class, 'show'])->name('store.show');
});

/*
|--------------------------------------------------------------------------
| Development/Testing Routes (Local Environment Only)
|--------------------------------------------------------------------------
*/
if (app()->environment('local')) {
    Route::prefix('dev')->name('dev.')->group(function () {
        // Quick login as first consumer
        Route::get('/login', function () {
            $consumer = \App\Models\Consumer::first();
            if ($consumer) {
                Auth::guard('consumer')->login($consumer);
                return redirect()->route('dashboard')->with('success', 'Dev login successful!');
            }
            return redirect()->route('login')->with('error', 'No consumer found. Please register first.');
        })->name('login');

        // Quick access to features
        Route::get('/dashboard', function () {
            $consumer = \App\Models\Consumer::first();
            if ($consumer) {
                Auth::guard('consumer')->login($consumer);
                return redirect()->route('dashboard');
            }
            return redirect()->route('login');
        })->name('dashboard');

        Route::get('/gallery', function () {
            $consumer = \App\Models\Consumer::first();
            if ($consumer) {
                Auth::guard('consumer')->login($consumer);
                return redirect()->route('gallery');
            }
            return redirect()->route('login');
        })->name('gallery');

        // Database status
        Route::get('/status', function () {
            return response()->json([
                'consumers' => \App\Models\Consumer::count(),
                'sellers' => \App\Models\Seller::count(),
                'photos' => \DB::table('seller_photos')->count(),
                'transactions' => \DB::table('point_transactions')->count(),
                'environment' => app()->environment(),
            ]);
        })->name('status');
    });
}

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