@extends('master')

@section('content')
    <div class="container page-content">
        <!-- Header with back button -->
        {{-- <div class="account-header">
            <div class="header-nav">
                <a href="{{ route('dashboard') }}" class="back-btn">
                    <span>←</span>
                </a>
                <h2>Account</h2>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn" title="Logout">
                        <span>🚪</span>
                    </button>
                </form>
            </div>
        </div> --}}

        <!-- Profile Section -->
        <div class="profile-section">
            <div class="profile-card">
                <div class="profile-avatar">
                    <div class="avatar-circle">
                        <span class="avatar-text">{{ substr($consumer->name ?? 'U', 0, 1) }}</span>
                    </div>
                </div>
                <div class="profile-info">
                    <h3 class="profile-name">{{ $consumer->name ?? 'Consumer' }}</h3>
                    <p class="profile-email">{{ $consumer->email ?? '' }}</p>
                    <!-- Debug: Check consumer data -->
                    @if(config('app.debug'))
                        <small style="color: #999; font-size: 10px;">
                            Debug: Consumer ID: {{ $consumer->id ?? 'N/A' }}, Name: "{{ $consumer->name ?? 'NULL' }}", Email: "{{ $consumer->email ?? 'NULL' }}"
                        </small>
                    @endif
                    <div class="profile-stats">
                        <span class="stat-item">
                            <span class="stat-icon">📅</span>
                            Member since {{ $consumer->created_at ? $consumer->created_at->format('M Y') : 'Unknown' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="flash-message success" id="flashMessage">
                <div class="flash-icon">✅</div>
                <div class="flash-text">{{ session('success') }}</div>
                <button class="flash-close" onclick="closeFlashMessage()">×</button>
            </div>
        @endif

        @if(session('error'))
            <div class="flash-message error" id="flashMessage">
                <div class="flash-icon">❌</div>
                <div class="flash-text">{{ session('error') }}</div>
                <button class="flash-close" onclick="closeFlashMessage()">×</button>
            </div>
        @endif

        @if($errors->any())
            <div class="flash-message error" id="flashMessage">
                <div class="flash-icon">❌</div>
                <div class="flash-text">
                    @foreach($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
                <button class="flash-close" onclick="closeFlashMessage()">×</button>
            </div>
        @endif

        <!-- Account Settings -->
        <div class="settings-section">
            <h3 class="section-title">Account Settings</h3>

            <div class="settings-list">
                <a href="{{ route('account.edit') }}" class="setting-item">
                    <div class="setting-icon">👤</div>
                    <div class="setting-content">
                        <div class="setting-title">Edit Profile</div>
                        <div class="setting-subtitle">Update your name and personal information</div>
                    </div>
                    <div class="setting-arrow">›</div>
                </a>

                <div class="setting-item change-password-btn" onclick="showPasswordChangeModal()" style="cursor: pointer;">
                    <div class="setting-icon">🔒</div>
                    <div class="setting-content">
                        <div class="setting-title">Change Password</div>
                        <div class="setting-subtitle">Update your account password</div>
                    </div>
                    <div class="setting-arrow">›</div>
                </div>

                <a href="{{ route('account.transactions') }}" class="setting-item">
                    <div class="setting-icon">📊</div>
                    <div class="setting-content">
                        <div class="setting-title">Transaction History</div>
                        <div class="setting-subtitle">View all your point transactions</div>
                    </div>
                    <div class="setting-arrow">›</div>
                </a>
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
            <div class="section-header">
                <h3 class="section-title">Recent Transactions</h3>
                <a href="{{ route('account.transactions') }}" class="see-all-link">See All</a>
            </div>

            @forelse($transactions->take(5) as $transaction)
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
                        <div class="view-indicator">›</div>
                    </div>
                </div>
            @empty
                <div class="no-transactions-card">
                    <div class="empty-state">
                        <div class="empty-icon">📊</div>
                        <div class="empty-title">No transactions yet</div>
                        <div class="empty-subtitle">Start scanning QR codes to earn points!</div>
                        <div style="margin-top: 20px;">
                            <a href="{{ route('dashboard') }}" class="empty-link">
                                Go to Dashboard →
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse

            @if($transactions->count() > 5)
                <div class="transaction-footer">
                    <small>Showing 5 of {{ $transactions->count() }} transactions</small>
                </div>
            @endif
        </div>
    </div>

    <!-- Password Change Modal -->
    <div id="passwordModal" class="password-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0); z-index: 10000; transition: all 0.3s ease; opacity: 0;">
        <div style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; padding: 20px;">
            <div id="modalContent" style="background: white; border-radius: 16px; max-width: 400px; width: 100%; position: relative; transform: translateY(20px); transition: all 0.3s ease; opacity: 0;">
                <div style="background: #1a1a1a; color: white; padding: 20px; border-radius: 16px 16px 0 0; display: flex; align-items: center; justify-content: space-between;">
                    <h3 style="margin: 0; font-size: 18px; font-weight: 600;">Change Password</h3>
                    <button onclick="closePasswordModal()" style="background: rgba(255,255,255,0.1); border: none; color: white; font-size: 24px; cursor: pointer; padding: 4px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.2s ease;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">×</button>
                </div>

                <form id="passwordForm" action="{{ route('account.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div style="padding: 20px;">
                        <div style="margin-bottom: 16px;">
                            <label for="current_password" style="display: block; font-size: 14px; font-weight: 600; color: #1a1a1a; margin-bottom: 6px;">Current Password</label>
                            <input type="password" id="current_password" name="current_password" required style="width: 100%; padding: 10px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: white; transition: border-color 0.2s ease; box-sizing: border-box;" onfocus="this.style.borderColor='#1a1a1a'" onblur="this.style.borderColor='#e9ecef'">
                        </div>

                        <div style="margin-bottom: 16px;">
                            <label for="password" style="display: block; font-size: 14px; font-weight: 600; color: #1a1a1a; margin-bottom: 6px;">New Password</label>
                            <input type="password" id="password" name="password" required minlength="8" style="width: 100%; padding: 10px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: white; transition: border-color 0.2s ease; box-sizing: border-box;" onfocus="this.style.borderColor='#1a1a1a'" onblur="this.style.borderColor='#e9ecef'">
                        </div>

                        <div style="margin-bottom: 24px;">
                            <label for="password_confirmation" style="display: block; font-size: 14px; font-weight: 600; color: #1a1a1a; margin-bottom: 6px;">Confirm New Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required style="width: 100%; padding: 10px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: white; transition: border-color 0.2s ease; box-sizing: border-box;" onfocus="this.style.borderColor='#1a1a1a'" onblur="this.style.borderColor='#e9ecef'">
                        </div>
                    </div>

                    <div style="padding: 20px; display: flex; gap: 12px; justify-content: flex-end;">
                        <button type="button" onclick="closePasswordModal()" style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s ease;" onmouseover="this.style.background='#5a6268'" onmouseout="this.style.background='#6c757d'">Cancel</button>
                        <button type="submit" style="background: #1a1a1a; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s ease;" onmouseover="this.style.background='#333'" onmouseout="this.style.background='#1a1a1a'">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Transaction Detail Modal -->
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
                    <h4 class="detail-header">QR Code Information</h4>

                    <div class="detail-row">
                        <span class="detail-label">QR Code</span>
                        <span class="detail-value qr-code" id="modalQrCode">-</span>
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Container */
        .container {
            background: #ffffff;
            min-height: calc(100vh - var(--navbar-height));
            position: relative;
            max-width: 100%;
            width: 100%;
            padding-top: 0;
        }

        @media (min-width: 768px) {
            .container {
                border-radius: 16px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
                margin: 1rem;
                min-height: calc(100vh - var(--navbar-height) - 2rem);
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
            -webkit-user-select: none;
            user-select: none;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .logout-btn:hover {
            background: rgba(255, 77, 77, 0.8);
            transform: scale(1.05);
        }

        .back-btn:active {
            transform: scale(0.95);
            background: rgba(255, 255, 255, 0.2);
        }

        .header-nav h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            letter-spacing: -0.5px;
        }

        .placeholder-btn {
            width: 40px;
            height: 40px;
        }

        /* Points Summary */
        .points-summary-section {
            background: #f8f8f8;
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
        }

        .points-circle {
            margin: 0 auto 24px;
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #1a1a1a;
        }

        .circle-content {
            text-align: center;
        }

        .circle-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .total-points {
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 4px;
        }

        .points-subtitle {
            font-size: 12px;
            color: #999;
        }

        .points-breakdown {
            display: flex;
            justify-content: center;
            gap: 40px;
        }

        .breakdown-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .breakdown-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 4px;
        }

        .breakdown-value {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
        }

        /* Profile Section */
        .profile-section {
            padding: 30px 20px 20px 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            margin-top: 0;
        }

        .profile-card {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .profile-avatar {
            flex-shrink: 0;
        }

        .avatar-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1a1a1a, #333);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .avatar-text {
            color: white;
            font-size: 24px;
            font-weight: 700;
        }

        .profile-info {
            flex: 1;
        }

        .profile-name {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 4px;
        }

        .profile-email {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
        }

        .profile-stats {
            display: flex;
            gap: 16px;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            color: #888;
        }

        .stat-icon {
            font-size: 14px;
        }

        /* Flash Messages */
        .flash-message {
            margin: 20px;
            padding: 16px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: fadeInDown 0.3s ease;
        }

        .flash-message.success {
            background: #d1fae5;
            border: 1px solid #10b981;
            color: #065f46;
        }

        .flash-message.error {
            background: #fee2e2;
            border: 1px solid #ef4444;
            color: #991b1b;
        }

        .flash-icon {
            font-size: 18px;
            flex-shrink: 0;
        }

        .flash-text {
            flex: 1;
            font-size: 14px;
            font-weight: 500;
        }

        .flash-close {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: inherit;
            opacity: 0.7;
            transition: opacity 0.2s ease;
        }

        .flash-close:hover {
            opacity: 1;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }

        /* Settings Section */
        .settings-section {
            padding: 20px;
        }

        .settings-list {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #f0f0f0;
        }

        .setting-item {
            display: flex;
            align-items: center;
            padding: 16px;
            text-decoration: none;
            color: inherit;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .setting-item:last-child {
            border-bottom: none;
        }

        .setting-item:hover {
            background: #f8f9fa;
        }

        .setting-item:active {
            background: #f0f0f0;
        }

        .setting-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #1a1a1a, #333);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-right: 12px;
        }

        .setting-content {
            flex: 1;
        }

        .setting-title {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 2px;
        }

        .setting-subtitle {
            font-size: 13px;
            color: #666;
        }

        .setting-arrow {
            font-size: 18px;
            color: #ccc;
        }

        /* Section Header */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .see-all-link {
            color: #1a1a1a;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .see-all-link:hover {
            text-decoration: underline;
        }

        /* Accounts Section */
        .accounts-section {
            padding: 20px;
            min-height: 400px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 16px;
        }

        .account-card {
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
            -webkit-user-select: none;
            user-select: none;
        }

        .account-card:active {
            transform: scale(0.98);
            background: #f8f8f8;
        }

        @media (hover: hover) {
            .account-card:hover {
                border-color: #1a1a1a;
                transform: translateY(-2px);
            }
        }

        .account-info {
            display: flex;
            align-items: center;
            flex: 1;
            min-width: 0;
        }

        .account-details {
            flex: 1;
            min-width: 0;
            margin-right: 16px;
        }

        .account-title {
            font-size: 15px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 2px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .account-subtitle {
            font-size: 13px;
            color: #666;
            margin-bottom: 2px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .account-date {
            font-size: 12px;
            color: #999;
        }

        .account-amount {
            text-align: right;
            margin-right: 12px;
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
            font-size: 11px;
            color: #999;
            font-weight: 500;
        }

        .view-indicator {
            font-size: 24px;
            color: #ccc;
            font-weight: 300;
        }

        /* Empty State */
        .no-transactions-card {
            background: #f8f8f8;
            border-radius: 12px;
            padding: 60px 20px;
            text-align: center;
            margin: 20px 0;
        }

        .empty-icon {
            font-size: 48px;
            margin-bottom: 16px;
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
            margin-bottom: 20px;
        }

        .empty-link {
            color: #1a1a1a;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            padding: 10px 20px;
            background: #e0e0e0;
            border-radius: 8px;
            display: inline-block;
            transition: all 0.2s ease;
        }

        .empty-link:active {
            transform: scale(0.95);
            background: #d0d0d0;
        }

        .transaction-footer {
            text-align: center;
            padding: 20px;
            color: #999;
            font-size: 13px;
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
            background: rgba(0, 0, 0, 0.6);
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            width: 100%;
            max-width: 500px;
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

        .modal-close:active {
            background: rgba(255, 255, 255, 0.1);
            transform: scale(0.9);
        }

        .modal-body {
            padding: 0;
        }

        .transaction-status {
            padding: 24px;
            text-align: center;
            background: #f0f9ff;
            border-bottom: 1px solid #e0e0e0;
        }

        .status-icon {
            font-size: 32px;
            margin-bottom: 8px;
        }

        .status-text {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .points-display {
            padding: 24px;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
        }

        .points-amount {
            font-size: 36px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 4px;
        }

        .points-amount[id="modalPointsAmount"]:first-child {
            color: #22c55e;
        }

        .detail-section {
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .detail-section:last-child {
            border-bottom: none;
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

        .btn-share:active {
            transform: scale(0.95);
            background: #333;
        }

        /* Password Modal */
        .password-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            padding: 20px;
        }

        .password-modal .modal-content {
            background: white;
            border-radius: 16px;
            max-width: 400px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            animation: modalSlideUp 0.3s ease;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #f0f0f0;
            border-radius: 8px;
            font-size: 16px;
            background: #fff;
            transition: all 0.2s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #1a1a1a;
            background: #f8f9fa;
        }

        .password-modal .modal-actions {
            padding: 20px;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .btn-cancel, .btn-save {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            min-width: 100px;
        }

        .btn-cancel {
            background: #f0f0f0;
            color: #666;
        }

        .btn-cancel:hover {
            background: #e0e0e0;
        }

        .btn-save {
            background: #1a1a1a;
            color: white;
        }

        .btn-save:hover {
            background: #333;
        }

        .btn-cancel:active, .btn-save:active {
            transform: scale(0.95);
        }

        /* Mobile Responsive */
        @media (max-width: 480px) {
            .points-circle {
                width: 120px;
                height: 120px;
            }

            .total-points {
                font-size: 28px;
            }

            .points-breakdown {
                gap: 24px;
            }

            .account-card {
                padding: 14px;
            }

            .account-title {
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

        /* Touch Optimizations */
        @media (hover: none) and (pointer: coarse) {
            .back-btn,
            .account-card,
            .btn-share,
            .modal-close,
            .empty-link {
                -webkit-tap-highlight-color: rgba(0, 0, 0, 0.1);
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

            let qrCode = transaction.qr_code || transaction.code || 'N/A';
            if (qrCode === 'N/A') {
                qrCode = 'QR-TXN-' + (transaction.id || 'X');
            }
            document.getElementById('modalQrCode').textContent = qrCode;

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
            const modal = document.getElementById('passwordModal');
            const modalContent = document.getElementById('modalContent');

            if (modal && modalContent) {
                // Show modal
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';

                // Clear form
                const form = document.getElementById('passwordForm');
                if (form) {
                    form.reset();
                }

                // Trigger animation
                setTimeout(() => {
                    modal.style.background = 'rgba(0,0,0,0.8)';
                    modal.style.opacity = '1';
                    modalContent.style.transform = 'translateY(0)';
                    modalContent.style.opacity = '1';
                }, 10);
            }
        }

        function closePasswordModal() {
            const modal = document.getElementById('passwordModal');
            const modalContent = document.getElementById('modalContent');

            if (modal && modalContent) {
                // Animate out
                modal.style.background = 'rgba(0,0,0,0)';
                modal.style.opacity = '0';
                modalContent.style.transform = 'translateY(20px)';
                modalContent.style.opacity = '0';

                // Hide after animation
                setTimeout(() => {
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }, 300);
            }
        }

        function closeFlashMessage() {
            const flashMessage = document.getElementById('flashMessage');
            if (flashMessage) {
                flashMessage.style.animation = 'fadeOut 0.3s ease forwards';
                setTimeout(() => {
                    flashMessage.remove();
                }, 300);
            }
        }

        // Auto-hide flash messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const flashMessage = document.getElementById('flashMessage');
            if (flashMessage) {
                setTimeout(() => {
                    closeFlashMessage();
                }, 5000);
            }
        });

        // Handle password form submission
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

        // Touch feedback
        document.addEventListener('DOMContentLoaded', function() {
            const touchElements = document.querySelectorAll('.account-card, .back-btn, .btn-share, .empty-link, .setting-item, .logout-btn');

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
