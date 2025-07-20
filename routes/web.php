<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    LoginController,
    ConsumerController,
    DashboardController,
    AccountController,
    QRController,
    MapController,
    SellerController,
    GalleryController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', fn() => redirect()->route('login'))->name('home');

/*
|--------------------------------------------------------------------------
| Public Routes (Guest Only)
|--------------------------------------------------------------------------
*/
Route::middleware(['guest:consumer'])->group(function () {
    // Authentication routes
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    
    // Consumer registration routes
    Route::get('/register', [ConsumerController::class, 'create'])->name('register');
    Route::post('/register', [ConsumerController::class, 'store'])->name('register.store');
    
    // Legacy routes for backward compatibility
    Route::get('/consumers/create', [ConsumerController::class, 'create'])->name('consumers.create');
    Route::post('/consumers', [ConsumerController::class, 'store'])->name('consumers.store');
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Authenticated Consumers Only)
|--------------------------------------------------------------------------
*/
Route::middleware(['consumer.auth'])->group(function () {
    
    // Core application pages
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/account', [AccountController::class, 'index'])->name('account');
    
    // QR Scanner page
    Route::get('/scan', function () {
        $consumer = Auth::guard('consumer')->user();
        return view('scan.index', compact('consumer'));
    })->name('scan');
    
    // Gallery page (Product service)
    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');
    Route::get('/products', [GalleryController::class, 'index'])->name('products'); // Alternative name
    
    // Map page
    Route::get('/map', [MapController::class, 'index'])->name('map');
    
    // Seller/Store pages
    Route::get('/seller/{id}', [SellerController::class, 'show'])->name('seller.show');
    Route::get('/store/{id}', [SellerController::class, 'show'])->name('store.show');
    
    /*
    |--------------------------------------------------------------------------
    | QR Processing Routes
    |--------------------------------------------------------------------------
    */
    Route::post('/qr/process-and-confirm', [QRController::class, 'processAndConfirm'])->name('qr.process.confirm');
    Route::post('/qr/process', [QRController::class, 'processScan'])->name('qr.process');
    Route::post('/qr/confirm-transaction', [QRController::class, 'confirmTransaction'])->name('qr.confirm');
    
    /*
    |--------------------------------------------------------------------------
    | Consumer Profile Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('consumer')->name('consumer.')->group(function () {
        Route::get('/qr', [ConsumerController::class, 'showQrCode'])->name('qr');
    //     Route::get('/profile', [ConsumerController::class, 'showProfile'])->name('profile');
    //     Route::put('/profile', [ConsumerController::class, 'updateProfile'])->name('profile.update');
    });
    
    /*
    |--------------------------------------------------------------------------
    | API Routes for Frontend
    |--------------------------------------------------------------------------
    */
    Route::prefix('api')->name('api.')->group(function () {
        // Store/Map APIs
        Route::get('/stores', [MapController::class, 'getStores'])->name('stores');
        Route::get('/store/{id}/details', [MapController::class, 'getStoreDetails'])->name('store.details');
        Route::post('/stores/search', [MapController::class, 'searchStores'])->name('stores.search');
        Route::post('/stores/distance', [MapController::class, 'calculateDistance'])->name('stores.distance');
    });
});

/*
|--------------------------------------------------------------------------
| Authentication Control Routes
|--------------------------------------------------------------------------
*/
Route::post('/logout', function () {
    Auth::guard('consumer')->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    
    return redirect()->route('login')->with('success', 'Logged out successfully');
})->middleware(['consumer.auth'])->name('logout');

Route::get('/logout', function () {
    Auth::guard('consumer')->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    
    return redirect()->route('login')->with('success', 'Logged out successfully');
})->middleware(['consumer.auth'])->name('logout.get');

/*
|--------------------------------------------------------------------------
| Error Handling Routes
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    if (!Auth::guard('consumer')->check()) {
        return redirect()->route('login')
            ->with('error', 'Please log in to access this page.');
    }
    
    return response()->view('errors.404', [], 404);
});

/*
|--------------------------------------------------------------------------
| Development/Testing Routes (Remove in Production)
|--------------------------------------------------------------------------
*/
if (app()->environment(['local', 'testing'])) {
    Route::prefix('dev')->name('dev.')->group(function () {
        Route::get('/test-auth', function () {
            return [
                'authenticated' => Auth::guard('consumer')->check(),
                'user' => Auth::guard('consumer')->user(),
                'session' => session()->all()
            ];
        })->name('test.auth');
        
        Route::get('/clear-session', function () {
            session()->flush();
            return redirect()->route('login')->with('success', 'Session cleared');
        })->name('clear.session');
    });
}