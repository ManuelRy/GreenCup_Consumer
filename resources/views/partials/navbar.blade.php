@php
    $consumer = auth('consumer')->user();
    $points   = number_format($consumer->available_points ?? 0);
    $fullName = $consumer->full_name ?? 'User';
    $initial  = strtoupper(substr($fullName, 0, 1));
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
            <button class="navbar-toggler rounded-3 p-2"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#mobileNav"
                    aria-controls="mobileNav"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>

        <!-- Desktop Nav (inline; no collapse on lg+) -->
        <div class="d-none d-lg-flex align-items-center flex-grow-1" id="navbarNav">
            <!-- Left Navigation -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                       href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif>
                        <i class="bi bi-speedometer2 me-1"></i><span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('gallery') || request()->routeIs('products') ? 'active' : '' }}"
                       href="{{ route('gallery') }}" @if(request()->routeIs('gallery') || request()->routeIs('products')) aria-current="page" @endif>
                        <i class="bi bi-grid me-1"></i><span>Products</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('map') ? 'active' : '' }}"
                       href="{{ route('map') }}" @if(request()->routeIs('map')) aria-current="page" @endif>
                        <i class="bi bi-geo-alt me-1"></i><span>Stores</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('scan.receipt') || request()->routeIs('scan') ? 'active' : '' }}"
                       href="{{ route('scan.receipt') }}" @if(request()->routeIs('scan.receipt') || request()->routeIs('scan')) aria-current="page" @endif>
                        <i class="bi bi-qr-code-scan me-1"></i><span>Scan</span>
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
                            <a class="dropdown-item {{ request()->routeIs('account') ? 'active' : '' }}" href="{{ route('account') }}">
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

    <!-- Mobile Offcanvas -->
    <div class="offcanvas offcanvas-end d-lg-none" tabindex="-1" id="mobileNav" aria-labelledby="mobileNavLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title d-flex align-items-center gap-2" id="mobileNavLabel">
                <span class="user-avatar">{{ $initial }}</span>
                <span>{{ $fullName }}</span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
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
                       href="{{ route('dashboard') }}"
                       data-bs-dismiss="offcanvas"
                       onclick="console.log('Dashboard clicked'); return true;">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-0 {{ request()->routeIs('gallery') || request()->routeIs('products') ? 'active' : '' }}"
                       href="{{ route('gallery') }}"
                       data-bs-dismiss="offcanvas"
                       onclick="console.log('Gallery clicked'); return true;">
                        <i class="bi bi-grid me-2"></i>Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-0 {{ request()->routeIs('map') ? 'active' : '' }}"
                       href="{{ route('map') }}"
                       data-bs-dismiss="offcanvas"
                       onclick="console.log('Map clicked'); return true;">
                        <i class="bi bi-geo-alt me-2"></i>Stores
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-0 {{ request()->routeIs('scan.receipt') || request()->routeIs('scan') ? 'active' : '' }}"
                       href="{{ route('scan.receipt') }}"
                       data-bs-dismiss="offcanvas"
                       onclick="console.log('Scan clicked'); return true;">
                        <i class="bi bi-qr-code-scan me-2"></i>Scan
                    </a>
                </li>
                <li><hr></li>
                <li class="nav-item">
                    <a class="nav-link px-0 {{ request()->routeIs('account') ? 'active' : '' }}"
                       href="{{ route('account') }}"
                       data-bs-dismiss="offcanvas"
                       onclick="console.log('Account clicked'); return true;">
                        <i class="bi bi-person me-2"></i>Account
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-0"
                       href="{{ route('account.edit') }}"
                       data-bs-dismiss="offcanvas"
                       onclick="console.log('Settings clicked'); return true;">
                        <i class="bi bi-gear me-2"></i>Settings
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
                    <a class="nav-link px-0"
                       href="{{ route('account.transactions') }}"
                       data-bs-dismiss="offcanvas"
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
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            <i class="bi bi-cup-hot"></i><span class="fw-semibold">Green Cups</span>
        </a>

        <!-- Guest mobile toggler -> #guestNav -->
        <div class="d-lg-none">
            <button class="navbar-toggler rounded-3 p-2" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#guestNav"
                    aria-controls="guestNav" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>

        <!-- Desktop links -->
        <div class="d-none d-lg-flex ms-auto">
            <div class="navbar-nav">
                <a class="nav-link" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a>
                <a class="nav-link" href="{{ route('register') }}"><i class="bi bi-person-plus me-1"></i>Register</a>
            </div>
        </div>
    </div>

    <!-- Guest Offcanvas (mobile) -->
    <div class="offcanvas offcanvas-end d-lg-none" tabindex="-1" id="guestNav" aria-labelledby="guestNavLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="guestNavLabel">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="navbar-nav">
                <a class="nav-link" href="{{ route('login') }}" data-bs-dismiss="offcanvas">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Login
                </a>
                <a class="nav-link" href="{{ route('register') }}" data-bs-dismiss="offcanvas">
                    <i class="bi bi-person-plus me-1"></i>Register
                </a>
            </div>
        </div>
    </div>
</nav>
@endauth

{{-- Minimal styles for your rounded green theme --}}
<style>
    .points-badge{
        display:inline-flex; align-items:center; gap:.25rem;
        padding:.25rem .5rem; border-radius:999px;
        background:#e9f7ef; color:#198754; font-weight:600; font-size:.9rem;
        border:1px solid rgba(25,135,84,.15);
        line-height:1;
    }
    .user-avatar{
        width:36px; height:36px; border-radius:50%;
        display:inline-flex; align-items:center; justify-content:center;
        background:#198754; color:#fff; font-weight:700; letter-spacing:.5px;
    }
    .navbar .nav-link{
        border-radius:.75rem; padding:.5rem .75rem;
        position: relative;
        z-index: 1;
    }
    .navbar .nav-link.active,
    .navbar .nav-link:focus{
        background:rgba(25,135,84,.1);
        outline:0;
    }
    .offcanvas .nav-link{
        padding:.6rem 0;
        cursor: pointer;
        position: relative;
        z-index: 1;
        display: block;
        text-decoration: none;
    }
    .offcanvas .nav-link:hover {
        background: rgba(25,135,84,.1);
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
    }
    /* Debug styling - remove after testing */
    .offcanvas .nav-link:active {
        background: rgba(25,135,84,.2) !important;
    }
</style>

{{-- Safe helpers: auto-close offcanvas; add fallback if Bootstrap JS isn't loaded --}}
<script>
document.addEventListener('DOMContentLoaded', function(){
  console.log('Navbar script loaded');

  function initNav() {
    console.log('Initializing navbar');

    // Enhanced auto-close functionality for offcanvas navigation
    document.querySelectorAll('.offcanvas .nav-link, [data-bs-dismiss="offcanvas"]').forEach(function(el){
      el.addEventListener('click', function(e){
        console.log('Navigation link clicked:', el.textContent.trim());

        const oc = el.closest('.offcanvas');
        if (!oc) return;

        // If it's a navigation link (has href), ensure navigation happens
        if (el.hasAttribute('href') && el.getAttribute('href') !== '#') {
          const href = el.getAttribute('href');
          console.log('Navigating to:', href);

          // Close the offcanvas first
          if (window.bootstrap && bootstrap.Offcanvas) {
            const offcanvasInstance = bootstrap.Offcanvas.getOrCreateInstance(oc);
            offcanvasInstance.hide();

            // Navigate after a short delay to allow offcanvas to close
            setTimeout(function() {
              console.log('Delayed navigation to:', href);
              window.location.href = href;
            }, 150);
          } else {
            // Fallback: navigate immediately if Bootstrap is not available
            console.log('Bootstrap not available, direct navigation to:', href);
            window.location.href = href;
          }

          // Prevent default to handle navigation manually
          e.preventDefault();
        } else {
          // For non-navigation elements (like close buttons), just close the offcanvas
          if (window.bootstrap && bootstrap.Offcanvas) {
            bootstrap.Offcanvas.getOrCreateInstance(oc).hide();
          }
        }
      });
    });

    // Simpler fallback: direct click handling
    document.querySelectorAll('.offcanvas .nav-link[href]:not([href="#"])').forEach(function(link) {
      link.addEventListener('click', function(e) {
        console.log('Direct link handler triggered');
        const href = this.getAttribute('href');
        if (href) {
          e.preventDefault();
          e.stopPropagation();

          // Close mobile menu if visible
          const offcanvas = document.querySelector('.offcanvas.show, .offcanvas[style*="display: block"]');
          if (offcanvas) {
            offcanvas.style.display = 'none';
            offcanvas.classList.remove('show');
            document.body.classList.remove('modal-open');

            // Remove backdrop if exists
            const backdrop = document.querySelector('.offcanvas-backdrop');
            if (backdrop) backdrop.remove();
          }

          // Navigate
          setTimeout(() => {
            window.location.href = href;
          }, 100);
        }
      });
    });

    // Optional: stronger shadow after scroll
    const nav = document.querySelector('.navbar.fixed-top');
    if (nav) {
      document.addEventListener('scroll', function(){
        nav.classList.toggle('shadow-sm', window.scrollY < 4);
        nav.classList.toggle('shadow', window.scrollY >= 4);
      });
    }

    // Handle form submissions in offcanvas (like logout)
    document.querySelectorAll('.offcanvas form').forEach(function(form) {
      form.addEventListener('submit', function(e) {
        const oc = form.closest('.offcanvas');
        if (oc && window.bootstrap && bootstrap.Offcanvas) {
          bootstrap.Offcanvas.getOrCreateInstance(oc).hide();
        }
      });
    });
  }

  // Check if Bootstrap is loaded
  if (window.bootstrap && bootstrap.Offcanvas) {
    console.log('Bootstrap found, initializing');
    initNav();
  } else {
    console.log('Bootstrap not found, checking for script');
    // Fallback: load Bootstrap bundle only if it's missing (won't double-load)
    const existingScript = document.querySelector('script[src*="bootstrap"]');
    if (!existingScript) {
      console.log('Loading Bootstrap');
      const s = document.createElement('script');
      s.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js';
      s.onload = function() {
        console.log('Bootstrap loaded successfully');
        initNav();
      };
      s.onerror = function() {
        console.error('Failed to load Bootstrap');
        // Fallback navigation without Bootstrap
        initNav(); // Still init for fallback handlers
      };
      document.body.appendChild(s);
    } else {
      // Bootstrap script exists, wait for it to load
      console.log('Bootstrap script exists, waiting');
      setTimeout(initNav, 100);
    }
  }

  // Additional safety: ensure offcanvas can be toggled even if Bootstrap fails
  document.querySelectorAll('[data-bs-toggle="offcanvas"]').forEach(function(toggler) {
    toggler.addEventListener('click', function(e) {
      console.log('Offcanvas toggler clicked');
      if (!window.bootstrap || !bootstrap.Offcanvas) {
        // Simple fallback: show/hide the offcanvas manually
        const target = document.querySelector(toggler.getAttribute('data-bs-target'));
        if (target) {
          console.log('Manual toggle offcanvas');
          if (target.style.display === 'block' || target.classList.contains('show')) {
            target.style.display = 'none';
            target.classList.remove('show');
            document.body.classList.remove('modal-open');
          } else {
            target.style.display = 'block';
            target.classList.add('show');
            document.body.classList.add('modal-open');
          }
        }
      }
    });
  });
});
</script>
