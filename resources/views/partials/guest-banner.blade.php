{{-- Guest Mode Banner - Informational banner encouraging users to sign up --}}
<div class="alert alert-info alert-dismissible fade show border-0 mb-4" role="alert" style="background: linear-gradient(135deg, #1dd1a1 0%, #10ac84 100%); color: white; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 8px rgba(16, 172, 132, 0.3);">
    <div class="d-flex align-items-center">
        <div class="flex-shrink-0 me-3">
            <i class="bi bi-info-circle-fill" style="font-size: 2rem;"></i>
        </div>
        <div class="flex-grow-1">
            <h5 class="alert-heading mb-1 fw-bold">👋 You're Browsing as a Guest</h5>
            <p class="mb-2 small">Create a free account to track your environmental impact, earn rewards, and scan receipts!</p>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('register') }}" class="btn btn-light btn-sm fw-semibold">
                    <i class="bi bi-person-plus me-1"></i>Sign Up Free
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm fw-semibold">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Login
                </a>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>