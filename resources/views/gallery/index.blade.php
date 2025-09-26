@extends('master')

@section('content')
  <div class="gallery-container">
    <!-- Mobile Stores Toggle -->
    <div class="d-lg-none mb-3">
      <button class="btn btn-success w-100 d-flex align-items-center justify-content-center gap-2" data-bs-toggle="offcanvas" data-bs-target="#storesPanel" aria-controls="storesPanel">
        <i class="bi bi-shop"></i>
        <span>Browse Stores</span>
        <span class="badge bg-light text-success" id="mobileStoresCount">0</span>
      </button>
    </div>

    <div class="row g-3">
      <!-- Stores Panel -->
      <div class="col-lg-4">
        <!-- Desktop Panel -->
        <div class="card border-0 shadow-sm d-none d-lg-block">
          <div class="card-header bg-white border-bottom-0 py-3">
            <div class="d-flex align-items-center justify-content-between">
              <h5 class="mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-shop text-success"></i>
                Stores Directory
              </h5>
              <span class="badge bg-success" id="storesCount">0</span>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="p-3 border-bottom">
              <div class="mb-3">
                <div class="input-group">
                  <span class="input-group-text border-end-0 bg-white">
                    <i class="bi bi-search text-muted"></i>
                  </span>
                  <input type="text" class="form-control border-start-0" id="storeSearch" placeholder="Search stores...">
                </div>
              </div>
              <select class="form-select" id="storeFilter">
                <option value="all">All Ranks</option>
                <option value="platinum">Platinum</option>
                <option value="gold">Gold</option>
                <option value="silver">Silver</option>
                <option value="bronze">Bronze</option>
                <option value="standard">Standard</option>
              </select>
            </div>
            <div class="stores-list" style="max-height: 70vh; overflow-y: auto;" id="storesList">
              <div class="text-center p-4">
                <div class="spinner-border text-success" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading stores...</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Mobile Offcanvas -->
        <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="storesPanel">
          <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title d-flex align-items-center gap-2">
              <i class="bi bi-shop text-success"></i>
              Stores Directory
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
          </div>
          <div class="offcanvas-body p-0">
            <div class="p-3 border-bottom">
              <div class="mb-3">
                <div class="input-group">
                  <span class="input-group-text border-end-0 bg-white">
                    <i class="bi bi-search text-muted"></i>
                  </span>
                  <input type="text" class="form-control border-start-0" id="mobileStoreSearch" placeholder="Search stores...">
                </div>
              </div>
              <select class="form-select" id="mobileStoreFilter">
                <option value="all">All Ranks</option>
                <option value="platinum">Platinum</option>
                <option value="gold">Gold</option>
                <option value="silver">Silver</option>
                <option value="bronze">Bronze</option>
                <option value="standard">Standard</option>
              </select>
            </div>
            <div class="mobile-stores-list" style="height: calc(100vh - 180px); overflow-y: auto;" id="mobileStoresList">
              <!-- Stores will be populated here -->
            </div>
          </div>
        </div>
      </div>

      <!-- Posts Panel -->
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
          <!-- Default State -->
          <div class="default-state text-center p-5" id="defaultState">
            <div class="mb-4">
              <i class="bi bi-images display-1 text-muted opacity-50"></i>
            </div>
            <h4 class="text-muted">Select a Store</h4>
            <p class="text-muted mb-0">Choose a store from the directory to view their photo gallery</p>
          </div>

          <!-- Selected Store Content -->
          <div class="selected-content" id="selectedContent" style="display: none;">
            <div class="card-header bg-white border-bottom py-3">
              <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="selected-store-info d-flex align-items-center gap-3" id="selectedStoreInfo">
                  <!-- Dynamic content -->
                </div>
                <span class="badge bg-success" id="postsCount">0 posts</span>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="posts-grid p-3" id="postsGrid">
                <!-- Posts will be populated here -->
              </div>
              <div class="text-center p-3 d-none" id="loadMoreSection">
                <button class="btn btn-outline-success" id="loadMoreBtn">
                  <span class="load-text">Load More Posts</span>
                  <span class="load-spinner d-none">
                    <span class="spinner-border spinner-border-sm me-1"></span>
                    Loading...
                  </span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Post Modal -->
  <div class="modal fade" id="postModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <div class="modal-header border-bottom-0">
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-0">
          <div class="row g-0">
            <div class="col-lg-8">
              <div class="ratio ratio-1x1 bg-dark">
                <img id="modalImage" class="img-fluid rounded-start" style="object-fit: contain;">
              </div>
            </div>
            <div class="col-lg-4">
              <div class="p-4 h-100 d-flex flex-column">
                <div class="store-profile mb-3" id="modalStoreProfile">
                  <!-- Dynamic content -->
                </div>
                <div class="post-content flex-grow-1">
                  <h6 id="modalPostTitle" class="fw-bold mb-2"></h6>
                  <p id="modalPostCaption" class="text-muted small mb-3"></p>
                  <div class="post-meta">
                    <small class="text-muted d-flex align-items-center gap-1 mb-2">
                      <i class="bi bi-geo-alt"></i>
                      <span id="modalStoreLocation"></span>
                    </small>
                    <small class="text-muted d-flex align-items-center gap-1 mb-2">
                      <i class="bi bi-star-fill"></i>
                      <span id="modalStoreRank"></span>
                    </small>
                    <small class="text-muted d-flex align-items-center gap-1">
                      <i class="bi bi-clock"></i>
                      <span id="modalPostDate"></span>
                    </small>
                  </div>
                </div>
                <div class="modal-actions d-grid gap-2">
                  <button class="btn btn-success" onclick="visitStore()">
                    <i class="bi bi-shop me-1"></i>Visit Store
                  </button>
                  <button class="btn btn-outline-secondary" onclick="sharePost()">
                    <i class="bi bi-share me-1"></i>Share
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    .gallery-container {
      padding: 1rem;
      max-width: 1400px;
      margin: 0 auto;
    }

    .store-card {
      border: none;
      border-radius: 12px;
      transition: all 0.2s ease;
      cursor: pointer;
      margin-bottom: 0.75rem;
    }

    .store-card:hover {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      transform: translateY(-1px);
    }

    .store-card.selected {
      border: 2px solid #198754;
      box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
    }

    .store-avatar {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      color: white;
      position: relative;
    }

    .store-avatar.platinum {
      background: linear-gradient(135deg, #9B59B6, #8E44AD);
    }

    .store-avatar.gold {
      background: linear-gradient(135deg, #FFD700, #FFA500);
      color: #333;
    }

    .store-avatar.silver {
      background: linear-gradient(135deg, #C0C0C0, #A8A8A8);
      color: #333;
    }

    .store-avatar.bronze {
      background: linear-gradient(135deg, #CD7F32, #B87333);
    }

    .store-avatar.standard {
      background: linear-gradient(135deg, #198754, #20c997);
    }

    .rank-badge {
      position: absolute;
      top: -5px;
      right: -5px;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      background: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 10px;
      border: 2px solid #fff;
    }

    .posts-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
      gap: 1rem;
      min-height: 400px;
    }

    .post-card {
      background: white;
      border-radius: 12px;
      overflow: hidden;
      transition: all 0.2s ease;
      cursor: pointer;
      border: 1px solid #e9ecef;
    }

    .post-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .post-image {
      width: 100%;
      height: 180px;
      object-fit: cover;
      background: #f8f9fa;
    }

    .featured-badge {
      position: absolute;
      top: 8px;
      left: 8px;
      background: linear-gradient(135deg, #FFD700, #FFA500);
      color: #333;
      padding: 2px 6px;
      border-radius: 6px;
      font-size: 10px;
      font-weight: 600;
    }

    .blurred {
      filter: blur(8px);
    }

    @media (max-width: 576px) {
      .posts-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
      }

      .post-image {
        height: 140px;
      }

      .gallery-container {
        padding: 0.5rem;
      }
    }

    @media (max-width: 768px) {
      .modal-dialog {
        margin: 0.5rem;
      }

      .modal-body .row {
        flex-direction: column;
      }

      .modal-body .col-lg-8 {
        order: 1;
      }

      .modal-body .col-lg-4 {
        order: 2;
      }
    }
  </style>

  <script>
    // Application State
    const app = {
      stores: [],
      filteredStores: [],
      selectedStore: null,
      posts: [],
      currentPage: 1,
      hasMorePosts: false,
      isLoading: false
    };

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
      initializeApp();
    });

    async function initializeApp() {
      try {
        await loadStores();
        initializeEventListeners();

        // Check for URL parameters to auto-select a store
        const urlParams = new URLSearchParams(window.location.search);
        const sellerId = urlParams.get('seller');

        if (sellerId) {
          // Find and select the store from URL parameter
          const targetStore = app.stores.find(store => store.id == sellerId);
          if (targetStore) {
            console.log('Auto-selecting store from URL:', targetStore.name);
            setTimeout(() => {
              selectStore(parseInt(sellerId));
            }, 500); // Small delay to ensure stores are rendered
          }
        }

        console.log('Gallery initialized successfully');
      } catch (error) {
        console.error('Failed to initialize app:', error);
        showErrorState('Failed to load stores');
      }
    }

    function initializeEventListeners() {
      // Search functionality
      ['storeSearch', 'mobileStoreSearch'].forEach(id => {
        const input = document.getElementById(id);
        if (input) {
          input.addEventListener('input', debounce(handleSearch, 300));
        }
      });

      // Filter functionality
      ['storeFilter', 'mobileStoreFilter'].forEach(id => {
        const select = document.getElementById(id);
        if (select) {
          select.addEventListener('change', handleFilter);
        }
      });

      // Load more posts
      const loadMoreBtn = document.getElementById('loadMoreBtn');
      if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', loadMorePosts);
      }

      // Sync search and filter between desktop and mobile
      syncInputs();
    }

    function applySearchAndFilter() {
      const query = (document.getElementById('storeSearch')?.value || document.getElementById('mobileStoreSearch')?.value || '').toLowerCase().trim();
      const filterValue = document.getElementById('storeFilter')?.value || document.getElementById('mobileStoreFilter')?.value || 'all';

      let filtered = [...app.stores];

      if (query) {
        filtered = filtered.filter(store =>
          store.name.toLowerCase().includes(query) ||
          (store.address && store.address.toLowerCase().includes(query))
        );
      }

      if (filterValue !== 'all') {
        filtered = filtered.filter(store => store.rank_class === filterValue);
      }

      app.filteredStores = filtered;
      renderStores();
      updateStoresCount();
    }

    function syncInputs() {
      const desktopSearch = document.getElementById('storeSearch');
      const mobileSearch = document.getElementById('mobileStoreSearch');
      const desktopFilter = document.getElementById('storeFilter');
      const mobileFilter = document.getElementById('mobileStoreFilter');

      if (desktopSearch && mobileSearch) {
        desktopSearch.addEventListener('input', () => {
          mobileSearch.value = desktopSearch.value;
          applySearchAndFilter();
        });
        mobileSearch.addEventListener('input', () => {
          desktopSearch.value = mobileSearch.value;
          applySearchAndFilter();
        });
      }

      if (desktopFilter && mobileFilter) {
        desktopFilter.addEventListener('change', () => {
          mobileFilter.value = desktopFilter.value;
          applySearchAndFilter();
        });
        mobileFilter.addEventListener('change', () => {
          desktopFilter.value = mobileFilter.value;
          applySearchAndFilter();
        });
      }
    }

    async function loadStores() {
      try {
        showStoresLoading(true);

        const response = await fetch('/public-api/stores');
        if (!response.ok) throw new Error(`HTTP ${response.status}`);

        const data = await response.json();

        if (data.success) {
          app.stores = (data.data || []).map(store => {
            // Use the total_points directly from the API response
            const actualPoints = parseInt(store.total_points) || 0;

            const processedStore = {
              id: store.id,
              name: store.name,
              address: store.address,
              phone: store.phone,
              image: store.image,
              total_points: actualPoints,
              points_reward: actualPoints,
              transaction_count: store.transaction_count || 0,
              rank_class: getRankClass(actualPoints),
              rank_text: getRankText(actualPoints),
              rank_icon: getRankIcon(actualPoints)
            };

            return processedStore;
          });

          // Set initial filtered stores to show all stores by default
          app.filteredStores = [...app.stores];

          // Render stores and update counts
          renderStores();
          updateStoresCount();

        } else {
          throw new Error(data.message || 'Failed to load stores');
        }
      } catch (error) {
        console.error('Error loading stores:', error);
        showErrorState(error.message);
      } finally {
        showStoresLoading(false);
      }
    }

    function showStoresLoading(show) {
      const loadingHTML = `
        <div class="text-center p-4">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading stores...</p>
        </div>
    `;

      const emptyHTML = '<div class="p-4"></div>';

      // Don't clear content if stores are already rendered
      if (!show && app.filteredStores && app.filteredStores.length > 0) {
        return;
      }

      document.getElementById('storesList').innerHTML = show ? loadingHTML : emptyHTML;
      document.getElementById('mobileStoresList').innerHTML = show ? loadingHTML : emptyHTML;
    }

    function renderStores() {
      if (app.filteredStores.length === 0) {
        const emptyHTML = `
            <div class="text-center p-4 text-muted">
                <p>No stores found</p>
            </div>
        `;
        document.getElementById('storesList').innerHTML = emptyHTML;
        document.getElementById('mobileStoresList').innerHTML = emptyHTML;
        return;
      }

      const storesHTML = app.filteredStores.map(store => `
        <div class="store-card p-3" onclick="selectStore(${store.id})" data-store-id="${store.id}">
            <div class="d-flex align-items-center">
                <div class="store-avatar ${store.rank_class} me-3">
                    ${store.name.charAt(0).toUpperCase()}
                    <div class="rank-badge">
                        ${store.rank_icon}
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-1">${store.name}</h6>
                    <small class="text-muted d-block">${store.address || 'No address'}</small>
                    <small class="text-muted">${store.total_points} pts • ${store.rank_text}</small>
                </div>
            </div>
        </div>
    `).join('');

      document.getElementById('storesList').innerHTML = storesHTML;
      document.getElementById('mobileStoresList').innerHTML = storesHTML;
    }

    function selectStore(storeId) {
      // Update UI selection
      document.querySelectorAll('.store-card').forEach(card => {
        card.classList.remove('selected');
      });

      document.querySelectorAll(`[data-store-id="${storeId}"]`).forEach(card => {
        card.classList.add('selected');
      });

      // Find and set selected store
      app.selectedStore = app.stores.find(store => store.id === storeId);
      if (!app.selectedStore) return;

      // Update UI
      document.getElementById('defaultState').style.display = 'none';
      document.getElementById('selectedContent').style.display = 'block';

      updateSelectedStoreInfo();
      loadStorePosts(storeId);

      // Close mobile offcanvas
      const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('storesPanel'));
      if (offcanvas) offcanvas.hide();
    }

    function updateSelectedStoreInfo() {
      document.getElementById('selectedStoreInfo').innerHTML = `
        <div class="store-avatar ${app.selectedStore.rank_class}">
            ${app.selectedStore.name.charAt(0).toUpperCase()}
            <div class="rank-badge">
                ${app.selectedStore.rank_icon}
            </div>
        </div>
        <div>
            <h6 class="mb-0">${app.selectedStore.name}</h6>
            <small class="text-muted">${app.selectedStore.rank_text} • ${app.selectedStore.total_points} points</small>
        </div>
    `;
    }

    async function loadStorePosts(storeId, page = 1) {
      try {
        app.isLoading = true;

        if (page === 1) {
          app.posts = [];
          app.currentPage = 1;
          document.getElementById('postsGrid').innerHTML = '<div class="col-12 text-center p-4"><div class="spinner-border text-success"></div></div>';
        }

        const response = await fetch(`/public-api/gallery/feed?seller_id=${storeId}&page=${page}`);
        if (!response.ok) throw new Error(`HTTP ${response.status}`);

        const data = await response.json();

        if (data.success) {
          const posts = (data.posts || []).map(post => ({
            id: post.id,
            photo_url: post.photo_url,
            caption: post.caption || '',
            created_at: post.created_at,
            time_ago: post.time_ago || 'Recently',
            is_featured: post.is_featured || false
          }));

          if (page === 1) {
            app.posts = posts;
          } else {
            app.posts.push(...posts);
          }

          app.hasMorePosts = data.hasMore || false;
          renderPosts();
          updatePostsCount();

          const loadMoreSection = document.getElementById('loadMoreSection');
          loadMoreSection.classList.toggle('d-none', !app.hasMorePosts);
        }
      } catch (error) {
        console.error('Error loading posts:', error);
        if (page === 1) {
          document.getElementById('postsGrid').innerHTML = `
                <div class="col-12 text-center p-4 text-muted">
                    <i class="bi bi-exclamation-triangle display-4 mb-3"></i>
                    <p>Failed to load posts</p>
                </div>
            `;
        }
      } finally {
        app.isLoading = false;
      }
    }

    function renderPosts() {
      const postsHTML = app.posts.map(post => {
        const isFrozen = /^\[frozen\]/i.test(post.caption); // check if caption starts with [frozen]
        const cleanCaption = post.caption.replace(/^\[frozen\]\s*/i, "");

        return `
        <div class="post-card position-relative" onclick="openPostModal(${post.id})">
            ${post.is_featured ? '<div class="featured-badge">Featured</div>' : ''}
            <img src="${post.photo_url}" 
                 alt="${cleanCaption}" 
                 class="post-image ${isFrozen ? 'blurred' : ''}" 
                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhmOWZhIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM2Yzc1N2QiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5JbWFnZSBOb3QgRm91bmQ8L3RleHQ+PC9zdmc+'">
            <div class="p-2">
                <small class="text-truncate d-block fw-medium">${cleanCaption || 'Store Post'}</small>
                <small class="text-muted">${post.time_ago}</small>
            </div>
        </div>
    `;
      }).join('');

      document.getElementById('postsGrid').innerHTML = postsHTML;
    }

    async function loadMorePosts() {
      if (app.isLoading || !app.hasMorePosts) return;

      const btn = document.getElementById('loadMoreBtn');
      const loadText = btn.querySelector('.load-text');
      const loadSpinner = btn.querySelector('.load-spinner');

      loadText.classList.add('d-none');
      loadSpinner.classList.remove('d-none');
      btn.disabled = true;

      try {
        app.currentPage++;
        await loadStorePosts(app.selectedStore.id, app.currentPage);
      } catch (error) {
        app.currentPage--;
      } finally {
        loadText.classList.remove('d-none');
        loadSpinner.classList.add('d-none');
        btn.disabled = false;
      }
    }

    function openPostModal(postId) {
      const post = app.posts.find(p => p.id === postId);
      if (!post) return;

      document.getElementById('modalImage').src = post.photo_url;
      document.getElementById('modalPostTitle').textContent = post.caption.replace(/^\[frozen\]\s*/i, "") || 'Store Post';
      document.getElementById('modalPostCaption').textContent = post.captionv || 'No caption provided';
      document.getElementById('modalPostDate').textContent = post.time_ago;
      document.getElementById('modalStoreLocation').textContent = app.selectedStore.address || 'Location not available';
      document.getElementById('modalStoreRank').textContent = `${app.selectedStore.rank_text} • ${app.selectedStore.total_points} points`;

      document.getElementById('modalStoreProfile').innerHTML = `
        <div class="d-flex align-items-center gap-2">
            <div class="store-avatar ${app.selectedStore.rank_class}" style="width: 36px; height: 36px; font-size: 14px;">
                ${app.selectedStore.name.charAt(0).toUpperCase()}
            </div>
            <div>
                <h6 class="mb-0 small">${app.selectedStore.name}</h6>
                <small class="text-muted">${app.selectedStore.rank_text}</small>
            </div>
        </div>
    `;

      new bootstrap.Modal(document.getElementById('postModal')).show();
    }

    function handleSearch() {
      applySearchAndFilter();
    }

    function handleFilter() {
      applySearchAndFilter();
    }

    function updateStoresCount() {
      const count = app.filteredStores.length;
      document.getElementById('storesCount').textContent = count;
      document.getElementById('mobileStoresCount').textContent = count;
    }

    function updatePostsCount() {
      document.getElementById('postsCount').textContent = `${app.posts.length} posts`;
    }

    function showErrorState(message) {
      const errorHTML = `
        <div class="text-center p-4 text-muted">
            <i class="bi bi-exclamation-triangle display-4 mb-3"></i>
            <p>${message}</p>
            <button class="btn btn-outline-success btn-sm" onclick="window.location.reload()">
                <i class="bi bi-arrow-clockwise me-1"></i>Retry
            </button>
        </div>
    `;

      document.getElementById('storesList').innerHTML = errorHTML;
      document.getElementById('mobileStoresList').innerHTML = errorHTML;
    }

    function visitStore() {
      if (app.selectedStore) {
        window.location.href = `/map?store=${app.selectedStore.id}`;
      }
    }

    function sharePost() {
      if (app.selectedStore) {
        const url = `${window.location.origin}/seller/${app.selectedStore.id}`;

        if (navigator.share) {
          navigator.share({
            title: `Check out ${app.selectedStore.name}!`,
            url: url
          });
        } else if (navigator.clipboard) {
          navigator.clipboard.writeText(url).then(() => {
            showToast('Link copied to clipboard!');
          });
        }
      }
    }

    function showToast(message) {
      const toast = document.createElement('div');
      toast.className = 'toast position-fixed top-0 end-0 m-3';
      toast.innerHTML = `
        <div class="toast-body bg-success text-white rounded">
            ${message}
        </div>
    `;
      document.body.appendChild(toast);

      const bsToast = new bootstrap.Toast(toast);
      bsToast.show();

      setTimeout(() => toast.remove(), 3000);
    }

    // Utility functions
    function debounce(func, wait) {
      let timeout;
      return function executedFunction(...args) {
        const later = () => {
          clearTimeout(timeout);
          func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
      };
    }

    function getRankClass(points) {
      if (points >= 2000) return 'platinum';
      if (points >= 1000) return 'gold';
      if (points >= 500) return 'silver';
      if (points >= 100) return 'bronze';
      return 'standard';
    }

    function getRankText(points) {
      if (points >= 2000) return 'Platinum';
      if (points >= 1000) return 'Gold';
      if (points >= 500) return 'Silver';
      if (points >= 100) return 'Bronze';
      return 'Standard';
    }

    function getRankIcon(points) {
      if (points >= 2000) return '👑';
      if (points >= 1000) return '🥇';
      if (points >= 500) return '🥈';
      return '⭐';
    }
  </script>
@endsection
