@php
  use App\Repository\ConsumerPointRepository;
  $cPRepo = new ConsumerPointRepository();
@endphp

@forelse($sellers as $seller)
  @php
    $coins = $cPRepo->getByConsumerAndSeller(Auth::id(), $seller->id)->coins;
  @endphp
  <div class="mb-5 shop-section" data-shop-name="{{ strtolower($seller->business_name) }}">
    <div class="d-flex align-items-center mb-2 flex-wrap gap-2">
      <h4 class="fw-bold mb-0"><i class="fas fa-store me-2"></i>{{ $seller->business_name }}</h4>
      <span class="badge bg-success ms-3">Wallet: {{ number_format($coins) }} pts</span>
    </div>
    <div class="row">
      @forelse($seller->rewards as $reward)
        @php
          // Manual reward data serialization to avoid Eloquent issues
          $rewardData = [
            'id' => $reward->id,
            'name' => $reward->name,
            'description' => $reward->description,
            'points_required' => $reward->points_required,
            'quantity' => $reward->quantity ?? 0,
            'quantity_redeemed' => $reward->quantity_redeemed ?? 0,
            'available_qty' => ($reward->quantity ?? 0) - ($reward->quantity_redeemed ?? 0),
            'image_url' => $reward->image_url,
            'category' => $reward->category,
            'is_available' => $reward->is_available
          ];
          $availableQty = $rewardData['available_qty'];
        @endphp
        <div class="col-6 col-md-4 col-lg-3 mb-3 mb-md-4 reward-card"
             data-category="{{ $reward->category ?? '' }}"
             data-points="{{ $reward->points_required }}"
             data-name="{{ strtolower($reward->name) }}"
             data-shop="{{ strtolower($seller->business_name) }}">
          <div class="modern-reward-card h-100 reward-preview-card"
               data-reward='@json($rewardData)'
               data-seller_id='@json($seller->id)'
               data-shop='@json($seller->business_name)'
               onclick="showRewardPreview(this); event.stopPropagation();"
               title="Click to preview this reward">

            <!-- Card Image Section -->
            <div class="reward-card-image-wrapper">
              @if($reward->image_url)
                <img src="{{ $reward->image_url }}" class="reward-card-img" alt="{{ $reward->name }}">
              @else
                <div class="reward-card-no-image">
                  <i class="fas fa-gift"></i>
                </div>
              @endif

              <!-- Status Badge -->
              @if ($coins >= $reward->points_required && $availableQty > 0)
                <div class="reward-status-badge available">
                  <i class="fas fa-check-circle"></i>
                </div>
              @elseif($availableQty <= 0)
                <div class="reward-status-badge out-of-stock">
                  <i class="fas fa-times-circle"></i>
                </div>
              @else
                <div class="reward-status-badge locked">
                  <i class="fas fa-lock"></i>
                </div>
              @endif

              <!-- Hover Overlay -->
              <div class="reward-hover-overlay">
                <div class="reward-hover-content">
                  <i class="fas fa-eye"></i>
                  <span>View Details</span>
                </div>
              </div>
            </div>

            <!-- Card Content -->
            <div class="reward-card-content">
              <h6 class="reward-card-title">{{ $reward->name }}</h6>

              <div class="reward-card-points">
                <i class="fas fa-coins"></i>
                <span>{{ number_format($reward->points_required) }}</span>
              </div>

              <div class="reward-card-stock">
                <i class="fas fa-box"></i>
                <span>{{ $availableQty }} left</span>
                @if($availableQty <= 5 && $availableQty > 0)
                  <span class="stock-warning">⚠️</span>
                @endif
              </div>

              <!-- Redeem Button -->
              @if ($availableQty <= 0)
                <button class="reward-redeem-btn out-of-stock" disabled>
                  <i class="fas fa-times-circle"></i>
                  <span class="d-none d-md-inline">Out of Stock</span>
                  <span class="d-md-none">N/A</span>
                </button>
              @elseif ($coins >= $reward->points_required)
                <button class="reward-redeem-btn available redeem-btn"
                        data-reward='@json($rewardData)'
                        data-seller_id='@json($seller->id)'
                        data-shop='@json($seller->business_name)'
                        data-debug-reward-id="{{ $reward->id ?? 'NULL' }}"
                        data-debug-reward-name="{{ $reward->name ?? 'NULL' }}"
                        onclick="event.stopPropagation();">
                  <i class="fas fa-gift"></i>
                  <span class="d-none d-md-inline">Redeem</span>
                  <span class="d-md-none">Get</span>
                </button>
              @else
                <button class="reward-redeem-btn locked" disabled>
                  <i class="fas fa-lock"></i>
                  <span class="d-none d-md-inline">Insufficient Points</span>
                  <span class="d-md-none">Need Pts</span>
                </button>
              @endif
            </div>
          </div>
        </div>
      @empty
        <div class="col-12">
          <div class="text-center py-4">
            <i class="fas fa-gift text-muted fs-1 mb-3"></i>
            <p class="text-muted">No rewards available for this shop yet.</p>
          </div>
        </div>
      @endforelse
    </div>
  </div>
@empty
  <div class="col-12">
    <div class="text-center py-5">
      <i class="fas fa-store-slash text-muted" style="font-size: 4rem;"></i>
      <h4 class="text-muted mt-3">No rewards found</h4>
      <p class="text-muted">Try adjusting your filters or check back later for new rewards.</p>
    </div>
  </div>
@endforelse