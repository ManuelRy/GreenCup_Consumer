@extends('master')

@section('content')
  @php
    use App\Repository\ConsumerPointRepository;
    $cPRepo = new ConsumerPointRepository();
  @endphp
  <div class="container-fluid min-vh-100 py-3">
    <div class="row justify-content-center">
      <div class="col-12 col-xl-10">

        <!-- Page Header -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white rounded-4">
              <div class="card-body py-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                  <div>
                    <div class="mb-3">
                      <i class="fas fa-gift fa-3x opacity-90"></i>
                    </div>
                    <h2 class="fw-bold mb-2">Reward Gallery</h2>
                    <p class="fw-light opacity-90 mb-0">Discover and redeem amazing rewards with your points</p>
                  </div>
                  <div class="text-end">
                    <a href="{{ route('reward.my') }}" class="btn btn-outline-light btn-lg">
                      <i class="fas fa-trophy me-2"></i>My Rewards
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filter and Search Section -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                  <h5 class="fw-semibold text-dark mb-0">
                    <i class="fas fa-filter me-2"></i>Filter & Search
                  </h5>
                  <a href="{{ route('reward.my') }}" class="btn btn-outline-primary">
                    <i class="fas fa-trophy me-1"></i>View My Rewards
                  </a>
                </div>
                <div class="row g-3 align-items-end">
                  <!-- Search Input -->
                  <div class="col-12 col-md-6 col-lg-4">
                    <label class="form-label fw-semibold text-dark">
                      <i class="fas fa-search me-2"></i>Search Rewards & Shops
                    </label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Find rewards or shops...">
                  </div>

                  <!-- Category Filter -->
                  <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fw-semibold text-dark">
                      <i class="fas fa-filter me-2"></i>Category
                    </label>
                    <select class="form-select" id="categoryFilter">
                      <option value="">All Categories</option>
                      <option value="food">Food & Beverages</option>
                      <option value="discount">Discounts</option>
                      <option value="merchandise">Merchandise</option>
                      <option value="experience">Experiences</option>
                    </select>
                  </div>

                  <!-- Points Range Filter -->
                  <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fw-semibold text-dark">
                      <i class="fas fa-coins me-2"></i>Points Range
                    </label>
                    <select class="form-select" id="pointsFilter">
                      <option value="">Any Range</option>
                      <option value="0-100">0 - 100 pts</option>
                      <option value="101-500">101 - 500 pts</option>
                      <option value="501-1000">501 - 1000 pts</option>
                      <option value="1001+">1001+ pts</option>
                    </select>
                  </div>

                  <!-- Sort Options -->
                  <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label fw-semibold text-dark">
                      <i class="fas fa-sort me-2"></i>Sort By
                    </label>
                    <select class="form-select" id="sortFilter">
                      <option value="newest">Newest</option>
                      <option value="points-low">Points (Low to High)</option>
                      <option value="points-high">Points (High to Low)</option>
                      <option value="popular">Most Popular</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Rewards Grid: Grouped by Shop -->
        <div id="rewardsContainer">
          @include('reward-redemption.partials.rewards-grid', ['sellers' => $sellers])
        </div>
      </div>

    </div>
  </div>
  </div>

  <!-- Redeem Modal -->
  <div class="modal fade" id="mockRedeemModal" tabindex="-1" aria-labelledby="mockRedeemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-gradient-primary text-white">
          <h5 class="modal-title" id="mockRedeemModalLabel"><i class="fas fa-gift me-2"></i>Redeem Reward</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="mock-reward-details"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-success" id="mockConfirmRedeem">Confirm Redemption</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Preview Modal -->
  <div class="modal fade" id="previewRewardModal" tabindex="-1" aria-labelledby="previewRewardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-gradient-info text-white">
          <h5 class="modal-title" id="previewRewardModalLabel"><i class="fas fa-eye me-2"></i>Reward Preview</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="preview-reward-details"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success" onclick="proceedToRedeem()">
            <i class="fas fa-gift me-2"></i>Proceed to Redeem
          </button>
        </div>
      </div>
    </div>
  </div>

  <style>
    /* Custom CSS consistent with dashboard theme */
    :root {
      --bs-primary: #1dd1a1;
      --bs-primary-rgb: 29, 209, 161;
      --bs-success: #22c55e;
      --bs-danger: #ef4444;
      --bs-warning: #f59e0b;
      --bs-info: #06b6d4;
    }

    .bg-gradient-primary {
      background: linear-gradient(135deg, #1dd1a1, #10ac84) !important;
    }

    .btn-primary {
      background: linear-gradient(135deg, #1dd1a1, #10ac84);
      border: none;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(29, 209, 161, 0.3);
      background: linear-gradient(135deg, #10ac84, #0e8e71);
    }

    .btn-outline-primary {
      border-color: #1dd1a1;
      color: #1dd1a1;
      transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
      background: #1dd1a1;
      border-color: #1dd1a1;
      transform: translateY(-1px);
    }

    /* Reward Cards */
    .reward-item {
      transition: all 0.3s ease;
    }

    .reward-item:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    /* Default reward card styling */
    .reward-default-card {
      height: 200px;
    }

    /* Reward overlay */
    .bg-gradient-to-t {
      background: linear-gradient(to top, rgba(0,0,0,0.7), rgba(0,0,0,0));
    }

    /* Points styling */
    .text-primary {
      color: #1dd1a1 !important;
    }

    /* Card animations */
    .card {
      animation: slideUp 0.6s ease-out;
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Form enhancements */
    .form-control,
    .form-select {
      border-radius: 0.5rem;
      border: 2px solid #e9ecef;
      transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
      border-color: #1dd1a1;
      box-shadow: 0 0 0 0.2rem rgba(29, 209, 161, 0.25);
    }

    /* Badge enhancements */
    .badge {
      font-size: 0.75rem;
      padding: 4px 8px;
      font-weight: 600;
    }

    /* Mobile optimizations */
    @media (max-width: 768px) {
      .container-fluid {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
      }

      .card-body {
        padding: 1rem;
      }

      .display-6 {
        font-size: 2rem !important;
      }

      .btn-lg {
        padding: 0.5rem 1rem;
        font-size: 1rem;
      }

      .card-title {
        font-size: 1rem;
      }

      .h5 {
        font-size: 1.1rem;
      }
    }

    @media (max-width: 576px) {
      .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
      }

      .text-end {
        text-align: start !important;
      }

      .col-xl-3 {
        min-width: 280px;
      }
    }

    /* Enhanced visual feedback */
    .btn:focus {
      outline: 2px solid #1dd1a1;
      outline-offset: 2px;
    }

    /* Staggered animation for reward cards */
    .reward-card:nth-child(1) { animation-delay: 0.1s; }
    .reward-card:nth-child(2) { animation-delay: 0.2s; }
    .reward-card:nth-child(3) { animation-delay: 0.3s; }
    .reward-card:nth-child(4) { animation-delay: 0.4s; }
    .reward-card:nth-child(5) { animation-delay: 0.5s; }
    .reward-card:nth-child(6) { animation-delay: 0.6s; }
    .reward-card:nth-child(7) { animation-delay: 0.7s; }
    .reward-card:nth-child(8) { animation-delay: 0.8s; }

    /* Loading states */
    .btn:disabled {
      opacity: 0.6;
      transform: none !important;
    }

    /* Improved loading animation */
    .spinner-border {
      width: 3rem;
      height: 3rem;
    }

    /* Shop section styling */
    .shop-section {
      opacity: 1;
      transition: all 0.3s ease;
    }

    .shop-section.filtering {
      opacity: 0.7;
    }

    /* Enhanced reward card hover effects */
    .reward-card {
      transform-origin: center;
    }

    .reward-card .card {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      border: 2px solid transparent;
    }

    .reward-card .card:hover {
      border-color: rgba(29, 209, 161, 0.3);
      transform: translateY(-8px) scale(1.02);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .reward-preview-card:hover {
      border-color: rgba(29, 209, 161, 0.5) !important;
      transform: translateY(-10px) scale(1.03) !important;
      box-shadow: 0 25px 50px rgba(29, 209, 161, 0.2) !important;
    }

    .reward-preview-card:hover::after {
      content: "👁️ Click to Preview";
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: rgba(29, 209, 161, 0.9);
      color: white;
      padding: 8px 16px;
      border-radius: 20px;
      font-size: 14px;
      font-weight: 600;
      z-index: 10;
      pointer-events: none;
      opacity: 0;
      animation: fadeInPreview 0.3s ease forwards;
    }

    @keyframes fadeInPreview {
      to {
        opacity: 1;
      }
    }

    /* Improved filter section */
    .form-control:focus,
    .form-select:focus {
      border-color: #1dd1a1;
      box-shadow: 0 0 0 0.2rem rgba(29, 209, 161, 0.25);
      outline: none;
    }

    /* Better mobile responsiveness */
    @media (max-width: 768px) {
      .reward-card .card:hover {
        transform: translateY(-4px) scale(1.01);
      }

      .shop-section h4 {
        font-size: 1.2rem;
      }

      .badge {
        font-size: 0.7rem;
      }
    }
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      let selectedReward = null;
      let selectedSellerId = null;
      let selectedShop = null;
      let filterTimeout = null;

      // Temporary: Log all clicks to debug
      document.addEventListener('click', function(e) {
        console.log('Global click on:', e.target.tagName, e.target.className);
      });

      // Initialize redeem modal functionality with event delegation
      initializeRedeemButtons();

      // Debug: Check if buttons exist and add fallback direct listeners
      setTimeout(function() {
        const buttons = document.querySelectorAll('.redeem-btn');
        console.log('Found', buttons.length, 'redeem buttons on page load');

        // Add direct listeners as fallback
        buttons.forEach((btn, index) => {
          console.log(`Button ${index}:`, btn, 'Data:', btn.dataset);

          // Add direct click listener as backup
          btn.addEventListener('click', function(e) {
            console.log('Direct listener triggered for button', index);
            handleRedeemClick(e);
          });

          // Add visual feedback to ensure buttons are clickable
          btn.style.cursor = 'pointer';
          btn.style.pointerEvents = 'auto';
        });
      }, 1000);

      function initializeRedeemButtons() {
        console.log('Setting up event delegation for redeem buttons');

        // Remove any existing event listeners to avoid duplicates
        document.removeEventListener('click', handleRedeemClick);

        // Use event delegation to handle dynamically loaded buttons
        document.addEventListener('click', handleRedeemClick);
      }

      function handleRedeemClick(e) {
        console.log('Click detected on:', e.target);

        // Check if the clicked element or its parent is a redeem button
        let btn = null;

        if (e.target.classList.contains('redeem-btn')) {
          btn = e.target;
          console.log('Direct click on redeem button');
        } else if (e.target.closest('.redeem-btn')) {
          btn = e.target.closest('.redeem-btn');
          console.log('Click on child of redeem button');
        } else {
          // Not a redeem button click, ignore
          return;
        }

        console.log('Redeem button clicked!', btn);
        console.log('Button classes:', btn.className);
        console.log('Button dataset:', btn.dataset);
        console.log('Raw reward data:', btn.dataset.reward);
        console.log('Raw seller_id data:', btn.dataset.seller_id);
        console.log('Raw shop data:', btn.dataset.shop);

        // Prevent default action
        e.preventDefault();
        e.stopPropagation();

        try {
          // Check if reward data exists
          if (!btn.dataset.reward || btn.dataset.reward.trim() === '') {
            console.error('Reward data is missing or empty');
            alert('Error: Reward data is missing. Please refresh the page and try again.');
            return;
          }

          // Check if seller_id exists
          if (!btn.dataset.seller_id || btn.dataset.seller_id.trim() === '') {
            console.error('Seller ID is missing or empty');
            alert('Error: Seller data is missing. Please refresh the page and try again.');
            return;
          }

          selectedReward = JSON.parse(btn.dataset.reward);
          selectedShop = JSON.parse(btn.dataset.shop);
          selectedSellerId = JSON.parse(btn.dataset.seller_id);

          console.log('Selected reward:', selectedReward);
          console.log('Selected shop:', selectedShop);
          console.log('Selected seller ID:', selectedSellerId);

          // Validate parsed data
          if (!selectedReward || !selectedReward.id) {
            console.error('Invalid reward data after parsing');
            alert('Error: Invalid reward data. Please refresh the page and try again.');
            return;
          }

          const imageHtml = selectedReward.image_url
            ? `<div class="text-center mb-3">
                 <img src="${selectedReward.image_url}" class="img-fluid rounded" style="max-height: 200px; width: auto; object-fit: cover; border-radius: 12px;">
               </div>`
            : `<div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-3 mb-3" style="height:180px;">
                 <div class="text-center">
                   <div class="bg-primary bg-opacity-20 rounded-circle p-3 mb-2">
                     <i class="fas fa-gift fa-3x text-primary"></i>
                   </div>
                   <div class="fw-semibold text-primary">Reward</div>
                 </div>
               </div>`;

          document.getElementById('mock-reward-details').innerHTML = `
            <div class='text-center'>
              ${imageHtml}
              <h4 class="fw-bold text-primary mb-2">🎁 ${selectedReward.name}</h4>
              <p class="text-muted mb-3">From <strong>${selectedShop}</strong></p>
              <div class="bg-success bg-opacity-10 text-success rounded-3 p-3 mb-3">
                <div class="d-flex align-items-center justify-content-center gap-2">
                  <i class="fas fa-coins"></i>
                  <span class="fw-bold fs-5">${selectedReward.points_required.toLocaleString()} Points</span>
                </div>
                <div class="small mt-1">Will be deducted from your wallet</div>
              </div>
              <div class="text-muted small">
                <i class="fas fa-info-circle me-1"></i>
                You'll receive a confirmation once the reward is processed
              </div>
            </div>
          `;

          console.log('Opening modal...');
          const modalElement = document.getElementById('mockRedeemModal');
          console.log('Modal element:', modalElement);

          if (modalElement) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
            console.log('Modal should be visible now');
          } else {
            console.error('Modal element not found!');
          }

        } catch (error) {
          console.error('Error processing redeem button click:', error);
          console.error('Error stack:', error.stack);
          alert(`Error processing reward: ${error.message}. Please refresh the page and try again.`);
        }
      }

      // Preview function for clicking on reward cards
      window.showRewardPreview = function(cardElement) {
        try {
          const reward = JSON.parse(cardElement.dataset.reward);
          const shop = JSON.parse(cardElement.dataset.shop);
          const sellerId = JSON.parse(cardElement.dataset.seller_id);

          console.log('Preview reward:', reward);

          const imageHtml = reward.image_url
            ? `<div class="text-center mb-3">
                 <img src="${reward.image_url}" class="img-fluid rounded" style="max-height: 250px; width: auto; object-fit: cover; border-radius: 12px;">
               </div>`
            : `<div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-3 mb-3" style="height:200px;">
                 <div class="text-center">
                   <div class="bg-primary bg-opacity-20 rounded-circle p-4 mb-2">
                     <i class="fas fa-gift fa-4x text-primary"></i>
                   </div>
                   <div class="fw-semibold text-primary fs-5">Reward</div>
                 </div>
               </div>`;

          document.getElementById('preview-reward-details').innerHTML = `
            <div class='text-center'>
              ${imageHtml}
              <h4 class="fw-bold text-primary mb-2">🎁 ${reward.name}</h4>
              <p class="text-muted mb-3">From <strong>${shop}</strong></p>
              <div class="mb-3">
                <p class="text-dark">${reward.description || 'Redeem this exclusive reward with your points.'}</p>
              </div>
              <div class="bg-info bg-opacity-10 text-info rounded-3 p-3 mb-3">
                <div class="d-flex align-items-center justify-content-center gap-2">
                  <i class="fas fa-coins"></i>
                  <span class="fw-bold fs-5">${reward.points_required.toLocaleString()} Points Required</span>
                </div>
                <div class="small mt-1">
                  <i class="fas fa-box me-1"></i>
                  ${reward.available_qty || 0} rewards remaining
                  ${reward.available_qty <= 5 && reward.available_qty > 0 ? '<span class="text-warning ms-2"><i class="fas fa-exclamation-triangle"></i> Limited Stock!</span>' : ''}
                  ${reward.available_qty <= 0 ? '<span class="text-danger ms-2"><i class="fas fa-times-circle"></i> Out of Stock</span>' : ''}
                </div>
              </div>
            </div>
          `;

          // Store data for potential redemption
          selectedReward = reward;
          selectedShop = shop;
          selectedSellerId = sellerId;

          const previewModal = new bootstrap.Modal(document.getElementById('previewRewardModal'));
          previewModal.show();

        } catch (error) {
          console.error('Error showing preview:', error);
          alert('Error showing reward preview. Please try again.');
        }
      }

      // Function to proceed from preview to redeem
      window.proceedToRedeem = function() {
        // Check if reward is still available
        if (selectedReward.available_qty <= 0) {
          alert('Sorry, this reward is currently out of stock.');
          return;
        }

        // Close preview modal
        bootstrap.Modal.getInstance(document.getElementById('previewRewardModal')).hide();

        // Wait a moment for the modal to close, then open redeem modal
        setTimeout(() => {
          if (selectedReward && selectedShop && selectedSellerId) {
            // Populate redeem modal with the same data
            const imageHtml = selectedReward.image_url
              ? `<div class="text-center mb-3">
                   <img src="${selectedReward.image_url}" class="img-fluid rounded" style="max-height: 200px; width: auto; object-fit: cover; border-radius: 12px;">
                 </div>`
              : `<div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-3 mb-3" style="height:180px;">
                   <div class="text-center">
                     <div class="bg-primary bg-opacity-20 rounded-circle p-3 mb-2">
                       <i class="fas fa-gift fa-3x text-primary"></i>
                     </div>
                     <div class="fw-semibold text-primary">Reward</div>
                   </div>
                 </div>`;

            document.getElementById('mock-reward-details').innerHTML = `
              <div class='text-center'>
                ${imageHtml}
                <h4 class="fw-bold text-primary mb-2">🎁 ${selectedReward.name}</h4>
                <p class="text-muted mb-3">From <strong>${selectedShop}</strong></p>
                <div class="bg-success bg-opacity-10 text-success rounded-3 p-3 mb-3">
                  <div class="d-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-coins"></i>
                    <span class="fw-bold fs-5">${selectedReward.points_required.toLocaleString()} Points</span>
                  </div>
                  <div class="small mt-1">Will be deducted from your wallet</div>
                </div>
                <div class="bg-info bg-opacity-10 text-info rounded-3 p-2 mb-3">
                  <div class="small text-center">
                    <i class="fas fa-box me-1"></i>
                    ${selectedReward.available_qty} remaining in stock
                    ${selectedReward.available_qty <= 5 ? ' <span class="text-warning">• Limited Stock!</span>' : ''}
                  </div>
                </div>
                <div class="text-muted small">
                  <i class="fas fa-info-circle me-1"></i>
                  You'll receive a confirmation once the reward is processed
                </div>
              </div>
            `;

            const redeemModal = new bootstrap.Modal(document.getElementById('mockRedeemModal'));
            redeemModal.show();
          }
        }, 300);
      }

      // Confirm redeem
      document.getElementById('mockConfirmRedeem').onclick = function() {
        if (!selectedReward || !selectedSellerId) {
          alert('No reward selected.');
          return;
        }
        const btn = this;
        btn.disabled = true;

        fetch(`/rewards/${selectedReward.id}/redeem`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({})
          })
          .then(response => {
            if (!response.ok) {
              if (response.status === 404) throw new Error('Not found');
              if (response.status === 422) throw new Error('Invalid Request');
              throw new Error(`HTTP_${response.status}`);
            }
            return response.json();
          })
          .then(data => {
            if (data.success) {
              alert('Reward redeemed successfully!');
              console.log('Redemption:', data);
              bootstrap.Modal.getInstance(
                document.getElementById('mockRedeemModal')
              ).hide();
              // Reload the rewards to reflect updated state
              applyFilters();
            } else {
              let msg = data.message || 'Unknown error while redeeming reward.';
              alert(msg);
            }
          })
          .catch(error => {
            console.error(error);
            let msg = 'Something went wrong. Try again later.';
            if (error.message === 'NOT_FOUND') msg = 'Reward not found.';
            else if (error.message === 'INVALID_REQUEST') msg = 'Invalid redemption request.';
            else if (error.message.startsWith('HTTP_')) msg = 'Server error. Try again.';
            alert(msg);
          })
          .finally(() => {
            btn.disabled = false;
            bootstrap.Modal.getInstance(document.getElementById('mockRedeemModal')).hide();
          });
      };

      // Filter functionality with AJAX
      const searchInput = document.getElementById('searchInput');
      const categoryFilter = document.getElementById('categoryFilter');
      const pointsFilter = document.getElementById('pointsFilter');
      const sortFilter = document.getElementById('sortFilter');
      const rewardsContainer = document.getElementById('rewardsContainer');

      function showLoading() {
        rewardsContainer.innerHTML = `
          <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-3">Loading rewards...</p>
          </div>
        `;
      }

      function applyFilters() {
        // Clear existing timeout
        if (filterTimeout) {
          clearTimeout(filterTimeout);
        }

        // Set new timeout for debouncing
        filterTimeout = setTimeout(() => {
          showLoading();

          // Get filter values
          const searchValue = searchInput.value.trim();
          const categoryValue = categoryFilter.value;
          const pointsValue = pointsFilter.value;
          const sortValue = sortFilter.value;

          // Build parameters for backend (excluding category since it's frontend-only)
          const params = new URLSearchParams({
            search: searchValue,
            points_range: pointsValue,
            sort: sortValue
          });

          // Remove empty parameters
          for (let [key, value] of [...params.entries()]) {
            if (!value) {
              params.delete(key);
            }
          }

          fetch(`{{ route('reward.index') }}?${params.toString()}`, {
            method: 'GET',
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Content-Type': 'application/json'
            }
          })
          .then(response => response.json())
          .then(data => {
            rewardsContainer.innerHTML = data.html;

            // Apply frontend category filtering after loading data
            if (categoryValue) {
              filterByCategory(categoryValue);
            }

            // Animate new cards (event listeners are handled by event delegation)
            animateCards();
          })
          .catch(error => {
            console.error('Filter error:', error);
            rewardsContainer.innerHTML = `
              <div class="col-12 text-center py-5">
                <div class="text-danger">
                  <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                  <h5>Error loading rewards</h5>
                  <p class="text-muted">Please try again later.</p>
                </div>
              </div>
            `;
          });
        }, 300); // 300ms debounce
      }

      // Frontend category filtering function
      function filterByCategory(selectedCategory) {
        if (!selectedCategory) return;

        const rewardCards = document.querySelectorAll('.reward-card');
        const shopSections = document.querySelectorAll('.shop-section');

        rewardCards.forEach(card => {
          const rewardName = card.dataset.name.toLowerCase();
          let matchesCategory = false;

          // Simple category matching based on reward name keywords
          switch (selectedCategory) {
            case 'food':
              matchesCategory = rewardName.includes('coffee') || rewardName.includes('drink') ||
                               rewardName.includes('meal') || rewardName.includes('food') ||
                               rewardName.includes('beverage') || rewardName.includes('tea');
              break;
            case 'discount':
              matchesCategory = rewardName.includes('discount') || rewardName.includes('%') ||
                               rewardName.includes('off') || rewardName.includes('coupon');
              break;
            case 'merchandise':
              matchesCategory = rewardName.includes('shirt') || rewardName.includes('mug') ||
                               rewardName.includes('bag') || rewardName.includes('merchandise') ||
                               rewardName.includes('item');
              break;
            case 'experience':
              matchesCategory = rewardName.includes('experience') || rewardName.includes('event') ||
                               rewardName.includes('class') || rewardName.includes('workshop');
              break;
            default:
              matchesCategory = true;
          }

          if (matchesCategory) {
            card.style.display = 'block';
          } else {
            card.style.display = 'none';
          }
        });

        // Hide shop sections with no visible rewards
        shopSections.forEach(section => {
          const visibleCards = section.querySelectorAll('.reward-card:not([style*="display: none"])');
          if (visibleCards.length === 0) {
            section.style.display = 'none';
          } else {
            section.style.display = 'block';
          }
        });
      }

      // Event listeners
      searchInput.addEventListener('input', applyFilters);
      categoryFilter.addEventListener('change', applyFilters);
      pointsFilter.addEventListener('change', applyFilters);
      sortFilter.addEventListener('change', applyFilters);

      // Animation functions
      function animateCards() {
        const rewardCards = document.querySelectorAll('.reward-card');
        rewardCards.forEach((card, index) => {
          card.style.opacity = '0';
          card.style.transform = 'translateY(30px)';
          card.style.transition = `all 0.6s ease ${index * 0.1}s`;

          setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
          }, index * 100);
        });
      }

      // Initial animation
      setTimeout(() => {
        animateCards();
      }, 100);

      // Animate cards on scroll
      const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
      };

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
          }
        });
      }, observerOptions);

      // Observe initially loaded cards
      document.querySelectorAll('.reward-card').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `all 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
      });
    });
  </script>
@endsection
