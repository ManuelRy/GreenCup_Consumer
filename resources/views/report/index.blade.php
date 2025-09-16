@extends('master')

@section('content')
<div class="container-fluid min-vh-100 py-3">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">

            <!-- Page Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm bg-gradient-primary text-white rounded-4">
                        <div class="card-body py-4 text-center">
                            <div class="mb-3">
                                <i class="fas fa-exclamation-triangle fa-3x opacity-90"></i>
                            </div>
                            <h2 class="fw-bold mb-2">Report an Issue</h2>
                            <p class="fw-light opacity-90 mb-0">Help us improve your Green Cups experience</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Form -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-0 pb-0">
                            <h5 class="fw-semibold text-dark mb-0">
                                <i class="fas fa-edit text-primary me-2"></i>
                                Submit Report
                            </h5>
                        </div>
                        <div class="card-body">
                            <form id="reportForm" method="POST" action="#">
                                @csrf

                                <!-- Report Type Selection -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-dark">
                                            <i class="fas fa-list me-2"></i>What type of issue are you reporting?
                                        </label>
                                        <div class="row g-3">
                                            <div class="col-6 col-md-4">
                                                <input type="radio" class="btn-check" name="tag" id="app-bug" value="app-bug" required>
                                                <label class="btn btn-outline-primary w-100 py-3 report-type-btn" for="app-bug">
                                                    <div class="fs-2 mb-2">
                                                        <i class="fas fa-bug"></i>
                                                    </div>
                                                    <div class="fw-semibold small">App Bug</div>
                                                </label>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <input type="radio" class="btn-check" name="tag" id="store-issue" value="store-issue" required>
                                                <label class="btn btn-outline-primary w-100 py-3 report-type-btn" for="store-issue">
                                                    <div class="fs-2 mb-2">
                                                        <i class="fas fa-store"></i>
                                                    </div>
                                                    <div class="fw-semibold small">Store Issue</div>
                                                </label>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <input type="radio" class="btn-check" name="tag" id="payment-problem" value="payment-problem" required>
                                                <label class="btn btn-outline-primary w-100 py-3 report-type-btn" for="payment-problem">
                                                    <div class="fs-2 mb-2">
                                                        <i class="fas fa-credit-card"></i>
                                                    </div>
                                                    <div class="fw-semibold small">Payment</div>
                                                </label>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <input type="radio" class="btn-check" name="tag" id="account-problem" value="account-problem" required>
                                                <label class="btn btn-outline-primary w-100 py-3 report-type-btn" for="account-problem">
                                                    <div class="fs-2 mb-2">
                                                        <i class="fas fa-user-circle"></i>
                                                    </div>
                                                    <div class="fw-semibold small">Account</div>
                                                </label>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <input type="radio" class="btn-check" name="tag" id="scanning-issue" value="scanning-issue" required>
                                                <label class="btn btn-outline-primary w-100 py-3 report-type-btn" for="scanning-issue">
                                                    <div class="fs-2 mb-2">
                                                        <i class="fas fa-qrcode"></i>
                                                    </div>
                                                    <div class="fw-semibold small">QR Scan</div>
                                                </label>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <input type="radio" class="btn-check" name="tag" id="other" value="other" required>
                                                <label class="btn btn-outline-primary w-100 py-3 report-type-btn" for="other">
                                                    <div class="fs-2 mb-2">
                                                        <i class="fas fa-question-circle"></i>
                                                    </div>
                                                    <div class="fw-semibold small">Other</div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Priority Level -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-dark">
                                            <i class="fas fa-exclamation-circle me-2"></i>How urgent is this issue?
                                        </label>
                                        <div class="row g-2">
                                            <div class="col-6 col-md-3">
                                                <input type="radio" class="btn-check" name="priority" id="low" value="low" required>
                                                <label class="btn btn-outline-success w-100 py-2" for="low">
                                                    <i class="fas fa-circle me-1"></i>Low
                                                </label>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <input type="radio" class="btn-check" name="priority" id="normal" value="normal" required>
                                                <label class="btn btn-outline-primary w-100 py-2" for="normal">
                                                    <i class="fas fa-circle me-1"></i>Normal
                                                </label>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <input type="radio" class="btn-check" name="priority" id="high" value="high" required>
                                                <label class="btn btn-outline-warning w-100 py-2" for="high">
                                                    <i class="fas fa-circle me-1"></i>High
                                                </label>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <input type="radio" class="btn-check" name="priority" id="critical" value="critical" required>
                                                <label class="btn btn-outline-danger w-100 py-2" for="critical">
                                                    <i class="fas fa-circle me-1"></i>Critical
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Report Title -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <label for="title" class="form-label fw-semibold text-dark">
                                            <i class="fas fa-heading me-2"></i>Issue Title
                                        </label>
                                        <input type="text"
                                               class="form-control form-control-lg"
                                               id="title"
                                               name="title"
                                               placeholder="Brief summary of the issue..."
                                               maxlength="100"
                                               required>
                                        <div class="form-text">
                                            <small class="text-muted">
                                                <span id="titleCounter">0</span>/100 characters
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Detailed Description -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <label for="description" class="form-label fw-semibold text-dark">
                                            <i class="fas fa-align-left me-2"></i>Detailed Description
                                        </label>
                                        <textarea class="form-control"
                                                  id="description"
                                                  name="description"
                                                  rows="6"
                                                  placeholder="Please provide detailed information about the issue. Include:&#10;• What were you trying to do?&#10;• What went wrong?&#10;• When did this happen?&#10;• Any error messages you saw?&#10;• Steps to reproduce the issue (if applicable)"
                                                  maxlength="1000"
                                                  required></textarea>
                                        <div class="form-text">
                                            <small class="text-muted">
                                                <span id="descCounter">0</span>/1000 characters
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Preference -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="card bg-light border-0">
                                            <div class="card-body py-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="followUp" name="allow_follow_up" value="1" checked>
                                                    <label class="form-check-label fw-semibold text-dark" for="followUp">
                                                        <i class="fas fa-envelope me-2"></i>
                                                        Allow follow-up communication
                                                    </label>
                                                    <div class="form-text mt-1">
                                                        <small class="text-muted">We may contact you for additional information or to provide updates on your report.</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Actions -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex gap-3 justify-content-end">
                                            <a href="#" class="btn btn-outline-secondary">
                                                <i class="fas fa-times me-2"></i>Cancel
                                            </a>
                                            <button type="submit" class="btn btn-primary px-4">
                                                <i class="fas fa-paper-plane me-2"></i>Submit Report
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Section -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-0 pb-0">
                            <h5 class="fw-semibold text-dark mb-0">
                                <i class="fas fa-info-circle text-info me-2"></i>
                                Before You Report
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-12 col-md-4">
                                    <div class="text-center">
                                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-search fa-lg text-primary"></i>
                                        </div>
                                        <h6 class="fw-semibold text-dark mb-2">Check FAQs</h6>
                                        <p class="text-muted small mb-0">Common issues might already have solutions in our help center.</p>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="text-center">
                                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-redo fa-lg text-warning"></i>
                                        </div>
                                        <h6 class="fw-semibold text-dark mb-2">Try Again</h6>
                                        <p class="text-muted small mb-0">Sometimes a simple refresh or retry can resolve temporary issues.</p>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="text-center">
                                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-clock fa-lg text-success"></i>
                                        </div>
                                        <h6 class="fw-semibold text-dark mb-2">Response Time</h6>
                                        <p class="text-muted small mb-0">We typically respond within 24-48 hours during business days.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom CSS consistent with dashboard theme */
:root {
    --bs-primary: #1dd1a1;
    --bs-primary-rgb: 29, 209, 161;
    --bs-success: #22c55e;
    --bs-danger: #ef4444;
    --bs-warning: #f59e0b;
    --bs-info: #06b6d4;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #1dd1a1, #10ac84) !important;
}

.btn-primary {
    background: linear-gradient(135deg, #1dd1a1, #10ac84);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(29, 209, 161, 0.3);
    background: linear-gradient(135deg, #10ac84, #0e8e71);
}

.btn-outline-primary {
    border-color: #1dd1a1;
    color: #1dd1a1;
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    background: #1dd1a1;
    border-color: #1dd1a1;
    transform: translateY(-1px);
}

.btn-outline-primary:checked {
    background: #1dd1a1;
    border-color: #1dd1a1;
    color: white;
}

.report-type-btn {
    transition: all 0.3s ease;
    height: 100px;
}

.report-type-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.btn-check:checked + .report-type-btn {
    background: #1dd1a1;
    border-color: #1dd1a1;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(29, 209, 161, 0.3);
}

/* Form enhancements */
.form-control {
    border-radius: 0.5rem;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #1dd1a1;
    box-shadow: 0 0 0 0.2rem rgba(29, 209, 161, 0.25);
}

/* Priority buttons */
.btn-outline-success:checked {
    background: #22c55e;
    border-color: #22c55e;
}

.btn-outline-warning:checked {
    background: #f59e0b;
    border-color: #f59e0b;
}

.btn-outline-danger:checked {
    background: #ef4444;
    border-color: #ef4444;
}

/* Card animations */
.card {
    animation: slideUp 0.6s ease-out;
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12) !important;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Character counters */
#titleCounter, #descCounter {
    font-weight: 600;
    color: #1dd1a1;
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }

    .report-type-btn {
        height: 80px;
        padding: 0.5rem !important;
    }

    .report-type-btn .fs-2 {
        font-size: 1.5rem !important;
    }

    .card-body {
        padding: 1rem;
    }
}

/* Loading state */
.btn:disabled {
    opacity: 0.6;
    transform: none !important;
}

/* Focus indicators for accessibility */
.btn:focus,
.form-control:focus {
    outline: 2px solid #1dd1a1;
    outline-offset: 2px;
}

/* Enhanced visual feedback */
.form-check-input:checked {
    background-color: #1dd1a1;
    border-color: #1dd1a1;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counters
    const titleInput = document.getElementById('title');
    const descInput = document.getElementById('description');
    const titleCounter = document.getElementById('titleCounter');
    const descCounter = document.getElementById('descCounter');

    function updateCounter(input, counter) {
        const count = input.value.length;
        const max = parseInt(input.getAttribute('maxlength'));
        counter.textContent = count;

        // Color coding
        if (count > max * 0.9) {
            counter.style.color = '#ef4444';
        } else if (count > max * 0.7) {
            counter.style.color = '#f59e0b';
        } else {
            counter.style.color = '#1dd1a1';
        }
    }

    titleInput.addEventListener('input', () => updateCounter(titleInput, titleCounter));
    descInput.addEventListener('input', () => updateCounter(descInput, descCounter));

    // Basic form interaction
    const form = document.getElementById('reportForm');
    const submitBtn = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Add loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';

        // Reset after demo
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit Report';
            alert('Form submitted! (This is just a demo)');
        }, 2000);
    });

    // Animate cards on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe all cards
    document.querySelectorAll('.card').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `all 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });

    // Touch feedback for mobile
    if ('ontouchstart' in window) {
        document.addEventListener('touchstart', function(e) {
            if (e.target.closest('.report-type-btn, .btn')) {
                e.target.closest('.report-type-btn, .btn').style.transform = 'scale(0.98)';
            }
        }, { passive: true });

        document.addEventListener('touchend', function(e) {
            if (e.target.closest('.report-type-btn, .btn')) {
                setTimeout(() => {
                    const btn = e.target.closest('.report-type-btn, .btn');
                    if (btn && !btn.classList.contains('btn-check:checked')) {
                        btn.style.transform = '';
                    }
                }, 100);
            }
        }, { passive: true });
    }
});
</script>
@endsection
