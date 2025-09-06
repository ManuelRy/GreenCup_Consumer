<!-- Desktop Navigation -->
<nav class="desktop-nav">
    <div class="nav-container">
        <!-- Logo -->
        <div class="nav-brand">
            <a href="{{ route('dashboard') }}" class="brand-link">
                <i class="fas fa-leaf brand-icon"></i>
                <span class="brand-text">Green Cup</span>
            </a>
        </div>

        <!-- Navigation Links -->
        <div class="nav-links">
            <a href="{{ route('dashboard') }}" class="nav-link">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            
            <a href="{{ route('gallery') }}" class="nav-link">
                <i class="fas fa-images"></i>
                <span>Gallery</span>
            </a>
            
            <a href="{{ route('map') }}" class="nav-link">
                <i class="fas fa-map-marker-alt"></i>
                <span>Store Map</span>
            </a>
            
            <a href="{{ route('scan.receipt') }}" class="nav-link">
                <i class="fas fa-qrcode"></i>
                <span>Scan Receipt</span>
            </a>
            
            <a href="{{ route('consumer.qr-code') }}" class="nav-link">
                <i class="fas fa-id-card"></i>
                <span>My QR Code</span>
            </a>
        </div>

        <!-- User Menu -->
        <div class="nav-user">
            <div class="user-dropdown">
                <button class="user-btn" onclick="toggleUserMenu()">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-info">
                        <span class="user-name">{{ auth('consumer')->user()->full_name }}</span>
                        <span class="user-points">{{ number_format(auth('consumer')->user()->available_points ?? 0) }} pts</span>
                    </div>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </button>

                <div class="user-menu" id="user-menu">
                    <a href="{{ route('account') }}" class="menu-item">
                        <i class="fas fa-user-cog"></i>
                        <span>Account Settings</span>
                    </a>
                    
                    <a href="{{ route('account.transactions') }}" class="menu-item">
                        <i class="fas fa-history"></i>
                        <span>Transaction History</span>
                    </a>
                    
                    <div class="menu-divider"></div>
                    
                    <form action="{{ route('logout') }}" method="POST" class="logout-form">
                        @csrf
                        <button type="submit" class="menu-item logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Button -->
        <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</nav>

<!-- Mobile Menu Overlay -->
<div class="menu-overlay" id="menu-overlay"></div>

<!-- Mobile Navigation -->
<div class="mobile-nav" id="mobile-menu">
    <div class="mobile-nav-header">
        <div class="mobile-user">
            <div class="mobile-user-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="mobile-user-info">
                <h3>{{ auth('consumer')->user()->full_name }}</h3>
                <p>{{ number_format(auth('consumer')->user()->available_points ?? 0) }} points available</p>
            </div>
        </div>
        <button class="mobile-close-btn" onclick="toggleMobileMenu()">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="mobile-nav-links">
        <a href="{{ route('dashboard') }}" class="mobile-nav-link">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        
        <a href="{{ route('gallery') }}" class="mobile-nav-link">
            <i class="fas fa-images"></i>
            <span>Gallery</span>
        </a>
        
        <a href="{{ route('map') }}" class="mobile-nav-link">
            <i class="fas fa-map-marker-alt"></i>
            <span>Store Map</span>
        </a>
        
        <a href="{{ route('scan.receipt') }}" class="mobile-nav-link">
            <i class="fas fa-qrcode"></i>
            <span>Scan Receipt</span>
        </a>
        
        <a href="{{ route('consumer.qr-code') }}" class="mobile-nav-link">
            <i class="fas fa-id-card"></i>
            <span>My QR Code</span>
        </a>
        
        <a href="{{ route('account') }}" class="mobile-nav-link">
            <i class="fas fa-user-cog"></i>
            <span>Account Settings</span>
        </a>
        
        <a href="{{ route('account.transactions') }}" class="mobile-nav-link">
            <i class="fas fa-history"></i>
            <span>Transaction History</span>
        </a>
    </div>

    <div class="mobile-nav-footer">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="mobile-logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</div>

<script>
// User dropdown menu toggle
function toggleUserMenu() {
    const userMenu = document.getElementById('user-menu');
    userMenu.classList.toggle('active');
}

// Close user menu when clicking outside
document.addEventListener('click', function(e) {
    const userDropdown = document.querySelector('.user-dropdown');
    const userMenu = document.getElementById('user-menu');
    
    if (!userDropdown.contains(e.target)) {
        userMenu.classList.remove('active');
    }
});
</script>
