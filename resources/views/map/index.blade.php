@extends('master')

@section('content')
    <div class="map-container">
        <!-- Header -->
        <div class="map-header">
            <div class="header-nav">
                <a href="{{ route('dashboard') }}" class="back-btn">
                    <span>←</span>
                </a>
                <h2>🗺️ Store Locator</h2>
                <button id="viewToggle" class="view-toggle-btn">
                    <span id="toggleIcon">📋</span>
                    <span id="toggleText">List</span>
                </button>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="search-section">
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search stores, locations..." class="search-input">
                <button id="searchBtn" class="search-btn">🔍</button>
                <button id="clearSearch" class="clear-btn" style="display: none;">×</button>
            </div>
            <button class="location-btn" id="locationBtn">
                <span>📍</span>
                <span>My Location</span>
            </button>
        </div>

        <!-- Controls & Filters -->
        <div class="toggle-controls">
            <div class="view-options">
                <button class="view-option active" data-view="map">
                    <span>🗺️</span>
                    <span>Map</span>
                </button>
                <button class="view-option" data-view="list">
                    <span>📋</span>
                    <span>List</span>
                </button>
            </div>
            <div class="filter-controls">
                <select id="sortSelect" class="sort-select">
                    <option value="nearest">📍 Nearest First</option>
                    <option value="farthest">📍 Farthest First</option>
                    <option value="name">🔤 Name A-Z</option>
                    <option value="rank">👑 Best Rank</option>
                    <option value="popular">🔥 Most Popular</option>
                </select>
                <select id="radiusSelect" class="radius-select">
                    <option value="5">Within 5km</option>
                    <option value="10">Within 10km</option>
                    <option value="25" selected>Within 25km</option>
                    <option value="50">Within 50km</option>
                    <option value="all">All Stores</option>
                </select>
            </div>
        </div>

        <!-- Loading Indicator -->
        <div id="loadingIndicator" class="loading-indicator" style="display: none;">
            <div class="spinner"></div>
            <span>Loading stores...</span>
        </div>

        <!-- Map View -->
        <div id="mapView" class="map-view">
            <!-- Map Style Controls -->
            <div class="map-style-controls">
                <button class="style-btn active" data-style="mapbox://styles/mapbox/streets-v12" title="Day Mode">
                    🌅
                </button>
                <button class="style-btn" data-style="mapbox://styles/mapbox/dark-v11" title="Night Mode">
                    🌙
                </button>
                <button class="style-btn" data-style="mapbox://styles/mapbox/satellite-v9" title="Satellite">
                    🛰️
                </button>
            </div>
            
            <!-- Map Legend -->
            <div class="map-legend">
                <div class="legend-item">
                    <div class="legend-marker platinum"></div>
                    <span>Platinum</span>
                </div>
                <div class="legend-item">
                    <div class="legend-marker gold"></div>
                    <span>Gold</span>
                </div>
                <div class="legend-item">
                    <div class="legend-marker silver"></div>
                    <span>Silver</span>
                </div>
                <div class="legend-item">
                    <div class="legend-marker bronze"></div>
                    <span>Bronze</span>
                </div>
                <div class="legend-item">
                    <div class="legend-marker user"></div>
                    <span>Your Location</span>
                </div>
            </div>
            
            <div id="map" class="mapbox-map"></div>
        </div>

        <!-- List View -->
        <div id="listView" class="list-view" style="display: none;">
            <div class="list-header">
                <h3>📍 Nearby Stores</h3>
                <div class="list-stats">
                    <span id="storeCount">0</span> stores found
                    <span id="userLocationText" style="display: none;"> within <span id="radiusText">25km</span></span>
                </div>
            </div>
            <div id="storeList" class="store-list">
                <!-- Store items will be populated here -->
            </div>
            <div id="noStoresMessage" class="no-stores" style="display: none;">
                <div class="no-stores-icon">🏪</div>
                <h3>No stores found</h3>
                <p>Try expanding your search radius or searching in a different area.</p>
                <button onclick="expandSearch()" class="expand-search-btn">Expand Search</button>
            </div>
        </div>

        <!-- Store Detail Modal -->
        <div id="storeModal" class="store-modal" style="display: none;">
            <div class="modal-overlay" onclick="closeStoreModal()"></div>
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="storeName">Store Details</h3>
                    <button onclick="closeStoreModal()" class="modal-close">×</button>
                </div>
                
                <div class="modal-body">
                    <!-- Store Rank Display -->
                    <div class="store-rank-display">
                        <div id="storeRankBadge" class="rank-badge-large platinum">
                            <span id="rankIcon">👑</span>
                            <span id="rankText">Platinum</span>
                        </div>
                    </div>

                    <!-- Store Information -->
                    <div class="store-info">
                        <div class="info-section">
                            <h4>📍 Location & Contact</h4>
                            <div class="info-row">
                                <span class="info-icon">📍</span>
                                <div class="info-content">
                                    <span class="info-label">Address</span>
                                    <span id="storeAddress" class="info-value">-</span>
                                </div>
                            </div>
                            
                            <div class="info-row">
                                <span class="info-icon">📞</span>
                                <div class="info-content">
                                    <span class="info-label">Phone</span>
                                    <span id="storePhone" class="info-value clickable" onclick="callStore()">-</span>
                                </div>
                            </div>
                            
                            <div class="info-row">
                                <span class="info-icon">📏</span>
                                <div class="info-content">
                                    <span class="info-label">Distance</span>
                                    <span id="storeDistance" class="info-value">-</span>
                                </div>
                            </div>
                        </div>

                        <div class="info-section">
                            <h4>⏰ Hours & Details</h4>
                            <div class="info-row">
                                <span class="info-icon">⏰</span>
                                <div class="info-content">
                                    <span class="info-label">Hours</span>
                                    <span id="storeHours" class="info-value">-</span>
                                </div>
                            </div>
                            
                            <div class="info-row">
                                <span class="info-icon">📊</span>
                                <div class="info-content">
                                    <span class="info-label">Popularity</span>
                                    <span id="storePopularity" class="info-value">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Store Description -->
                    <div class="store-description">
                        <h4>ℹ️ About This Store</h4>
                        <p id="storeDesc">Store description will appear here...</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button onclick="getDirections()" class="btn-directions">
                            <span>🧭</span>
                            <span>Directions</span>
                        </button>
                        <button onclick="callStore()" class="btn-call">
                            <span>📞</span>
                            <span>Call</span>
                        </button>
                        <button onclick="shareStore()" class="btn-share">
                            <span>📤</span>
                            <span>Share</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Messages -->
        <div id="errorToast" class="error-toast" style="display: none;">
            <span id="errorMessage">An error occurred</span>
            <button onclick="hideErrorToast()">×</button>
        </div>
    </div>

    <style>
        /* Base Styles */
        .map-container {
            position: relative;
            height: 100vh;
            overflow: hidden;
            background: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Header */
        .map-header {
            background: linear-gradient(135deg, #2E8B57, #3CB371);
            padding: 15px 20px;
            color: white;
            position: relative;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            font-size: 20px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            text-decoration: none;
            transform: scale(1.05);
        }

        .header-nav h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            flex: 1;
            text-align: center;
        }

        .view-toggle-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
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
        }

        .view-toggle-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Search Section */
        .search-section {
            background: white;
            padding: 15px 20px;
            display: flex;
            gap: 10px;
            border-bottom: 1px solid #e9ecef;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .search-container {
            flex: 1;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 12px 80px 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: #2E8B57;
            box-shadow: 0 0 0 3px rgba(46, 139, 87, 0.1);
        }

        .search-btn, .clear-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: #2E8B57;
            border: none;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            color: white;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .search-btn {
            right: 45px;
        }

        .clear-btn {
            right: 5px;
            background: #dc3545;
            font-size: 18px;
        }

        .location-btn {
            background: #2E8B57;
            border: none;
            border-radius: 20px;
            padding: 10px 15px;
            color: white;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
            transition: all 0.3s ease;
        }

        .location-btn:hover {
            background: #228B22;
            transform: translateY(-1px);
        }

        .location-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Controls */
        .toggle-controls {
            background: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e9ecef;
            flex-wrap: wrap;
            gap: 15px;
        }

        .view-options {
            display: flex;
            background: #f8f9fa;
            border-radius: 25px;
            padding: 4px;
        }

        .view-option {
            background: none;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            color: #666;
        }

        .view-option.active {
            background: #2E8B57;
            color: white;
            box-shadow: 0 2px 8px rgba(46, 139, 87, 0.3);
        }

        .filter-controls {
            display: flex;
            gap: 10px;
        }

        .sort-select, .radius-select {
            padding: 8px 12px;
            border: 2px solid #e9ecef;
            border-radius: 20px;
            font-size: 14px;
            outline: none;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .sort-select:focus, .radius-select:focus {
            border-color: #2E8B57;
        }

        /* Loading */
        .loading-indicator {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 15px;
            z-index: 2000;
        }

        .spinner {
            width: 24px;
            height: 24px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #2E8B57;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Map View */
        .map-view {
            position: relative;
            height: calc(100vh - 200px);
        }

        .map-style-controls {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .style-btn {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .style-btn.active {
            background: #2E8B57;
            color: white;
            border-color: #2E8B57;
            transform: scale(1.1);
        }

        .style-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .map-legend {
            position: absolute;
            bottom: 15px;
            left: 15px;
            background: white;
            padding: 12px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            z-index: 1000;
            font-size: 12px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
        }

        .legend-item:last-child {
            margin-bottom: 0;
        }

        .legend-marker {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }

        .legend-marker.platinum {
            background: linear-gradient(135deg, #E5E4E2, #BCC6CC);
        }

        .legend-marker.gold {
            background: #FFD700;
        }

        .legend-marker.silver {
            background: #C0C0C0;
        }

        .legend-marker.bronze {
            background: #CD7F32;
        }

        .legend-marker.user {
            background: #FF6B35;
        }

        .mapbox-map {
            width: 100%;
            height: 100%;
        }

        /* List View */
        .list-view {
            height: calc(100vh - 200px);
            overflow-y: auto;
            background: white;
        }

        .list-header {
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
            background: #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .list-header h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }

        .list-stats {
            font-size: 14px;
            color: #666;
        }

        .store-list {
            padding: 0 20px 20px;
        }

        /* Store Items with Rank Borders */
        .store-item {
            background: white;
            border: 4px solid #e9ecef;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .store-item.platinum {
            border: 4px solid;
            border-image: linear-gradient(135deg, #E5E4E2, #BCC6CC, #E5E4E2) 1;
            box-shadow: 0 4px 20px rgba(229, 228, 226, 0.3);
        }

        .store-item.gold {
            border-color: #FFD700;
            box-shadow: 0 4px 20px rgba(255, 215, 0, 0.3);
        }

        .store-item.silver {
            border-color: #C0C0C0;
            box-shadow: 0 4px 20px rgba(192, 192, 192, 0.3);
        }

        .store-item.bronze {
            border-color: #CD7F32;
            box-shadow: 0 4px 20px rgba(205, 127, 50, 0.3);
        }

        .store-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .store-item-header {
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }

        .store-rank-indicator {
            position: relative;
            flex-shrink: 0;
        }

        .store-card-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2E8B57, #3CB371);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: bold;
            text-transform: uppercase;
            border: 4px solid;
            position: relative;
        }

        .store-card-avatar.platinum {
            border-color: #E5E4E2;
            box-shadow: 0 0 0 2px #BCC6CC, 0 4px 12px rgba(229, 228, 226, 0.5);
        }

        .store-card-avatar.gold {
            border-color: #FFD700;
            box-shadow: 0 0 0 2px #FFA500, 0 4px 12px rgba(255, 215, 0, 0.5);
        }

        .store-card-avatar.silver {
            border-color: #C0C0C0;
            box-shadow: 0 0 0 2px #A0A0A0, 0 4px 12px rgba(192, 192, 192, 0.5);
        }

        .store-card-avatar.bronze {
            border-color: #CD7F32;
            box-shadow: 0 0 0 2px #B87333, 0 4px 12px rgba(205, 127, 50, 0.5);
        }

        .rank-crown {
            position: absolute;
            top: -10px;
            right: -10px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            border: 2px solid white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        }

        .rank-crown.platinum {
            background: linear-gradient(135deg, #E5E4E2, #BCC6CC);
        }

        .rank-crown.gold {
            background: #FFD700;
        }

        .rank-crown.silver {
            background: #C0C0C0;
        }

        .rank-crown.bronze {
            background: #CD7F32;
            color: white;
        }

        .store-main-info {
            flex: 1;
            min-width: 0;
        }

        .store-name {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 6px;
            word-wrap: break-word;
        }

        .store-address {
            font-size: 14px;
            color: #666;
            margin-bottom: 12px;
            line-height: 1.4;
            word-wrap: break-word;
        }

        .store-meta {
            display: flex;
            gap: 15px;
            font-size: 13px;
            color: #999;
            align-items: center;
            flex-wrap: wrap;
        }

        .store-rank-text {
            display: flex;
            align-items: center;
            gap: 4px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
        }

        .store-rank-text.platinum {
            background: linear-gradient(135deg, #E5E4E2, #BCC6CC);
            color: #333;
        }

        .store-rank-text.gold {
            background: #FFD700;
            color: #333;
        }

        .store-rank-text.silver {
            background: #C0C0C0;
            color: #333;
        }

        .store-rank-text.bronze {
            background: #CD7F32;
            color: white;
        }

        .store-side-info {
            text-align: right;
            flex-shrink: 0;
        }

        .store-distance {
            font-size: 16px;
            font-weight: 700;
            color: #2E8B57;
            margin-bottom: 6px;
        }

        .store-phone {
            font-size: 12px;
            color: #666;
        }

        /* No Stores Message */
        .no-stores {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }

        .no-stores-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .no-stores h3 {
            margin: 0 0 10px 0;
            font-size: 18px;
        }

        .no-stores p {
            margin: 0 0 20px 0;
            line-height: 1.5;
        }

        .expand-search-btn {
            background: #2E8B57;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .expand-search-btn:hover {
            background: #228B22;
        }

        /* Modal Styles */
        .store-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            width: 100%;
            max-width: 420px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            animation: modalSlideUp 0.3s ease;
        }

        @keyframes modalSlideUp {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #2E8B57, #3CB371);
            color: white;
            padding: 20px;
            border-radius: 16px 16px 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 4px;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .modal-body {
            padding: 20px;
        }

        /* Store Rank Display in Modal */
        .store-rank-display {
            text-align: center;
            margin-bottom: 25px;
        }

        .rank-badge-large {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 16px;
            border: 3px solid;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .rank-badge-large.platinum {
            background: linear-gradient(135deg, #E5E4E2, #BCC6CC);
            border-color: #D3D3D3;
            color: #333;
        }

        .rank-badge-large.gold {
            background: #FFD700;
            border-color: #FFA500;
            color: #333;
        }

        .rank-badge-large.silver {
            background: #C0C0C0;
            border-color: #A0A0A0;
            color: #333;
        }

        .rank-badge-large.bronze {
            background: #CD7F32;
            border-color: #B87333;
            color: white;
        }

        /* Store Info Sections */
        .store-info {
            margin-bottom: 20px;
        }

        .info-section {
            margin-bottom: 20px;
        }

        .info-section h4 {
            margin: 0 0 15px 0;
            font-size: 16px;
            color: #333;
            border-bottom: 2px solid #2E8B57;
            padding-bottom: 5px;
        }

        .info-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-icon {
            font-size: 16px;
            width: 20px;
            text-align: center;
            margin-top: 2px;
        }

        .info-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .info-label {
            font-size: 12px;
            color: #666;
            font-weight: 500;
        }

        .info-value {
            font-size: 14px;
            color: #333;
            font-weight: 600;
            line-height: 1.4;
        }

        .info-value.clickable {
            color: #2E8B57;
            cursor: pointer;
            text-decoration: underline;
        }

        .store-description {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .store-description h4 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #333;
        }

        .store-description p {
            margin: 0;
            font-size: 14px;
            color: #555;
            line-height: 1.5;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-directions, .btn-call, .btn-share {
            flex: 1;
            padding: 12px 8px;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            transition: all 0.3s ease;
        }

        .btn-directions {
            background: linear-gradient(135deg, #2E8B57, #3CB371);
            color: white;
        }

        .btn-directions:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(46, 139, 87, 0.3);
        }

        .btn-call {
            background: #007bff;
            color: white;
        }

        .btn-call:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        .btn-share {
            background: #6c757d;
            color: white;
        }

        .btn-share:hover {
            background: #545b62;
            transform: translateY(-2px);
        }

        /* Store Markers - Rank-based */
        .store-marker {
            position: relative;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 4px solid;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            cursor: pointer;
            transition: all 0.3s ease;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .store-marker.platinum {
            border-color: #E5E4E2;
            box-shadow: 0 4px 12px rgba(229, 228, 226, 0.6);
        }

        .store-marker.gold {
            border-color: #FFD700;
            box-shadow: 0 4px 12px rgba(255, 215, 0, 0.6);
        }

        .store-marker.silver {
            border-color: #C0C0C0;
            box-shadow: 0 4px 12px rgba(192, 192, 192, 0.6);
        }

        .store-marker.bronze {
            border-color: #CD7F32;
            box-shadow: 0 4px 12px rgba(205, 127, 50, 0.6);
        }

        .store-marker:hover {
            transform: scale(1.2);
            box-shadow: 0 6px 20px rgba(0,0,0,0.4);
        }

        .marker-initial {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #2E8B57, #3CB371);
            color: white;
            font-size: 18px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            text-transform: uppercase;
        }

        .rank-crown-marker {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .rank-crown-marker.platinum {
            background: linear-gradient(135deg, #E5E4E2, #BCC6CC);
        }

        .rank-crown-marker.gold {
            background: #FFD700;
        }

        .rank-crown-marker.silver {
            background: #C0C0C0;
        }

        .rank-crown-marker.bronze {
            background: #CD7F32;
            color: white;
        }

        .user-marker {
            background: #FF6B35;
            border: 3px solid white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        /* Error Toast */
        .error-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #dc3545;
            color: white;
            padding: 12px 16px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 10001;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
            animation: slideInRight 0.3s ease;
        }

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

        .error-toast button {
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            padding: 0;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .map-view, .list-view {
                height: calc(100vh - 220px);
            }

            .toggle-controls {
                flex-direction: column;
                gap: 10px;
                align-items: stretch;
            }

            .filter-controls {
                justify-content: space-between;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-directions, .btn-call, .btn-share {
                flex-direction: row;
                justify-content: center;
            }

            .store-card-avatar {
                width: 60px;
                height: 60px;
                font-size: 24px;
            }
        }

        @media (max-width: 480px) {
            .map-header {
                padding: 15px;
            }

            .search-section {
                padding: 15px;
            }

            .toggle-controls {
                padding: 15px;
            }

            .map-view, .list-view {
                height: calc(100vh - 240px);
            }

            .store-list {
                padding: 0 15px 15px;
            }

            .modal-content {
                margin: 10px;
                max-height: 95vh;
                max-width: calc(100% - 20px);
            }
        }
    </style>

    <!-- Mapbox GL JS -->
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet">
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>

    <script>
        // Configuration
        const CONFIG = {
            mapboxToken: '{{ $mapboxToken }}',
            defaultCenter: [104.9910, 11.5564], // Phnom Penh
            defaultZoom: 12,
            maxZoom: 18,
            minZoom: 8
        };

        // Application State
        let app = {
            map: null,
            userLocation: null,
            currentView: 'map',
            stores: @json($stores),
            filteredStores: [],
            markers: [],
            userMarker: null,
            selectedStore: null,
            isLoading: false,
            searchRadius: 25
        };

        // Initialize application
        document.addEventListener('DOMContentLoaded', function() {
            initializeApp();
        });

        async function initializeApp() {
            try {
                // Initialize map first
                console.log('Initializing map...');
                initializeMap();
                
                // Initialize event listeners
                console.log('Setting up event listeners...');
                initializeEventListeners();
                
                // Initialize stores data
                console.log('Loading stores...');
                initializeStores();
                
                // Set up initial view
                console.log('Setting up initial view...');
                setupInitialView();
                
                console.log('App initialized successfully!');
            } catch (error) {
                console.error('App initialization failed:', error);
                
                // Show a more user-friendly error
                const mapContainer = document.getElementById('map');
                if (mapContainer) {
                    mapContainer.innerHTML = `
                        <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: #f8f9fa; text-align: center; padding: 20px;">
                            <div>
                                <div style="font-size: 48px; margin-bottom: 15px;">🗺️</div>
                                <h3 style="color: #333; margin-bottom: 10px;">Map Loading...</h3>
                                <p style="color: #666; margin-bottom: 20px;">Please wait while we load the store locations.</p>
                                <button onclick="location.reload()" style="background: #2E8B57; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                                    Refresh Page
                                </button>
                            </div>
                        </div>
                    `;
                }
                
                // Try to still show the list view with sample data
                setTimeout(() => {
                    try {
                        app.stores = [];
                        app.filteredStores = [];
                        switchView('list');
                        updateStoreDisplay();
                    } catch (e) {
                        console.error('Fallback failed:', e);
                    }
                }, 1000);
            }
        }

        function initializeMap() {
            mapboxgl.accessToken = CONFIG.mapboxToken;
            
            app.map = new mapboxgl.Map({
                container: 'map',
                style: 'mapbox://styles/mapbox/streets-v12',
                center: CONFIG.defaultCenter,
                zoom: CONFIG.defaultZoom,
                maxZoom: CONFIG.maxZoom,
                minZoom: CONFIG.minZoom
            });

            // Add controls
            app.map.addControl(new mapboxgl.NavigationControl(), 'top-left');
            
            // Add geolocate control
            const geolocate = new mapboxgl.GeolocateControl({
                positionOptions: {
                    enableHighAccuracy: true
                },
                trackUserLocation: true,
                showUserHeading: true
            });
            
            app.map.addControl(geolocate, 'top-left');

            // Handle user location updates
            geolocate.on('geolocate', function(e) {
                updateUserLocation(e.coords.latitude, e.coords.longitude);
            });
        }

        function initializeEventListeners() {
            // View toggle buttons
            document.querySelectorAll('.view-option').forEach(btn => {
                btn.addEventListener('click', () => switchView(btn.dataset.view));
            });

            // Header toggle button
            document.getElementById('viewToggle').addEventListener('click', () => {
                const newView = app.currentView === 'map' ? 'list' : 'map';
                switchView(newView);
            });

            // Map style buttons
            document.querySelectorAll('.style-btn').forEach(btn => {
                btn.addEventListener('click', () => changeMapStyle(btn.dataset.style, btn));
            });

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            const searchBtn = document.getElementById('searchBtn');
            const clearBtn = document.getElementById('clearSearch');

            searchBtn.addEventListener('click', handleSearch);
            clearBtn.addEventListener('click', clearSearch);
            
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') handleSearch();
            });

            searchInput.addEventListener('input', (e) => {
                const hasValue = e.target.value.length > 0;
                clearBtn.style.display = hasValue ? 'block' : 'none';
                searchBtn.style.right = hasValue ? '45px' : '5px';
            });

            // Sort and filter controls
            document.getElementById('sortSelect').addEventListener('change', (e) => {
                sortStores(e.target.value);
            });

            document.getElementById('radiusSelect').addEventListener('change', (e) => {
                app.searchRadius = e.target.value;
                document.getElementById('radiusText').textContent = 
                    e.target.value === 'all' ? 'all areas' : e.target.value + 'km';
                if (app.userLocation) {
                    filterStoresByRadius();
                }
            });

            // Location button
            document.getElementById('locationBtn').addEventListener('click', getCurrentLocation);
        }

        function initializeStores() {
            app.filteredStores = [...app.stores];
            addMarkersToMap();
            updateStoreDisplay();
        }

        function setupInitialView() {
            switchView('map');
            if (app.stores.length === 0) {
                showError('No stores found in the database');
            }
        }

        function switchView(view) {
            app.currentView = view;
            
            // Update view option buttons
            document.querySelectorAll('.view-option').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.view === view);
            });

            // Update header toggle button
            const toggleIcon = document.getElementById('toggleIcon');
            const toggleText = document.getElementById('toggleText');
            
            if (view === 'map') {
                document.getElementById('mapView').style.display = 'block';
                document.getElementById('listView').style.display = 'none';
                toggleIcon.textContent = '📋';
                toggleText.textContent = 'List';
                
                // Resize map after showing
                setTimeout(() => app.map.resize(), 100);
            } else {
                document.getElementById('mapView').style.display = 'none';
                document.getElementById('listView').style.display = 'block';
                toggleIcon.textContent = '🗺️';
                toggleText.textContent = 'Map';
                updateListView();
            }
        }

        function changeMapStyle(styleUrl, button) {
            showLoading();
            
            app.map.setStyle(styleUrl);
            
            // Update active button
            document.querySelectorAll('.style-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            button.classList.add('active');

            // Re-add markers after style change
            app.map.once('styledata', () => {
                addMarkersToMap();
                hideLoading();
            });
        }

        function addMarkersToMap() {
            // Clear existing markers
            app.markers.forEach(marker => marker.remove());
            app.markers = [];

            app.filteredStores.forEach(store => {
                // Create custom rank-based marker
                const markerElement = document.createElement('div');
                const rankClass = getRankClass(store.rating);
                markerElement.className = `store-marker ${rankClass}`;
                
                // Add store initial
                markerElement.innerHTML = `<div class="marker-initial">${store.name.charAt(0)}</div>`;
                
                // Add rank crown
                const rankCrown = document.createElement('div');
                rankCrown.className = `rank-crown-marker ${rankClass}`;
                rankCrown.textContent = getRankIcon(store.rating);
                markerElement.appendChild(rankCrown);
                
                markerElement.title = `${store.name} - ${getRankText(store.rating)}`;

                const marker = new mapboxgl.Marker(markerElement)
                    .setLngLat([store.longitude, store.latitude])
                    .addTo(app.map);

                markerElement.addEventListener('click', () => {
                    showStoreDetail(store);
                });

                app.markers.push(marker);
            });
        }

        function updateListView() {
            const storeList = document.getElementById('storeList');
            const noStoresMessage = document.getElementById('noStoresMessage');
            
            storeList.innerHTML = '';

            if (app.filteredStores.length === 0) {
                noStoresMessage.style.display = 'block';
                return;
            }

            noStoresMessage.style.display = 'none';

            app.filteredStores.forEach(store => {
                const storeItem = document.createElement('div');
                const rankClass = getRankClass(store.rating);
                storeItem.className = `store-item ${rankClass}`;
                
                const distanceText = store.distance > 0 ? 
                    `${store.distance.toFixed(1)} km` : 
                    'Getting location...';
                
                const rankIcon = getRankIcon(store.rating);
                const rankText = getRankText(store.rating);
                
                storeItem.innerHTML = `
                    <div class="store-item-header">
                        <div class="store-rank-indicator">
                            <div class="store-card-avatar ${rankClass}">${store.name.charAt(0)}</div>
                            <div class="rank-crown ${rankClass}">${rankIcon}</div>
                        </div>
                        <div class="store-main-info">
                            <div class="store-name">${store.name}</div>
                            <div class="store-address">${store.address}</div>
                            <div class="store-meta">
                                <div class="store-rank-text ${rankClass}">
                                    <span>${rankIcon}</span>
                                    <span>${rankText}</span>
                                </div>
                                <span>📊 ${store.transaction_count} visits</span>
                            </div>
                        </div>
                        <div class="store-side-info">
                            <div class="store-distance">${distanceText}</div>
                            <div class="store-phone">📞 ${store.phone}</div>
                        </div>
                    </div>
                `;

                storeItem.addEventListener('click', () => {
                    showStoreDetail(store);
                });

                storeList.appendChild(storeItem);
            });
        }

        // Helper functions for ranking system
        function getRankIcon(rating) {
            if (rating >= 4.8) return '👑'; // Platinum
            if (rating >= 4.5) return '🥇'; // Gold  
            if (rating >= 4.0) return '🥈'; // Silver
            if (rating >= 3.5) return '🥉'; // Bronze
            return '⭐'; // Standard
        }

        function getRankText(rating) {
            if (rating >= 4.8) return 'Platinum';
            if (rating >= 4.5) return 'Gold';
            if (rating >= 4.0) return 'Silver';
            if (rating >= 3.5) return 'Bronze';
            return 'Standard';
        }

        function getRankClass(rating) {
            if (rating >= 4.8) return 'platinum';
            if (rating >= 4.5) return 'gold';
            if (rating >= 4.0) return 'silver';
            if (rating >= 3.5) return 'bronze';
            return 'standard';
        }

        function updateStoreDisplay() {
            updateStoreCount();
            if (app.currentView === 'list') {
                updateListView();
            }
            addMarkersToMap();
        }

        function updateStoreCount() {
            document.getElementById('storeCount').textContent = app.filteredStores.length;
        }

        async function handleSearch() {
            const query = document.getElementById('searchInput').value.trim();
            if (!query) return;

            try {
                showLoading();
                
                const response = await fetch(`/api/stores?search=${encodeURIComponent(query)}`);
                const data = await response.json();
                
                if (data.success) {
                    app.filteredStores = data.data;
                    updateStoreDisplay();
                } else {
                    throw new Error(data.message || 'Search failed');
                }
            } catch (error) {
                console.error('Search error:', error);
                showError('Search failed. Please try again.');
            } finally {
                hideLoading();
            }
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            document.getElementById('clearSearch').style.display = 'none';
            document.getElementById('searchBtn').style.right = '5px';
            
            app.filteredStores = [...app.stores];
            updateStoreDisplay();
        }

        function sortStores(sortType) {
            switch(sortType) {
                case 'nearest':
                    app.filteredStores.sort((a, b) => (a.distance || Infinity) - (b.distance || Infinity));
                    break;
                case 'farthest':
                    app.filteredStores.sort((a, b) => (b.distance || 0) - (a.distance || 0));
                    break;
                case 'name':
                    app.filteredStores.sort((a, b) => a.name.localeCompare(b.name));
                    break;
                case 'rank':
                    app.filteredStores.sort((a, b) => b.rating - a.rating);
                    break;
                case 'popular':
                    app.filteredStores.sort((a, b) => b.transaction_count - a.transaction_count);
                    break;
            }
            
            updateStoreDisplay();
        }

        async function getCurrentLocation() {
            const locationBtn = document.getElementById('locationBtn');
            
            if (!navigator.geolocation) {
                showError('Geolocation is not supported by this browser');
                return;
            }

            locationBtn.disabled = true;
            locationBtn.innerHTML = '<span>⌛</span><span>Getting Location...</span>';

            try {
                const position = await new Promise((resolve, reject) => {
                    navigator.geolocation.getCurrentPosition(resolve, reject, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 60000
                    });
                });

                updateUserLocation(position.coords.latitude, position.coords.longitude);
                
            } catch (error) {
                console.error('Geolocation error:', error);
                showError('Unable to get your location. Please check your location settings.');
            } finally {
                locationBtn.disabled = false;
                locationBtn.innerHTML = '<span>📍</span><span>My Location</span>';
            }
        }

        function updateUserLocation(latitude, longitude) {
            app.userLocation = { latitude, longitude };
            
            // Update user location marker
            if (app.userMarker) {
                app.userMarker.remove();
            }
            
            const userMarkerElement = document.createElement('div');
            userMarkerElement.className = 'user-marker';
            
            app.userMarker = new mapboxgl.Marker(userMarkerElement)
                .setLngLat([longitude, latitude])
                .addTo(app.map);

            // Fly to user location
            app.map.flyTo({
                center: [longitude, latitude],
                zoom: 14
            });

            // Calculate distances and update display
            calculateDistances();
            filterStoresByRadius();
            
            // Show user location text
            document.getElementById('userLocationText').style.display = 'inline';
            
            // Update the display to show calculated distances
            updateStoreDisplay();
        }

        function calculateDistances() {
            if (!app.userLocation) return;

            app.stores.forEach(store => {
                store.distance = calculateHaversineDistance(
                    app.userLocation.latitude,
                    app.userLocation.longitude,
                    store.latitude,
                    store.longitude
                );
            });

            app.filteredStores.forEach(store => {
                store.distance = calculateHaversineDistance(
                    app.userLocation.latitude,
                    app.userLocation.longitude,
                    store.latitude,
                    store.longitude
                );
            });

            // Re-sort if currently sorting by distance
            const currentSort = document.getElementById('sortSelect').value;
            if (currentSort === 'nearest' || currentSort === 'farthest') {
                sortStores(currentSort);
            }
        }

        function filterStoresByRadius() {
            if (!app.userLocation || app.searchRadius === 'all') {
                app.filteredStores = [...app.stores];
            } else {
                const radiusKm = parseFloat(app.searchRadius);
                app.filteredStores = app.stores.filter(store => 
                    store.distance <= radiusKm
                );
            }
            
            updateStoreDisplay();
        }

        function calculateHaversineDistance(lat1, lng1, lat2, lng2) {
            const R = 6371; // Earth's radius in kilometers
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLng = (lng2 - lng1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLng/2) * Math.sin(dLng/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        async function showStoreDetail(store) {
            app.selectedStore = store;
            
            try {
                // Get detailed store information
                const response = await fetch(`/api/store/${store.id}/details`);
                const data = await response.json();
                
                if (data.success) {
                    const storeDetails = data.data;
                    populateStoreModal(storeDetails);
                } else {
                    populateStoreModal(store);
                }
            } catch (error) {
                console.error('Error fetching store details:', error);
                populateStoreModal(store);
            }
            
            document.getElementById('storeModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function populateStoreModal(store) {
            document.getElementById('storeName').textContent = store.name;
            document.getElementById('storeAddress').textContent = store.address;
            document.getElementById('storePhone').textContent = store.phone;
            document.getElementById('storeHours').textContent = store.hours || 'Hours not specified';
            document.getElementById('storeDesc').textContent = store.description || 'No description available';
            
            // Distance
            const distanceText = store.distance > 0 ? 
                `${store.distance.toFixed(1)} km away` : 
                'Distance unknown';
            document.getElementById('storeDistance').textContent = distanceText;
            
            // Popularity
            document.getElementById('storePopularity').textContent = 
                `${store.transaction_count} customer visits`;
            
            // Rank display
            const rankBadge = document.getElementById('storeRankBadge');
            const rankIcon = document.getElementById('rankIcon');
            const rankText = document.getElementById('rankText');
            const rankClass = getRankClass(store.rating);
            
            rankBadge.className = `rank-badge-large ${rankClass}`;
            rankIcon.textContent = getRankIcon(store.rating);
            rankText.textContent = getRankText(store.rating);
        }

        function closeStoreModal() {
            document.getElementById('storeModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            app.selectedStore = null;
        }

        function getDirections() {
            if (!app.selectedStore) return;
            
            const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${app.selectedStore.latitude},${app.selectedStore.longitude}&travelmode=driving`;
            window.open(googleMapsUrl, '_blank');
        }

        function callStore() {
            if (!app.selectedStore || !app.selectedStore.phone) {
                showError('Phone number not available');
                return;
            }
            window.location.href = `tel:${app.selectedStore.phone}`;
        }

        function shareStore() {
            if (!app.selectedStore) return;
            
            const shareData = {
                title: app.selectedStore.name,
                text: `Check out ${app.selectedStore.name} - ${app.selectedStore.address}`,
                url: window.location.href
            };
            
            if (navigator.share) {
                navigator.share(shareData);
            } else {
                // Fallback
                navigator.clipboard.writeText(`${shareData.title} - ${shareData.text}`);
                showSuccess('Store information copied to clipboard!');
            }
        }

        function expandSearch() {
            document.getElementById('radiusSelect').value = 'all';
            app.searchRadius = 'all';
            app.filteredStores = [...app.stores];
            updateStoreDisplay();
        }

        function showLoading() {
            app.isLoading = true;
            document.getElementById('loadingIndicator').style.display = 'flex';
        }

        function hideLoading() {
            app.isLoading = false;
            document.getElementById('loadingIndicator').style.display = 'none';
        }

        function showError(message) {
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('errorToast').style.display = 'flex';
            
            setTimeout(() => {
                hideErrorToast();
            }, 5000);
        }

        function showSuccess(message) {
            // You can implement a success toast similar to error toast
            console.log('Success:', message);
        }

        function hideErrorToast() {
            document.getElementById('errorToast').style.display = 'none';
        }

        // Handle modal closing
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                closeStoreModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeStoreModal();
            }
        });

        // Handle page visibility changes
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden && app.map) {
                app.map.resize();
            }
        });
    </script>
@endsection