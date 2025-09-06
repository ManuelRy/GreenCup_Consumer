<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Green Cup App')</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    
    <!-- CSS -->
    <link href="{{ asset('dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @stack('styles')
</head>
<body class="app-layout">
    <!-- Navigation -->
    @auth('consumer')
        @include('layouts.navigation')
    @endauth

    <!-- Main Content Wrapper -->
    <div class="main-wrapper @auth('consumer') with-nav @endauth">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="flash-message flash-success" id="flash-message">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
                <button class="flash-close" onclick="closeFlash()">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="flash-message flash-error" id="flash-message">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
                <button class="flash-close" onclick="closeFlash()">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div class="flash-message flash-error" id="flash-message">
                <i class="fas fa-exclamation-triangle"></i>
                {{ $errors->first() }}
                <button class="flash-close" onclick="closeFlash()">&times;</button>
            </div>
        @endif

        <!-- Page Content -->
        @yield('content')
    </div>

    <!-- Bottom Navigation for Mobile -->
    @auth('consumer')
        @include('layouts.bottom-nav')
    @endauth

    <!-- JavaScript -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('dashboard.js') }}"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    
    <!-- Master Layout JavaScript -->
    <script>
        // Flash message auto-hide
        function closeFlash() {
            const flashMessage = document.getElementById('flash-message');
            if (flashMessage) {
                flashMessage.style.opacity = '0';
                setTimeout(() => flashMessage.remove(), 300);
            }
        }

        // Auto-hide flash messages after 5 seconds
        setTimeout(() => {
            const flashMessage = document.getElementById('flash-message');
            if (flashMessage) {
                closeFlash();
            }
        }, 5000);

        // Mobile menu toggle
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            const overlay = document.getElementById('menu-overlay');
            
            if (mobileMenu && overlay) {
                mobileMenu.classList.toggle('active');
                overlay.classList.toggle('active');
            }
        }

        // Close mobile menu when clicking overlay
        document.addEventListener('click', function(e) {
            if (e.target.id === 'menu-overlay') {
                toggleMobileMenu();
            }
        });

        // Active navigation highlighting
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-link, .bottom-nav-item');
            
            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (href && href === currentPath) {
                    link.classList.add('active');
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
