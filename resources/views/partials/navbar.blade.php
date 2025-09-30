@php
    $consumer = auth('consumer')->user();

    if ($consumer) {
        // Get points using the same method as dashboard/account pages
        try {
            $cPRepo = app(\App\Repository\ConsumerPointRepository::class);
            $totalData = $cPRepo->getTotalByConsumerId($consumer->id);
            $availablePoints = is_array($totalData) ? ($totalData['coins'] ?? 0) : 0;
        } catch (\Exception $e) {
            $availablePoints = 0;
        }

        $points = number_format($availablePoints);
        $fullName = $consumer->full_name ?? 'User';
        $initial = strtoupper(substr($fullName, 0, 1));
    } else {
        $points = '0';
        $fullName = 'User';
        $initial = 'U';
    }
@endphp

@auth('consumer')
    <nav class="navbar navbar-expand-lg bg-white fixed-top shadow-sm border-bottom py-1">
        <div class="container-fluid px-3">
            <!-- Brand -->
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                <i class="bi bi-cup-hot"></i>
                <span class="fw-semibold">Green Cups</span>
            </a>

            <!-- Mobile Right: points + toggler -->
            <div class="d-lg-none d-flex align-items-center gap-2">
                <span class="points-badge">
                    <i class="bi bi-star-fill me-1"></i>{{ $points }} <span class="d-none d-sm-inline">pts</span>
                </span>
                <!-- Toggler points to #mobileNav (offcanvas) -->
                <button class="navbar-toggler rounded-3 p-2" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#mobileNav" aria-controls="mobileNav" aria-label="Toggle navigation">
                    <span class="hamburger-menu">
                        <span class="line"></span>
                        <span class="line"></span>
                        <span class="line"></span>
                    </span>
                </button>
            </div>

            <!-- Desktop Nav (inline; no collapse on lg+) -->
            <div class="d-none d-lg-flex align-items-center flex-grow-1" id="navbarNav">
                <!-- Left Navigation -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}" @if (request()->routeIs('dashboard')) aria-current="page" @endif>
                            <i class="bi bi-speedometer2 me-1"></i><span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reward.*') ? 'active' : '' }}" href="{{ route('reward.index') }}" @if (request()->routeIs('reward.*')) aria-current="page" @endif>
                            <i class="bi bi-gift me-1"></i><span>Rewards</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('gallery') || request()->routeIs('products') ? 'active' : '' }}"
                            href="{{ route('gallery') }}" @if (request()->routeIs('gallery') || request()->routeIs('products')) aria-current="page" @endif>
                            <i class="bi bi-grid me-1"></i><span>Products</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('map') ? 'active' : '' }}" href="{{ route('map') }}"
                            @if (request()->routeIs('map')) aria-current="page" @endif>
                            <i class="bi bi-geo-alt me-1"></i><span>Stores</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('scan.receipt') || request()->routeIs('scan') ? 'active' : '' }}"
                            href="{{ route('scan.receipt') }}" @if (request()->routeIs('scan.receipt') || request()->routeIs('scan')) aria-current="page" @endif>
                            <i class="bi bi-qr-code-scan me-1"></i><span>Scan</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('report.*') ? 'active' : '' }}"
                            href="{{ route('report.index') }}">
                            <i class="bi bi-exclamation-triangle me-1"></i><span>Report</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('environmental-impact.*') ? 'active' : '' }}"
                            href="{{ route('environmental-impact.index') }}" @if (request()->routeIs('environmental-impact.*')) aria-current="page" @endif>
                            <i class="bi bi-graph-up me-1"></i><span>Impact</span>
                        </a>
                    </li>
                </ul>

                <!-- Right Navigation -->
                <ul class="navbar-nav">
                    <li class="nav-item d-none d-lg-flex align-items-center me-3">
                        <span class="points-badge">
                            <i class="bi bi-star-fill me-1"></i>{{ $points }} pts
                        </span>
                    </li>

                    <!-- User Dropdown -->
                    <li class="nav-item dropdown user-dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="d-none d-md-inline">{{ $fullName }}</span>
                            <span class="user-avatar">{{ $initial }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="d-md-none">
                                <h6 class="dropdown-header">{{ $fullName }}</h6>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('account') ? 'active' : '' }}"
                                    href="{{ route('account') }}">
                                    <i class="bi bi-person me-2"></i>Account
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('account.edit') }}">
                                    <i class="bi bi-gear me-2"></i>Settings
                                </a>
                            </li>
                            {{-- <li>
                            <a class="dropdown-item" href="{{ route('consumer.qr-code') }}">
                                <i class="bi bi-qr-code me-2"></i>My QR Code
                            </a>
                        </li> --}}
                            <li>
                                <a class="dropdown-item" href="{{ route('account.transactions') }}">
                                    <i class="bi bi-clock-history me-2"></i>Transactions
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="restartTour()">
                                    <i class="bi bi-question-circle me-2"></i>Take Tour Again
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Mobile Offcanvas -->
        <div class="offcanvas offcanvas-end d-lg-none" tabindex="-1" id="mobileNav" aria-labelledby="mobileNavLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title d-flex align-items-center gap-2" id="mobileNavLabel">
                    <span class="user-avatar">{{ $initial }}</span>
                    <span>{{ $fullName }}</span>
                </h5>
            </div>
            <div class="offcanvas-body d-flex flex-column">
                <div class="mb-3">
                    <span class="points-badge w-100 justify-content-center">
                        <i class="bi bi-star-fill me-1"></i>{{ $points }} pts
                    </span>
                </div>

                <ul class="navbar-nav flex-grow-1">
                    <li class="nav-item">
                        <a class="nav-link px-0 {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}" data-bs-dismiss="offcanvas"
                            onclick="console.log('Dashboard clicked'); return true;">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-0 {{ request()->routeIs('reward.*') ? 'active' : '' }}"
                            href="{{ route('reward.index') }}" data-bs-dismiss="offcanvas"
                            onclick="console.log('Rewards clicked'); return true;">
                            <i class="bi bi-gift me-2"></i>Rewards
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-0 {{ request()->routeIs('gallery') || request()->routeIs('products') ? 'active' : '' }}"
                            href="{{ route('gallery') }}" data-bs-dismiss="offcanvas"
                            onclick="console.log('Gallery clicked'); return true;">
                            <i class="bi bi-grid me-2"></i>Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-0 {{ request()->routeIs('map') ? 'active' : '' }}"
                            href="{{ route('map') }}" data-bs-dismiss="offcanvas"
                            onclick="console.log('Map clicked'); return true;">
                            <i class="bi bi-geo-alt me-2"></i>Stores
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-0 {{ request()->routeIs('scan.receipt') || request()->routeIs('scan') ? 'active' : '' }}"
                            href="{{ route('scan.receipt') }}" data-bs-dismiss="offcanvas"
                            onclick="console.log('Scan clicked'); return true;">
                            <i class="bi bi-qr-code-scan me-2"></i>Scan
                        </a>
                    </li>
                    <li>
                        <hr>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-0 {{ request()->routeIs('account') ? 'active' : '' }}"
                            href="{{ route('account') }}" data-bs-dismiss="offcanvas"
                            onclick="console.log('Account clicked'); return true;">
                            <i class="bi bi-person me-2"></i>Account
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-0" href="{{ route('account.edit') }}" data-bs-dismiss="offcanvas"
                            onclick="console.log('Settings clicked'); return true;">
                            <i class="bi bi-gear me-2"></i>Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-0 {{ request()->routeIs('report.*') ? 'active' : '' }}"
                            href="{{ route('report.index') }}" data-bs-dismiss="offcanvas"
                            onclick="console.log('Report clicked'); return true;">
                            <i class="bi bi-exclamation-triangle me-2"></i>Report
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-0 {{ request()->routeIs('environmental-impact.*') ? 'active' : '' }}"
                            href="{{ route('environmental-impact.index') }}" data-bs-dismiss="offcanvas"
                            onclick="console.log('Environmental Impact clicked'); return true;">
                            <i class="bi bi-graph-up me-2"></i>Environmental Impact
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                    <a class="nav-link px-0"
                       href="{{ route('consumer.qr-code') }}"
                       data-bs-dismiss="offcanvas"
                       onclick="console.log('QR Code clicked'); return true;">
                        <i class="bi bi-qr-code me-2"></i>My QR Code
                    </a>
                </li> --}}
                    <li class="nav-item">
                        <a class="nav-link px-0" href="{{ route('account.transactions') }}" data-bs-dismiss="offcanvas"
                            onclick="console.log('Transactions clicked'); return true;">
                            <i class="bi bi-clock-history me-2"></i>Transactions
                        </a>
                    </li>
                </ul>

                <form action="{{ route('logout') }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>
@else
    <!-- Guest Navigation -->
    <nav class="navbar navbar-expand-lg bg-white fixed-top shadow-sm border-bottom py-1">
        <div class="container-fluid px-3">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ auth('consumer')->check() ? route('dashboard') : route('guest.dashboard') }}">
                <i class="bi bi-cup-hot"></i><span class="fw-semibold">Green Cups</span>
            </a>

            <!-- Guest mobile toggler -> #guestNav -->
            <div class="d-lg-none">
                <button class="navbar-toggler rounded-3 p-2" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#guestNav" aria-controls="guestNav" aria-label="Toggle navigation">
                    <span class="hamburger-menu">
                        <span class="line"></span>
                        <span class="line"></span>
                        <span class="line"></span>
                    </span>
                </button>
            </div>

            <!-- Desktop links -->
            <div class="d-none d-lg-flex align-items-center flex-grow-1" id="navbarNav">
                <!-- Left Navigation -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('guest.dashboard') ? 'active' : '' }}"
                            href="{{ route('guest.dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i><span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('guest.gallery') ? 'active' : '' }}"
                            href="{{ route('guest.gallery') }}">
                            <i class="bi bi-grid me-1"></i><span>Products</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('guest.map') ? 'active' : '' }}"
                            href="{{ route('guest.map') }}">
                            <i class="bi bi-geo-alt me-1"></i><span>Stores</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('guest.environmental-impact') ? 'active' : '' }}"
                            href="{{ route('guest.environmental-impact') }}">
                            <i class="bi bi-graph-up me-1"></i><span>Impact</span>
                        </a>
                    </li>
                </ul>

                <!-- Right Navigation -->
                <div class="navbar-nav d-flex align-items-center">
                    <a class="nav-link" href="javascript:void(0)" onclick="restartTour()" title="Take a Tour">
                        <i class="bi bi-question-circle fs-5"></i>
                    </a>
                    <a class="nav-link btn btn-outline-success me-2" href="{{ route('login') }}">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Login
                    </a>
                    <a class="nav-link btn btn-success text-white" href="{{ route('register') }}">
                        <i class="bi bi-person-plus me-1"></i>Sign Up Free
                    </a>
                </div>
            </div>
        </div>

        <!-- Guest Offcanvas (mobile) -->
        <div class="offcanvas offcanvas-end d-lg-none" tabindex="-1" id="guestNav" aria-labelledby="guestNavLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="guestNavLabel">Menu</h5>
            </div>
            <div class="offcanvas-body">
                <div class="navbar-nav">
                    <a class="nav-link {{ request()->routeIs('guest.dashboard') ? 'active' : '' }}"
                        href="{{ route('guest.dashboard') }}" data-bs-dismiss="offcanvas">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                    <a class="nav-link {{ request()->routeIs('guest.gallery') ? 'active' : '' }}"
                        href="{{ route('guest.gallery') }}" data-bs-dismiss="offcanvas">
                        <i class="bi bi-grid me-1"></i>Products
                    </a>
                    <a class="nav-link {{ request()->routeIs('guest.map') ? 'active' : '' }}"
                        href="{{ route('guest.map') }}" data-bs-dismiss="offcanvas">
                        <i class="bi bi-geo-alt me-1"></i>Stores
                    </a>
                    <a class="nav-link {{ request()->routeIs('guest.environmental-impact') ? 'active' : '' }}"
                        href="{{ route('guest.environmental-impact') }}" data-bs-dismiss="offcanvas">
                        <i class="bi bi-graph-up me-1"></i>Impact
                    </a>
                    <hr class="my-3">
                    <a class="nav-link" href="javascript:void(0)" onclick="restartTour(); document.querySelector('[data-bs-dismiss=offcanvas]').click();">
                        <i class="bi bi-question-circle me-1"></i>Take a Tour
                    </a>
                    <hr class="my-3">
                    <a class="nav-link btn btn-outline-success w-100 mb-2" href="{{ route('login') }}" data-bs-dismiss="offcanvas">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Login
                    </a>
                    <a class="nav-link btn btn-success text-white w-100" href="{{ route('register') }}" data-bs-dismiss="offcanvas">
                        <i class="bi bi-person-plus me-1"></i>Sign Up Free
                    </a>
                </div>
            </div>
        </div>
    </nav>
@endauth

{{-- Minimal styles for your rounded green theme --}}
<style>
    .points-badge {
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        padding: .25rem .5rem;
        border-radius: 999px;
        background: #e9f7ef;
        color: #198754;
        font-weight: 600;
        font-size: .9rem;
        border: 1px solid rgba(25, 135, 84, .15);
        line-height: 1;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #198754;
        color: #fff;
        font-weight: 700;
        letter-spacing: .5px;
    }

    .navbar .nav-link {
        border-radius: .75rem;
        padding: .5rem .75rem;
        position: relative;
        z-index: 1;
    }

    .navbar .nav-link.active,
    .navbar .nav-link:focus {
        background: rgba(25, 135, 84, .1);
        outline: 0;
    }

    .offcanvas .nav-link {
        padding: .6rem 0;
        cursor: pointer;
        position: relative;
        z-index: 1;
        display: block;
        text-decoration: none;
    }

    .offcanvas .nav-link:hover {
        background: rgba(25, 135, 84, .1);
        border-radius: .5rem;
    }

    /* Ensure navbar links are clickable */
    .navbar .nav-link {
        border-radius: .75rem;
        padding: .5rem .75rem;
        position: relative;
        z-index: 1;
        pointer-events: auto !important;
        cursor: pointer !important;
    }

    .navbar .nav-link.active,
    .navbar .nav-link:focus {
        background: rgba(25, 135, 84, .1);
        outline: 0;
    }

    .navbar .dropdown-toggle::after {
        margin-left: 0.5rem;
    }

    .navbar .dropdown-menu {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        border-radius: 0.5rem;
        z-index: 1050;
    }

    .navbar .dropdown-item {
        pointer-events: auto !important;
        cursor: pointer !important;
    }

    .offcanvas .nav-link {
        padding: .6rem 0;
        cursor: pointer;
        position: relative;
        z-index: 1;
        display: block;
        text-decoration: none;
    }

    .offcanvas .nav-link:hover {
        background: rgba(25, 135, 84, .1);
        border-radius: .5rem;
    }

    /* Ensure offcanvas links are clickable */
    .offcanvas .navbar-nav .nav-item {
        position: relative;
        z-index: 1;
    }

    .offcanvas .navbar-nav .nav-link {
        pointer-events: auto;
        touch-action: manipulation;
        display: block;
        width: 100%;
        text-decoration: none;
        color: inherit;
        cursor: pointer;
        padding: 0.6rem 0.75rem;
        border-radius: 0.5rem;
        transition: background-color 0.15s ease-in-out;
    }

    .offcanvas .navbar-nav .nav-link:hover,
    .offcanvas .navbar-nav .nav-link:focus {
        background: rgba(25, 135, 84, .1);
        text-decoration: none;
        color: inherit;
    }

    /* Fix mobile touch targets */
    @media (max-width: 991.98px) {
        .offcanvas .navbar-nav .nav-link {
            min-height: 48px;
            display: flex;
            align-items: center;
            touch-action: manipulation;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0.1);
        }
    }

    /* Ensure navbar toggler works */
    .navbar-toggler {
        position: relative;
        z-index: 1050;
        cursor: pointer;
        border: none;
        background: transparent;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .navbar-toggler:focus {
        box-shadow: none;
        outline: none;
    }

    /* Hamburger Menu Animation */
    .hamburger-menu {
        width: 20px;
        height: 14px;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .hamburger-menu .line {
        width: 100%;
        height: 2px;
        background-color: #333;
        transition: all 0.3s ease;
        transform-origin: center;
    }

    /* When offcanvas is shown (active state) */
    .navbar-toggler[aria-expanded="true"] .hamburger-menu .line:nth-child(1) {
        transform: rotate(45deg) translate(5px, 5px);
    }

    .navbar-toggler[aria-expanded="true"] .hamburger-menu .line:nth-child(2) {
        opacity: 0;
    }

    .navbar-toggler[aria-expanded="true"] .hamburger-menu .line:nth-child(3) {
        transform: rotate(-45deg) translate(5px, -5px);
    }
</style>

{{-- Fix specifically for account dropdown and mobile navigation --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing navigation...');

    // Initialize Bootstrap components
    function initializeBootstrap() {
        if (window.bootstrap) {
            console.log('Bootstrap is available');

            // Initialize all dropdowns
            const dropdownElements = document.querySelectorAll('[data-bs-toggle="dropdown"]');
            dropdownElements.forEach(function(element) {
                if (!bootstrap.Dropdown.getInstance(element)) {
                    new bootstrap.Dropdown(element);
                    console.log('Dropdown initialized for:', element);
                }
            });

            // Initialize all offcanvas
            const offcanvasElements = document.querySelectorAll('.offcanvas');
            offcanvasElements.forEach(function(element) {
                if (!bootstrap.Offcanvas.getInstance(element)) {
                    new bootstrap.Offcanvas(element);
                    console.log('Offcanvas initialized for:', element.id);
                }
            });
        } else {
            console.log('Bootstrap not ready, retrying...');
            setTimeout(initializeBootstrap, 100);
        }
    }

    // Initialize Bootstrap after a short delay
    setTimeout(initializeBootstrap, 50);

    // Handle mobile navigation links
    const mobileNavLinks = document.querySelectorAll('#mobileNav .nav-link[href]:not([href="#"])');
    mobileNavLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            console.log('Mobile nav link clicked:', this.textContent.trim());

            // Add a small delay before navigation to ensure offcanvas closes
            const href = this.getAttribute('href');
            if (href && href !== '#') {
                // Close the offcanvas
                const offcanvasElement = document.getElementById('mobileNav');
                if (offcanvasElement) {
                    const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasElement);
                    if (offcanvasInstance) {
                        offcanvasInstance.hide();
                    }
                }

                // Navigate after a short delay
                setTimeout(function() {
                    window.location.href = href;
                }, 150);

                // Prevent default to handle navigation manually
                e.preventDefault();
                return false;
            }
        });
    });

    // Handle hamburger menu animation
    function handleTogglerAnimation(togglerSelector, offcanvasSelector) {
        const toggler = document.querySelector(togglerSelector);
        const offcanvas = document.querySelector(offcanvasSelector);

        if (toggler && offcanvas) {
            // Handle offcanvas show event
            offcanvas.addEventListener('show.bs.offcanvas', function() {
                toggler.setAttribute('aria-expanded', 'true');
            });

            // Handle offcanvas hide event
            offcanvas.addEventListener('hide.bs.offcanvas', function() {
                toggler.setAttribute('aria-expanded', 'false');
            });

            // Ensure initial state is correct
            toggler.setAttribute('aria-expanded', 'false');
        }
    }

    // Apply animation handling to both togglers
    handleTogglerAnimation('[data-bs-target="#mobileNav"]', '#mobileNav');
    handleTogglerAnimation('[data-bs-target="#guestNav"]', '#guestNav');

    // Clean up backdrop issues
    document.addEventListener('hidden.bs.offcanvas', function () {
        // Remove any lingering backdrops
        const backdrops = document.querySelectorAll('.offcanvas-backdrop');
        backdrops.forEach(backdrop => backdrop.remove());

        // Re-enable body scroll
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    });

    console.log('Navigation initialization complete');
});

// Fallback for touch devices
document.addEventListener('touchstart', function() {}, {passive: true});
</script>
