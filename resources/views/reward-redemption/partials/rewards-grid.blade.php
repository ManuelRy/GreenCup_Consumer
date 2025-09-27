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
        <div class="col-12 col-sm-6 col-lg-4 col-xl-3 mb-4 reward-card" 
             data-category="{{ $reward->category ?? '' }}" 
             data-points="{{ $reward->points_required }}"
             data-name="{{ strtolower($reward->name) }}"
             data-shop="{{ strtolower($seller->business_name) }}">
          <div class="card h-100 reward-item border-0 shadow-sm reward-preview-card" 
               data-reward='@json($rewardData)' 
               data-seller_id='@json($seller->id)'
               data-shop='@json($seller->business_name)'
               style="cursor: pointer; transition: all 0.3s ease;"
               onclick="showRewardPreview(this); event.stopPropagation();"
               title="Click to preview this reward">
            @if ($coins >= $reward->points_required && $availableQty > 0)
              <div class="position-absolute top-0 end-0 z-3">
                <div class="bg-success text-white px-3 py-1 rounded-bottom-start-3 fw-semibold small">
                  AVAILABLE
                </div>
              </div>
            @endif
            
            @if($reward->image_url)
              <div class="position-relative overflow-hidden">
                <img src="{{ $reward->image_url }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="{{ $reward->name }}">
                <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-white p-3">
                  <div class="d-flex justify-content-between align-items-end">
                    <div>
                      <div class="h4 fw-bold mb-0">{{ number_format($reward->points_required) }}</div>
                      <div class="small opacity-75">POINTS</div>
                    </div>
                    <div class="text-end">
                      <div class="small opacity-75 mb-1">{{ $availableQty }} left</div>
                      <i class="fas fa-eye fs-4"></i>
                    </div>
                  </div>
                </div>
              </div>
            @else
              <div class="d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                <div class="text-center text-muted">
                  <i class="fas fa-image fs-1 mb-2"></i>
                  <div>No Image</div>
                </div>
              </div>
            @endif

            <div class="card-body p-3">
              <h6 class="card-title fw-bold mb-2">{{ $reward->name }}</h6>
              <p class="card-text text-muted small mb-3">{{ Str::limit($reward->description, 80) }}</p>

              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-primary">{{ $reward->category ?? 'General' }}</span>
                <span class="fw-bold text-success">{{ number_format($reward->points_required) }} pts</span>
              </div>
              
              <div class="d-flex justify-content-between align-items-center mb-3">
                <small class="text-muted">
                  <i class="fas fa-box me-1"></i>Stock: {{ $availableQty }} available
                </small>
                @if($availableQty <= 5 && $availableQty > 0)
                  <small class="text-warning">
                    <i class="fas fa-exclamation-triangle me-1"></i>Limited Stock!
                  </small>
                @elseif($availableQty == 0)
                  <small class="text-danger">
                    <i class="fas fa-times-circle me-1"></i>Out of Stock
                  </small>
                @endif
              </div>

              @if ($availableQty <= 0)
                <button class="btn btn-danger w-100 fw-semibold" disabled>
                  <i class="fas fa-times-circle me-2"></i>OUT OF STOCK
                </button>
              @elseif ($coins >= $reward->points_required)
                <button class="btn btn-success w-100 fw-semibold redeem-btn" 
                        data-reward='@json($rewardData)' 
                        data-seller_id='@json($seller->id)'
                        data-shop='@json($seller->business_name)'
                        data-debug-reward-id="{{ $reward->id ?? 'NULL' }}"
                        data-debug-reward-name="{{ $reward->name ?? 'NULL' }}"
                        onclick="event.stopPropagation();">
                  <i class="fas fa-gift me-2"></i>REDEEM REWARD
                </button>
              @else
                <button class="btn btn-secondary w-100 fw-semibold" disabled>
                  <i class="fas fa-lock me-2"></i>Insufficient Points
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