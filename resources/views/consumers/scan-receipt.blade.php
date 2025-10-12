@extends('master')

@section('content')
<div class="scan-app">
    <!-- Header -->
    <div class="app-header">
        <div class="header-container">
            <a href="{{ route('dashboard') }}" class="back-btn">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="header-info">
                <h1>Scan Receipt</h1>
                <p>Scan QR code to earn points</p>
            </div>
            <div class="points-chip">
                <span id="current-points">{{ Auth::guard('consumer')->user()->getAvailablePoints() ?? 0 }}</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="app-content">
        <!-- Scanner Card -->
        <div class="scanner-card">
            <div class="card-header">
                <div class="scanner-title">
                    <div class="title-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <path d="M21 15l-5-5L5 21"/>
                        </svg>
                    </div>
                    <div>
                        <h2>QR Scanner</h2>
                        <p>Position QR code in the center</p>
                    </div>
                </div>
            </div>

            <div class="scanner-viewport">
                <div id="qr-reader"></div>

                <!-- Scanner Frame -->
                <div class="scanner-frame">
                    <div class="frame-corner tl"></div>
                    <div class="frame-corner tr"></div>
                    <div class="frame-corner bl"></div>
                    <div class="frame-corner br"></div>
                    <div class="scan-beam"></div>
                </div>

                <!-- Camera Status -->
                <div id="camera-status" class="camera-overlay hidden">
                    <div class="status-content">
                        <div class="status-icon">📷</div>
                        <div class="status-text">Starting camera...</div>
                        <div class="status-actions">
                            <button id="retry-camera" class="status-btn primary hidden">Try Again</button>
                            <button id="use-manual" class="status-btn secondary hidden">Manual Input</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="scanner-footer">
                <div class="tip">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                        <point cx="12" cy="17"/>
                    </svg>
                    Hold steady and ensure good lighting
                </div>
            </div>
        </div>

        <!-- Manual Input -->
        <div class="input-section">
            <div class="divider">
                <span>Or enter manually</span>
            </div>

            <form id="manual-code-form" class="manual-form">
                <div class="input-wrapper">
                    <input
                        type="text"
                        id="receipt-code"
                        placeholder="Enter receipt code"
                        class="code-input"
                        autocomplete="off"
                    >
                    <button type="submit" class="submit-button">
                        <span class="btn-content">Verify</span>
                        <div class="btn-loader hidden">
                            <div class="spinner"></div>
                        </div>
                    </button>
                </div>
            </form>
        </div>

        <!-- Recent Activity -->
        <div class="activity-card">
            <div class="card-header">
                <h3>Recent Claims</h3>
            </div>

            <div class="activity-list">
                @php
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
                        $recentClaims = collect([]);
                        \Log::error('Recent claims query failed: ' . $e->getMessage());
                    }
                @endphp

                @forelse($recentClaims as $claim)
                    <div class="activity-item">
                        <div class="activity-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9,22 9,12 15,12 15,22"/>
                            </svg>
                        </div>
                        <div class="activity-details">
                            <div class="store-name">{{ $claim->business_name }}</div>
                            <div class="activity-meta">
                                <span class="points">+{{ $claim->total_points }}</span>
                                <span class="time">{{ \Carbon\Carbon::parse($claim->claimed_at)->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <path d="M21 15l-5-5L5 21"/>
                            </svg>
                        </div>
                        <h4>No claims yet</h4>
                        <p>Start scanning to earn your first points</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Receipt Modal -->
<div id="receipt-modal" class="modal-overlay hidden">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Receipt Preview</h3>
            <button class="close-btn">&times;</button>
        </div>

        <div class="modal-body">
            <div class="store-card">
                <div class="store-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9,22 9,12 15,12 15,22"/>
                    </svg>
                </div>
                <div class="store-info">
                    <h4 id="store-name">Loading...</h4>
                    <p id="store-address"></p>
                </div>
            </div>

            <div class="items-section">
                <h4>Items</h4>
                <div id="items-list" class="items-grid">
                    <!-- Populated by JS -->
                </div>
            </div>

            <div class="total-card">
                <span>Total Points</span>
                <span id="total-points" class="total-amount">0</span>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn secondary" onclick="closeModal()">Cancel</button>
            <button id="claim-button" class="btn primary" onclick="claimPoints()">
                <span class="btn-content">Claim Points</span>
                <div class="btn-loader hidden">
                    <div class="spinner"></div>
                </div>
            </button>
        </div>
    </div>
</div>

<!-- Success Animation -->
<div id="success-overlay" class="success-screen hidden">
    <div class="success-content">
        <div class="success-animation">
            <div class="checkmark">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                    <path d="m9 12 2 2 4-4"/>
                </svg>
            </div>
            <div class="ripple-ring"></div>
            <div class="ripple-ring delay-1"></div>
            <div class="ripple-ring delay-2"></div>
        </div>
        <h2>Success!</h2>
        <p>You earned <strong id="points-amount">0</strong> points</p>
        <div style="display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; margin-top: 24px;">
            <button class="btn primary large" onclick="location.reload()" style="flex: 1; min-width: 140px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;">
                    <path d="M3 7v6h6M21 17v-6h-6M3 13a9 9 0 0 1 15-6.7L21 11M3 13l3 5 3-5"/>
                </svg>
                Scan Again
            </button>
            <button class="btn secondary large" onclick="window.location.href='{{ route('dashboard') }}'" style="flex: 1; min-width: 140px; background: #6b7280; border-color: #6b7280;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Dashboard
            </button>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="error-toast" class="toast hidden">
    <div class="toast-content">
        <div class="toast-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="15" y1="9" x2="9" y2="15"/>
                <line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
        </div>
        <div class="toast-message" id="error-message">Error message</div>
    </div>
</div>

<style>
/* Design System */
:root {
    /* Colors */
    --primary: #1dd1a1;
    --primary-dark: #10ac84;
    --primary-light: #7fffd4;
    --secondary: #6c757d;
    --success: #28a745;
    --danger: #dc3545;
    --warning: #ffc107;
    --info: #17a2b8;

    /* Neutrals */
    --white: #ffffff;
    --gray-50: #f8fafc;
    --gray-100: #f1f5f9;
    --gray-200: #e2e8f0;
    --gray-300: #cbd5e1;
    --gray-400: #94a3b8;
    --gray-500: #64748b;
    --gray-600: #475569;
    --gray-700: #334155;
    --gray-800: #1e293b;
    --gray-900: #0f172a;

    /* Spacing */
    --space-1: 4px;
    --space-2: 8px;
    --space-3: 12px;
    --space-4: 16px;
    --space-5: 20px;
    --space-6: 24px;
    --space-8: 32px;
    --space-10: 40px;
    --space-12: 48px;
    --space-16: 64px;

    /* Border Radius */
    --radius-sm: 6px;
    --radius: 8px;
    --radius-lg: 12px;
    --radius-xl: 16px;
    --radius-2xl: 24px;

    /* Shadows */
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);

    /* Typography */
    --font-sans: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
    --font-mono: 'SF Mono', Monaco, 'Cascadia Code', 'Roboto Mono', Consolas, 'Courier New', monospace;
}

/* Reset & Base */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: var(--font-sans);
    line-height: 1.5;
    color: var(--gray-800);
    background: var(--gray-50);
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* App Layout */
.scan-app {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    width: 100%;
    overflow-x: hidden;
}

/* Header */
.app-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: var(--white);
    padding: var(--space-6) var(--space-4);
    position: relative;
    width: 100%;
    z-index: 100;
    box-shadow: var(--shadow-md);
}

.header-container {
    max-width: 480px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    gap: var(--space-4);
}

.back-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: var(--radius-lg);
    color: var(--white);
    text-decoration: none;
    transition: all 0.2s ease;
    backdrop-filter: blur(10px);
}

.back-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateX(-2px);
    color: var(--white);
}

.header-info {
    flex: 1;
    text-align: center;
}

.header-info h1 {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 2px;
}

.header-info p {
    font-size: 14px;
    opacity: 0.9;
}

.points-chip {
    background: rgba(255, 255, 255, 0.15);
    border-radius: var(--radius-xl);
    padding: var(--space-2) var(--space-4);
    font-size: 16px;
    font-weight: 700;
    backdrop-filter: blur(10px);
    min-width: 60px;
    text-align: center;
}

/* Content */
.app-content {
    flex: 1;
    max-width: 480px;
    margin: 0 auto;
    padding: var(--space-6) var(--space-4);
    width: 100%;
}

/* Scanner Card */
.scanner-card {
    background: var(--white);
    border-radius: var(--radius-2xl);
    box-shadow: var(--shadow-lg);
    margin-bottom: var(--space-8);
    overflow: hidden;
}

.card-header {
    padding: var(--space-6);
    border-bottom: 1px solid var(--gray-100);
}

.scanner-title {
    display: flex;
    align-items: center;
    gap: var(--space-4);
}

.title-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
}

.scanner-title h2 {
    font-size: 20px;
    font-weight: 600;
    color: var(--gray-800);
    margin-bottom: 2px;
}

.scanner-title p {
    font-size: 14px;
    color: var(--gray-500);
}

/* Scanner Viewport */
.scanner-viewport {
    position: relative;
    height: 320px;
    background: var(--gray-900);
    overflow: hidden;
}

#qr-reader {
    width: 100%;
    height: 100%;
}

#qr-reader video {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
}

#qr-reader__dashboard_section_swaplink,
#qr-reader__dashboard_section_csr {
    display: none !important;
}

/* Scanner Frame */
.scanner-frame {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 200px;
    height: 200px;
    pointer-events: none;
    z-index: 10;
}

.frame-corner {
    position: absolute;
    width: 24px;
    height: 24px;
    border: 3px solid var(--primary);
}

.frame-corner.tl {
    top: 0;
    left: 0;
    border-right: none;
    border-bottom: none;
    border-radius: var(--radius-sm) 0 0 0;
}

.frame-corner.tr {
    top: 0;
    right: 0;
    border-left: none;
    border-bottom: none;
    border-radius: 0 var(--radius-sm) 0 0;
}

.frame-corner.bl {
    bottom: 0;
    left: 0;
    border-right: none;
    border-top: none;
    border-radius: 0 0 0 var(--radius-sm);
}

.frame-corner.br {
    bottom: 0;
    right: 0;
    border-left: none;
    border-top: none;
    border-radius: 0 0 var(--radius-sm) 0;
}

.scan-beam {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--primary), transparent);
    animation: scanBeam 2s ease-in-out infinite;
    opacity: 0.8;
}

@keyframes scanBeam {
    0%, 100% {
        transform: translateY(0);
        opacity: 0.8;
    }
    50% {
        transform: translateY(196px);
        opacity: 1;
    }
}

/* Camera Status */
.camera-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(4px);
    z-index: 20;
}

.camera-overlay.hidden {
    display: none;
}

.status-content {
    text-align: center;
    color: var(--white);
    max-width: 280px;
    padding: var(--space-6);
}

.status-icon {
    font-size: 48px;
    margin-bottom: var(--space-4);
    opacity: 0.8;
}

.status-text {
    font-size: 16px;
    margin-bottom: var(--space-6);
    line-height: 1.5;
}

.status-actions {
    display: flex;
    flex-direction: column;
    gap: var(--space-3);
}

.status-btn {
    padding: var(--space-3) var(--space-6);
    border: none;
    border-radius: var(--radius-lg);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.status-btn.primary {
    background: var(--primary);
    color: var(--white);
}

.status-btn.primary:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

.status-btn.secondary {
    background: rgba(255, 255, 255, 0.1);
    color: var(--white);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.status-btn.secondary:hover {
    background: rgba(255, 255, 255, 0.2);
}

.status-btn.hidden {
    display: none;
}

/* Scanner Footer */
.scanner-footer {
    padding: var(--space-4) var(--space-6);
    background: var(--gray-50);
    border-top: 1px solid var(--gray-100);
}

.tip {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    font-size: 14px;
    color: var(--gray-600);
    justify-content: center;
}

/* Manual Input */
.input-section {
    margin-bottom: var(--space-8);
}

.divider {
    display: flex;
    align-items: center;
    margin-bottom: var(--space-6);
    font-size: 14px;
    color: var(--gray-500);
}

.divider::before,
.divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--gray-200);
}

.divider span {
    margin: 0 var(--space-4);
    background: var(--gray-50);
    padding: 0 var(--space-2);
}

.manual-form {
    background: var(--white);
    border-radius: var(--radius-xl);
    padding: var(--space-6);
    box-shadow: var(--shadow);
}

.input-wrapper {
    display: flex;
    gap: var(--space-3);
}

.code-input {
    flex: 1;
    padding: var(--space-4);
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-lg);
    font-size: 16px;
    font-family: var(--font-mono);
    transition: all 0.2s ease;
    background: var(--gray-50);
}

.code-input:focus {
    outline: none;
    border-color: var(--primary);
    background: var(--white);
    box-shadow: 0 0 0 3px rgba(29, 209, 161, 0.1);
}

.submit-button {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: var(--white);
    border: none;
    border-radius: var(--radius-lg);
    padding: var(--space-4) var(--space-6);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    min-width: 100px;
}

.submit-button:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.submit-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

/* Activity Card */
.activity-card {
    background: var(--white);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.activity-card .card-header {
    padding: var(--space-5) var(--space-6);
}

.activity-card h3 {
    font-size: 18px;
    font-weight: 600;
    color: var(--gray-800);
}

.activity-list {
    max-height: 300px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    align-items: center;
    padding: var(--space-4) var(--space-6);
    border-bottom: 1px solid var(--gray-100);
    transition: all 0.2s ease;
}

.activity-item:hover {
    background: var(--gray-50);
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    margin-right: var(--space-4);
}

.activity-details {
    flex: 1;
    min-width: 0;
}

.store-name {
    font-weight: 600;
    color: var(--gray-800);
    margin-bottom: 2px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.activity-meta {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    font-size: 14px;
}

.points {
    color: var(--success);
    font-weight: 600;
    background: rgba(40, 167, 69, 0.1);
    padding: 2px var(--space-2);
    border-radius: var(--radius-sm);
}

.time {
    color: var(--gray-500);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: var(--space-12) var(--space-6);
}

.empty-icon {
    margin: 0 auto var(--space-6);
    width: 64px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--gray-100);
    border-radius: 50%;
    color: var(--gray-400);
}

.empty-state h4 {
    font-size: 18px;
    font-weight: 600;
    color: var(--gray-800);
    margin-bottom: var(--space-2);
}

.empty-state p {
    color: var(--gray-500);
}

/* Modal */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--space-4);
    z-index: 1000;
    backdrop-filter: blur(4px);
}

.modal-overlay.hidden {
    display: none;
}

.modal-container {
    background: var(--white);
    border-radius: var(--radius-2xl);
    width: 100%;
    max-width: 400px;
    max-height: 80vh;
    overflow: hidden;
    box-shadow: var(--shadow-xl);
    animation: modalSlide 0.3s ease-out;
}

@keyframes modalSlide {
    from {
        opacity: 0;
        transform: translateY(20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--space-6);
    border-bottom: 1px solid var(--gray-100);
}

.modal-header h3 {
    font-size: 18px;
    font-weight: 600;
    color: var(--gray-800);
}

.close-btn {
    width: 32px;
    height: 32px;
    border: none;
    background: var(--gray-100);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: var(--gray-500);
    cursor: pointer;
    transition: all 0.2s ease;
}

.close-btn:hover {
    background: var(--gray-200);
    color: var(--gray-700);
}

.modal-body {
    padding: var(--space-6);
    max-height: 50vh;
    overflow-y: auto;
}

/* Store Card */
.store-card {
    display: flex;
    align-items: center;
    gap: var(--space-4);
    padding: var(--space-4);
    background: var(--gray-50);
    border-radius: var(--radius-lg);
    margin-bottom: var(--space-6);
}

.store-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
}

.store-info h4 {
    font-size: 16px;
    font-weight: 600;
    color: var(--gray-800);
    margin-bottom: 2px;
}

.store-info p {
    font-size: 14px;
    color: var(--gray-500);
}

/* Items Section */
.items-section {
    margin-bottom: var(--space-6);
}

.items-section h4 {
    font-size: 16px;
    font-weight: 600;
    color: var(--gray-800);
    margin-bottom: var(--space-4);
}

.items-grid {
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    overflow: hidden;
}

.receipt-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--space-4);
    border-bottom: 1px solid var(--gray-200);
    background: var(--white);
    transition: background 0.2s ease;
}

.receipt-item:hover {
    background: var(--gray-50);
}

.receipt-item:last-child {
    border-bottom: none;
}

.item-details .item-name {
    font-weight: 600;
    color: var(--gray-800);
    margin-bottom: 2px;
}

.item-details .item-qty {
    font-size: 14px;
    color: var(--gray-500);
}

.item-points {
    font-weight: 700;
    color: var(--primary);
    font-size: 16px;
}

/* Total Card */
.total-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--space-5);
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: var(--white);
    border-radius: var(--radius-lg);
    font-weight: 600;
}

.total-amount {
    font-size: 24px;
    font-weight: 800;
}

/* Modal Footer */
.modal-footer {
    display: flex;
    gap: var(--space-3);
    padding: var(--space-6);
    border-top: 1px solid var(--gray-100);
    background: var(--gray-50);
}

/* Buttons */
.btn {
    border: none;
    border-radius: var(--radius-lg);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-2);
    position: relative;
    font-family: inherit;
    font-size: 14px;
    padding: var(--space-3) var(--space-6);
    min-height: 44px; /* Prevent height collapse on mobile */
}

.btn.secondary {
    background: var(--gray-200);
    color: var(--gray-700);
    flex: 1;
}

.btn.secondary:hover {
    background: var(--gray-300);
}

.btn.primary {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: var(--white);
    flex: 2;
}

.btn.primary:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: var(--shadow-lg);
}

.btn.large {
    padding: var(--space-4) var(--space-8);
    font-size: 16px;
    min-height: 52px;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.btn-content {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    white-space: nowrap; /* Prevent text wrapping */
    transition: opacity 0.2s ease, visibility 0.2s ease;
}

.btn-loader {
    position: absolute;
    display: flex;
    align-items: center;
    gap: var(--space-2);
}

.btn-loader.hidden {
    display: none;
}

/* Spinner */
.spinner {
    width: 20px; /* Larger for better mobile visibility */
    height: 20px;
    border: 2.5px solid rgba(255, 255, 255, 0.3);
    border-top: 2.5px solid var(--white);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Success Screen */
.success-screen {
    position: fixed;
    inset: 0;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
    backdrop-filter: blur(10px);
}

.success-screen.hidden {
    display: none;
}

.success-content {
    text-align: center;
    color: var(--white);
    padding: var(--space-8);
}

.success-animation {
    position: relative;
    margin: 0 auto var(--space-8);
    width: 120px;
    height: 120px;
}

.checkmark {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80px;
    height: 80px;
    background: var(--white);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    z-index: 3;
    animation: checkmarkBounce 0.6s ease-out;
}

@keyframes checkmarkBounce {
    0% {
        transform: translate(-50%, -50%) scale(0);
        opacity: 0;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.1);
        opacity: 1;
    }
    100% {
        transform: translate(-50%, -50%) scale(1);
    }
}

.ripple-ring {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    animation: ripple 2s ease-out infinite;
}

.ripple-ring {
    width: 80px;
    height: 80px;
}

.ripple-ring.delay-1 {
    width: 100px;
    height: 100px;
    animation-delay: 0.3s;
}

.ripple-ring.delay-2 {
    width: 120px;
    height: 120px;
    animation-delay: 0.6s;
}

@keyframes ripple {
    0% {
        transform: translate(-50%, -50%) scale(0);
        opacity: 1;
    }
    100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0;
    }
}

.success-content h2 {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: var(--space-4);
}

.success-content p {
    font-size: 18px;
    margin-bottom: var(--space-8);
    opacity: 0.95;
}

.success-content strong {
    font-size: 24px;
    font-weight: 800;
}

/* Toast */
.toast {
    position: fixed;
    top: var(--space-6);
    right: var(--space-6);
    background: var(--white);
    border-radius: var(--radius-lg);
    padding: var(--space-4);
    box-shadow: var(--shadow-xl);
    border-left: 4px solid var(--danger);
    max-width: 320px;
    z-index: 3000;
    transform: translateX(400px);
    transition: transform 0.3s ease;
}

.toast.hidden {
    display: none;
}

.toast.show {
    transform: translateX(0);
}

.toast-content {
    display: flex;
    align-items: flex-start;
    gap: var(--space-3);
}

.toast-icon {
    color: var(--danger);
    flex-shrink: 0;
    margin-top: 2px;
}

.toast-message {
    font-weight: 500;
    color: var(--gray-800);
    line-height: 1.4;
}

/* Responsive Design */
@media (max-width: 640px) {
    .app-content {
        padding: var(--space-4);
    }

    .scanner-viewport {
        height: 280px;
    }

    .scan-beam {
        animation: scanBeamMobile 2s ease-in-out infinite;
    }

    @keyframes scanBeamMobile {
        0%, 100% {
            transform: translateY(0);
            opacity: 0.8;
        }
        50% {
            transform: translateY(156px);
            opacity: 1;
        }
    }

    .scanner-frame {
        width: 160px;
        height: 160px;
    }

    .frame-corner {
        width: 20px;
        height: 20px;
        border-width: 2px;
    }

    .input-wrapper {
        flex-direction: column;
        gap: var(--space-4);
    }

    .submit-button {
        width: 100%;
    }

    .code-input {
        font-size: 16px; /* Prevent zoom on iOS */
    }

    .modal-container {
        margin: var(--space-4);
        width: calc(100% - var(--space-8));
    }

    /* Modal footer mobile styles */
    .modal-footer {
        flex-direction: column;
        gap: var(--space-3);
    }

    .modal-footer .btn {
        width: 100%;
        flex: none;
    }

    .toast {
        top: var(--space-4);
        right: var(--space-4);
        left: var(--space-4);
        max-width: none;
        transform: translateY(-100px);
    }

    .toast.show {
        transform: translateY(0);
    }
}

@media (max-width: 480px) {
    .app-header {
        padding: var(--space-4);
    }

    .header-info h1 {
        font-size: 16px;
    }

    .header-info p {
        font-size: 13px;
    }

    .points-chip {
        font-size: 14px;
        min-width: 50px;
    }

    .success-content {
        padding: var(--space-6) var(--space-4);
    }

    .success-content h2 {
        font-size: 24px;
    }

    .success-content p {
        font-size: 16px;
    }

    .success-content strong {
        font-size: 20px;
    }
}

/* Hidden utility */
.hidden {
    display: none !important;
}

/* Smooth transitions */
* {
    transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}
</style>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
// PRESERVE ALL ORIGINAL JAVASCRIPT LOGIC EXACTLY
let scanner = null;
let isProcessing = false;
let currentReceiptCode = null;

function showCameraStatus(message, icon = '📷', showRetry = false, showManual = false) {
    const statusEl = document.getElementById('camera-status');
    const messageEl = statusEl.querySelector('.status-text');
    const iconEl = statusEl.querySelector('.status-icon');
    const retryBtn = document.getElementById('retry-camera');
    const manualBtn = document.getElementById('use-manual');

    messageEl.innerHTML = message.replace(/\n/g, '<br>');
    iconEl.textContent = icon;

    if (showRetry) {
        retryBtn.classList.remove('hidden');
    } else {
        retryBtn.classList.add('hidden');
    }

    if (showManual) {
        manualBtn.classList.remove('hidden');
    } else {
        manualBtn.classList.add('hidden');
    }

    statusEl.classList.remove('hidden');
}

function hideCameraStatus() {
    document.getElementById('camera-status').classList.add('hidden');
}

function isInAppBrowser() {
    return /FBAV|FBAN|FB_IAB|FBOP|Instagram|Line|Messenger/i.test(navigator.userAgent);
}

async function initializeCamera() {
    if (!navigator.mediaDevices?.getUserMedia) {
        showCameraStatus(
            'Camera not supported on this browser.<br>Use Chrome, Safari, or Firefox.<br>Manual input below.',
            '❌', false, true
        );
        return;
    }
    if (isInAppBrowser()) {
        showCameraStatus(
            'Camera does NOT work in Messenger, Facebook, or Instagram app browser.<br>Please tap the (•••) menu and open in Chrome or Safari.',
            '📱', false, true
        );
        return;
    }
    showCameraStatus('Starting camera...', '📷');
    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: { ideal: "environment" }, width: { ideal: 1280 }, height: { ideal: 720 } }
        });
        stream.getTracks().forEach(track => track.stop());
        startScanner();
    } catch (err) {
        if (err.name === "NotAllowedError" || err.name === "PermissionDeniedError") {
            showCameraStatus(
                'Camera permission denied.<br>Tap the camera icon in your browser\'s address bar and select "Allow", then refresh.<br>Or use manual input.',
                '🚫', false, true
            );
        } else if (err.name === "NotFoundError" || err.name === "DevicesNotFoundError") {
            showCameraStatus(
                'No camera found on this device.<br>Please use manual input.',
                '❌', false, true
            );
        } else if (err.message && err.message.includes('Only secure origins are allowed')) {
            showCameraStatus(
                'Browser blocks camera on this address (HTTP/IP).<br>Try using HTTPS if possible, or use manual input.',
                '🔒', false, true
            );
        } else if (err.name === "NotReadableError" || err.name === "TrackStartError") {
            showCameraStatus(
                'Camera is being used by another app.<br>Close other camera apps/tabs and try again.',
                '📷', true, true
            );
        } else {
            showCameraStatus(
                'Failed to access camera.<br>Error: ' + (err.message || err) + '<br>Use manual input below.',
                '❌', false, true
            );
        }
    }
}

function startScanner() {
    if (scanner) {
        try { scanner.clear(); } catch (e) {}
        scanner = null;
    }
    const readerElement = document.getElementById('qr-reader');
    readerElement.innerHTML = "";
    scanner = new Html5Qrcode("qr-reader");
    scanner.start(
        { facingMode: "environment" },
        { fps: 12, qrbox: { width: 200, height: 200 } },
        (decodedText, decodedResult) => { if (!isProcessing) onScanSuccess(decodedText, decodedResult); },
        () => {}
    ).then(hideCameraStatus)
    .catch((err) => {
        showCameraStatus(
            'Camera failed to start.<br>Error: ' + (err.message || err),
            '❌', true, true
        );
    });
}

function resumeScanner() {
    if (scanner && !isProcessing) {
        try { scanner.resume(); }
        catch { setTimeout(() => { initializeCamera(); }, 500); }
    }
}

function retryCamera() {
    if (scanner) { try { scanner.clear(); } catch (e) {} scanner = null; }
    showCameraStatus('Retrying camera...', '🔄');
    setTimeout(() => { initializeCamera(); }, 600);
}

function focusManualInput() {
    hideCameraStatus();
    document.querySelector('.input-section').scrollIntoView({ behavior: 'smooth' });
    setTimeout(() => { document.getElementById('receipt-code').focus(); }, 500);
}

function showModal() {
    document.getElementById('receipt-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('receipt-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('receipt-code').value = '';
    resumeScanner();
}

function setLoadingState(button, loading) {
    const content = button.querySelector('.btn-content');
    const loader = button.querySelector('.btn-loader');

    button.disabled = loading;

    if (loading) {
        content.style.opacity = '0';
        content.style.visibility = 'hidden';
        loader.classList.remove('hidden');
    } else {
        content.style.opacity = '1';
        content.style.visibility = 'visible';
        loader.classList.add('hidden');
    }
}

function showSuccess(points) {
    document.getElementById('points-amount').textContent = points;
    document.getElementById('success-overlay').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function showError(message) {
    const toast = document.getElementById('error-toast');
    const messageEl = document.getElementById('error-message');
    messageEl.innerHTML = message.replace(/\n/g, '<br>');

    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('show'), 10);

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.classList.add('hidden'), 300);
    }, 4000 + Math.min(message.length * 12, 3000));
}

function updatePointsDisplay(newBalance) {
    const pointsEl = document.getElementById('current-points');
    if (pointsEl && newBalance !== undefined) {
        pointsEl.textContent = newBalance;
    }
}

function extractReceiptCode(decodedText) {
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

function isValidReceiptCode(code) {
    if (!code || typeof code !== 'string') return false;
    if (code.length < 3) return false;
    if (code.toLowerCase() === 'demo123') return true;
    return /^[a-zA-Z0-9-_]+$/.test(code);
}

function onScanSuccess(decodedText, decodedResult) {
    if (isProcessing) return;
    let receiptCode = extractReceiptCode(decodedText);
    if (receiptCode.toLowerCase().startsWith('demo')) {
        showDemoReceipt();
        return;
    }
    checkReceipt(receiptCode);
}

function checkReceipt(code) {
    if (isProcessing) return;
    if (!isValidReceiptCode(code)) {
        showError(`Invalid QR code: "${code}". Use a store's official QR code.`);
        resumeScanner();
        return;
    }
    isProcessing = true;
    currentReceiptCode = code;
    showModal();

    const claimButton = document.getElementById('claim-button');
    setLoadingState(claimButton, true);

    document.getElementById('store-name').textContent = 'Verifying receipt code...';
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
            if (response.status === 404) throw new Error('API_NOT_FOUND');
            if (response.status === 422) throw new Error('INVALID_CODE');
            throw new Error(`HTTP_${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            displayReceipt(data.receipt);
        } else {
            let msg = data.message || 'Unknown error occurred';
            if (msg.includes('not found') || msg.includes('invalid')) {
                msg = `Receipt "${code}" not found. Maybe:\n• Expired\n• Invalid\n• From another system`;
            } else if (msg.includes('claimed')) {
                msg = `Receipt "${code}" has already been claimed`;
            } else if (msg.includes('expired')) {
                msg = `Receipt "${code}" has expired`;
            }
            showError(msg);
            closeModal();
            resumeScanner();
        }
    })
    .catch(error => {
        closeModal();
        console.error(error);
        let errorMessage = '';
        if (error.message === 'API_NOT_FOUND') {
            errorMessage = 'Scanning feature not ready. Try later or contact support.';
        } else if (error.message === 'INVALID_CODE') {
            errorMessage = `Invalid receipt code "${code}".`;
        } else if (error.message.includes('HTTP_')) {
            errorMessage = `Server error. Try again.\n${error.message}`;
        } else {
            errorMessage = `Error checking "${code}". Try again.`;
        }
        showError(errorMessage);
        resumeScanner();
    })
    .finally(() => {
        isProcessing = false;
        setLoadingState(document.getElementById('claim-button'), false);
    });
}

function displayReceipt(receipt) {
    document.getElementById('store-name').textContent = receipt.store_name;
    document.getElementById('store-address').textContent = receipt.store_address || '';
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

    document.getElementById('total-points').textContent = receipt.total_points || 0;
    const claimButton = document.getElementById('claim-button');
    const btnContent = claimButton.querySelector('.btn-content');

    if (receipt.status === 'pending') {
        claimButton.disabled = false;
        btnContent.textContent = 'Claim Points';
        claimButton.classList.remove('claimed', 'expired');
    } else if (receipt.status === 'claimed') {
        claimButton.disabled = true;
        btnContent.textContent = 'Already Claimed';
        claimButton.classList.add('claimed');
    } else {
        claimButton.disabled = true;
        btnContent.textContent = 'Receipt Expired';
        claimButton.classList.add('expired');
    }
}

function claimPoints() {
    if (!currentReceiptCode || isProcessing) return;
    if (currentReceiptCode === 'DEMO123') {
        claimDemoPoints();
        return;
    }

    isProcessing = true;
    const claimButton = document.getElementById('claim-button');
    setLoadingState(claimButton, true);

    fetch('/api/receipt/claim', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ receipt_code: currentReceiptCode })
    })
    .then(response => {
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return response.json();
    })
    .then(data => {
        if (data.success) {
            closeModal();
            showSuccess(data.points_earned);
            updatePointsDisplay(data.new_balance);
        } else {
            showError(data.message || 'Failed to claim points');
            setLoadingState(claimButton, false);
        }
    })
    .catch(error => {
        showError('Network error. Please try again.');
        setLoadingState(claimButton, false);
    })
    .finally(() => {
        isProcessing = false;
    });
}

function escapeHtml(text) {
    const map = {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'};
    return text.replace(/[&<>"']/g, m => map[m]);
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
    setLoadingState(document.getElementById('claim-button'), false);
    displayReceipt(demoReceipt);
}

function claimDemoPoints() {
    closeModal();
    showSuccess(5);
    const currentPoints = parseInt(document.getElementById('current-points').textContent) || 0;
    updatePointsDisplay(currentPoints + 5);
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Form submission
    document.getElementById('manual-code-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const code = document.getElementById('receipt-code').value.trim();
        if (code && !isProcessing) {
            const submitButton = this.querySelector('.submit-button');
            setLoadingState(submitButton, true);

            if (code.toLowerCase() === 'demo123') {
                setTimeout(() => {
                    setLoadingState(submitButton, false);
                    showDemoReceipt();
                }, 500);
                return;
            }

            setTimeout(() => {
                setLoadingState(submitButton, false);
                checkReceipt(code);
            }, 500);
        }
    });

    // Camera controls
    document.getElementById('retry-camera').addEventListener('click', retryCamera);
    document.getElementById('use-manual').addEventListener('click', focusManualInput);

    // Modal controls
    document.querySelector('.close-btn').addEventListener('click', closeModal);
    document.getElementById('receipt-modal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });

    // Visibility change handler
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden && !isProcessing) {
            setTimeout(() => { initializeCamera(); }, 1000);
        }
    });

    // Initialize camera
    setTimeout(() => { initializeCamera(); }, 100);
});

// Cleanup
window.addEventListener('beforeunload', function() {
    if (scanner) { try { scanner.clear(); } catch (error) {} }
});
</script>

@endsection
