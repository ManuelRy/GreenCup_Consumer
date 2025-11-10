<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Green Cups App</title>
    <link rel="icon" type="image/png" href="{{ asset('logo/consumer-logo.png') }}">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <link href="{{ asset('dashboard.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* More visible green background with content spacing */
        body {
            background: #e8f5e8 !important;
            padding-top: var(--navbar-height) !important;
        }

        /* Add padding around main content to show background */
        .container, .container-fluid {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }

        /* Add some margin to dashboard content */
        .main-content, .page-content, [class*="dashboard"], [class*="content"] {
            margin: 1rem 0;
        }

        /* Define navbar height as CSS variable for responsiveness */
        :root {
            --navbar-height: 60px; /* Default for large screens */
        }

        /* Responsive navbar height adjustments */
        @media (max-width: 991.98px) {
            :root {
                --navbar-height: 64px; /* Slightly taller on tablets */
            }
        }

        @media (max-width: 767.98px) {
            :root {
                --navbar-height: 68px; /* Taller on mobile for touch targets */
            }
        }

        @media (max-width: 575.98px) {
            :root {
                --navbar-height: 70px; /* Maximum height on small mobile */
            }
        }

        /* Custom navbar styles */
        .navbar-brand {
            font-weight: 600;
            font-size: 1.4rem;
            color: #28a745 !important;
            margin-right: 1rem;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            background-color: #ffffff !important;
            min-height: var(--navbar-height);
            max-height: var(--navbar-height);
            transition: min-height 0.3s ease;
            padding: 0.25rem 0;
        }

        .navbar-nav .nav-link {
            color: #495057 !important;
            font-weight: 500;
            transition: all 0.2s ease;
            margin: 0 0.1rem;
            padding: 0.4rem 0.6rem !important;
            border-radius: 6px;
            font-size: 0.9rem;
        }

        .navbar-nav .nav-link:hover {
            color: #28a745 !important;
            background-color: rgba(40, 167, 69, 0.1);
        }

        .navbar-nav .nav-link.active {
            color: #28a745 !important;
            font-weight: 600;
            background-color: rgba(40, 167, 69, 0.15);
        }

        .navbar-toggler {
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .user-dropdown .dropdown-toggle::after {
            display: none;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #28a745, #20c997);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.8rem;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .points-badge {
            background: linear-gradient(135deg, #28a745, #20c997) !important;
            color: white !important;
            padding: 0.25rem 0.7rem !important;
            border-radius: 20px !important;
            font-size: 0.8rem !important;
            font-weight: 600 !important;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3) !important;
            display: inline-flex !important;
            align-items: center !important;
            border: none !important;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 0.5rem 0;
            z-index: 1050 !important;
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .dropdown-item.active {
            background-color: rgba(40, 167, 69, 0.15);
            color: #28a745;
            font-weight: 600;
        }

        .dropdown-divider {
            margin: 0.5rem 0;
        }

        /* Mobile optimizations */
        @media (max-width: 991.98px) {
            .navbar-nav {
                padding-top: 0.5rem;
                border-top: 1px solid #dee2e6;
                margin-top: 0.5rem;
            }

            .navbar-nav .nav-link {
                padding: 0.5rem 0 !important;
                border-bottom: 1px solid #f8f9fa;
                margin: 0;
                border-radius: 0;
                font-size: 0.9rem;
            }

            .navbar-nav .nav-link:hover,
            .navbar-nav .nav-link.active {
                background-color: transparent;
                border-left: 3px solid #28a745;
                padding-left: 1rem !important;
            }

            .user-dropdown {
                margin-top: 0.5rem;
                padding-top: 0.5rem;
                border-top: 1px solid #dee2e6;
            }

            .points-badge {
                margin-bottom: 0.25rem !important;
                display: inline-flex !important;
            }

            .user-info {
                justify-content: flex-start;
            }

            /* Ensure collapsed navbar doesn't take too much space */
            .navbar-collapse {
                max-height: calc(100vh - var(--navbar-height) - 20px);
                overflow-y: auto;
            }
        }

        /* Extra small screens */
        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.2rem;
            }

            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .points-badge {
                font-size: 0.75rem !important;
                padding: 0.2rem 0.5rem !important;
            }

            .user-avatar {
                width: 28px;
                height: 28px;
                font-size: 0.75rem;
            }

            .navbar-nav .nav-link {
                font-size: 0.85rem;
                padding: 0.3rem 0.5rem !important;
            }
        }

        /* Content spacing adjustment */
        .main-content {
            padding-top: 2rem;
        }

        /* Ensure proper spacing for content below fixed navbar */
        .container {
            margin-top: 0;
        }

        /* Additional spacing for pages that need it */
        .page-content {
            padding-top: 1rem;
        }
    </style>
</head>
<body>
    @include('partials.navbar')
    @yield('content')

    <!-- Onboarding Tours (separate for guests and authenticated users) -->
    @auth('consumer')
        @include('partials.onboarding-tour')
    @else
        @include('partials.guest-tour')
    @endauth

    <!-- Load Bootstrap first to ensure proper initialization -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="{{ asset('dashboard.js') }}"></script>

    <!-- Global Points Update Function -->
    <script>
        // Global function to update points display across all navbar instances
        window.updateNavbarPoints = function(newBalance) {
            if (newBalance === undefined || newBalance === null) return;

            const formattedPoints = Number(newBalance).toLocaleString();

            // Update desktop navbar points
            const desktopPoints = document.getElementById('desktop-points-value');
            if (desktopPoints) {
                desktopPoints.textContent = formattedPoints;
            }

            // Update mobile navbar points
            const mobilePoints = document.getElementById('mobile-points-value');
            if (mobilePoints) {
                mobilePoints.textContent = formattedPoints;
            }

            // Update offcanvas menu points
            const offcanvasPoints = document.getElementById('offcanvas-points-value');
            if (offcanvasPoints) {
                offcanvasPoints.textContent = formattedPoints;
            }
        };
    </script>
</body>
</html>
