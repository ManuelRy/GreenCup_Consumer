# Image URL Fix - Testing Guide

## Overview
This guide helps you test the image URL fixes for seller photos and report evidence.

## What Was Fixed

### 1. Seller Profile Images
- ✅ Store map seller avatars
- ✅ Map modal shop profile photos
- ✅ Gallery feed photos
- ✅ Individual store profiles

### 2. Report Evidence Images
- ✅ Evidence thumbnails in report list
- ✅ Full-size evidence images
- ✅ Consumer can see their own uploaded evidence

## Testing Instructions

### Test 1: Store Map Images

1. **Navigate to Store Map**
   ```
   URL: http://localhost/stores/map
   ```

2. **Check Seller Avatars on Map**
   - Seller avatars should display on map markers
   - If no avatar, should show first letter of business name

3. **Click on a Store Marker**
   - Modal should open
   - Store profile photo should display in the modal header
   - If no photo, should show fallback avatar

4. **Open Browser DevTools (F12)**
   - Go to Network tab
   - Filter by "files"
   - Verify image requests go through `/files/` route
   - Example: `/files/seller_photos/abc123.jpg`

### Test 2: Gallery Feed Images

1. **Navigate to Gallery**
   ```
   URL: http://localhost/stores/gallery
   ```

2. **Verify Photos Display**
   - All gallery photos should load
   - Store avatars should display
   - No broken image icons

3. **Check Network Requests**
   - Open DevTools → Network tab
   - Verify images use `/files/` proxy route

### Test 3: Report Evidence Images

1. **Create a New Report with Image**
   ```
   URL: http://localhost/reports/create
   ```
   - Fill in title, priority, tag, description
   - Upload an image (max 5MB)
   - Submit the report

2. **View Your Reports List**
   ```
   URL: http://localhost/reports
   ```
   - Find your newly created report
   - Evidence thumbnail should display (80x80 preview)
   - No broken image

3. **Click on Evidence Thumbnail**
   - Full-size image should open in modal/viewer
   - Image should be clear and properly loaded

4. **Verify Network Requests**
   - Open DevTools → Network tab
   - Filter by "files"
   - Evidence image should use `/files/report_evidences/` route
   - Example: `/files/report_evidences/xyz789.jpg`

## Expected Behavior

### Success Indicators
- ✅ All images load without broken image icons
- ✅ Images are served through `/files/{path}` proxy route
- ✅ No mixed content warnings in console
- ✅ Images have proper caching headers
- ✅ Fallback avatars display when no image exists

### URL Pattern Examples

**Seller Photos:**
```
/files/seller_photos/1234567890abcdef.jpg
/files/sellers/profile_123.png
```

**Report Evidence:**
```
/files/report_evidences/evidence_1234567890.jpg
```

**Gallery Photos:**
```
/files/seller_photos/featured_abc123.jpg
```

## Troubleshooting

### Images Not Loading?

1. **Check Configuration**
   ```bash
   php artisan config:cache
   php artisan config:clear
   ```

2. **Verify FILES_HOST in .env**
   ```env
   FILES_HOST=http://188.166.186.208
   FILES_USE_PROXY=true
   ```

3. **Check File Proxy Controller**
   ```bash
   php artisan route:list --path=files
   ```
   Should show: `GET|HEAD files/{path} ... files.proxy`

4. **Test Proxy Directly**
   ```
   Visit: http://localhost/files/test.jpg
   ```
   Should attempt to fetch from remote server

### Console Errors?

1. **Mixed Content Warning**
   - Ensure `FILES_USE_PROXY=true` in .env
   - Verify `URL::forceScheme('https')` in production

2. **404 Not Found**
   - Check if file exists on remote server
   - Verify path format (no leading `/`)

3. **CORS Errors**
   - Proxy controller handles CORS automatically
   - Check `FileProxyController::show()` method

## Manual Testing Checklist

- [ ] Store map loads with seller avatars
- [ ] Clicking store marker shows modal with profile photo
- [ ] Gallery feed displays all photos correctly
- [ ] Individual store profiles show photos
- [ ] Create report with image evidence
- [ ] View reports list shows evidence thumbnails
- [ ] Click evidence thumbnail opens full-size image
- [ ] No broken images anywhere
- [ ] Browser DevTools shows images via `/files/` route
- [ ] No console errors related to images
- [ ] Images load on both HTTP and HTTPS
- [ ] Fallback avatars work when no image exists

## Performance Testing

### Check Image Loading Times
1. Open DevTools → Network tab
2. Reload page
3. Look for image requests
4. Verify caching headers:
   - `Cache-Control: public, max-age=604800`
   - Images should load faster on second visit

### Check Proxy Response
1. Test direct file URL:
   ```
   http://localhost/files/seller_photos/test.jpg
   ```
2. Should return image quickly (< 1 second)
3. Second request should be faster (cached)

## Automated Testing (Future)

Consider adding these tests:

```php
// Feature test example
public function test_seller_images_display_correctly()
{
    $seller = Seller::factory()->create([
        'photo_url' => 'seller_photos/test.jpg'
    ]);
    
    $response = $this->get('/stores/map');
    
    $response->assertSee($seller->photo_url);
    $this->assertStringContainsString('/files/', $seller->photo_url);
}

public function test_report_evidence_displays_correctly()
{
    $consumer = Consumer::factory()->create();
    $report = Report::factory()->create([
        'reporter_id' => $consumer->id
    ]);
    $evidence = ReportEvidence::factory()->create([
        'report_id' => $report->id,
        'file_url' => 'report_evidences/test.jpg'
    ]);
    
    $this->actingAs($consumer, 'consumer')
         ->get('/reports')
         ->assertSee($evidence->file_url);
}
```

## Sign-Off

- **Tested By:** _______________
- **Date:** _______________
- **All Tests Passed:** [ ] Yes [ ] No
- **Notes:** _______________________________________________
