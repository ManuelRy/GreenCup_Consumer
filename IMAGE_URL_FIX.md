# Seller Photo URL Fix - Complete

## Problems Identified

### 1. Seller Profile Images Not Displaying
The seller profile images (`photo_url`) were not being fetched and displayed correctly on the consumer app. The images stored on the remote file server (http://188.166.186.208) were not being properly normalized and proxied.

### 2. Map Modal Shop Photos Not Displaying
After clicking on a shop marker on the map, the modal should display the shop's profile photo, but it wasn't working properly.

### 3. Report Evidence Images Not Displaying
When consumers submit reports with image attachments, the evidence photos were not displaying when they view their own reports.

## Root Causes

### Issue 1 & 2: Store Controller URL Handling
The application uses two different approaches for fetching seller data:

1. **Eloquent Model Approach**: The `Seller` and `SellerPhoto` models have the `NormalizesRemoteUrl` trait and `getPhotoUrlAttribute()` accessor that properly handles remote URLs by:
   - Using the `FileRepository` to construct proper proxy URLs
   - Converting `http://` to `https://`
   - Routing through the local proxy endpoint (`/files/{path}`)

2. **Query Builder Approach**: The `StoreController` was using `DB::table('sellers')` queries which bypass Eloquent models and their accessors. This meant:
   - The `getPhotoUrlAttribute()` accessor was never called
   - The custom `resolveSellerImage()` method didn't handle remote file server URLs
   - Photos were treated as local assets instead of remote files

### Issue 3: ReportEvidence Model Missing URL Normalization
The `ReportEvidence` model didn't have the `NormalizesRemoteUrl` trait or an accessor for `file_url`, so evidence images were being used directly from the database without proper URL normalization.

## Solutions Implemented

### Fix 1: Updated StoreController (Issues 1 & 2)

#### Changes Made

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

5. **Verified `getAllStoresForMap()` method**
   - Already uses `resolveSellerImage()` for the `photo_url` field
   - Map modal now displays shop profile photos correctly

### Fix 2: Updated ReportEvidence Model (Issue 3)

1. **Added `NormalizesRemoteUrl` trait**
   - Imported the trait into the `ReportEvidence` model

2. **Added `getFileUrlAttribute()` accessor**
   - Normalizes the `file_url` field using the trait's `normalizeRemoteUrl()` method
   - Automatically called when accessing `$evidence->file_url` in Blade templates

## How It Works Now

### Remote URL Flow
1. Database stores: `seller_photos/abc123.jpg` or `report_evidences/xyz789.jpg` or full URL
2. Model accessor (or `resolveSellerImage()`) detects it's a remote path
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
2. ✅ Map modal displays shop profile photos properly
3. ✅ Report evidence images display when consumers view their reports
4. ✅ HTTPS is enforced (no mixed content warnings)
5. ✅ Consistent URL handling across the application
6. ✅ Works with gallery feed, store map, and report system
7. ✅ Proper caching headers from proxy controller

## Testing Checklist

### Store/Seller Images
- [x] Check the store map (`/stores/map`) - seller avatars should display
- [x] Click on a store marker - modal should show shop profile photo
- [x] Check the gallery feed (`/stores/gallery`) - all photos should load
- [x] Check individual store profiles - photos should be visible
- [x] Open browser dev tools and verify images are served via `/files/` proxy route

### Report Evidence
- [x] Create a new report with an image attachment
- [x] View your reports list (`/report`) - evidence thumbnails should display
- [x] Click on evidence thumbnail - full-size image should open
- [x] Verify image URLs use `/files/` proxy route

## Files Modified
1. `app/Http/Controllers/StoreController.php`
   - Added `FileRepository` dependency injection
   - Updated `resolveSellerImage()` method
   - Added `normalizeRemoteUrl()` helper method
   - Updated `getSellerPhotos()` method
   - Updated `getFeedPosts()` method

2. `app/Models/ReportEvidence.php`
   - Added `NormalizesRemoteUrl` trait
   - Added `getFileUrlAttribute()` accessor

## Technical Notes

### Why This Approach?
1. **Central Configuration**: File server settings in one place (`config/services.php`)
2. **Security**: All remote files served through local proxy with validation
3. **HTTPS Enforcement**: Prevents mixed content warnings in production
4. **Caching**: Proxy controller adds proper cache headers for performance
5. **Flexibility**: Easy to switch between local and remote storage

### Model Accessors vs Manual Normalization
- **Eloquent Models**: Use accessors (automatic when accessing properties)
- **Query Builder Results**: Use `resolveSellerImage()` helper (manual call needed)
- Both approaches use the same underlying `FileRepository` logic

### Future Improvements
- Consider migrating all `DB::table()` queries to Eloquent for consistency
- Add image optimization/resizing in the proxy controller
- Implement CDN integration for better performance
- Add image placeholder fallbacks for failed loads
