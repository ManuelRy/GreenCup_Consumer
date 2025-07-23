@extends('master')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <header class="dashboard-header">
        <div class="header-content">
            <h1 class="app-logo">Green Cup</h1>
            <div class="user-info">
                <span class="user-name">{{ $consumer->full_name ?? 'User' }}</span>
                <div class="user-avatar">👤</div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Points Section -->
        <section class="points-section fade-in">
            <div class="points-value">{{ number_format($availablePoints ?? 1247) }}</div>
            <div class="points-label">Available Points</div>
        </section>

        <!-- Analytics Section with Donut Chart -->
        <div class="analytics-container">
            <h2 class="analytics-title">Points Analytics</h2>
            
            <div class="donut-chart-container">
                @php
                    $pointsIn = $monthlyData['points_in'] ?? 0;
                    $pointsOut = $monthlyData['points_out'] ?? 0;
                    $total = $monthlyData['all_activities'] ?? ($pointsIn + $pointsOut);
                    
                    // Calculate percentages
                    if ($total > 0) {
                        $pointsInPercent = ($pointsIn / $total) * 100;
                        $pointsOutPercent = ($pointsOut / $total) * 100;
                    } else {
                        $pointsInPercent = 0;
                        $pointsOutPercent = 0;
                    }
                    
                    // Calculate SVG arc lengths
                    $circumference = 2 * pi() * 40;
                    $pointsInLength = ($pointsInPercent / 100) * $circumference;
                    $pointsOutLength = ($pointsOutPercent / 100) * $circumference;
                @endphp
                
                <svg class="donut-chart" viewBox="0 0 100 100">
                    @if($total > 0)
                        <!-- Background circle -->
                        <circle cx="50" cy="50" r="40" fill="none" stroke="#f0f0f0" stroke-width="12"/>
                        
                        <!-- Points In -->
                        @if($pointsIn > 0)
                            <circle cx="50" cy="50" r="40" fill="none" 
                                    stroke="#22c55e" 
                                    stroke-width="12"
                                    stroke-dasharray="{{ $pointsInLength }} {{ $circumference - $pointsInLength }}"
                                    stroke-dashoffset="0"
                                    transform="rotate(-90 50 50)"/>
                        @endif
                        
                        <!-- Points Out -->
                        @if($pointsOut > 0)
                            <circle cx="50" cy="50" r="40" fill="none" 
                                    stroke="#ef4444" 
                                    stroke-width="12"
                                    stroke-dasharray="{{ $pointsOutLength }} {{ $circumference - $pointsOutLength }}"
                                    stroke-dashoffset="-{{ $pointsInLength }}"
                                    transform="rotate(-90 50 50)"/>
                        @endif
                    @else
                        <!-- Empty state circle -->
                        <circle cx="50" cy="50" r="40" fill="none" stroke="#e0e0e0" stroke-width="12"/>
                    @endif
                </svg>
                
                <div class="donut-center">
                    <div class="donut-center-value">{{ number_format($total) }}</div>
                    <div class="donut-center-label">Total Activity</div>
                </div>
            </div>
            
            <div class="chart-legend" style="display: flex; justify-content: center; align-items: center; gap: 30px; margin-top: 20px;">
                <div class="legend-item" style="display: flex; align-items: center; gap: 8px;">
                    <span class="legend-color" style="background: #22c55e; width: 16px; height: 16px; border-radius: 4px; display: inline-block;"></span>
                    <span class="legend-label" style="font-size: 14px; color: #666; font-weight: 500;">Points Earned</span>
                </div>
                <div class="legend-item" style="display: flex; align-items: center; gap: 8px;">
                    <span class="legend-color" style="background: #ef4444; width: 16px; height: 16px; border-radius: 4px; display: inline-block;"></span>
                    <span class="legend-label" style="font-size: 14px; color: #666; font-weight: 500;">Points Spent</span>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card fade-in">
                <div class="stat-header" style="display: flex; justify-content: space-between; align-items: center; min-height: 24px;">
                    <h3 class="stat-title">Points Earned</h3>
                    <span class="stat-icon" style="font-size: 20px; line-height: 1; height: 24px; display: flex; align-items: center;">📈</span>
                </div>
                <div class="stat-value">{{ number_format($monthlyData['points_in'] ?? 0) }}</div>
                <div class="stat-change">
                    @if(($monthlyData['prev_points_in'] ?? 0) > 0)
                        {{ number_format($monthlyData['prev_points_in']) }} pts in {{ $monthlyData['prev_month_name'] ?? 'last month' }}
                    @else
                        This Month
                    @endif
                </div>
            </div>

            <div class="stat-card fade-in">
                <div class="stat-header" style="display: flex; justify-content: space-between; align-items: center; min-height: 24px;">
                    <h3 class="stat-title">Points Spent</h3>
                    <span class="stat-icon" style="font-size: 20px; line-height: 1; height: 24px; display: flex; align-items: center;">💳</span>
                </div>
                <div class="stat-value">{{ number_format($monthlyData['points_out'] ?? 0) }}</div>
                <div class="stat-change">
                    @if(($monthlyData['prev_points_out'] ?? 0) > 0)
                        {{ number_format($monthlyData['prev_points_out']) }} pts in {{ $monthlyData['prev_month_name'] ?? 'last month' }}
                    @else
                        This Month
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="{{ route('account') }}" class="action-btn">
                <div class="action-icon">👤</div>
                <span class="action-label">Account</span>
            </a>
            
            <a href="{{ route('gallery') }}" class="action-btn">
                <div class="action-icon">🛍️</div>
                <span class="action-label">Products</span>
            </a>
            
            <a href="{{ route('scan.receipt') }}" class="action-btn">
                <div class="action-icon">📱</div>
                <span class="action-label">Scan QR</span>
            </a>
            
            <a href="{{ route('map') }}" class="action-btn">
                <div class="action-icon">📍</div>
                <span class="action-label">Locations</span>
            </a>
        </div>

        <!-- Recent Activity -->
        <section class="activity-section">
            <div class="section-header">
                <h2 class="section-title">Recent Activity</h2>
                <a href="#" class="view-all">View All →</a>
            </div>
            
            <div class="activity-list">
                <div class="activity-item">
                    <div class="activity-info">
                        <div class="activity-icon">☕</div>
                        <div class="activity-details">
                            <div class="activity-name">Coffee Purchase</div>
                            <div class="activity-time">2 hours ago</div>
                        </div>
                    </div>
                    <div class="activity-points positive">+25 pts</div>
                </div>

                <div class="activity-item">
                    <div class="activity-info">
                        <div class="activity-icon">🎁</div>
                        <div class="activity-details">
                            <div class="activity-name">Reward Redeemed</div>
                            <div class="activity-time">Yesterday</div>
                        </div>
                    </div>
                    <div class="activity-points negative">-150 pts</div>
                </div>

                <div class="activity-item">
                    <div class="activity-info">
                        <div class="activity-icon">🌱</div>
                        <div class="activity-details">
                            <div class="activity-name">Eco Action Bonus</div>
                            <div class="activity-time">2 days ago</div>
                        </div>
                    </div>
                    <div class="activity-points positive">+50 pts</div>
                </div>
            </div>
        </section>
    </main>
</div>
@endsection