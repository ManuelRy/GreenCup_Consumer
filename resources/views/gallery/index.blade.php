@extends('master')

@section('content')
    <!-- Same animated background as dashboard -->
    <div class="background-animation">
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
        <div class="particles">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>
    </div>

    <div class="container">
        <!-- Header matching Green Cup dashboard style -->
        <div class="header">
            <div class="gallery-nav">
                <a href="{{ route('dashboard') }}" class="back-btn">
                    ←
                </a>
                <div class="app-title">Store Gallery</div>
                <button class="search-btn" onclick="toggleSearch()">
                    🔍
                </button>
            </div>
        </div>

        <!-- Gallery Content -->
        <div class="gallery-content">
            @if($stores && count($stores) > 0)
                <div class="stores-gallery">
                    @foreach($stores as $store)
                    <div class="store-gallery-section">
                        <!-- Store Header with Rank Badge -->
                        <div class="store-header" onclick="viewStore({{ $store->id }})">
                            <div class="store-profile">
                                <div class="store-avatar {{ $store->rank_class }}">
                                    {{ substr($store->business_name, 0, 1) }}
                                </div>
                                <div class="store-info">
                                    <h3 class="store-name">{{ $store->business_name }}</h3>
                                    <p class="store-location">📍 {{ $store->address }}</p>
                                    <div class="store-rank-info">
                                        <span class="rank-badge {{ $store->rank_class }}">
                                            {{ $store->rank_icon }} {{ $store->rank_text }}
                                        </span>
                                        <span class="post-count">{{ count($store->photos) }} photos</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Store Description -->
                        <div class="store-description">
                            <p>{{ $store->description }}</p>
                        </div>

                        <!-- Photo Gallery for this Store -->
                        <div class="photo-gallery">
                            @if(count($store->photos) == 1)
                                <!-- Single Photo Layout -->
                                <div class="single-photo">
                                    <img src="{{ $store->photos[0]->url }}" alt="{{ $store->business_name }}" 
                                         onclick="openPhotoModal('{{ $store->photos[0]->url }}', '{{ $store->business_name }}')"
                                         onerror="this.src='https://via.placeholder.com/800x600/2E8B57/FFFFFF?text={{ urlencode(substr($store->business_name, 0, 1)) }}'">
                                </div>
                            @elseif(count($store->photos) == 2)
                                <!-- Two Photos Layout -->
                                <div class="two-photos">
                                    <img src="{{ $store->photos[0]->url }}" alt="{{ $store->business_name }}" 
                                         onclick="openPhotoModal('{{ $store->photos[0]->url }}', '{{ $store->business_name }}')"
                                         onerror="this.src='https://via.placeholder.com/400x300/2E8B57/FFFFFF?text={{ urlencode(substr($store->business_name, 0, 1)) }}'">
                                    <img src="{{ $store->photos[1]->url }}" alt="{{ $store->business_name }}" 
                                         onclick="openPhotoModal('{{ $store->photos[1]->url }}', '{{ $store->business_name }}')"
                                         onerror="this.src='https://via.placeholder.com/400x300/3CB371/FFFFFF?text={{ urlencode(substr($store->business_name, 0, 1)) }}'">
                                </div>
                            @elseif(count($store->photos) == 3)
                                <!-- Three Photos Layout -->
                                <div class="three-photos">
                                    <div class="main-photo">
                                        <img src="{{ $store->photos[0]->url }}" alt="{{ $store->business_name }}" 
                                             onclick="openPhotoModal('{{ $store->photos[0]->url }}', '{{ $store->business_name }}')"
                                             onerror="this.src='https://via.placeholder.com/600x300/2E8B57/FFFFFF?text={{ urlencode(substr($store->business_name, 0, 1)) }}'">
                                    </div>
                                    <div class="side-photos">
                                        <img src="{{ $store->photos[1]->url }}" alt="{{ $store->business_name }}" 
                                             onclick="openPhotoModal('{{ $store->photos[1]->url }}', '{{ $store->business_name }}')"
                                             onerror="this.src='https://via.placeholder.com/300x150/3CB371/FFFFFF?text={{ urlencode(substr($store->business_name, 0, 1)) }}'">
                                        <img src="{{ $store->photos[2]->url }}" alt="{{ $store->business_name }}" 
                                             onclick="openPhotoModal('{{ $store->photos[2]->url }}', '{{ $store->business_name }}')"
                                             onerror="this.src='https://via.placeholder.com/300x150/228B22/FFFFFF?text={{ urlencode(substr($store->business_name, 0, 1)) }}'">
                                    </div>
                                </div>
                            @else
                                <!-- Four+ Photos Layout (Facebook style) -->
                                <div class="multiple-photos">
                                    <div class="main-photo">
                                        <img src="{{ $store->photos[0]->url }}" alt="{{ $store->business_name }}" 
                                             onclick="openPhotoModal('{{ $store->photos[0]->url }}', '{{ $store->business_name }}')"
                                             onerror="this.src='https://via.placeholder.com/600x400/2E8B57/FFFFFF?text={{ urlencode(substr($store->business_name, 0, 1)) }}'">
                                    </div>
                                    <div class="grid-photos">
                                        @for($i = 1; $i < min(4, count($store->photos)); $i++)
                                            <div class="grid-photo {{ $i == 3 && count($store->photos) > 4 ? 'more-photos' : '' }}">
                                                <img src="{{ $store->photos[$i]->url }}" alt="{{ $store->business_name }}" 
                                                     onclick="openPhotoModal('{{ $store->photos[$i]->url }}', '{{ $store->business_name }}')"
                                                     onerror="this.src='https://via.placeholder.com/200x133/3CB371/FFFFFF?text={{ urlencode(substr($store->business_name, 0, 1)) }}'">
                                                @if($i == 3 && count($store->photos) > 4)
                                                    <div class="photo-overlay">
                                                        <span>+{{ count($store->photos) - 4 }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Store Actions -->
                        <div class="store-actions">
                            <button class="action-btn location-btn" onclick="openLocation('{{ $store->address }}')">
                                <span>📍</span>
                                <span>View Location</span>
                            </button>
                            <button class="action-btn share-btn" onclick="shareStore({{ $store->id }})">
                                <span>📤</span>
                                <span>Share Store</span>
                            </button>
                            <button class="action-btn visit-btn" onclick="viewStore({{ $store->id }})">
                                <span>🏪</span>
                                <span>Visit Store</span>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="no-posts">
                    <div class="no-posts-icon">🏪</div>
                    <h3>No stores found</h3>
                    <p>There are no stores with photos in the database yet. Add some stores with photos to see the gallery!</p>
                    <div style="margin-top: 20px;">
                        <a href="{{ route('dashboard') }}" style="color: #2E8B57; text-decoration: none; font-weight: 600;">
                            ← Back to Dashboard
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Photo Modal -->
    <div id="photoModal" class="photo-modal" style="display: none;">
        <div class="modal-overlay" onclick="closePhotoModal()"></div>
        <div class="modal-content">
            <button class="modal-close" onclick="closePhotoModal()">×</button>
            <img id="modalImage" src="" alt="">
            <div class="modal-caption" id="modalCaption"></div>
        </div>
    </div>

    <style>
        /* Gallery Header */
        .gallery-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .back-btn, .search-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            font-size: 24px;
            font-weight: bold;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            cursor: pointer;
        }

        .back-btn:hover, .search-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }

        /* Gallery Content */
        .gallery-content {
            background: white;
            margin: -30px 30px 30px;
            padding: 0;
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            min-height: 500px;
            overflow: hidden;
        }

        .stores-gallery {
            max-width: 600px;
            margin: 0 auto;
        }

        /* Store Gallery Section */
        .store-gallery-section {
            background: white;
            border-bottom: 8px solid #f0f2f5;
            padding: 20px;
        }

        .store-gallery-section:last-child {
            border-bottom: none;
        }

        /* Store Header */
        .store-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
            cursor: pointer;
        }

        .store-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .store-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            font-weight: bold;
            border: 3px solid;
        }

        .store-avatar.platinum { 
            background: linear-gradient(135deg, #9B59B6, #8E44AD); 
            border-color: #7D3C98;
        }
        .store-avatar.gold { 
            background: linear-gradient(135deg, #FFD700, #FFA500); 
            border-color: #E67E22;
            color: #333;
        }
        .store-avatar.silver { 
            background: linear-gradient(135deg, #C0C0C0, #A8A8A8); 
            border-color: #95A5A6;
            color: #333;
        }
        .store-avatar.bronze { 
            background: linear-gradient(135deg, #CD7F32, #B87333); 
            border-color: #A0522D;
        }
        .store-avatar.standard { 
            background: linear-gradient(135deg, #2E8B57, #3CB371); 
            border-color: #228B22;
        }

        .store-info {
            flex: 1;
        }

        .store-name {
            margin: 0 0 4px 0;
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .store-location {
            margin: 0 0 8px 0;
            font-size: 13px;
            color: #666;
        }

        .store-rank-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .rank-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            color: white;
        }

        .rank-badge.platinum { background: linear-gradient(135deg, #9B59B6, #8E44AD); }
        .rank-badge.gold { background: linear-gradient(135deg, #FFD700, #FFA500); color: #333; }
        .rank-badge.silver { background: linear-gradient(135deg, #C0C0C0, #A8A8A8); color: #333; }
        .rank-badge.bronze { background: linear-gradient(135deg, #CD7F32, #B87333); }
        .rank-badge.standard { background: linear-gradient(135deg, #2E8B57, #3CB371); }

        .post-count {
            font-size: 12px;
            color: #999;
        }

        /* Store Description */
        .store-description {
            margin-bottom: 15px;
        }

        .store-description p {
            margin: 0;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
        }

        /* Photo Gallery Layouts */
        .photo-gallery {
            margin-bottom: 15px;
            border-radius: 12px;
            overflow: hidden;
        }

        .photo-gallery img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.3s ease;
            background: #f0f2f5;
            border-radius: 4px;
        }

        .photo-gallery img:hover {
            transform: scale(1.02);
        }

        /* Image loading state */
        .photo-gallery img[src=""] {
            background: linear-gradient(45deg, #f0f2f5 25%, transparent 25%), 
                        linear-gradient(-45deg, #f0f2f5 25%, transparent 25%), 
                        linear-gradient(45deg, transparent 75%, #f0f2f5 75%), 
                        linear-gradient(-45deg, transparent 75%, #f0f2f5 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
            animation: loading 1s linear infinite;
        }

        @keyframes loading {
            0% { background-position: 0 0, 0 10px, 10px -10px, -10px 0px; }
            100% { background-position: 20px 20px, 20px 30px, 30px 10px, 10px 20px; }
        }

        /* Single Photo */
        .single-photo {
            height: 400px;
        }

        /* Two Photos */
        .two-photos {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3px;
            height: 300px;
        }

        /* Three Photos */
        .three-photos {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 3px;
            height: 300px;
        }

        .three-photos .side-photos {
            display: grid;
            grid-template-rows: 1fr 1fr;
            gap: 3px;
        }

        /* Multiple Photos (Facebook style) */
        .multiple-photos {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 3px;
            height: 400px;
        }

        .multiple-photos .grid-photos {
            display: grid;
            grid-template-rows: 1fr 1fr 1fr;
            gap: 3px;
        }

        .grid-photo {
            position: relative;
            overflow: hidden;
        }

        .grid-photo.more-photos {
            position: relative;
        }

        .photo-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        /* Store Actions */
        .store-actions {
            display: flex;
            justify-content: space-around;
            padding: 12px 0;
            border-top: 1px solid #e9ecef;
            gap: 8px;
        }

        .action-btn {
            background: none;
            border: none;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            color: #666;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            background: #f0f2f5;
            transform: translateY(-1px);
        }

        .location-btn:hover { color: #2E8B57; }
        .share-btn:hover { color: #45b7d1; }
        .visit-btn:hover { color: #9B59B6; }

        /* Photo Modal */
        .photo-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        }

        .modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .modal-content {
            position: relative;
            max-width: 90%;
            max-height: 90%;
        }

        .modal-content img {
            max-width: 100%;
            max-height: 80vh;
            object-fit: contain;
        }

        .modal-close {
            position: absolute;
            top: -40px;
            right: 0;
            background: none;
            border: none;
            color: white;
            font-size: 30px;
            cursor: pointer;
        }

        .modal-caption {
            color: white;
            text-align: center;
            padding: 10px;
            font-size: 16px;
        }

        /* No Posts */
        .no-posts {
            text-align: center;
            padding: 80px 20px;
            color: #666;
        }

        .no-posts-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .no-posts h3 {
            margin: 0 0 12px 0;
            font-size: 24px;
            color: #2E8B57;
            font-weight: 600;
        }

        .no-posts p {
            margin: 0;
            font-size: 16px;
            line-height: 1.5;
            color: #666;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .gallery-content {
                margin: -30px 20px 20px;
            }
            
            .store-gallery-section {
                padding: 15px;
            }
            
            .back-btn, .search-btn {
                width: 45px;
                height: 45px;
                font-size: 18px;
            }
            
            .app-title {
                font-size: 28px;
            }

            .multiple-photos, .three-photos {
                height: 250px;
            }

            .single-photo {
                height: 250px;
            }

            .two-photos {
                height: 200px;
            }
        }

        @media (max-width: 480px) {
            .gallery-content {
                margin: -30px 15px 15px;
            }
            
            .store-gallery-section {
                padding: 12px;
            }
            
            .app-title {
                font-size: 24px;
            }
            
            .back-btn, .search-btn {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }

            .store-actions {
                flex-wrap: wrap;
                gap: 8px;
            }

            .action-btn {
                font-size: 12px;
                padding: 6px 10px;
            }
        }
    </style>

    <script>
        function viewStore(storeId) {
            window.location.href = `/seller/${storeId}`;
        }

        function openLocation(address) {
            const googleMapsUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(address)}`;
            window.open(googleMapsUrl, '_blank');
        }

        function shareStore(storeId) {
            if (navigator.share) {
                navigator.share({
                    title: 'Check out this store!',
                    url: window.location.href
                });
            } else {
                navigator.clipboard.writeText(window.location.href);
                alert('Link copied to clipboard!');
            }
        }

        function openPhotoModal(imageUrl, storeName) {
            document.getElementById('modalImage').src = imageUrl;
            document.getElementById('modalCaption').textContent = storeName;
            document.getElementById('photoModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closePhotoModal() {
            document.getElementById('photoModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function toggleSearch() {
            // Implement search functionality
            alert('Search functionality coming soon!');
        }

        // Handle image loading errors gracefully
        function handleImageError(img, storeName) {
            const initial = storeName.charAt(0).toUpperCase();
            const colors = ['FF6B6B', '4ECDC4', '45B7D1', 'FFA726', '66BB6A', 'AB47BC'];
            const color = colors[storeName.length % colors.length];
            img.src = `https://via.placeholder.com/800x600/${color}/FFFFFF?text=${encodeURIComponent(initial)}`;
        }

        // Add loading states for images
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('.photo-gallery img');
            images.forEach(img => {
                img.addEventListener('load', function() {
                    this.style.opacity = '1';
                });
                img.addEventListener('error', function() {
                    console.log('Image failed to load:', this.src);
                });
            });
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePhotoModal();
            }
        });
    </script>
@endsection