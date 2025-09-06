<!-- Bottom Navigation for Mobile -->
<div class="bottom-nav">
    <a href="{{ route('dashboard') }}" class="bottom-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fas fa-home"></i>
        <span>Home</span>
    </a>
    
    <a href="{{ route('gallery') }}" class="bottom-nav-item {{ request()->routeIs('gallery') ? 'active' : '' }}">
        <i class="fas fa-images"></i>
        <span>Gallery</span>
    </a>
    
    <a href="{{ route('scan.receipt') }}" class="bottom-nav-item scan-highlight {{ request()->routeIs('scan.receipt') || request()->routeIs('scan') ? 'active' : '' }}">
        <div class="scan-icon">
            <i class="fas fa-qrcode"></i>
        </div>
        <span>Scan</span>
    </a>
    
    <a href="{{ route('map') }}" class="bottom-nav-item {{ request()->routeIs('map') ? 'active' : '' }}">
        <i class="fas fa-map-marker-alt"></i>
        <span>Map</span>
    </a>
    
    <a href="{{ route('account') }}" class="bottom-nav-item {{ request()->routeIs('account*') ? 'active' : '' }}">
        <i class="fas fa-user"></i>
        <span>Account</span>
    </a>
</div>
