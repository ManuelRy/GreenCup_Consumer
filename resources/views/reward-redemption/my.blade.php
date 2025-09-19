@extends('master')

@section('content')
@php
$redemptions = [
  (object)[
    'reward_name' => 'Free Coffee',
    'store_name' => 'Green Cafe',
    'description' => 'Enjoy a free cup of our signature coffee.',
    'image_url' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80',
    'created_at' => now()->subDays(2),
  ],
  (object)[
    'reward_name' => '10% Discount',
    'store_name' => 'Green Cafe',
    'description' => 'Get 10% off your next purchase.',
    'image_url' => 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=400&q=80',
    'created_at' => now()->subDays(5),
  ],
];
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
                      <i class="fas fa-trophy fa-3x opacity-90"></i>
                    </div>
                    <h2 class="fw-bold mb-2">My Rewards</h2>
                    <p class="fw-light opacity-90 mb-0">All rewards you have redeemed</p>
                  </div>
                  <div class="text-end">
                    <div class="mb-2">
                      <small class="text-white opacity-75">Total Redeemed</small>
                    </div>
                    <div class="display-6 fw-bold">
                      {{ count($redemptions) }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Rewards Grid -->
        <div class="row">
          @forelse($redemptions as $redemption)
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3 mb-4 reward-card">
              <div class="card border-0 shadow-sm rounded-3 h-100 reward-item">
                <div class="position-relative">
                  <img src="{{ $redemption->image_url ?? asset('images/default-reward.jpg') }}"
                       class="card-img-top rounded-top-3"
                       style="height: 200px; object-fit: cover;"
                       alt="{{ $redemption->reward_name ?? 'Reward' }}">
                  <span class="badge bg-success position-absolute top-0 start-0 m-2">
                    <i class="fas fa-check me-1"></i>Redeemed
                  </span>
                </div>
                <div class="card-body d-flex flex-column">
                  <h5 class="card-title fw-bold text-dark mb-2">{{ $redemption->reward_name ?? 'Reward' }}</h5>
                  <div class="mb-2">
                    <small class="text-muted"><i class="fas fa-store me-1"></i>{{ $redemption->store_name ?? '' }}</small>
                  </div>
                  <p class="card-text text-muted small mb-3 flex-grow-1">
                    {{ Str::limit($redemption->description ?? '', 80) }}
                  </p>
                  <div class="mb-3">
                    <span class="badge bg-primary">Redeemed: {{ $redemption->created_at ? $redemption->created_at->format('M d, Y') : '' }}</span>
                  </div>
                  <div class="mt-auto">
                    <span class="badge bg-success w-100 p-2">
                      <i class="fas fa-check me-1"></i>Successfully Redeemed
                    </span>
                  </div>
                </div>
              </div>
            </div>
          @empty
            <div class="col-12">
              <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body text-center py-5">
                  <div class="mb-4">
                    <i class="fas fa-gift fa-4x text-muted opacity-50"></i>
                  </div>
                  <h4 class="fw-bold text-dark mb-3">No Rewards Redeemed Yet</h4>
                  <p class="text-muted mb-4">
                    Start exploring and redeem your first reward from our amazing collection!
                  </p>
                  <a href="{{ route('reward.index') }}" class="btn btn-primary">
                    <i class="fas fa-gift me-2"></i>Browse Rewards
                  </a>
                </div>
              </div>
            </div>
          @endforelse
        </div>

      </div>
    </div>
  </div>

  <style>
    /* Same styles as reward gallery */
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

    .reward-item {
      transition: all 0.3s ease;
      border-left: 4px solid #1dd1a1 !important;
    }

    .reward-item:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
    }

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

    .badge {
      font-size: 0.75rem;
      padding: 4px 8px;
      font-weight: 600;
    }

    @media (max-width: 768px) {
      .container-fluid {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
      }
      .display-6 {
        font-size: 2rem !important;
      }
    }

    .reward-card:nth-child(1) { animation-delay: 0.1s; }
    .reward-card:nth-child(2) { animation-delay: 0.2s; }
    .reward-card:nth-child(3) { animation-delay: 0.3s; }
    .reward-card:nth-child(4) { animation-delay: 0.4s; }
  </style>
@endsection
