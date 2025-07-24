@extends('master')

@section('content')
    <div class="fullscreen-map-container">
        <!-- Header (Fixed at top) -->
        <div class="map-header-fixed">
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

        <!-- Search Bar (Fixed at top) -->
        <div class="search-section-fixed">
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

        <!-- Controls & Filters (Fixed at top) -->
        <div class="toggle-controls-fixed">
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

        <!-- Location Debug Info (Temporary) -->
        <div id="locationDebug" class="location-debug" style="display: none;">
            <div class="debug-info">
                <h4>🐛 Location Debug</h4>
                <p>Latitude: <span id="debugLat">-</span></p>
                <p>Longitude: <span id="debugLng">-</span></p>
                <p>Accuracy: <span id="debugAccuracy">-</span>m</p>
                <p>Timestamp: <span id="debugTime">-</span></p>
                <button onclick="hideLocationDebug()">Hide</button>
            </div>
        </div>

        <!-- Full Screen Map View -->
        <div id="mapView" class="fullscreen-map-view">
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

            <!-- Debug Location Button -->
            <button id="debugLocationBtn" class="debug-location-btn" onclick="showLocationDebug()">
                🐛 Debug Location
            </button>

            <div id="map" class="fullscreen-mapbox-map"></div>
        </div>

        <!-- Full Screen List View -->
        <div id="listView" class="fullscreen-list-view" style="display: none;">
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
        /* FULL SCREEN MAP STYLES */
        .fullscreen-map-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: white;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            z-index: 1000;
            overflow: hidden;
        }

        /* Fixed Header */
        .map-header-fixed {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #2E8B57, #3CB371);
            padding: 15px 20px;
            color: white;
            z-index: 1001;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            font-size: 18px;
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
            font-size: 18px;
            font-weight: 600;
            flex: 1;
            text-align: center;
        }

        .view-toggle-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 20px;
            padding: 6px 14px;
            color: white;
            font-size: 13px;
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

        /* Fixed Search Section */
        .search-section-fixed {
            position: fixed;
            top: 70px;
            left: 0;
            right: 0;
            background: white;
            padding: 12px 20px;
            display: flex;
            gap: 12px;
            z-index: 1001;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .search-container {
            flex: 1;
            position: relative;
            max-width: 600px;
        }

        .search-input {
            width: 100%;
            padding: 10px 70px 10px 15px;
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

        .search-btn,
        .clear-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: #2E8B57;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            color: white;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .search-btn {
            right: 40px;
        }

        .clear-btn {
            right: 5px;
            background: #dc3545;
            font-size: 16px;
        }

        .location-btn {
            background: #2E8B57;
            border: none;
            border-radius: 20px;
            padding: 8px 12px;
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

        /* Fixed Controls */
        .toggle-controls-fixed {
            position: fixed;
            top: 126px;
            left: 0;
            right: 0;
            background: white;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1001;
            border-bottom: 1px solid #e9ecef;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .view-options {
            display: flex;
            background: #f8f9fa;
            border-radius: 25px;
            padding: 3px;
        }

        .view-option {
            background: none;
            border: none;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
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
            gap: 8px;
        }

        .sort-select,
        .radius-select {
            padding: 6px 10px;
            border: 2px solid #e9ecef;
            border-radius: 20px;
            font-size: 13px;
            outline: none;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .sort-select:focus,
        .radius-select:focus {
            border-color: #2E8B57;
        }

        /* Full Screen Map View */
        .fullscreen-map-view {
            position: absolute;
            top: 178px;
            left: 0;
            right: 0;
            bottom: 0;
            overflow: hidden;
        }

        .fullscreen-mapbox-map {
            width: 100%;
            height: 100%;
        }

        /* Full Screen List View */
        .fullscreen-list-view {
            position: absolute;
            top: 178px;
            left: 0;
            right: 0;
            bottom: 0;
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
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .list-header h3 {
            margin: 0;
            font-size: 16px;
            color: #333;
        }

        .list-stats {
            font-size: 13px;
            color: #666;
        }

        .store-list {
            padding: 15px 20px;
        }

        /* Map Controls Repositioned */
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
            width: 40px;
            height: 40px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .map-legend {
            position: absolute;
            bottom: 15px;
            left: 15px;
            background: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            font-size: 11px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 4px;
        }

        .legend-item:last-child {
            margin-bottom: 0;
        }

        .legend-marker {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        .legend-marker.platinum {
            background: linear-gradient(135deg, #9B59B6, #8E44AD);
        }

        .legend-marker.gold {
            background: linear-gradient(135deg, #FFD700, #FFA500);
        }

        .legend-marker.silver {
            background: linear-gradient(135deg, #C0C0C0, #A8A8A8);
        }

        .legend-marker.bronze {
            background: linear-gradient(135deg, #CD7F32, #B87333);
        }

        .legend-marker.user {
            background: #FF6B35;
        }

        /* DEBUG LOCATION STYLES */
        .debug-location-btn {
            position: absolute;
            bottom: 15px;
            right: 15px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 12px;
            cursor: pointer;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .debug-location-btn:hover {
            background: #c82333;
        }

        .location-debug {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border: 2px solid #dc3545;
            border-radius: 8px;
            padding: 20px;
            z-index: 2000;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        .debug-info h4 {
            margin: 0 0 10px 0;
            color: #dc3545;
        }

        .debug-info p {
            margin: 5px 0;
            font-size: 13px;
        }

        .debug-info button {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        /* Enhanced User Marker */
        .user-marker {
            background: #FF6B35;
            border: 4px solid white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            box-shadow: 0 3px 12px rgba(255, 107, 53, 0.5);
            position: relative;
        }

        .user-marker::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 8px;
            height: 8px;
            background: white;
            border-radius: 50%;
        }

        /* Store Items */
        .store-item {
            background: white;
            border: 3px solid #e9ecef;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .store-item.platinum {
            border: 3px solid #9B59B6;
            box-shadow: 0 3px 15px rgba(155, 89, 182, 0.2);
        }

        .store-item.gold {
            border: 3px solid #FFD700;
            box-shadow: 0 3px 15px rgba(255, 215, 0, 0.2);
        }

        .store-item.silver {
            border: 3px solid #C0C0C0;
            box-shadow: 0 3px 15px rgba(192, 192, 192, 0.2);
        }

        .store-item.bronze {
            border: 3px solid #CD7F32;
            box-shadow: 0 3px 15px rgba(205, 127, 50, 0.2);
        }

        .store-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .store-item-header {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .store-rank-indicator {
            position: relative;
            flex-shrink: 0;
        }

        .store-card-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2E8B57, #3CB371);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            border: 3px solid;
            position: relative;
        }

        .store-card-avatar.platinum {
            border-color: #9B59B6;
            box-shadow: 0 0 0 2px #8E44AD, 0 3px 10px rgba(155, 89, 182, 0.4);
        }

        .store-card-avatar.gold {
            border-color: #FFD700;
            box-shadow: 0 0 0 2px #FFA500, 0 3px 10px rgba(255, 215, 0, 0.4);
        }

        .store-card-avatar.silver {
            border-color: #C0C0C0;
            box-shadow: 0 0 0 2px #A8A8A8, 0 3px 10px rgba(192, 192, 192, 0.4);
        }

        .store-card-avatar.bronze {
            border-color: #CD7F32;
            box-shadow: 0 0 0 2px #B87333, 0 3px 10px rgba(205, 127, 50, 0.4);
        }

        .rank-crown {
            position: absolute;
            top: -6px;
            right: -6px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            border: 2px solid white;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        .rank-crown.platinum {
            background: linear-gradient(135deg, #9B59B6, #8E44AD);
            color: white;
        }

        .rank-crown.gold {
            background: linear-gradient(135deg, #FFD700, #FFA500);
            color: #333;
        }

        .rank-crown.silver {
            background: linear-gradient(135deg, #C0C0C0, #A8A8A8);
            color: #333;
        }

        .rank-crown.bronze {
            background: linear-gradient(135deg, #CD7F32, #B87333);
            color: white;
        }

        .store-main-info {
            flex: 1;
            min-width: 0;
        }

        .store-name {
            font-size: 15px;
            font-weight: 700;
            color: #333;
            margin-bottom: 3px;
            word-wrap: break-word;
        }

        .store-address {
            font-size: 12px;
            color: #666;
            margin-bottom: 6px;
            line-height: 1.4;
            word-wrap: break-word;
        }

        .store-meta {
            display: flex;
            gap: 10px;
            font-size: 11px;
            color: #999;
            align-items: center;
            flex-wrap: wrap;
        }

        .store-rank-text {
            display: flex;
            align-items: center;
            gap: 3px;
            font-weight: 600;
            padding: 3px 6px;
            border-radius: 10px;
            font-size: 11px;
        }

        .store-rank-text.platinum {
            background: linear-gradient(135deg, #9B59B6, #8E44AD);
            color: white;
        }

        .store-rank-text.gold {
            background: linear-gradient(135deg, #FFD700, #FFA500);
            color: #333;
        }

        .store-rank-text.silver {
            background: linear-gradient(135deg, #C0C0C0, #A8A8A8);
            color: #333;
        }

        .store-rank-text.bronze {
            background: linear-gradient(135deg, #CD7F32, #B87333);
            color: white;
        }

        .store-side-info {
            text-align: right;
            flex-shrink: 0;
        }

        .store-distance {
            font-size: 13px;
            font-weight: 700;
            color: #2E8B57;
            margin-bottom: 3px;
        }

        .store-phone {
            font-size: 10px;
            color: #666;
        }

        /* Loading */
        .loading-indicator {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
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

        /* Store Markers - Enhanced */
        .store-marker {
            position: relative;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 4px solid;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            cursor: pointer;
            transition: all 0.3s ease;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .store-marker.platinum {
            border-color: #9B59B6;
            box-shadow: 0 4px 12px rgba(155, 89, 182, 0.6);
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
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        }

        .marker-initial {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #2E8B57, #3CB371);
            color: white;
            font-size: 16px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            text-transform: uppercase;
        }

        .rank-crown-marker {
            position: absolute;
            top: -6px;
            right: -6px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .rank-crown-marker.platinum {
            background: linear-gradient(135deg, #9B59B6, #8E44AD);
            color: white;
        }

        .rank-crown-marker.gold {
            background: linear-gradient(135deg, #FFD700, #FFA500);
            color: #333;
        }

        .rank-crown-marker.silver {
            background: linear-gradient(135deg, #C0C0C0, #A8A8A8);
            color: #333;
        }

        .rank-crown-marker.bronze {
            background: linear-gradient(135deg, #CD7F32, #B87333);
            color: white;
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
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        .modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: transparent;
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
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .rank-badge-large.platinum {
            background: linear-gradient(135deg, #9B59B6, #8E44AD);
            border-color: #7D3C98;
            color: white;
        }

        .rank-badge-large.gold {
            background: linear-gradient(135deg, #FFD700, #FFA500);
            border-color: #E67E22;
            color: #333;
        }

        .rank-badge-large.silver {
            background: linear-gradient(135deg, #C0C0C0, #A8A8A8);
            border-color: #95A5A6;
            color: #333;
        }

        .rank-badge-large.bronze {
            background: linear-gradient(135deg, #CD7F32, #B87333);
            border-color: #A0522D;
            color: white;
        }

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

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-directions,
        .btn-call,
        .btn-share {
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

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .header-nav h2 {
                font-size: 16px;
            }
            
            .search-section-fixed {
                flex-direction: column;
                gap: 8px;
            }
            
            .location-btn {
                align-self: flex-start;
            }
            
            .toggle-controls-fixed {
                flex-direction: column;
                gap: 10px;
                align-items: stretch;
            }
            
            .filter-controls {
                justify-content: space-between;
            }
            
            .fullscreen-map-view,
            .fullscreen-list-view {
                top: 220px;
            }
        }

        @media (max-width: 480px) {
            .map-header-fixed {
                padding: 12px 15px;
            }
            
            .search-section-fixed {
                padding: 10px 15px;
            }
            
            .toggle-controls-fixed {
                padding: 10px 15px;
            }
            
            .modal-content {
                margin: 10px;
                max-height: 95vh;
                max-width: calc(100% - 20px);
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-directions,
            .btn-call,
            .btn-share {
                flex-direction: row;
                justify-content: center;
            }
        }
    </style>

    <!-- Mapbox GL JS -->
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet">
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>

    <script>
        // Enhanced Configuration with Better Location Handling
        const CONFIG = {
            mapboxToken: '{{ $mapboxToken }}',
            // Start with a broader view
            defaultCenter: [104.9910, 11.5564], 
            defaultZoom: 11,
            maxZoom: 18,
            minZoom: 6
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
            searchRadius: 25,
            locationRequested: false,
            debugMode: false
        };

        // Initialize application
        document.addEventListener('DOMContentLoaded', function () {
            initializeApp();
        });

        async function initializeApp() {
            try {
                console.log('Initializing full screen map...');
                initializeMap();
                initializeEventListeners();
                initializeStores();
                setupInitialView();

                // Auto-request location after a short delay
                setTimeout(() => {
                    if (!app.userLocation) {
                        getCurrentLocationSilently();
                    }
                }, 1500);

                console.log('Full screen map initialized successfully!');
            } catch (error) {
                console.error('App initialization failed:', error);
                handleInitializationError();
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

            // Add navigation controls
            app.map.addControl(new mapboxgl.NavigationControl(), 'top-left');

            // Enhanced geolocate control
            const geolocate = new mapboxgl.GeolocateControl({
                positionOptions: {
                    enableHighAccuracy: true,
                    timeout: 20000,
                    maximumAge: 300000 // 5 minutes cache
                },
                trackUserLocation: true,
                showUserHeading: true,
                showAccuracyCircle: true
            });

            app.map.addControl(geolocate, 'top-left');

            // Handle successful geolocation
            geolocate.on('geolocate', function (e) {
                console.log('✅ Mapbox geolocation success:', e.coords);
                updateLocationDebugInfo(e.coords);
                updateUserLocation(e.coords.latitude, e.coords.longitude, true);
                app.locationRequested = true;
            });

            // Handle geolocation errors
            geolocate.on('error', function (e) {
                console.error('❌ Mapbox geolocation error:', e);
                showError('Unable to get your location from map controls. Try the location button.');
            });

            // Map load event
            app.map.on('load', () => {
                console.log('Map loaded successfully');
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
                searchBtn.style.right = hasValue ? '40px' : '5px';
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

            // Location button - Enhanced
            document.getElementById('locationBtn').addEventListener('click', getCurrentLocationWithHighAccuracy);

            // Escape key handler
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeStoreModal();
                    hideLocationDebug();
                }
            });
        }

        function initializeStores() {
            app.stores.forEach((store, index) => {
                store.distance = null;
                store.points_reward = parseFloat(store.points_reward) || 0;
                console.log(`Store ${index}: ${store.name} - Points: ${store.points_reward} - Rank: ${getRankText(store.points_reward)}`);
            });

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

        // ENHANCED LOCATION FUNCTIONS

        // Silent location request (no user feedback)
        async function getCurrentLocationSilently() {
            if (!navigator.geolocation) {
                console.log('Geolocation not supported');
                return;
            }

            try {
                const position = await new Promise((resolve, reject) => {
                    navigator.geolocation.getCurrentPosition(
                        resolve, 
                        reject, 
                        {
                            enableHighAccuracy: false,
                            timeout: 5000,
                            maximumAge: 600000 // 10 minutes
                        }
                    );
                });

                console.log('📍 Silent location success:', position.coords);
                updateLocationDebugInfo(position.coords);
                updateUserLocation(position.coords.latitude, position.coords.longitude, false);

            } catch (error) {
                console.log('Silent location failed (this is normal):', error.message);
            }
        }

        // High accuracy location request with user feedback
        async function getCurrentLocationWithHighAccuracy() {
            const locationBtn = document.getElementById('locationBtn');

            if (!navigator.geolocation) {
                showError('Geolocation is not supported by this browser');
                return;
            }

            locationBtn.disabled = true;
            locationBtn.innerHTML = '<span>⌛</span><span>Getting Location...</span>';

            try {
                // MULTIPLE ATTEMPTS WITH DIFFERENT SETTINGS
                let position = null;
                
                // Attempt 1: High accuracy
                try {
                    position = await new Promise((resolve, reject) => {
                        navigator.geolocation.getCurrentPosition(resolve, reject, {
                            enableHighAccuracy: true,
                            timeout: 15000,
                            maximumAge: 0 // Force fresh location
                        });
                    });
                    console.log('🎯 High accuracy location success:', position.coords);
                } catch (highAccuracyError) {
                    console.log('High accuracy failed, trying standard accuracy...');
                    
                    // Attempt 2: Standard accuracy
                    position = await new Promise((resolve, reject) => {
                        navigator.geolocation.getCurrentPosition(resolve, reject, {
                            enableHighAccuracy: false,
                            timeout: 10000,
                            maximumAge: 60000 // 1 minute cache OK
                        });
                    });
                    console.log('📍 Standard accuracy location success:', position.coords);
                }

                updateLocationDebugInfo(position.coords);
                updateUserLocation(position.coords.latitude, position.coords.longitude, true);
                app.locationRequested = true;

                // Show success feedback
                locationBtn.innerHTML = '<span>✅</span><span>Location Found</span>';
                setTimeout(() => {
                    locationBtn.innerHTML = '<span>📍</span><span>My Location</span>';
                }, 2000);

            } catch (error) {
                console.error('❌ All location attempts failed:', error);
                
                let errorMessage = 'Unable to get your location. ';
                
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage += 'Please allow location access in your browser.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage += 'Location services are unavailable.';
                        break;
                    case error.TIMEOUT:
                        errorMessage += 'Location request timed out. Please try again.';
                        break;
                    default:
                        errorMessage += 'An unknown error occurred.';
                        break;
                }
                
                showError(errorMessage);
                
                locationBtn.innerHTML = '<span>❌</span><span>Location Failed</span>';
                setTimeout(() => {
                    locationBtn.innerHTML = '<span>📍</span><span>Try Again</span>';
                }, 3000);
            } finally {
                locationBtn.disabled = false;
            }
        }

        // ENHANCED USER LOCATION UPDATE
        function updateUserLocation(latitude, longitude, flyToLocation = true) {
            console.log(`🎯 Updating user location: ${latitude}, ${longitude}`);
            console.log(`Accuracy check - Lat valid: ${latitude >= -90 && latitude <= 90}, Lng valid: ${longitude >= -180 && longitude <= 180}`);
            
            // Validate coordinates
            if (!latitude || !longitude || 
                latitude < -90 || latitude > 90 || 
                longitude < -180 || longitude > 180) {
                console.error('❌ Invalid coordinates received:', { latitude, longitude });
                showError('Invalid location coordinates received');
                return;
            }

            app.userLocation = { latitude, longitude };

            // Remove existing user marker
            if (app.userMarker) {
                app.userMarker.remove();
            }

            // Create enhanced user marker
            const userMarkerElement = document.createElement('div');
            userMarkerElement.className = 'user-marker';
            userMarkerElement.title = `Your Location (${latitude.toFixed(6)}, ${longitude.toFixed(6)})`;

            // CRITICAL: Ensure correct coordinate order [longitude, latitude]
            app.userMarker = new mapboxgl.Marker(userMarkerElement)
                .setLngLat([longitude, latitude]) // Mapbox expects [lng, lat]
                .addTo(app.map);

            console.log(`✅ User marker added at [${longitude}, ${latitude}]`);

            // Fly to location if requested
            if (flyToLocation) {
                console.log('🎯 Flying to user location...');
                app.map.flyTo({
                    center: [longitude, latitude], // [lng, lat]
                    zoom: 15,
                    duration: 2500,
                    essential: true
                });
            }

            // Calculate distances and update display
            calculateDistances();
            filterStoresByRadius();

            // Show user location text
            document.getElementById('userLocationText').style.display = 'inline';
            updateStoreDisplay();
            
            console.log('✅ User location updated successfully');
        }

        // DEBUG FUNCTIONS
        function updateLocationDebugInfo(coords) {
            if (!app.debugMode) return;
            
            document.getElementById('debugLat').textContent = coords.latitude.toFixed(6);
            document.getElementById('debugLng').textContent = coords.longitude.toFixed(6);
            document.getElementById('debugAccuracy').textContent = coords.accuracy.toFixed(0);
            document.getElementById('debugTime').textContent = new Date().toLocaleTimeString();
        }

        function showLocationDebug() {
            app.debugMode = true;
            document.getElementById('locationDebug').style.display = 'block';
            
            // Trigger a fresh location request for debugging
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        updateLocationDebugInfo(position.coords);
                        console.log('🐛 Debug location:', position.coords);
                    },
                    (error) => {
                        console.error('🐛 Debug location error:', error);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            }
        }

        function hideLocationDebug() {
            app.debugMode = false;
            document.getElementById('locationDebug').style.display = 'none';
        }

        // Continue with the rest of your existing functions...
        // (switchView, addMarkersToMap, updateListView, etc.)
        // Copy all the remaining functions from your previous JavaScript code

        function switchView(view) {
            app.currentView = view;

            document.querySelectorAll('.view-option').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.view === view);
            });

            const toggleIcon = document.getElementById('toggleIcon');
            const toggleText = document.getElementById('toggleText');

            if (view === 'map') {
                document.getElementById('mapView').style.display = 'block';
                document.getElementById('listView').style.display = 'none';
                toggleIcon.textContent = '📋';
                toggleText.textContent = 'List';
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
            document.querySelectorAll('.style-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            button.classList.add('active');
            app.map.once('styledata', () => {
                addMarkersToMap();
                hideLoading();
            });
        }

        function addMarkersToMap() {
            app.markers.forEach(marker => marker.remove());
            app.markers = [];

            app.filteredStores.forEach(store => {
                const markerElement = document.createElement('div');
                const rankClass = getRankClass(store.points_reward);
                markerElement.className = `store-marker ${rankClass}`;
                markerElement.innerHTML = `<div class="marker-initial">${store.name.charAt(0)}</div>`;

                const rankCrown = document.createElement('div');
                rankCrown.className = `rank-crown-marker ${rankClass}`;
                rankCrown.textContent = getRankIcon(store.points_reward);
                markerElement.appendChild(rankCrown);

                markerElement.title = `${store.name} - ${getRankText(store.points_reward)} (${store.points_reward} points)`;

                const marker = new mapboxgl.Marker(markerElement)
                    .setLngLat([store.longitude, store.latitude])
                    .addTo(app.map);

                markerElement.addEventListener('click', () => {
                    showStoreDetail({ ...store });
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
                const rankClass = getRankClass(store.points_reward);
                storeItem.className = `store-item ${rankClass}`;

                let distanceText = 'Distance unknown';
                if (app.userLocation && store.distance !== undefined) {
                    if (store.distance > 0) {
                        distanceText = `${store.distance.toFixed(1)} km`;
                    } else if (store.distance === 0) {
                        distanceText = 'Same location';
                    }
                } else if (!app.userLocation) {
                    distanceText = 'Location needed';
                }

                const rankIcon = getRankIcon(store.points_reward);
                const rankText = getRankText(store.points_reward);

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

        // Ranking helper functions
        function getRankIcon(points) {
            const numPoints = parseFloat(points) || 0;
            if (numPoints >= 2000) return '👑';
            if (numPoints >= 1000) return '🥇';
            if (numPoints >= 500) return '🥈';
            if (numPoints >= 100) return '🥉';
            return '⭐';
        }

        function getRankText(points) {
            const numPoints = parseFloat(points) || 0;
            if (numPoints >= 2000) return 'Platinum';
            if (numPoints >= 1000) return 'Gold';
            if (numPoints >= 500) return 'Silver';
            if (numPoints >= 100) return 'Bronze';
            return 'Standard';
        }

        function getRankClass(points) {
            const numPoints = parseFloat(points) || 0;
            if (numPoints >= 2000) return 'platinum';
            if (numPoints >= 1000) return 'gold';
            if (numPoints >= 500) return 'silver';
            if (numPoints >= 100) return 'bronze';
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
            switch (sortType) {
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
                    app.filteredStores.sort((a, b) => (b.points_reward || 0) - (a.points_reward || 0));
                    break;
                case 'popular':
                    app.filteredStores.sort((a, b) => b.transaction_count - a.transaction_count);
                    break;
            }
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

            const currentSort = document.getElementById('sortSelect').value;
            if (currentSort === 'nearest' || currentSort === 'farthest') {
                sortStores(currentSort);
            } else {
                updateStoreDisplay();
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
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLng = (lng2 - lng1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLng / 2) * Math.sin(dLng / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        async function showStoreDetail(store) {
            app.selectedStore = JSON.parse(JSON.stringify(store));

            try {
                const response = await fetch(`/api/store/${store.id}/details`);
                const data = await response.json();

                if (data.success) {
                    const storeDetails = { ...data.data, points_reward: app.selectedStore.points_reward };
                    populateStoreModal(storeDetails);
                } else {
                    populateStoreModal(app.selectedStore);
                }
            } catch (error) {
                console.error('Error fetching store details:', error);
                populateStoreModal(app.selectedStore);
            }

            document.getElementById('storeModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function populateStoreModal(store) {
            const points = parseFloat(store.points_reward) || 0;
            const rankClass = getRankClass(points);
            const rankIcon = getRankIcon(points);
            const rankText = getRankText(points);

            document.getElementById('storeName').textContent = store.name;
            document.getElementById('storeAddress').textContent = store.address;
            document.getElementById('storePhone').textContent = store.phone;
            document.getElementById('storeHours').textContent = store.hours || 'Hours not specified';
            document.getElementById('storeDesc').textContent = store.description || 'No description available';

            let distanceText = 'Distance unknown';
            if (app.userLocation && store.distance !== undefined && store.distance !== null) {
                if (store.distance > 0) {
                    distanceText = `${store.distance.toFixed(1)} km away`;
                } else if (store.distance === 0) {
                    distanceText = 'Same location';
                }
            } else if (!app.userLocation) {
                distanceText = 'Enable location to see distance';
            }
            document.getElementById('storeDistance').textContent = distanceText;

            document.getElementById('storePopularity').textContent =
                `${store.transaction_count || 0} customer visits`;

            const rankBadge = document.getElementById('storeRankBadge');
            const rankIconElement = document.getElementById('rankIcon');
            const rankTextElement = document.getElementById('rankText');

            rankBadge.className = `rank-badge-large ${rankClass}`;
            rankIconElement.textContent = rankIcon;
            rankTextElement.textContent = `${rankText} • ${points} pts`;
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
            console.log('Success:', message);
        }

        function hideErrorToast() {
            document.getElementById('errorToast').style.display = 'none';
        }

        function handleInitializationError() {
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

            setTimeout(() => {
                try {
                    switchView('list');
                    updateStoreDisplay();
                } catch (e) {
                    console.error('Fallback failed:', e);
                }
            }, 1000);
        }

        // Modal event handlers
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('modal-overlay')) {
                closeStoreModal();
            }
        });

        // Handle page visibility changes
        document.addEventListener('visibilitychange', function () {
            if (!document.hidden && app.map) {
                app.map.resize();
            }
        });

        // Debug function for testing location
        window.testLocation = function() {
            console.log('🧪 Testing location...');
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        console.log('✅ Test location success:');
                        console.log('Latitude:', position.coords.latitude);
                        console.log('Longitude:', position.coords.longitude);
                        console.log('Accuracy:', position.coords.accuracy, 'meters');
                        console.log('Timestamp:', new Date(position.timestamp).toLocaleString());
                        updateLocationDebugInfo(position.coords);
                    },
                    function(error) {
                        console.error('❌ Test location error:', error);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 15000,
                        maximumAge: 0
                    }
                );
            } else {
                console.error('❌ Geolocation not supported');
            }
        };

        // Make functions available globally for debugging
        window.showLocationDebug = showLocationDebug;
        window.hideLocationDebug = hideLocationDebug;
    </script>
@endsection