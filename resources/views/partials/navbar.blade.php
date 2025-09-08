@auth('consumer')
<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="bi bi-cup-hot me-2"></i>Green Cup
        </a>

        <!-- Mobile Points Badge (visible only on mobile) -->
        <div class="d-lg-none me-2">
            <span class="points-badge">
                <i class="bi bi-star-fill me-1"></i>
                {{ number_format(auth('consumer')->user()->available_points) }}
                <span class="d-none d-sm-inline">pts</span>
            </span>
        </div>

        <!-- Mobile Menu Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Left Navigation -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                       href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2 me-1"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('gallery') || request()->routeIs('products') ? 'active' : '' }}"
                       href="{{ route('gallery') }}">
                        <i class="bi bi-grid me-1"></i>
                        <span>Products</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('map') ? 'active' : '' }}"
                       href="{{ route('map') }}">
                        <i class="bi bi-geo-alt me-1"></i>
                        <span>Stores</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('scan.receipt') || request()->routeIs('scan') ? 'active' : '' }}"
                       href="{{ route('scan.receipt') }}">
                        <i class="bi bi-qr-code-scan me-1"></i>
                        <span>Scan</span>
                    </a>
                </li>
            </ul>

            <!-- Right Navigation -->
            <ul class="navbar-nav">
                <!-- Points Display (hidden on mobile) -->
                <li class="nav-item d-none d-lg-flex align-items-center me-3">
                    <span class="points-badge">
                        <i class="bi bi-star-fill me-1"></i>
                        {{ number_format(auth('consumer')->user()->available_points) }}
                        pts
                    </span>
                </li>

                <!-- User Dropdown -->
                <li class="nav-item dropdown user-dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-info">
                            <span class="d-none d-md-inline me-2">
                                {{ auth('consumer')->user()->full_name ?? 'User' }}
                            </span>
                            <div class="user-avatar">
                                {{ substr(auth('consumer')->user()->full_name ?? 'U', 0, 1) }}
                            </div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <h6 class="dropdown-header d-md-none">
                                {{ auth('consumer')->user()->full_name ?? 'User' }}
                            </h6>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('account') }}">
                                <i class="bi bi-person me-2"></i>Account
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('account.edit') }}">
                                <i class="bi bi-gear me-2"></i>Settings
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('consumer.qr-code') }}">
                                <i class="bi bi-qr-code me-2"></i>My QR Code
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('account.transactions') }}">
                                <i class="bi bi-clock-history me-2"></i>Transactions
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
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
</nav>
@else
<!-- Guest Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="bi bi-cup-hot me-2"></i>Green Cup
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#guestNav"
                aria-controls="guestNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="guestNav">
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('login') }}">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Login
                </a>
                <a class="nav-link" href="{{ route('register') }}">
                    <i class="bi bi-person-plus me-1"></i>Register
                </a>
            </div>
        </div>
    </div>
</nav>
@endauth
