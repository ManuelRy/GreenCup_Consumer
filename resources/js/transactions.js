/**
 * Transaction History Page - JavaScript Module
 * Handles transaction detail modal and sharing functionality
 */

export function showTransactionDetail(transaction) {
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

export function shareTransaction() {
    if (navigator.share) {
        const pointsAmount = document.getElementById('modalPointsAmount').textContent;
        const storeName = document.getElementById('modalStoreName').textContent;
        const receiptCode = document.getElementById('modalReceiptCode').textContent;

        navigator.share({
            title: 'Transaction Receipt',
            text: `I earned ${pointsAmount} points at ${storeName}! Receipt: ${receiptCode}`,
            url: window.location.href
        }).catch(err => {
            console.log('Error sharing:', err);
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

// Make functions globally available
window.showTransactionDetail = showTransactionDetail;
window.shareTransaction = shareTransaction;
