<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Green Cup App</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <link href="{{ asset('dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        /* Responsive fixed navbar body padding */
        body {
            padding-top: var(--navbar-height) !important;
        }

        /* Define navbar height as CSS variable for responsiveness */
        :root {
            --navbar-height: 70px; /* Default for large screens */
        }

        /* Responsive navbar height adjustments */
        @media (max-width: 991.98px) {
            :root {
                --navbar-height: 76px; /* Slightly taller on tablets */
            }
        }

        @media (max-width: 767.98px) {
            :root {
                --navbar-height: 80px; /* Taller on mobile for touch targets */
            }
        }

        @media (max-width: 575.98px) {
            :root {
                --navbar-height: 84px; /* Maximum height on small mobile */
            }
        }

        /* Custom navbar styles */
        .navbar-brand {
            font-weight: 600;
            font-size: 1.5rem;
            color: #28a745 !important;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            background-color: #ffffff !important;
            min-height: var(--navbar-height);
            transition: min-height 0.3s ease;
        }

        .navbar-nav .nav-link {
            color: #495057 !important;
            font-weight: 500;
            transition: all 0.2s ease;
            margin: 0 0.25rem;
            padding: 0.5rem 0.75rem !important;
            border-radius: 6px;
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
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #28a745, #20c997);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .points-badge {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 0.35rem 0.85rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 0.5rem 0;
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .dropdown-divider {
            margin: 0.5rem 0;
        }

        /* Mobile optimizations */
        @media (max-width: 991.98px) {
            .navbar-nav {
                padding-top: 1rem;
                border-top: 1px solid #dee2e6;
                margin-top: 1rem;
            }

            .navbar-nav .nav-link {
                padding: 0.75rem 0 !important;
                border-bottom: 1px solid #f8f9fa;
                margin: 0;
                border-radius: 0;
            }

            .navbar-nav .nav-link:hover,
            .navbar-nav .nav-link.active {
                background-color: transparent;
                border-left: 3px solid #28a745;
                padding-left: 1rem !important;
            }

            .user-dropdown {
                margin-top: 1rem;
                padding-top: 1rem;
                border-top: 1px solid #dee2e6;
            }

            .points-badge {
                margin-bottom: 0.5rem;
                display: inline-block;
            }

            .user-info {
                justify-content: flex-start;
            }
        }

        /* Extra small screens */
        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.25rem;
            }

            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .points-badge {
                font-size: 0.8rem;
                padding: 0.25rem 0.65rem;
            }

            .user-avatar {
                width: 32px;
                height: 32px;
                font-size: 0.8rem;
            }
        }

        /* Content spacing adjustment */
        .main-content {
            padding-top: 2rem;
        }

        /* Ensure content doesn't hide behind fixed navbar */
        body {
            padding-top: 0;
        }
    </style>
</head>
<body>
    @include('partials.navbar')
    @yield('content')

    {{-- Your JavaScript --}}
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('dashboard.js') }}"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
