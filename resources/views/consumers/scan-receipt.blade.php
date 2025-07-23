@extends('master')

@section('content')
<div class="scan-container">
    <!-- Header -->
    <div class="scan-header">
        <div class="header-content">
            <a href="{{ route('dashboard') }}" class="back-button">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="header-title">
                <h1>Scan Receipt</h1>
                <p>Scan seller's QR code to earn points</p>
            </div>
            <div class="points-display">
                <span class="points-value" id="current-points">{{ Auth::guard('consumer')->user()->getAvailablePoints() ?? 0 }}</span>
                <span class="points-label">points</span>
            </div>
        </div>
    </div>

    <!-- Scanner Section -->
    <div class="scanner-section">
        <div class="scanner-header">
            <h3>📱 QR Code Scanner</h3>
            <p>Position the QR code within the frame</p>
        </div>
        
        <div class="scanner-wrapper">
            <div id="qr-reader"></div>
            <div class="scanner-overlay">
                <div class="scanner-frame">
                    <div class="corner tl"></div>
                    <div class="corner tr"></div>
                    <div class="corner bl"></div>
                    <div class="corner br"></div>
                    <div class="scan-line"></div>
                </div>
            </div>
            
            <!-- Camera Status Overlay - FIXED: Now properly hidden when camera loads -->
            <div id="camera-status" class="camera-status hidden">
                <div class="status-icon">📷</div>
                <div class="status-message">Initializing camera...</div>
                <button id="retry-camera" class="retry-btn" style="display: none;">Try Again</button>
                <button id="use-manual" class="manual-btn" style="display: none;">Use Manual Input</button>
                <button id="debug-camera" class="debug-btn" style="display: none;">Show Debug Info</button>
            </div>
        </div>
        
        <div class="scanner-instructions">
            <p>💡 Hold your phone steady and make sure the QR code is clearly visible</p>
            <p style="margin-top: 0.5rem; font-size: 0.8rem; opacity: 0.7;">
                🧪 For testing: Enter "demo123" in manual input to see demo receipt<br>
                ✅ Valid: Receipt QR codes from participating stores<br>
                ❌ Invalid: Website URLs, social media codes, personal QR codes<br>
                📱 <strong>Mobile Tips:</strong><br>
                • Allow camera permissions when prompted<br>
                • Close other camera apps if scanner fails<br>
                • Use "Try Again" if camera doesn't start<br>
                • Manual input works on all devices
            </p>
        </div>
    </div>

    <!-- Manual Input Section -->
    <div class="manual-section">
        <div class="divider">
            <span>Or enter code manually</span>
        </div>
        
        <form id="manual-code-form" class="input-group">
            <input type="text" 
                   id="receipt-code" 
                   placeholder="Enter receipt code" 
                   class="receipt-input"
                   autocomplete="off">
            <button type="submit" class="submit-btn">
                <span class="btn-text">Verify</span>
                <span class="btn-loading" style="display: none;">
                    <div class="loading-spinner"></div>
                </span>
            </button>
        </form>
    </div>

    <!-- Recent Claims -->
    <div class="recent-section">
        <div class="recent-header">
            <h3>🎁 Recent Claims</h3>
        </div>
        <div class="recent-list">
            @php
                // Use point_transactions table since pending_transactions might not exist yet
                try {
                    $recentClaims = DB::table('point_transactions')
                        ->where('consumer_id', Auth::guard('consumer')->user()->id)
                        ->where('type', 'earn')
                        ->join('sellers', 'point_transactions.seller_id', '=', 'sellers.id')
                        ->select(
                            'point_transactions.points as total_points',
                            'point_transactions.description',
                            'point_transactions.scanned_at as claimed_at',
                            'point_transactions.created_at',
                            'sellers.business_name'
                        )
                        ->orderBy('point_transactions.scanned_at', 'desc')
                        ->limit(5)
                        ->get();
                } catch (\Exception $e) {
                    // Handle case where tables don't exist or query fails
                    $recentClaims = collect([]);
                    \Log::error('Recent claims query failed: ' . $e->getMessage());
                }
            @endphp
            
            @forelse($recentClaims as $claim)
                <div class="recent-item">
                    <div class="recent-icon">🏪</div>
                    <div class="recent-details">
                        <p class="recent-store">{{ $claim->business_name }}</p>
                        <div class="recent-meta">
                            <span class="points-badge">+{{ $claim->total_points }} pts</span>
                            <span>{{ \Carbon\Carbon::parse($claim->claimed_at)->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="no-claims">
                    <div class="no-claims-icon">📱</div>
                    <p><strong>No claims yet</strong><br>Start scanning to earn your first points!</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Receipt Preview Modal -->
<div id="receipt-modal" class="modal">
    <div class="modal-backdrop"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h2>📋 Receipt Details</h2>
            <button class="close-modal" type="button">&times;</button>
        </div>
        
        <div class="modal-body">
            <!-- Store Info -->
            <div class="store-info">
                <div class="store-avatar">🏪</div>
                <div class="store-details">
                    <h3 id="store-name">Loading...</h3>
                    <p id="store-address"></p>
                </div>
            </div>
            
            <!-- Items List -->
            <div class="receipt-items">
                <h4>📦 Items</h4>
                <div id="items-list" class="items-container">
                    <!-- Items will be populated here -->
                </div>
            </div>
            
            <!-- Total Points -->
            <div class="receipt-total">
                <span>Total Points:</span>
                <span class="total-points" id="total-points">0</span>
            </div>
            
            <!-- Action Buttons -->
            <div class="modal-actions">
                <button class="cancel-btn" onclick="closeModal()">Cancel</button>
                <button class="claim-btn" id="claim-button" onclick="claimPoints()">
                    <span class="btn-text">Claim Points</span>
                    <span class="btn-loading" style="display: none;">
                        <div class="loading-spinner"></div>
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Animation -->
<div id="success-overlay" class="success-overlay">
    <div class="success-content">
        <div class="success-animation">
            <div class="success-icon">✓</div>
            <div class="success-rings">
                <div class="ring ring-1"></div>
                <div class="ring ring-2"></div>
                <div class="ring ring-3"></div>
            </div>
        </div>
        <h2>Points Claimed!</h2>
        <p class="success-message">
            🎉 You earned <span class="points-earned" id="points-amount">0</span> points!
        </p>
        <button class="success-btn" onclick="location.reload()">Scan Another Receipt</button>
    </div>
</div>

<!-- Error Toast -->
<div id="error-toast" class="toast error-toast">
    <div class="toast-content">
        <span class="toast-icon">⚠️</span>
        <span class="toast-message" id="error-message">Something went wrong</span>
    </div>
</div>

<style>
/* Modern Color System */
:root {
    --primary-green: #10b981;
    --primary-green-dark: #059669;
    --primary-green-light: #34d399;
    --background: #f8fafc;
    --card-bg: #ffffff;
    --text-primary: #111827;
    --text-secondary: #6b7280;
    --text-muted: #9ca3af;
    --border: #e5e7eb;
    --border-light: #f3f4f6;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Base Styles */
* {
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    line-height: 1.5;
    color: var(--text-primary);
    background: var(--background);
    margin: 0;
    padding: 0;
}

/* Container */
.scan-container {
    min-height: 100vh;
    background: var(--background);
    padding-bottom: 2rem;
}

/* Header */
.scan-header {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    color: white;
    padding: 1.5rem 1rem;
    position: relative;
    overflow: hidden;
}

.scan-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
    animation: backgroundMove 20s linear infinite;
}

@keyframes backgroundMove {
    0% { transform: translate(0, 0); }
    100% { transform: translate(60px, 60px); }
}

.header-content {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    gap: 1rem;
    max-width: 500px;
    margin: 0 auto;
}

.back-button {
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    width: 44px;
    height: 44px;
    border-radius: 12px;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.back-button:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateX(-2px);
}

.header-title {
    flex: 1;
    text-align: center;
}

.header-title h1 {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.header-title p {
    font-size: 0.875rem;
    margin: 0.25rem 0 0;
    opacity: 0.9;
}

.points-display {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 0.75rem;
    text-align: center;
    min-width: 70px;
    flex-shrink: 0;
}

.points-value {
    display: block;
    font-size: 1.25rem;
    font-weight: 800;
    line-height: 1;
}

.points-label {
    display: block;
    font-size: 0.75rem;
    opacity: 0.8;
    margin-top: 2px;
}

/* Scanner Section */
.scanner-section {
    margin: 1.5rem 1rem;
    background: var(--card-bg);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--shadow-lg);
}

.scanner-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.25rem;
    text-align: center;
}

.scanner-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.scanner-header p {
    margin: 0.5rem 0 0;
    opacity: 0.9;
    font-size: 0.875rem;
}

.scanner-wrapper {
    position: relative;
    background: #000;
    height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

#qr-reader {
    width: 100%;
    height: 100%;
}

/* FIXED: Simplified video styling */
#qr-reader video {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    border-radius: 0 !important;
}

/* FIXED: Less aggressive element hiding */
#qr-reader__dashboard_section_swaplink,
#qr-reader__dashboard_section_csr {
    display: none !important;
}

/* Mobile-specific adjustments */
@media (max-width: 768px) {
    .scanner-wrapper {
        height: 350px;
    }
}

.scanner-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 220px;
    height: 220px;
    pointer-events: none;
    z-index: 10;
}

/* FIXED: Camera Status Overlay - starts hidden */
.camera-status {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    color: white;
    background: rgba(0, 0, 0, 0.8);
    padding: 2rem;
    border-radius: 16px;
    z-index: 15;
    backdrop-filter: blur(10px);
    max-width: 300px;
}

.camera-status.hidden {
    display: none !important;
}

.status-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.8;
}

.status-message {
    font-size: 1rem;
    margin-bottom: 1.5rem;
    opacity: 0.9;
    line-height: 1.4;
}

.retry-btn, .manual-btn, .debug-btn {
    background: var(--primary-green);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    pointer-events: auto;
    margin: 0.25rem;
}

.retry-btn:hover, .manual-btn:hover, .debug-btn:hover {
    background: var(--primary-green-dark);
    transform: translateY(-1px);
}

.manual-btn {
    background: var(--text-secondary);
}

.manual-btn:hover {
    background: var(--text-primary);
}

.debug-btn {
    background: #6366f1;
    font-size: 0.8rem;
    padding: 0.5rem 1rem;
}

.debug-btn:hover {
    background: #4f46e5;
}

.scanner-frame {
    width: 100%;
    height: 100%;
    position: relative;
}

.corner {
    position: absolute;
    width: 30px;
    height: 30px;
    border: 3px solid var(--primary-green);
    border-radius: 4px;
}

.corner.tl {
    top: 0;
    left: 0;
    border-right: none;
    border-bottom: none;
}

.corner.tr {
    top: 0;
    right: 0;
    border-left: none;
    border-bottom: none;
}

.corner.bl {
    bottom: 0;
    left: 0;
    border-right: none;
    border-top: none;
}

.corner.br {
    bottom: 0;
    right: 0;
    border-left: none;
    border-top: none;
}

.scan-line {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--primary-green);
    box-shadow: 0 0 10px var(--primary-green);
    animation: scanLine 2s ease-in-out infinite;
}

@keyframes scanLine {
    0%, 100% { 
        transform: translateY(0); 
        opacity: 1; 
    }
    50% { 
        transform: translateY(216px); 
        opacity: 0.8; 
    }
}

.scanner-instructions {
    padding: 1.5rem;
    text-align: center;
    background: var(--border-light);
    color: var(--text-secondary);
    font-size: 0.875rem;
}

/* Manual Input */
.manual-section {
    margin: 1.5rem 1rem;
    background: var(--card-bg);
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: var(--shadow);
}

.divider {
    display: flex;
    align-items: center;
    margin: 0 0 1.5rem;
    color: var(--text-muted);
    font-size: 0.875rem;
    text-align: center;
}

.divider::before,
.divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border);
}

.divider span {
    padding: 0 1rem;
    background: var(--card-bg);
    font-weight: 500;
}

.input-group {
    display: flex;
    gap: 0.75rem;
    align-items: stretch;
}

.receipt-input {
    flex: 1;
    padding: 1rem;
    border: 2px solid var(--border);
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: var(--border-light);
    -webkit-appearance: none;
    -webkit-border-radius: 12px;
    min-height: 48px;
}

.receipt-input:focus {
    outline: none;
    border-color: var(--primary-green);
    background: white;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    font-size: 16px;
}

/* Mobile input improvements */
@media (max-width: 768px) {
    .receipt-input {
        font-size: 16px;
        padding: 1.2rem;
        min-height: 52px;
    }
    
    .submit-btn {
        min-height: 52px;
        padding: 1.2rem 1.5rem;
    }
}

.submit-btn, .claim-btn, .cancel-btn, .success-btn {
    position: relative;
    padding: 1rem 1.5rem;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.submit-btn {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    color: white;
    box-shadow: var(--shadow);
}

.submit-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: var(--shadow-lg);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

/* Recent Claims */
.recent-section {
    margin: 1.5rem 1rem;
    background: var(--card-bg);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow);
}

.recent-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border-light);
    background: linear-gradient(135deg, var(--border-light) 0%, var(--card-bg) 100%);
}

.recent-header h3 {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
}

.recent-list {
    max-height: 280px;
    overflow-y: auto;
}

.recent-item {
    display: flex;
    align-items: center;
    padding: 1rem 1.5rem;
    transition: all 0.3s ease;
    border-bottom: 1px solid var(--border-light);
}

.recent-item:hover {
    background: var(--border-light);
    transform: translateX(4px);
}

.recent-item:last-child {
    border-bottom: none;
}

.recent-icon {
    width: 44px;
    height: 44px;
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    margin-right: 1rem;
    flex-shrink: 0;
}

.recent-details {
    flex: 1;
    min-width: 0;
}

.recent-store {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.recent-meta {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.points-badge {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
    flex-shrink: 0;
}

.no-claims {
    text-align: center;
    padding: 3rem 1.5rem;
    color: var(--text-secondary);
}

.no-claims-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1000;
}

.modal-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
}

.modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: var(--card-bg);
    border-radius: 20px;
    width: 90%;
    max-width: 420px;
    max-height: 80vh;
    overflow: hidden;
    box-shadow: var(--shadow-xl);
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translate(-50%, -60%);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%);
    }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-light);
    background: linear-gradient(135deg, var(--border-light) 0%, var(--card-bg) 100%);
}

.modal-header h2 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
}

.close-modal {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--text-muted);
    cursor: pointer;
    padding: 0.25rem;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.close-modal:hover {
    background: var(--border-light);
    color: var(--text-secondary);
}

.modal-body {
    padding: 1.5rem;
    max-height: 60vh;
    overflow-y: auto;
}

.store-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: var(--border-light);
    border-radius: 12px;
}

.store-avatar {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.store-details h3 {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
}

.store-details p {
    margin: 0.25rem 0 0;
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.receipt-items {
    margin-bottom: 1.5rem;
}

.receipt-items h4 {
    margin: 0 0 1rem;
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
}

.items-container {
    border: 1px solid var(--border-light);
    border-radius: 12px;
    overflow: hidden;
}

.receipt-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid var(--border-light);
    background: var(--card-bg);
    transition: background-color 0.3s ease;
}

.receipt-item:last-child {
    border-bottom: none;
}

.receipt-item:hover {
    background: var(--border-light);
}

.item-details {
    flex: 1;
}

.item-name {
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.item-qty {
    color: var(--text-secondary);
    font-size: 0.875rem;
    margin: 0.25rem 0 0;
}

.item-points {
    font-weight: 700;
    color: var(--primary-green);
    font-size: 1.125rem;
}

.receipt-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem;
    background: linear-gradient(135deg, var(--primary-green-light) 0%, var(--primary-green) 100%);
    color: white;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    font-size: 1.125rem;
    font-weight: 600;
}

.total-points {
    font-size: 1.5rem;
    font-weight: 800;
}

.modal-actions {
    display: flex;
    gap: 0.75rem;
}

.cancel-btn {
    flex: 1;
    background: var(--border);
    color: var(--text-secondary);
}

.cancel-btn:hover {
    background: var(--text-muted);
    color: white;
}

.claim-btn {
    flex: 2;
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    color: white;
}

.claim-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: var(--shadow-lg);
}

.claim-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

/* Success Overlay */
.success-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.95) 0%, rgba(5, 150, 105, 0.95) 100%);
    z-index: 2000;
    backdrop-filter: blur(10px);
}

.success-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    color: white;
    padding: 2rem;
}

.success-animation {
    position: relative;
    margin: 0 auto 2rem;
    width: 120px;
    height: 120px;
}

.success-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80px;
    height: 80px;
    background: white;
    color: var(--primary-green);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-weight: bold;
    z-index: 3;
    animation: successBounce 0.6s ease-out;
}

@keyframes successBounce {
    0% {
        transform: translate(-50%, -50%) scale(0);
        opacity: 0;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.2);
        opacity: 1;
    }
    100% {
        transform: translate(-50%, -50%) scale(1);
    }
}

.success-rings {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.ring {
    position: absolute;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    animation: ripple 2s ease-out infinite;
}

.ring-1 {
    width: 80px;
    height: 80px;
    margin: -40px 0 0 -40px;
    animation-delay: 0s;
}

.ring-2 {
    width: 100px;
    height: 100px;
    margin: -50px 0 0 -50px;
    animation-delay: 0.3s;
}

.ring-3 {
    width: 120px;
    height: 120px;
    margin: -60px 0 0 -60px;
    animation-delay: 0.6s;
}

@keyframes ripple {
    0% {
        transform: scale(0);
        opacity: 1;
    }
    100% {
        transform: scale(1);
        opacity: 0;
    }
}

.success-content h2 {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 0.5rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.success-message {
    font-size: 1.125rem;
    margin: 0 0 2rem;
    opacity: 0.95;
}

.points-earned {
    font-weight: 800;
    font-size: 1.5rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.success-btn {
    background: white;
    color: var(--primary-green);
    border-radius: 12px;
    font-weight: 600;
    box-shadow: var(--shadow-lg);
}

.success-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-xl);
}

/* Loading Spinner */
.loading-spinner {
    width: 20px;
    height: 20px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.btn-loading {
    display: none;
}

/* Toast */
.toast {
    position: fixed;
    top: 2rem;
    right: 2rem;
    background: var(--card-bg);
    border-radius: 12px;
    padding: 1rem;
    box-shadow: var(--shadow-lg);
    z-index: 3000;
    transform: translateX(400px);
    transition: transform 0.3s ease;
    max-width: 350px;
    min-width: 280px;
}

.toast.show {
    transform: translateX(0);
}

.toast.error-toast {
    background: #fee2e2;
    border-left: 4px solid #dc2626;
}

.toast-content {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.toast-icon {
    font-size: 1.25rem;
    flex-shrink: 0;
    margin-top: 0.125rem;
}

.toast-message {
    font-weight: 500;
    color: var(--text-primary);
    line-height: 1.4;
    word-wrap: break-word;
}

.toast-message br {
    margin-bottom: 0.5rem;
}

/* Responsive Design */
@media (max-width: 480px) {
    .scan-container {
        padding-bottom: 1rem;
    }
    
    .scan-header {
        padding: 1rem;
    }
    
    .header-title h1 {
        font-size: 1.25rem;
    }
    
    .scanner-section, .manual-section, .recent-section {
        margin: 1rem 0.75rem;
    }
    
    .scanner-wrapper {
        height: 350px;
    }
    
    .camera-status {
        padding: 1.5rem;
        max-width: 280px;
        font-size: 0.9rem;
    }
    
    .status-icon {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
    }
    
    .status-message {
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }
    
    .retry-btn, .manual-btn, .debug-btn {
        padding: 0.6rem 1.25rem;
        font-size: 0.85rem;
        display: block;
        width: 100%;
        margin: 0.25rem 0;
    }
    
    .debug-btn {
        font-size: 0.75rem;
        padding: 0.5rem 1rem;
    }
    
    .scanner-overlay {
        width: 180px;
        height: 180px;
    }
    
    .corner {
        width: 24px;
        height: 24px;
        border-width: 2px;
    }
    
    .scan-line {
        animation: scanLineMobile 2s ease-in-out infinite;
    }
    
    @keyframes scanLineMobile {
        0%, 100% { 
            transform: translateY(0); 
            opacity: 1; 
        }
        50% { 
            transform: translateY(176px); 
            opacity: 0.8; 
        }
    }
    
    .modal-content {
        width: 95%;
        max-height: 90vh;
    }
    
    .input-group {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .submit-btn {
        width: 100%;
    }
    
    .toast {
        top: 1rem;
        right: 1rem;
        left: 1rem;
        max-width: none;
        min-width: auto;
        transform: translateY(-100px);
    }
    
    .toast.show {
        transform: translateY(0);
    }
}

@media (max-width: 360px) {
    .header-content {
        gap: 0.5rem;
    }
    
    .points-display {
        min-width: 60px;
        padding: 0.5rem;
    }
    
    .points-value {
        font-size: 1.125rem;
    }
}
</style>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
let currentReceiptCode = null;
let scanner = null;
let isProcessing = false;
let cameraInitialized = false;

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    setupEventListeners();
    initializeCamera();
});

// FIXED: Simplified camera initialization
function initializeCamera() {
    console.log('Initializing camera...');
    
    // Check basic requirements
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        showCameraStatus('Camera not supported on this browser.\n\nTry using Chrome, Firefox, or Safari.', '❌', false, true);
        return;
    }

    // Check HTTPS requirement
    if (!window.isSecureContext && !location.hostname.includes('localhost')) {
        showCameraStatus('Camera requires HTTPS connection for security.', '🔒', false, true);
        return;
    }

    // Start scanner directly
    startSimpleScanner();
}

// FIXED: Simple scanner start function
function startSimpleScanner() {
    console.log('Starting simple scanner...');
    showCameraStatus('Starting camera...', '📷');
    
    try {
        // Clear any existing scanner
        if (scanner) {
            scanner.clear();
        }

        // Clear the container
        const readerElement = document.getElementById('qr-reader');
        readerElement.innerHTML = '';

        // Simple, working configuration
        const config = {
            fps: 10,
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0,
            disableFlip: false
        };
        
        scanner = new Html5QrcodeScanner("qr-reader", config);
        
        scanner.render(
            (decodedText, decodedResult) => {
                console.log('QR code scanned:', decodedText);
                onScanSuccess(decodedText, decodedResult);
            },
            (error) => {
                // Only log real errors, not scanning attempts
                if (!error.includes('NotFoundException') && !error.includes('No QR code found')) {
                    console.log('Scanner error:', error);
                }
            }
        );
        
        // Check if camera loaded after delay
        setTimeout(() => {
            checkCameraLoaded();
        }, 3000);
        
    } catch (error) {
        console.error('Scanner initialization failed:', error);
        showCameraStatus('Failed to initialize scanner.\n\nError: ' + error.message, '❌', true, true);
    }
}

// FIXED: Simplified camera loading check
function checkCameraLoaded() {
    console.log('Checking camera status...');
    
    const video = document.querySelector('#qr-reader video');
    
    if (video && video.videoWidth > 0 && video.videoHeight > 0) {
        console.log('Camera loaded successfully!');
        hideCameraStatus();
        cameraInitialized = true;
        return;
    }
    
    // Wait a bit more
    setTimeout(() => {
        const video2 = document.querySelector('#qr-reader video');
        if (video2 && video2.videoWidth > 0 && video2.videoHeight > 0) {
            console.log('Camera loaded after delay!');
            hideCameraStatus();
            cameraInitialized = true;
        } else {
            console.log('Camera failed to load');
            showCameraStatus(
                'Camera failed to start.\n\nThis might be due to:\n• Camera permissions\n• Camera being used by another app\n• Browser compatibility\n\nPlease try again or use manual input.', 
                '❌', 
                true, 
                true
            );
        }
    }, 2000);
}

function showCameraStatus(message, icon = '📷', showRetry = false, showManual = false) {
    const statusEl = document.getElementById('camera-status');
    const messageEl = statusEl.querySelector('.status-message');
    const iconEl = statusEl.querySelector('.status-icon');
    const retryBtn = document.getElementById('retry-camera');
    const manualBtn = document.getElementById('use-manual');
    const debugBtn = document.getElementById('debug-camera');
    
    messageEl.innerHTML = message.replace(/\n/g, '<br>');
    iconEl.textContent = icon;
    
    retryBtn.style.display = showRetry ? 'inline-block' : 'none';
    manualBtn.style.display = showManual ? 'inline-block' : 'none';
    debugBtn.style.display = 'none'; // Simplified - removed debug for now
    
    statusEl.classList.remove('hidden');
    console.log('Camera status shown:', message);
}

function hideCameraStatus() {
    const statusEl = document.getElementById('camera-status');
    statusEl.classList.add('hidden');
    console.log('Camera status hidden');
}

function retryCamera() {
    console.log('Retrying camera...');
    showCameraStatus('🔄 Retrying camera...', '🔄');
    cameraInitialized = false;
    
    // Clear existing scanner
    if (scanner) {
        try {
            scanner.clear();
        } catch (e) {
            console.log('Scanner clear failed:', e);
        }
        scanner = null;
    }
    
    // Clear the qr-reader div
    const readerElement = document.getElementById('qr-reader');
    readerElement.innerHTML = '';
    
    // Retry after delay
    setTimeout(() => {
        startSimpleScanner();
    }, 1000);
}

function focusManualInput() {
    document.querySelector('.manual-section').scrollIntoView({ behavior: 'smooth' });
    setTimeout(() => {
        document.getElementById('receipt-code').focus();
    }, 500);
}

function setupEventListeners() {
    // Manual code form
    document.getElementById('manual-code-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const code = document.getElementById('receipt-code').value.trim();
        if (code && !isProcessing) {
            if (code.toLowerCase() === 'demo123') {
                showDemoReceipt();
                return;
            }
            checkReceipt(code);
        }
    });

    // Camera retry button
    document.getElementById('retry-camera').addEventListener('click', retryCamera);
    
    // Manual input button
    document.getElementById('use-manual').addEventListener('click', focusManualInput);

    // Modal close events
    document.querySelector('.close-modal').addEventListener('click', closeModal);
    document.querySelector('.modal-backdrop').addEventListener('click', closeModal);
    
    // Keyboard events
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
}

function onScanSuccess(decodedText, decodedResult) {
    if (isProcessing) return;
    
    // Stop scanning temporarily
    if (scanner) {
        try {
            scanner.pause();
        } catch (e) {
            console.log('Scanner pause failed:', e);
        }
    }
    
    // Extract receipt code
    let receiptCode = extractReceiptCode(decodedText);
    
    // For testing - if code starts with "demo", show demo receipt
    if (receiptCode.toLowerCase().startsWith('demo')) {
        showDemoReceipt();
        return;
    }
    
    checkReceipt(receiptCode);
}

function extractReceiptCode(decodedText) {
    // Handle different QR code formats
    if (decodedText.includes('/')) {
        const parts = decodedText.split('/');
        return parts[parts.length - 1];
    }
    
    if (decodedText.includes('receipt_code=')) {
        const match = decodedText.match(/receipt_code=([^&]+)/);
        return match ? match[1] : decodedText;
    }
    
    return decodedText;
}

// FIXED: Added missing function
function isValidReceiptCode(code) {
    // Basic validation - adjust as needed
    if (!code || typeof code !== 'string') return false;
    if (code.length < 3) return false;
    if (code.toLowerCase() === 'demo123') return true; // Allow demo
    
    // Add your specific validation rules here
    return /^[a-zA-Z0-9-_]+$/.test(code);
}

function checkReceipt(code) {
    if (isProcessing) return;
    
    // Validate code format first
    if (!isValidReceiptCode(code)) {
        showError(`Invalid QR code format: "${code}". Please scan a valid receipt QR code from a store.`);
        resumeScanner();
        return;
    }
    
    isProcessing = true;
    currentReceiptCode = code;
    
    // Show loading state
    showModal();
    setLoadingState(true);
    document.getElementById('store-name').textContent = 'Verifying receipt code...';
    
    // Clear previous data
    document.getElementById('items-list').innerHTML = '';
    document.getElementById('total-points').textContent = '0';
    
    fetch('/api/receipt/check', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ receipt_code: code })
    })
    .then(response => {
        if (!response.ok) {
            if (response.status === 404) {
                throw new Error('API_NOT_FOUND');
            } else if (response.status === 422) {
                throw new Error('INVALID_CODE');
            }
            throw new Error(`HTTP_${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            displayReceipt(data.receipt);
        } else {
            let errorMessage = 'Unknown error occurred';
            
            if (data.message) {
                if (data.message.includes('not found') || data.message.includes('invalid')) {
                    errorMessage = `❌ Receipt code "${code}" not found. This may be:\n• An expired receipt\n• Invalid receipt code\n• Receipt from another system`;
                } else if (data.message.includes('claimed')) {
                    errorMessage = `⚠️ Receipt "${code}" has already been claimed`;
                } else if (data.message.includes('expired')) {
                    errorMessage = `⏰ Receipt "${code}" has expired`;
                } else {
                    errorMessage = data.message;
                }
            }
            
            showError(errorMessage);
            closeModal();
            resumeScanner();
        }
    })
    .catch(error => {
        console.error('Check receipt error:', error);
        closeModal();
        
        let errorMessage = '';
        
        if (error.message === 'API_NOT_FOUND') {
            errorMessage = '🚧 Receipt scanning feature is not yet implemented.\n\n📧 Please contact support or try again later when seller features are available.';
        } else if (error.message === 'INVALID_CODE') {
            errorMessage = `❌ Invalid receipt code "${code}".\n\nPlease make sure you're scanning a valid receipt QR code from a participating store.`;
        } else if (error.message.includes('HTTP_')) {
            errorMessage = `🌐 Server error occurred. Please try again in a moment.\n\nError: ${error.message}`;
        } else if (error.name === 'TypeError' && error.message.includes('fetch')) {
            errorMessage = '📡 Network connection failed.\n\nPlease check your internet connection and try again.';
        } else {
            errorMessage = `❌ Error checking receipt "${code}".\n\nPlease try again or contact support if the problem persists.`;
        }
        
        showError(errorMessage);
        resumeScanner();
    })
    .finally(() => {
        isProcessing = false;
        setLoadingState(false);
    });
}

function displayReceipt(receipt) {
    // Display store info
    document.getElementById('store-name').textContent = receipt.store_name;
    document.getElementById('store-address').textContent = receipt.store_address || '';
    
    // Display items
    const itemsList = document.getElementById('items-list');
    itemsList.innerHTML = '';
    
    if (receipt.items && receipt.items.length > 0) {
        receipt.items.forEach(item => {
            const itemDiv = document.createElement('div');
            itemDiv.className = 'receipt-item';
            itemDiv.innerHTML = `
                <div class="item-details">
                    <div class="item-name">${escapeHtml(item.name)}</div>
                    <div class="item-qty">Quantity: ${item.quantity}</div>
                </div>
                <div class="item-points">+${item.total_points}</div>
            `;
            itemsList.appendChild(itemDiv);
        });
    } else {
        itemsList.innerHTML = '<div class="receipt-item"><div class="item-details"><div class="item-name">No items found</div></div></div>';
    }
    
    // Display total points
    document.getElementById('total-points').textContent = receipt.total_points || 0;
    
    // Configure claim button
    const claimButton = document.getElementById('claim-button');
    const btnText = claimButton.querySelector('.btn-text');
    
    if (receipt.status === 'pending') {
        claimButton.disabled = false;
        btnText.textContent = 'Claim Points';
        claimButton.classList.remove('claimed', 'expired');
    } else if (receipt.status === 'claimed') {
        claimButton.disabled = true;
        btnText.textContent = 'Already Claimed';
        claimButton.classList.add('claimed');
    } else {
        claimButton.disabled = true;
        btnText.textContent = 'Receipt Expired';
        claimButton.classList.add('expired');
    }
}

function claimPoints() {
    if (!currentReceiptCode || isProcessing) return;
    
    // Handle demo mode
    if (currentReceiptCode === 'DEMO123') {
        claimDemoPoints();
        return;
    }
    
    isProcessing = true;
    const claimButton = document.getElementById('claim-button');
    const btnText = claimButton.querySelector('.btn-text');
    const btnLoading = claimButton.querySelector('.btn-loading');
    
    // Show loading state
    claimButton.disabled = true;
    btnText.style.display = 'none';
    btnLoading.style.display = 'flex';
    
    fetch('/api/receipt/claim', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ receipt_code: currentReceiptCode })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            closeModal();
            showSuccess(data.points_earned);
            updatePointsDisplay(data.new_balance);
        } else {
            showError(data.message || 'Failed to claim points');
            // Reset button state
            claimButton.disabled = false;
            btnText.style.display = 'inline';
            btnLoading.style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Claim points error:', error);
        if (error.message.includes('404')) {
            showError('Point claiming not yet implemented. Please wait for seller features.');
        } else {
            showError('Network error. Please try again.');
        }
        // Reset button state
        claimButton.disabled = false;
        btnText.style.display = 'inline';
        btnLoading.style.display = 'none';
    })
    .finally(() => {
        isProcessing = false;
    });
}

function showModal() {
    document.getElementById('receipt-modal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('receipt-modal').style.display = 'none';
    document.body.style.overflow = 'auto';
    document.getElementById('receipt-code').value = '';
    resumeScanner();
}

function resumeScanner() {
    if (scanner && cameraInitialized && !isProcessing) {
        setTimeout(() => {
            try {
                scanner.resume();
                console.log('Scanner resumed');
            } catch (error) {
                console.log('Scanner resume failed:', error);
            }
        }, 500);
    }
}

function showSuccess(points) {
    document.getElementById('points-amount').textContent = points;
    document.getElementById('success-overlay').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function showError(message) {
    const toast = document.getElementById('error-toast');
    const messageEl = document.getElementById('error-message');
    
    const formattedMessage = message.replace(/\n/g, '<br>');
    messageEl.innerHTML = formattedMessage;
    
    toast.classList.add('show');
    
    const hideDelay = message.length > 100 ? 6000 : 4000;
    setTimeout(() => {
        toast.classList.remove('show');
    }, hideDelay);
}

function updatePointsDisplay(newBalance) {
    const pointsEl = document.getElementById('current-points');
    if (pointsEl && newBalance !== undefined) {
        pointsEl.textContent = newBalance;
    }
}

function setLoadingState(loading) {
    const claimButton = document.getElementById('claim-button');
    claimButton.disabled = loading;
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

function showDemoReceipt() {
    const demoReceipt = {
        receipt_code: 'DEMO123',
        store_name: 'Demo Coffee Shop',
        store_address: '123 Main Street, Phnom Penh',
        status: 'pending',
        total_points: 5,
        total_quantity: 3,
        items: [
            { name: 'Coffee', quantity: 2, points_per_unit: 1, total_points: 2 },
            { name: 'Reusable Cup', quantity: 1, points_per_unit: 3, total_points: 3 }
        ],
        created_at: 'Dec 15, 2024 2:30 PM'
    };
    
    currentReceiptCode = 'DEMO123';
    showModal();
    setLoadingState(false);
    displayReceipt(demoReceipt);
}

function claimDemoPoints() {
    closeModal();
    showSuccess(5);
    
    const currentPoints = parseInt(document.getElementById('current-points').textContent) || 0;
    updatePointsDisplay(currentPoints + 5);
}

// FIXED: Added missing functions (dummy implementations)
function requestWakeLock() {
    // Optional: implement wake lock for mobile devices
    console.log('Wake lock requested');
}

function releaseWakeLock() {
    // Optional: release wake lock
    console.log('Wake lock released');
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (scanner) {
        try {
            scanner.clear();
        } catch (error) {
            console.log('Scanner cleanup failed:', error);
        }
    }
});
</script>
@endsection