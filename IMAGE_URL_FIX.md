# Seller Photo URL Fix

## Problem
The seller profile images (`photo_url`) were not being fetched and displayed correctly on the consumer app. The images stored on the remote file server (http://188.166.186.208) were not being properly normalized and proxied.

## Root Cause
The application uses two different approaches for fetching seller data:

1. **Eloquent Model Approach**: The `Seller` and `SellerPhoto` models have the `NormalizesRemoteUrl` trait and `getPhotoUrlAttribute()` accessor that properly handles remote URLs by:
   - Using the `FileRepository` to construct proper proxy URLs
   - Converting `http://` to `https://`
   - Routing through the local proxy endpoint (`/files/{path}`)

2. **Query Builder Approach**: The `StoreController` was using `DB::table('sellers')` queries which bypass Eloquent models and their accessors. This meant:
   - The `getPhotoUrlAttribute()` accessor was never called
   - The custom `resolveSellerImage()` method didn't handle remote file server URLs
   - Photos were treated as local assets instead of remote files

## Solution
Updated the `StoreController` to properly handle remote file URLs:

### Changes Made

1. **Injected FileRepository** into StoreController constructor
   - Added dependency injection for `FileRepository`
   - Now available as `$this->fileRepo`

2. **Updated `resolveSellerImage()` method**
   - Added logic to detect and normalize remote URLs
   - Integrated `FileRepository` to handle remote file paths
   - Created `normalizeRemoteUrl()` helper method (mirrors the trait logic)
   - Proper handling for:
     - Full URLs starting with `http://` or `https://`
     - Protocol-relative URLs starting with `//`
     - Relative paths that need to be resolved via `FileRepository`
     - Legacy local storage paths

3. **Updated `getSellerPhotos()` method**
   - Changed from manual URL construction to using `resolveSellerImage()`
   - Ensures all seller photo URLs are properly normalized

4. **Updated `getFeedPosts()` method**
   - Removed manual photo URL handling
   - Now uses `resolveSellerImage()` for consistency

## How It Works Now

### Remote URL Flow
1. Database stores: `seller_photos/abc123.jpg` or full URL
2. `resolveSellerImage()` detects it's a remote path
3. Calls `FileRepository::get()` which returns: `/files/seller_photos/abc123.jpg`
4. This route is handled by `FileProxyController`
5. Proxy fetches from: `http://188.166.186.208/api/files/get/seller_photos/abc123.jpg`
6. Returns image with HTTPS over the proxy

### Configuration
File server settings are in `config/services.php`:
```php
'files' => [
    'host' => env('FILES_HOST', 'http://188.166.186.208'),
    'use_proxy' => env('FILES_USE_PROXY', true),
],
```

### Proxy Route
Defined in `routes/web.php`:
```php
Route::get('/files/{path}', [FileProxyController::class, 'show'])
    ->where('path', '.*')
    ->name('files.proxy');
```

## Benefits
1. ✅ All seller images now load correctly from remote server
2. ✅ HTTPS is enforced (no mixed content warnings)
3. ✅ Consistent URL handling across the application
4. ✅ Works with both gallery feed and store map
5. ✅ Proper caching headers from proxy controller

## Testing
To verify the fix:
1. Check the store map (`/stores/map`) - seller avatars should display
2. Check the gallery feed (`/stores/gallery`) - all photos should load
3. Check individual store profiles - photos should be visible
4. Open browser dev tools and verify images are served via `/files/` proxy route

## Files Modified
- `app/Http/Controllers/StoreController.php`
  - Added `FileRepository` dependency injection
  - Updated `resolveSellerImage()` method
  - Added `normalizeRemoteUrl()` helper method
  - Updated `getSellerPhotos()` method
  - Updated `getFeedPosts()` method
