<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Green Cup App')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="{{ asset('dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <style>
        :root {
            --primary: #10b981;
            --primary-dark: #047857;
            --primary-light: #a7f3d0;
            --secondary: #06b6d4;
            --accent: #f59e0b;
            --success: #059669;
            --danger: #dc2626;
            --warning: #d97706;
            --dark: #111827;
            --light: #f9fafb;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 50%, #fafafa 100%);
            min-height: 100vh;
            line-height: 1.6;
            color: var(--gray-700);
            font-size: 14px;
        }

        /* Navigation */
        .navbar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%) !important;
            backdrop-filter: blur(20px);
            box-shadow: 0 8px 32px rgba(16, 185, 129, 0.15);
            border: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;
            padding: 1rem 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: white !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .navbar-brand:hover {
            color: var(--accent) !important;
            transform: scale(1.02);
        }

        .navbar-brand i {
            font-size: 1.75rem;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        }

        .navbar-nav .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 600;
            padding: 0.75rem 1.25rem !important;
            border-radius: 12px;
            margin: 0 0.25rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .navbar-nav .nav-link:hover {
            color: white !important;
            background: rgba(255,255,255,0.15);
            transform: translateY(-2px);
        }

        .navbar-nav .nav-link.active {
            color: white !important;
            background: rgba(4, 120, 87, 0.8);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        /* Main Content */
        .main-content {
            margin-top: 90px;
            margin-bottom: 100px;
            min-height: calc(100vh - 190px);
        }

        .container-custom {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Dashboard Specific Styles */
        .dashboard-wrapper {
            background: transparent;
            min-height: calc(100vh - 190px);
        }

        .dashboard-header {
            background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.8) 100%);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.1);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .app-title {
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
            line-height: 1.2;
        }

        .user-info-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: rgba(16, 185, 129, 0.05);
            padding: 1rem 1.5rem;
            border-radius: 50px;
            border: 1px solid rgba(16, 185, 129, 0.1);
            backdrop-filter: blur(10px);
        }

        .user-name {
            font-weight: 600;
            color: var(--primary-dark);
            font-size: 1rem;
        }

        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.25rem;
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);
            border: 3px solid rgba(255,255,255,0.9);
            transition: all 0.3s ease;
        }

        .user-avatar:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 25px rgba(16, 185, 129, 0.4);
        }

        /* Points Section */
        .points-showcase {
            background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(16, 185, 129, 0.02) 100%);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 3rem 2rem;
            text-align: center;
            margin-bottom: 2rem;
            box-shadow: 0 12px 40px rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.1);
            position: relative;
            overflow: hidden;
        }

        .points-showcase::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .points-number {
            font-size: 4.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .points-label {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        /* Analytics Section */
        .analytics-section {
            background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(6, 182, 212, 0.02) 100%);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 12px 40px rgba(6, 182, 212, 0.08);
            border: 1px solid rgba(6, 182, 212, 0.1);
        }

        .analytics-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .donut-chart-container {
            position: relative;
            max-width: 300px;
            margin: 0 auto 2rem;
        }

        .donut-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .donut-center-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-dark);
            line-height: 1;
        }

        .donut-center-label {
            font-size: 0.875rem;
            color: var(--gray-500);
            font-weight: 500;
        }

        /* Stats Grid */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.7) 100%);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 8px 32px rgba(16, 185, 129, 0.06);
            border: 1px solid rgba(16, 185, 129, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 48px rgba(16, 185, 129, 0.12);
        }

        .stat-card:hover::before {
            transform: scaleX(1);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-title {
            font-weight: 600;
            color: var(--gray-600);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-dark);
            line-height: 1;
        }

        /* Mobile Bottom Navigation */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border-top: 1px solid rgba(16, 185, 129, 0.1);
            z-index: 1030;
            padding: 1rem 0;
            box-shadow: 0 -8px 32px rgba(0,0,0,0.08);
        }

        .bottom-nav .nav-link {
            text-align: center;
            padding: 0.75rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--gray-400);
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 16px;
            margin: 0 0.25rem;
        }

        .bottom-nav .nav-link:hover,
        .bottom-nav .nav-link.active {
            color: var(--primary);
            background: rgba(16, 185, 129, 0.08);
            transform: translateY(-4px);
        }

        .bottom-nav .nav-link i {
            font-size: 1.5rem;
            display: block;
            margin-bottom: 0.25rem;
            transition: all 0.3s ease;
        }

        .bottom-nav .nav-link.active i {
            transform: scale(1.1);
            filter: drop-shadow(0 2px 4px rgba(16, 185, 129, 0.3));
        }

        /* Mobile Responsiveness */
        @media (min-width: 768px) {
            .bottom-nav {
                display: none;
            }
            .main-content {
                margin-bottom: 40px;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-top: 80px;
                padding: 0 1rem;
            }

            .dashboard-header {
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .app-title {
                font-size: 2rem;
            }

            .points-number {
                font-size: 3.5rem;
            }

            .stats-container {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }

        @media (max-width: 480px) {
            .dashboard-header {
                padding: 1rem;
            }

            .points-showcase {
                padding: 2rem 1rem;
            }

            .analytics-section {
                padding: 1.5rem;
            }
        }

        /* Dropdown Styles */
        .dropdown-menu {
            border: none;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-radius: 16px;
            padding: 1rem;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(16, 185, 129, 0.1);
        }

        .dropdown-item {
            border-radius: 12px;
            transition: all 0.3s ease;
            padding: 0.75rem 1rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .dropdown-item:hover {
            background: rgba(16, 185, 129, 0.08);
            color: var(--primary-dark);
            transform: translateX(8px);
        }

        .dropdown-item i {
            font-size: 1.125rem;
            color: var(--primary);
        }

        /* Off-canvas Menu */
        .offcanvas {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        }

        .offcanvas-header {
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .offcanvas-title {
            color: white;
        }

        .btn-close {
            filter: invert(1);
        }

        .list-group-item {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.1);
            color: white;
            transition: all 0.3s ease;
        }

        .list-group-item:hover {
            background: rgba(255,255,255,0.2);
            transform: translateX(8px);
        }

        /* Animations */
        .fade-in {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .pulse-green {
            animation: pulseGreen 2s infinite;
        }

        @keyframes pulseGreen {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.02);
            }
        }

        /* Loading and Toast */
        .toast-container {
            position: fixed;
            top: 110px;
            right: 20px;
            z-index: 1055;
        }

        .spinner-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(8px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
            border-width: 0.3rem;
            border-color: var(--primary);
            border-right-color: transparent;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(16, 185, 129, 0.05);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Top Navigation (Desktop) -->
    <nav class="navbar navbar-expand-lg d-none d-md-block">
        <div class="container-fluid">
            <a class="navbar-brand pulse-green" href="{{ route('dashboard') }}">
                <i class="bi bi-cup-hot-fill"></i> Green Cup
            </a>
            
            <div class="navbar-nav me-auto">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a class="nav-link {{ request()->routeIs('map') ? 'active' : '' }}" href="{{ route('map') }}">
                    <i class="bi bi-geo-alt-fill"></i> Store Locator
                </a>
                <a class="nav-link {{ request()->routeIs('gallery') ? 'active' : '' }}" href="{{ route('gallery') }}">
                    <i class="bi bi-images"></i> Gallery
                </a>
                <a class="nav-link {{ request()->routeIs('scan*') ? 'active' : '' }}" href="{{ route('scan.receipt') }}">
                    <i class="bi bi-qr-code-scan"></i> Scan Receipt
                </a>
            </div>
            
            <!-- User Menu -->
            <div class="navbar-nav">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <div class="user-avatar me-2" style="width: 35px; height: 35px; font-size: 1rem;">
                            {{ strtoupper(substr(Auth::guard('consumer')->user()->full_name ?? 'U', 0, 1)) }}
                        </div>
                        <span class="d-none d-lg-inline">{{ Auth::guard('consumer')->user()->full_name ?? 'User' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('account') }}"><i class="bi bi-person"></i> My Account</a></li>
                        <li><a class="dropdown-item" href="{{ route('consumer.qr-code') }}"><i class="bi bi-qr-code"></i> My QR Code</a></li>
                        <li><a class="dropdown-item" href="{{ route('account.transactions') }}"><i class="bi bi-clock-history"></i> Transaction History</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right"></i> Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Mobile Top Bar -->
    <nav class="navbar d-md-none">
        <div class="container-fluid">
            <a class="navbar-brand pulse-green" href="{{ route('dashboard') }}">
                <i class="bi bi-cup-hot-fill"></i> Green Cup
            </a>
            
            <div class="d-flex align-items-center">
                <div class="user-avatar" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" style="cursor: pointer; width: 35px; height: 35px; font-size: 1rem;">
                    {{ strtoupper(substr(Auth::guard('consumer')->user()->full_name ?? 'U', 0, 1)) }}
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Mobile Off-canvas Menu -->
    <div class="offcanvas offcanvas-end d-md-none" tabindex="-1" id="mobileMenu">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <div class="d-flex flex-column">
                <div class="text-center mb-4">
                    <div class="user-avatar mx-auto mb-2" style="width: 60px; height: 60px; font-size: 1.5rem;">
                        {{ strtoupper(substr(Auth::guard('consumer')->user()->full_name ?? 'U', 0, 1)) }}
                    </div>
                    <h6>{{ Auth::guard('consumer')->user()->full_name ?? 'User' }}</h6>
                    <small class="text-muted">{{ Auth::guard('consumer')->user()->email ?? '' }}</small>
                </div>
                
                <div class="list-group list-group-flush">
                    <a href="{{ route('account') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-person me-2"></i> My Account
                    </a>
                    <a href="{{ route('consumer.qr-code') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-qr-code me-2"></i> My QR Code
                    </a>
                    <a href="{{ route('account.transactions') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-clock-history me-2"></i> Transaction History
                    </a>
                </div>
                
                <div class="mt-auto pt-3">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-light w-100">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="container-custom">
            @yield('content')
        </div>
    </main>
    
    <!-- Bottom Navigation (Mobile) -->
    <nav class="bottom-nav d-md-none">
        <div class="container-fluid">
            <div class="row text-center">
                <div class="col">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-house-fill"></i>
                        <span>Home</span>
                    </a>
                </div>
                <div class="col">
                    <a href="{{ route('map') }}" class="nav-link {{ request()->routeIs('map') ? 'active' : '' }}">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>Map</span>
                    </a>
                </div>
                <div class="col">
                    <a href="{{ route('scan.receipt') }}" class="nav-link {{ request()->routeIs('scan*') ? 'active' : '' }}">
                        <i class="bi bi-qr-code-scan"></i>
                        <span>Scan</span>
                    </a>
                </div>
                <div class="col">
                    <a href="{{ route('gallery') }}" class="nav-link {{ request()->routeIs('gallery') ? 'active' : '' }}">
                        <i class="bi bi-images"></i>
                        <span>Gallery</span>
                    </a>
                </div>
                <div class="col">
                    <a href="{{ route('account') }}" class="nav-link {{ request()->routeIs('account*') ? 'active' : '' }}">
                        <i class="bi bi-person-fill"></i>
                        <span>Account</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Toast Container -->
    <div class="toast-container"></div>
    
    <!-- Loading Spinner -->
    <div class="spinner-overlay d-none" id="loadingSpinner">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    <!-- Your JavaScript -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('dashboard.js') }}"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    
    <!-- Enhanced JavaScript -->
    <script>
        // Show loading spinner
        function showLoading() {
            document.getElementById('loadingSpinner').classList.remove('d-none');
        }
        
        // Hide loading spinner
        function hideLoading() {
            document.getElementById('loadingSpinner').classList.add('d-none');
        }
        
        // Show toast notification
        function showToast(message, type = 'success') {
            const toastContainer = document.querySelector('.toast-container');
            const toastId = 'toast-' + Date.now();
            
            const toastHtml = `
                <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
                    <div class="toast-header">
                        <i class="bi bi-${type === 'success' ? 'check-circle-fill text-success' : 'exclamation-triangle-fill text-warning'} me-2"></i>
                        <strong class="me-auto">Green Cup</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            const toast = new bootstrap.Toast(document.getElementById(toastId));
            toast.show();
            
            document.getElementById(toastId).addEventListener('hidden.bs.toast', function() {
                this.remove();
            });
        }
        
        // Enhanced page interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Navbar scroll effect
            window.addEventListener('scroll', function() {
                const navbar = document.querySelector('.navbar');
                if (navbar && window.scrollY > 50) {
                    navbar.style.background = 'rgba(16, 185, 129, 0.95)';
                    navbar.style.boxShadow = '0 8px 32px rgba(16, 185, 129, 0.25)';
                } else if (navbar) {
                    navbar.style.background = 'linear-gradient(135deg, #10b981 0%, #047857 100%)';
                    navbar.style.boxShadow = '0 8px 32px rgba(16, 185, 129, 0.15)';
                }
            });
            
            // Form loading states
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    showLoading();
                });
            });
            
            // Mobile menu auto-close
            document.querySelectorAll('.bottom-nav .nav-link').forEach(link => {
                link.addEventListener('click', function() {
                    const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('mobileMenu'));
                    if (offcanvas) {
                        offcanvas.hide();
                    }
                });
            });
            
            // Enhanced card hover effects
            document.querySelectorAll('.stat-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px)';
                    this.style.boxShadow = '0 16px 48px rgba(16, 185, 129, 0.12)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 8px 32px rgba(16, 185, 129, 0.06)';
                });
            });
            
            console.log('Green Cup Enhanced Layout Loaded Successfully!');
        });
    </script>
    
    @stack('scripts')
</body>
</html>
