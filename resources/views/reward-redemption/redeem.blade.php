@extends('master')

@section('content')
  <div class="container-fluid min-vh-100 py-3">
    <div class="row justify-content-center">
      <div class="col-12 col-xl-8">

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
                    <h2 class="fw-bold mb-2">Redeem Reward</h2>
                    <p class="fw-light opacity-90 mb-0">Confirm your reward redemption</p>
                  </div>
                  <div class="text-end">
                    <div class="mb-2">
                      <small class="text-white opacity-75">Your Points</small>
                    </div>
                    <div class="display-6 fw-bold">
                      {{ number_format($currentTotal['coins'] ?? 0) }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
              <div class="card-header bg-white border-0 pb-0">
                <div class="d-flex justify-content-between align-items-center">
                  <h5 class="fw-semibold text-dark mb-0">
                    <i class="fas fa-check-circle text-primary me-2"></i>
                    Reward Details
                  </h5>
                  <a href="{{ route('reward.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Back to Rewards
                  </a>
                </div>
              </div>
              <div class="card-body">

                <!-- Display Validation Errors -->
                @if($errors->any())
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                      @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                @endif

                <!-- Reward Information -->
                <div class="row mb-4">
                  <!-- Reward Image -->
                  <div class="col-12 col-md-6 mb-4 mb-md-0">
                    <div class="reward-image-container">
                      <img src="{{ $reward->image_url ?? asset('images/default-reward.jpg') }}"
                           class="img-fluid rounded-3 w-100"
                           style="height: 300px; object-fit: cover;"
                           alt="{{ $reward->name }}">

                      <!-- Status Badges -->
                      <div class="position-absolute top-0 end-0 m-3">
                        @if($reward->stock > 0)
                          <span class="badge bg-success mb-2 d-block">
                            {{ $reward->stock }} Available
                          </span>
                        @else
                          <span class="badge bg-danger mb-2 d-block">
                            Out of Stock
                          </span>
                        @endif

                        <span class="badge bg-primary d-block">
                          @switch($reward->category)
                            @case('food')
                              <i class="fas fa-utensils me-1"></i>Food
                              @break
                            @case('discount')
                              <i class="fas fa-percentage me-1"></i>Discount
                              @break
                            @case('merchandise')
                              <i class="fas fa-tshirt me-1"></i>Merchandise
                              @break
                            @case('experience')
                              <i class="fas fa-star me-1"></i>Experience
                              @break
                            @default
                              <i class="fas fa-gift me-1"></i>Reward
                          @endswitch
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Reward Details -->
                  <div class="col-12 col-md-6">
                    <div class="reward-details">
                      <!-- Reward Name -->
                      <h3 class="fw-bold text-dark mb-3">{{ $reward->name }}</h3>

                      <!-- Store Information -->
                      <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                          <i class="fas fa-store text-primary me-2"></i>
                          <span class="fw-semibold">{{ $reward->store->name }}</span>
                        </div>
                        @if($reward->store->address)
                          <small class="text-muted ms-4">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            {{ $reward->store->address }}
                          </small>
                        @endif
                      </div>

                      <!-- Description -->
                      <div class="mb-3">
                        <h6 class="fw-semibold text-dark mb-2">Description</h6>
                        <p class="text-muted">{{ $reward->description }}</p>
                      </div>

                      <!-- Points Required -->
                      <div class="mb-3">
                        <div class="points-section bg-light rounded-3 p-3">
                          <div class="d-flex justify-content-between align-items-center">
                            <div>
                              <h6 class="fw-semibold text-dark mb-1">Points Required</h6>
                              <div class="h4 fw-bold text-primary mb-0">
                                {{ number_format($reward->points_required) }} pts
                              </div>
                            </div>
                            <div class="text-end">
                              <small class="text-muted d-block">Your Balance</small>
                              <div class="h5 fw-bold {{ ($currentTotal['coins'] ?? 0) >= $reward->points_required ? 'text-success' : 'text-danger' }} mb-0">
                                {{ number_format($currentTotal['coins'] ?? 0) }} pts
                              </div>
                            </div>
                          </div>

                          @if(($currentTotal['coins'] ?? 0) >= $reward->points_required)
                            <div class="progress mt-3" style="height: 8px;">
                              <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="mt-2">
                              <small class="text-success">
                                <i class="fas fa-check-circle me-1"></i>You have enough points!
                              </small>
                            </div>
                          @else
                            @php
                              $percentage = ($currentTotal['coins'] / $reward->points_required) * 100;
                            @endphp
                            <div class="progress mt-3" style="height: 8px;">
                              <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="mt-2">
                              <small class="text-danger">
                                <i class="fas fa-exclamation-circle me-1"></i>Need {{ number_format($reward->points_required - ($currentTotal['coins'] ?? 0)) }} more points
                              </small>
                            </div>
                          @endif
                        </div>
                      </div>

                      <!-- Additional Information -->
                      <div class="mb-3">
                        <div class="row g-3">
                          <div class="col-6">
                            <div class="info-item">
                              <i class="fas fa-clock text-primary me-2"></i>
                              <div>
                                <small class="text-muted d-block">Valid Until</small>
                                <span class="fw-medium">{{ $reward->expires_at ? $reward->expires_at->format('M d, Y') : 'No expiry' }}</span>
                              </div>
                            </div>
                          </div>
                          <div class="col-6">
                            <div class="info-item">
                              <i class="fas fa-users text-primary me-2"></i>
                              <div>
                                <small class="text-muted d-block">Redeemed</small>
                                <span class="fw-medium">{{ $reward->redeemed_count ?? 0 }} times</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Redemption Form -->
                @if($reward->stock > 0 && (($currentTotal['coins'] ?? 0) >= $reward->points_required))
                  <form method="POST" action="{{ route('reward.process', $reward->id) }}">
                    @csrf

                    <div class="redemption-form bg-light rounded-3 p-4">
                      <h6 class="fw-semibold text-dark mb-3">
                        <i class="fas fa-clipboard-check text-primary me-2"></i>
                        Redemption Confirmation
                      </h6>

                      <!-- Quantity Selection -->
                      <div class="row mb-3">
                        <div class="col-md-6">
                          <label for="quantity" class="form-label fw-semibold text-dark">
                            <i class="fas fa-sort-numeric-up me-2"></i>Quantity
                          </label>
                          <select class="form-select" id="quantity" name="quantity" required>
                            @for($i = 1; $i <= min(5, $reward->stock, floor(($currentTotal['coins'] ?? 0) / $reward->points_required)); $i++)
                              <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                          </select>
                          <div class="form-text">
                            <small class="text-muted">Maximum quantity based on your points and stock availability</small>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <label class="form-label fw-semibold text-dark">Total Cost</label>
                          <div class="total-cost-display bg-white border rounded p-3">
                            <div class="d-flex justify-content-between align-items-center">
                              <span class="text-muted">Points to spend:</span>
                              <span class="h5 fw-bold text-primary mb-0" id="totalPoints">
                                {{ number_format($reward->points_required) }} pts
                              </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                              <span class="text-muted">Remaining balance:</span>
                              <span class="fw-medium" id="remainingPoints">
                                {{ number_format(($currentTotal['coins'] ?? 0) - $reward->points_required) }} pts
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Special Instructions -->
                      <div class="mb-3">
                        <label for="instructions" class="form-label fw-semibold text-dark">
                          <i class="fas fa-sticky-note me-2"></i>Special Instructions (Optional)
                        </label>
                        <textarea class="form-control" id="instructions" name="instructions" rows="3"
                          placeholder="Any special requests or notes for the store..."></textarea>
                      </div>

                      <!-- Terms and Conditions -->
                      <div class="mb-4">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="agreeTerms" name="agree_terms" required>
                          <label class="form-check-label fw-medium" for="agreeTerms">
                            I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">terms and conditions</a>
                            and understand this redemption is final.
                          </label>
                        </div>
                      </div>

                      <!-- Action Buttons -->
                      <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('reward.index') }}" class="btn btn-outline-secondary">
                          <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4" id="redeemButton">
                          <i class="fas fa-gift me-2"></i>Confirm Redemption
                        </button>
                      </div>
                    </div>
                  </form>

                @elseif($reward->stock <= 0)
                  <!-- Out of Stock Message -->
                  <div class="alert alert-warning" role="alert">
                    <div class="d-flex align-items-center">
                      <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                      <div>
                        <h5 class="alert-heading mb-1">Out of Stock</h5>
                        <p class="mb-0">This reward is currently out of stock. Please check back later or browse other available rewards.</p>
                      </div>
                    </div>
                  </div>

                @else
                  <!-- Insufficient Points Message -->
                  <div class="alert alert-info" role="alert">
                    <div class="d-flex align-items-center">
                      <i class="fas fa-coins fa-2x me-3"></i>
                      <div>
                        <h5 class="alert-heading mb-1">Insufficient Points</h5>
                        <p class="mb-2">You need {{ number_format($reward->points_required - ($currentTotal['coins'] ?? 0)) }} more points to redeem this reward.</p>
                        <div class="mt-3">
                          <a href="{{ route('scan.receipt') }}" class="btn btn-primary me-2">
                            <i class="fas fa-qrcode me-2"></i>Earn More Points
                          </a>
                          <a href="{{ route('reward.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Browse Other Rewards
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                @endif

              </div>
            </div>
          </div>
        </div>

        <!-- Related Rewards Section -->
        @if($relatedRewards->count() > 0)
          <div class="row mt-5">
            <div class="col-12">
              <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-0 pb-0">
                  <h5 class="fw-semibold text-dark mb-0">
                    <i class="fas fa-star text-primary me-2"></i>
                    More from {{ $reward->store->name }}
                  </h5>
                </div>
                <div class="card-body">
                  <div class="row">
                    @foreach($relatedRewards->take(3) as $relatedReward)
                      <div class="col-12 col-md-4 mb-3">
                        <div class="card border-0 bg-light h-100">
                          <div class="card-body p-3">
                            <h6 class="card-title fw-bold text-dark mb-2">{{ $relatedReward->name }}</h6>
                            <p class="card-text small text-muted mb-2">
                              {{ Str::limit($relatedReward->description, 60) }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                              <span class="fw-bold text-primary">{{ number_format($relatedReward->points_required) }} pts</span>
                              <a href="{{ route('reward.redeem', $relatedReward->id) }}" class="btn btn-outline-primary btn-sm">
                                View
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endif

      </div>
    </div>
  </div>

  <!-- Terms and Conditions Modal -->
  <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="termsModalLabel">
            <i class="fas fa-file-contract text-primary me-2"></i>
            Redemption Terms & Conditions
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="terms-content">
            <h6 class="fw-semibold">Reward Redemption Policy</h6>
            <ul class="list-unstyled">
              <li class="mb-2">
                <i class="fas fa-check text-success me-2"></i>
                All reward redemptions are final and cannot be reversed once confirmed.
              </li>
              <li class="mb-2">
                <i class="fas fa-check text-success me-2"></i>
                Points will be deducted from your account immediately upon confirmation.
              </li>
              <li class="mb-2">
                <i class="fas fa-check text-success me-2"></i>
                You will receive a redemption code that must be presented to the store.
              </li>
              <li class="mb-2">
                <i class="fas fa-check text-success me-2"></i>
                Rewards must be claimed within the validity period specified.
              </li>
              <li class="mb-2">
                <i class="fas fa-check text-success me-2"></i>
                Store availability and terms may apply to reward usage.
              </li>
            </ul>

            <h6 class="fw-semibold mt-4">Important Notes</h6>
            <ul class="list-unstyled">
              <li class="mb-2">
                <i class="fas fa-info-circle text-info me-2"></i>
                Contact the store directly for any issues with reward usage.
              </li>
              <li class="mb-2">
                <i class="fas fa-info-circle text-info me-2"></i>
                Green Cups is not responsible for store-specific policies or availability.
              </li>
              <li class="mb-2">
                <i class="fas fa-info-circle text-info me-2"></i>
                Fraudulent use of rewards may result in account suspension.
              </li>
            </ul>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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

    /* Reward image container */
    .reward-image-container {
      position: relative;
    }

    /* Points section styling */
    .points-section {
      background: rgba(29, 209, 161, 0.05) !important;
      border: 1px solid rgba(29, 209, 161, 0.2);
    }

    /* Info items */
    .info-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    /* Form enhancements */
    .form-control, .form-select {
      border-radius: 0.5rem;
      border: 2px solid #e9ecef;
      transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
      border-color: #1dd1a1;
      box-shadow: 0 0 0 0.2rem rgba(29, 209, 161, 0.25);
    }

    /* Total cost display */
    .total-cost-display {
      background: rgba(255, 255, 255, 0.8) !important;
      border: 1px solid #e9ecef !important;
    }

    /* Card animations */
    .card {
      animation: slideUp 0.6s ease-out;
      transition: all 0.3s ease;
    }

    .card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12) !important;
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

    /* Badge enhancements */
    .badge {
      font-size: 0.75rem;
      padding: 6px 10px;
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

      .reward-details h3 {
        font-size: 1.5rem;
      }
    }

    /* Progress bar styling */
    .progress-bar {
      transition: width 1s ease-in-out;
    }

    .progress-bar.bg-success {
      background: linear-gradient(90deg, #22c55e, #16a34a) !important;
    }

    .progress-bar.bg-warning {
      background: linear-gradient(90deg, #f59e0b, #d97706) !important;
    }

    /* Enhanced visual feedback */
    .btn:focus {
      outline: 2px solid #1dd1a1;
      outline-offset: 2px;
    }

    /* Modal enhancements */
    .modal-content {
      border-radius: 1rem;
      border: none;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
      border-bottom: 1px solid #e9ecef;
      background: #f8f9fa;
      border-radius: 1rem 1rem 0 0;
    }

    /* Loading states */
    .btn:disabled {
      opacity: 0.6;
      transform: none !important;
    }

    /* Form check enhancements */
    .form-check-input:checked {
      background-color: #1dd1a1;
      border-color: #1dd1a1;
    }
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const quantitySelect = document.getElementById('quantity');
      const totalPointsSpan = document.getElementById('totalPoints');
      const remainingPointsSpan = document.getElementById('remainingPoints');
      const redeemButton = document.getElementById('redeemButton');
      const agreeTermsCheckbox = document.getElementById('agreeTerms');

      const pointsPerReward = {{ $reward->points_required }};
      const availablePoints = {{ $currentTotal['coins'] ?? 0 }};

      // Update total cost when quantity changes
      if (quantitySelect && totalPointsSpan && remainingPointsSpan) {
        quantitySelect.addEventListener('change', function() {
          const quantity = parseInt(this.value);
          const totalCost = quantity * pointsPerReward;
          const remaining = availablePoints - totalCost;

          totalPointsSpan.textContent = new Intl.NumberFormat().format(totalCost) + ' pts';
          remainingPointsSpan.textContent = new Intl.NumberFormat().format(remaining) + ' pts';

          // Update color based on remaining points
          if (remaining >= 0) {
            remainingPointsSpan.className = 'fw-medium text-success';
          } else {
            remainingPointsSpan.className = 'fw-medium text-danger';
          }
        });
      }

      // Enable/disable redeem button based on terms acceptance
      if (agreeTermsCheckbox && redeemButton) {
        function updateRedeemButton() {
          redeemButton.disabled = !agreeTermsCheckbox.checked;
        }

        agreeTermsCheckbox.addEventListener('change', updateRedeemButton);
        updateRedeemButton(); // Initial check
      }

      // Form submission handling
      const redeemForm = document.querySelector('form');
      if (redeemForm && redeemButton) {
        redeemForm.addEventListener('submit', function(e) {
          // Add loading state
          redeemButton.disabled = true;
          redeemButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

          // Show confirmation if needed
          const quantity = quantitySelect ? parseInt(quantitySelect.value) : 1;
          const totalCost = quantity * pointsPerReward;

          if (!confirm(`Confirm redemption of ${quantity} reward(s) for ${new Intl.NumberFormat().format(totalCost)} points?`)) {
            e.preventDefault();
            redeemButton.disabled = false;
            redeemButton.innerHTML = '<i class="fas fa-gift me-2"></i>Confirm Redemption';
          }
        });
      }

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

      // Observe all cards
      document.querySelectorAll('.card').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `all 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
      });
    });
  </script>
@endsection
