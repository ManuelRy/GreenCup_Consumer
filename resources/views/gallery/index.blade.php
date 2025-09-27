@extends('master')

@section('content')
<div class="container-fluid px-3 py-4" style="max-width: 1400px;">
  <!-- Mobile Toggle -->
  <div class="d-lg-none mb-3">
    <button class="btn btn-success w-100 d-flex align-items-center justify-content-center gap-2" data-bs-toggle="offcanvas" data-bs-target="#storesPanel">
      <i class="bi bi-shop"></i><span>Browse Stores</span><span class="badge bg-light text-success" id="mobileStoresCount">0</span>
    </button>
  </div>

  <div class="row g-3">
    <!-- Stores Panel -->
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm d-none d-lg-block">
        <div class="card-header bg-white py-3">
          <div class="d-flex align-items-center justify-content-between">
            <h5 class="mb-0 d-flex align-items-center gap-2"><i class="bi bi-shop text-success"></i>Stores Directory</h5>
            <span class="badge bg-success" id="storesCount">0</span>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="p-3 border-bottom">
            <div class="input-group mb-3">
              <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
              <input type="text" class="form-control" id="storeSearch" placeholder="Search stores...">
            </div>
            <select class="form-select" id="storeFilter">
              <option value="all">All Ranks</option><option value="platinum">Platinum</option><option value="gold">Gold</option><option value="silver">Silver</option><option value="bronze">Bronze</option><option value="standard">Standard</option>
            </select>
          </div>
          <div class="overflow-auto" style="max-height: 70vh;">
            <div class="p-3 border-2 border-success rounded mx-3 mb-2" onclick="selectAllStores()" data-store-id="all" style="cursor: pointer;">
              <div class="d-flex align-items-center">
                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;"><i class="bi bi-grid-3x3-gap"></i></div>
                <div><h6 class="mb-1">All Stores</h6><small class="text-muted d-block">View photos from all stores</small><small class="text-muted">Gallery • Featured</small></div>
              </div>
            </div>
            <hr class="mx-3">
            <div id="storesList"><div class="text-center p-4"><div class="spinner-border text-success"></div><p class="mt-2 text-muted">Loading stores...</p></div></div>
          </div>
        </div>
      </div>

      <!-- Mobile Offcanvas -->
      <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="storesPanel">
        <div class="offcanvas-header">
          <h5 class="d-flex align-items-center gap-2"><i class="bi bi-shop text-success"></i>Stores Directory</h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
          <div class="p-3 border-bottom">
            <div class="input-group mb-3">
              <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
              <input type="text" class="form-control" id="mobileStoreSearch" placeholder="Search stores...">
            </div>
            <select class="form-select" id="mobileStoreFilter">
              <option value="all">All Ranks</option><option value="platinum">Platinum</option><option value="gold">Gold</option><option value="silver">Silver</option><option value="bronze">Bronze</option><option value="standard">Standard</option>
            </select>
          </div>
          <div class="overflow-auto" style="height: calc(100vh - 180px);" id="mobileStoresList"></div>
        </div>
      </div>
    </div>

    <!-- Posts Panel -->
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <!-- All Stores View -->
        <div id="allStoresView">
          <div class="card-header bg-white py-3">
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center gap-3">
                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;"><i class="bi bi-grid-3x3-gap"></i></div>
                <div><h6 class="mb-0">All Stores Gallery</h6><small class="text-muted">Photos from all participating stores</small></div>
              </div>
              <span class="badge bg-success" id="allPostsCount">0 posts</span>
            </div>
          </div>
          <div class="card-body">
            <div id="allPostsGrid"><div class="text-center p-4"><div class="spinner-border text-success"></div><p class="mt-2 text-muted">Loading posts...</p></div></div>
            <div class="text-center mt-3 d-none" id="allLoadMoreSection">
              <button class="btn btn-outline-success" id="allLoadMoreBtn">
                <span class="all-load-text">Load More Posts</span>
                <span class="all-load-spinner d-none"><span class="spinner-border spinner-border-sm me-1"></span>Loading...</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Selected Store Content -->
        <div id="selectedContent" style="display: none;">
          <div class="card-header bg-white py-3">
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center gap-3" id="selectedStoreInfo"></div>
              <span class="badge bg-success" id="postsCount">0 posts</span>
            </div>
          </div>
          <div class="card-body">
            <div id="postsGrid"></div>
            <div class="text-center mt-3 d-none" id="loadMoreSection">
              <button class="btn btn-outline-success" id="loadMoreBtn">
                <span class="load-text">Load More Posts</span>
                <span class="load-spinner d-none"><span class="spinner-border spinner-border-sm me-1"></span>Loading...</span>
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
    <div class="modal-content">
      <div class="modal-header"><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body p-0">
        <div class="row g-0">
          <div class="col-lg-8">
            <div class="ratio ratio-1x1 bg-dark position-relative">
              <img id="modalImage" class="img-fluid" style="object-fit: contain;">
              <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark d-flex align-items-center justify-content-center d-none" id="modalCensoredOverlay" style="background: rgba(0,0,0,0.9);">
                <div class="text-center text-white p-4">
                  <i class="bi bi-exclamation-triangle-fill text-warning display-4 mb-3"></i>
                  <h5 class="text-uppercase fw-bold mb-2">Content Under Review</h5>
                  <p class="small opacity-75">This content is being reviewed by our moderation team</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="p-4 h-100 d-flex flex-column">
              <div class="mb-3" id="modalStoreProfile"></div>
              <div class="flex-grow-1">
                <h6 id="modalPostTitle" class="fw-bold mb-2"></h6>
                <p id="modalPostCaption" class="text-muted small mb-3"></p>
                <div>
                  <small class="text-muted d-flex align-items-center gap-1 mb-2"><i class="bi bi-geo-alt"></i><span id="modalStoreLocation"></span></small>
                  <small class="text-muted d-flex align-items-center gap-1 mb-2"><i class="bi bi-star-fill"></i><span id="modalStoreRank"></span></small>
                  <small class="text-muted d-flex align-items-center gap-1"><i class="bi bi-clock"></i><span id="modalPostDate"></span></small>
                </div>
              </div>
              <div class="d-grid gap-2">
                <button class="btn btn-success" onclick="visitStore()"><i class="bi bi-shop me-1"></i>Visit Store</button>
                <button class="btn btn-outline-secondary" onclick="sharePost()"><i class="bi bi-share me-1"></i>Share</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const app = {stores: [], filteredStores: [], selectedStore: null, posts: [], allPosts: [], currentPage: 1, allCurrentPage: 1, hasMorePosts: false, allHasMorePosts: false, isLoading: false};

document.addEventListener('DOMContentLoaded', initializeApp);

async function initializeApp() {
  try {
    await loadStores();
    initializeEventListeners();
    const sellerId = new URLSearchParams(window.location.search).get('seller');
    if (sellerId) {
      const targetStore = app.stores.find(s => s.id == sellerId);
      if (targetStore) setTimeout(() => selectStore(parseInt(sellerId)), 500);
    } else {
      setTimeout(loadAllStoresPosts, 500);
    }
  } catch (error) {
    console.error('Failed to initialize:', error);
    showErrorState('Failed to load stores');
  }
}

function initializeEventListeners() {
  ['storeSearch', 'mobileStoreSearch'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', debounce(applySearchAndFilter, 300));
  });
  ['storeFilter', 'mobileStoreFilter'].forEach(id => {
    document.getElementById(id)?.addEventListener('change', applySearchAndFilter);
  });
  document.getElementById('loadMoreBtn')?.addEventListener('click', loadMorePosts);
  document.getElementById('allLoadMoreBtn')?.addEventListener('click', loadMoreAllPosts);
  syncInputs();
}

function syncInputs() {
  const ds = document.getElementById('storeSearch'), ms = document.getElementById('mobileStoreSearch');
  const df = document.getElementById('storeFilter'), mf = document.getElementById('mobileStoreFilter');
  if (ds && ms) {
    ds.addEventListener('input', () => ms.value = ds.value);
    ms.addEventListener('input', () => ds.value = ms.value);
  }
  if (df && mf) {
    df.addEventListener('change', () => mf.value = df.value);
    mf.addEventListener('change', () => df.value = mf.value);
  }
}

function applySearchAndFilter() {
  const query = (document.getElementById('storeSearch')?.value || '').toLowerCase().trim();
  const filter = document.getElementById('storeFilter')?.value || 'all';
  let filtered = [...app.stores];
  if (query) filtered = filtered.filter(s => s.name.toLowerCase().includes(query) || (s.address && s.address.toLowerCase().includes(query)));
  if (filter !== 'all') filtered = filtered.filter(s => s.rank_class === filter);
  app.filteredStores = filtered;
  renderStores();
  updateStoresCount();
}

async function loadStores() {
  try {
    const response = await fetch('/public-api/stores');
    const data = await response.json();
    if (data.success) {
      app.stores = data.data.map(store => ({
        id: store.id, name: store.name, address: store.address, phone: store.phone,
        image: store.image, total_points: parseInt(store.total_points) || 0,
        rank_class: getRankClass(parseInt(store.total_points) || 0),
        rank_text: getRankText(parseInt(store.total_points) || 0),
        rank_icon: getRankIcon(parseInt(store.total_points) || 0)
      }));
      app.filteredStores = [...app.stores];
      renderStores();
      updateStoresCount();
    }
  } catch (error) {
    showErrorState(error.message);
  }
}

function renderStores() {
  const rankColors = {platinum: 'bg-purple-600', gold: 'bg-warning text-dark', silver: 'bg-secondary text-dark', bronze: 'bg-warning-subtle text-dark', standard: 'bg-success'};
  const html = app.filteredStores.map(store => `
    <div class="p-3 mb-2 rounded border" style="cursor: pointer;" onclick="selectStore(${store.id})" data-store-id="${store.id}">
      <div class="d-flex align-items-center">
        <div class="${rankColors[store.rank_class]} text-white rounded-circle d-flex align-items-center justify-content-center me-3 position-relative" style="width: 48px; height: 48px;">
          ${store.name.charAt(0).toUpperCase()}
          <span class="position-absolute top-0 end-0 translate-middle badge rounded-pill bg-light text-dark" style="font-size: 10px;">${store.rank_icon}</span>
        </div>
        <div>
          <h6 class="mb-1">${store.name}</h6>
          <small class="text-muted d-block">${store.address || 'No address'}</small>
          <small class="text-muted">${store.total_points} pts • ${store.rank_text}</small>
        </div>
      </div>
    </div>
  `).join('');
  document.getElementById('storesList').innerHTML = html;
  document.getElementById('mobileStoresList').innerHTML = html;
}

function selectStore(storeId) {
  document.querySelectorAll('[data-store-id]').forEach(el => el.classList.remove('border-success', 'border-2'));
  document.querySelectorAll(`[data-store-id="${storeId}"]`).forEach(el => el.classList.add('border-success', 'border-2'));
  app.selectedStore = app.stores.find(s => s.id === storeId);
  if (!app.selectedStore) return;
  document.getElementById('allStoresView').style.display = 'none';
  document.getElementById('selectedContent').style.display = 'block';
  updateSelectedStoreInfo();
  loadStorePosts(storeId);
  bootstrap.Offcanvas.getInstance(document.getElementById('storesPanel'))?.hide();
}

function selectAllStores() {
  document.querySelectorAll('[data-store-id]').forEach(el => el.classList.remove('border-success', 'border-2'));
  document.querySelectorAll('[data-store-id="all"]').forEach(el => el.classList.add('border-success', 'border-2'));
  app.selectedStore = null;
  document.getElementById('selectedContent').style.display = 'none';
  document.getElementById('allStoresView').style.display = 'block';
  loadAllStoresPosts();
  bootstrap.Offcanvas.getInstance(document.getElementById('storesPanel'))?.hide();
}

function updateSelectedStoreInfo() {
  const rankColors = {platinum: 'bg-purple-600', gold: 'bg-warning text-dark', silver: 'bg-secondary text-dark', bronze: 'bg-warning-subtle text-dark', standard: 'bg-success'};
  document.getElementById('selectedStoreInfo').innerHTML = `
    <div class="${rankColors[app.selectedStore.rank_class]} text-white rounded-circle d-flex align-items-center justify-content-center position-relative" style="width: 48px; height: 48px;">
      ${app.selectedStore.name.charAt(0).toUpperCase()}
      <span class="position-absolute top-0 end-0 translate-middle badge rounded-pill bg-light text-dark" style="font-size: 10px;">${app.selectedStore.rank_icon}</span>
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
      document.getElementById('postsGrid').innerHTML = '<div class="text-center p-4"><div class="spinner-border text-success"></div></div>';
    }
    const response = await fetch(`/public-api/gallery/feed?seller_id=${storeId}&page=${page}`);
    const data = await response.json();
    if (data.success) {
      const posts = data.posts.map(post => ({id: post.id, photo_url: post.photo_url, caption: post.caption || '', time_ago: post.time_ago || 'Recently', is_featured: post.is_featured || false}));
      if (page === 1) app.posts = posts; else app.posts.push(...posts);
      app.hasMorePosts = data.hasMore || false;
      renderPosts();
      updatePostsCount();
      document.getElementById('loadMoreSection').classList.toggle('d-none', !app.hasMorePosts);
    }
  } catch (error) {
    if (page === 1) document.getElementById('postsGrid').innerHTML = '<div class="text-center p-4 text-muted"><i class="bi bi-exclamation-triangle display-4 mb-3"></i><p>Failed to load posts</p></div>';
  } finally {
    app.isLoading = false;
  }
}

function renderPosts() {
  const html = app.posts.map(post => {
    const isFrozen = /^\[frozen\]/i.test(post.caption);
    const cleanCaption = post.caption.replace(/^\[frozen\]\s*/i, "");
    return `
      <div class="col-md-4 mb-3">
        <div class="bg-white border rounded shadow-sm position-relative" style="cursor: pointer;" onclick="openPostModal(${post.id})">
          ${post.is_featured ? '<div class="position-absolute top-0 start-0 bg-warning text-dark px-2 py-1 small fw-bold" style="font-size: 10px; z-index: 5;">Featured</div>' : ''}
          <div class="position-relative">
            <img src="${post.photo_url}" alt="${cleanCaption}" class="w-100 rounded-top" style="height: 200px; object-fit: cover;">
            ${isFrozen ? '<div class="position-absolute top-0 start-0 w-100 h-100 bg-dark d-flex align-items-center justify-content-center rounded-top" style="background: rgba(0,0,0,0.8);"><div class="text-center text-white"><i class="bi bi-exclamation-triangle-fill text-warning fs-3 mb-2"></i><div class="small fw-bold">CONTENT UNDER REVIEW</div></div></div>' : ''}
          </div>
          <div class="p-3">
            <small class="d-block fw-medium">${cleanCaption || 'Store Post'}</small>
            <small class="text-muted">${post.time_ago}</small>
          </div>
        </div>
      </div>
    `;
  }).join('');
  document.getElementById('postsGrid').innerHTML = `<div class="row">${html}</div>`;
}

async function loadAllStoresPosts(page = 1) {
  try {
    app.isLoading = true;
    if (page === 1) {
      app.allPosts = [];
      document.getElementById('allPostsGrid').innerHTML = '<div class="text-center p-4"><div class="spinner-border text-success"></div><p class="mt-2 text-muted">Loading posts...</p></div>';
    }
    const response = await fetch(`/public-api/gallery/feed?page=${page}`);
    const data = await response.json();
    if (data.success) {
      const posts = data.posts.map(post => ({id: post.id, seller_id: post.seller_id, business_name: post.business_name, photo_url: post.photo_url, caption: post.caption || '', time_ago: post.time_ago || 'Recently', is_featured: post.is_featured || false, total_points: post.total_points || 0, rank_class: post.rank_class || 'standard', rank_text: post.rank_text || 'Standard', address: post.address || 'Location not available'}));
      if (page === 1) app.allPosts = posts; else app.allPosts.push(...posts);
      app.allHasMorePosts = data.hasMore || false;
      renderAllPosts();
      updateAllPostsCount();
      document.getElementById('allLoadMoreSection').classList.toggle('d-none', !app.allHasMorePosts);
    }
  } catch (error) {
    if (page === 1) document.getElementById('allPostsGrid').innerHTML = '<div class="text-center p-4 text-muted"><i class="bi bi-exclamation-triangle display-4 mb-3"></i><p>Failed to load posts</p></div>';
  } finally {
    app.isLoading = false;
  }
}

function renderAllPosts() {
  const rankColors = {platinum: 'bg-purple-600', gold: 'bg-warning text-dark', silver: 'bg-secondary text-dark', bronze: 'bg-warning-subtle text-dark', standard: 'bg-success'};
  const html = app.allPosts.map(post => {
    const isFrozen = /^\[frozen\]/i.test(post.caption);
    const cleanCaption = post.caption.replace(/^\[frozen\]\s*/i, "");
    return `
      <div class="col-md-4 mb-3">
        <div class="bg-white border rounded shadow-sm position-relative" style="cursor: pointer;" onclick="openPostModal(${post.id})">
          ${post.is_featured ? '<div class="position-absolute top-0 start-0 bg-warning text-dark px-2 py-1 small fw-bold" style="font-size: 10px; z-index: 5;">Featured</div>' : ''}
          <div class="position-relative">
            <img src="${post.photo_url}" alt="${cleanCaption}" class="w-100 rounded-top" style="height: 200px; object-fit: cover;">
            ${isFrozen ? '<div class="position-absolute top-0 start-0 w-100 h-100 bg-dark d-flex align-items-center justify-content-center rounded-top" style="background: rgba(0,0,0,0.8);"><div class="text-center text-white"><i class="bi bi-exclamation-triangle-fill text-warning fs-3 mb-2"></i><div class="small fw-bold">CONTENT UNDER REVIEW</div></div></div>' : ''}
          </div>
          <div class="p-3">
            <small class="d-block fw-medium">${cleanCaption || 'Store Post'}</small>
            <div class="d-flex align-items-center gap-1 mt-1">
              <div class="${rankColors[post.rank_class]} text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 16px; height: 16px; font-size: 8px;">${post.business_name.charAt(0).toUpperCase()}</div>
              <small class="text-muted">${post.business_name}</small>
            </div>
            <small class="text-muted">${post.time_ago}</small>
          </div>
        </div>
      </div>
    `;
  }).join('');
  document.getElementById('allPostsGrid').innerHTML = `<div class="row">${html}</div>`;
}

function openPostModal(postId) {
  const post = app.posts.find(p => p.id === postId) || app.allPosts.find(p => p.id === postId);
  if (!post) return;
  const isFrozen = /^\[frozen\]/i.test(post.caption);
  const cleanCaption = post.caption.replace(/^\[frozen\]\s*/i, "");
  document.getElementById('modalImage').src = post.photo_url;
  document.getElementById('modalPostTitle').textContent = cleanCaption || 'Store Post';
  document.getElementById('modalPostCaption').textContent = cleanCaption || 'No caption';
  document.getElementById('modalPostDate').textContent = post.time_ago;
  document.getElementById('modalCensoredOverlay').classList.toggle('d-none', !isFrozen);
  if (app.selectedStore) {
    document.getElementById('modalStoreLocation').textContent = app.selectedStore.address || 'Location not available';
    document.getElementById('modalStoreRank').textContent = `${app.selectedStore.rank_text} • ${app.selectedStore.total_points} points`;
    const rankColors = {platinum: 'bg-purple-600', gold: 'bg-warning text-dark', silver: 'bg-secondary text-dark', bronze: 'bg-warning-subtle text-dark', standard: 'bg-success'};
    document.getElementById('modalStoreProfile').innerHTML = `<div class="d-flex align-items-center gap-2"><div class="${rankColors[app.selectedStore.rank_class]} text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">${app.selectedStore.name.charAt(0).toUpperCase()}</div><div><h6 class="mb-0 small">${app.selectedStore.name}</h6><small class="text-muted">${app.selectedStore.rank_text}</small></div></div>`;
  } else if (post.business_name) {
    document.getElementById('modalStoreLocation').textContent = post.address;
    document.getElementById('modalStoreRank').textContent = `${post.rank_text} • ${post.total_points} points`;
    const rankColors = {platinum: 'bg-purple-600', gold: 'bg-warning text-dark', silver: 'bg-secondary text-dark', bronze: 'bg-warning-subtle text-dark', standard: 'bg-success'};
    document.getElementById('modalStoreProfile').innerHTML = `<div class="d-flex align-items-center gap-2"><div class="${rankColors[post.rank_class]} text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">${post.business_name.charAt(0).toUpperCase()}</div><div><h6 class="mb-0 small">${post.business_name}</h6><small class="text-muted">${post.rank_text}</small></div></div>`;
  }
  new bootstrap.Modal(document.getElementById('postModal')).show();
}

async function loadMorePosts() {
  if (app.isLoading || !app.hasMorePosts) return;
  const btn = document.getElementById('loadMoreBtn');
  btn.querySelector('.load-text').classList.add('d-none');
  btn.querySelector('.load-spinner').classList.remove('d-none');
  btn.disabled = true;
  try {
    await loadStorePosts(app.selectedStore.id, ++app.currentPage);
  } catch {
    app.currentPage--;
  } finally {
    btn.querySelector('.load-text').classList.remove('d-none');
    btn.querySelector('.load-spinner').classList.add('d-none');
    btn.disabled = false;
  }
}

async function loadMoreAllPosts() {
  if (app.isLoading || !app.allHasMorePosts) return;
  const btn = document.getElementById('allLoadMoreBtn');
  btn.querySelector('.all-load-text').classList.add('d-none');
  btn.querySelector('.all-load-spinner').classList.remove('d-none');
  btn.disabled = true;
  try {
    await loadAllStoresPosts(++app.allCurrentPage);
  } catch {
    app.allCurrentPage--;
  } finally {
    btn.querySelector('.all-load-text').classList.remove('d-none');
    btn.querySelector('.all-load-spinner').classList.add('d-none');
    btn.disabled = false;
  }
}

function updatePostsCount() {
  document.getElementById('postsCount').textContent = `${app.posts.length} posts`;
}

function updateAllPostsCount() {
  document.getElementById('allPostsCount').textContent = `${app.allPosts.length} posts`;
}

function updateStoresCount() {
  const count = app.filteredStores.length;
  document.getElementById('storesCount').textContent = count;
  document.getElementById('mobileStoresCount').textContent = count;
}

function showErrorState(message) {
  const errorHTML = `<div class="text-center p-4 text-muted"><i class="bi bi-exclamation-triangle display-4 mb-3"></i><p>${message}</p><button class="btn btn-outline-success btn-sm" onclick="window.location.reload()"><i class="bi bi-arrow-clockwise me-1"></i>Retry</button></div>`;
  document.getElementById('storesList').innerHTML = errorHTML;
  document.getElementById('mobileStoresList').innerHTML = errorHTML;
}

function visitStore() {
  if (app.selectedStore) {
    window.location.href = `/map?store=${app.selectedStore.id}`;
  } else {
    const currentPost = app.allPosts.find(p => p.photo_url === document.getElementById('modalImage').src);
    window.location.href = currentPost?.seller_id ? `/map?store=${currentPost.seller_id}` : '/map';
  }
}

function sharePost() {
  let url, title;
  if (app.selectedStore) {
    url = `${window.location.origin}/seller/${app.selectedStore.id}`;
    title = `Check out ${app.selectedStore.name}!`;
  } else {
    const currentPost = app.allPosts.find(p => p.photo_url === document.getElementById('modalImage').src);
    if (currentPost?.seller_id) {
      url = `${window.location.origin}/seller/${currentPost.seller_id}`;
      title = `Check out ${currentPost.business_name}!`;
    } else {
      url = `${window.location.origin}/gallery`;
      title = 'Check out GreenCup Store Gallery!';
    }
  }

  if (navigator.share) {
    navigator.share({ title, url });
  } else if (navigator.clipboard) {
    navigator.clipboard.writeText(url).then(() => showToast('Link copied to clipboard!'));
  }
}

function showToast(message) {
  const toast = document.createElement('div');
  toast.className = 'toast position-fixed top-0 end-0 m-3';
  toast.innerHTML = `<div class="toast-body bg-success text-white rounded">${message}</div>`;
  document.body.appendChild(toast);
  new bootstrap.Toast(toast).show();
  setTimeout(() => toast.remove(), 3000);
}

function debounce(func, wait) {
  let timeout;
  return function(...args) {
    clearTimeout(timeout);
    timeout = setTimeout(() => func(...args), wait);
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
  if (points >= 100) return '🥉';
  return '⭐';
}
</script>
@endsection
