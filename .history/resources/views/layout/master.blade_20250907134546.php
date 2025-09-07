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
            --primary-color: #28a745;
            --secondary-color: #6c757d;
            --accent-color: #ffc107;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
        }
        
        .navbar-nav .nav-link {
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover {
            color: var(--primary-color) !important;
        }
        
        .navbar-nav .nav-link.active {
            color: var(--primary-color) !important;
            font-weight: 600;
        }
        
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #dee2e6;
            z-index: 1030;
            padding: 0.5rem 0;
        }
        
        .bottom-nav .nav-link {
            text-align: center;
            padding: 0.5rem 0.25rem;
            font-size: 0.75rem;
            color: var(--secondary-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .bottom-nav .nav-link:hover,
        .bottom-nav .nav-link.active {
            color: var(--primary-color);
        }
        
        .bottom-nav .nav-link i {
            font-size: 1.5rem;
            display: block;
            margin-bottom: 0.25rem;
        }
        
        .main-content {
            padding-bottom: 80px; /* Space for bottom navigation */
        }
        
        /* Hide bottom nav on larger screens */
        @media (min-width: 768px) {
            .bottom-nav {
                display: none;
            }
            
            .main-content {
                padding-bottom: 20px;
            }
        }
        
        /* User dropdown styles */
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .dropdown-toggle::after {
            display: none;
        }
        
        /* Toast notifications */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1055;
        }
        
        /* Loading spinner */
        .spinner-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        /* Responsive utilities */
        @media (max-width: 575.98px) {
            .container-fluid {
                padding-left: 15px;
                padding-right: 15px;
            }
            
            .navbar-brand {
                font-size: 1.1rem;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Top Navigation (Desktop) -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm d-none d-md-block">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-cup-hot"></i> Green Cup
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
                            <i class="bi bi-geo-alt"></i> Store Locator
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
    <nav class="navbar navbar-light bg-white shadow-sm d-md-none">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-cup-hot"></i> Green Cup
            </a>
            
            <div class="d-flex align-items-center">
                <div class="user-avatar" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
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
                        <i class="bi bi-house"></i>
                        <span>Home</span>
                    </a>
                </div>
                <div class="col">
                    <a href="{{ route('map') }}" class="nav-link {{ request()->routeIs('map') ? 'active' : '' }}">
                        <i class="bi bi-geo-alt"></i>
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