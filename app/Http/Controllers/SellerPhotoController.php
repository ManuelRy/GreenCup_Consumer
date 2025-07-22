<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Models\SellerPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SellerPhotoController extends Controller
{
    /**
     * Display the photo gallery management page
     */
    public function index()
    {
        try {
            $seller = Auth::guard('seller')->user();
            
            if (!$seller) {
                return redirect()->route('login')->with('error', 'Please log in to access photos.');
            }

            // Get photos ordered by featured first, then by sort order, then by newest
            $photos = $this->getSellerPhotosFromDB($seller->id);

            return view('sellers.photo', compact('seller', 'photos'));
            
        } catch (\Exception $e) {
            Log::error('Error loading photo gallery: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Unable to load photo gallery.');
        }
    }

    /**
     * Store a newly uploaded photo.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
                'caption' => 'nullable|string|max:255',
                'category' => 'nullable|in:store,products,ambiance',
                'is_featured' => 'nullable|boolean'
            ], [
                'photo.required' => 'Please select a photo to upload.',
                'photo.image' => 'The file must be an image.',
                'photo.mimes' => 'The photo must be a JPEG, PNG, JPG, or GIF file.',
                'photo.max' => 'The photo may not be greater than 5MB.',
                'caption.max' => 'Caption must not exceed 255 characters.',
                'category.in' => 'Please select a valid category.'
            ]);

            $seller = Auth::guard('seller')->user();

            if (!$seller) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please log in to upload photos.'
                ], 401);
            }

            // Handle file upload
            $file = $request->file('photo');
            $path = $file->store('seller_photos', 'public');
            $photoUrl = '/storage/' . $path;

            // If marking as featured, unset other featured photos
            $isFeatured = $request->filled('is_featured') && $request->is_featured;
            if ($isFeatured) {
                DB::table('seller_photos')
                    ->where('seller_id', $seller->id)
                    ->update(['is_featured' => false]);
            }

            // Get current photo count for sort order
            $photoCount = DB::table('seller_photos')
                ->where('seller_id', $seller->id)
                ->count();

            // Insert new photo
            $photoId = DB::table('seller_photos')->insertGetId([
                'seller_id' => $seller->id,
                'photo_url' => $photoUrl,
                'caption' => $request->caption,
                'category' => $request->category ?? 'store',
                'is_featured' => $isFeatured,
                'sort_order' => $photoCount,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Update seller's main photo if this is featured
            if ($isFeatured) {
                DB::table('sellers')
                    ->where('id', $seller->id)
                    ->update([
                        'photo_url' => $photoUrl,
                        'photo_caption' => $request->caption,
                        'updated_at' => now()
                    ]);
            }

            // Return JSON response for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Photo uploaded successfully! 📷',
                    'photo' => [
                        'id' => $photoId,
                        'url' => $photoUrl,
                        'caption' => $request->caption,
                        'category' => $request->category ?? 'store',
                        'is_featured' => $isFeatured
                    ]
                ]);
            }

            return redirect()->route('seller.photos')->with('success', 'Photo uploaded successfully! 📷');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();

        } catch (\Exception $e) {
            Log::error('Error uploading photo: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while uploading the photo.'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'An error occurred while uploading the photo. Please try again.');
        }
    }

    /**
     * Update photo details
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'caption' => 'nullable|string|max:255',
                'category' => 'nullable|in:store,products,ambiance',
                'is_featured' => 'nullable|boolean'
            ], [
                'caption.max' => 'Caption must not exceed 255 characters.',
                'category.in' => 'Please select a valid category.'
            ]);

            $seller = Auth::guard('seller')->user();
            
            if (!$seller) {
                return redirect()->route('login')->with('error', 'Please log in to update photos.');
            }
            
            // Check if photo exists and belongs to this seller
            $photo = DB::table('seller_photos')
                ->where('id', $id)
                ->where('seller_id', $seller->id)
                ->first();

            if (!$photo) {
                return redirect()->route('seller.photos')->with('error', 'Photo not found.');
            }

            // If marking as featured, unset other featured photos
            if ($request->filled('is_featured') && $request->is_featured) {
                DB::table('seller_photos')
                    ->where('seller_id', $seller->id)
                    ->where('id', '!=', $id)
                    ->update(['is_featured' => false]);
            }

            // Update photo
            DB::table('seller_photos')
                ->where('id', $id)
                ->update([
                    'caption' => $request->caption,
                    'category' => $request->category ?? $photo->category,
                    'is_featured' => $request->filled('is_featured') ? true : false,
                    'updated_at' => now()
                ]);

            // Update seller's main photo if this is now featured
            if ($request->filled('is_featured') && $request->is_featured) {
                DB::table('sellers')
                    ->where('id', $seller->id)
                    ->update([
                        'photo_url' => $photo->photo_url,
                        'photo_caption' => $request->caption,
                        'updated_at' => now()
                    ]);
            } elseif (!DB::table('seller_photos')->where('seller_id', $seller->id)->where('is_featured', true)->exists()) {
                // If no featured photo exists, clear seller's main photo
                DB::table('sellers')
                    ->where('id', $seller->id)
                    ->update([
                        'photo_url' => null,
                        'photo_caption' => null,
                        'updated_at' => now()
                    ]);
            }

            return redirect()->route('seller.photos')->with('success', 'Photo updated successfully! ✏️');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating photo: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the photo. Please try again.');
        }
    }

    /**
     * Delete a photo
     */
    public function destroy($id)
    {
        try {
            $seller = Auth::guard('seller')->user();
            
            if (!$seller) {
                return redirect()->route('login')->with('error', 'Please log in to delete photos.');
            }
            
            // Get photo that belongs to this seller
            $photo = DB::table('seller_photos')
                ->where('id', $id)
                ->where('seller_id', $seller->id)
                ->first();

            if (!$photo) {
                return redirect()->route('seller.photos')->with('error', 'Photo not found.');
            }

            // Delete file from storage
            $photoPath = str_replace('/storage/', '', $photo->photo_url);
            
            if (Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            // If this was the featured photo, clear it from seller and set next photo as featured if exists
            if ($photo->is_featured) {
                DB::table('sellers')
                    ->where('id', $seller->id)
                    ->update([
                        'photo_url' => null,
                        'photo_caption' => null,
                        'updated_at' => now()
                    ]);

                // Set the next most recent photo as featured
                $nextPhoto = DB::table('seller_photos')
                    ->where('seller_id', $seller->id)
                    ->where('id', '!=', $id)
                    ->orderByDesc('created_at')
                    ->first();
                
                if ($nextPhoto) {
                    DB::table('seller_photos')
                        ->where('id', $nextPhoto->id)
                        ->update(['is_featured' => true, 'updated_at' => now()]);
                        
                    DB::table('sellers')
                        ->where('id', $seller->id)
                        ->update([
                            'photo_url' => $nextPhoto->photo_url,
                            'photo_caption' => $nextPhoto->caption,
                            'updated_at' => now()
                        ]);
                }
            }

            // Delete photo record
            DB::table('seller_photos')->where('id', $id)->delete();

            return redirect()->route('seller.photos')->with('success', 'Photo deleted successfully! 🗑️');
            
        } catch (\Exception $e) {
            Log::error('Error deleting photo: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while deleting the photo. Please try again.');
        }
    }

    /**
     * Get photo details for editing (AJAX)
     */
    public function show($id)
    {
        try {
            $seller = Auth::guard('seller')->user();
            
            if (!$seller) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }
            
            $photo = DB::table('seller_photos')
                ->where('id', $id)
                ->where('seller_id', $seller->id)
                ->first();

            if (!$photo) {
                return response()->json(['success' => false, 'message' => 'Photo not found'], 404);
            }

            return response()->json([
                'success' => true,
                'photo' => [
                    'id' => $photo->id,
                    'photo_url' => $photo->photo_url,
                    'caption' => $photo->caption,
                    'category' => $photo->category,
                    'is_featured' => $photo->is_featured,
                    'sort_order' => $photo->sort_order,
                    'created_at' => \Carbon\Carbon::parse($photo->created_at)->format('M j, Y g:i A')
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching photo details: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching photo details.'], 500);
        }
    }

    /**
     * Reorder photos (AJAX)
     */
    public function reorder(Request $request)
    {
        try {
            $request->validate([
                'photo_ids' => 'required|array',
                'photo_ids.*' => 'integer|exists:seller_photos,id'
            ]);

            $seller = Auth::guard('seller')->user();
            
            if (!$seller) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            // Update sort order for each photo
            foreach ($request->photo_ids as $index => $photoId) {
                DB::table('seller_photos')
                    ->where('id', $photoId)
                    ->where('seller_id', $seller->id) // Security check
                    ->update([
                        'sort_order' => $index,
                        'updated_at' => now()
                    ]);
            }

            return response()->json(['success' => true, 'message' => 'Photos reordered successfully!']);
            
        } catch (\Exception $e) {
            Log::error('Error reordering photos: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while reordering photos.'], 500);
        }
    }

    /**
     * Get photos for a seller (helper method for other controllers)
     */
    public function getSellerPhotosFromDB($sellerId)
    {
        try {
            if (!DB::getSchemaBuilder()->hasTable('seller_photos')) {
                return collect();
            }

            return DB::table('seller_photos')
                ->where('seller_id', $sellerId)
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->orderByDesc('created_at')
                ->get();

        } catch (\Exception $e) {
            Log::error('Error getting photos from DB for seller ' . $sellerId . ': ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get photo statistics
     */
    public function getStats()
    {
        try {
            $seller = Auth::guard('seller')->user();
            
            if (!$seller) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $stats = [
                'total_photos' => DB::table('seller_photos')->where('seller_id', $seller->id)->count(),
                'featured_photos' => DB::table('seller_photos')->where('seller_id', $seller->id)->where('is_featured', true)->count(),
                'categories' => DB::table('seller_photos')
                    ->where('seller_id', $seller->id)
                    ->select('category', DB::raw('COUNT(*) as count'))
                    ->groupBy('category')
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting photo stats: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Unable to get statistics'], 500);
        }
    }
}