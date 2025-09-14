@extends('master')

@section('content')
<div class="container-fluid min-vh-100 py-3">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">

            <!-- Main Card -->
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">

                <!-- Profile Section -->
                <div class="card-body bg-gradient-light p-0">
                    <div class="p-4">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="avatar-circle bg-gradient-primary text-white d-flex align-items-center justify-content-center rounded-circle shadow-sm"
                                     style="width: 70px; height: 70px;">
                                    <span class="fs-3 fw-bold">{{ substr($consumer->name ?? 'U', 0, 1) }}</span>
                                </div>
                            </div>
                            <div class="col">
                                <h3 class="h4 fw-bold text-dark mb-1">{{ $consumer->name ?? 'Consumer' }}</h3>
                                <p class="text-muted mb-2">{{ $consumer->email ?? '' }}</p>

                                @if($consumer->phone_number)
                                    <div class="text-muted small mb-2">
                                        <i class="fas fa-phone me-2"></i>
                                        <span>{{ $consumer->phone_number }}</span>
                                    </div>
                                @endif

                                @if($consumer->gender)
                                    <div class="d-flex align-items-center text-muted small mb-2">
                                        <i class="fas fa-user me-2"></i>
                                        <span>{{ ucfirst($consumer->gender) }}</span>
                                        @if($consumer->date_of_birth)
                                            <span class="ms-2">• {{ \Carbon\Carbon::parse($consumer->date_of_birth)->age }} years old</span>
                                        @endif
                                    </div>
                                @endif

                                <div class="d-flex align-items-center text-muted small">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    <span>Member since {{ $consumer->created_at ? $consumer->created_at->format('M Y') : 'Unknown' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show m-3 mb-0" role="alert" id="flashMessage">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-2"></i>
                            <div class="flex-grow-1">{{ session('success') }}</div>
                            <button type="button" class="btn-close" onclick="closeFlashMessage()" aria-label="Close"></button>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show m-3 mb-0" role="alert" id="flashMessage">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <div class="flex-grow-1">{{ session('error') }}</div>
                            <button type="button" class="btn-close" onclick="closeFlashMessage()" aria-label="Close"></button>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show m-3 mb-0" role="alert" id="flashMessage">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <div class="flex-grow-1">
                                @foreach($errors->all() as $error)
                                    {{ $error }}
                                @endforeach
                            </div>
                            <button type="button" class="btn-close" onclick="closeFlashMessage()" aria-label="Close"></button>
                        </div>
                    </div>
                @endif

                <!-- Points Summary Section -->
                <div class="card-body bg-light border-top border-bottom p-0">
                    <div class="p-4 text-center">
                        <div class="row justify-content-center">
                            <div class="col-auto">
                                <div class="points-circle bg-white border border-3 border-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm mx-auto mb-4"
                                     style="width: 140px; height: 140px;">
                                    <div class="text-center">
                                        <div class="text-uppercase small text-muted fw-semibold mb-1" style="letter-spacing: 1px; font-size: 0.7em;">
                                            Total Points
                                        </div>
                                        <div class="fw-bold text-primary mb-1" style="font-size: 2rem;">
                                            {{ number_format($availablePoints ?? 0) }}
                                        </div>
                                        <small class="text-muted">Available</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-auto">
                                <div class="d-flex justify-content-center gap-4">
                                    <div class="text-center">
                                        <small class="text-muted d-block mb-1">Total Earned</small>
                                        <span class="fw-semibold text-dark">{{ number_format($totalPointsEarned ?? 0) }} pts</span>
                                    </div>
                                    <div class="text-center">
                                        <small class="text-muted d-block mb-1">Total Spent</small>
                                        <span class="fw-semibold text-dark">{{ number_format($totalPointsSpent ?? 0) }} pts</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Settings -->
                <div class="card-body p-0">
                    <div class="p-4">
                        <h5 class="fw-semibold text-dark mb-3">
                            <i class="fas fa-cog text-primary me-2"></i>
                            Account Settings
                        </h5>

                        <div class="list-group list-group-flush">
                            <a href="{{ route('account.edit') }}"
                               class="list-group-item list-group-item-action border-0 rounded-3 mb-2 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="setting-icon bg-gradient-primary text-white rounded-3 d-flex align-items-center justify-content-center me-3"
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="flex-grow-1 me-3">
                                        <div class="fw-semibold text-dark mb-1">Edit Profile</div>
                                        <small class="text-muted">Update your name and personal information</small>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </div>
                            </a>

                            <div class="list-group-item list-group-item-action border-0 rounded-3 mb-2 py-3 cursor-pointer"
                                 onclick="showPasswordChangeModal()">
                                <div class="d-flex align-items-center">
                                    <div class="setting-icon bg-gradient-primary text-white rounded-3 d-flex align-items-center justify-content-center me-3"
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                    <div class="flex-grow-1 me-3">
                                        <div class="fw-semibold text-dark mb-1">Change Password</div>
                                        <small class="text-muted">Update your account password</small>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </div>
                            </div>

                            <a href="{{ route('account.transactions') }}"
                               class="list-group-item list-group-item-action border-0 rounded-3 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="setting-icon bg-gradient-primary text-white rounded-3 d-flex align-items-center justify-content-center me-3"
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-chart-bar"></i>
                                    </div>
                                    <div class="flex-grow-1 me-3">
                                        <div class="fw-semibold text-dark mb-1">Transaction History</div>
                                        <small class="text-muted">View all your point transactions</small>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="card-body border-top p-0">
                    <div class="p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-semibold text-dark mb-0">
                                <i class="fas fa-history text-primary me-2"></i>
                                Recent Transactions
                            </h5>
                            <a href="{{ route('account.transactions') }}"
                               class="btn btn-outline-primary btn-sm">
                                See All
                            </a>
                        </div>

                        @forelse($transactions->take(5) as $transaction)
                            <div class="card border transaction-card mb-3 cursor-pointer"
                                 onclick="showTransactionDetail({{ json_encode($transaction) }})">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-grow-1 me-3">
                                            <h6 class="card-title mb-1 fw-semibold text-truncate">
                                                {{ $transaction->item_name ?: 'Unknown Item' }}
                                            </h6>
                                            <div class="text-muted small mb-1">
                                                <i class="fas fa-store me-1"></i>
                                                {{ $transaction->store_name ?: 'Unknown Store' }}
                                                @if($transaction->store_location)
                                                    <span class="text-secondary"> • {{ $transaction->store_location }}</span>
                                                @endif
                                            </div>
                                            <div class="text-muted small">
                                                <i class="far fa-calendar me-1"></i>
                                                @if($transaction->transaction_date)
                                                    {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y • h:i A') }}
                                                @else
                                                    {{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y • h:i A') }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-end d-flex align-items-center">
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
                        @empty
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-chart-bar fa-4x text-muted opacity-50"></i>
                                </div>
                                <h6 class="fw-semibold text-dark mb-3">No transactions yet</h6>
                                <p class="text-muted mb-4">Start scanning QR codes to earn points!</p>
                                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                    <i class="fas fa-qrcode me-2"></i>
                                    Go to Dashboard
                                </a>
                            </div>
                        @endforelse

                        @if($transactions->count() > 5)
                            <div class="text-center mt-3">
                                <small class="text-muted">Showing 5 of {{ $transactions->count() }} transactions</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Password Change Modal -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="passwordModalLabel">
                    <i class="fas fa-lock me-2"></i>
                    Change Password
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="passwordForm" action="{{ route('account.password.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-semibold">Current Password</label>
                        <input type="password" class="form-control form-control-lg" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">New Password</label>
                        <input type="password" class="form-control form-control-lg" id="password" name="password" required minlength="8">
                        <div class="form-text">Password must be at least 8 characters long.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label fw-semibold">Confirm New Password</label>
                        <input type="password" class="form-control form-control-lg" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark">
                        <i class="fas fa-save me-2"></i>
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Transaction Detail Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white border-bottom-0 pb-0">
                <h5 class="modal-title" id="transactionModalLabel">
                    <i class="fas fa-receipt me-2"></i>
                    Transaction Receipt
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0">
                <!-- Transaction Status -->
                <div class="text-center py-4 bg-light">
                    <div class="mb-2">
                        <i class="fas fa-check-circle fa-3x text-success"></i>
                    </div>
                    <h6 class="fw-semibold text-success mb-0">Transaction Completed</h6>
                </div>

                <!-- Points Display -->
                <div class="text-center py-4 border-bottom">
                    <div class="fw-bold text-primary mb-1" style="font-size: 2.5rem;" id="modalPointsAmount">+0</div>
                    <small class="text-muted fw-semibold">POINTS</small>
                </div>

                <div class="p-4">
                    <!-- Transaction Details -->
                    <div class="mb-4">
                        <h6 class="fw-semibold text-dark mb-3 pb-2 border-bottom">
                            <i class="fas fa-info-circle me-2"></i>
                            Transaction Details
                        </h6>

                        <div class="row g-2 small">
                            <div class="col-4 text-muted">Item:</div>
                            <div class="col-8 fw-medium" id="modalItemName">-</div>

                            <div class="col-4 text-muted">Store:</div>
                            <div class="col-8 fw-medium" id="modalStoreName">-</div>

                            <div class="col-4 text-muted">Location:</div>
                            <div class="col-8 fw-medium" id="modalStoreLocation">-</div>

                            <div class="col-4 text-muted">Date & Time:</div>
                            <div class="col-8 fw-medium" id="modalDateTime">-</div>

                            <div class="col-4 text-muted">Transaction ID:</div>
                            <div class="col-8 fw-medium font-monospace" id="modalTransactionId">-</div>

                            <div class="col-4 text-muted">Units Scanned:</div>
                            <div class="col-8 fw-medium" id="modalUnitsScanned">-</div>
                        </div>
                    </div>

                    <!-- QR Code Information -->
                    <div class="mb-4">
                        <h6 class="fw-semibold text-dark mb-3 pb-2 border-bottom">
                            <i class="fas fa-qrcode me-2"></i>
                            QR Code Information
                        </h6>

                        <div class="row g-2 small">
                            <div class="col-4 text-muted">QR Code:</div>
                            <div class="col-8">
                                <span class="badge bg-light text-dark font-monospace" id="modalQrCode">-</span>
                            </div>

                            <div class="col-4 text-muted">Points per Unit:</div>
                            <div class="col-8 fw-medium text-success" id="modalPointsPerUnit">-</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-dark w-100" onclick="shareTransaction()">
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

.bg-gradient-light {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%) !important;
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

.btn-dark {
    background: #1a1a1a;
    border-color: #1a1a1a;
    transition: all 0.3s ease;
}

.btn-dark:hover {
    background: #333;
    border-color: #333;
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

.list-group-item-action {
    transition: all 0.3s ease;
    border: 1px solid transparent !important;
}

.list-group-item-action:hover {
    background-color: rgba(29, 209, 161, 0.05) !important;
    border-color: rgba(29, 209, 161, 0.1) !important;
    transform: translateY(-1px);
}

.list-group-item-action:active {
    transform: translateY(0);
}

.cursor-pointer {
    cursor: pointer;
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

/* Mobile optimizations */
@media (max-width: 576px) {
    .container-fluid {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }

    .transaction-card .card-body,
    .list-group-item {
        padding: 1rem !important;
    }

    .points-circle {
        width: 120px !important;
        height: 120px !important;
        max-width: 60vw;
        max-height: 60vw;
        min-width: 80px;
        min-height: 80px;
        box-sizing: border-box;
    }

    .points-circle .fw-bold {
        font-size: 1.75rem !important;
    }

    @media (max-width: 576px) {
        .points-circle {
            width: 38vw !important;
            height: 38vw !important;
            min-width: 70px;
            min-height: 70px;
            max-width: 90vw;
            max-height: 90vw;
        }
        .points-circle .fw-bold {
            font-size: 1.2rem !important;
        }
    }

    .modal-dialog {
        margin: 0.75rem;
    }
}

/* Loading states */
.btn:disabled {
    opacity: 0.6;
    transform: none !important;
}

/* Better focus indicators for accessibility */
.btn:focus,
.form-control:focus,
.list-group-item-action:focus {
    outline: 2px solid #1dd1a1;
    outline-offset: 2px;
}
</style>

<!-- Bootstrap JS (if not already included) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Font Awesome (if not already included) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<script>
// Preserve all original functionality
function showTransactionDetail(transaction) {
    // Populate modal with transaction data - exact same logic
    const pointsAmount = document.getElementById('modalPointsAmount');
    const points = (transaction.type === 'earn' ? '+' : '-') + (transaction.points || 0);
    pointsAmount.textContent = points;
    pointsAmount.style.color = transaction.type === 'earn' ? '#27ae60' : '#e74c3c';

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

    let qrCode = transaction.qr_code || transaction.code || 'N/A';
    if (qrCode === 'N/A') {
        qrCode = 'QR-TXN-' + (transaction.id || 'X');
    }
    document.getElementById('modalQrCode').textContent = qrCode;

    document.getElementById('modalPointsPerUnit').textContent =
        (transaction.points_per_unit || transaction.points || 0) + ' pts';

    // Show modal using Bootstrap
    const modal = new bootstrap.Modal(document.getElementById('transactionModal'));
    modal.show();
}

function shareTransaction() {
    // Exact same logic preserved
    if (navigator.share) {
        navigator.share({
            title: 'Green Cups Transaction',
            text: 'Check out my eco-friendly purchase!',
            url: window.location.href
        }).catch(console.error);
    } else {
        // Fallback
        const receiptText = `Green Cups Transaction\nID: ${document.getElementById('modalTransactionId').textContent}\nPoints: ${document.getElementById('modalPointsAmount').textContent}`;

        if (navigator.clipboard) {
            navigator.clipboard.writeText(receiptText).then(() => {
                alert('Receipt copied to clipboard!');
            });
        } else {
            alert('Sharing not supported on this device');
        }
    }
}

function showPasswordChangeModal() {
    // Show Bootstrap modal
    const modal = new bootstrap.Modal(document.getElementById('passwordModal'));
    modal.show();

    // Clear form
    const form = document.getElementById('passwordForm');
    if (form) {
        form.reset();
    }
}

function closeFlashMessage() {
    const flashMessage = document.getElementById('flashMessage');
    if (flashMessage) {
        // Use Bootstrap's fade out
        flashMessage.style.animation = 'fadeOut 0.3s ease forwards';
        setTimeout(() => {
            flashMessage.remove();
        }, 300);
    }
}

// Auto-hide flash messages after 5 seconds - preserved logic
document.addEventListener('DOMContentLoaded', function() {
    const flashMessage = document.getElementById('flashMessage');
    if (flashMessage) {
        setTimeout(() => {
            closeFlashMessage();
        }, 5000);
    }
});

// Handle password form submission - exact same validation logic
document.addEventListener('DOMContentLoaded', function() {
    const passwordForm = document.getElementById('passwordForm');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('New passwords do not match!');
                return;
            }

            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long!');
                return;
            }
        });
    }
});

// Enhanced mobile experience
document.addEventListener('DOMContentLoaded', function() {
    // Auto-resize inputs on mobile
    if (window.innerWidth <= 768) {
        const inputs = document.querySelectorAll('input');
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

// CSS animation for fade out
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(-10px); }
    }
`;
document.head.appendChild(style);
</script>
@endsection
