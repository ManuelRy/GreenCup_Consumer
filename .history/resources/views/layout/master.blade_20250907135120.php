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
    
    <!-- Custom CSS -->
    <link href="{{ asset('dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <!-- Custom responsive styles -->
    <style>
        :root {
            --primary-color: #22c55e;
            --primary-dark: #16a34a;
            --primary-light: #dcfce7;
            --secondary-color: #059669;
            --accent-color: #fbbf24;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
            --gradient-primary: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            --gradient-secondary: linear-gradient(135deg, #059669 0%, #047857 100%);
            --shadow-soft: 0 4px 20px rgba(34, 197, 94, 0.15);
            --shadow-medium: 0 8px 30px rgba(34, 197, 94, 0.2);
            --shadow-strong: 0 12px 40px rgba(34, 197, 94, 0.25);
        }
        
        body {
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
        }
        
        /* Fixed Navbar Styles */
        .navbar {
            position: fixed !important;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;
            background: var(--gradient-primary) !important;
            backdrop-filter: blur(10px);
            border-bottom: 2px solid var(--primary-dark);
            box-shadow: var(--shadow-medium);
            transition: all 0.3s ease;
        }
        
        .navbar.scrolled {
            box-shadow: var(--shadow-strong);
        }
        
        .navbar-brand {
            font-weight: bold;
            color: white !important;
            font-size: 1.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-brand:hover {
            color: var(--accent-color) !important;
            transform: scale(1.05);
            transition: all 0.3s ease;
        }
        
        .navbar-nav .nav-link {
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9) !important;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 0 4px;
            padding: 8px 16px !important;
        }
        
        .navbar-nav .nav-link:hover {
            color: white !important;
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }
        
        .navbar-nav .nav-link.active {
            color: white !important;
            background: var(--primary-dark);
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        /* Mobile Top Bar */
        .mobile-navbar {
            background: var(--gradient-primary) !important;
            position: fixed !important;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;
            box-shadow: var(--shadow-medium);
        }
        
        .mobile-navbar .navbar-brand {
            color: white !important;
        }
        
        /* Bottom Navigation - Enhanced */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e5e7eb;
            z-index: 1030;
            padding: 0.75rem 0;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
        }
        
        .bottom-nav .nav-link {
            text-align: center;
            padding: 0.5rem 0.25rem;
            font-size: 0.7rem;
            color: var(--secondary-color);
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 12px;
            margin: 0 2px;
        }
        
        .bottom-nav .nav-link:hover,
        .bottom-nav .nav-link.active {
            color: var(--primary-color);
            background: var(--primary-light);
            transform: translateY(-2px);
        }
        
        .bottom-nav .nav-link i {
            font-size: 1.4rem;
            display: block;
            margin-bottom: 0.25rem;
            transition: all 0.3s ease;
        }
        
        .bottom-nav .nav-link.active i {
            transform: scale(1.1);
        }
        
        /* Main Content Spacing */
        .main-content {
            padding-top: 80px; /* Space for fixed navbar */
            padding-bottom: 100px; /* Space for bottom navigation */
            min-height: calc(100vh - 80px);
        }
        
        /* Hide bottom nav on larger screens */
        @media (min-width: 768px) {
            .bottom-nav {
                display: none;
            }
            
            .main-content {
                padding-bottom: 40px;
            }
        }
        
        /* User Avatar Enhanced */
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--gradient-secondary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            border: 2px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }
        
        .user-avatar:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        /* Dropdown Enhancements */
        .dropdown-menu {
            border: none;
            box-shadow: var(--shadow-medium);
            border-radius: 12px;
            padding: 8px;
            background: white;
            backdrop-filter: blur(10px);
        }
        
        .dropdown-item {
            border-radius: 8px;
            transition: all 0.3s ease;
            padding: 10px 16px;
        }
        
        .dropdown-item:hover {
            background: var(--primary-light);
            color: var(--primary-dark);
            transform: translateX(4px);
        }
        
        .dropdown-toggle::after {
            display: none;
        }
        
        /* Off-canvas Menu Enhanced */
        .offcanvas {
            background: var(--gradient-primary);
            color: white;
        }
        
        .offcanvas-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .offcanvas-title {
            color: white;
        }
        
        .btn-close {
            filter: invert(1);
        }
        
        .list-group-item {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }
        
        .list-group-item:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(8px);
        }
        
        /* Toast notifications Enhanced */
        .toast-container {
            position: fixed;
            top: 100px; /* Below fixed navbar */
            right: 20px;
            z-index: 1055;
        }
        
        .toast {
            border: none;
            box-shadow: var(--shadow-medium);
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }
        
        .toast-header {
            background: var(--gradient-primary);
            color: white;
            border-bottom: none;
        }
        
        /* Loading spinner Enhanced */
        .spinner-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            backdrop-filter: blur(5px);
        }
        
        .spinner-border {
            border-color: var(--primary-color);
            border-right-color: transparent;
        }
        
        /* Card Enhancements */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: var(--shadow-soft);
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }
        
        .card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-medium);
        }
        
        /* Button Enhancements */
        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            border-radius: 12px;
            font-weight: 600;
            padding: 12px 24px;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-soft);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
            background: var(--primary-dark);
        }
        
        .btn-success {
            background: var(--gradient-secondary);
            border: none;
            border-radius: 12px;
        }
        
        /* Responsive utilities */
        @media (max-width: 575.98px) {
            .container-fluid {
                padding-left: 15px;
                padding-right: 15px;
            }
            
            .navbar-brand {
                font-size: 1.2rem;
            }
            
            .main-content {
                padding-top: 70px;
            }
        }
        
        /* Custom Green Cup Animations */
        @keyframes pulse-green {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 0 0 10px rgba(34, 197, 94, 0);
            }
        }
        
        .pulse-green {
            animation: pulse-green 2s infinite;
        }
        
        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--light-color);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
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
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('map') ? 'active' : '' }}" href="{{ route('map') }}">
                            <i class="bi bi-geo-alt-fill"></i> Store Locator
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('gallery') ? 'active' : '' }}" href="{{ route('gallery') }}">
                            <i class="bi bi-images"></i> Gallery
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('scan*') ? 'active' : '' }}" href="{{ route('scan.receipt') }}">
                            <i class="bi bi-qr-code-scan"></i> Scan Receipt
                        </a>
                    </li>
                </ul>
                
                <!-- User Menu -->
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <div class="user-avatar me-2">
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
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Mobile Top Bar -->
    <nav class="navbar mobile-navbar d-md-none">
        <div class="container-fluid">
            <a class="navbar-brand pulse-green" href="{{ route('dashboard') }}">
                <i class="bi bi-cup-hot-fill"></i> Green Cup
            </a>
            
            <div class="d-flex align-items-center">
                <div class="user-avatar" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" style="cursor: pointer;">
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
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
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
                        <i class="bi bi-person"></i>
                        <span>Account</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Toast Container -->
    <div class="toast-container"></div>
    
    <!-- Loading Spinner (hidden by default) -->
    <div class="spinner-overlay d-none" id="loadingSpinner">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    <!-- Your JavaScript -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('dashboard.js') }}"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    
    <!-- Common JavaScript utilities -->
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
            
            // Remove toast element after it's hidden
            document.getElementById(toastId).addEventListener('hidden.bs.toast', function() {
                this.remove();
            });
        }
        
        // Handle form submissions with loading states
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading state to form submissions
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    showLoading();
                });
            });
            
            // Close mobile menu when clicking on nav links
            document.querySelectorAll('.bottom-nav .nav-link').forEach(link => {
                link.addEventListener('click', function() {
                    const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('mobileMenu'));
                    if (offcanvas) {
                        offcanvas.hide();
                    }
                });
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>