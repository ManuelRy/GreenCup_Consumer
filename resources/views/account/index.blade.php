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
        <!-- Header with back button -->
        <div class="account-header">
            <div class="header-nav">
                <a href="{{ route('dashboard') }}" class="back-btn">
                    <span>←</span>
                </a>
                <h2>Green Cup Account</h2>
                <div class="close-btn">×</div>
            </div>
        </div>

        <!-- Total Points Summary Section -->
        <div class="points-summary-section">
            <div class="points-circle">
                <div class="circle-content">
                    <div class="circle-label">Total Points</div>
                    <div class="total-points">{{ number_format($availablePoints ?? 0) }}</div>
                    <div class="points-subtitle">Available</div>
                </div>
            </div>
            
            <div class="points-breakdown">
                <div class="breakdown-item">
                    <span class="breakdown-label">Total Earned</span>
                    <span class="breakdown-value">{{ number_format($totalPointsEarned ?? 0) }} pts</span>
                </div>
                <div class="breakdown-item">
                    <span class="breakdown-label">Total Spent</span>
                    <span class="breakdown-value">{{ number_format($totalPointsSpent ?? 0) }} pts</span>
                </div>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="accounts-section">
            @forelse($transactions as $transaction)
                <div class="account-card" onclick="showTransactionDetail({{ json_encode($transaction) }})">
                    <div class="account-info">
                        <div class="account-details">
                            <div class="account-title">
                                {{ $transaction->item_name ?: 'Unknown Item' }}
                            </div>
                            <div class="account-subtitle">
                                {{ $transaction->store_name ?: 'Unknown Store' }}
                                @if($transaction->store_location)
                                    • {{ $transaction->store_location }}
                                @endif
                            </div>
                            <div class="account-date">
                                @if($transaction->transaction_date)
                                    {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y • h:i A') }}
                                @else
                                    {{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y • h:i A') }}
                                @endif
                            </div>
                        </div>
                        <div class="account-amount">
                            <div class="amount {{ $transaction->type ?? 'earn' }}">
                                @if(($transaction->type ?? 'earn') === 'earn')
                                    +{{ number_format($transaction->points ?? 0) }}
                                @else
                                    -{{ number_format($transaction->points ?? 0) }}
                                @endif
                            </div>
                            <div class="amount-label">PTS</div>
                        </div>
                    </div>
                    <div class="account-actions">
                        <div class="view-indicator">👁️</div>
                    </div>
                </div>
            @empty
                <div class="no-transactions-card">
                    <div class="empty-state">
                        <div class="empty-icon">📊</div>
                        <div class="empty-title">No transactions yet</div>
                        <div class="empty-subtitle">Start scanning QR codes to earn points!</div>
                        <div style="margin-top: 15px;">
                            <a href="{{ route('dashboard') }}" style="color: #2E8B57; text-decoration: none; font-weight: 600;">
                                Go to Dashboard →
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse

            @if($transactions->count() > 0)
                <div style="text-align: center; padding: 20px; color: #666;">
                    <small>Showing {{ $transactions->count() }} recent transactions</small>
                </div>
            @endif
        </div>

        <!-- Hidden/Upcoming Features -->
        <div class="hidden-section">
            <button class="hidden-toggle">
                <span>More features coming soon</span>
                <span class="toggle-icon">⌄</span>
            </button>
        </div>
    </div>

    <!-- Transaction Detail Modal -->
    <div id="transactionModal" class="transaction-modal" style="display: none;">
        <div class="modal-overlay" onclick="closeTransactionDetail()"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3>📧 Transaction Receipt</h3>
                <button onclick="closeTransactionDetail()" class="modal-close">×</button>
            </div>
            
            <div class="modal-body">
                <!-- Receipt Header -->
                <div class="receipt-header">
                    <div class="receipt-logo">🌱</div>
                    <div class="receipt-title">Green Cup</div>
                    <div class="receipt-subtitle">Digital Receipt</div>
                </div>

                <!-- Transaction Status -->
                <div class="transaction-status success">
                    <div class="status-icon">✅</div>
                    <div class="status-text">Transaction Completed</div>
                </div>

                <!-- Points Earned/Spent -->
                <div class="points-section">
                    <div class="points-display">
                        <div class="points-amount" id="modalPointsAmount">+0</div>
                        <div class="points-label">Points</div>
                    </div>
                </div>

                <!-- Transaction Details -->
                <div class="detail-section">
                    <div class="detail-header">Transaction Details</div>
                    
                    <div class="detail-row">
                        <span class="detail-label">📦 Item</span>
                        <span class="detail-value" id="modalItemName">-</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">🏪 Store</span>
                        <span class="detail-value" id="modalStoreName">-</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">📍 Location</span>
                        <span class="detail-value" id="modalStoreLocation">-</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">📅 Date & Time</span>
                        <span class="detail-value" id="modalDateTime">-</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">🆔 Transaction ID</span>
                        <span class="detail-value" id="modalTransactionId">-</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">🔢 Units Scanned</span>
                        <span class="detail-value" id="modalUnitsScanned">-</span>
                    </div>
                </div>

                <!-- QR Code Info -->
                <div class="detail-section">
                    <div class="detail-header">🔗 QR Code Information</div>
                    
                    <div class="detail-row">
                        <span class="detail-label">QR Code</span>
                        <span class="detail-value qr-code" id="modalQrCode">-</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Points per Unit</span>
                        <span class="detail-value" id="modalPointsPerUnit">-</span>
                    </div>
                </div>

                <!-- Environmental Impact (Optional) -->
                <div class="detail-section eco-section">
                    <div class="detail-header">🌍 Environmental Impact</div>
                    <div class="eco-message">
                        <div class="eco-icon">♻️</div>
                        <div class="eco-text">
                            Thank you for choosing eco-friendly options! 
                            This purchase contributes to a sustainable future.
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="modal-actions">
                    <button onclick="shareTransaction()" class="btn-share centered">
                        <span>📤</span> Share Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Prevent horizontal overflow */
        * {
            box-sizing: border-box;
        }
        
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            max-width: 100vw;
        }

        .background-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            max-width: 100vw;
            overflow: hidden;
            z-index: -1;
        }

        .floating-shapes, .particles {
            width: 100%;
            height: 100%;
            max-width: 100vw;
            overflow: hidden;
        }

        /* ... (keep all existing styles) ... */
        
        /* Updated account card styles */
        .account-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .account-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }

        .account-card:active {
            transform: translateY(0);
        }

        .view-indicator {
            font-size: 18px;
            color: #999;
            transition: all 0.3s ease;
        }

        .account-card:hover .view-indicator {
            color: #2E8B57;
            transform: scale(1.1);
        }

        /* Modal Styles */
        .transaction-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            animation: modalSlideUp 0.3s ease;
        }

        @keyframes modalSlideUp {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #2E8B57, #3CB371);
            color: white;
            padding: 20px;
            border-radius: 16px 16px 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 4px;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .modal-body {
            padding: 0;
        }

        .receipt-header {
            text-align: center;
            padding: 25px 20px 20px;
            background: #f8f9fa;
        }

        .receipt-logo {
            font-size: 40px;
            margin-bottom: 8px;
        }

        .receipt-title {
            font-size: 20px;
            font-weight: 700;
            color: #2E8B57;
            margin-bottom: 4px;
        }

        .receipt-subtitle {
            font-size: 14px;
            color: #666;
        }

        .transaction-status {
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        .transaction-status.success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
        }

        .status-icon {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .status-text {
            font-size: 16px;
            font-weight: 600;
            color: #155724;
        }

        .points-section {
            padding: 25px 20px;
            text-align: center;
            border-bottom: 1px solid #e9ecef;
        }

        .points-display {
            background: linear-gradient(135deg, #E8F5E8, #F0F8FF);
            border-radius: 12px;
            padding: 20px;
            border: 2px solid #2E8B57;
        }

        .points-amount {
            font-size: 36px;
            font-weight: 700;
            color: #2E8B57;
            margin-bottom: 4px;
        }

        .points-label {
            font-size: 14px;
            color: #666;
            font-weight: 600;
        }

        .detail-section {
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
        }

        .detail-header {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #2E8B57;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        .detail-value {
            font-size: 14px;
            color: #333;
            font-weight: 600;
            text-align: right;
            max-width: 60%;
            word-wrap: break-word;
        }

        .detail-value.qr-code {
            font-family: monospace;
            font-size: 12px;
            background: #f8f9fa;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .eco-section {
            background: linear-gradient(135deg, #E8F5E8, #F0F8FF);
            border-bottom: none;
        }

        .eco-message {
            display: flex;
            align-items: center;
            gap: 12px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #28a745;
        }

        .eco-icon {
            font-size: 24px;
        }

        .eco-text {
            font-size: 14px;
            color: #155724;
            line-height: 1.4;
        }

        .modal-actions {
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        .btn-share, .btn-report {
            padding: 12px 16px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.3s ease;
        }

        .btn-share {
            background: linear-gradient(135deg, #2E8B57, #3CB371);
            color: white;
            min-width: 160px;
        }

        .btn-share.centered {
            width: 100%;
            max-width: 200px;
        }

        .btn-share:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(46, 139, 87, 0.3);
        }

        /* Mobile responsive */
        @media (max-width: 480px) {
            .container {
                border-radius: 0;
                margin: 0;
                max-width: 100%;
                width: 100vw;
            }
            
            .account-header {
                padding: 15px 15px 10px;
            }

            .header-nav {
                padding: 5px 0;
            }

            .back-btn {
                width: 42px;
                height: 42px;
                min-width: 42px;
                min-height: 42px;
                font-size: 20px;
            }

            .header-nav h2 {
                font-size: 18px;
            }

            .close-btn {
                width: 42px;
                height: 42px;
                font-size: 24px;
            }

            .points-summary-section {
                padding: 20px 15px 30px;
            }

            .points-circle {
                width: 130px;
                height: 130px;
                margin-bottom: 20px;
            }

            .total-points {
                font-size: 26px;
            }

            .points-breakdown {
                gap: 10px;
                flex-wrap: wrap;
                justify-content: center;
            }

            .breakdown-value {
                font-size: 14px;
            }

            .breakdown-label {
                font-size: 11px;
            }

            .account-card {
                padding: 15px;
                margin-bottom: 10px;
            }

            .accounts-section {
                padding: 15px;
            }

            .hidden-section {
                padding: 15px;
            }

            .modal-content {
                margin: 10px;
                max-height: 95vh;
                width: calc(100% - 20px);
            }

            .points-amount {
                font-size: 28px;
            }

            .modal-actions {
                flex-direction: row;
                justify-content: center;
            }
            
            .btn-share.centered {
                width: 100%;
                max-width: 280px;
            }
        }

        @media (min-width: 481px) and (max-width: 767px) {
            .container {
                max-width: 100%;
                margin: 0;
                border-radius: 0;
            }
            
            .account-header {
                padding: 18px 20px 12px;
            }

            .back-btn {
                width: 44px;
                height: 44px;
                min-width: 44px;
                min-height: 44px;
                font-size: 21px;
            }

            .header-nav h2 {
                font-size: 19px;
            }

            .close-btn {
                width: 44px;
                height: 44px;
                font-size: 25px;
            }

            .points-circle {
                width: 140px;
                height: 140px;
            }

            .total-points {
                font-size: 28px;
            }
        }

        /* Existing styles remain the same... */
        .account-header {
            background: linear-gradient(135deg, #2E8B57, #3CB371);
            padding: 15px 20px 10px;
            color: white;
        }

        .header-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 5px 0;
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
            min-width: 44px;
            min-height: 44px;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            text-decoration: none;
            transform: scale(1.1);
        }

        .header-nav h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }

        .close-btn {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
        }

        .points-summary-section {
            background: linear-gradient(135deg, #2E8B57, #3CB371);
            padding: 30px 20px 40px;
            color: white;
            text-align: center;
        }

        .container {
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            min-height: 100vh;
            position: relative;
            backdrop-filter: blur(10px);
            border-radius: 0;
            overflow: hidden;
            max-width: 100%;
            width: 100%;
        }

        @media (min-width: 768px) {
            .container {
                max-width: 700px;
                margin: 20px auto;
                border-radius: 25px;
                box-shadow: 0 20px 50px rgba(0,0,0,0.3);
                min-height: calc(100vh - 40px);
            }
        }

        @media (min-width: 1024px) {
            .container {
                max-width: 700px;
                transform: none;
            }
        }

        .points-circle {
            margin: 0 auto 30px;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .circle-content {
            text-align: center;
        }

        .circle-label {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 8px;
        }

        .total-points {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .points-subtitle {
            font-size: 12px;
            opacity: 0.8;
        }

        .points-breakdown {
            display: flex;
            justify-content: space-around;
            gap: 20px;
        }

        .breakdown-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .breakdown-label {
            font-size: 12px;
            opacity: 0.8;
            margin-bottom: 4px;
        }

        .breakdown-value {
            font-size: 16px;
            font-weight: 600;
        }

        .accounts-section {
            background: #f8f9fa;
            padding: 20px;
            min-height: 400px;
        }

        .account-info {
            display: flex;
            align-items: center;
            flex: 1;
        }

        .account-details {
            flex: 1;
            min-width: 0; /* Allow flex item to shrink */
            margin-right: 15px;
        }

        .account-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .account-subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 4px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            line-height: 1.3;
        }

        .account-date {
            font-size: 12px;
            color: #999;
            word-wrap: break-word;
        }

        .account-amount {
            text-align: right;
            margin-right: 15px;
        }

        .amount {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .amount.earn {
            color: #28a745;
        }

        .amount.spend {
            color: #dc3545;
        }

        .amount-label {
            font-size: 12px;
            color: #666;
            font-weight: 500;
        }

        .account-actions {
            display: flex;
            align-items: center;
        }

        .no-transactions-card {
            background: white;
            border-radius: 12px;
            padding: 40px 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .empty-state {
            color: #666;
        }

        .empty-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .empty-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .empty-subtitle {
            font-size: 14px;
            opacity: 0.8;
        }

        .hidden-section {
            background: #e9ecef;
            padding: 20px;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }

        .hidden-toggle {
            width: 100%;
            max-width: 100%;
            background: white;
            border: none;
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 14px;
            color: #666;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            box-sizing: border-box;
        }
    </style>

<script>
        function showTransactionDetail(transaction) {
            // Populate modal with transaction data
            document.getElementById('modalPointsAmount').textContent = 
                (transaction.type === 'earn' ? '+' : '-') + (transaction.points || 0);
            document.getElementById('modalItemName').textContent = 
                transaction.item_name || 'Unknown Item';
            document.getElementById('modalStoreName').textContent = 
                transaction.store_name || 'Unknown Store';
            document.getElementById('modalStoreLocation').textContent = 
                transaction.store_location || 'Location not specified';
            document.getElementById('modalDateTime').textContent = 
                new Date(transaction.transaction_date || transaction.created_at).toLocaleString();
            document.getElementById('modalTransactionId').textContent = 
                '#' + (transaction.id || '000').toString().padStart(6, '0');
            
            // FIXED: Handle units_scanned safely
            document.getElementById('modalUnitsScanned').textContent = 
                transaction.units_scanned || '1';
            
            // FIXED: Handle QR code display - with fallback logic
            let qrCode = transaction.qr_code || transaction.code || 'N/A';
            if (qrCode === 'N/A') {
                // Generate a meaningful QR code reference using available data
                qrCode = 'QR-TXN-' + (transaction.id || 'X');
            }
            document.getElementById('modalQrCode').textContent = qrCode;
            
            // FIXED: Handle points per unit with fallback
            document.getElementById('modalPointsPerUnit').textContent = 
                (transaction.points_per_unit || transaction.points || 0) + ' pts';

            // Show modal
            document.getElementById('transactionModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeTransactionDetail() {
            document.getElementById('transactionModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function shareTransaction() {
            if (navigator.share) {
                navigator.share({
                    title: 'Green Cup Transaction Receipt',
                    text: 'Check out my eco-friendly purchase!',
                    url: window.location.href
                }).catch(console.error);
            } else {
                // Fallback - copy to clipboard
                const receiptText = `Green Cup Transaction Receipt\nTransaction ID: #${document.getElementById('modalTransactionId').textContent}\nPoints: ${document.getElementById('modalPointsAmount').textContent}\nStore: ${document.getElementById('modalStoreName').textContent}`;
                
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(receiptText).then(() => {
                        alert('Receipt details copied to clipboard!');
                    }).catch(() => {
                        alert('Unable to copy to clipboard');
                    });
                } else {
                    alert('Sharing not supported on this device');
                }
            }
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                closeTransactionDetail();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeTransactionDetail();
            }
        });

        // OPTIONAL: Add loading state for slow connections
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth scroll behavior
            document.documentElement.style.scrollBehavior = 'smooth';
            
            // Add touch feedback for mobile
            const cards = document.querySelectorAll('.account-card');
            cards.forEach(card => {
                card.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                }, { passive: true });
                
                card.addEventListener('touchend', function() {
                    this.style.transform = '';
                }, { passive: true });
            });
        });
    </script>

@endsection