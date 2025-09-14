@extends('master')

@section('content')
<div class="container-fluid bg-light min-vh-100 py-3">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6 col-xl-5">
            <!-- Main Card -->
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">

                <!-- Header -->
                <div class="card-header bg-gradient-primary text-white p-0">
                    <div class="d-flex align-items-center justify-content-between p-3">
                        <a href="{{ route('account') }}"
                           class="btn btn-light btn-sm rounded-circle p-2 d-flex align-items-center justify-content-center text-decoration-none"
                           style="width: 40px; height: 40px;"
                           aria-label="Back to Account">
                            <i class="fas fa-chevron-left"></i>
                        </a>

                        <h1 class="h5 mb-0 fw-semibold text-center flex-grow-1 mx-3">Edit Profile</h1>

                        <div style="width: 40px; height: 40px;"></div> <!-- Spacer -->
                    </div>
                </div>

                <!-- Form Body -->
                <div class="card-body p-0">
                    <form action="{{ route('account.profile.update') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')

                        <!-- Personal Information Section -->
                        <div class="p-4">
                            <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                                <i class="fas fa-user-circle text-primary me-2"></i>
                                <h2 class="h6 mb-0 fw-semibold text-dark">Personal Information</h2>
                            </div>

                            <!-- Full Name -->
                            <div class="mb-3">
                                <label for="full_name" class="form-label fw-medium text-dark">
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control form-control-lg @error('full_name') is-invalid @enderror"
                                       id="full_name"
                                       name="full_name"
                                       value="{{ old('full_name', $consumer->full_name) }}"
                                       required>
                                @error('full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-medium text-dark">Email Address</label>
                                <input type="email"
                                       class="form-control form-control-lg bg-light"
                                       id="email"
                                       name="email"
                                       value="{{ $consumer->email }}"
                                       readonly>
                                <div class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>Email cannot be changed
                                </div>
                            </div>

                            <!-- Phone Number -->
                            <div class="mb-3">
                                <label for="phone_number" class="form-label fw-medium text-dark">
                                    Phone Number
                                    <span class="badge bg-secondary ms-1" style="font-size: 0.7em;">Optional</span>
                                </label>
                                <input type="tel"
                                       class="form-control form-control-lg @error('phone_number') is-invalid @enderror"
                                       id="phone_number"
                                       name="phone_number"
                                       value="{{ old('phone_number', $consumer->phone_number) }}"
                                       placeholder="+1 (555) 000-0000"
                                       minlength="8" maxlength="20">
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Gender -->
                            <div class="mb-3">
                                <label for="gender" class="form-label fw-medium text-dark">
                                    Gender <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-lg @error('gender') is-invalid @enderror"
                                        id="gender"
                                        name="gender"
                                        required>
                                    <option value="" disabled>Choose your gender</option>
                                    <option value="male" {{ old('gender', $consumer->gender) == 'male' ? 'selected' : '' }}>
                                        Male
                                    </option>
                                    <option value="female" {{ old('gender', $consumer->gender) == 'female' ? 'selected' : '' }}>
                                        Female
                                    </option>
                                    <option value="other" {{ old('gender', $consumer->gender) == 'other' ? 'selected' : '' }}>
                                        Other
                                    </option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Date of Birth -->
                            <div class="mb-4">
                                <label for="date_of_birth" class="form-label fw-medium text-dark">
                                    Date of Birth
                                    <span class="badge bg-secondary ms-1" style="font-size: 0.7em;">Optional</span>
                                </label>
                                <input type="date"
                                       class="form-control form-control-lg @error('date_of_birth') is-invalid @enderror"
                                       id="date_of_birth"
                                       name="date_of_birth"
                                       value="{{ old('date_of_birth', $consumer->date_of_birth ? $consumer->date_of_birth->format('Y-m-d') : '') }}"
                                       max="{{ date('Y-m-d', strtotime('-1 day')) }}">
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card-footer bg-light border-top p-4">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6 order-2 order-sm-1">
                                    <a href="{{ route('account') }}"
                                       class="btn btn-outline-secondary btn-lg w-100 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-times me-2"></i>
                                        Cancel
                                    </a>
                                </div>
                                <div class="col-12 col-sm-6 order-1 order-sm-2">
                                    <button type="submit"
                                            class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-save me-2"></i>
                                        Save Changes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom CSS for enhanced styling */
:root {
    --bs-primary: #1dd1a1;
    --bs-primary-rgb: 29, 209, 161;
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

.btn-primary:active {
    transform: translateY(0);
}

.btn-outline-secondary {
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    transform: translateY(-1px);
}

.form-control:focus,
.form-select:focus {
    border-color: #1dd1a1;
    box-shadow: 0 0 0 0.2rem rgba(29, 209, 161, 0.25);
}

.card {
    border-radius: 1rem !important;
}

.form-control-lg,
.form-select-lg {
    border-radius: 0.5rem;
    border-width: 2px;
}

/* Loading state */
.btn:disabled {
    opacity: 0.6;
    transform: none !important;
}

/* Mobile optimizations */
@media (max-width: 576px) {
    .container-fluid {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }

    .card-body .p-4 {
        padding: 1.5rem !important;
    }

    .card-footer {
        padding: 1.5rem !important;
    }

    .btn-lg {
        font-size: 1rem;
        padding: 0.75rem 1rem;
    }

    .form-control-lg,
    .form-select-lg {
        font-size: 1rem;
        padding: 0.75rem;
    }
}

/* Extra small devices */
@media (max-width: 375px) {
    .card-body .p-4 {
        padding: 1rem !important;
    }

    .card-footer {
        padding: 1rem !important;
    }

    .h5 {
        font-size: 1rem !important;
    }
}

/* Success/Error animations */
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.is-invalid {
    animation: shake 0.5s ease-in-out;
}

/* Smooth form appearance */
.card {
    animation: slideUp 0.4s ease-out;
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

/* Improved focus indicators for accessibility */
.btn:focus,
.form-control:focus,
.form-select:focus {
    outline: 2px solid #1dd1a1;
    outline-offset: 2px;
}

/* Better hover states for touch devices */
@media (hover: hover) {
    .form-control:hover {
        border-color: #1dd1a1;
    }

    .form-select:hover {
        border-color: #1dd1a1;
    }
}
</style>

<!-- Add Font Awesome if not already included -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<!-- Bootstrap Form Validation Script -->
<script>
// Bootstrap form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// Enhanced mobile experience
document.addEventListener('DOMContentLoaded', function() {
    // Auto-resize textarea on mobile
    if (window.innerWidth <= 768) {
        const inputs = document.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                setTimeout(() => {
                    this.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            });
        });
    }
});
</script>
@endsection
