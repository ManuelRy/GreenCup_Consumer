@extends('layout.master')

@section('title', 'Responsive Layout Example - Green Cup')

@push('styles')
<style>
    .feature-card {
        transition: transform 0.3s ease;
    }
    
    .feature-card:hover {
        transform: translateY(-5px);
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="bg-primary text-white rounded-3 p-4 p-md-5 text-center">
                <h1 class="display-5 fw-bold mb-3">Welcome to Green Cup</h1>
                <p class="lead mb-4">Your eco-friendly rewards app with responsive design</p>
                <button class="btn btn-light btn-lg" onclick="showToast('Welcome! This is a responsive layout demo.', 'success')">
                    <i class="bi bi-cup-hot"></i> Get Started
                </button>
            </div>
        </div>
    </div>
    
    <!-- Features Grid -->
    <div class="row g-4 mb-5">
        <div class="col-12 mb-3">
            <h2 class="h3 text-center">App Features</h2>
            <p class="text-muted text-center">Explore what makes Green Cup special</p>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 feature-card shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-geo-alt text-primary fs-1 mb-3"></i>
                    <h5 class="card-title">Store Locator</h5>
                    <p class="card-text">Find eco-friendly stores near you with our interactive map.</p>
                    <a href="{{ route('map') }}" class="btn btn-outline-primary">Explore Map</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 feature-card shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-qr-code-scan text-success fs-1 mb-3"></i>
                    <h5 class="card-title">Scan Receipts</h5>
                    <p class="card-text">Earn points by scanning your eco-friendly purchases.</p>
                    <a href="{{ route('scan.receipt') }}" class="btn btn-outline-success">Start Scanning</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 feature-card shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-images text-warning fs-1 mb-3"></i>
                    <h5 class="card-title">Gallery</h5>
                    <p class="card-text">Browse through our collection of eco-friendly products.</p>
                    <a href="{{ route('gallery') }}" class="btn btn-outline-warning">View Gallery</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 feature-card shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-person-circle text-info fs-1 mb-3"></i>
                    <h5 class="card-title">My Account</h5>
                    <p class="card-text">Manage your profile, points, and transaction history.</p>
                    <a href="{{ route('account') }}" class="btn btn-outline-info">My Profile</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Responsive Grid Demo -->
    <div class="row mb-5">
        <div class="col-12 mb-3">
            <h2 class="h3 text-center">Responsive Design Demo</h2>
            <p class="text-muted text-center">This layout adapts to all screen sizes</p>
        </div>
        
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-phone text-primary fs-2"></i>
                    <h6 class="mt-2">Mobile First</h6>
                    <small class="text-muted">Optimized for mobile devices</small>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-tablet text-success fs-2"></i>
                    <h6 class="mt-2">Tablet Ready</h6>
                    <small class="text-muted">Perfect for tablet viewing</small>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-laptop text-warning fs-2"></i>
                    <h6 class="mt-2">Desktop</h6>
                    <small class="text-muted">Full desktop experience</small>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-bootstrap text-info fs-2"></i>
                    <h6 class="mt-2">Bootstrap 5</h6>
                    <small class="text-muted">Latest Bootstrap framework</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Interactive Demo -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Interactive Components Demo</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <button class="btn btn-primary w-100" onclick="showLoading(); setTimeout(hideLoading, 2000)">
                                <i class="bi bi-arrow-clockwise"></i> Test Loading Spinner
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-success w-100" onclick="showToast('This is a success message!', 'success')">
                                <i class="bi bi-check-circle"></i> Show Success Toast
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-warning w-100" onclick="showToast('This is a warning message!', 'warning')">
                                <i class="bi bi-exclamation-triangle"></i> Show Warning Toast
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-info w-100" data-bs-toggle="modal" data-bs-target="#demoModal">
                                <i class="bi bi-window"></i> Open Modal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Demo Modal -->
<div class="modal fade" id="demoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-info-circle text-primary"></i> Bootstrap Modal Demo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>This is a responsive Bootstrap modal that works perfectly on all devices!</p>
                <div class="alert alert-info" role="alert">
                    <i class="bi bi-lightbulb"></i>
                    <strong>Tip:</strong> The responsive layout automatically adapts to different screen sizes.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Got it!</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add some interactive animations
        const cards = document.querySelectorAll('.feature-card');
        
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.boxShadow = '0 10px 25px rgba(0,0,0,0.1)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.boxShadow = '';
            });
        });
        
        console.log('Green Cup Responsive Layout Loaded Successfully!');
    });
</script>
@endpush
