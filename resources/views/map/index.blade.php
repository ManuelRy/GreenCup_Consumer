@extends('master')

@section('content')
  <div class="fullscreen-map-container">
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
        <button id="viewToggle" class="view-toggle-btn">
          <span id="toggleIcon">📋🗺️</span>
          <span id="toggleText">List</span>
        </button>
      </div>
      <div class="filter-controls">
        <select id="sortSelect" class="sort-select">
          <option value="nearest">📍 Nearest First</option>
          <option value="farthest">📍 Farthest First</option>
          <option value="name">🔤 Name A-Z</option>
          <option value="rank">🏆 Highest Score</option>
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
        <div id="sortLegend" class="legend-item sort-legend" style="display: none;">
          <div class="legend-marker sort-number">1</div>
          <span id="sortLegendText">Top Results</span>
        </div>
      </div>

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
          <div class="modal-header-content">
            <div class="modal-store-avatar" id="modalStoreAvatar">
              <span id="modalAvatarFallback">S</span>
            </div>
            <h3 id="storeName">Store Details</h3>
          </div>
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

          <!-- Store Photo Gallery -->
          <div class="store-gallery" id="storeGallery" style="display: none;">
            <div class="gallery-header">
              <h4>📸 Store Gallery</h4>
              <button id="seeMoreBtn" class="see-more-btn" onclick="viewFullGallery()" style="display: none;">
                View All Photos
              </button>
            </div>
            <div class="gallery-container" id="galleryContainer">
              <!-- Photos will be dynamically loaded here -->
            </div>
          </div>
          <div class="store-items" id="storeItems" style="display: none;">
            <h4>🛍️ Available Items</h4>
            <div class="items-grid" id="itemsContainer">
              <!-- Items will be populated dynamically -->
            </div>
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
      background-color: #1a1a1a;
      padding: 15px 20px;
      color: white;
      z-index: 1001;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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

    /* Fixed Search Section - Responsive */
    .search-section-fixed {
      position: fixed;
      top: var(--navbar-height);
      left: 0;
      right: 0;
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.98));
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      padding: 12px 20px;
      display: flex;
      gap: 12px;
      z-index: 1001;
      box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
      border-bottom: 1px solid rgba(226, 232, 240, 0.5);
      min-height: 60px;
      box-sizing: border-box;
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

    /* Fixed Controls - Responsive */
    .toggle-controls-fixed {
      position: fixed;
      top: calc(var(--navbar-height) + 72px);
      left: 0;
      right: 0;
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.98));
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      padding: 12px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      z-index: 1001;
      border-bottom: 1px solid rgba(226, 232, 240, 0.5);
      box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
      min-height: 60px;
      box-sizing: border-box;
      overflow: visible;
    }

    .view-options {
      display: flex;
      background: rgba(248, 249, 250, 0.8);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: 16px;
      padding: 4px;
      border: 1px solid rgba(226, 232, 240, 0.5);
    }

    .view-toggle-btn {
      background: none;
      border: none;
      padding: 8px 16px;
      border-radius: 12px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 6px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      color: #64748b;
      min-height: 36px;
    }

    .view-toggle-btn:hover {
      background: rgba(241, 245, 249, 0.8);
      color: #334155;
    }

    .view-toggle-btn.active {
      background: linear-gradient(135deg, #22c55e, #16a34a);
      color: white;
      box-shadow: 0 2px 8px rgba(34, 197, 94, 0.3);
    }

    .filter-controls {
      display: flex;
      gap: 10px;
      position: relative;
      z-index: 1002;
    }

    .sort-select,
    .radius-select {
      padding: 8px 14px;
      border: 2px solid rgba(226, 232, 240, 0.6);
      border-radius: 12px;
      font-size: 13px;
      font-weight: 500;
      outline: none;
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      color: #334155;
      min-height: 36px;
      box-sizing: border-box;
      position: relative;
      z-index: 1050;
    }

    .sort-select:focus,
    .radius-select:focus {
      border-color: #22c55e;
      box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
      background: rgba(255, 255, 255, 1);
      transform: translateY(-1px);
      z-index: 1060;
    }

    .sort-select:hover,
    .radius-select:hover {
      border-color: #94a3b8;
      background: rgba(255, 255, 255, 1);
    }

    /* Full Screen Map View - Responsive */
    .fullscreen-map-view {
      position: absolute;
      top: calc(var(--navbar-height) + 144px);
      left: 0;
      right: 0;
      bottom: 0;
      overflow: hidden;
    }

    .fullscreen-mapbox-map {
      width: 100%;
      height: 100%;
    }

    /* Full Screen List View - Responsive */
    .fullscreen-list-view {
      position: absolute;
      top: calc(var(--navbar-height) + 144px);
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
      bottom: 48px;
      left: 16px;
      background: #fff;
      padding: 8px 14px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.10);
      z-index: 1000;
      font-size: 13px;
    }

    .legend-item {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 4px;
      word-break: break-word;
    }

    .legend-item:last-child {
      margin-bottom: 0;
    }

    @media (max-width: 600px) {
      .map-legend {
        left: 8px;
        bottom: 32px;
        padding: 7px 10px;
        font-size: 13px;
      }

      .legend-item {
        gap: 6px;
        font-size: 13px;
      }
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

    .legend-marker.sort-number {
      background: linear-gradient(135deg, #007bff, #0056b3);
      color: white;
      font-size: 8px;
      font-weight: bold;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .sort-legend {
      border-top: 1px solid #e0e0e0;
      margin-top: 4px;
      padding-top: 4px;
      animation: legendFadeIn 0.3s ease-out;
    }

    @keyframes legendFadeIn {
      0% {
        opacity: 0;
        transform: translateY(-5px);
      }

      100% {
        opacity: 1;
        transform: translateY(0);
      }
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
      overflow: hidden;
    }

    .store-card-avatar .avatar-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
    }

    .store-card-avatar .avatar-fallback {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      height: 100%;
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

    .store-score {
      background: linear-gradient(135deg, #2E8B57, #3CB371);
      color: white;
      padding: 2px 6px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 10px;
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
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }

    /* Store Markers - Enhanced with better positioning */
    .store-marker {
      position: relative;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      border: 3px solid;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
      cursor: pointer;
      transition: all 0.3s ease;
      overflow: visible;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .store-marker.platinum {
      border-color: #9B59B6;
      box-shadow: 0 3px 10px rgba(155, 89, 182, 0.6);
    }

    .store-marker.gold {
      border-color: #FFD700;
      box-shadow: 0 3px 10px rgba(255, 215, 0, 0.6);
    }

    .store-marker.silver {
      border-color: #C0C0C0;
      box-shadow: 0 3px 10px rgba(192, 192, 192, 0.6);
    }

    .store-marker.bronze {
      border-color: #CD7F32;
      box-shadow: 0 3px 10px rgba(205, 127, 50, 0.6);
    }

    .store-marker.standard {
      border-color: #2E8B57;
      box-shadow: 0 3px 10px rgba(46, 139, 87, 0.6);
    }

    .store-marker:hover {
      transform: scale(1.15);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4);
      z-index: 100;
    }

    .marker-initial {
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, #2E8B57, #3CB371);
      color: white;
      font-size: 14px;
      font-weight: bold;
      display: flex;
      align-items: center;
      justify-content: center;
      text-transform: uppercase;
      border-radius: 50%;
      position: relative;
      z-index: 1;
    }

    .rank-crown-marker {
      position: absolute;
      top: -4px;
      right: -4px;
      width: 16px;
      height: 16px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 9px;
      border: 2px solid white;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
      z-index: 2;
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

    .rank-crown-marker.standard {
      background: linear-gradient(135deg, #2E8B57, #3CB371);
      color: white;
    }

    /* Sort Order Indicators on Map - Better positioning */
    .sort-indicator {
      position: absolute;
      top: -6px;
      left: -6px;
      width: 18px;
      height: 18px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 10px;
      font-weight: bold;
      color: white;
      border: 2px solid white;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
      z-index: 3;
      animation: sortIndicatorAppear 0.4s ease-out;
    }

    @keyframes sortIndicatorAppear {
      0% {
        opacity: 0;
        transform: scale(0.5);
      }

      50% {
        transform: scale(1.1);
      }

      100% {
        opacity: 1;
        transform: scale(1);
      }
    }

    .sort-indicator.distance-sort {
      background: linear-gradient(135deg, #007bff, #0056b3);
    }

    .sort-indicator.rank-sort {
      background: linear-gradient(135deg, #ffc107, #e0a800);
      color: #333;
    }

    .sort-indicator.popular-sort {
      background: linear-gradient(135deg, #dc3545, #c82333);
    }

    /* Modal Styles - Responsive */
    .store-modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      z-index: 20000;
      /* Increased for higher focus */
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 32px 20px 32px 20px;
      background: rgba(0, 0, 0, 0.55);
      backdrop-filter: blur(6px);
      -webkit-backdrop-filter: blur(6px);
      box-sizing: border-box;
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
      border-radius: 22px;
      width: 100%;
      max-width: 440px;
      max-height: 80vh;
      /* Reduce max height for better fit */
      overflow-y: auto;
      position: relative;
      animation: modalSlideUp 0.3s ease;
      margin: 32px auto 24px auto;
      /* Add bottom margin */
      box-shadow: 0 8px 40px rgba(0, 0, 0, 0.18);
      border: 1.5px solid #e9ecef;
    }

    @media (min-width: 600px) {
      .modal-content {
        margin-top: calc(var(--navbar-height, 70px) + 24px);
        margin-bottom: 40px;
        /* More bottom margin on desktop */
        max-height: 90vh;
        /* Even shorter on desktop */
      }
    }

    @media (min-width: 600px) {
      .modal-content {
        margin-top: calc(var(--navbar-height, 70px) + 24px);
        /* Use navbar height if defined, else fallback */
      }
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

    .modal-header-content {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .modal-store-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.2);
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .modal-store-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
    }

    .modal-store-avatar span {
      font-weight: 600;
      font-size: 16px;
      color: white;
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

    /* Store Gallery Styles */
    .store-gallery {
      background: #f8f9fa;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
    }

    .store-gallery h4 {
      margin: 0 0 15px 0;
      font-size: 16px;
      color: #333;
    }

    .gallery-container {
      display: flex;
      gap: 8px;
      overflow-x: auto;
      padding-bottom: 5px;
    }

    .gallery-item {
      flex-shrink: 0;
      width: 80px;
      height: 80px;
      border-radius: 8px;
      overflow: hidden;
      position: relative;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .gallery-item:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .gallery-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: all 0.3s ease;
    }

    .gallery-item:hover img {
      filter: brightness(1.1);
    }

    .gallery-no-photos {
      color: #888;
      font-style: italic;
      font-size: 13px;
      text-align: center;
      padding: 10px;
    }

    /* Photo overlay for featured badge */
    .gallery-item.featured::after {
      content: '⭐';
      position: absolute;
      top: 4px;
      right: 4px;
      background: rgba(255, 255, 255, 0.9);
      border-radius: 50%;
      width: 20px;
      height: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 10px;
    }

    /* Gallery header with see more button */
    .gallery-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }

    .gallery-header h4 {
      margin: 0;
      font-size: 16px;
      color: #333;
    }

    .see-more-btn {
      background: linear-gradient(135deg, #2E8B57, #3CB371);
      color: white;
      border: none;
      border-radius: 6px;
      padding: 6px 12px;
      font-size: 12px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .see-more-btn:hover {
      background: linear-gradient(135deg, #3CB371, #2E8B57);
      transform: translateY(-1px);
      box-shadow: 0 2px 8px rgba(46, 139, 87, 0.3);
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

    .items-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 15px;
      margin-top: 15px;
    }

    .item-card {
      background: white;
      border: 1px solid #e0e0e0;
      border-radius: 12px;
      padding: 16px;
      transition: all 0.3s ease;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .item-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    }

    .item-header {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 12px;
    }

    .item-image {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 10px;
      background: #f5f5f5;
      border: 2px solid #e0e0e0;
    }

    .item-details {
      flex: 1;
    }

    .item-name {
      font-weight: 600;
      color: #333;
      margin: 0 0 4px 0;
      font-size: 16px;
    }

    .item-points {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      background: linear-gradient(135deg, #22c55e, #16a34a);
      color: white;
      padding: 6px 12px;
      border-radius: 20px;
      font-weight: 600;
      font-size: 14px;
    }

    .item-points::before {
      content: "⭐";
      font-size: 12px;
    }

    @media (max-width: 768px) {
      .items-grid {
        grid-template-columns: 1fr;
        gap: 12px;
      }

      .item-card {
        padding: 12px;
      }

      .item-image {
        width: 50px;
        height: 50px;
      }
    }

    /* Error Toast - Less Prominent */
    .error-toast {
      position: fixed;
      top: 200px;
      right: 20px;
      background: #6c757d;
      color: white;
      padding: 10px 14px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      gap: 10px;
      z-index: 10001;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
      animation: slideInRight 0.3s ease;
      font-size: 13px;
      max-width: 280px;
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
    @media (max-width: 991.98px) {
      .search-section-fixed {
        padding: 10px 16px;
        min-height: 64px;
      }

      .toggle-controls-fixed {
        padding: 10px 16px;
        min-height: 64px;
        top: calc(var(--navbar-height) + 76px);
      }

      .fullscreen-map-view,
      .fullscreen-list-view {
        top: calc(var(--navbar-height) + 152px);
      }
    }

    @media (max-width: 767.98px) {
      .search-section-fixed {
        flex-direction: column;
        gap: 8px;
        padding: 8px 12px;
        min-height: 80px;
      }

      .location-btn {
        align-self: flex-start;
      }

      .toggle-controls-fixed {
        flex-direction: column;
        gap: 10px;
        align-items: stretch;
        padding: 8px 12px;
        min-height: 80px;
        top: calc(var(--navbar-height) + 92px);
      }

      .filter-controls {
        justify-content: space-between;
      }

      .fullscreen-map-view,
      .fullscreen-list-view {
        top: calc(var(--navbar-height) + 184px);
      }

      /* Modal Responsive - Tablet */
      .store-modal {
        padding: 8px 4px 16px 4px;
        align-items: flex-start;
        padding-top: calc(var(--navbar-height) + 10px);
      }

      .modal-content {
        max-height: calc(100vh - var(--navbar-height) - 20px);
        max-width: calc(100% - 10px);
        margin-top: 0;
        border-radius: 14px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.18);
      }

      .modal-header {
        padding: 14px 14px;
      }

      .modal-header h3 {
        font-size: 15px;
      }

      .modal-body {
        padding: 13px 12px;
      }
    }

    @media (max-width: 575.98px) {
      .search-section-fixed {
        padding: 6px 8px;
        min-height: 90px;
      }

      .toggle-controls-fixed {
        padding: 6px 8px;
        min-height: 90px;
        top: calc(var(--navbar-height) + 102px);
      }

      .fullscreen-map-view,
      .fullscreen-list-view {
        top: calc(var(--navbar-height) + 204px);
      }

      /* Modal Responsive - Mobile */
      .store-modal {
        padding: 4px 2px 12px 2px;
        align-items: flex-start;
        padding-top: calc(var(--navbar-height) + 6px);
      }

      .modal-content {
        max-height: calc(100vh - var(--navbar-height) - 10px);
        max-width: calc(100% - 4px);
        border-radius: 10px;
        margin-top: 0;
        box-shadow: 0 2px 16px rgba(0, 0, 0, 0.18);
      }

      .modal-header {
        padding: 10px 10px;
        border-radius: 10px 10px 0 0;
      }

      .modal-header h3 {
        font-size: 14px;
      }

      .modal-close {
        width: 26px;
        height: 26px;
        font-size: 18px;
      }

      .modal-body {
        padding: 10px 8px;
      }

      .action-buttons {
        flex-direction: column;
        gap: 8px;
      }

      .btn-directions,
      .btn-call,
      .btn-share {
        flex-direction: row;
        justify-content: center;
        padding: 10px;
        font-size: 13px;
      }

      .info-section h4 {
        font-size: 13px;
      }

      .info-row {
        padding: 5px 0;
      }

      .store-rank-display {
        margin-bottom: 12px;
      }

      .rank-badge-large {
        padding: 8px 12px;
        font-size: 13px;
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
      locationRequested: false
    };

    // Initialize application
    document.addEventListener('DOMContentLoaded', function() {
      initializeApp();
    });

    async function initializeApp() {
      try {
        console.log('Initializing full screen map...');
        initializeMap();
        initializeEventListeners();
        initializeStores();
        setupInitialView();

        // Try to get location automatically after a short delay
        setTimeout(() => {
          if (!app.userLocation && !app.locationRequested) {
            getCurrentLocationSilently();
          }
        }, 800);

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
      geolocate.on('geolocate', function(e) {
        console.log('✅ Mapbox geolocation success:', e.coords);
        updateUserLocation(e.coords.latitude, e.coords.longitude, true);
        app.locationRequested = true;
      });

      // Handle geolocation errors (silently)
      geolocate.on('error', function(e) {
        console.log('Mapbox geolocation not available, will use manual location button');
        // Don't show error - user can still use location button
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
        handleSearch();
      });

      // Sort and filter controls
      document.getElementById('sortSelect').addEventListener('change', (e) => {
        console.log('Sort selection changed to:', e.target.value);

        // Add visual feedback
        e.target.style.background = '#e3f2fd';
        setTimeout(() => {
          e.target.style.background = 'white';
        }, 300);

        sortStores(e.target.value);
      });

      document.getElementById('radiusSelect').addEventListener('change', (e) => {
        console.log('Radius selection changed to:', e.target.value);

        app.searchRadius = e.target.value;
        document.getElementById('radiusText').textContent =
          e.target.value === 'all' ? 'all areas' : e.target.value + 'km';

        if (app.userLocation) {
          filterStoresByRadius();
        } else {
          // If no user location, just update the text
          updateStoreDisplay();
        }
      });

      // Location button - Enhanced
      document.getElementById('locationBtn').addEventListener('click', getCurrentLocationWithHighAccuracy);

      // Escape key handler
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          closeStoreModal();
        }
      });
    }

    function initializeStores() {
      app.stores.forEach((store, index) => {
        // Ensure all required properties exist
        store.distance = null;
        store.points_reward = parseFloat(store.points_reward) || parseFloat(store.total_points) || 0;
        store.transaction_count = parseInt(store.transaction_count) || 0;

        console.log(`Store ${index}: ${store.name} - Points: ${store.points_reward} - Transactions: ${store.transaction_count} - Rank: ${getRankText(store.points_reward)}`);
      });

      app.filteredStores = [...app.stores];
      addMarkersToMap();
      updateStoreDisplay();
    }

    function setupInitialView() {
      switchView('map');

      // Hide sort legend initially
      updateSortLegend(null);

      if (app.stores.length === 0) {
        console.log('No stores found in the database');
      } else {
        console.log(`Loaded ${app.stores.length} stores successfully`);

        // Debug first few stores
        app.stores.slice(0, 3).forEach((store, i) => {
          console.log(`Store ${i + 1}: "${store.name}" - Initial: "${store.name.charAt(0).toUpperCase()}" - Rank: ${getRankText(store.points_reward)}`);
        });
      }

      // Check for storeId parameter in URL for auto-selection
      checkForAutoSelection();
    }

    function checkForAutoSelection() {
      const urlParams = new URLSearchParams(window.location.search);
      // Accept both ?store=ID and ?storeId=ID for compatibility
      const storeId = urlParams.get('store') || urlParams.get('storeId');

      if (storeId) {
        console.log(`Auto-selecting store with ID: ${storeId}`);

        // Find the store in the stores array
        const store = app.stores.find(s => s.id == storeId);

        if (store) {
          console.log(`Found store: ${store.name}`);

          // Wait a moment for map to be fully loaded, then show store detail
          setTimeout(() => {
            showStoreDetail(store);

            // Also focus the map on the store location
            if (app.map) {
              app.map.flyTo({
                center: [store.longitude, store.latitude],
                zoom: 15,
                duration: 1500
              });
            }
          }, 1000);
        } else {
          console.log(`Store with ID ${storeId} not found`);
        }
      }
    }

    // ENHANCED LOCATION FUNCTIONS

    // Silent location request (no user feedback)
    async function getCurrentLocationSilently() {
      if (!navigator.geolocation) {
        return;
      }

      try {
        const position = await new Promise((resolve, reject) => {
          navigator.geolocation.getCurrentPosition(
            resolve,
            reject, {
              enableHighAccuracy: false,
              timeout: 8000,
              maximumAge: 300000 // 5 minutes cache
            }
          );
        });

        console.log('📍 Silent location success');
        updateUserLocation(position.coords.latitude, position.coords.longitude, false);

        // Show subtle success indicator
        const locationBtn = document.getElementById('locationBtn');
        locationBtn.style.background = '#28a745';
        setTimeout(() => {
          locationBtn.style.background = '#2E8B57';
        }, 2000);

      } catch (error) {
        // Silent failure - this is normal and expected
        console.log('Silent location not available (normal)');
      }
    }

    // High accuracy location request with user feedback
    async function getCurrentLocationWithHighAccuracy() {
      const locationBtn = document.getElementById('locationBtn');

      // Prevent multiple simultaneous location requests
      if (locationBtn.disabled || app.isLoading) {
        console.log('Location request already in progress, ignoring click');
        return;
      }

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

        updateUserLocation(position.coords.latitude, position.coords.longitude, true);
        app.locationRequested = true;

        // Show success feedback
        locationBtn.innerHTML = '<span>✅</span><span>Location Found</span>';
        setTimeout(() => {
          locationBtn.innerHTML = '<span>📍</span><span>My Location</span>';
        }, 2000);

      } catch (error) {
        console.error('Location request failed:', error);

        let errorMessage = 'Location access is currently unavailable. ';

        switch (error.code) {
          case error.PERMISSION_DENIED:
            errorMessage = 'Location access was denied. You can enable it in your browser settings or search for stores manually.';
            break;
          case error.POSITION_UNAVAILABLE:
            errorMessage = 'Your location could not be determined. You can still search for stores manually.';
            break;
          case error.TIMEOUT:
            errorMessage = 'Location request took too long. You can try again or search manually.';
            break;
          default:
            errorMessage = 'Unable to get your location. You can still search and browse stores manually.';
            break;
        }

        // Show less intrusive error
        showError(errorMessage);

        locationBtn.innerHTML = '<span>📍</span><span>Try Again</span>';
        locationBtn.style.background = '#6c757d';
        setTimeout(() => {
          locationBtn.innerHTML = '<span>📍</span><span>My Location</span>';
          locationBtn.style.background = '#2E8B57';
        }, 4000);
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
        console.error('❌ Invalid coordinates received:', {
          latitude,
          longitude
        });
        showError('Invalid location coordinates received');
        return;
      }

      app.userLocation = {
        latitude,
        longitude
      };

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
      // Prevent changing to the same style or if already loading
      if (button.classList.contains('active') || app.isLoading) {
        console.log('Style already active or map is loading, ignoring click');
        return;
      }

      console.log('Changing map style to:', styleUrl);
      showLoading();

      app.map.setStyle(styleUrl);

      // Update button states
      document.querySelectorAll('.style-btn').forEach(btn => {
        btn.classList.remove('active');
      });
      button.classList.add('active');

      // Wait for style to load before re-adding markers
      app.map.once('styledata', () => {
        console.log('Map style loaded, re-adding markers');
        addMarkersToMap();
        hideLoading();
      });
    }

    function addMarkersToMap() {
      // Remove existing markers cleanly
      app.markers.forEach(marker => {
        marker.remove();
      });
      app.markers = [];

      // Get current sort type for visual indicators
      const currentSort = document.getElementById('sortSelect').value;

      app.filteredStores.forEach((store, index) => {
        try {
          const markerElement = document.createElement('div');
          const rankClass = getRankClass(store.points_reward);
          markerElement.className = `store-marker ${rankClass}`;

          // Create main marker content with store initial
          const markerContent = document.createElement('div');
          markerContent.className = 'marker-initial';
          const storeInitial = (store.name || 'S').charAt(0).toUpperCase();
          markerContent.textContent = storeInitial;
          markerElement.appendChild(markerContent);

          // Add rank crown in top-right corner
          const rankCrown = document.createElement('div');
          rankCrown.className = `rank-crown-marker ${rankClass}`;
          rankCrown.textContent = getRankIcon(store.points_reward);
          markerElement.appendChild(rankCrown);

          // Add sort order indicator ONLY for top 5 stores when actively sorting
          if (index < 5 && currentSort && currentSort !== 'name' && currentSort !== 'nearest') {
            const sortIndicator = document.createElement('div');
            sortIndicator.className = 'sort-indicator';
            sortIndicator.textContent = index + 1;

            // Different colors for different sort types
            if (currentSort === 'farthest') {
              sortIndicator.classList.add('distance-sort');
            } else if (currentSort === 'rank') {
              sortIndicator.classList.add('rank-sort');
            } else if (currentSort === 'popular') {
              sortIndicator.classList.add('popular-sort');
            }

            markerElement.appendChild(sortIndicator);
          }

          // Enhanced title with sort info
          let titleText = `${store.name || 'Unknown Store'} - ${getRankText(store.points_reward)}`;
          if (store.distance !== null && store.distance !== undefined) {
            titleText += ` - ${store.distance.toFixed(1)}km away`;
          }
          if (index < 5 && currentSort && currentSort !== 'name') {
            titleText = `#${index + 1}: ${titleText}`;
          }
          markerElement.title = titleText;

          // Validate coordinates before creating marker
          if (!store.longitude || !store.latitude ||
            Math.abs(store.longitude) > 180 || Math.abs(store.latitude) > 90) {
            console.warn(`Invalid coordinates for store: ${store.name}`, store);
            return;
          }

          // Create Mapbox marker
          const marker = new mapboxgl.Marker(markerElement)
            .setLngLat([store.longitude, store.latitude])
            .addTo(app.map);

          // Add click handler
          markerElement.addEventListener('click', (e) => {
            e.stopPropagation();
            showStoreDetail({
              ...store
            });
          });

          app.markers.push(marker);

        } catch (error) {
          console.error(`Error creating marker for store ${store.name}:`, error);
        }
      });

      console.log(`Successfully added ${app.markers.length} markers to map with sort: ${currentSort}`);
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
                            <div class="store-card-avatar ${rankClass}">
                                ${store.photo_url ?
                                    `<img src="${store.photo_url}" alt="${store.name}" class="avatar-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                     <span class="avatar-fallback" style="display:none;">${store.name.charAt(0)}</span>`
                                    :
                                    `<span class="avatar-fallback">${store.name.charAt(0)}</span>`
                                }
                            </div>
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
                                <span class="store-score">🏆 ${store.points_reward || 0} pts</span>
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

    function getRankClass(points) {
      const numPoints = parseFloat(points) || 0;
      if (numPoints >= 2000) return 'platinum';
      if (numPoints >= 1000) return 'gold';
      if (numPoints >= 500) return 'silver';
      if (numPoints >= 100) return 'bronze';
      return 'standard';
    }

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

    function updateStoreDisplay() {
      updateStoreCount();

      // Always update markers regardless of current view
      addMarkersToMap();

      // Update list view if currently visible
      if (app.currentView === 'list') {
        updateListView();
      }
    }

    function updateStoreCount() {
      document.getElementById('storeCount').textContent = app.filteredStores.length;
    }

    async function handleSearch() {
      const query = document.getElementById('searchInput').value.trim();
      if (!query) {
        // If input is empty, reset to all stores
        app.filteredStores = [...app.stores];
        updateStoreDisplay();
        focusMapOnResults(app.filteredStores);
        return;
      }

      try {
        showLoading();
        const response = await fetch(`/api/stores?search=${encodeURIComponent(query)}`);
        const data = await response.json();

        if (data.success) {
          app.filteredStores = data.data;

          // Ensure new search results have all required properties
          app.filteredStores.forEach(store => {
            store.distance = null;
            store.points_reward = parseFloat(store.points_reward) || parseFloat(store.total_points) || 0;
            store.transaction_count = parseInt(store.transaction_count) || 0;
          });

          // Recalculate distances if user location is available
          if (app.userLocation) {
            app.filteredStores.forEach(store => {
              store.distance = calculateHaversineDistance(
                app.userLocation.latitude,
                app.userLocation.longitude,
                store.latitude,
                store.longitude
              );
            });
          }

          // Re-apply current sort
          const currentSort = document.getElementById('sortSelect').value;
          if (currentSort && currentSort !== 'nearest') {
            sortStores(currentSort);
            // Do not focus map here, let sort logic handle
          } else {
            updateStoreDisplay();
            // Only focus map if not sorting by distance
            focusMapOnResults(app.filteredStores);
          }
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

    // Focus map on search results
    function focusMapOnResults(stores) {
      if (!app.map || !stores) return;
      if (stores.length === 0) {
        showError('No stores found for your search.');
        return;
      }
      if (stores.length === 1) {
        // Center and zoom to the single store
        const s = stores[0];
        if (s.longitude && s.latitude) {
          app.map.flyTo({
            center: [s.longitude, s.latitude],
            zoom: 16
          });
        }
      } else {
        // Fit bounds to all stores
        const bounds = new mapboxgl.LngLatBounds();
        stores.forEach(s => {
          if (s.longitude && s.latitude) {
            bounds.extend([s.longitude, s.latitude]);
          }
        });
        if (!bounds.isEmpty()) {
          app.map.fitBounds(bounds, {
            padding: 80,
            maxZoom: 16
          });
        }
      }
    }

    function clearSearch() {
      console.log('Clearing search and resetting to all stores');

      document.getElementById('searchInput').value = '';
      document.getElementById('clearSearch').style.display = 'none';
      document.getElementById('searchBtn').style.right = '5px';

      // Reset to all stores
      app.filteredStores = [...app.stores];

      // Recalculate distances if needed
      if (app.userLocation) {
        calculateDistances();
      } else {
        // Re-apply current sort without distances
        const currentSort = document.getElementById('sortSelect').value;
        if (currentSort && currentSort !== 'nearest') {
          sortStores(currentSort);
        } else {
          updateStoreDisplay();
          updateSortLegend(currentSort);
        }
      }
    }

    function sortStores(sortType) {
      console.log(`Sorting stores by: ${sortType}`);

      // Show loading briefly for visual feedback
      showLoading();

      setTimeout(() => {
        try {
          switch (sortType) {
            case 'nearest':
              if (!app.userLocation) {
                showError('Enable location to sort by distance');
                hideLoading();
                return;
              }
              app.filteredStores.sort((a, b) => {
                const distanceA = a.distance !== null ? a.distance : Infinity;
                const distanceB = b.distance !== null ? b.distance : Infinity;
                return distanceA - distanceB;
              });
              break;

            case 'farthest':
              if (!app.userLocation) {
                showError('Enable location to sort by distance');
                hideLoading();
                return;
              }
              app.filteredStores.sort((a, b) => {
                const distanceA = a.distance !== null ? a.distance : 0;
                const distanceB = b.distance !== null ? b.distance : 0;
                return distanceB - distanceA;
              });
              break;

            case 'name':
              app.filteredStores.sort((a, b) => {
                const nameA = (a.name || '').toLowerCase();
                const nameB = (b.name || '').toLowerCase();
                return nameA.localeCompare(nameB);
              });
              break;

            case 'rank':
              app.filteredStores.sort((a, b) => {
                const pointsA = parseFloat(a.points_reward) || 0;
                const pointsB = parseFloat(b.points_reward) || 0;
                return pointsB - pointsA; // Highest score first
              });

              // Debug: Show top scores after sorting
              console.log('Top 5 stores by score:', app.filteredStores.slice(0, 5).map(s => ({
                name: s.name,
                score: s.points_reward || 0,
                rank: s.rank_class
              })));
              break;

            case 'popular':
              app.filteredStores.sort((a, b) => {
                const countA = parseInt(a.transaction_count) || 0;
                const countB = parseInt(b.transaction_count) || 0;
                return countB - countA; // Highest first
              });
              break;

            default:
              console.log('Unknown sort type:', sortType);
              hideLoading();
              return;
          }

          console.log(`Successfully sorted ${app.filteredStores.length} stores by ${sortType}`);
          updateStoreDisplay();

          // Update map legend for sorting
          updateSortLegend(sortType);

          // For distance-based sorting, focus map on nearest stores
          if ((sortType === 'nearest' || sortType === 'farthest') && app.userLocation && app.filteredStores.length > 0) {
            focusMapOnTopResults(sortType);
          }

        } catch (error) {
          console.error('Error sorting stores:', error);
          showError('Unable to sort stores. Please try again.');
        } finally {
          hideLoading();
        }
      }, 100); // Small delay for visual feedback
    }

    function calculateDistances() {
      if (!app.userLocation) return;

      console.log('Calculating distances for all stores...');

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

      // Re-apply current sort after calculating distances
      const currentSort = document.getElementById('sortSelect').value;
      console.log('Re-applying sort after distance calculation:', currentSort);

      if (currentSort === 'nearest' || currentSort === 'farthest') {
        sortStores(currentSort);
      } else {
        updateStoreDisplay();
        updateSortLegend(currentSort);
      }
    }

    function filterStoresByRadius() {
      console.log('Filtering stores by radius:', app.searchRadius);

      if (!app.userLocation || app.searchRadius === 'all') {
        app.filteredStores = [...app.stores];
      } else {
        const radiusKm = parseFloat(app.searchRadius);
        app.filteredStores = app.stores.filter(store => {
          return store.distance !== null && store.distance <= radiusKm;
        });
      }

      console.log(`Filtered to ${app.filteredStores.length} stores within radius`);

      // Re-apply current sort after filtering
      const currentSort = document.getElementById('sortSelect').value;
      if (currentSort && currentSort !== 'nearest') { // nearest is default, skip re-sort
        console.log('Re-applying sort after radius filter:', currentSort);
        sortStores(currentSort);
      } else {
        updateStoreDisplay();
        updateSortLegend(currentSort);
      }
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

    function focusMapOnTopResults(sortType) {
      if (!app.map || !app.filteredStores || app.filteredStores.length === 0) return;

      try {
        // Get top 3-5 results to focus on
        const topStores = app.filteredStores.slice(0, Math.min(5, app.filteredStores.length));

        if (topStores.length === 1) {
          // Single store - center on it
          app.map.flyTo({
            center: [topStores[0].longitude, topStores[0].latitude],
            zoom: 14,
            duration: 1500
          });
        } else if (topStores.length > 1) {
          // Multiple stores - fit bounds to show all top results
          const coordinates = topStores.map(store => [store.longitude, store.latitude]);

          // Add user location if available for distance sorts
          if ((sortType === 'nearest' || sortType === 'farthest') && app.userLocation) {
            coordinates.push([app.userLocation.longitude, app.userLocation.latitude]);
          }

          const bounds = coordinates.reduce((bounds, coord) => {
            return bounds.extend(coord);
          }, new mapboxgl.LngLatBounds(coordinates[0], coordinates[0]));

          app.map.fitBounds(bounds, {
            padding: {
              top: 50,
              bottom: 50,
              left: 50,
              right: 50
            },
            maxZoom: 15,
            duration: 1500
          });
        }

        console.log(`Map focused on top ${topStores.length} results for ${sortType} sort`);
      } catch (error) {
        console.error('Error focusing map on results:', error);
      }
    }

    function updateSortLegend(sortType) {
      const sortLegend = document.getElementById('sortLegend');
      const sortLegendText = document.getElementById('sortLegendText');

      if (!sortType || sortType === 'name') {
        sortLegend.style.display = 'none';
        return;
      }

      // Show legend with appropriate text
      sortLegend.style.display = 'flex';

      switch (sortType) {
        case 'nearest':
          sortLegendText.textContent = 'Nearest First';
          break;
        case 'farthest':
          sortLegendText.textContent = 'Farthest First';
          break;
        case 'rank':
          sortLegendText.textContent = 'Best Ranked';
          break;
        case 'popular':
          sortLegendText.textContent = 'Most Popular';
          break;
        default:
          sortLegendText.textContent = 'Top Results';
      }
    }

    async function showStoreDetail(store) {
      app.selectedStore = JSON.parse(JSON.stringify(store));

      try {
        const response = await fetch(`/api/store/${store.id}/details`);
        const data = await response.json();

        console.log(data);

        if (data.success) {
          // Preserve the distance from the original store object
          const storeDetails = {
            ...data.data,
            points_reward: app.selectedStore.points_reward,
            distance: app.selectedStore.distance
          };
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

      // Update modal avatar
      const modalAvatar = document.getElementById('modalStoreAvatar');
      const avatarFallback = document.getElementById('modalAvatarFallback');

      // Remove any existing image
      const existingImg = modalAvatar.querySelector('img');
      if (existingImg) {
        existingImg.remove();
      }

      if (store.photo_url) {
        const avatarImg = document.createElement('img');
        avatarImg.src = store.photo_url;
        avatarImg.alt = store.name;
        avatarImg.onerror = function() {
          this.style.display = 'none';
          avatarFallback.style.display = 'flex';
          avatarFallback.textContent = store.name.charAt(0).toUpperCase();
        };
        avatarImg.onload = function() {
          avatarFallback.style.display = 'none';
        };
        modalAvatar.appendChild(avatarImg);
      } else {
        avatarFallback.style.display = 'flex';
        avatarFallback.textContent = store.name.charAt(0).toUpperCase();
      }
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

      // Populate photo gallery
      populateStoreGallery(store.photos || [], store.total_photos || 0);
      populateStoreItems(store.items || []);
    }

    function populateStoreGallery(photos, totalPhotos) {
      const gallerySection = document.getElementById('storeGallery');
      const galleryContainer = document.getElementById('galleryContainer');
      const seeMoreBtn = document.getElementById('seeMoreBtn');

      // Clear existing photos
      galleryContainer.innerHTML = '';

      if (!photos || photos.length === 0) {
        gallerySection.style.display = 'none';
        seeMoreBtn.style.display = 'none';
        return;
      }

      // Show gallery section
      gallerySection.style.display = 'block';

      // Show "View All Photos" button with count
      seeMoreBtn.style.display = 'inline-block';
      if (totalPhotos > photos.length) {
        seeMoreBtn.textContent = `View All ${totalPhotos} Photos`;
      } else {
        seeMoreBtn.textContent = `View Photos (${totalPhotos})`;
      }

      // Add photos (last 3 images - they should already be limited by backend)
      photos.forEach((photo, index) => {
        const galleryItem = document.createElement('div');
        galleryItem.className = `gallery-item ${photo.is_featured ? 'featured' : ''}`;

        const img = document.createElement('img');
        img.src = photo.url;
        img.alt = photo.caption || `Store photo ${index + 1}`;
        img.loading = 'lazy';

        // Add error handling for broken images
        img.onerror = function() {
          galleryItem.style.display = 'none';
        };

        // Add click handler to open full image
        galleryItem.addEventListener('click', (e) => {
          e.stopPropagation();
          openPhotoModal(photo);
        });

        galleryItem.appendChild(img);
        galleryContainer.appendChild(galleryItem);
      });
    }

    function populateStoreItems(items) {
      const itemsContainer = document.getElementById('storeItems');
      const itemsGrid = document.getElementById('itemsContainer');
      itemsGrid.innerHTML = '';

      if (items && items.length > 0) {
        items.forEach(item => {
          // Create item card
          const card = document.createElement('div');
          card.className = 'item-card';

          // Create item header with image and details
          const header = document.createElement('div');
          header.className = 'item-header';

          const img = document.createElement('img');
          img.src = item.image_url || '/images/placeholder.png';
          img.alt = item.name;
          img.className = 'item-image';
          img.onerror = function() {
            this.src = '/images/placeholder.png';
          };

          const details = document.createElement('div');
          details.className = 'item-details';

          const name = document.createElement('h5');
          name.className = 'item-name';
          name.textContent = item.name;

          const points = document.createElement('div');
          points.className = 'item-points';
          points.textContent = `${item.points_per_unit} pts`;

          // Assemble the card
          details.appendChild(name);
          details.appendChild(points);
          header.appendChild(img);
          header.appendChild(details);
          card.appendChild(header);

          itemsGrid.appendChild(card);
        });

        itemsContainer.style.display = 'block';
      } else {
        itemsContainer.style.display = 'none';
      }
    }

    function openPhotoModal(photo) {
      // Create a simple photo modal
      const modal = document.createElement('div');
      modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0,0,0,0.9);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10000;
                cursor: pointer;
            `;

      const img = document.createElement('img');
      img.src = photo.url;
      img.style.cssText = `
                max-width: 90vw;
                max-height: 90vh;
                object-fit: contain;
                border-radius: 8px;
            `;

      modal.appendChild(img);

      // Close on click
      modal.addEventListener('click', () => {
        document.body.removeChild(modal);
      });

      document.body.appendChild(modal);
    }

    function viewFullGallery() {
      if (!app.selectedStore) {
        showError('No store selected');
        return;
      }

      // Navigate to the gallery with seller ID parameter in the same tab
      const galleryUrl = `/gallery?seller=${app.selectedStore.id}`;
      window.location.href = galleryUrl;
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
      if (app.isLoading) {
        console.log('Loading already active, ignoring showLoading call');
        return;
      }

      app.isLoading = true;
      document.getElementById('loadingIndicator').style.display = 'flex';
      console.log('Loading indicator shown');
    }

    function hideLoading() {
      app.isLoading = false;
      document.getElementById('loadingIndicator').style.display = 'none';
      console.log('Loading indicator hidden');
    }

    function showError(message) {
      document.getElementById('errorMessage').textContent = message;
      document.getElementById('errorToast').style.display = 'flex';
      // Auto-hide after 3 seconds instead of 5
      setTimeout(() => {
        hideErrorToast();
      }, 3000);
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
    document.addEventListener('click', function(e) {
      if (e.target.classList.contains('modal-overlay')) {
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
