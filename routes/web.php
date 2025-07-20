<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    LoginController,
    ConsumerController,
    DashboardController,
    AccountController,
    QRController,
    MapController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
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
    
    /*
    |--------------------------------------------------------------------------
    | QR Scanner Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('scan')->name('scan.')->group(function () {
        // QR Scanner page
        Route::get('/', function () {
            $consumer = Auth::guard('consumer')->user();
            return view('scan.index', compact('consumer'));
        })->name('index');
        
        // QR processing endpoints
        Route::post('/process', [QRController::class, 'processAndConfirm'])->name('process');
        Route::post('/confirm', [QRController::class, 'confirmTransaction'])->name('confirm');
    });
    
    // Legacy QR routes for backward compatibility
    Route::prefix('qr')->name('qr.')->group(function () {
        Route::post('/process-and-confirm', [QRController::class, 'processAndConfirm'])->name('process.confirm');
        Route::post('/process', [QRController::class, 'processScan'])->name('process');
        Route::post('/confirm-transaction', [QRController::class, 'confirmTransaction'])->name('confirm');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Store Locator Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('map')->name('map.')->group(function () {
        // Main map page
        Route::get('/', [MapController::class, 'index'])->name('index');
    });
    
    // Alternative map route for backward compatibility
    Route::get('/map', [MapController::class, 'index'])->name('map');
    
    /*
    |--------------------------------------------------------------------------
    | Seller/Store Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('store')->name('store.')->group(function () {
        Route::get('/{id}', [QRController::class, 'showSellerDetails'])->name('details');
    });
    
    // Legacy seller route
    Route::get('/seller/{id}', [QRController::class, 'showSellerDetails'])->name('seller.details');
    
    /*
    |--------------------------------------------------------------------------
    | API Routes for Frontend
    |--------------------------------------------------------------------------
    */
    Route::prefix('api')->name('api.')->group(function () {
        
        // Store/Map APIs
        Route::prefix('stores')->name('stores.')->group(function () {
            Route::get('/', [MapController::class, 'getStores'])->name('list');
            Route::post('/search', [MapController::class, 'searchStores'])->name('search');
            Route::post('/distance', [MapController::class, 'calculateDistance'])->name('distance');
        });
        
        // Individual store APIs
        Route::prefix('store')->name('store.')->group(function () {
            Route::get('/{id}/details', [MapController::class, 'getStoreDetails'])->name('details');
        });
        
        // Legacy API routes for backward compatibility
        Route::get('/stores', [MapController::class, 'getStores'])->name('stores.legacy');
        Route::get('/store/{id}/details', [MapController::class, 'getStoreDetails'])->name('store.details.legacy');
        Route::post('/calculate-distance', [MapController::class, 'calculateDistance'])->name('calculate.distance');
        Route::post('/search-stores', [MapController::class, 'searchStores'])->name('search.stores');
    });
});

/*
|--------------------------------------------------------------------------
| Authentication Control Routes
|--------------------------------------------------------------------------
*/
// Logout route
Route::post('/logout', function () {
    Auth::guard('consumer')->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    
    return redirect()->route('login')->with('success', 'Logged out successfully');
})->middleware(['consumer.auth'])->name('logout');

// Alternative logout for GET requests (for convenience)
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
// Handle 404 errors with authentication check
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
    // Test routes for development
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