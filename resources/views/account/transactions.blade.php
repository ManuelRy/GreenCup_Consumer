@extends('master')

@section('content')
    <div class="container">
        <!-- Header with back button -->
        <div class="account-header">
            <div class="header-nav">
                <a href="{{ route('account') }}" class="back-btn">
                    <span>←</span>
                </a>
                <h2>Transaction History</h2>
                <div class="placeholder-btn"></div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="{{ route('account.transactions') }}" class="filter-form">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="type">Type</label>
                        <select name="type" id="type">
                            <option value="">All Types</option>
                            <option value="earn" {{ request('type') == 'earn' ? 'selected' : '' }}>Earned</option>
                            <option value="spend" {{ request('type') == 'spend' ? 'selected' : '' }}>Spent</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="store">Store</label>
                        <select name="store" id="store">
                            <option value="">All Stores</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" {{ request('store') == $store->id ? 'selected' : '' }}>
                                    {{ $store->business_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="filter-row">
                    <div class="filter-group">
                        <label for="date_from">From Date</label>
                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}">
                    </div>

                    <div class="filter-group">
                        <label for="date_to">To Date</label>
                        <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}">
                    </div>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn-filter">Apply Filters</button>
                    <a href="{{ route('account.transactions') }}" class="btn-clear">Clear</a>
                </div>
            </form>
        </div>

        <!-- Transaction List -->
        <div class="transactions-section">
            <div class="transactions-header">
                <h3>Transactions ({{ $transactions->total() }})</h3>
            </div>

            @forelse($transactions as $transaction)
                <div class="transaction-card" onclick="showTransactionDetail({{ json_encode($transaction) }})">
                    <div class="transaction-info">
                        <div class="transaction-details">
                            <div class="transaction-title">
                                {{ $transaction->item_name ?: 'Unknown Item' }}
                            </div>
                            <div class="transaction-subtitle">
                                {{ $transaction->store_name ?: 'Unknown Store' }}
                                @if($transaction->store_location)
                                    • {{ $transaction->store_location }}
                                @endif
                            </div>
                            <div class="transaction-date">
                                @if($transaction->transaction_date)
                                    {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y • h:i A') }}
                                @else
                                    {{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y • h:i A') }}
                                @endif
                            </div>
                            @if($transaction->receipt_code)
                                <div class="transaction-receipt">
                                    Receipt: {{ $transaction->receipt_code }}
                                </div>
                            @endif
                        </div>
                        <div class="transaction-amount">
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
                    <div class="transaction-actions">
                        <div class="view-indicator">›</div>
                    </div>
                </div>
            @empty
                <div class="no-transactions-card">
                    <div class="empty-state">
                        <div class="empty-icon">📊</div>
                        <div class="empty-title">No transactions found</div>
                        <div class="empty-subtitle">
                            @if(request()->anyFilled(['type', 'store', 'date_from', 'date_to']))
                                Try adjusting your filters or
                                <a href="{{ route('account.transactions') }}" class="clear-filters-link">clear all filters</a>
                            @else
                                Start scanning QR codes to earn points!
                            @endif
                        </div>
                        @if(!request()->anyFilled(['type', 'store', 'date_from', 'date_to']))
                            <div style="margin-top: 20px;">
                                <a href="{{ route('dashboard') }}" class="empty-link">
                                    Go to Dashboard →
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforelse

            <!-- Pagination -->
            @if($transactions->hasPages())
                <div class="pagination-wrapper">
                    {{ $transactions->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Transaction Detail Modal (reuse from account page) -->
    <div id="transactionModal" class="transaction-modal" style="display: none;">
        <div class="modal-overlay" onclick="closeTransactionDetail()"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3>Transaction Receipt</h3>
                <button onclick="closeTransactionDetail()" class="modal-close">×</button>
            </div>

            <div class="modal-body">
                <!-- Transaction Status -->
                <div class="transaction-status">
                    <div class="status-icon">✅</div>
                    <div class="status-text">Transaction Completed</div>
                </div>

                <!-- Points Display -->
                <div class="points-display">
                    <div class="points-amount" id="modalPointsAmount">+0</div>
                    <div class="points-label">Points</div>
                </div>

                <!-- Transaction Details -->
                <div class="detail-section">
                    <h4 class="detail-header">Transaction Details</h4>

                    <div class="detail-row">
                        <span class="detail-label">Item</span>
                        <span class="detail-value" id="modalItemName">-</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Store</span>
                        <span class="detail-value" id="modalStoreName">-</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Location</span>
                        <span class="detail-value" id="modalStoreLocation">-</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Date & Time</span>
                        <span class="detail-value" id="modalDateTime">-</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Transaction ID</span>
                        <span class="detail-value" id="modalTransactionId">-</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Units Scanned</span>
                        <span class="detail-value" id="modalUnitsScanned">-</span>
                    </div>
                </div>

                <!-- QR Code Info -->
                <div class="detail-section">
                    <h4 class="detail-header">Receipt Information</h4>

                    <div class="detail-row">
                        <span class="detail-label">Receipt Code</span>
                        <span class="detail-value qr-code" id="modalReceiptCode">-</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Points per Unit</span>
                        <span class="detail-value" id="modalPointsPerUnit">-</span>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="modal-actions">
                    <button onclick="shareTransaction()" class="btn-share">
                        Share Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            background: #f8f8f8;
        }

        /* Container */
        .container {
            background: #ffffff;
            min-height: 100vh;
            position: relative;
            max-width: 100%;
            width: 100%;
        }

        @media (min-width: 768px) {
            .container {
                border-radius: 16px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            }
        }

        /* Header */
        .account-header {
            background: #1a1a1a;
            padding: 16px 20px;
            color: white;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            font-size: 20px;
            font-weight: bold;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
            color: white;
            text-decoration: none;
        }

        .header-nav h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            flex: 1;
            text-align: center;
        }

        .placeholder-btn {
            width: 40px;
            height: 40px;
        }

        /* Filter Section */
        .filter-section {
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        .filter-form {
            max-width: 600px;
            margin: 0 auto;
        }

        .filter-row {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
        }

        .filter-group {
            flex: 1;
        }

        .filter-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 6px;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-size: 14px;
            background: white;
            transition: all 0.2s ease;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: #1a1a1a;
        }

        .filter-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .btn-filter, .btn-clear {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-filter {
            background: #1a1a1a;
            color: white;
        }

        .btn-filter:hover {
            background: #333;
        }

        .btn-clear {
            background: #6c757d;
            color: white;
        }

        .btn-clear:hover {
            background: #5a6268;
            text-decoration: none;
            color: white;
        }

        /* Transactions Section */
        .transactions-section {
            padding: 20px;
        }

        .transactions-header {
            margin-bottom: 16px;
        }

        .transactions-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .transaction-card {
            background: #ffffff;
            border: 2px solid #f0f0f0;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .transaction-card:hover {
            border-color: #1a1a1a;
            transform: translateY(-2px);
        }

        .transaction-card:active {
            transform: scale(0.98);
        }

        .transaction-info {
            display: flex;
            align-items: center;
            flex: 1;
            min-width: 0;
        }

        .transaction-details {
            flex: 1;
            min-width: 0;
            margin-right: 16px;
        }

        .transaction-title {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 4px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .transaction-subtitle {
            font-size: 13px;
            color: #666;
            margin-bottom: 4px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .transaction-date {
            font-size: 12px;
            color: #999;
        }

        .transaction-receipt {
            font-size: 11px;
            color: #666;
            font-family: monospace;
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 4px;
        }

        .transaction-amount {
            text-align: right;
            flex-shrink: 0;
        }

        .amount {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .amount.earn {
            color: #22c55e;
        }

        .amount.spend {
            color: #ef4444;
        }

        .amount-label {
            font-size: 10px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .transaction-actions {
            margin-left: 12px;
        }

        .view-indicator {
            font-size: 20px;
            color: #ccc;
        }

        /* Empty State */
        .no-transactions-card {
            background: #f8f9fa;
            border: 2px dashed #ddd;
            border-radius: 12px;
            padding: 40px 20px;
            text-align: center;
        }

        .empty-state {
            max-width: 300px;
            margin: 0 auto;
        }

        .empty-icon {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.6;
        }

        .empty-title {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .empty-subtitle {
            font-size: 14px;
            color: #666;
            line-height: 1.5;
        }

        .empty-link, .clear-filters-link {
            color: #1a1a1a;
            text-decoration: none;
            font-weight: 600;
        }

        .empty-link:hover, .clear-filters-link:hover {
            text-decoration: underline;
        }

        /* Pagination */
        .pagination-wrapper {
            margin-top: 24px;
            display: flex;
            justify-content: center;
        }

        /* Transaction Modal (same as account page) */
        .transaction-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 20px;
        }

        .modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            max-width: 500px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            animation: modalSlideUp 0.3s ease;
        }

        @keyframes modalSlideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            background: #1a1a1a;
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
            font-size: 28px;
            cursor: pointer;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .modal-body {
            padding: 20px;
        }

        .transaction-status {
            text-align: center;
            margin-bottom: 24px;
        }

        .status-icon {
            font-size: 48px;
            margin-bottom: 8px;
        }

        .status-text {
            font-size: 16px;
            font-weight: 600;
            color: #22c55e;
        }

        .points-display {
            text-align: center;
            margin-bottom: 32px;
            padding: 24px;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .points-amount {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .points-label {
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .detail-section {
            margin-bottom: 24px;
        }

        .detail-header {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0 0 16px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
        }

        .detail-value {
            font-size: 14px;
            color: #1a1a1a;
            font-weight: 500;
            text-align: right;
            max-width: 60%;
            word-wrap: break-word;
        }

        .detail-value.qr-code {
            font-family: monospace;
            font-size: 12px;
            background: #f8f8f8;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .modal-actions {
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        .btn-share {
            background: #1a1a1a;
            color: white;
            border: none;
            padding: 12px 32px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            min-width: 160px;
        }

        .btn-share:hover {
            background: #333;
        }

        /* Mobile Responsive */
        @media (max-width: 480px) {
            .filter-row {
                flex-direction: column;
                gap: 12px;
            }

            .filter-actions {
                flex-direction: column;
            }

            .btn-filter, .btn-clear {
                width: 100%;
            }

            .transaction-card {
                padding: 14px;
            }

            .transaction-title {
                font-size: 14px;
            }

            .amount {
                font-size: 16px;
            }

            .modal-content {
                margin: 10px;
                max-height: 95vh;
            }
        }
    </style>

    <script>
        function showTransactionDetail(transaction) {
            // Populate modal with transaction data
            const pointsAmount = document.getElementById('modalPointsAmount');
            const points = (transaction.type === 'earn' ? '+' : '-') + (transaction.points || 0);
            pointsAmount.textContent = points;
            pointsAmount.style.color = transaction.type === 'earn' ? '#22c55e' : '#ef4444';

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
            document.getElementById('modalUnitsScanned').textContent =
                transaction.units_scanned || '1';

            // Use receipt code instead of QR code
            let receiptCode = transaction.receipt_code || 'N/A';
            if (receiptCode === 'N/A') {
                receiptCode = 'RCP-' + (transaction.id || 'X');
            }
            document.getElementById('modalReceiptCode').textContent = receiptCode;

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
                    title: 'Green Cup Transaction',
                    text: 'Check out my eco-friendly purchase!',
                    url: window.location.href
                }).catch(console.error);
            } else {
                // Fallback
                const receiptText = `Green Cup Transaction\nID: ${document.getElementById('modalTransactionId').textContent}\nPoints: ${document.getElementById('modalPointsAmount').textContent}`;

                if (navigator.clipboard) {
                    navigator.clipboard.writeText(receiptText).then(() => {
                        alert('Receipt copied to clipboard!');
                    });
                } else {
                    alert('Sharing not supported on this device');
                }
            }
        }

        // Touch feedback
        document.addEventListener('DOMContentLoaded', function() {
            const touchElements = document.querySelectorAll('.transaction-card, .back-btn, .btn-share, .empty-link, .btn-filter, .btn-clear');

            touchElements.forEach(element => {
                element.addEventListener('touchstart', function() {
                    this.style.opacity = '0.7';
                }, { passive: true });

                element.addEventListener('touchend', function() {
                    this.style.opacity = '';
                }, { passive: true });
            });
        });
    </script>
@endsection
