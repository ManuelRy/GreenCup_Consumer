<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RewardRedemptionController;
use App\Http\Controllers\{AccountController, AuthController, ConsumerController, DashboardController, LoginController, StoreController, ReceiptController, RegisterController};

/*
|--------------------------------------------------------------------------
| GreenCup Consumer Web Routes - UPDATED WITH REWARDS
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('dashboard');

// Debug route for testing images
Route::get('/test-images', function () {
    return view('test-images');
});

/*
|--------------------------------------------------------------------------
| Public Routes (Guest Only) - Authentication & Registration
|--------------------------------------------------------------------------
*/
Route::middleware(['guest:consumer'])->group(function () {
    // Authentication
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    // Registration
    Route::get('/register', [RegisterController::class, 'index'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

    // Legacy routes for backward compatibility
    Route::get('/consumers/create', [RegisterController::class, 'index'])->name('consumers.create');
    Route::post('/consumers', [RegisterController::class, 'store'])->name('consumers.store');
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Authenticated Consumers Only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:consumer'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Consumer Pages - Dashboard, Account, Profile, QR Code
    |--------------------------------------------------------------------------
    */
    // Core consumer pages
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/account', [AccountController::class, 'index'])->name('account');
    Route::get('/account/edit', [AccountController::class, 'edit'])->name('account.edit');

    // Profile management
    Route::put('/account/profile', [AccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::put('/account/password', [AccountController::class, 'updatePassword'])->name('account.password.update');
    Route::get('/account/transactions', [AccountController::class, 'transactionHistory'])->name('account.transactions');

    // QR Code
    Route::get('/qr-code', [ConsumerController::class, 'showQrCode'])->name('consumer.qr-code');

    // Receipt scanning
    Route::get('/scan-receipt', [ReceiptController::class, 'scan'])->name('scan.receipt');

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

    // Individual store profiles (PROTECTED - requires auth)
    Route::get('/seller/{id}', [StoreController::class, 'show'])->name('seller.show');
    Route::get('/store/{id}', [StoreController::class, 'show'])->name('store.show');

    /*
    |--------------------------------------------------------------------------
    | Report Routes
    |--------------------------------------------------------------------------
    */
    Route::resource('reports', ReportController::class)->names('report')->only(['index', 'create', 'store']);

    /*
    |--------------------------------------------------------------------------
    | Reward Redemption Routes - NEW
    |--------------------------------------------------------------------------
    */
    Route::prefix('rewards')->name('reward.')->group(function () {
        Route::get('/', [RewardRedemptionController::class, 'index'])->name('index');
        Route::get('/my', [RewardRedemptionController::class, 'myRewards'])->name('my');
        Route::post('/{reward}/redeem', [RewardRedemptionController::class, 'redeem'])->name('redeem');
        Route::post('/{reward}/process', [RewardRedemptionController::class, 'process'])->name('process');
        Route::get('/history', [RewardRedemptionController::class, 'history'])->name('history');
        Route::get('/redemption/{redemption}', [RewardRedemptionController::class, 'show'])->name('redemption.show');
    });

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

        // Store search and distance
        Route::get('/stores/search', [StoreController::class, 'search'])->name('stores.search');
        Route::post('/stores/distance', [StoreController::class, 'calculateDistance'])->name('stores.distance');

        // Gallery APIs
        Route::get('/gallery/feed', [StoreController::class, 'getFeed'])->name('gallery.feed');
        Route::get('/gallery/search', [StoreController::class, 'gallerySearch'])->name('gallery.search');
        Route::get('/gallery/stats', [StoreController::class, 'getPhotoStats'])->name('gallery.stats');

        // Receipt APIs
        Route::prefix('receipt')->name('receipt.')->group(function () {
            Route::post('/check', [ReceiptController::class, 'check'])->name('check');
            Route::post('/claim', [ReceiptController::class, 'claim'])->name('claim');
            Route::get('/history', [ReceiptController::class, 'history'])->name('history');
        });

        // Reward APIs - NEW
        Route::prefix('rewards')->name('rewards.')->group(function () {
            Route::get('/search', [RewardRedemptionController::class, 'search'])->name('search');
            Route::get('/filter', [RewardRedemptionController::class, 'filter'])->name('filter');
            Route::get('/{reward}/check-availability', [RewardRedemptionController::class, 'checkAvailability'])->name('check-availability');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Logout Routes
    |--------------------------------------------------------------------------
    */
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');
});

/*
|--------------------------------------------------------------------------
| Public API Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/
Route::prefix('public-api')->name('public.api.')->group(function () {
    // Store discovery (for gallery browser)
    Route::get('/stores', [StoreController::class, 'getStores'])->name('stores');
    Route::get('/store/{id}', [StoreController::class, 'getStoreDetails'])->name('store.details');
    Route::get('/stores/search', [StoreController::class, 'search'])->name('stores.search');

    // Gallery feed (for gallery browser)
    Route::get('/gallery/feed', [StoreController::class, 'getFeed'])->name('gallery.feed');
    Route::get('/gallery/search', [StoreController::class, 'gallerySearch'])->name('gallery.search');
    Route::get('/gallery/stats', [StoreController::class, 'getPhotoStats'])->name('gallery.stats');

    // Store profiles (for public access - different route names)
    Route::get('/seller/{id}', [StoreController::class, 'show'])->name('seller.public.show');
    Route::get('/store/{id}', [StoreController::class, 'show'])->name('store.public.show');
});

/*
|--------------------------------------------------------------------------
| Error Handling
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    if (!auth('consumer')->check()) {
        return redirect()->route('login')->with('error', 'Please log in to access this page.');
    }

    // If logged in but page not found, redirect to dashboard
    return redirect()->route('dashboard')->with('error', 'Page not found.');
});
