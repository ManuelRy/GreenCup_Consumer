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
                <button class="search-btn">
                    🔍
                </button>
            </div>
        </div>

        <!-- Gallery Content -->
        <div class="gallery-content">
            @if($stores && count($stores) > 0)
                <div class="gallery-grid">
                    @foreach($stores as $store)
                    <div class="gallery-item" onclick="viewStore({{ $store->id }})">
                        <div class="image-container">
                            <img src="{{ $store->photo_url }}" alt="{{ $store->business_name }}" class="store-image">
                            
                            <!-- Rank Badge -->
                            <div class="rank-badge {{ $store->rank_class }}">
                                <span class="rank-icon">{{ $store->rank_icon }}</span>
                            </div>
                            
                            <!-- Store Info Overlay -->
                            <div class="store-overlay">
                                <h3 class="store-name">{{ $store->business_name }}</h3>
                                <p class="store-desc">{{ Str::limit($store->description, 50) }}</p>
                                
                                <!-- Location Link -->
                                <div class="store-location">
                                    <button onclick="openLocation(event, '{{ $store->address }}')" class="location-btn">
                                        <span class="location-icon">📍</span>
                                        <span class="location-text">View on Map</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="no-posts">
                    <div class="no-posts-icon">📷</div>
                    <h3>No posts yet</h3>
                    <p>Stores haven't posted any photos yet. Check back later!</p>
                </div>
            @endif
        </div>
    </div>

    <style>
        /* Gallery-specific styles that match Green Cup theme */
        .gallery-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .back-btn {
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
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }

        .search-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            color: white;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .search-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        /* Gallery Content - styled like points card */
        .gallery-content {
            background: white;
            margin: -30px 30px 30px;
            padding: 35px;
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            min-height: 500px;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
        }

        .gallery-item {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .gallery-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.18);
        }

        .image-container {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
        }

        .store-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .gallery-item:hover .store-image {
            transform: scale(1.05);
        }

        /* Rank Badge - Green Cup colors */
        .rank-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid white;
            font-size: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        }

        .rank-badge.platinum { background: linear-gradient(135deg, #9B59B6, #8E44AD); }
        .rank-badge.gold { background: linear-gradient(135deg, #FFD700, #FFA500); }
        .rank-badge.silver { background: linear-gradient(135deg, #C0C0C0, #A8A8A8); }
        .rank-badge.bronze { background: linear-gradient(135deg, #CD7F32, #B87333); }
        .rank-badge.standard { background: linear-gradient(135deg, #2E8B57, #3CB371); }

        /* Store Overlay */
        .store-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(46, 139, 87, 0.95));
            color: white;
            padding: 20px;
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .gallery-item:hover .store-overlay {
            transform: translateY(0);
            opacity: 1;
        }

        .store-name {
            margin: 0 0 8px 0;
            font-size: 18px;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }

        .store-desc {
            margin: 0 0 12px 0;
            font-size: 14px;
            opacity: 0.9;
            line-height: 1.4;
        }

        /* Location Button - Green Cup style */
        .store-location {
            margin-top: 10px;
        }

        .location-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 8px 16px;
            color: white;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .location-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
        }

        .location-icon {
            font-size: 14px;
        }

        .location-text {
            font-weight: 600;
        }

        /* No Posts State */
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
                padding: 25px;
            }
            
            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 20px;
            }
            
            .back-btn, .search-btn {
                width: 45px;
                height: 45px;
                font-size: 18px;
            }
            
            .app-title {
                font-size: 28px;
            }
        }

        @media (max-width: 480px) {
            .gallery-content {
                margin: -30px 15px 15px;
                padding: 20px;
            }
            
            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 15px;
            }
            
            .image-container {
                height: 150px;
            }
            
            .app-title {
                font-size: 24px;
            }
            
            .back-btn, .search-btn {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }
            
            .store-overlay {
                position: static;
                transform: none;
                opacity: 1;
                background: linear-gradient(135deg, #2E8B57, #3CB371);
                padding: 15px;
            }
        }
    </style>

    <script>
        function viewStore(storeId) {
            // Redirect to store page when clicked
            window.location.href = `/seller/${storeId}`;
        }

        function openLocation(event, address) {
            // Prevent the viewStore function from being called
            event.stopPropagation();
            
            // Open Google Maps with the store address
            const googleMapsUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(address)}`;
            window.open(googleMapsUrl, '_blank');
        }
    </script>
@endsection