@extends('master')

@section('content')
    <div class="background-animation">
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
        <div class="particles">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="seller-header">
            <div class="header-nav">
                <a href="{{ route('scan') }}" class="back-btn">
                    <span>←</span>
                </a>
                <h2>Store Details</h2>
                <div class="header-action"></div>
            </div>
        </div>

        <!-- Seller Info Card -->
        <div class="seller-card">
            <div class="seller-main-info">
                <div class="seller-logo">
                    @if($photos->where('is_featured', true)->first())
                        <img src="{{ $photos->where('is_featured', true)->first()->url }}" alt="{{ $seller->business_name }}" class="logo-img">
                    @else
                        <div class="logo-placeholder">🏪</div>
                    @endif
                </div>
                <div class="seller-details">
                    <h3 class="seller-name">{{ $seller->business_name }}</h3>
                    <div class="seller-meta">
                        <div class="verification-badge">
                            <span class="verified-icon">✅</span>
                            <span>Verified Partner</span>
                        </div>
                        @if($seller->location)
                            <div class="location-info">
                                <span class="location-icon">📍</span>
                                <span>{{ $seller->location }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($seller->description)
                <div class="seller-description">
                    <p>{{ $seller->description }}</p>
                </div>
            @endif

            @if($seller->working_hours)
                <div class="working-hours">
                    <h4>⏰ Working Hours</h4>
                    <p>{{ $seller->working_hours }}</p>
                </div>
            @endif
        </div>

        <!-- Transaction Info -->
        <div class="transaction-info">
            <div class="info-card">
                <div class="info-header">
                    <span class="info-icon">🎯</span>
                    <span class="info-title">Points Earned</span>
                </div>
                <div class="info-value">+150 pts</div>
                <div class="info-description">For this purchase</div>
            </div>

            @if($previousTransactions > 0)
                <div class="info-card">
                    <div class="info-header">
                        <span class="info-icon">📊</span>
                        <span class="info-title">History</span>
                    </div>
                    <div class="info-value">{{ $previousTransactions }}</div>
                    <div class="info-description">Previous transactions</div>
                </div>
            @endif
        </div>

        <!-- Trust Indicators -->
        <div class="trust-section">
            <div class="trust-item">
                <span class="trust-icon">🔒</span>
                <span class="trust-text">Secure Transaction</span>
            </div>
            <div class="trust-item">
                <span class="trust-icon">⚡</span>
                <span class="trust-text">Instant Points</span>
            </div>
            <div class="trust-item">
                <span class="trust-icon">🏆</span>
                <span class="trust-text">Trusted Partner</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-section">
            <button onclick="confirmPurchase()" class="btn-confirm">
                <span class="btn-icon">✨</span>
                Confirm Purchase
            </button>
            <button onclick="cancelTransaction()" class="btn-cancel">
                <span class="btn-icon">❌</span>
                Cancel
            </button>
        </div>
    </div>

    <style>
        .seller-header {
            background: linear-gradient(135deg, #2E8B57, #3CB371);
            padding: 20px;
            color: white;
        }

        .header-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            font-size: 20px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            text-decoration: none;
        }

        .header-nav h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }

        .seller-card {
            background: white;
            margin: 20px;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .seller-main-info {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .seller-logo {
            width: 80px;
            height: 80px;
            border-radius: 16px;
            overflow: hidden;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .logo-placeholder {
            font-size: 32px;
            color: #2E8B57;
        }

        .seller-name {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin: 0 0 10px 0;
        }

        .seller-meta {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .verification-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #10b981;
            font-size: 14px;
            font-weight: 600;
        }

        .location-info {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            font-size: 14px;
        }

        .seller-description {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .seller-description p {
            margin: 0;
            color: #555;
            line-height: 1.6;
        }

        .working-hours {
            border-top: 1px solid #e9ecef;
            padding-top: 15px;
        }

        .working-hours h4 {
            margin: 0 0 8px 0;
            font-size: 16px;
            color: #333;
        }

        .working-hours p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }

        .transaction-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px;
        }

        .info-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .info-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 12px;
        }

        .info-icon {
            font-size: 18px;
        }

        .info-title {
            font-size: 14px;
            font-weight: 600;
            color: #666;
        }

        .info-value {
            font-size: 24px;
            font-weight: 700;
            color: #2E8B57;
            margin-bottom: 5px;
        }

        .info-description {
            font-size: 12px;
            color: #999;
        }

        .trust-section {
            display: flex;
            justify-content: space-around;
            margin: 20px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .trust-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            text-align: center;
        }

        .trust-icon {
            font-size: 24px;
        }

        .trust-text {
            font-size: 12px;
            font-weight: 500;
            color: #666;
        }

        .action-section {
            display: flex;
            gap: 15px;
            margin: 20px;
        }

        .btn-confirm {
            flex: 2;
            background: linear-gradient(135deg, #2E8B57, #3CB371);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 16px 24px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 139, 87, 0.3);
        }

        .btn-cancel {
            flex: 1;
            background: #f8f9fa;
            color: #666;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 16px 24px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            background: #e9ecef;
        }

        .btn-icon {
            font-size: 18px;
        }

        /* Mobile responsive */
        @media (max-width: 480px) {
            .seller-main-info {
                flex-direction: column;
                text-align: center;
            }

            .transaction-info {
                grid-template-columns: 1fr;
            }

            .trust-section {
                flex-direction: column;
                gap: 15px;
            }

            .trust-item {
                flex-direction: row;
                justify-content: center;
            }

            .action-section {
                flex-direction: column;
            }
        }
    </style>

    <script>
        function confirmPurchase() {
            const confirmBtn = document.querySelector('.btn-confirm');
            confirmBtn.innerHTML = '<span class="btn-icon">⏳</span> Processing...';
            confirmBtn.disabled = true;

            // Get data from URL or session storage
            const urlParams = new URLSearchParams(window.location.search);
            const sellerId = {{ $seller->id }};
            const itemId = urlParams.get('item_id') || 1; // Default to 1 for now
            const qrData = urlParams.get('qr_data') || 'test_qr';

            fetch('/transaction/confirm', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    seller_id: sellerId,
                    item_id: itemId,
                    qr_data: qrData
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Success! You earned ${data.points_earned} points!`);
                    window.location.href = data.redirect;
                } else {
                    alert('Error: ' + data.message);
                    confirmBtn.innerHTML = '<span class="btn-icon">✨</span> Confirm Purchase';
                    confirmBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Network error occurred');
                confirmBtn.innerHTML = '<span class="btn-icon">✨</span> Confirm Purchase';
                confirmBtn.disabled = false;
            });
        }

        function cancelTransaction() {
            window.location.href = '{{ route("scan") }}';
        }
    </script>
@endsection