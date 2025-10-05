@extends('master')

@section('content')
<div class="container-fluid min-vh-100 py-3">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">

            <!-- Main Card -->
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">

                <!-- Header -->
                <div class="card-header bg-gradient-primary text-white p-0">
                    <div class="d-flex align-items-center justify-content-between p-3">
                        <a href="{{ route('account') }}"
                           class="btn btn-light btn-sm rounded-circle p-2 d-flex align-items-center justify-content-center text-decoration-none"
                           style="width: 40px; height: 40px;"
                           aria-label="Back to Account">
                            <i class="fas fa-chevron-left"></i>
                        </a>

                        <div class="text-center flex-grow-1 mx-3">
                            <h1 class="h5 mb-0 fw-semibold">Transaction History</h1>
                            @if(isset($consumer) && $consumer)
                                <small class="opacity-75">{{ $consumer->name ?? $consumer->email ?? 'Consumer' }}</small>
                            @endif
                        </div>

                        <div style="width: 40px; height: 40px;"></div> <!-- Spacer -->
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="card-body bg-light border-bottom p-0">
                    <div class="p-3 p-md-4">
                        <form method="GET" action="{{ route('account.transactions') }}" class="row g-3">

                            <!-- Filter Row 1 -->
                            <div class="col-12 col-md-6">
                                <label for="type" class="form-label fw-medium text-dark">Type</label>
                                <select name="type" id="type" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="earn" {{ request('type') == 'earn' ? 'selected' : '' }}>Earned</option>
                                    <option value="spend" {{ request('type') == 'spend' ? 'selected' : '' }}>Spent</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="store" class="form-label fw-medium text-dark">Store</label>
                                <select name="store" id="store" class="form-select">
                                    <option value="">All Stores</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}" {{ request('store') == $store->id ? 'selected' : '' }}>
                                            {{ $store->business_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter Row 2 -->
                            <div class="col-12 col-md-6">
                                <label for="date_from" class="form-label fw-medium text-dark">From Date</label>
                                <input type="date" name="date_from" id="date_from"
                                       value="{{ request('date_from') }}"
                                       class="form-control">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="date_to" class="form-label fw-medium text-dark">To Date</label>
                                <input type="date" name="date_to" id="date_to"
                                       value="{{ request('date_to') }}"
                                       class="form-control">
                            </div>

                            <!-- Filter Actions -->
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('account.transactions') }}"
                                       class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>
                                        Clear
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter me-1"></i>
                                        Apply Filters
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Transactions Section -->
                <div class="card-body p-0">
                    <!-- Section Header -->
                    <div class="p-3 p-md-4 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-receipt text-primary me-2"></i>
                            <h3 class="h6 mb-0 fw-semibold">Transactions ({{ $transactions->total() }})</h3>
                        </div>
                    </div>

                    <!-- Transaction List -->
                    <div class="p-3 p-md-4">
                        @forelse($transactions as $transaction)
                            <div class="card border transaction-card mb-3 cursor-pointer"
                                 onclick="showTransactionDetail({{ json_encode($transaction) }})">
                                <div class="card-body p-3">
                                    <div class="row align-items-start">
                                        <!-- Transaction Details -->
                                        <div class="col">
                                            <div class="d-flex align-items-start justify-content-between">
                                                <div class="flex-grow-1 me-3">
                                                    <h6 class="card-title mb-1 fw-semibold text-truncate">
                                                        @if($transaction->transaction_type === 'reward_redemption')
                                                            <i class="fas fa-gift text-primary me-1"></i>
                                                        @endif
                                                        {{ $transaction->description ?: 'Transaction' }}
                                                    </h6>

                                                    <div class="text-muted small mb-1">
                                                        <i class="fas fa-store me-1"></i>
                                                        {{ $transaction->store_name ?: 'Unknown Store' }}
                                                        @if($transaction->store_location)
                                                            <span class="text-secondary"> • {{ $transaction->store_location }}</span>
                                                        @endif
                                                    </div>

                                                    <div class="text-muted small mb-1">
                                                        <i class="far fa-calendar me-1"></i>
                                                        @if($transaction->transaction_date)
                                                            {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y • h:i A') }}
                                                        @else
                                                            {{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y • h:i A') }}
                                                        @endif
                                                    </div>

                                                    @if($transaction->receipt_code)
                                                        <div class="small">
                                                            <span class="badge bg-light text-dark font-monospace">
                                                                <i class="fas fa-receipt me-1"></i>
                                                                {{ $transaction->receipt_code }}
                                                            </span>
                                                        </div>
                                                    @endif

                                                    @if($transaction->transaction_type === 'reward_redemption' && $transaction->reward_status)
                                                        <div class="small mt-1">
                                                            @if($transaction->reward_status === 'pending')
                                                                <span class="badge bg-warning text-dark">
                                                                    <i class="fas fa-clock me-1"></i>
                                                                    Pending Approval
                                                                </span>
                                                            @elseif($transaction->reward_status === 'approved')
                                                                <span class="badge bg-success">
                                                                    <i class="fas fa-check-circle me-1"></i>
                                                                    Approved
                                                                </span>
                                                            @elseif($transaction->reward_status === 'rejected')
                                                                <span class="badge bg-danger">
                                                                    <i class="fas fa-times-circle me-1"></i>
                                                                    Rejected
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @endif

                                                    @if(config('app.debug'))
                                                        <small class="text-muted d-block mt-1" style="font-size: 0.7em;">
                                                            Debug: description: "{{ $transaction->description ?? 'NULL' }}", type: "{{ $transaction->type ?? 'NULL' }}"
                                                        </small>
                                                    @endif
                                                </div>

                                                <!-- Points Display -->
                                                <div class="text-end flex-shrink-0 d-flex align-items-center">
                                                    <div class="me-2">
                                                        <div class="fw-bold {{ ($transaction->type ?? 'earn') === 'earn' ? 'text-success' : 'text-danger' }}">
                                                            @if(($transaction->type ?? 'earn') === 'earn')
                                                                +{{ number_format($transaction->points ?? 0) }}
                                                            @else
                                                                -{{ number_format($transaction->points ?? 0) }}
                                                            @endif
                                                        </div>
                                                        <small class="text-muted">PTS</small>
                                                    </div>
                                                    <i class="fas fa-chevron-right text-muted"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <!-- Empty State -->
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-chart-bar fa-4x text-muted opacity-50"></i>
                                </div>
                                <h5 class="fw-semibold text-dark mb-3">No transactions found</h5>
                                <p class="text-muted mb-4">
                                    @if(request()->anyFilled(['type', 'store', 'date_from', 'date_to']))
                                        Try adjusting your filters or
                                        <a href="{{ route('account.transactions') }}" class="text-decoration-none fw-semibold">
                                            clear all filters
                                        </a>
                                    @else
                                        Start scanning QR codes to earn points!
                                    @endif
                                </p>
                                @if(!request()->anyFilled(['type', 'store', 'date_from', 'date_to']))
                                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                        <i class="fas fa-qrcode me-2"></i>
                                        Go to Dashboard
                                    </a>
                                @endif
                            </div>
                        @endforelse

                        <!-- Pagination -->
                        @if($transactions->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $transactions->appends(request()->query())->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Detail Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title" id="transactionModalLabel">
                    <i class="fas fa-receipt text-primary me-2"></i>
                    Transaction Receipt
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <!-- Transaction Status -->
                <div class="text-center py-3 mb-4 bg-light rounded-3">
                    <div class="mb-2">
                        <i class="fas fa-check-circle fa-3x text-success"></i>
                    </div>
                    <h6 class="fw-semibold text-success mb-0">Transaction Completed</h6>
                </div>

                <!-- Points Display -->
                <div class="text-center py-3 mb-4 border-top border-bottom">
                    <div class="fw-bold text-primary mb-1" style="font-size: 2rem;" id="modalPointsAmount">+0</div>
                    <small class="text-muted fw-semibold">POINTS</small>
                </div>

                <!-- Transaction Details -->
                <div class="mb-4">
                    <h6 class="fw-semibold text-dark mb-3 pb-2 border-bottom">
                        <i class="fas fa-info-circle me-2"></i>
                        Transaction Details
                    </h6>

                    <div class="row g-2 small">
                        <div class="col-4 col-md-3 text-muted">Item:</div>
                        <div class="col-8 col-md-9 fw-medium" id="modalItemName">-</div>

                        <div class="col-4 col-md-3 text-muted">Store:</div>
                        <div class="col-8 col-md-9 fw-medium" id="modalStoreName">-</div>

                        <div class="col-4 col-md-3 text-muted">Location:</div>
                        <div class="col-8 col-md-9 fw-medium" id="modalStoreLocation">-</div>

                        <div class="col-4 col-md-3 text-muted">Date & Time:</div>
                        <div class="col-8 col-md-9 fw-medium" id="modalDateTime">-</div>

                        <div class="col-4 col-md-3 text-muted">Transaction ID:</div>
                        <div class="col-8 col-md-9 fw-medium font-monospace" id="modalTransactionId">-</div>

                        <div class="col-4 col-md-3 text-muted">Units Scanned:</div>
                        <div class="col-8 col-md-9 fw-medium" id="modalUnitsScanned">-</div>
                    </div>
                </div>

                <!-- Receipt Information -->
                <div class="mb-4">
                    <h6 class="fw-semibold text-dark mb-3 pb-2 border-bottom">
                        <i class="fas fa-qrcode me-2"></i>
                        Receipt Information
                    </h6>

                    <div class="row g-2 small">
                        <div class="col-4 col-md-3 text-muted">Receipt Code:</div>
                        <div class="col-8 col-md-9">
                            <span class="badge bg-light text-dark font-monospace" id="modalReceiptCode">-</span>
                        </div>

                        <div class="col-4 col-md-3 text-muted">Points per Unit:</div>
                        <div class="col-8 col-md-9 fw-medium text-success" id="modalPointsPerUnit">-</div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-primary w-100" onclick="shareTransaction()">
                    <i class="fas fa-share-alt me-2"></i>
                    Share Receipt
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom CSS for enhanced styling */
:root {
    --bs-primary: #1dd1a1;
    --bs-primary-rgb: 29, 209, 161;
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

.btn-primary:active {
    transform: translateY(0);
}

.btn-outline-secondary {
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    transform: translateY(-1px);
}

.form-control:focus,
.form-select:focus {
    border-color: #1dd1a1;
    box-shadow: 0 0 0 0.2rem rgba(29, 209, 161, 0.25);
}

.card {
    border-radius: 1rem !important;
}

.transaction-card {
    transition: all 0.3s ease;
    cursor: pointer;
    border: 2px solid var(--bs-border-color) !important;
}

.transaction-card:hover {
    border-color: #1dd1a1 !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.transaction-card:active {
    transform: translateY(0);
}

/* Loading states */
.btn:disabled {
    opacity: 0.6;
    transform: none !important;
}

/* Custom scrollbar for modal */
.modal-body {
    scrollbar-width: thin;
    scrollbar-color: #1dd1a1 #f1f3f4;
}

.modal-body::-webkit-scrollbar {
    width: 6px;
}

.modal-body::-webkit-scrollbar-track {
    background: #f1f3f4;
}

.modal-body::-webkit-scrollbar-thumb {
    background: #1dd1a1;
    border-radius: 3px;
}

/* Mobile optimizations */
@media (max-width: 576px) {
    .container-fluid {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }

    .transaction-card .card-body {
        padding: 1rem !important;
    }

    .modal-dialog {
        margin: 0.75rem;
    }
}

/* Animation for smooth interactions */
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

.card {
    animation: slideUp 0.4s ease-out;
}

/* Better focus indicators for accessibility */
.btn:focus,
.form-control:focus,
.form-select:focus {
    outline: 2px solid #1dd1a1;
    outline-offset: 2px;
}

/* Enhanced hover states */
@media (hover: hover) {
    .form-control:hover,
    .form-select:hover {
        border-color: #1dd1a1;
    }
}

.cursor-pointer {
    cursor: pointer;
}

/* Success/Error text colors */
.text-success {
    color: #27ae60 !important;
}

.text-danger {
    color: #e74c3c !important;
}

/* Font monospace for codes */
.font-monospace {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace !important;
}
</style>

<!-- Font Awesome (if not already included) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<script>
function showTransactionDetail(transaction) {
    // Populate modal with transaction data
    document.getElementById('modalPointsAmount').textContent =
        (transaction.type === 'spend' ? '-' : '+') + (transaction.points || 0);

    document.getElementById('modalItemName').textContent =
        transaction.description || 'Transaction';

    document.getElementById('modalStoreName').textContent =
        transaction.store_name || 'Unknown Store';

    document.getElementById('modalStoreLocation').textContent =
        transaction.store_location || 'N/A';

    // Format date
    const date = transaction.transaction_date || transaction.created_at;
    if (date) {
        const formattedDate = new Date(date).toLocaleString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        document.getElementById('modalDateTime').textContent = formattedDate;
    }

    document.getElementById('modalTransactionId').textContent =
        transaction.id || 'N/A';

    document.getElementById('modalUnitsScanned').textContent =
        transaction.units_scanned || '1';

    document.getElementById('modalReceiptCode').textContent =
        transaction.receipt_code || 'N/A';

    document.getElementById('modalPointsPerUnit').textContent =
        transaction.points_per_unit || (transaction.points || 0);

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('transactionModal'));
    modal.show();
}

function shareTransaction() {
    if (navigator.share) {
        const pointsAmount = document.getElementById('modalPointsAmount').textContent;
        const storeName = document.getElementById('modalStoreName').textContent;
        const receiptCode = document.getElementById('modalReceiptCode').textContent;

        navigator.share({
            title: 'Transaction Receipt',
            text: `I earned ${pointsAmount} points at ${storeName}! Receipt: ${receiptCode}`,
            url: window.location.href
        });
    } else {
        // Fallback for browsers that don't support Web Share API
        alert('Sharing not supported on this device');
    }
}

// Enhanced mobile experience
document.addEventListener('DOMContentLoaded', function() {
    // Auto-resize inputs on mobile
    if (window.innerWidth <= 768) {
        const inputs = document.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                setTimeout(() => {
                    this.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            });
        });
    }

    // Initialize tooltips if needed
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
