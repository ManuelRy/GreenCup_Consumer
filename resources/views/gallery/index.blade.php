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
                <div class="app-title">Store Feed</div>
                <button class="search-btn" onclick="toggleSearch()">
                    🔍
                </button>
            </div>
        </div>

        <!-- Search Bar (Hidden by default) -->
        <div id="searchBar" class="search-container" style="display: none;">
            <input type="text" id="searchInput" placeholder="Search stores, products, or locations..." class="search-input">
            <div id="searchResults" class="search-results"></div>
        </div>

        <!-- Feed Content -->
        <div class="feed-content">
            @php
                // Use posts if available, otherwise empty collection
                $feedPosts = $posts ?? collect();
            @endphp
            
            @if($feedPosts && $feedPosts->count() > 0)
                <div class="posts-feed">
                    @foreach($feedPosts as $post)
                    <div class="post-card">
                        <!-- Post Header -->
                        <div class="post-header">
                            <div class="seller-profile" onclick="viewStore({{ $post->seller_id }})">
                                <div class="seller-avatar {{ $post->rank_class }}">
                                    {{ substr($post->business_name, 0, 1) }}
                                </div>
                                <div class="seller-info">
                                    <h3 class="seller-name">{{ $post->business_name }}</h3>
                                    <p class="post-time">{{ $post->time_ago }}</p>
                                </div>
                            </div>
                            <div class="post-options">
                                <button class="options-btn">⋯</button>
                            </div>
                        </div>

                        <!-- Post Caption (if any) -->
                        @if($post->caption)
                            <div class="post-caption">
                                <p>{{ $post->caption }}</p>
                            </div>
                        @endif

                        <!-- Post Image -->
                        <div class="post-image" onclick="openPhotoModal('{{ $post->photo_url }}', '{{ addslashes($post->caption ?: $post->business_name) }}', {{ $post->id }})">
                            <img src="{{ $post->photo_url }}" 
                                 alt="{{ $post->caption ?: $post->business_name }}" 
                                 onerror="handleImageError(this, '{{ $post->business_name }}')"
                                 loading="lazy">
                            @if($post->is_featured)
                                <div class="featured-badge">⭐ Featured</div>
                            @endif
                        </div>

                        <!-- Post Stats -->
                        <div class="post-stats">
                            <div class="stats-row">
                                <span class="stat-item">
                                    <span class="rank-badge-mini {{ $post->rank_class }}">{{ $post->rank_icon }}</span>
                                    {{ $post->rank_text }} Seller
                                </span>
                                <span class="stat-item">{{ $post->total_points }} points</span>
                            </div>
                        </div>

                        <!-- Post Actions -->
                        <div class="post-actions">
                            <button class="action-btn" onclick="openLocation('{{ addslashes($post->address) }}')">
                                <span>📍</span>
                                <span>Location</span>
                            </button>
                            <button class="action-btn" onclick="sharePost({{ $post->id }}, '{{ addslashes($post->business_name) }}')">
                                <span>📤</span>
                                <span>Share</span>
                            </button>
                            <button class="action-btn" onclick="viewStore({{ $post->seller_id }})">
                                <span>🏪</span>
                                <span>Visit Store</span>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Load More Button -->
                @if(isset($hasMore) && $hasMore)
                <div class="load-more-container">
                    <button class="load-more-btn" onclick="loadMorePosts()">
                        Load More Posts
                    </button>
                </div>
                @endif
            @else
                <!-- No Posts Found -->
                <div class="no-posts">
                    <div class="no-posts-icon">📱</div>
                    <h3>No posts yet</h3>
                    <p>Be the first to share your store photos!</p>
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
            <div class="modal-inner">
                <img id="modalImage" src="" alt="" loading="lazy">
                <div class="modal-info">
                    <div class="modal-header">
                        <div id="modalSellerInfo" class="modal-seller-info"></div>
                    </div>
                    <div class="modal-caption" id="modalCaption"></div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Feed Styles - Facebook Style */
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
            transform: translateY(-2px);
        }

        /* Search Container */
        .search-container {
            background: white;
            margin: -10px 30px 20px;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }

        .search-input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .search-input:focus {
            border-color: #2E8B57;
        }

        /* Feed Content */
        .feed-content {
            background: #f0f2f5;
            margin: -30px 30px 30px;
            padding: 20px 0;
            border-radius: 25px;
            min-height: 500px;
        }

        .posts-feed {
            max-width: 680px;
            margin: 0 auto;
        }

        /* Post Card - Facebook Style */
        .post-card {
            background: white;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .post-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
        }

        .seller-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
        }

        .seller-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            font-weight: bold;
            border: 2px solid;
        }

        /* Rank Colors */
        .seller-avatar.platinum, .rank-badge-mini.platinum { 
            background: linear-gradient(135deg, #9B59B6, #8E44AD); 
            border-color: #7D3C98;
        }
        .seller-avatar.gold, .rank-badge-mini.gold { 
            background: linear-gradient(135deg, #FFD700, #FFA500); 
            border-color: #E67E22;
            color: #333;
        }
        .seller-avatar.silver, .rank-badge-mini.silver { 
            background: linear-gradient(135deg, #C0C0C0, #A8A8A8); 
            border-color: #95A5A6;
            color: #333;
        }
        .seller-avatar.bronze, .rank-badge-mini.bronze { 
            background: linear-gradient(135deg, #CD7F32, #B87333); 
            border-color: #A0522D;
        }
        .seller-avatar.standard, .rank-badge-mini.standard { 
            background: linear-gradient(135deg, #2E8B57, #3CB371); 
            border-color: #228B22;
        }

        .seller-info {
            flex: 1;
        }

        .seller-name {
            margin: 0;
            font-size: 15px;
            font-weight: 600;
            color: #1c1e21;
        }

        .post-time {
            margin: 0;
            font-size: 13px;
            color: #65676b;
        }

        .options-btn {
            background: none;
            border: none;
            font-size: 20px;
            color: #65676b;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: background 0.2s;
        }

        .options-btn:hover {
            background: #f0f2f5;
        }

        /* Post Caption */
        .post-caption {
            padding: 0 16px 12px;
        }

        .post-caption p {
            margin: 0;
            font-size: 15px;
            line-height: 1.4;
            color: #1c1e21;
        }

        /* Post Image */
        .post-image {
            position: relative;
            cursor: pointer;
            background: #000;
            line-height: 0;
        }

        .post-image img {
            width: 100%;
            height: auto;
            max-height: 600px;
            object-fit: contain;
        }

        .featured-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: linear-gradient(135deg, #FFD700, #FFA500);
            color: #333;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        /* Post Stats */
        .post-stats {
            padding: 12px 16px;
            border-top: 1px solid #ced0d4;
            border-bottom: 1px solid #ced0d4;
        }

        .stats-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            color: #65676b;
        }

        .rank-badge-mini {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            font-size: 10px;
        }

        /* Post Actions */
        .post-actions {
            display: flex;
            padding: 8px 16px;
        }

        .post-actions .action-btn {
            flex: 1;
            background: none;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #65676b;
            cursor: pointer;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .post-actions .action-btn:hover {
            background: #f0f2f5;
        }

        /* Load More */
        .load-more-container {
            text-align: center;
            padding: 20px;
        }

        .load-more-btn {
            background: white;
            border: 1px solid #dadde1;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            color: #1877f2;
            cursor: pointer;
            transition: all 0.2s;
        }

        .load-more-btn:hover {
            background: #f0f2f5;
        }

        /* Enhanced Photo Modal */
        .photo-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.95);
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
            max-width: 95%;
            max-height: 95%;
            display: flex;
            background: #000;
        }

        .modal-inner {
            display: flex;
            background: #000;
            max-height: 90vh;
        }

        .modal-inner img {
            max-width: 70vw;
            max-height: 90vh;
            object-fit: contain;
        }

        .modal-info {
            background: #242526;
            width: 360px;
            display: flex;
            flex-direction: column;
            color: #e4e6eb;
        }

        .modal-header {
            padding: 16px;
            border-bottom: 1px solid #3e4042;
        }

        .modal-seller-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modal-close {
            position: absolute;
            top: 16px;
            right: 16px;
            background: rgba(255,255,255,0.1);
            border: none;
            color: white;
            font-size: 24px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }

        .modal-close:hover {
            background: rgba(255,255,255,0.2);
        }

        .modal-caption {
            padding: 16px;
            font-size: 15px;
            line-height: 1.4;
        }

        /* No Posts */
        .no-posts {
            text-align: center;
            padding: 80px 20px;
            background: white;
            margin: 20px;
            border-radius: 12px;
        }

        .no-posts-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .feed-content {
                margin: -30px 15px 15px;
                padding: 15px 0;
            }
            
            .posts-feed {
                max-width: 100%;
            }
            
            .modal-inner {
                flex-direction: column;
            }
            
            .modal-inner img {
                max-width: 100vw;
                max-height: 60vh;
            }
            
            .modal-info {
                width: 100%;
                max-height: 30vh;
                overflow-y: auto;
            }
        }

        @media (max-width: 480px) {
            .feed-content {
                margin: -30px 10px 10px;
            }
            
            .post-card {
                border-radius: 0;
                margin-bottom: 8px;
            }
            
            .app-title {
                font-size: 24px;
            }
        }
    </style>

    <script>
        let currentPage = 1;
        let loading = false;

        // View store profile
        function viewStore(sellerId) {
            window.location.href = `/seller/${sellerId}`;
        }

        // Open location in maps
        function openLocation(address) {
            const googleMapsUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(address)}`;
            window.open(googleMapsUrl, '_blank');
        }

        // Share post
        function sharePost(postId, storeName) {
            const url = `${window.location.origin}/gallery/post/${postId}`;
            if (navigator.share) {
                navigator.share({
                    title: `Check out this post from ${storeName}!`,
                    url: url
                });
            } else {
                navigator.clipboard.writeText(url).then(() => {
                    alert(`Link copied to clipboard!`);
                }).catch(() => {
                    prompt('Copy this link:', url);
                });
            }
        }

        // Open photo modal with seller info
        function openPhotoModal(imageUrl, caption, postId) {
            document.getElementById('modalImage').src = imageUrl;
            document.getElementById('modalCaption').textContent = caption;
            
            // Get seller info for this post
            const postCard = document.querySelector(`[onclick*="openPhotoModal"][onclick*="${postId}"]`).closest('.post-card');
            const sellerAvatar = postCard.querySelector('.seller-avatar').cloneNode(true);
            const sellerName = postCard.querySelector('.seller-name').textContent;
            const postTime = postCard.querySelector('.post-time').textContent;
            
            // Update modal seller info
            const modalSellerInfo = document.getElementById('modalSellerInfo');
            modalSellerInfo.innerHTML = `
                ${sellerAvatar.outerHTML}
                <div>
                    <div style="font-weight: 600; font-size: 15px;">${sellerName}</div>
                    <div style="font-size: 13px; color: #b0b3b8;">${postTime}</div>
                </div>
            `;
            
            document.getElementById('photoModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        // Close photo modal
        function closePhotoModal() {
            document.getElementById('photoModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Toggle search
        function toggleSearch() {
            const searchBar = document.getElementById('searchBar');
            if (searchBar.style.display === 'none') {
                searchBar.style.display = 'block';
                document.getElementById('searchInput').focus();
            } else {
                searchBar.style.display = 'none';
            }
        }

        // Load more posts
        function loadMorePosts() {
            if (loading) return;
            loading = true;
            
            const btn = document.querySelector('.load-more-btn');
            btn.textContent = 'Loading...';
            btn.disabled = true;
            
            fetch(`/gallery/feed?page=${currentPage + 1}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.posts.length > 0) {
                        currentPage++;
                        appendPosts(data.posts);
                        
                        if (!data.hasMore) {
                            btn.style.display = 'none';
                        } else {
                            btn.textContent = 'Load More Posts';
                            btn.disabled = false;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading posts:', error);
                    btn.textContent = 'Load More Posts';
                    btn.disabled = false;
                })
                .finally(() => {
                    loading = false;
                });
        }

        // Append new posts to feed
        function appendPosts(posts) {
            const feed = document.querySelector('.posts-feed');
            posts.forEach(post => {
                const postHtml = createPostHtml(post);
                feed.insertAdjacentHTML('beforeend', postHtml);
            });
        }

        // Create post HTML
        function createPostHtml(post) {
            const featuredBadge = post.is_featured ? '<div class="featured-badge">⭐ Featured</div>' : '';
            const caption = post.caption ? `<div class="post-caption"><p>${post.caption}</p></div>` : '';
            
            return `
                <div class="post-card">
                    <div class="post-header">
                        <div class="seller-profile" onclick="viewStore(${post.seller_id})">
                            <div class="seller-avatar ${post.rank_class}">
                                ${post.business_name.charAt(0)}
                            </div>
                            <div class="seller-info">
                                <h3 class="seller-name">${post.business_name}</h3>
                                <p class="post-time">${post.time_ago}</p>
                            </div>
                        </div>
                        <div class="post-options">
                            <button class="options-btn">⋯</button>
                        </div>
                    </div>
                    ${caption}
                    <div class="post-image" onclick="openPhotoModal('${post.photo_url}', '${post.caption || post.business_name}', ${post.id})">
                        <img src="${post.photo_url}" alt="${post.caption || post.business_name}" onerror="handleImageError(this, '${post.business_name}')" loading="lazy">
                        ${featuredBadge}
                    </div>
                    <div class="post-stats">
                        <div class="stats-row">
                            <span class="stat-item">
                                <span class="rank-badge-mini ${post.rank_class}">${post.rank_icon}</span>
                                ${post.rank_text} Seller
                            </span>
                            <span class="stat-item">${post.total_points} points</span>
                        </div>
                    </div>
                    <div class="post-actions">
                        <button class="action-btn" onclick="openLocation('${post.address}')">
                            <span>📍</span>
                            <span>Location</span>
                        </button>
                        <button class="action-btn" onclick="sharePost(${post.id}, '${post.business_name}')">
                            <span>📤</span>
                            <span>Share</span>
                        </button>
                        <button class="action-btn" onclick="viewStore(${post.seller_id})">
                            <span>🏪</span>
                            <span>Visit Store</span>
                        </button>
                    </div>
                </div>
            `;
        }

        // Real-time search
        let searchTimeout;
        document.getElementById('searchInput')?.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                document.getElementById('searchResults').innerHTML = '';
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetch('/gallery/search?q=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            displaySearchResults(data.sellers);
                        }
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                    });
            }, 300);
        });

        // Display search results
        function displaySearchResults(sellers) {
            const resultsContainer = document.getElementById('searchResults');
            if (sellers.length === 0) {
                resultsContainer.innerHTML = '<div style="padding: 20px; text-align: center; color: #666;">No stores found</div>';
                return;
            }
            
            const html = sellers.map(seller => `
                <div onclick="viewStore(${seller.id})" style="padding: 15px; border-bottom: 1px solid #f0f2f5; cursor: pointer; display: flex; align-items: center; gap: 12px;">
                    <div class="seller-avatar ${seller.rank_class}" style="width: 40px; height: 40px; font-size: 16px;">
                        ${seller.business_name.charAt(0)}
                    </div>
                    <div>
                        <div style="font-weight: 600; color: #333;">${seller.business_name}</div>
                        <div style="font-size: 12px; color: #666;">${seller.address}</div>
                    </div>
                </div>
            `).join('');
            
            resultsContainer.innerHTML = html;
        }

        // Handle image errors
        function handleImageError(img, storeName) {
            const initial = storeName.charAt(0).toUpperCase();
            const colors = ['2E8B57', '3CB371', '228B22', '32CD32', '98FB98'];
            const color = colors[storeName.length % colors.length];
            img.src = `https://via.placeholder.com/800x600/${color}/FFFFFF?text=${encodeURIComponent(initial)}`;
        }

        // Close modal with Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePhotoModal();
            }
        });

        // Auto-hide search when clicking outside
        document.addEventListener('click', function(e) {
            const searchContainer = document.getElementById('searchBar');
            const searchBtn = document.querySelector('.search-btn');
            
            if (!searchContainer?.contains(e.target) && !searchBtn?.contains(e.target)) {
                if (searchContainer) {
                    searchContainer.style.display = 'none';
                }
            }
        });

        // Loading animation for images
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('.post-image img');
            images.forEach(img => {
                img.style.opacity = '0';
                img.addEventListener('load', function() {
                    this.style.transition = 'opacity 0.3s ease';
                    this.style.opacity = '1';
                });
            });
        });
    </script>
@endsection