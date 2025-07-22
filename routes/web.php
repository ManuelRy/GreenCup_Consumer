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
| GreenCup Consumer Web Routes
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
    
    /*
    |--------------------------------------------------------------------------
    | Enhanced Gallery Routes - Instagram/Facebook Style
    |--------------------------------------------------------------------------
    */
    // Main gallery pages
    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');
    Route::get('/products', [GalleryController::class, 'index'])->name('products'); // Alternative name
    
    Route::get('/gallery/feed', [App\Http\Controllers\GalleryController::class, 'getFeed'])->name('gallery.feed');
    Route::get('/gallery/search', [GalleryController::class, 'search'])->name('gallery.search');
    Route::get('/gallery/stats', [GalleryController::class, 'getPhotoStats'])->name('gallery.stats');
    
    // Individual seller/store viewing (for consumers)
    Route::get('/seller/{id}', [GalleryController::class, 'show'])->name('seller.show');
    Route::get('/store/{id}', [GalleryController::class, 'show'])->name('store.show');
    
    // Post modal functionality - view all photos from a post
    Route::get('/seller/{sellerId}/post/{postIndex}', [GalleryController::class, 'showPost'])->name('seller.post');
    
    // Store transactions for consumers to view
    Route::get('/seller/{id}/transactions', [GalleryController::class, 'getTransactions'])->name('seller.transactions');
    
    /*
    |--------------------------------------------------------------------------
    | Map and Location Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/map', [MapController::class, 'index'])->name('map');
    
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
        // Ready for future profile management
        // Route::get('/profile', [ConsumerController::class, 'showProfile'])->name('profile');
        // Route::put('/profile', [ConsumerController::class, 'updateProfile'])->name('profile.update');
    });
    
    /*
    |--------------------------------------------------------------------------
    | API Routes for Frontend AJAX
    |--------------------------------------------------------------------------
    */
    Route::prefix('api')->name('api.')->group(function () {
        // Map/Store APIs
        Route::get('/stores', [MapController::class, 'getStores'])->name('stores');
        Route::get('/store/{id}/details', [MapController::class, 'getStoreDetails'])->name('store.details');
        Route::post('/stores/search', [MapController::class, 'searchStores'])->name('stores.search');
        Route::post('/stores/distance', [MapController::class, 'calculateDistance'])->name('stores.distance');
        
        // Gallery APIs for AJAX functionality
        Route::get('/gallery/stores', [GalleryController::class, 'index'])->name('gallery.api.stores');
        Route::post('/gallery/stores/search', [GalleryController::class, 'search'])->name('gallery.api.search');
        
        // Post interaction APIs (ready for future features)
        Route::post('/post/{sellerId}/{postIndex}/like', function($sellerId, $postIndex) {
            // Like post functionality
            return response()->json(['success' => true, 'likes' => rand(1, 50)]);
        })->name('post.like');
        
        Route::post('/post/{sellerId}/{postIndex}/comment', function($sellerId, $postIndex) {
            // Comment functionality
            return response()->json(['success' => true, 'message' => 'Comment feature coming soon!']);
        })->name('post.comment');
        
        Route::get('/seller/{id}/follow', function($id) {
            // Follow store functionality
            return response()->json(['success' => true, 'following' => true]);
        })->name('seller.follow');
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
        
        // Authentication testing
        Route::get('/test-auth', function () {
            return [
                'authenticated' => Auth::guard('consumer')->check(),
                'user' => Auth::guard('consumer')->user(),
                'session' => session()->all()
            ];
        })->name('test.auth');
        
        // Session management
        Route::get('/clear-session', function () {
            session()->flush();
            return redirect()->route('login')->with('success', 'Session cleared');
        })->name('clear.session');
        
        // Gallery testing
        Route::get('/test-gallery', function () {
            try {
                $galleryController = new \App\Http\Controllers\GalleryController();
                $stats = $galleryController->getPhotoStats();
                return $stats->getData();
            } catch (Exception $e) {
                return ['error' => $e->getMessage()];
            }
        })->name('test.gallery');
        
        // Database and storage testing
        Route::get('/test-db', function () {
            try {
                $results = [
                    'database' => [
                        'sellers_count' => \DB::table('sellers')->where('is_active', true)->count(),
                        'photos_count' => \DB::getSchemaBuilder()->hasTable('seller_photos') 
                            ? \DB::table('seller_photos')->count() 
                            : 0,
                        'consumers_count' => \DB::table('consumers')->count(),
                    ],
                    'tables_exist' => [
                        'sellers' => \DB::getSchemaBuilder()->hasTable('sellers'),
                        'seller_photos' => \DB::getSchemaBuilder()->hasTable('seller_photos'),
                        'consumers' => \DB::getSchemaBuilder()->hasTable('consumers'),
                        'point_transactions' => \DB::getSchemaBuilder()->hasTable('point_transactions'),
                        'qr_codes' => \DB::getSchemaBuilder()->hasTable('qr_codes'),
                    ],
                    'storage' => [
                        'storage_link_exists' => is_link(public_path('storage')) || is_dir(public_path('storage')),
                        'seller_photos_accessible' => is_dir(storage_path('app/public/seller_photos')),
                    ]
                ];
                
                return response()->json($results, 200, [], JSON_PRETTY_PRINT);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 500);
            }
        })->name('test.db');
        
        // Storage testing (for your symbolic link issue)
        Route::get('/debug-storage', function() {
            $results = [];
            
            // Check if storage link exists
            $publicStoragePath = public_path('storage');
            $results['storage_link_exists'] = is_link($publicStoragePath) || is_dir($publicStoragePath);
            $results['public_storage_path'] = $publicStoragePath;
            
            // Check actual storage path
            $storagePath = storage_path('app/public/seller_photos');
            $results['storage_folder_exists'] = is_dir($storagePath);
            $results['storage_folder_path'] = $storagePath;
            
            // List files in storage
            if (is_dir($storagePath)) {
                $files = scandir($storagePath);
                $results['files_in_storage'] = array_slice(array_diff($files, ['.', '..']), 0, 10);
                $results['total_files'] = count(array_diff($files, ['.', '..']));
            }
            
            // Check sample photo from database
            if (\DB::getSchemaBuilder()->hasTable('seller_photos')) {
                $samplePhoto = \DB::table('seller_photos')->first();
                if ($samplePhoto) {
                    $results['sample_photo'] = [
                        'database_url' => $samplePhoto->photo_url,
                        'asset_url' => asset($samplePhoto->photo_url),
                        'file_exists' => file_exists(public_path($samplePhoto->photo_url)),
                        'storage_file_exists' => file_exists(storage_path('app/public/seller_photos/' . basename($samplePhoto->photo_url))),
                    ];
                }
            }
            
            // Test URLs
            $results['test_urls'] = [
                'gallery' => url('/gallery'),
                'sample_image' => url('/storage/seller_photos/test.jpg'),
                'direct_public' => url('/public/storage/seller_photos/test.jpg'),
            ];
            
            return response()->json($results, 200, [], JSON_PRETTY_PRINT);
        })->name('debug.storage');
        
        // Quick photo upload test for sellers (if needed)
        Route::get('/test-photo-urls', function() {
            if (!\DB::getSchemaBuilder()->hasTable('seller_photos')) {
                return ['error' => 'seller_photos table does not exist'];
            }
            
            $photos = \DB::table('seller_photos')->limit(5)->get(['id', 'photo_url', 'seller_id']);
            
            $testResults = [];
            foreach ($photos as $photo) {
                $testResults[] = [
                    'id' => $photo->id,
                    'original_url' => $photo->photo_url,
                    'asset_url' => asset($photo->photo_url),
                    'direct_url' => url($photo->photo_url),
                    'file_exists' => file_exists(public_path($photo->photo_url)),
                ];
            }
            
            return response()->json($testResults, 200, [], JSON_PRETTY_PRINT);
        })->name('test.photo.urls');
    });
    
    // Quick test page to verify everything is working
    Route::get('/test', function() {
        $consumer = Auth::guard('consumer')->user();
        
        return response()->json([
            'app_name' => config('app.name'),
            'environment' => app()->environment(),
            'consumer_authenticated' => Auth::guard('consumer')->check(),
            'consumer_info' => $consumer ? [
                'id' => $consumer->id,
                'name' => $consumer->full_name ?? $consumer->name,
                'email' => $consumer->email,
            ] : null,
            'available_routes' => [
                'gallery' => route('gallery'),
                'dashboard' => route('dashboard'),
                'scan' => route('scan'),
                'map' => route('map'),
            ],
            'timestamp' => now()->toDateTimeString(),
        ]);
    });
}

/*
|--------------------------------------------------------------------------
| Health Check Route
|--------------------------------------------------------------------------
*/
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toDateTimeString(),
        'environment' => app()->environment(),
        'database' => \DB::connection()->getPdo() ? 'connected' : 'disconnected',
    ]);
})->name('health');