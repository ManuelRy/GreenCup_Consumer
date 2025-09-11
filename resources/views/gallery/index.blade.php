@extends('master')

@section('content')
<div class="sellers-gallery-container page-content">
    <!-- Header -->
    {{-- <header class="gallery-header">
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
    </header> --}}

    <!-- Mobile Filter Toggle (Only visible on mobile when header is hidden) -->
    <div class="mobile-filter-toggle">
        <button class="filter-toggle-btn" onclick="toggleMobileView()" aria-label="Open Stores Filter">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9,22 9,12 15,12 15,22"/>
            </svg>
            <span>Stores</span>
            <span class="stores-count-badge" id="mobileStoresCount">0</span>
        </button>
    </div>

    <!-- Mobile Backdrop -->
    <div class="mobile-backdrop" id="mobileBackdrop" onclick="closeMobilePanel()"></div>

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
                <div class="panel-header-actions">
                    <div class="sellers-count">
                        <span id="sellersCount">0</span> stores
                    </div>
                    <button class="mobile-close-btn" onclick="closeMobilePanel()" aria-label="Close stores panel">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
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

                <button class="show-details-btn" onclick="this.nextElementSibling.classList.toggle('collapsed')">Show Details</button>
                <div class="modal-info-section collapsed">
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
/* CSS Variables with Enhanced Mobile-First Responsive Scaling */
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

    /* Navbar height - inherit from master layout */
    --navbar-height: 60px; /* Default, will be overridden by master layout */

    /* Enhanced mobile-first responsive spacing */
    --spacing-xs: clamp(3px, 0.8vw, 6px);
    --spacing-sm: clamp(6px, 1.2vw, 10px);
    --spacing-md: clamp(10px, 2vw, 16px);
    --spacing-lg: clamp(16px, 3vw, 20px);
    --spacing-xl: clamp(20px, 4vw, 28px);
    --spacing-xxl: clamp(28px, 5vw, 36px);

    /* Enhanced mobile-first responsive font sizes */
    --font-xs: clamp(11px, 2.5vw, 13px);
    --font-sm: clamp(13px, 3vw, 15px);
    --font-base: clamp(15px, 3.5vw, 17px);
    --font-lg: clamp(17px, 4vw, 19px);
    --font-xl: clamp(19px, 4.5vw, 22px);
    --font-xxl: clamp(22px, 5.5vw, 26px);

    /* Enhanced responsive widths */
    --sidebar-width: clamp(320px, 38vw, 420px);
    --modal-width: clamp(340px, 92vw, 920px);
    --modal-height: clamp(450px, 85vh, 650px);

    /* Mobile-optimized touch targets */
    --touch-target-min: 48px;
    --touch-target-comfortable: 56px;
}

/* Responsive navbar height adjustments */
@media (max-width: 991.98px) {
    :root {
        --navbar-height: 64px;
    }
}

@media (max-width: 767.98px) {
    :root {
        --navbar-height: 68px;
        --touch-target-min: 52px;
        --touch-target-comfortable: 60px;
    }
}

@media (max-width: 575.98px) {
    :root {
        --navbar-height: 70px;
        --touch-target-min: 56px;
        --touch-target-comfortable: 64px;
    }
}

@media (max-width: 390px) {
    :root {
        --navbar-height: 72px;
        --spacing-xs: 4px;
        --spacing-sm: 8px;
        --spacing-md: 12px;
        --spacing-lg: 18px;
        --spacing-xl: 24px;
        --spacing-xxl: 32px;
    }
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
    min-height: calc(100vh - var(--navbar-height));
    display: flex;
    flex-direction: column;
    background: var(--background-color);
}

/* Enhanced Mobile Filter Toggle */
.mobile-filter-toggle {
    display: none;
    padding: var(--spacing-lg) var(--spacing-md);
    background: var(--panel-bg);
    border-bottom: 1px solid var(--border-color);
    position: sticky;
    top: var(--navbar-height);
    z-index: 100;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.filter-toggle-btn {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    border: none;
    padding: var(--spacing-lg) var(--spacing-xl);
    border-radius: 30px;
    font-size: var(--font-lg);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 6px 20px rgba(29, 209, 161, 0.3);
    margin: 0 auto;
    min-height: var(--touch-target-comfortable);
    width: 100%;
    max-width: 320px;
    justify-content: center;
    position: relative;
    overflow: hidden;
    will-change: transform;
}

.filter-toggle-btn:hover {
    background: linear-gradient(135deg, var(--primary-dark), #0e8f6e);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(29, 209, 161, 0.4);
}

.filter-toggle-btn:active {
    transform: translateY(1px) scale(0.98);
    box-shadow: 0 4px 15px rgba(29, 209, 161, 0.4);
    transition: all 0.1s ease;
}

.filter-toggle-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.filter-toggle-btn:hover::before {
    left: 100%;
}

.stores-count-badge {
    background: rgba(255, 255, 255, 0.25);
    padding: 8px 14px;
    border-radius: 24px;
    font-size: var(--font-sm);
    font-weight: 700;
    margin-left: var(--spacing-md);
    min-width: 32px;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
}

/* Header - Fully Responsive */
.gallery-header {
    background: var(--background-color);
    padding: clamp(12px, 2vh, 16px) clamp(16px, 4vw, 24px);
    border-bottom: 1px solid rgba(255,255,255,0.2);
    position: relative;
    z-index: 10;
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
    min-width: 0;
}

.back-btn {
    background: rgba(255,255,255,0.15);
    border: none;
    border-radius: 50%;
    width: var(--touch-target-min);
    height: var(--touch-target-min);
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
    width: var(--touch-target-min);
    height: var(--touch-target-min);
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

/* Enhanced Main Content - Mobile-First Grid */
.gallery-main {
    flex: 1;
    max-width: 1400px;
    margin: 0 auto;
    width: 100%;
    display: grid;
    grid-template-columns: var(--sidebar-width) 1fr;
    gap: var(--spacing-xl);
    padding: var(--spacing-lg);
    min-height: calc(100vh - var(--navbar-height) - 100px);
}

/* Enhanced Panels with Better Mobile Spacing */
.sellers-panel, .posts-panel {
    background: var(--panel-bg);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-medium);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Enhanced Panel Headers with Better Mobile Layout */
.panel-header {
    padding: var(--spacing-lg) var(--spacing-xl);
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(135deg, #f8f9fa, #ffffff);
    gap: var(--spacing-md);
    flex-wrap: wrap;
    min-height: 80px;
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
    padding: 6px var(--spacing-md);
    border-radius: 16px;
    font-size: var(--font-xs);
    font-weight: 600;
    white-space: nowrap;
    flex-shrink: 0;
}

.panel-header-actions {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.mobile-close-btn {
    display: none;
    background: var(--hover-bg);
    border: 1px solid var(--border-color);
    border-radius: 50%;
    width: var(--touch-target-min);
    height: var(--touch-target-min);
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    color: var(--text-secondary);
    flex-shrink: 0;
}

.mobile-close-btn:hover {
    background: var(--border-color);
    color: var(--text-primary);
}

.mobile-close-btn:active {
    transform: scale(0.95);
}

/* Enhanced Panel Controls with Better Mobile UX */
.panel-controls {
    padding: var(--spacing-lg) var(--spacing-xl) 0;
    display: flex;
    gap: var(--spacing-md);
    flex-wrap: wrap;
}

.search-wrapper {
    position: relative;
    flex: 1;
    min-width: 220px;
}

.search-input {
    width: 100%;
    padding: var(--spacing-md) var(--spacing-lg) var(--spacing-md) 44px;
    border: 2px solid var(--border-color);
    border-radius: 25px;
    font-size: var(--font-sm);
    outline: none;
    transition: all 0.2s ease;
    background: white;
    min-height: var(--touch-target-min);
}

.search-input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(29, 209, 161, 0.1);
}

.search-icon {
    position: absolute;
    left: var(--spacing-lg);
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
}

.filter-select {
    padding: var(--spacing-md) var(--spacing-lg);
    border: 2px solid var(--border-color);
    border-radius: 25px;
    font-size: var(--font-sm);
    outline: none;
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: 140px;
    min-height: var(--touch-target-min);
}

.filter-select:focus {
    border-color: var(--primary-color);
}

/* Enhanced Sellers List */
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
    width: clamp(28px, 6vw, 36px);
    height: clamp(28px, 6vw, 36px);
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

/* Enhanced Seller Card with Better Mobile Touch Experience */
.seller-card {
    background: var(--card-bg);
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: var(--spacing-lg);
    margin-bottom: var(--spacing-md);
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    min-height: var(--touch-target-comfortable);
    will-change: transform;
}

.seller-card:hover {
    border-color: var(--primary-color);
    box-shadow: var(--shadow-medium);
    transform: translateY(-2px);
}

.seller-card:active {
    transform: translateY(0) scale(0.98);
    transition: all 0.1s ease;
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
    width: clamp(44px, 9vw, 52px);
    height: clamp(44px, 9vw, 52px);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: clamp(16px, 3.5vw, 20px);
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
    top: -6px;
    right: -6px;
    width: clamp(18px, 4vw, 22px);
    height: clamp(18px, 4vw, 22px);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: clamp(9px, 2vw, 11px);
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
    margin-bottom: 4px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.seller-location {
    font-size: var(--font-sm);
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 6px;
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
    gap: 6px;
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
    width: clamp(52px, 12vw, 68px);
    height: clamp(52px, 12vw, 68px);
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
    width: clamp(36px, 7vw, 44px);
    height: clamp(36px, 7vw, 44px);
    font-size: clamp(14px, 3vw, 18px);
}

.selected-seller-info .seller-info .seller-name {
    font-size: var(--font-sm);
}

.selected-seller-info .seller-info .seller-location {
    font-size: var(--font-xs);
}

/* Enhanced Posts Grid - Ultra Mobile Responsive */
.posts-grid {
    flex: 1;
    padding: var(--spacing-xl);
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(clamp(160px, 28vw, 200px), 1fr));
    gap: var(--spacing-lg);
    overflow-y: auto;
    scroll-behavior: smooth;
}

.post-thumbnail {
    background: var(--card-bg);
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    overflow: hidden;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    aspect-ratio: 1;
    display: flex;
    flex-direction: column;
    will-change: transform;
}

.post-thumbnail:hover {
    border-color: var(--primary-color);
    box-shadow: var(--shadow-medium);
    transform: translateY(-2px);
}

.post-thumbnail:active {
    transform: translateY(0) scale(0.98);
    transition: all 0.1s ease;
}

.post-image-wrapper {
    flex: 1;
    position: relative;
    overflow: hidden;
    background: #f8f9fa;
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
    padding: 4px 8px;
    border-radius: 8px;
    font-size: var(--font-xs);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}

.post-info {
    padding: var(--spacing-md);
    background: white;
}

.post-title {
    font-size: var(--font-sm);
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 4px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.post-date {
    font-size: var(--font-xs);
    color: var(--text-muted);
}

/* Enhanced Load More Button */
.load-more-wrapper {
    padding: var(--spacing-xl);
    text-align: center;
}

.load-more-btn {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: var(--spacing-lg) var(--spacing-xl);
    border-radius: 25px;
    font-size: var(--font-sm);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    margin: 0 auto;
    min-height: var(--touch-target-min);
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
    width: 18px;
    height: 18px;
    border: 2px solid rgba(255,255,255,0.3);
    border-top: 2px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

/* Enhanced Post Detail Modal - Mobile-First */
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
    padding: var(--spacing-md);
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
    max-height: 92vh;
    width: 100%;
    overflow: hidden;
    box-shadow: var(--shadow-heavy);
}

.modal-close {
    position: absolute;
    top: var(--spacing-lg);
    right: var(--spacing-lg);
    background: rgba(0,0,0,0.6);
    border: none;
    color: white;
    font-size: clamp(20px, 5vw, 28px);
    width: var(--touch-target-min);
    height: var(--touch-target-min);
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    z-index: 10001;
}

.modal-close:hover {
    background: rgba(0,0,0,0.8);
    transform: scale(1.05);
}

.modal-body {
    display: grid;
    grid-template-columns: 1fr 360px;
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
    display: block;
    width: 100%;
    height: auto;
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    margin: 0 auto;
    border-radius: 12px;
    background: #f8f8f8;
}

.show-details-btn {
    display: none;
    width: 100%;
    padding: var(--spacing-lg);
    background: var(--primary-color);
    color: white;
    border: none;
    font-size: var(--font-base);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.show-details-btn:hover {
    background: var(--primary-dark);
}

.modal-info-section {
    background: white;
    padding: var(--spacing-xl);
    display: flex;
    flex-direction: column;
    overflow-y: auto;
}

.modal-info-section.collapsed {
    display: none;
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
    width: clamp(40px, 8vw, 48px);
    height: clamp(40px, 8vw, 48px);
    font-size: clamp(16px, 3.5vw, 20px);
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
    line-height: 1.6;
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
    min-width: 140px;
    padding: var(--spacing-lg);
    border: none;
    border-radius: var(--spacing-md);
    font-size: var(--font-sm);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-sm);
    min-height: var(--touch-target-min);
}

.action-btn.primary {
    background: var(--primary-color);
    color: white;
}

.action-btn.primary:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

.action-btn.secondary {
    background: var(--hover-bg);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
}

.action-btn.secondary:hover {
    background: var(--border-color);
    transform: translateY(-1px);
}

/* Enhanced Mobile Responsiveness - Improved Breakpoints */

/* Extra Large Desktop */
@media (min-width: 1400px) {
    .gallery-main {
        grid-template-columns: 440px 1fr;
        padding: var(--spacing-xxl);
    }
}

/* Large Desktop */
@media (max-width: 1199.98px) {
    .gallery-main {
        grid-template-columns: 340px 1fr;
        padding: var(--spacing-xl);
    }

    .modal-body {
        grid-template-columns: 1fr 300px;
    }
}

/* Medium Desktop/Tablet Landscape */
@media (max-width: 991.98px) {
    .gallery-main {
        grid-template-columns: 320px 1fr;
        gap: var(--spacing-lg);
        padding: var(--spacing-lg);
    }

    .posts-grid {
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: var(--spacing-md);
    }

    .modal-body {
        grid-template-columns: 1fr 280px;
        height: 75vh;
    }

    .panel-header {
        flex-direction: column;
        align-items: flex-start;
        gap: var(--spacing-md);
    }

    .panel-header-actions {
        width: 100%;
        justify-content: space-between;
    }
}

/* Enhanced Tablet Portrait & Mobile Landscape */
@media (max-width: 767.98px) {
    /* Show enhanced mobile interface */
    .mobile-filter-toggle {
        display: block;
        position: sticky;
        top: var(--navbar-height);
        z-index: 100;
        background: var(--panel-bg);
        border-bottom: 2px solid var(--border-color);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        padding: var(--spacing-lg) var(--spacing-lg);
    }

    .filter-toggle-btn {
        max-width: none;
        width: 100%;
        min-height: var(--touch-target-comfortable);
        font-size: var(--font-lg);
        padding: var(--spacing-xl);
        border-radius: 28px;
    }

    .mobile-close-btn {
        display: flex;
        min-width: var(--touch-target-min);
        min-height: var(--touch-target-min);
    }

    .gallery-main {
        grid-template-columns: 1fr;
        gap: 0;
        padding: var(--spacing-md);
        min-height: calc(100vh - var(--navbar-height) - 140px);
    }

    .sellers-panel {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        z-index: 1500;
        border-radius: 0;
        max-width: none;
        overflow-y: auto;
        background: var(--panel-bg);
        box-shadow: 0 0 40px rgba(0, 0, 0, 0.3);
        transform: translateX(-100%);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        padding-top: var(--navbar-height);
    }

    .sellers-panel.mobile-active {
        display: flex;
        transform: translateX(0);
    }

    .mobile-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.6);
        z-index: 1400;
        opacity: 0;
        transition: opacity 0.3s ease;
        backdrop-filter: blur(4px);
    }

    .mobile-backdrop.active {
        display: block;
        opacity: 1;
    }

    .posts-panel {
        grid-column: 1;
        min-height: calc(100vh - var(--navbar-height) - 180px);
        margin-top: var(--spacing-lg);
    }

    .posts-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: var(--spacing-md);
        padding: var(--spacing-lg);
    }

    /* Enhanced Mobile Modal */
    .modal-body {
        grid-template-columns: 1fr;
        grid-template-rows: 1fr auto;
        height: auto;
        max-height: 88vh;
        padding: 0;
    }

    .modal-image-section {
        max-height: 55vh;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #000;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
        overflow: hidden;
    }

    .modal-image-section img {
        width: 100%;
        max-width: 100%;
        max-height: 55vh;
        height: auto;
        object-fit: contain;
        border-radius: 0;
        background: #000;
    }

    .show-details-btn {
        display: block;
        min-height: var(--touch-target-comfortable);
    }

    .modal-info-section {
        padding: var(--spacing-lg);
        max-height: 45vh;
        overflow-y: auto;
    }

    .panel-controls {
        flex-direction: column;
        gap: var(--spacing-lg);
        padding: var(--spacing-lg);
    }

    .search-input, .filter-select {
        min-height: var(--touch-target-comfortable);
        font-size: var(--font-base);
        padding: var(--spacing-lg);
        border-radius: 28px;
        width: 100%;
    }

    .search-input {
        padding-left: 56px;
    }

    .search-icon {
        left: var(--spacing-xl);
        width: 22px;
        height: 22px;
    }
}

/* Enhanced Mobile Portrait */
@media (max-width: 575.98px) {
    .mobile-filter-toggle {
        padding: var(--spacing-lg) var(--spacing-md);
    }

    .filter-toggle-btn {
        min-height: 64px;
        font-size: var(--font-xl);
        border-radius: 24px;
    }

    .gallery-main {
        padding: var(--spacing-sm);
        min-height: calc(100vh - var(--navbar-height) - 120px);
    }

    .panel-header,
    .panel-controls,
    .sellers-list {
        padding-left: var(--spacing-lg);
        padding-right: var(--spacing-lg);
    }

    .seller-card {
        padding: var(--spacing-lg);
        margin-bottom: var(--spacing-lg);
        min-height: 92px;
        border-radius: var(--border-radius);
    }

    .posts-grid {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: var(--spacing-sm);
        padding: var(--spacing-md);
    }

    .modal-content {
        border-radius: 20px;
        max-height: calc(100vh - var(--navbar-height) - 20px);
        margin: 0 auto;
        width: 96vw;
    }

    .modal-actions {
        flex-direction: column;
        gap: var(--spacing-md);
    }

    .action-btn {
        min-width: auto;
        min-height: var(--touch-target-comfortable);
        width: 100%;
    }
}

/* Ultra Small Mobile Devices */
@media (max-width: 390px) {
    .posts-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: var(--spacing-xs);
        padding: var(--spacing-sm);
    }

    .seller-card {
        padding: var(--spacing-md);
        min-height: 80px;
    }

    .modal-content {
        width: 98vw;
        border-radius: 16px;
    }

    .filter-toggle-btn {
        padding: var(--spacing-lg) var(--spacing-xl);
        font-size: var(--font-lg);
        min-height: 56px;
    }
}

/* Very Small Screens */
@media (max-width: 360px) {
    .header-title svg {
        display: none;
    }

    .back-btn, .mobile-toggle {
        width: 40px;
        height: 40px;
    }

    .posts-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 6px;
    }

    .panel-header {
        padding: var(--spacing-md);
    }

    .sellers-list {
        padding: var(--spacing-md);
    }
}

/* Accessibility Enhancements */
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

/* Enhanced Focus styles for accessibility */
.seller-card:focus,
.post-thumbnail:focus,
.back-btn:focus,
.mobile-toggle:focus,
.search-input:focus,
.filter-select:focus,
.load-more-btn:focus,
.action-btn:focus,
.filter-toggle-btn:focus {
    outline: 3px solid var(--primary-color);
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

/* Enhanced Touch-friendly improvements */
@media (hover: none) and (pointer: coarse) {
    .seller-card,
    .post-thumbnail,
    .load-more-btn,
    .action-btn,
    .filter-toggle-btn {
        min-height: var(--touch-target-comfortable);
    }

    .back-btn,
    .mobile-toggle,
    .mobile-close-btn {
        min-width: var(--touch-target-min);
        min-height: var(--touch-target-min);
    }

    /* Enhanced touch feedback */
    .seller-card:active,
    .post-thumbnail:active,
    .filter-toggle-btn:active {
        transform: scale(0.98);
        transition: transform 0.1s ease;
    }
}

/* Safe area adjustments for notched devices */
@supports (padding: max(0px)) {
    .mobile-filter-toggle {
        padding-left: max(var(--spacing-md), env(safe-area-inset-left));
        padding-right: max(var(--spacing-md), env(safe-area-inset-right));
    }

    .sellers-panel {
        padding-left: max(var(--spacing-lg), env(safe-area-inset-left));
        padding-right: max(var(--spacing-lg), env(safe-area-inset-right));
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
    handleResize();
});

// Enhanced resize listener for responsive adjustments
window.addEventListener('resize', handleResize);

function handleResize() {
    // Close mobile panel if screen becomes large
    if (window.innerWidth > 767) {
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
    const showDetailsBtn = document.querySelector('.show-details-btn');
    const modalInfoSection = document.querySelector('.modal-info-section');

    if (window.innerWidth <= 767) {
        modalBody.style.gridTemplateColumns = '1fr';
        modalBody.style.gridTemplateRows = '1fr auto';
        modalBody.style.height = 'auto';
        modalBody.style.maxHeight = '88vh';
        showDetailsBtn.style.display = 'block';
        modalInfoSection.classList.add('collapsed');
    } else {
        modalBody.style.gridTemplateColumns = window.innerWidth <= 1200 ? '1fr 280px' : '1fr 360px';
        modalBody.style.gridTemplateRows = 'unset';
        modalBody.style.height = '600px';
        modalBody.style.maxHeight = '90vh';
        showDetailsBtn.style.display = 'none';
        modalInfoSection.classList.remove('collapsed');
    }
}

async function initializeApp() {
    try {
        await loadSellers();
        initializeEventListeners();

        // Check for seller parameter in URL
        const urlParams = new URLSearchParams(window.location.search);
        const sellerId = urlParams.get('seller');
        if (sellerId) {
            setTimeout(() => selectSeller(parseInt(sellerId)), 500);
        }

        console.log('Sellers Gallery Browser initialized successfully');
    } catch (error) {
        console.error('Failed to initialize app:', error);
        showErrorState('Initialization failed');
    }
}

function initializeEventListeners() {
    // Search functionality with enhanced debouncing
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

    // Enhanced touch gesture support for mobile
    let touchStartX = 0;
    let touchStartY = 0;
    let touchStartTime = 0;

    document.addEventListener('touchstart', function(e) {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
        touchStartTime = Date.now();
    }, { passive: true });

    document.addEventListener('touchend', function(e) {
        if (!touchStartX || !touchStartY) return;

        const touchEndX = e.changedTouches[0].clientX;
        const touchEndY = e.changedTouches[0].clientY;
        const touchEndTime = Date.now();

        const diffX = touchStartX - touchEndX;
        const diffY = touchStartY - touchEndY;
        const timeDiff = touchEndTime - touchStartTime;

        // Only handle swipes that are quick enough and primarily horizontal
        if (timeDiff < 300 && Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
            const sellersPanel = document.getElementById('sellersPanel');

            // Swipe right to open panel (on mobile)
            if (diffX < 0 && window.innerWidth <= 767 && !sellersPanel.classList.contains('mobile-active')) {
                toggleMobileView();
            }
            // Swipe left to close panel
            else if (diffX > 0 && sellersPanel.classList.contains('mobile-active')) {
                closeMobilePanel();
            }
        }

        touchStartX = 0;
        touchStartY = 0;
        touchStartTime = 0;
    }, { passive: true });

    // Enhanced scroll performance
    const postsGrid = document.getElementById('postsGrid');
    if (postsGrid) {
        postsGrid.addEventListener('scroll', throttle(handlePostsScroll, 100), { passive: true });
    }
}

// Throttle function for performance
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

function handlePostsScroll() {
    // Auto-load more posts when near bottom
    const postsGrid = document.getElementById('postsGrid');
    if (postsGrid.scrollTop + postsGrid.clientHeight >= postsGrid.scrollHeight - 200) {
        if (app.hasMorePosts && !app.isLoading) {
            loadMorePosts();
        }
    }
}

// REAL DATA LOADING - No Mock Fallback
async function loadSellers() {
    try {
        showSellersLoading(true);

        // Enhanced timeout handling
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 15000); // 15 second timeout

        const response = await fetch('/public-api/stores', {
            signal: controller.signal
        });

        clearTimeout(timeoutId);

        if (!response.ok) {
            throw new Error(`API response: ${response.status}`);
        }

        const data = await response.json();

        if (data.success) {
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
        app.sellers = [];
        app.filteredSellers = [];
        showErrorState(error.message);
    } finally {
        showSellersLoading(false);
    }
}

function showSellersLoading(show) {
    const sellersList = document.getElementById('sellersList');

    if (show) {
        const skeletons = Array(4).fill(0).map(() => '<div class="skeleton skeleton-seller"></div>').join('');
        sellersList.innerHTML = `<div style="padding: var(--spacing-lg);">${skeletons}</div>`;
    }
}

function showEmptyDatabase() {
    const sellersList = document.getElementById('sellersList');
    sellersList.innerHTML = `
        <div style="text-align: center; padding: var(--spacing-xxl); color: var(--text-muted);">
            <div style="font-size: clamp(36px, 10vw, 52px); margin-bottom: var(--spacing-lg); opacity: 0.5;">🏪</div>
            <h3 style="margin-bottom: var(--spacing-sm); color: var(--text-secondary); font-size: var(--font-lg);">No Stores Yet</h3>
            <p style="font-size: var(--font-sm); line-height: 1.5;">Your database is empty. Add some stores to see them here.</p>
            <button onclick="refreshData()" style="
                margin-top: var(--spacing-lg);
                background: var(--primary-color);
                color: white;
                border: none;
                padding: var(--spacing-md) var(--spacing-lg);
                border-radius: 24px;
                cursor: pointer;
                font-size: var(--font-sm);
                transition: all 0.2s ease;
                min-height: var(--touch-target-min);
            " onmouseover="this.style.background='var(--primary-dark)'" onmouseout="this.style.background='var(--primary-color)'">Refresh</button>
        </div>
    `;
}

function showErrorState(errorMessage) {
    const sellersList = document.getElementById('sellersList');
    sellersList.innerHTML = `
        <div style="text-align: center; padding: var(--spacing-xxl); color: var(--text-muted);">
            <div style="font-size: clamp(36px, 10vw, 52px); margin-bottom: var(--spacing-lg); opacity: 0.5;">⚠️</div>
            <h3 style="margin-bottom: var(--spacing-sm); color: var(--text-secondary); font-size: var(--font-lg);">Connection Error</h3>
            <p style="font-size: var(--font-sm); margin-bottom: var(--spacing-sm); line-height: 1.5;">Failed to load stores:</p>
            <p style="font-size: var(--font-xs); color: var(--text-muted); word-break: break-word; max-width: 280px; margin: 0 auto;">${errorMessage}</p>
            <button onclick="refreshData()" style="
                margin-top: var(--spacing-lg);
                background: var(--primary-color);
                color: white;
                border: none;
                padding: var(--spacing-md) var(--spacing-lg);
                border-radius: 24px;
                cursor: pointer;
                font-size: var(--font-sm);
                transition: all 0.2s ease;
                min-height: var(--touch-target-min);
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

        // Enhanced keyboard support
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

            // Smooth scroll to selected card on mobile
            if (window.innerWidth <= 767) {
                selectedCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
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

// Enhanced REAL POSTS LOADING
async function loadSellerPosts(sellerId, page = 1) {
    try {
        if (page === 1) {
            app.selectedSellerPosts = [];
            app.currentPostsPage = 1;
            document.getElementById('postsGrid').innerHTML = '';
        }

        showPostsLoading(page === 1);

        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 15000);

        const response = await fetch(`/public-api/gallery/feed?seller_id=${sellerId}&page=${page}`, {
            signal: controller.signal
        });

        clearTimeout(timeoutId);

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
                    ${post.caption ? (post.caption.length > 20 ? post.caption.substring(0, 20) + '...' : post.caption) : 'Store Post'}
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
            <div style="font-size: clamp(36px, 10vw, 52px); margin-bottom: var(--spacing-lg); opacity: 0.5;">📷</div>
            <h3 style="margin-bottom: var(--spacing-sm); color: var(--text-secondary); font-size: var(--font-lg);">No posts yet</h3>
            <p style="font-size: var(--font-sm);">This store hasn't shared any photos yet.</p>
        </div>
    `;
}

function showNoPostsError(errorMessage) {
    const postsGrid = document.getElementById('postsGrid');
    postsGrid.innerHTML = `
        <div style="grid-column: 1 / -1; text-align: center; padding: var(--spacing-xxl); color: var(--text-muted);">
            <div style="font-size: clamp(36px, 10vw, 52px); margin-bottom: var(--spacing-lg); opacity: 0.5;">⚠️</div>
            <h3 style="margin-bottom: var(--spacing-sm); color: var(--text-secondary); font-size: var(--font-lg);">Error Loading Posts</h3>
            <p style="font-size: var(--font-sm); word-break: break-word; max-width: 280px; margin: 0 auto;">Failed to load posts: ${errorMessage}</p>
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

    // Enhanced focus management for accessibility
    setTimeout(() => {
        const modalClose = document.querySelector('.modal-close');
        if (modalClose) modalClose.focus();
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
        app.currentPostsPage--;
    } finally {
        app.isLoading = false;
        loadText.style.display = 'block';
        loadSpinner.style.display = 'none';
        loadBtn.disabled = false;
    }
}

// Enhanced SEARCH AND FILTER
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
        console.log(`🔍 Search "${query}": ${app.filteredSellers.length} results`);
    }, 200); // Reduced debounce time for better responsiveness
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
    console.log(`🔧 Filter "${filterValue}": ${app.filteredSellers.length} results`);
}

function updateSellersCount() {
    const count = app.filteredSellers.length;
    document.getElementById('sellersCount').textContent = count;

    const mobileCountBadge = document.getElementById('mobileStoresCount');
    if (mobileCountBadge) {
        mobileCountBadge.textContent = count;
    }
}

function updatePostsCount() {
    document.getElementById('postsCount').textContent = app.selectedSellerPosts.length;
}

// Enhanced MOBILE FUNCTIONS
function toggleMobileView() {
    const sellersPanel = document.getElementById('sellersPanel');
    const mobileBackdrop = document.getElementById('mobileBackdrop');

    sellersPanel.classList.toggle('mobile-active');
    mobileBackdrop.classList.toggle('active');

    if (sellersPanel.classList.contains('mobile-active')) {
        document.body.style.overflow = 'hidden';

        // Enhanced focus management
        const searchInput = document.getElementById('sellerSearch');
        setTimeout(() => {
            if (searchInput) searchInput.focus();
        }, 100);

        // Add enhanced swipe gesture support
        addSwipeGesture(sellersPanel);
    } else {
        document.body.style.overflow = '';
    }
}

function closeMobilePanel() {
    const sellersPanel = document.getElementById('sellersPanel');
    const mobileBackdrop = document.getElementById('mobileBackdrop');

    if (sellersPanel.classList.contains('mobile-active')) {
        sellersPanel.classList.remove('mobile-active');
        mobileBackdrop.classList.remove('active');
        document.body.style.overflow = '';

        // Reset panel transform
        sellersPanel.style.transform = '';
    }
}

// Enhanced swipe gesture for mobile panel
function addSwipeGesture(panel) {
    let startX = 0;
    let currentX = 0;
    let isSwping = false;

    function handleTouchStart(e) {
        startX = e.touches[0].clientX;
        isSwping = true;
        panel.style.transition = 'none';
    }

    function handleTouchMove(e) {
        if (!isSwping) return;
        currentX = e.touches[0].clientX;
        const diffX = startX - currentX;

        // Only allow left swipe (positive diffX)
        if (diffX > 0) {
            const translateX = Math.min(diffX, panel.offsetWidth);
            panel.style.transform = `translateX(-${translateX}px)`;
        }
    }

    function handleTouchEnd(e) {
        if (!isSwping) return;
        isSwping = false;

        const diffX = startX - currentX;
        const threshold = panel.offsetWidth * 0.25; // 25% threshold

        panel.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';

        if (diffX > threshold) {
            closeMobilePanel();
        } else {
            panel.style.transform = 'translateX(0)';
        }
    }

    // Remove existing listeners
    panel.removeEventListener('touchstart', handleTouchStart);
    panel.removeEventListener('touchmove', handleTouchMove);
    panel.removeEventListener('touchend', handleTouchEnd);

    // Add new listeners
    panel.addEventListener('touchstart', handleTouchStart, { passive: true });
    panel.addEventListener('touchmove', handleTouchMove, { passive: true });
    panel.addEventListener('touchend', handleTouchEnd, { passive: true });
}

// MODAL ACTIONS
function visitStore() {
    if (app.selectedSeller) {
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
                // Enhanced fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = url;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
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

// Enhanced UTILITY FUNCTIONS
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
        padding: 14px 18px;
        border-radius: 12px;
        z-index: 10002;
        font-size: var(--font-sm);
        box-shadow: var(--shadow-medium);
        animation: slideInRight 0.3s ease;
        max-width: calc(100vw - 40px);
        word-wrap: break-word;
        font-weight: 500;
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease forwards';
        setTimeout(() => toast.remove(), 300);
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

// Enhanced CSS animations for toast notifications
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

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    /* Enhanced scrollbar styling for better mobile experience */
    .sellers-list::-webkit-scrollbar,
    .posts-grid::-webkit-scrollbar,
    .modal-info-section::-webkit-scrollbar {
        width: 6px;
    }

    .sellers-list::-webkit-scrollbar-track,
    .posts-grid::-webkit-scrollbar-track,
    .modal-info-section::-webkit-scrollbar-track {
        background: var(--hover-bg);
        border-radius: 3px;
    }

    .sellers-list::-webkit-scrollbar-thumb,
    .posts-grid::-webkit-scrollbar-thumb,
    .modal-info-section::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 3px;
        transition: background 0.2s ease;
    }

    .sellers-list::-webkit-scrollbar-thumb:hover,
    .posts-grid::-webkit-scrollbar-thumb:hover,
    .modal-info-section::-webkit-scrollbar-thumb:hover {
        background: var(--text-muted);
    }

    /* Enhanced mobile scrolling performance */
    .sellers-list,
    .posts-grid,
    .modal-info-section {
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
        overscroll-behavior: contain;
    }

    /* Mobile-specific optimizations */
    @media (max-width: 767.98px) {
        /* Hide scrollbars on mobile for cleaner look */
        .sellers-list::-webkit-scrollbar,
        .posts-grid::-webkit-scrollbar,
        .modal-info-section::-webkit-scrollbar {
            display: none;
        }

        .sellers-list,
        .posts-grid,
        .modal-info-section {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Enhanced touch feedback */
        .seller-card:active,
        .post-thumbnail:active,
        .action-btn:active,
        .load-more-btn:active {
            transform: scale(0.97);
            transition: transform 0.1s ease;
        }

        /* Improved modal positioning on mobile */
        .post-modal {
            padding: var(--spacing-xs);
            align-items: flex-start;
            padding-top: max(var(--spacing-lg), env(safe-area-inset-top));
        }

        .modal-content {
            width: calc(100vw - var(--spacing-md));
            max-width: none;
            margin-top: var(--spacing-md);
        }

        /* Better mobile backdrop */
        .mobile-backdrop {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        /* Enhanced mobile panel animation */
        .sellers-panel {
            will-change: transform;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
        }

        /* Improved mobile grid spacing */
        @media (max-width: 480px) {
            .posts-grid {
                padding: var(--spacing-md) var(--spacing-sm);
            }
        }

        @media (max-width: 390px) {
            .posts-grid {
                gap: var(--spacing-xs);
                padding: var(--spacing-sm);
            }
        }
    }

    /* Performance optimizations */
    .seller-card,
    .post-thumbnail,
    .filter-toggle-btn,
    .load-more-btn,
    .action-btn {
        will-change: transform;
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
    }

    /* Prevent text selection on interactive elements */
    .seller-card,
    .post-thumbnail,
    .filter-toggle-btn,
    .mobile-close-btn,
    .load-more-btn,
    .action-btn {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        -webkit-tap-highlight-color: transparent;
    }

    /* Enhanced dark mode support for future implementation */
    @media (prefers-color-scheme: dark) {
        :root {
            --panel-bg: #1a1a1a;
            --card-bg: #2d2d2d;
            --text-primary: #ffffff;
            --text-secondary: #b0b0b0;
            --text-muted: #808080;
            --border-color: #404040;
            --hover-bg: #353535;
        }
    }
`;
document.head.appendChild(style);
</script>
@endsection
