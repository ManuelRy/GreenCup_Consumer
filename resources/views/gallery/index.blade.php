@extends('master')

@section('content')
<div class="sellers-gallery-container">
    <!-- Header -->
    <header class="gallery-header">
        <div class="header-content">
            <div class="header-left">
                <a href="{{ route('dashboard') }}" class="back-btn" aria-label="Back to Dashboard">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m15 18-6-6 6-6"/>
                    </svg>
                </a>
                <div class="header-info">
                    <h1 class="header-title">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <circle cx="9" cy="9" r="2"/>
                            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                        </svg>
                        <span class="title-text">Sellers Gallery Browser</span>
                    </h1>
                    <p class="header-subtitle">Discover stores and their amazing posts</p>
                </div>
            </div>
            <button class="mobile-toggle" onclick="toggleMobileView()" aria-label="Toggle view">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
        </div>
    </header>

    <!-- Main Content -->
    <main class="gallery-main">
        <!-- Sellers Panel (Left) -->
        <section class="sellers-panel" id="sellersPanel">
            <div class="panel-header">
                <h2 class="panel-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9,22 9,12 15,12 15,22"/>
                    </svg>
                    Stores Directory
                </h2>
                <div class="sellers-count">
                    <span id="sellersCount">0</span> stores
                </div>
            </div>

            <!-- Search & Filters -->
            <div class="panel-controls">
                <div class="search-wrapper">
                    <input type="text" id="sellerSearch" placeholder="Search stores..." class="search-input">
                    <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                </div>
                <select id="sellerFilter" class="filter-select">
                    <option value="all">All Ranks</option>
                    <option value="platinum">Platinum</option>
                    <option value="gold">Gold</option>
                    <option value="silver">Silver</option>
                    <option value="bronze">Bronze</option>
                    <option value="standard">Standard</option>
                </select>
            </div>

            <!-- Sellers List -->
            <div class="sellers-list" id="sellersList">
                <div class="loading-sellers">
                    <div class="loading-spinner"></div>
                    <p>Loading stores...</p>
                </div>
            </div>
        </section>

        <!-- Posts Panel (Right) -->
        <section class="posts-panel" id="postsPanel">
            <!-- Default State -->
            <div class="panel-default" id="defaultState">
                <div class="default-content">
                    <div class="default-icon">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <circle cx="9" cy="9" r="2"/>
                            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                        </svg>
                    </div>
                    <h3>Select a Store</h3>
                    <p>Choose a store from the left panel to view their photo gallery</p>
                </div>
            </div>

            <!-- Selected Seller Posts -->
            <div class="panel-content" id="selectedSellerContent" style="display: none;">
                <div class="panel-header">
                    <div class="selected-seller-info" id="selectedSellerInfo">
                        <!-- Dynamic content -->
                    </div>
                    <div class="posts-count">
                        <span id="postsCount">0</span> posts
                    </div>
                </div>

                <!-- Posts Grid -->
                <div class="posts-grid" id="postsGrid">
                    <!-- Dynamic content -->
                </div>

                <!-- Load More -->
                <div class="load-more-wrapper" id="loadMoreWrapper" style="display: none;">
                    <button class="load-more-btn" onclick="loadMorePosts()">
                        <span class="load-text">Load More Posts</span>
                        <div class="load-spinner" style="display: none;">
                            <div class="spinner"></div>
                        </div>
                    </button>
                </div>
            </div>
        </section>
    </main>

    <!-- Post Detail Modal -->
    <div id="postDetailModal" class="post-modal" style="display: none;" role="dialog" aria-modal="true">
        <div class="modal-overlay" onclick="closePostModal()"></div>
        <div class="modal-content">
            <button class="modal-close" onclick="closePostModal()" aria-label="Close">×</button>
            
            <div class="modal-body">
                <div class="modal-image-section">
                    <img id="modalPostImage" src="" alt="" loading="lazy">
                </div>
                
                <div class="modal-info-section">
                    <div class="modal-header-info">
                        <div id="modalSellerProfile" class="modal-seller-profile">
                            <!-- Dynamic seller info -->
                        </div>
                        <time id="modalPostDate" class="modal-post-date"></time>
                    </div>
                    
                    <div class="modal-post-content">
                        <h3 id="modalPostTitle" class="modal-post-title"></h3>
                        <p id="modalPostCaption" class="modal-post-caption"></p>
                    </div>
                    
                    <div class="modal-post-stats">
                        <div class="stat-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <span id="modalSellerLocation">Store Location</span>
                        </div>
                        <div class="stat-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
                            </svg>
                            <span id="modalSellerRank">Store Rank</span>
                        </div>
                    </div>
                    
                    <div class="modal-actions">
                        <button class="action-btn primary" onclick="visitStore()">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9,22 9,12 15,12 15,22"/>
                            </svg>
                            Visit Store
                        </button>
                        <button class="action-btn secondary" onclick="sharePost()">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/>
                                <polyline points="16,6 12,2 8,6"/>
                                <line x1="12" y1="2" x2="12" y2="15"/>
                            </svg>
                            Share
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* CSS Variables with Responsive Scaling */
:root {
    --primary-color: #1dd1a1;
    --primary-dark: #10ac84;
    --secondary-color: #2e8b57;
    --background-color: #1dd1a1;
    --panel-bg: #ffffff;
    --card-bg: #ffffff;
    --text-primary: #2c3e50;
    --text-secondary: #7f8c8d;
    --text-muted: #95a5a6;
    --border-color: #e8eaed;
    --hover-bg: #f8f9fa;
    --shadow-light: 0 2px 4px rgba(0,0,0,0.08);
    --shadow-medium: 0 4px 12px rgba(0,0,0,0.12);
    --shadow-heavy: 0 8px 24px rgba(0,0,0,0.16);
    --border-radius: 12px;
    --border-radius-lg: 16px;
    
    /* Responsive spacing - scales with viewport */
    --spacing-xs: clamp(2px, 0.5vw, 4px);
    --spacing-sm: clamp(4px, 1vw, 8px);
    --spacing-md: clamp(8px, 1.5vw, 12px);
    --spacing-lg: clamp(12px, 2vw, 16px);
    --spacing-xl: clamp(16px, 3vw, 24px);
    --spacing-xxl: clamp(24px, 4vw, 32px);
    
    /* Responsive font sizes */
    --font-xs: clamp(10px, 2vw, 12px);
    --font-sm: clamp(12px, 2.5vw, 14px);
    --font-base: clamp(14px, 3vw, 16px);
    --font-lg: clamp(16px, 3.5vw, 18px);
    --font-xl: clamp(18px, 4vw, 20px);
    --font-xxl: clamp(20px, 5vw, 24px);
    
    /* Responsive widths */
    --sidebar-width: clamp(280px, 35vw, 400px);
    --modal-width: clamp(320px, 90vw, 900px);
    --modal-height: clamp(400px, 80vh, 600px);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
    background: var(--background-color);
    color: var(--text-primary);
    line-height: 1.6;
    font-size: var(--font-base);
}

/* Main Container */
.sellers-gallery-container {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* Header - Fully Responsive */
.gallery-header {
    background: var(--background-color);
    padding: clamp(12px, 2vh, 16px) clamp(16px, 4vw, 24px);
    border-bottom: 1px solid rgba(255,255,255,0.2);
    position: sticky;
    top: 0;
    z-index: 100;
}

.header-content {
    max-width: 1400px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--spacing-md);
}

.header-left {
    display: flex;
    align-items: center;
    gap: var(--spacing-lg);
    flex: 1;
    min-width: 0; /* Allows text truncation */
}

.back-btn {
    background: rgba(255,255,255,0.15);
    border: none;
    border-radius: 50%;
    width: clamp(40px, 8vw, 44px);
    height: clamp(40px, 8vw, 44px);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    transition: all 0.2s ease;
    backdrop-filter: blur(10px);
    flex-shrink: 0;
}

.back-btn:hover {
    background: rgba(255,255,255,0.25);
    transform: translateY(-1px);
    color: white;
}

.header-info {
    color: white;
    flex: 1;
    min-width: 0;
}

.header-title {
    font-size: var(--font-xxl);
    font-weight: 700;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.header-title svg {
    flex-shrink: 0;
    width: clamp(20px, 5vw, 28px);
    height: clamp(20px, 5vw, 28px);
}

.title-text {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.header-subtitle {
    font-size: var(--font-sm);
    opacity: 0.9;
    margin: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.mobile-toggle {
    background: rgba(255,255,255,0.15);
    border: none;
    border-radius: var(--spacing-sm);
    width: clamp(40px, 8vw, 44px);
    height: clamp(40px, 8vw, 44px);
    display: none;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.mobile-toggle:hover {
    background: rgba(255,255,255,0.25);
}

/* Main Content - Responsive Grid */
.gallery-main {
    flex: 1;
    max-width: 1400px;
    margin: 0 auto;
    width: 100%;
    display: grid;
    grid-template-columns: var(--sidebar-width) 1fr;
    gap: var(--spacing-xl);
    padding: var(--spacing-xl);
    min-height: calc(100vh - 100px);
}

/* Panels */
.sellers-panel, .posts-panel {
    background: var(--panel-bg);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-medium);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Panel Headers - Responsive */
.panel-header {
    padding: var(--spacing-xl);
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(135deg, #f8f9fa, #ffffff);
    gap: var(--spacing-md);
    flex-wrap: wrap;
}

.panel-title {
    font-size: var(--font-lg);
    font-weight: 700;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    margin: 0;
    min-width: 0;
}

.sellers-count, .posts-count {
    background: var(--primary-color);
    color: white;
    padding: 4px var(--spacing-sm);
    border-radius: 12px;
    font-size: var(--font-xs);
    font-weight: 600;
    white-space: nowrap;
    flex-shrink: 0;
}

/* Panel Controls - Responsive */
.panel-controls {
    padding: var(--spacing-lg) var(--spacing-xl) 0;
    display: flex;
    gap: var(--spacing-md);
    flex-wrap: wrap;
}

.search-wrapper {
    position: relative;
    flex: 1;
    min-width: 200px;
}

.search-input {
    width: 100%;
    padding: var(--spacing-sm) var(--spacing-md) var(--spacing-sm) 36px;
    border: 2px solid var(--border-color);
    border-radius: 20px;
    font-size: var(--font-sm);
    outline: none;
    transition: all 0.2s ease;
    background: white;
}

.search-input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(29, 209, 161, 0.1);
}

.search-icon {
    position: absolute;
    left: var(--spacing-md);
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
}

.filter-select {
    padding: var(--spacing-sm) var(--spacing-md);
    border: 2px solid var(--border-color);
    border-radius: 20px;
    font-size: var(--font-sm);
    outline: none;
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: 120px;
}

.filter-select:focus {
    border-color: var(--primary-color);
}

/* Sellers List - Responsive */
.sellers-list {
    flex: 1;
    overflow-y: auto;
    padding: var(--spacing-lg) var(--spacing-xl) var(--spacing-xl);
}

.loading-sellers {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: var(--spacing-xxl);
    color: var(--text-muted);
}

.loading-spinner {
    width: clamp(24px, 5vw, 32px);
    height: clamp(24px, 5vw, 32px);
    border: 3px solid var(--border-color);
    border-top: 3px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: var(--spacing-md);
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Seller Card - Fully Responsive */
.seller-card {
    background: var(--card-bg);
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: var(--spacing-lg);
    margin-bottom: var(--spacing-md);
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
}

.seller-card:hover {
    border-color: var(--primary-color);
    box-shadow: var(--shadow-medium);
    transform: translateY(-2px);
}

.seller-card.selected {
    border-color: var(--primary-color);
    background: rgba(29, 209, 161, 0.05);
    box-shadow: var(--shadow-medium);
}

.seller-card-header {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-md);
}

.seller-avatar {
    width: clamp(40px, 8vw, 48px);
    height: clamp(40px, 8vw, 48px);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: clamp(14px, 3vw, 18px);
    font-weight: 700;
    border: 3px solid;
    position: relative;
    flex-shrink: 0;
}

/* Seller rank colors */
.seller-avatar.platinum { 
    background: linear-gradient(135deg, #9B59B6, #8E44AD); 
    border-color: #7D3C98;
}
.seller-avatar.gold { 
    background: linear-gradient(135deg, #FFD700, #FFA500); 
    border-color: #E67E22;
    color: #333;
}
.seller-avatar.silver { 
    background: linear-gradient(135deg, #C0C0C0, #A8A8A8); 
    border-color: #95A5A6;
    color: #333;
}
.seller-avatar.bronze { 
    background: linear-gradient(135deg, #CD7F32, #B87333); 
    border-color: #A0522D;
}
.seller-avatar.standard { 
    background: linear-gradient(135deg, #2E8B57, #3CB371); 
    border-color: #228B22;
}

.rank-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    width: clamp(16px, 3vw, 20px);
    height: clamp(16px, 3vw, 20px);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: clamp(8px, 1.5vw, 10px);
    border: 2px solid white;
    box-shadow: var(--shadow-light);
}

.rank-badge.platinum {
    background: linear-gradient(135deg, #9B59B6, #8E44AD);
    color: white;
}
.rank-badge.gold {
    background: linear-gradient(135deg, #FFD700, #FFA500);
    color: #333;
}
.rank-badge.silver {
    background: linear-gradient(135deg, #C0C0C0, #A8A8A8);
    color: #333;
}
.rank-badge.bronze {
    background: linear-gradient(135deg, #CD7F32, #B87333);
    color: white;
}
.rank-badge.standard {
    background: linear-gradient(135deg, #2E8B57, #3CB371);
    color: white;
}

.seller-info {
    flex: 1;
    min-width: 0;
}

.seller-name {
    font-size: var(--font-base);
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 2px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.seller-location {
    font-size: var(--font-sm);
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 4px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.seller-stats {
    display: flex;
    gap: var(--spacing-lg);
    margin-top: var(--spacing-sm);
    flex-wrap: wrap;
}

.stat {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: var(--font-xs);
    color: var(--text-muted);
}

.stat-value {
    font-weight: 600;
    color: var(--text-secondary);
}

/* Posts Panel Default State */
.panel-default {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--spacing-xl);
}

.default-content {
    text-align: center;
    color: var(--text-muted);
    max-width: 300px;
}

.default-icon {
    margin-bottom: var(--spacing-lg);
    opacity: 0.5;
}

.default-icon svg {
    width: clamp(48px, 10vw, 64px);
    height: clamp(48px, 10vw, 64px);
}

.default-content h3 {
    font-size: var(--font-xl);
    margin-bottom: var(--spacing-sm);
    color: var(--text-secondary);
}

.default-content p {
    font-size: var(--font-sm);
    line-height: 1.5;
}

/* Panel Content */
.panel-content {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.selected-seller-info {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    min-width: 0;
}

.selected-seller-info .seller-avatar {
    width: clamp(32px, 6vw, 40px);
    height: clamp(32px, 6vw, 40px);
    font-size: clamp(12px, 2.5vw, 16px);
}

.selected-seller-info .seller-info .seller-name {
    font-size: var(--font-sm);
}

.selected-seller-info .seller-info .seller-location {
    font-size: var(--font-xs);
}

/* Posts Grid - Fully Responsive */
.posts-grid {
    flex: 1;
    padding: var(--spacing-xl);
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(clamp(140px, 25vw, 200px), 1fr));
    gap: var(--spacing-lg);
    overflow-y: auto;
}

.post-thumbnail {
    background: var(--card-bg);
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    overflow: hidden;
    cursor: pointer;
    transition: all 0.2s ease;
    aspect-ratio: 1;
    display: flex;
    flex-direction: column;
}

.post-thumbnail:hover {
    border-color: var(--primary-color);
    box-shadow: var(--shadow-medium);
    transform: translateY(-2px);
}

.post-image-wrapper {
    flex: 1;
    position: relative;
    overflow: hidden;
    background: #f5f5f5;
}

.post-thumbnail-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.2s ease;
}

.post-thumbnail:hover .post-thumbnail-img {
    transform: scale(1.05);
}

.post-featured-badge {
    position: absolute;
    top: var(--spacing-sm);
    left: var(--spacing-sm);
    background: linear-gradient(135deg, #FFD700, #FFA500);
    color: #333;
    padding: 2px 6px;
    border-radius: 8px;
    font-size: var(--font-xs);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 2px;
}

.post-info {
    padding: var(--spacing-sm);
    background: white;
}

.post-title {
    font-size: var(--font-sm);
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 2px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.post-date {
    font-size: var(--font-xs);
    color: var(--text-muted);
}

/* Load More - Responsive */
.load-more-wrapper {
    padding: var(--spacing-xl);
    text-align: center;
}

.load-more-btn {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: var(--spacing-md) var(--spacing-xl);
    border-radius: 20px;
    font-size: var(--font-sm);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    margin: 0 auto;
}

.load-more-btn:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

.load-more-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.spinner {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255,255,255,0.3);
    border-top: 2px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

/* Post Detail Modal - Fully Responsive */
.post-modal {
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
    padding: var(--spacing-lg);
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
    background: white;
    border-radius: var(--border-radius-lg);
    max-width: var(--modal-width);
    max-height: 90vh;
    width: 100%;
    overflow: hidden;
    box-shadow: var(--shadow-heavy);
}

.modal-close {
    position: absolute;
    top: var(--spacing-lg);
    right: var(--spacing-lg);
    background: rgba(0,0,0,0.5);
    border: none;
    color: white;
    font-size: clamp(18px, 4vw, 24px);
    width: clamp(32px, 6vw, 40px);
    height: clamp(32px, 6vw, 40px);
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s ease;
    z-index: 10001;
}

.modal-close:hover {
    background: rgba(0,0,0,0.7);
}

.modal-body {
    display: grid;
    grid-template-columns: 1fr 350px;
    height: var(--modal-height);
}

.modal-image-section {
    background: #000;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.modal-image-section img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.modal-info-section {
    background: white;
    padding: var(--spacing-xl);
    display: flex;
    flex-direction: column;
    overflow-y: auto;
}

.modal-header-info {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: var(--spacing-lg);
    gap: var(--spacing-md);
    flex-wrap: wrap;
}

.modal-seller-profile {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    min-width: 0;
}

.modal-seller-profile .seller-avatar {
    width: clamp(36px, 7vw, 44px);
    height: clamp(36px, 7vw, 44px);
    font-size: clamp(14px, 3vw, 16px);
}

.modal-post-date {
    font-size: var(--font-xs);
    color: var(--text-muted);
    align-self: flex-start;
    white-space: nowrap;
}

.modal-post-content {
    flex: 1;
    margin-bottom: var(--spacing-lg);
}

.modal-post-title {
    font-size: var(--font-lg);
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: var(--spacing-sm);
    word-wrap: break-word;
}

.modal-post-caption {
    font-size: var(--font-sm);
    color: var(--text-secondary);
    line-height: 1.5;
    word-wrap: break-word;
}

.modal-post-stats {
    margin-bottom: var(--spacing-lg);
    padding-top: var(--spacing-lg);
    border-top: 1px solid var(--border-color);
}

.modal-post-stats .stat-item {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-sm);
    font-size: var(--font-sm);
    color: var(--text-secondary);
}

.modal-actions {
    display: flex;
    gap: var(--spacing-md);
    flex-wrap: wrap;
}

.action-btn {
    flex: 1;
    min-width: 120px;
    padding: var(--spacing-md);
    border: none;
    border-radius: var(--spacing-sm);
    font-size: var(--font-sm);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-sm);
}

.action-btn.primary {
    background: var(--primary-color);
    color: white;
}

.action-btn.primary:hover {
    background: var(--primary-dark);
}

.action-btn.secondary {
    background: var(--hover-bg);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
}

.action-btn.secondary:hover {
    background: var(--border-color);
}

/* Responsive Design Breakpoints */

/* Large Desktop */
@media (min-width: 1400px) {
    .gallery-main {
        grid-template-columns: 420px 1fr;
    }
}

/* Desktop */
@media (max-width: 1200px) {
    .gallery-main {
        grid-template-columns: 320px 1fr;
    }
    
    .modal-body {
        grid-template-columns: 1fr 280px;
    }
}

/* Tablet Landscape */
@media (max-width: 1024px) {
    .gallery-main {
        grid-template-columns: 300px 1fr;
        gap: var(--spacing-lg);
    }
    
    .posts-grid {
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: var(--spacing-md);
    }
    
    .modal-body {
        grid-template-columns: 1fr 260px;
        height: 70vh;
    }
}

/* Tablet Portrait & Mobile Landscape */
@media (max-width: 968px) {
    .gallery-main {
        grid-template-columns: 1fr;
        gap: 0;
        padding: var(--spacing-md);
    }
    
    .sellers-panel {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1000;
        border-radius: 0;
        max-width: none;
    }
    
    .sellers-panel.mobile-active {
        display: flex;
    }
    
    .mobile-toggle {
        display: flex;
    }
    
    .posts-panel {
        grid-column: 1;
        min-height: calc(100vh - 120px);
    }
    
    .posts-grid {
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: var(--spacing-sm);
        padding: var(--spacing-md);
    }
    
    .modal-body {
        grid-template-columns: 1fr;
        height: auto;
        max-height: 85vh;
    }
    
    .modal-image-section {
        max-height: 50vh;
    }
    
    .modal-info-section {
        padding: var(--spacing-lg);
    }
    
    .panel-header {
        flex-wrap: wrap;
        gap: var(--spacing-sm);
    }
    
    .panel-controls {
        flex-direction: column;
        gap: var(--spacing-sm);
    }
    
    .filter-select {
        width: 100%;
    }
}

/* Mobile Portrait */
@media (max-width: 640px) {
    .gallery-header {
        padding: var(--spacing-sm) var(--spacing-md);
    }
    
    .header-title {
        font-size: var(--font-lg);
    }
    
    .header-subtitle {
        display: none; /* Hide subtitle on very small screens */
    }
    
    .gallery-main {
        padding: var(--spacing-sm);
    }
    
    .posts-grid {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: var(--spacing-xs);
        padding: var(--spacing-sm);
    }
    
    .panel-header,
    .panel-controls,
    .sellers-list {
        padding-left: var(--spacing-md);
        padding-right: var(--spacing-md);
    }
    
    .post-modal {
        padding: var(--spacing-sm);
    }
    
    .modal-content {
        border-radius: var(--spacing-md);
    }
    
    .modal-info-section {
        padding: var(--spacing-md);
    }
    
    .modal-actions {
        flex-direction: column;
    }
    
    .action-btn {
        min-width: auto;
    }
    
    .seller-card {
        padding: var(--spacing-md);
    }
    
    .seller-stats {
        gap: var(--spacing-md);
    }
}

/* Extra Small Mobile */
@media (max-width: 480px) {
    .header-left {
        gap: var(--spacing-sm);
    }
    
    .posts-grid {
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    }
    
    .panel-header {
        padding: var(--spacing-md);
    }
    
    .panel-controls {
        padding: var(--spacing-md) var(--spacing-md) 0;
    }
    
    .sellers-list {
        padding: var(--spacing-md);
    }
    
    .modal-body {
        height: auto;
        max-height: 90vh;
    }
    
    .modal-image-section {
        max-height: 40vh;
    }
}

/* Very Small Screens */
@media (max-width: 360px) {
    .posts-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .header-title svg {
        display: none; /* Hide icon on very small screens */
    }
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* High DPI Displays */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
    .post-thumbnail-img,
    .modal-image-section img {
        image-rendering: -webkit-optimize-contrast;
        image-rendering: crisp-edges;
    }
}

/* Focus styles for accessibility */
.seller-card:focus,
.post-thumbnail:focus,
.back-btn:focus,
.mobile-toggle:focus,
.search-input:focus,
.filter-select:focus,
.load-more-btn:focus,
.action-btn:focus {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}

/* Loading states */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

.skeleton-seller {
    height: clamp(80px, 15vw, 100px);
    border-radius: var(--border-radius);
    margin-bottom: var(--spacing-md);
}

.skeleton-post {
    aspect-ratio: 1;
    border-radius: var(--border-radius);
}

/* Touch-friendly improvements */
@media (hover: none) and (pointer: coarse) {
    .seller-card,
    .post-thumbnail,
    .load-more-btn,
    .action-btn {
        min-height: 44px; /* Ensure minimum touch target size */
    }
    
    .back-btn,
    .mobile-toggle {
        min-width: 44px;
        min-height: 44px;
    }
}
</style>

<script>
// Application State
const app = {
    sellers: [],
    filteredSellers: [],
    selectedSeller: null,
    selectedSellerPosts: [],
    currentPostsPage: 1,
    hasMorePosts: false,
    isLoading: false,
    searchTimeout: null
};

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
    handleResize(); // Initial responsive setup
});

// Add resize listener for responsive adjustments
window.addEventListener('resize', handleResize);

function handleResize() {
    // Close mobile panel if screen becomes large
    if (window.innerWidth > 968) {
        closeMobilePanel();
    }
    
    // Adjust modal size based on viewport
    const modal = document.getElementById('postDetailModal');
    if (modal && modal.style.display !== 'none') {
        adjustModalSize();
    }
}

function adjustModalSize() {
    const modalBody = document.querySelector('.modal-body');
    if (window.innerWidth <= 968) {
        modalBody.style.gridTemplateColumns = '1fr';
        modalBody.style.height = 'auto';
        modalBody.style.maxHeight = '85vh';
    } else {
        modalBody.style.gridTemplateColumns = window.innerWidth <= 1200 ? '1fr 280px' : '1fr 350px';
        modalBody.style.height = '600px';
        modalBody.style.maxHeight = '90vh';
    }
}

async function initializeApp() {
    try {
        await loadSellers();
        initializeEventListeners();
        console.log('Sellers Gallery Browser initialized successfully');
    } catch (error) {
        console.error('Failed to initialize app:', error);
        showErrorState('Initialization failed');
    }
}

function initializeEventListeners() {
    // Search functionality
    const searchInput = document.getElementById('sellerSearch');
    searchInput.addEventListener('input', handleSearch);
    
    // Filter functionality
    const filterSelect = document.getElementById('sellerFilter');
    filterSelect.addEventListener('change', handleFilter);
    
    // Modal close with escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePostModal();
            closeMobilePanel();
        }
    });
    
    // Touch gesture support for mobile
    let touchStartX = 0;
    let touchStartY = 0;
    
    document.addEventListener('touchstart', function(e) {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
    });
    
    document.addEventListener('touchend', function(e) {
        if (!touchStartX || !touchStartY) return;
        
        const touchEndX = e.changedTouches[0].clientX;
        const touchEndY = e.changedTouches[0].clientY;
        
        const diffX = touchStartX - touchEndX;
        const diffY = touchStartY - touchEndY;
        
        // Only handle horizontal swipes
        if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
            const sellersPanel = document.getElementById('sellersPanel');
            
            // Swipe right to open panel (on mobile)
            if (diffX < 0 && window.innerWidth <= 968 && !sellersPanel.classList.contains('mobile-active')) {
                toggleMobileView();
            }
            // Swipe left to close panel
            else if (diffX > 0 && sellersPanel.classList.contains('mobile-active')) {
                closeMobilePanel();
            }
        }
        
        touchStartX = 0;
        touchStartY = 0;
    });
}

// REAL DATA LOADING - No Mock Fallback
async function loadSellers() {
    try {
        showSellersLoading(true);
        
        // Add timeout to prevent hanging
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout
        
        const response = await fetch('/public-api/stores', {
            signal: controller.signal
        });
        
        clearTimeout(timeoutId);
        
        if (!response.ok) {
            throw new Error(`API response: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            // Accept empty array as valid response (empty database)
            app.sellers = (data.data || []).map(seller => ({
                id: seller.id,
                name: seller.name,
                address: seller.address,
                phone: seller.phone,
                latitude: seller.latitude,
                longitude: seller.longitude,
                image: seller.image,
                total_points: seller.total_points || 0,
                points_reward: seller.points_reward || seller.total_points || 0,
                transaction_count: seller.transaction_count || 0,
                rank_class: seller.rank_class || getRankClass(seller.points_reward || seller.total_points || 0),
                rank_text: seller.rank_text || getRankText(seller.points_reward || seller.total_points || 0),
                rank_icon: seller.rank_icon || getRankIcon(seller.points_reward || seller.total_points || 0)
            }));
            
            app.filteredSellers = [...app.sellers];
            renderSellers();
            updateSellersCount();
            
            if (app.sellers.length === 0) {
                console.log('✅ Database is empty - no sellers found');
                showEmptyDatabase();
            } else {
                console.log(`✅ Loaded ${app.sellers.length} sellers from database`);
            }
        } else {
            throw new Error(data.message || 'API returned no data');
        }
    } catch (error) {
        console.error('Failed to load sellers:', error.message);
        
        // Show error state instead of mock data
        app.sellers = [];
        app.filteredSellers = [];
        showErrorState(error.message);
    } finally {
        showSellersLoading(false);
    }
}

function showSellersLoading(show) {
    const loadingElement = document.querySelector('.loading-sellers');
    const sellersList = document.getElementById('sellersList');
    
    if (show) {
        sellersList.innerHTML = '<div class="loading-sellers"><div class="loading-spinner"></div><p>Loading stores...</p></div>';
    }
}

function showEmptyDatabase() {
    const sellersList = document.getElementById('sellersList');
    sellersList.innerHTML = `
        <div style="text-align: center; padding: var(--spacing-xxl); color: var(--text-muted);">
            <div style="font-size: clamp(32px, 8vw, 48px); margin-bottom: var(--spacing-lg); opacity: 0.5;">🏪</div>
            <h3 style="margin-bottom: var(--spacing-sm); color: var(--text-secondary); font-size: var(--font-lg);">No Stores Yet</h3>
            <p style="font-size: var(--font-sm);">Your database is empty. Add some stores to see them here.</p>
            <button onclick="refreshData()" style="
                margin-top: var(--spacing-lg); 
                background: var(--primary-color); 
                color: white; 
                border: none; 
                padding: var(--spacing-sm) var(--spacing-lg); 
                border-radius: 20px; 
                cursor: pointer;
                font-size: var(--font-sm);
                transition: all 0.2s ease;
            " onmouseover="this.style.background='var(--primary-dark)'" onmouseout="this.style.background='var(--primary-color)'">Refresh</button>
        </div>
    `;
}

function showErrorState(errorMessage) {
    const sellersList = document.getElementById('sellersList');
    sellersList.innerHTML = `
        <div style="text-align: center; padding: var(--spacing-xxl); color: var(--text-muted);">
            <div style="font-size: clamp(32px, 8vw, 48px); margin-bottom: var(--spacing-lg); opacity: 0.5;">⚠️</div>
            <h3 style="margin-bottom: var(--spacing-sm); color: var(--text-secondary); font-size: var(--font-lg);">Connection Error</h3>
            <p style="font-size: var(--font-sm); margin-bottom: var(--spacing-sm);">Failed to load stores:</p>
            <p style="font-size: var(--font-xs); color: var(--text-muted); word-break: break-word;">${errorMessage}</p>
            <button onclick="refreshData()" style="
                margin-top: var(--spacing-lg); 
                background: var(--primary-color); 
                color: white; 
                border: none; 
                padding: var(--spacing-sm) var(--spacing-lg); 
                border-radius: 20px; 
                cursor: pointer;
                font-size: var(--font-sm);
                transition: all 0.2s ease;
            " onmouseover="this.style.background='var(--primary-dark)'" onmouseout="this.style.background='var(--primary-color)'">Try Again</button>
        </div>
    `;
}

function refreshData() {
    loadSellers();
}

function renderSellers() {
    const sellersList = document.getElementById('sellersList');
    
    if (app.filteredSellers.length === 0) {
        sellersList.innerHTML = `
            <div style="text-align: center; padding: var(--spacing-xxl); color: var(--text-muted);">
                <p style="font-size: var(--font-sm);">No stores found matching your criteria</p>
            </div>
        `;
        return;
    }
    
    // Use document fragment for better performance
    const fragment = document.createDocumentFragment();
    
    app.filteredSellers.forEach(seller => {
        const sellerCard = document.createElement('div');
        sellerCard.className = 'seller-card';
        sellerCard.setAttribute('data-seller-id', seller.id);
        sellerCard.onclick = () => selectSeller(seller.id);
        sellerCard.setAttribute('tabindex', '0');
        sellerCard.setAttribute('role', 'button');
        sellerCard.setAttribute('aria-label', `Select ${seller.name} store`);
        
        // Add keyboard support
        sellerCard.onkeydown = (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                selectSeller(seller.id);
            }
        };
        
        sellerCard.innerHTML = `
            <div class="seller-card-header">
                <div class="seller-avatar ${seller.rank_class}">
                    ${seller.name.charAt(0).toUpperCase()}
                    <div class="rank-badge ${seller.rank_class}">
                        ${seller.rank_icon}
                    </div>
                </div>
                <div class="seller-info">
                    <div class="seller-name" title="${seller.name}">
                        ${seller.name}
                    </div>
                    <div class="seller-location" title="${seller.address}">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        ${seller.address || 'Location not available'}
                    </div>
                </div>
            </div>
            <div class="seller-stats">
                <div class="stat">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
                    </svg>
                    <span class="stat-value">${seller.points_reward || seller.total_points}</span> points
                </div>
                <div class="stat">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span class="stat-value">${seller.transaction_count}</span> visits
                </div>
            </div>
        `;
        
        fragment.appendChild(sellerCard);
    });
    
    // Single DOM update instead of multiple innerHTML calls
    sellersList.innerHTML = '';
    sellersList.appendChild(fragment);
}

async function selectSeller(sellerId) {
    try {
        // Update UI selection
        document.querySelectorAll('.seller-card').forEach(card => {
            card.classList.remove('selected');
            card.setAttribute('aria-selected', 'false');
        });
        
        const selectedCard = document.querySelector(`[data-seller-id="${sellerId}"]`);
        if (selectedCard) {
            selectedCard.classList.add('selected');
            selectedCard.setAttribute('aria-selected', 'true');
        }
        
        // Find seller data
        app.selectedSeller = app.sellers.find(seller => seller.id === sellerId);
        if (!app.selectedSeller) {
            throw new Error('Seller not found');
        }
        
        // Show posts panel and hide default state
        document.getElementById('defaultState').style.display = 'none';
        document.getElementById('selectedSellerContent').style.display = 'flex';
        
        // Update selected seller info
        updateSelectedSellerInfo();
        
        // Load seller's posts
        await loadSellerPosts(sellerId);
        
        // Close mobile panel if open
        closeMobilePanel();
        
    } catch (error) {
        console.error('Error selecting seller:', error);
        showError('Failed to load seller posts');
    }
}

function updateSelectedSellerInfo() {
    const selectedSellerInfo = document.getElementById('selectedSellerInfo');
    selectedSellerInfo.innerHTML = `
        <div class="seller-avatar ${app.selectedSeller.rank_class}">
            ${app.selectedSeller.name.charAt(0).toUpperCase()}
            <div class="rank-badge ${app.selectedSeller.rank_class}">
                ${app.selectedSeller.rank_icon}
            </div>
        </div>
        <div class="seller-info">
            <div class="seller-name">${app.selectedSeller.name}</div>
            <div class="seller-location">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                ${app.selectedSeller.address || 'Location not available'}
            </div>
        </div>
    `;
}

// REAL POSTS LOADING - No Mock Fallback
async function loadSellerPosts(sellerId, page = 1) {
    try {
        if (page === 1) {
            app.selectedSellerPosts = [];
            app.currentPostsPage = 1;
            document.getElementById('postsGrid').innerHTML = '';
        }
        
        showPostsLoading(page === 1);
        
        // Only try real API, no mock fallback
        const response = await fetch(`/public-api/gallery/feed?seller_id=${sellerId}&page=${page}`);
        
        if (!response.ok) {
            throw new Error(`API response: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            const posts = (data.posts || []).map(post => ({
                id: post.id,
                photo_url: post.photo_url,
                caption: post.caption || '',
                business_name: post.business_name || app.selectedSeller.name,
                created_at: post.created_at,
                time_ago: post.time_ago || 'Recently',
                is_featured: post.is_featured || false,
                seller_id: post.seller_id || sellerId
            }));
            
            if (page === 1) {
                app.selectedSellerPosts = posts;
            } else {
                app.selectedSellerPosts.push(...posts);
            }
            
            app.hasMorePosts = data.hasMore || false;
            
            renderPosts();
            updatePostsCount();
            
            // Show/hide load more button
            const loadMoreWrapper = document.getElementById('loadMoreWrapper');
            loadMoreWrapper.style.display = app.hasMorePosts ? 'block' : 'none';
            
            console.log(`✅ Loaded ${posts.length} real posts for seller ${sellerId}`);
            
            if (posts.length === 0 && page === 1) {
                showNoPosts();
            }
        } else {
            throw new Error(data.message || 'Failed to load posts');
        }
    } catch (error) {
        console.error('Error loading seller posts:', error);
        if (page === 1) {
            showNoPostsError(error.message);
        }
    } finally {
        showPostsLoading(false);
    }
}

function showPostsLoading(show) {
    const postsGrid = document.getElementById('postsGrid');
    
    if (show) {
        // Show skeleton loading with responsive grid
        const skeletons = Array(6).fill(0).map(() => '<div class="skeleton skeleton-post"></div>').join('');
        postsGrid.innerHTML = skeletons;
    }
}

function renderPosts() {
    const postsGrid = document.getElementById('postsGrid');
    
    const postsHTML = app.selectedSellerPosts.map(post => `
        <div class="post-thumbnail" onclick="openPostDetail(${post.id})" tabindex="0" role="button" aria-label="View post: ${post.caption || 'Store post'}" onkeydown="handlePostKeydown(event, ${post.id})">
            <div class="post-image-wrapper">
                <img src="${post.photo_url}" 
                     alt="${post.caption || post.business_name}" 
                     class="post-thumbnail-img"
                     onerror="handleImageError(this)"
                     loading="lazy">
                ${post.is_featured ? `
                    <div class="post-featured-badge">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor">
                            <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
                        </svg>
                        Featured
                    </div>
                ` : ''}
            </div>
            <div class="post-info">
                <div class="post-title" title="${post.caption || 'Store Post'}">
                    ${post.caption ? (post.caption.length > 25 ? post.caption.substring(0, 25) + '...' : post.caption) : 'Store Post'}
                </div>
                <div class="post-date">${post.time_ago}</div>
            </div>
        </div>
    `).join('');
    
    postsGrid.innerHTML = postsHTML;
}

function handlePostKeydown(event, postId) {
    if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        openPostDetail(postId);
    }
}

function showNoPosts() {
    const postsGrid = document.getElementById('postsGrid');
    postsGrid.innerHTML = `
        <div style="grid-column: 1 / -1; text-align: center; padding: var(--spacing-xxl); color: var(--text-muted);">
            <div style="font-size: clamp(32px, 8vw, 48px); margin-bottom: var(--spacing-lg); opacity: 0.5;">📷</div>
            <h3 style="margin-bottom: var(--spacing-sm); color: var(--text-secondary); font-size: var(--font-lg);">No posts yet</h3>
            <p style="font-size: var(--font-sm);">This store hasn't shared any photos yet.</p>
        </div>
    `;
}

function showNoPostsError(errorMessage) {
    const postsGrid = document.getElementById('postsGrid');
    postsGrid.innerHTML = `
        <div style="grid-column: 1 / -1; text-align: center; padding: var(--spacing-xxl); color: var(--text-muted);">
            <div style="font-size: clamp(32px, 8vw, 48px); margin-bottom: var(--spacing-lg); opacity: 0.5;">⚠️</div>
            <h3 style="margin-bottom: var(--spacing-sm); color: var(--text-secondary); font-size: var(--font-lg);">Error Loading Posts</h3>
            <p style="font-size: var(--font-sm); word-break: break-word;">Failed to load posts: ${errorMessage}</p>
        </div>
    `;
}

function openPostDetail(postId) {
    const post = app.selectedSellerPosts.find(p => p.id === postId);
    if (!post || !app.selectedSeller) return;
    
    // Populate modal content
    document.getElementById('modalPostImage').src = post.photo_url;
    document.getElementById('modalPostTitle').textContent = post.caption || 'Store Post';
    document.getElementById('modalPostCaption').textContent = post.caption || 'No caption provided';
    document.getElementById('modalPostDate').textContent = post.time_ago;
    document.getElementById('modalSellerLocation').textContent = app.selectedSeller.address || 'Location not available';
    document.getElementById('modalSellerRank').textContent = `${app.selectedSeller.rank_text} • ${app.selectedSeller.points_reward || app.selectedSeller.total_points} points`;
    
    // Update seller profile in modal
    const modalSellerProfile = document.getElementById('modalSellerProfile');
    modalSellerProfile.innerHTML = `
        <div class="seller-avatar ${app.selectedSeller.rank_class}">
            ${app.selectedSeller.name.charAt(0).toUpperCase()}
            <div class="rank-badge ${app.selectedSeller.rank_class}">
                ${app.selectedSeller.rank_icon}
            </div>
        </div>
        <div class="seller-info">
            <div class="seller-name">${app.selectedSeller.name}</div>
            <div class="seller-location">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                ${app.selectedSeller.rank_text}
            </div>
        </div>
    `;
    
    // Show modal
    document.getElementById('postDetailModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    // Adjust modal size for current viewport
    adjustModalSize();
    
    // Focus management for accessibility
    setTimeout(() => {
        document.querySelector('.modal-close').focus();
    }, 100);
}

function closePostModal() {
    document.getElementById('postDetailModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

async function loadMorePosts() {
    if (app.isLoading || !app.hasMorePosts || !app.selectedSeller) return;
    
    app.isLoading = true;
    app.currentPostsPage++;
    
    const loadBtn = document.querySelector('.load-more-btn');
    const loadText = loadBtn.querySelector('.load-text');
    const loadSpinner = loadBtn.querySelector('.load-spinner');
    
    loadText.style.display = 'none';
    loadSpinner.style.display = 'flex';
    loadBtn.disabled = true;
    
    try {
        await loadSellerPosts(app.selectedSeller.id, app.currentPostsPage);
    } catch (error) {
        console.error('Error loading more posts:', error);
        app.currentPostsPage--; // Revert page increment
    } finally {
        app.isLoading = false;
        loadText.style.display = 'block';
        loadSpinner.style.display = 'none';
        loadBtn.disabled = false;
    }
}

// SEARCH AND FILTER - Real Data Only
function handleSearch() {
    clearTimeout(app.searchTimeout);
    const query = document.getElementById('sellerSearch').value.toLowerCase().trim();
    
    app.searchTimeout = setTimeout(() => {
        if (query === '') {
            app.filteredSellers = [...app.sellers];
        } else {
            app.filteredSellers = app.sellers.filter(seller => {
                return seller.name.toLowerCase().includes(query) ||
                       (seller.address && seller.address.toLowerCase().includes(query)) ||
                       (seller.phone && seller.phone.toLowerCase().includes(query));
            });
        }
        
        renderSellers();
        updateSellersCount();
        
        // Log search results for debugging
        console.log(`🔍 Search "${query}": ${app.filteredSellers.length} results`);
    }, 300);
}

function handleFilter() {
    const filterValue = document.getElementById('sellerFilter').value;
    const query = document.getElementById('sellerSearch').value.toLowerCase().trim();
    
    let filtered = [...app.sellers];
    
    // Apply search filter
    if (query) {
        filtered = filtered.filter(seller => {
            return seller.name.toLowerCase().includes(query) ||
                   (seller.address && seller.address.toLowerCase().includes(query)) ||
                   (seller.phone && seller.phone.toLowerCase().includes(query));
        });
    }
    
    // Apply rank filter
    if (filterValue !== 'all') {
        filtered = filtered.filter(seller => seller.rank_class === filterValue);
    }
    
    app.filteredSellers = filtered;
    renderSellers();
    updateSellersCount();
    
    // Log filter results for debugging
    console.log(`🔧 Filter "${filterValue}": ${app.filteredSellers.length} results`);
}

function updateSellersCount() {
    document.getElementById('sellersCount').textContent = app.filteredSellers.length;
}

function updatePostsCount() {
    document.getElementById('postsCount').textContent = app.selectedSellerPosts.length;
}

// MOBILE FUNCTIONS
function toggleMobileView() {
    const sellersPanel = document.getElementById('sellersPanel');
    sellersPanel.classList.toggle('mobile-active');
    
    // Focus management for accessibility
    if (sellersPanel.classList.contains('mobile-active')) {
        const searchInput = document.getElementById('sellerSearch');
        setTimeout(() => searchInput.focus(), 100);
    }
}

function closeMobilePanel() {
    const sellersPanel = document.getElementById('sellersPanel');
    if (sellersPanel.classList.contains('mobile-active')) {
        sellersPanel.classList.remove('mobile-active');
    }
}

// MODAL ACTIONS
function visitStore() {
    if (app.selectedSeller) {
        // Use public route
        window.location.href = `/public-api/seller/${app.selectedSeller.id}`;
    }
}

function sharePost() {
    if (app.selectedSeller) {
        const url = `${window.location.origin}/seller/${app.selectedSeller.id}`;
        
        if (navigator.share) {
            navigator.share({
                title: `Check out ${app.selectedSeller.name}!`,
                text: `Amazing store with great posts`,
                url: url
            }).catch(console.error);
        } else {
            navigator.clipboard?.writeText(url).then(() => {
                showToast('Link copied to clipboard!');
            }).catch(() => {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = url;
                document.body.appendChild(textArea);
                textArea.select();
                try {
                    document.execCommand('copy');
                    showToast('Link copied to clipboard!');
                } catch (err) {
                    showToast('Unable to copy link');
                }
                document.body.removeChild(textArea);
            });
        }
    }
}

// UTILITY FUNCTIONS
function handleImageError(img) {
    const fallbackColors = ['2E8B57', '3CB371', '228B22', '32CD32', '98FB98'];
    const color = fallbackColors[Math.floor(Math.random() * fallbackColors.length)];
    img.src = `https://via.placeholder.com/400x400/${color}/FFFFFF?text=🏪`;
}

function showError(message) {
    console.error(message);
    showToast(message, 'error');
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'error' ? '#e74c3c' : 'var(--primary-color)'};
        color: white;
        padding: 12px 16px;
        border-radius: 8px;
        z-index: 10002;
        font-size: var(--font-sm);
        box-shadow: var(--shadow-medium);
        animation: slideInRight 0.3s ease;
        max-width: calc(100vw - 40px);
        word-wrap: break-word;
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// RANKING UTILITY FUNCTIONS
function getRankClass(points) {
    const numPoints = parseFloat(points) || 0;
    if (numPoints >= 2000) return 'platinum';
    if (numPoints >= 1000) return 'gold';
    if (numPoints >= 500) return 'silver';
    if (numPoints >= 100) return 'bronze';
    return 'standard';
}

function getRankText(points) {
    const numPoints = parseFloat(points) || 0;
    if (numPoints >= 2000) return 'Platinum';
    if (numPoints >= 1000) return 'Gold';
    if (numPoints >= 500) return 'Silver';
    if (numPoints >= 100) return 'Bronze';
    return 'Standard';
}

function getRankIcon(points) {
    const numPoints = parseFloat(points) || 0;
    if (numPoints >= 2000) return '👑';
    if (numPoints >= 1000) return '🥇';
    if (numPoints >= 500) return '🥈';
    if (numPoints >= 100) return '🥉';
    return '⭐';
}

// Add CSS animation for toast
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);
</script>
@endsection