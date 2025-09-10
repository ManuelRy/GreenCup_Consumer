@extends('master')

@section('content')
<div class="transactions-page-container page-content">
    <div class="transactions-container">
        <!-- Header with back button -->
        <div class="transactions-header">
            <div class="header-nav">
                <a href="{{ route('account') }}" class="back-btn" aria-label="Back to Account">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m15 18-6-6 6-6"/>
                    </svg>
                </a>
                <div class="header-title">
                    <h1>Transaction History</h1>
                    @if(isset($consumer) && $consumer)
                        <p class="user-info">{{ $consumer->name ?? $consumer->email ?? 'Consumer' }}</p>
                    @endif
                </div>
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
                                <!-- Debug: Check transaction data -->
                                @if(config('app.debug'))
                                    <small style="display: block; color: #999; font-size: 10px;">
                                        Debug: item_name: "{{ $transaction->item_name ?? 'NULL' }}", description: "{{ $transaction->description ?? 'NULL' }}"
                                    </small>
                                @endif
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
                    {{ $transactions->appends(request()->query())->links() }}
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
</div>

<style>
/* CSS Variables for Responsive Design */
:root {
    --primary-color: #1dd1a1;
    --primary-dark: #10ac84;
    --secondary-color: #2e8b57;
    --background-color: #f8f9fa;
    --card-bg: #ffffff;
    --text-primary: #2c3e50;
    --text-secondary: #7f8c8d;
    --text-muted: #95a5a6;
    --border-color: #e8eaed;
    --hover-bg: #f8f9fa;
    --shadow-light: 0 2px 4px rgba(0,0,0,0.08);
    --shadow-medium: 0 4px 12px rgba(0,0,0,0.12);
    --border-radius: 12px;
    --border-radius-lg: 16px;

    /* Responsive spacing */
    --spacing-xs: clamp(4px, 1vw, 8px);
    --spacing-sm: clamp(8px, 2vw, 12px);
    --spacing-md: clamp(12px, 3vw, 16px);
    --spacing-lg: clamp(16px, 4vw, 24px);
    --spacing-xl: clamp(24px, 5vw, 32px);

    /* Responsive font sizes */
    --font-xs: clamp(10px, 2vw, 12px);
    --font-sm: clamp(12px, 2.5vw, 14px);
    --font-base: clamp(14px, 3vw, 16px);
    --font-lg: clamp(16px, 3.5vw, 18px);
    --font-xl: clamp(18px, 4vw, 22px);
    --font-xxl: clamp(20px, 5vw, 24px);
}

/* Reset and Base Styles */
* {
    box-sizing: border-box;
}

/* Main Container */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

/* Universal box-sizing */
* {
    box-sizing: border-box;
}

/* Prevent horizontal overflow */
body, html {
    overflow-x: hidden;
    width: 100%;
}

/* Base Styles */
.transactions-page-container {
    padding: var(--spacing-lg);
    min-height: 100vh;
    background: var(--bg-light);
}

.transactions-container {
    max-width: 800px;
    margin: 0 auto;
    background: white;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}

/* Header */
.transactions-header {
    background: white;
    border-bottom: 1px solid var(--border-color);
    padding: var(--spacing-lg);
    position: sticky;
    top: 0;
    z-index: 10;
}

.header-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--spacing-md);
    max-width: 100%;
}

.back-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: var(--hover-bg);
    color: var(--text-primary);
    text-decoration: none;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.back-btn:hover {
    background: var(--border-color);
    transform: translateY(-1px);
    color: var(--text-primary);
    text-decoration: none;
}

.header-title {
    flex: 1;
    text-align: center;
    min-width: 0;
}

.header-title h1 {
    margin: 0;
    font-size: var(--font-xl);
    font-weight: 600;
    color: var(--text-primary);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.user-info {
    margin: 0;
    font-size: var(--font-sm);
    color: var(--text-muted);
    margin-top: var(--spacing-xs);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.placeholder-btn {
    width: 44px;
    height: 44px;
    flex-shrink: 0;
}

/* Filter Section */
.filter-section {
    background: var(--hover-bg);
    border-bottom: 1px solid var(--border-color);
    padding: var(--spacing-xl);
}

.filter-form {
    width: 100%;
}

.filter-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-lg);
}

.filter-group {
    display: flex;
    flex-direction: column;
    width: 100%;
}

.filter-group label {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: var(--spacing-sm);
    font-size: var(--font-base);
}

.filter-group select,
.filter-group input {
    padding: var(--spacing-md);
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    font-size: var(--font-base);
    background: white;
    transition: all 0.2s ease;
    min-height: 44px;
    width: 100%;
    box-sizing: border-box;
}

.filter-group select:focus,
.filter-group input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(29, 209, 161, 0.1);
}

.filter-actions {
    display: flex;
    gap: var(--spacing-md);
    justify-content: flex-end;
    align-items: center;
    flex-wrap: wrap;
}

.btn-filter,
.btn-clear {
    padding: var(--spacing-md) var(--spacing-lg);
    border-radius: var(--border-radius);
    font-size: var(--font-base);
    font-weight: 600;
    text-decoration: none;
    text-align: center;
    transition: all 0.2s ease;
    cursor: pointer;
    border: none;
    min-height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
    min-width: 100px;
}

.btn-filter {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
}

.btn-filter:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(29, 209, 161, 0.3);
    color: white;
    text-decoration: none;
}

.btn-clear {
    background: var(--hover-bg);
    color: var(--text-secondary);
    border: 2px solid var(--border-color);
}

.btn-clear:hover {
    background: var(--border-color);
    color: var(--text-primary);
    text-decoration: none;
}

/* Transactions Section */
.transactions-section {
    padding: var(--spacing-xl);
}

.transactions-section .transactions-header {
    background: none;
    padding: 0 0 var(--spacing-lg) 0;
    color: var(--text-primary);
    position: static;
    border-bottom: 2px solid var(--border-color);
    margin-bottom: var(--spacing-lg);
}

.transactions-section h3 {
    margin: 0;
    font-size: var(--font-lg);
    font-weight: 600;
}

/* Transaction Cards */
.transaction-card {
    background: var(--card-bg);
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: var(--spacing-lg);
    margin-bottom: var(--spacing-md);
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
}

.transaction-card:hover {
    border-color: var(--primary-color);
    box-shadow: var(--shadow-medium);
    transform: translateY(-2px);
}

.transaction-card:active {
    transform: translateY(0);
}

.transaction-info {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: var(--spacing-md);
}

.transaction-details {
    flex: 1;
    min-width: 0;
}

.transaction-title {
    font-size: var(--font-base);
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: var(--spacing-xs);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.transaction-subtitle {
    font-size: var(--font-sm);
    color: var(--text-secondary);
    margin-bottom: var(--spacing-xs);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.transaction-date {
    font-size: var(--font-xs);
    color: var(--text-muted);
    margin-bottom: var(--spacing-xs);
}

.transaction-receipt {
    font-size: var(--font-xs);
    color: var(--text-muted);
    font-family: monospace;
    background: var(--hover-bg);
    padding: 4px 8px;
    border-radius: 4px;
    display: inline-block;
}

.transaction-amount {
    text-align: right;
    flex-shrink: 0;
}

.amount {
    font-size: var(--font-lg);
    font-weight: 700;
    margin-bottom: 2px;
}

.amount.earn {
    color: #27ae60;
}

.amount.spend {
    color: #e74c3c;
}

.amount-label {
    font-size: var(--font-xs);
    color: var(--text-muted);
    font-weight: 600;
}

.transaction-actions {
    position: absolute;
    right: var(--spacing-md);
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 20px;
    font-weight: 300;
}

/* Empty State */
.no-transactions-card {
    background: var(--card-bg);
    border: 2px dashed var(--border-color);
    border-radius: var(--border-radius-lg);
    padding: var(--spacing-xl);
    text-align: center;
}

.empty-state {
    max-width: 300px;
    margin: 0 auto;
}

.empty-icon {
    font-size: 48px;
    margin-bottom: var(--spacing-lg);
}

.empty-title {
    font-size: var(--font-lg);
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: var(--spacing-sm);
}

.empty-subtitle {
    font-size: var(--font-sm);
    color: var(--text-secondary);
    line-height: 1.5;
}

.clear-filters-link,
.empty-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
}

.clear-filters-link:hover,
.empty-link:hover {
    color: var(--primary-dark);
    text-decoration: underline;
}

/* Modal Styles */
.transaction-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--spacing-lg);
}

.modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
}

.modal-content {
    background: var(--card-bg);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-medium);
    max-width: 500px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    z-index: 1;
}

.modal-header {
    padding: var(--spacing-lg);
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-header h3 {
    margin: 0;
    font-size: var(--font-lg);
    font-weight: 600;
    color: var(--text-primary);
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: var(--text-muted);
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.modal-close:hover {
    background: var(--hover-bg);
    color: var(--text-primary);
}

.modal-body {
    padding: var(--spacing-lg);
}

.transaction-status {
    text-align: center;
    padding: var(--spacing-lg) 0;
    border-bottom: 1px solid var(--border-color);
    margin-bottom: var(--spacing-lg);
}

.status-icon {
    font-size: 48px;
    margin-bottom: var(--spacing-sm);
}

.status-text {
    font-size: var(--font-lg);
    font-weight: 600;
    color: var(--text-primary);
}

.points-display {
    text-align: center;
    padding: var(--spacing-lg) 0;
    border-bottom: 1px solid var(--border-color);
    margin-bottom: var(--spacing-lg);
}

.points-amount {
    font-size: var(--font-xxl);
    font-weight: 700;
    color: var(--primary-color);
}

.points-label {
    font-size: var(--font-sm);
    color: var(--text-muted);
    margin-top: var(--spacing-xs);
}

.detail-section {
    margin-bottom: var(--spacing-lg);
}

.detail-header {
    font-size: var(--font-base);
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: var(--spacing-md);
    padding-bottom: var(--spacing-sm);
    border-bottom: 1px solid var(--border-color);
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--spacing-sm) 0;
    border-bottom: 1px solid var(--hover-bg);
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    font-size: var(--font-sm);
    color: var(--text-secondary);
    flex-shrink: 0;
}

.detail-value {
    font-size: var(--font-sm);
    color: var(--text-primary);
    font-weight: 500;
    text-align: right;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    margin-left: var(--spacing-md);
}

.detail-value.qr-code {
    font-family: monospace;
    background: var(--hover-bg);
    padding: 4px 8px;
    border-radius: 4px;
}

.modal-actions {
    padding-top: var(--spacing-lg);
    border-top: 1px solid var(--border-color);
    text-align: center;
}

.btn-share {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    border: none;
    padding: var(--spacing-md) var(--spacing-xl);
    border-radius: var(--border-radius);
    font-size: var(--font-base);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    min-height: 48px;
}

.btn-share:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(29, 209, 161, 0.3);
}

/* Pagination */
.pagination-wrapper {
    margin-top: var(--spacing-xl);
    display: flex;
    justify-content: center;
}

/* Responsive Design */
@media (max-width: 991.98px) {
    .transactions-page-container {
        padding: var(--spacing-md);
    }

    .filter-row {
        grid-template-columns: repeat(2, 1fr);
        gap: var(--spacing-md);
    }

    .filter-actions {
        margin-top: var(--spacing-md);
    }
}

@media (max-width: 767.98px) {
    .transactions-page-container {
        padding: var(--spacing-sm);
    }

    .transactions-container {
        border-radius: var(--border-radius);
        margin: 0;
    }

    .transactions-header {
        padding: var(--spacing-md);
    }

    .header-nav {
        padding: 0 var(--spacing-xs);
    }

    .header-title h1 {
        font-size: var(--font-lg);
    }

    .filter-section,
    .transactions-section {
        padding: var(--spacing-md);
    }

    .filter-row {
        grid-template-columns: 1fr;
        gap: var(--spacing-sm);
    }

    .filter-group {
        width: 100%;
    }

    .filter-actions {
        flex-direction: column;
        gap: var(--spacing-sm);
        margin-top: var(--spacing-md);
    }

    .btn-filter,
    .btn-clear {
        width: 100%;
        min-height: 48px;
        font-size: var(--font-base);
        padding: var(--spacing-md);
    }

    .filter-group select,
    .filter-group input {
        min-height: 48px;
        padding: var(--spacing-md);
        font-size: var(--font-base);
        width: 100%;
        box-sizing: border-box;
    }

    .transaction-card {
        padding: var(--spacing-md);
        margin-bottom: var(--spacing-sm);
    }

    .transaction-info {
        flex-direction: column;
        align-items: stretch;
        gap: var(--spacing-sm);
    }

    .transaction-details {
        flex: 1;
    }

    .transaction-amount {
        text-align: left;
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
        justify-content: space-between;
    }

    .transaction-actions {
        position: static;
        transform: none;
        align-self: center;
        margin-top: 0;
    }

    .modal-content {
        margin: var(--spacing-sm);
        width: calc(100% - calc(var(--spacing-sm) * 2));
        max-height: calc(100vh - calc(var(--spacing-sm) * 2));
        overflow-y: auto;
    }

    .modal-header {
        padding: var(--spacing-md);
    }

    .modal-body {
        padding: var(--spacing-md);
    }

    .detail-row {
        flex-direction: column;
        align-items: flex-start;
        gap: var(--spacing-xs);
        padding: var(--spacing-sm) 0;
    }

    .detail-value {
        text-align: left;
        margin-left: 0;
        width: 100%;
        word-wrap: break-word;
    }
}

@media (max-width: 575.98px) {
    .transactions-page-container {
        padding: var(--spacing-xs);
    }

    .header-title h1 {
        font-size: var(--font-base);
    }

    .user-info {
        font-size: var(--font-xs);
    }

    .filter-section,
    .transactions-section {
        padding: var(--spacing-sm);
    }

    .filter-group label {
        font-size: var(--font-sm);
        margin-bottom: var(--spacing-xs);
    }

    .filter-group select,
    .filter-group input {
        font-size: var(--font-sm);
        min-height: 44px;
    }

    .transaction-card {
        padding: var(--spacing-sm);
    }

    .transaction-title {
        font-size: var(--font-sm);
        line-height: 1.4;
    }

    .transaction-subtitle,
    .transaction-date {
        font-size: var(--font-xs);
    }

    .amount {
        font-size: var(--font-lg);
    }

    .amount-label {
        font-size: var(--font-xs);
    }

    .empty-icon {
        font-size: 36px;
    }

    .modal-content {
        margin: var(--spacing-xs);
        width: calc(100% - calc(var(--spacing-xs) * 2));
        border-radius: var(--spacing-sm);
    }

    .modal-header h3 {
        font-size: var(--font-base);
    }

    .points-amount {
        font-size: var(--font-xl);
    }
}

@media (max-width: 390px) {
    .header-nav {
        padding: 0;
    }

    .back-btn,
    .placeholder-btn {
        width: 36px;
        height: 36px;
    }

    .header-title h1 {
        font-size: var(--font-sm);
    }

    .filter-section,
    .transactions-section {
        padding: var(--spacing-xs);
    }

    .transaction-card {
        border-radius: var(--spacing-xs);
    }

    .btn-filter,
    .btn-clear {
        min-height: 40px;
        font-size: var(--font-sm);
        padding: var(--spacing-sm);
    }
}

/* Animation for smooth interactions */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.transactions-container {
    animation: fadeIn 0.3s ease;
}

.transaction-modal {
    animation: fadeIn 0.2s ease;
}

</style>
@endsection
