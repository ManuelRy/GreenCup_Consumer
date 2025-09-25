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
                <div class="d-flex align-items-center justify-content-between">
                  <div>
                    <div class="mb-3">
                      <i class="fas fa-gift fa-3x opacity-90"></i>
                    </div>
                    <h2 class="fw-bold mb-2">Reward Gallery</h2>
                    <p class="fw-light opacity-90 mb-0">Discover and redeem amazing rewards with your points</p>
                  </div>
                  {{-- <div class="text-end">
                    <div class="mb-2">
                      <small class="text-white opacity-75">Available Points</small>
                    </div>
                    <div class="display-6 fw-bold">
                      {{ number_format($currentTotal['coins'] ?? 0) }}
                    </div>
                  </div> --}}
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
                <div class="row g-3 align-items-end">
                  <!-- Search Input -->
                  <div class="col-12 col-md-6 col-lg-4">
                    <label class="form-label fw-semibold text-dark">
                      <i class="fas fa-search me-2"></i>Search Rewards
                    </label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Find rewards...">
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
        @forelse($sellers as $seller)
          @php
            $coins = $cPRepo->getByConsumerAndSeller(Auth::id(), $seller->id)->coins;
          @endphp
          <div class="mb-5">
            <div class="d-flex align-items-center mb-2">
              <h4 class="fw-bold mb-0"><i class="fas fa-store me-2"></i>{{ $seller->business_name }}</h4>
              <span class="badge bg-success ms-3">Wallet: {{ number_format($coins) }} pts</span>
            </div>
            <div class="row" id="rewardsContainer">
              @forelse($seller->rewards as $reward)
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3 mb-4 reward-card" data-category="{{ '' }}" data-points="{{ $reward->points_required }}"
                  data-name="{{ strtolower($reward->name) }}">
                  <div class="card border-0 shadow-sm rounded-3 h-100 reward-item">
                    <div class="position-relative">
                      <img src="{{ $reward->image_url ?? asset('images/default-reward.jpg') }}" class="card-img-top rounded-top-3" style="height: 200px; object-fit: cover;"
                        alt="{{ $reward->name }}">
                      <span class="badge bg-primary position-absolute top-0 start-0 m-2">
                        {{ $seller->business_name }}
                      </span>
                    </div>
                    <div class="card-body d-flex flex-column">
                      <h5 class="card-title fw-bold text-dark mb-2">{{ $reward->name }}: {{ $reward->quantity - $reward->quantity_redeemed }} left</h5>
                      <p class="card-text text-muted small mb-3 flex-grow-1">
                        {{ Str::limit($reward->description ?? '', 80) }}
                      </p>
                      <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="points-required">
                          <span class="h5 fw-bold text-primary mb-0">
                            {{ number_format($reward->points_required) }}
                          </span>
                          <small class="text-muted"> points</small>
                        </div>
                        @if ($coins >= $reward->points_required)
                          <span class="badge bg-success">
                            <i class="fas fa-check me-1"></i>Can Afford
                          </span>
                        @else
                          <span class="badge bg-light text-dark">
                            <i class="fas fa-lock me-1"></i>Need More
                          </span>
                        @endif
                      </div>
                      <div class="mt-auto">
                        @if ($coins >= $reward->points_required)
                          <button class="btn btn-primary w-100 redeem-btn" data-reward='@json($reward)' data-seller_id='@json($seller->id)'
                            data-shop='@json($seller->business_name)'>
                            <i class="fas fa-gift me-2"></i>Request Redeem
                          </button>
                        @else
                          <button class="btn btn-outline-primary w-100" disabled>
                            <i class="fas fa-coins me-2"></i>Insufficient Points
                          </button>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
              @empty
                <div class="col-12">
                  <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body text-center py-5">
                      <h6 class="fw-semibold text-dark mb-3">No rewards available for this shop.</h6>
                    </div>
                  </div>
                </div>
              @endforelse
            </div>
          </div>
        @empty
          <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
              <div class="card-body text-center py-5">
                <div class="mb-4">
                  <i class="fas fa-gift fa-4x text-muted opacity-50"></i>
                </div>
                <h4 class="fw-bold text-dark mb-3">No Rewards Available</h4>
                <p class="text-muted mb-4">
                  Check back later for exciting rewards from your favorite stores!
                </p>
                <a href="{{ route('gallery') }}" class="btn btn-primary">
                  <i class="fas fa-store me-2"></i>Browse Stores
                </a>
              </div>
            </div>
          </div>
        @endforelse
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
          <button type="button" class="btn btn-success" id="mockConfirmRedeem">Confirm Request</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      let selectedReward = null;
      let selectedSellerId = null;
      let selectedShop = null;
      document.querySelectorAll('.redeem-btn').forEach(btn => {
        btn.addEventListener('click', function() {
          selectedReward = JSON.parse(this.dataset.reward);
          selectedShop = this.dataset.shop;
          selectedSellerId = this.dataset.seller_id;
          document.getElementById('mock-reward-details').innerHTML = `
            <div class='text-center mb-3'>
              <img src="${selectedReward.image_url}" class="img-fluid rounded mb-2" style="max-height:120px;">
              <h5 class="fw-bold mt-2">${selectedReward.name}</h5>
              <div class="mb-2">From <b>${selectedShop}</b></div>
              <div class="mb-2"><span class="badge bg-success">${selectedReward.points_required} pts</span></div>
            </div>
          `;
          new bootstrap.Modal(document.getElementById('mockRedeemModal')).show();
        });
      });
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
    });
  </script>
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
      border-left: 4px solid #1dd1a1 !important;
    }

    .reward-item:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
    }

    /* Points styling */
    .points-required .h5 {
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
    }

    /* Enhanced visual feedback */
    .btn:focus {
      outline: 2px solid #1dd1a1;
      outline-offset: 2px;
    }

    /* Pagination styling */
    .pagination .page-link {
      color: #1dd1a1;
      border-color: #e9ecef;
    }

    .pagination .page-link:hover {
      color: #10ac84;
      background-color: rgba(29, 209, 161, 0.1);
      border-color: #1dd1a1;
    }

    .pagination .page-item.active .page-link {
      background-color: #1dd1a1;
      border-color: #1dd1a1;
    }

    /* Filter section enhancements */
    .form-label {
      font-size: 0.9rem;
      margin-bottom: 0.5rem;
    }

    /* Staggered animation for reward cards */
    .reward-card:nth-child(1) {
      animation-delay: 0.1s;
    }

    .reward-card:nth-child(2) {
      animation-delay: 0.2s;
    }

    .reward-card:nth-child(3) {
      animation-delay: 0.3s;
    }

    .reward-card:nth-child(4) {
      animation-delay: 0.4s;
    }

    .reward-card:nth-child(5) {
      animation-delay: 0.5s;
    }

    .reward-card:nth-child(6) {
      animation-delay: 0.6s;
    }

    .reward-card:nth-child(7) {
      animation-delay: 0.7s;
    }

    .reward-card:nth-child(8) {
      animation-delay: 0.8s;
    }

    /* Loading states */
    .btn:disabled {
      opacity: 0.6;
      transform: none !important;
    }
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.getElementById('searchInput');
      const categoryFilter = document.getElementById('categoryFilter');
      const pointsFilter = document.getElementById('pointsFilter');
      const sortFilter = document.getElementById('sortFilter');
      const rewardsContainer = document.getElementById('rewardsContainer');
      const rewardCards = document.querySelectorAll('.reward-card');

      // Filter function
      function filterRewards() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value;
        const selectedPointsRange = pointsFilter.value;

        let visibleCards = [];

        rewardCards.forEach(card => {
          const name = card.dataset.name;
          const category = card.dataset.category;
          const points = parseInt(card.dataset.points);

          let showCard = true;

          // Search filter
          if (searchTerm && !name.includes(searchTerm)) {
            showCard = false;
          }

          // Category filter
          if (selectedCategory && category !== selectedCategory) {
            showCard = false;
          }

          // Points range filter
          if (selectedPointsRange) {
            const [min, max] = selectedPointsRange.split('-').map(p => p.replace('+', ''));
            const minPoints = parseInt(min);
            const maxPoints = max ? parseInt(max) : Infinity;

            if (points < minPoints || points > maxPoints) {
              showCard = false;
            }
          }

          if (showCard) {
            card.style.display = 'block';
            visibleCards.push(card);
          } else {
            card.style.display = 'none';
          }
        });

        // Sort visible cards
        sortCards(visibleCards);

        // Show/hide empty state
        const emptyState = document.querySelector('.col-12 .card .card-body.text-center');
        if (visibleCards.length === 0 && emptyState) {
          emptyState.closest('.col-12').style.display = 'block';
        } else if (emptyState) {
          emptyState.closest('.col-12').style.display = 'none';
        }
      }

      // Sort function
      function sortCards(cards) {
        const sortBy = sortFilter.value;

        cards.sort((a, b) => {
          const aPoints = parseInt(a.dataset.points);
          const bPoints = parseInt(b.dataset.points);

          switch (sortBy) {
            case 'points-low':
              return aPoints - bPoints;
            case 'points-high':
              return bPoints - aPoints;
            case 'newest':
              // Assuming newest first (could be enhanced with actual date data)
              return 0;
            case 'popular':
              // Could be enhanced with popularity data
              return 0;
            default:
              return 0;
          }
        });

        // Reorder DOM elements
        cards.forEach(card => {
          rewardsContainer.appendChild(card);
        });
      }

      // Event listeners
      searchInput.addEventListener('input', filterRewards);
      categoryFilter.addEventListener('change', filterRewards);
      pointsFilter.addEventListener('change', filterRewards);
      sortFilter.addEventListener('change', filterRewards);

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

      // Observe all reward cards
      rewardCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `all 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
      });
    });
  </script>
@endsection
