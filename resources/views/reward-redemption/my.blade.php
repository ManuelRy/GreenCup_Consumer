@extends('master')

@section('content')
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
                            <div class="card h-100 reward-item border-0 shadow-sm">
                                @php
                                    // Determine status based on the status field
                                    if ($redemption->status === 'approved' || $redemption->is_redeemed) {
                                        $statusClass = 'success';
                                        $statusText = 'APPROVED';
                                        $statusIcon = 'check-circle';
                                    } elseif ($redemption->status === 'rejected') {
                                        $statusClass = 'danger';
                                        $statusText = 'REJECTED';
                                        $statusIcon = 'times-circle';
                                    } else {
                                        $statusClass = 'warning';
                                        $statusText = 'PENDING';
                                        $statusIcon = 'clock';
                                    }
                                @endphp

                                <div class="position-absolute top-0 end-0 z-3">
                                    <div class="bg-{{ $statusClass }} text-white px-3 py-1 rounded-bottom-start-3 fw-semibold small">
                                        {{ $statusText }}
                                    </div>
                                </div>

                                @if($redemption->reward && $redemption->reward->image_url)
                                    <div class="position-relative overflow-hidden">
                                        <img src="{{ $redemption->reward->image_url }}" class="card-img-top"
                                                 style="height: 200px; object-fit: cover;"
                                                 alt="{{ $redemption->reward->name ?? 'Reward' }}"
                                                 onerror="console.log('Image failed to load:', this.src); this.style.display='none'; this.nextElementSibling.style.display='block';">
                                        <div class="reward-default-card position-relative" style="display: none;">
                                            <div class="position-absolute top-0 end-0 bottom-0 start-0 bg-success"></div>
                                            <div class="position-relative p-4 text-white" style="height: 200px;">
                                                <div class="d-flex flex-column h-100">
                                                    <div class="mb-auto">
                                                        <div class="h6 fw-bold mb-0">REDEEMED</div>
                                                        <div class="small opacity-75">REWARD</div>
                                                    </div>
                                                    <div class="mb-auto">
                                                        <div class="h5 fw-bold mb-1">{{ $redemption->reward->name ?? 'Reward' }}</div>
                                                        <div class="small opacity-75">{{ $redemption->created_at ? $redemption->created_at->format('M d, Y') : '' }}</div>
                                                    </div>
                                                    <div class="text-end">
                                                        <i class="fas fa-{{ $statusIcon }} fa-2x opacity-75"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-white p-3">
                                            <div class="d-flex justify-content-between align-items-end">
                                                <div>
                                                    <div class="h6 fw-bold mb-0">{{ $redemption->reward->name ?? 'Reward' }}</div>
                                                    <div class="small opacity-75">{{ $redemption->created_at ? $redemption->created_at->format('M d, Y') : '' }}</div>
                                                </div>
                                                <div class="text-end">
                                                    <i class="fas fa-{{ $statusIcon }} fa-lg"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="reward-default-card position-relative">
                                        <div class="position-absolute top-0 end-0 bottom-0 start-0 bg-{{ $statusClass }}"></div>
                                        <div class="position-relative p-4 text-white" style="height: 200px;">
                                            <div class="d-flex flex-column h-100">
                                                <div class="mb-auto">
                                                    <div class="h6 fw-bold mb-0">REDEEMED</div>
                                                    <div class="small opacity-75">REWARD</div>
                                                </div>
                                                <div class="mb-auto">
                                                    <div class="h5 fw-bold mb-1">{{ $redemption->reward->name ?? 'Reward' }}</div>
                                                    <div class="small opacity-75">{{ $redemption->created_at ? $redemption->created_at->format('M d, Y') : '' }}</div>
                                                </div>
                                                <div class="text-end">
                                                    <i class="fas fa-{{ $statusIcon }} fa-2x opacity-75"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="card-body">
                                    <h6 class="card-title fw-bold mb-2 d-flex align-items-center gap-2">
                                        <i class="fas fa-{{ $statusIcon === 'check-circle' ? 'trophy' : ($statusIcon === 'times-circle' ? 'ban' : 'hourglass-half') }} text-{{ $statusClass }}"></i>
                                        {{ $redemption->reward->name ?? 'Reward' }}
                                    </h6>
                                    @if($redemption->reward && $redemption->reward->seller)
                                        <div class="mb-2">
                                            <small class="text-muted d-flex align-items-center gap-1">
                                                <i class="fas fa-store"></i>{{ $redemption->reward->seller->business_name }}
                                            </small>
                                        </div>
                                    @endif
                                    <div class="mb-2">
                                        <span class="badge bg-primary text-white">
                                            <i class="fas fa-box me-1"></i>Quantity: {{ $redemption->quantity ?? 1 }}
                                        </span>
                                    </div>
                                    <p class="card-text text-muted small mb-3">
                                        {{ Str::limit($redemption->reward->description ?? 'Reward successfully redeemed.', 70) }}
                                    </p>
                                    <div class="mb-3">
                                        <span class="badge bg-light text-muted px-2 py-1">
                                            {{ $redemption->created_at ? $redemption->created_at->format('M d, Y \a\t g:i A') : 'Date unknown' }}
                                        </span>
                                    </div>
                                    @if($redemption->status === 'approved' || $redemption->is_redeemed)
                                        <div class="alert alert-success border-0 mb-0 py-2 small">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <strong>Completed!</strong> This reward has been successfully approved.
                                        </div>
                                    @elseif($redemption->status === 'rejected')
                                        <div class="alert alert-danger border-0 mb-0 py-2 small">
                                            <i class="fas fa-times-circle me-2"></i>
                                            <strong>Rejected!</strong> {{ $redemption->rejection_reason ?? 'This reward redemption was rejected by the seller.' }}
                                        </div>
                                    @else
                                        <div class="alert alert-warning border-0 mb-0 py-2 small">
                                            <i class="fas fa-clock me-2"></i>
                                            <strong>Processing...</strong> Your redemption request is being processed.
                                        </div>
                                    @endif
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

        .reward-item {
            transition: all 0.3s ease;
        }

        .reward-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .reward-default-card {
            height: 200px;
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

            .card-title {
                font-size: 1rem;
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
        }

        .reward-card:nth-child(1) { animation-delay: 0.1s; }
        .reward-card:nth-child(2) { animation-delay: 0.2s; }
        .reward-card:nth-child(3) { animation-delay: 0.3s; }
        .reward-card:nth-child(4) { animation-delay: 0.4s; }
        .reward-card:nth-child(5) { animation-delay: 0.5s; }
        .reward-card:nth-child(6) { animation-delay: 0.6s; }
        .reward-card:nth-child(7) { animation-delay: 0.7s; }
        .reward-card:nth-child(8) { animation-delay: 0.8s; }
    </style>
@endsection
