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
        <div class="header">
            <div class="app-title">Green Cup</div>
            <div class="profile-section">
                <div class="profile-pic"></div>
                <div class="greeting">
                    <h2>Hello, {{ $consumer->full_name ?? 'User' }}!</h2>
                    <p>View Profile ></p>
                </div>
            </div>
        </div>

        <div class="points-card">
            <div class="points-amount">{{ number_format($availablePoints ?? 1247) }}</div>
            <div class="points-label">Available Points</div>
        </div>

        <div class="analytics-section">
            <div class="month-tabs">
                <div class="month-tabs-center">
                    @php
                        $months = ['May', 'June', 'July'];
                        $currentSelectedMonth = $selectedMonth ?? 'July';
                    @endphp
                    @foreach($months as $month)
                        <button class="month-tab {{ $month === $currentSelectedMonth ? 'active' : '' }}"
                            onclick="changeMonth('{{ $month }}')">
                            {{ $month }}
                        </button>
                    @endforeach
                </div>
                <div class="month-dropdown">
                    <button class="month-button" onclick="toggleMonthDropdown()">
                        📅 {{ $currentSelectedMonth }} ⌄
                    </button>
                    <div class="month-options" id="monthDropdown">
                        @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                            <div class="month-option {{ $month === $currentSelectedMonth ? 'selected' : '' }}"
                                onclick="selectMonth('{{ $month }}')">
                                {{ $month }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="spending-chart">
                <div class="chart-circle">
                    <div class="chart-content">
                        <div class="chart-icon">⏱</div>
                        <div class="chart-title">All Activities</div>
                        <div class="chart-amount">{{ number_format($monthlyData['all_activities'] ?? 1172) }} pts</div>
                    </div>
                </div>
                <div class="analytics-badge">📊 Analytics</div>
            </div>

            <div class="flow-cards">
                <div class="flow-card">
                    <div class="flow-header">
                        <div class="flow-title">
                            ✅ Points In
                        </div>
                        <div class="flow-info">⏰</div>
                    </div>
                    <div class="flow-amount">{{ number_format($monthlyData['points_in'] ?? 1237) }} pts</div>
                    <div class="flow-change points-in-change">
                        {{ number_format($monthlyData['prev_points_in'] ?? 2717) }} pts in
                        {{ $monthlyData['prev_month_name'] ?? 'Jun' }}
                    </div>
                </div>

                <div class="flow-card">
                    <div class="flow-header">
                        <div class="flow-title">
                            ↗️ Points Out
                        </div>
                        <div class="flow-info">ℹ️</div>
                    </div>
                    <div class="flow-amount">{{ number_format($monthlyData['points_out'] ?? 1682) }} pts</div>
                    <div class="flow-change points-out-change">
                        {{ number_format($monthlyData['prev_points_out'] ?? 2782) }} pts in
                        {{ $monthlyData['prev_month_name'] ?? 'Jun' }}
                    </div>
                </div>
            </div>

            <div class="cash-flow-section">
                <div class="cash-flow-header">
                    <div class="cash-flow-title">
                        💹 Points Flow
                    </div>
                    <div class="flow-info">ℹ️</div>
                </div>

                <div class="net-flow-label">Net Points Flow</div>
                @php
                    $netFlow = $monthlyData['net_flow'] ?? -444;
                    $pointsIn = $monthlyData['points_in'] ?? 1237;
                    $pointsOut = $monthlyData['points_out'] ?? 1682;
                    $allActivities = $monthlyData['all_activities'] ?? 1172;
                @endphp
                <div class="net-flow-amount">
                    {{ $netFlow >= 0 ? '+' : '' }}{{ number_format($netFlow) }} pts
                </div>

                <div class="flow-bars">
                    <div class="flow-bar-section">
                        <div class="flow-bar-label">Points In</div>
                        <div class="flow-bar points-in-bar"
                            style="width: {{ $allActivities > 0 ? ($pointsIn / ($pointsIn + $pointsOut)) * 100 : 50 }}%">
                        </div>
                        <div class="flow-bar-amount">{{ number_format($pointsIn) }} pts</div>
                    </div>
                    <div class="flow-bar-section">
                        <div class="flow-bar-label">Points Out</div>
                        <div class="flow-bar points-out-bar"
                            style="width: {{ ($pointsIn + $pointsOut) > 0 ? ($pointsOut / ($pointsIn + $pointsOut)) * 100 : 50 }}%">
                        </div>
                        <div class="flow-bar-amount">{{ number_format($pointsOut) }} pts</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="services-grid">
            <a href="{{ route('account') }}" class="service-item">
                <div class="service-icon">👤</div>
                <div class="service-name">Account</div>
            </a>
            <div class="service-item">
                <div class="service-icon">🛍️</div>
                <div class="service-name">Product</div>
            </div>
            <a href="{{ route('scan') }}" class="service-item">
                <div class="service-icon">📱</div>
                <div class="service-name">Scan</div>
            </a>
            <a href="{{ route('map') }}" class="service-item">
                <div class="service-icon">🗺️</div>
                <div class="service-name">Map</div>
            </a>

        </div>



@endsection