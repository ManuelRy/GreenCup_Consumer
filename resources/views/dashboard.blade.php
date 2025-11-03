@extends('master')

@section('content')
  <div class="container-fluid min-vh-100 py-3">
    <div class="row justify-content-center">
      <div class="col-12 col-xl-10">

        <!-- Guest Banner -->
        @if($consumer->id === null)
          @include('partials.guest-banner')
        @endif

        <!-- Motivational Message Section -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-success text-white rounded-4">
              <div class="card-body py-4 text-center">
                <div class="hero-icon mb-3">🌱</div>
                <h4 class="fw-bold mb-2">{{ $motivationalMessage }}</h4>
                <p class="mb-3 opacity-90">
                  @if(isset($environmentalData['total_units']) && $environmentalData['total_units'] > 0)
                    You've saved {{ number_format($environmentalData['total_units']) }} cups, preventing {{ number_format($environmentalData['co2_saved_grams']) }}g CO₂ and {{ number_format($environmentalData['waste_prevented_grams']) }}g waste!
                  @else
                    Start your eco-journey today and make a positive impact on our planet!
                  @endif
                </p>
                @if($consumer->id === null)
                  <!-- Guest Mode CTA -->
                  <div class="mt-4">
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg me-2 mb-2">
                      <i class="fas fa-user-plus me-2"></i>Sign Up to Track Your Impact
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg mb-2">
                      <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                  </div>
                @else
                  <div class="progress-badges d-flex justify-content-center gap-3 flex-wrap">
                    @if(isset($environmentalData['total_units']))
                      @if($environmentalData['total_units'] >= 1)
                        <span class="badge bg-white text-success px-3 py-2">🌱 Eco Beginner</span>
                      @endif
                      @if($environmentalData['total_units'] >= 10)
                        <span class="badge bg-white text-success px-3 py-2">🌿 Green Warrior</span>
                      @endif
                      @if($environmentalData['total_units'] >= 50)
                        <span class="badge bg-white text-success px-3 py-2">🌳 Planet Protector</span>
                      @endif
                      @if($environmentalData['total_units'] >= 100)
                        <span class="badge bg-white text-success px-3 py-2">🌍 Earth Champion</span>
                      @endif
                    @endif
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>

        <!-- Get Started Encouragement Banner (only for authenticated users with no activity) -->
        @if($consumer->id !== null && isset($environmentalData['total_units']) && $environmentalData['total_units'] == 0)
        <div class="row mb-4">
          <div class="col-12">
            <div class="card border-0 shadow-lg bg-gradient-primary text-white rounded-4 get-started-banner">
              <div class="card-body py-5">
                <div class="row align-items-center">
                  <div class="col-12 col-md-8 text-center text-md-start mb-4 mb-md-0">
                    <h3 class="fw-bold mb-3">
                      <i class="fas fa-rocket me-2"></i>Ready to Start Your Eco-Journey?
                    </h3>
                    <p class="mb-0 fs-5 opacity-90">
                      Take your first step towards a greener future! Visit a participating store, make an eco-friendly purchase, and scan your receipt to earn your first points.
                    </p>
                  </div>
                  <div class="col-12 col-md-4 text-center">
                    <a href="{{ route('scan.receipt') }}" class="btn btn-light btn-lg mb-2 d-block pulse-button">
                      <i class="fas fa-qrcode me-2"></i>Scan Your First Receipt
                    </a>
                    <a href="{{ route('map') }}" class="btn btn-outline-light d-block">
                      <i class="fas fa-map-marker-alt me-2"></i>Find Stores Near You
                    </a>
                  </div>
                </div>
                <div class="mt-4 pt-4 border-top border-light border-opacity-25">
                  <div class="row text-center">
                    <div class="col-4">
                      <div class="fs-2 mb-2">📱</div>
                      <div class="small fw-semibold">Scan Receipt</div>
                    </div>
                    <div class="col-4">
                      <div class="fs-2 mb-2">💰</div>
                      <div class="small fw-semibold">Earn Points</div>
                    </div>
                    <div class="col-4">
                      <div class="fs-2 mb-2">🎁</div>
                      <div class="small fw-semibold">Get Rewards</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif

        <!-- Points Section -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white text-center rounded-4">
              <div class="card-body py-5">
                <div class="display-2 fw-bold mb-2 animate-number">
                  @if(is_array($currentTotal))
                    {{ number_format($currentTotal['coins'] ?? 0) }}
                  @else
                    {{ number_format($currentTotal) }}
                  @endif
                </div>
                <h5 class="fw-light opacity-90 mb-0">Available Points</h5>
              </div>
            </div>
          </div>
        </div>

        <!-- Environmental Impact Section (inspired by environmental-impact page) -->
        @if($consumer->id !== null && isset($environmentalData['total_units']) && $environmentalData['total_units'] > 0)
        <div class="row mb-4">
          <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
              <div class="card-header bg-white border-0 pt-4">
                <h5 class="fw-bold text-dark mb-0">
                  <i class="fas fa-leaf text-success me-2"></i>
                  🌱 Your Environmental Impact
                </h5>
                <p class="text-muted small mb-0 mt-1">Real-time impact of your eco-friendly choices</p>
              </div>
              <div class="card-body">
                <div class="row g-3">
                  <!-- Cups Saved -->
                  <div class="col-6 col-md-4">
                    <div class="p-3 bg-light rounded-3 text-center h-100">
                      <div class="fs-3 mb-2">♻️</div>
                      <div class="h4 fw-bold text-success mb-1">{{ number_format($environmentalData['total_units']) }}</div>
                      <small class="text-muted fw-semibold">Cups Saved</small>
                    </div>
                  </div>

                  <!-- CO2 Saved -->
                  <div class="col-6 col-md-4">
                    <div class="p-3 bg-light rounded-3 text-center h-100">
                      <div class="fs-3 mb-2">🌱</div>
                      <div class="h4 fw-bold text-success mb-1">{{ number_format($environmentalData['co2_saved_grams']) }}<small class="fs-6">g</small></div>
                      <small class="text-muted fw-semibold">CO₂ Saved</small>
                      <div class="text-muted" style="font-size: 0.7rem;">({{ number_format($environmentalData['co2_saved'], 2) }}kg)</div>
                    </div>
                  </div>

                  <!-- Waste Prevented -->
                  <div class="col-6 col-md-4">
                    <div class="p-3 bg-light rounded-3 text-center h-100">
                      <div class="fs-3 mb-2">🗑️</div>
                      <div class="h4 fw-bold text-danger mb-1">{{ number_format($environmentalData['waste_prevented_grams']) }}<small class="fs-6">g</small></div>
                      <small class="text-muted fw-semibold">Waste Prevented</small>
                      <div class="text-muted" style="font-size: 0.7rem;">({{ number_format($environmentalData['waste_prevented'], 2) }}kg)</div>
                    </div>
                  </div>
                </div>

                <!-- View Full Impact Button -->
                <div class="text-center mt-3">
                  <a href="{{ route('environmental-impact.index') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-chart-line me-2"></i>View Full Impact Report
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif

        <!-- Analytics & Stats Row -->
        <div class="row mb-4">
          <!-- Analytics Donut Chart -->
          <div class="col-12 col-lg-6 mb-4 mb-lg-0">
            <div class="card border-0 shadow-sm h-100 rounded-3">
              <div class="card-header bg-white border-0 pb-0">
                <h5 class="fw-semibold text-dark mb-0">
                  <i class="fas fa-chart-pie text-primary me-2"></i>
                  Points Analytics
                </h5>
              </div>
              <div class="card-body text-center">
                @php
                  $pointsIn = $currentTotal['earned'] ?? 0;
                  $pointsOut = $currentTotal['spent'] ?? 0;
                  $total = $currentTotal['coins'] ?? $pointsIn + $pointsOut;

                  // Calculate percentages
                  if ($total > 0) {
                      $pointsInPercent = ($pointsIn / $total) * 100;
                      $pointsOutPercent = ($pointsOut / $total) * 100;
                  } else {
                      $pointsInPercent = 0;
                      $pointsOutPercent = 0;
                  }

                  // Calculate SVG arc lengths
                  $circumference = 2 * pi() * 40;
                  $pointsInLength = ($pointsInPercent / 100) * $circumference;
                  $pointsOutLength = ($pointsOutPercent / 100) * $circumference;
                @endphp

                <div class="position-relative d-inline-block mb-4">
                  <svg class="donut-chart" viewBox="0 0 100 100" style="width: 180px; height: 180px;">
                    @if ($total > 0)
                      <!-- Background circle -->
                      <circle cx="50" cy="50" r="40" fill="none" stroke="#f0f0f0" stroke-width="12" />

                      <!-- Points In -->
                      @if ($pointsIn > 0)
                        <circle cx="50" cy="50" r="40" fill="none" stroke="#22c55e" stroke-width="12"
                          stroke-dasharray="{{ $pointsInLength }} {{ $circumference - $pointsInLength }}" stroke-dashoffset="0" transform="rotate(-90 50 50)" class="animate-chart" />
                      @endif

                      <!-- Points Out -->
                      @if ($pointsOut > 0)
                        <circle cx="50" cy="50" r="40" fill="none" stroke="#ef4444" stroke-width="12"
                          stroke-dasharray="{{ $pointsOutLength }} {{ $circumference - $pointsOutLength }}" stroke-dashoffset="-{{ $pointsInLength }}" transform="rotate(-90 50 50)"
                          class="animate-chart" />
                      @endif
                    @else
                      <!-- Empty state circle -->
                      <circle cx="50" cy="50" r="40" fill="none" stroke="#e0e0e0" stroke-width="12" />
                    @endif
                  </svg>

                  <div class="position-absolute top-50 start-50 translate-middle text-center">
                    <div class="h3 fw-bold text-dark mb-0">{{ number_format($total) }}</div>
                    <small class="text-muted fw-medium">Total Activity</small>
                  </div>
                </div>

                <!-- Chart Legend -->
                <div class="d-flex justify-content-center gap-4">
                  <div class="d-flex align-items-center">
                    <span class="bg-success rounded me-2" style="width: 16px; height: 16px; display: inline-block;"></span>
                    <small class="text-muted fw-medium">Points Earned</small>
                  </div>
                  <div class="d-flex align-items-center">
                    <span class="bg-danger rounded me-2" style="width: 16px; height: 16px; display: inline-block;"></span>
                    <small class="text-muted fw-medium">Points Spent</small>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Stats Cards -->
          <div class="col-12 col-lg-6">
            <div class="row h-100">
              <!-- Points Earned Card -->
              <div class="col-12 col-sm-6 col-lg-12 mb-3">
                <div class="card border-0 shadow-sm h-100 rounded-3">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                      <h6 class="fw-semibold text-dark mb-0">Points Earned</h6>
                      <div class="text-success fs-4">
                        <i class="fas fa-trending-up"></i>
                      </div>
                    </div>
                    <div class="h3 fw-bold text-success mb-2">
                      {{ number_format($currentTotal['earned'] ?? 0) }}
                    </div>

                  </div>
                </div>
              </div>

              <!-- Points Spent Card -->
              <div class="col-12 col-sm-6 col-lg-12">
                <div class="card border-0 shadow-sm h-100 rounded-3">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                      <h6 class="fw-semibold text-dark mb-0">Points Spent</h6>
                      <div class="text-danger fs-4">
                        <i class="fas fa-credit-card"></i>
                      </div>
                    </div>
                    <div class="h3 fw-bold text-danger mb-2">
                      {{ number_format($currentTotal['spent'] ?? 0) }}
                    </div>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
              <div class="card-header bg-white border-0 pb-0">
                <h5 class="fw-semibold text-dark mb-0">
                  <i class="fas fa-bolt text-primary me-2"></i>
                  Quick Actions
                </h5>
              </div>
              <div class="card-body">
                <div class="row g-3">
                  @if($consumer->id !== null)
                    <div class="col-6 col-lg-3">
                      <a href="{{ route('account') }}" class="btn btn-outline-primary w-100 py-3 text-decoration-none action-btn">
                        <div class="fs-2 mb-2">
                          <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="fw-semibold small">Account</div>
                      </a>
                    </div>
                    <div class="col-6 col-lg-3">
                      <a href="{{ route('reward.index') }}" class="btn btn-outline-primary w-100 py-3 text-decoration-none action-btn">
                        <div class="fs-2 mb-2">
                          <i class="fas fa-gift"></i>
                        </div>
                        <div class="fw-semibold small">Rewards</div>
                      </a>
                    </div>
                  @endif
                  <div class="col-6 col-lg-3">
                    <a href="{{ $consumer->id !== null ? route('gallery') : route('guest.gallery') }}" class="btn btn-outline-primary w-100 py-3 text-decoration-none action-btn">
                      <div class="fs-2 mb-2">
                        <i class="fas fa-shopping-bag"></i>
                      </div>
                      <div class="fw-semibold small">Shop Gallery</div>
                    </a>
                  </div>
                  @if($consumer->id !== null)
                    <div class="col-6 col-lg-3">
                      <a href="{{ route('scan.receipt') }}" class="btn btn-primary w-100 py-3 text-decoration-none action-btn">
                        <div class="fs-2 mb-2">
                          <i class="fas fa-qrcode text-white"></i>
                        </div>
                        <div class="fw-semibold small text-white">Scan QR</div>
                      </a>
                    </div>
                  @endif
                  <div class="col-6 col-lg-3">
                    <a href="{{ $consumer->id !== null ? route('map') : route('guest.map') }}" class="btn btn-outline-primary w-100 py-3 text-decoration-none action-btn">
                      <div class="fs-2 mb-2">
                        <i class="fas fa-map-marker-alt"></i>
                      </div>
                      <div class="fw-semibold small">Locations</div>
                    </a>
                  </div>
                  <div class="col-6 col-lg-3">
                    <a href="{{ $consumer->id !== null ? route('environmental-impact.index') : route('guest.environmental-impact') }}" class="btn btn-outline-primary w-100 py-3 text-decoration-none action-btn">
                      <div class="fs-2 mb-2">
                        <i class="fas fa-leaf"></i>
                      </div>
                      <div class="fw-semibold small">Impacts</div>
                    </a>
                  </div>
                  @if($consumer->id === null)
                    <div class="col-6 col-lg-3">
                      <a href="{{ route('login') }}" class="btn btn-primary w-100 py-3 text-decoration-none action-btn">
                        <div class="fs-2 mb-2">
                          <i class="fas fa-sign-in-alt text-white"></i>
                        </div>
                        <div class="fw-semibold small text-white">Login</div>
                      </a>
                    </div>
                    <div class="col-6 col-lg-3">
                      <a href="{{ route('register') }}" class="btn btn-success w-100 py-3 text-decoration-none action-btn">
                        <div class="fs-2 mb-2">
                          <i class="fas fa-user-plus text-white"></i>
                        </div>
                        <div class="fw-semibold small text-white">Sign Up</div>
                      </a>
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
          <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
              <div class="card-header bg-white border-0 pb-0">
                <div class="d-flex justify-content-between align-items-center">
                  <h5 class="fw-semibold text-dark mb-0">
                    <i class="fas fa-history text-primary me-2"></i>
                    Recent Activity
                  </h5>
                  <a href="{{ route('account') }}" class="btn btn-outline-primary btn-sm">
                    View All
                    <i class="fas fa-arrow-right ms-1"></i>
                  </a>
                </div>
              </div>
              <div class="card-body">
                @forelse($recentActivity as $activity)
                  <div class="d-flex align-items-start py-3 border-bottom border-light activity-item">
                    <div class="me-3">
                      <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.2em;">
                        {{ $activity->icon }}
                      </div>
                    </div>
                    <div class="flex-grow-1 me-3">
                      <h6 class="fw-semibold text-dark mb-1">{{ $activity->name }}</h6>
                      <div class="text-muted small mb-1">{{ $activity->time_ago }}</div>
                      @if ($activity->store_name)
                        <div class="text-muted small">
                          <i class="fas fa-store me-1"></i>
                          {{ $activity->store_name }}
                        </div>
                      @endif
                    </div>
                    <div class="text-end">
                      <span class="fw-bold {{ $activity->type === 'earn' ? 'text-success' : 'text-danger' }}">
                        {{ $activity->type === 'earn' ? '+' : '-' }}{{ number_format($activity->points) }} pts
                      </span>
                    </div>
                  </div>
                @empty
                  <div class="text-center py-5">
                    <div class="mb-4">
                      <i class="fas fa-mobile-alt fa-4x text-muted opacity-50"></i>
                    </div>
                    <h6 class="fw-semibold text-dark mb-3">No recent activity</h6>
                    <p class="text-muted mb-4">Start scanning receipts to earn points!</p>
                    <a href="{{ route('scan.receipt') }}" class="btn btn-primary">
                      <i class="fas fa-qrcode me-2"></i>
                      Scan Receipt
                    </a>
                  </div>
                @endforelse
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    /* Custom CSS for enhanced styling */
    :root {
      --bs-primary: #1dd1a1;
      --bs-primary-rgb: 29, 209, 161;
      --bs-success: #22c55e;
      --bs-danger: #ef4444;
    }

    .bg-gradient-primary {
      background: linear-gradient(135deg, #1dd1a1, #10ac84) !important;
    }

    .bg-gradient-success {
      background: linear-gradient(135deg, #2d5a27 0%, #4a7c59 100%) !important;
      box-shadow: 0 10px 40px rgba(45, 90, 39, 0.3) !important;
    }

    .hero-icon {
      font-size: 3rem;
      display: inline-block;
      animation: bounce 2s infinite;
    }

    @keyframes bounce {
      0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
      40% { transform: translateY(-10px); }
      60% { transform: translateY(-5px); }
    }

    .progress-badges .badge {
      font-size: 0.9rem;
      font-weight: 600;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }

    .progress-badges .badge:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(0,0,0,0.15);
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

    .btn-success {
      background: linear-gradient(135deg, #22c55e, #16a34a);
      border: none;
      transition: all 0.3s ease;
    }

    .btn-success:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(34, 197, 94, 0.3);
      background: linear-gradient(135deg, #16a34a, #15803d);
    }

    .btn-light {
      transition: all 0.3s ease;
    }

    .btn-light:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(255, 255, 255, 0.3);
    }

    .btn-outline-light {
      border-color: rgba(255, 255, 255, 0.5);
      color: white;
      transition: all 0.3s ease;
    }

    .btn-outline-light:hover {
      background: rgba(255, 255, 255, 0.2);
      border-color: white;
      color: white;
      transform: translateY(-2px);
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

    .action-btn {
      transition: all 0.3s ease;
    }

    .action-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .action-btn:active {
      transform: translateY(-1px);
    }

    .activity-item {
      transition: all 0.3s ease;
    }

    .activity-item:hover {
      background: rgba(29, 209, 161, 0.02);
      border-radius: 0.5rem;
      margin: 0 -1rem;
      padding-left: 1rem !important;
      padding-right: 1rem !important;
    }

    .activity-item:last-child {
      border-bottom: none !important;
    }

    /* Chart Animations */
    .animate-chart {
      stroke-dasharray: 0;
      animation: drawChart 2s ease-out forwards;
    }

    @keyframes drawChart {
      from {
        stroke-dasharray: 0;
      }
    }

    .animate-number {
      animation: fadeInUp 1s ease-out;
    }

    /* Card animations */
    .card {
        animation: slideUp 0.6s ease-out;
    }    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Success/Error colors */
    .text-success {
      color: #22c55e !important;
    }

    .text-danger {
      color: #ef4444 !important;
    }

    .bg-success {
      background-color: #22c55e !important;
    }

    .bg-danger {
      background-color: #ef4444 !important;
    }

    /* Hover effects for cards */
    .card {
      transition: all 0.3s ease;
    }

    .card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12) !important;
    }

    /* Mobile optimizations */
    @media (max-width: 576px) {
      .container-fluid {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
      }

      .display-2 {
        font-size: 2.5rem !important;
      }

      .action-btn {
        padding: 1rem !important;
      }

      .donut-chart {
        width: 150px !important;
        height: 150px !important;
      }
    }

    /* Loading states */
    .btn:disabled {
      opacity: 0.6;
      transform: none !important;
    }

    /* Better focus indicators for accessibility */
    .btn:focus,
    .card:focus {
      outline: 2px solid #1dd1a1;
      outline-offset: 2px;
    }

    /* Rounded corners for better visual appeal */
    .rounded-4 {
      border-radius: 1.5rem !important;
    }

    /* Staggered animation for activity items */
    .activity-item:nth-child(1) {
      animation-delay: 0.1s;
    }

    .activity-item:nth-child(2) {
      animation-delay: 0.2s;
    }

    .activity-item:nth-child(3) {
      animation-delay: 0.3s;
    }

    .activity-item:nth-child(4) {
      animation-delay: 0.4s;
    }

    .activity-item:nth-child(5) {
      animation-delay: 0.5s;
    }

    /* Enhanced shadow for primary card */
    .bg-gradient-primary {
        box-shadow: 0 10px 40px rgba(29, 209, 161, 0.3) !important;
    }

    /* Get Started Banner Styles */
    .get-started-banner {
      animation: slideInUp 0.8s ease-out;
      background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 50%, #10b981 100%) !important;
    }

    @keyframes slideInUp {
      from {
        opacity: 0;
        transform: translateY(50px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Pulse animation for the main CTA button */
    .pulse-button {
      animation: pulse 2s infinite;
      box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
    }

    @keyframes pulse {
      0% {
        box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
        transform: scale(1);
      }
      50% {
        box-shadow: 0 0 0 10px rgba(255, 255, 255, 0);
        transform: scale(1.02);
      }
      100% {
        box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
        transform: scale(1);
      }
    }

    .pulse-button:hover {
      animation: none;
      transform: scale(1.05) !important;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
    }

    /* Mobile responsiveness for get started banner */
    @media (max-width: 768px) {
      .get-started-banner h3 {
        font-size: 1.5rem;
      }
      .get-started-banner p {
        font-size: 1rem !important;
      }
    }
  </style>

  <!-- Font Awesome (if not already included) -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

  <script>
    // Enhanced mobile experience
    document.addEventListener('DOMContentLoaded', function() {
      // Animate numbers on load
      const numberElements = document.querySelectorAll('.animate-number');
      numberElements.forEach(el => {
        const finalNumber = el.textContent.replace(/,/g, '');
        if (!isNaN(finalNumber)) {
          let currentNumber = 0;
          const increment = Math.ceil(finalNumber / 50);
          const timer = setInterval(() => {
            currentNumber += increment;
            if (currentNumber >= finalNumber) {
              currentNumber = finalNumber;
              clearInterval(timer);
            }
            el.textContent = new Intl.NumberFormat().format(currentNumber);
          }, 30);
        }
      });

      // Add loading states for action buttons
      document.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
          // Add a subtle loading state
          this.style.opacity = '0.7';
          setTimeout(() => {
            this.style.opacity = '';
          }, 200);
        });
      });

      // Intersection Observer for animations
      const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
      };

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
          }
        });
      }, observerOptions);

      // Observe all cards for scroll animations
      document.querySelectorAll('.card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
      });
    });

    // Touch feedback for mobile devices
    if ('ontouchstart' in window) {
      document.addEventListener('touchstart', function(e) {
        if (e.target.closest('.action-btn, .btn')) {
          e.target.closest('.action-btn, .btn').style.transform = 'scale(0.98)';
        }
      }, {
        passive: true
      });

      document.addEventListener('touchend', function(e) {
        if (e.target.closest('.action-btn, .btn')) {
          setTimeout(() => {
            e.target.closest('.action-btn, .btn').style.transform = '';
          }, 100);
        }
      }, {
        passive: true
      });
    }
  </script>
@endsection
