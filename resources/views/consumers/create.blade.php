<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - GreenCup</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 50%, #10b981 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .auth-container {
      width: 100%;
      max-width: 520px;
      background: white;
      border-radius: 24px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      padding: 40px;
    }

    .brand-header {
      text-align: center;
      margin-bottom: 40px;
    }

    .brand-logo {
      width: 80px;
      height: 80px;
      background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 50%, #10b981 100%);
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 40px;
      color: white;
      margin: 0 auto 20px;
      animation: logoFloat 3s ease-in-out infinite;
    }

    @keyframes logoFloat {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-8px); }
    }

    .brand-header h1 {
      font-size: 32px;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 8px;
    }

    .brand-header p {
      color: #6b7280;
      font-size: 15px;
    }

    .alert {
      border: none;
      border-radius: 12px;
      padding: 16px 20px;
      margin-bottom: 24px;
      display: flex;
      align-items: flex-start;
      gap: 12px;
      animation: slideIn 0.4s ease;
    }

    @keyframes slideIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .alert-danger {
      background: #fee2e2;
      color: #991b1b;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-label {
      display: block;
      font-weight: 600;
      font-size: 14px;
      color: #374151;
      margin-bottom: 8px;
    }

    .input-group {
      position: relative;
    }

    .input-icon {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: #9ca3af;
      font-size: 18px;
      pointer-events: none;
    }

    .form-control {
      width: 100%;
      height: 52px;
      padding: 0 16px 0 50px;
      border: 2px solid #e5e7eb;
      border-radius: 12px;
      font-size: 15px;
      transition: all 0.3s ease;
      background: #f9fafb;
    }

    .form-control:focus {
      outline: none;
      border-color: #10b981;
      background: #ffffff;
      box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
    }

    .form-control.is-invalid {
      border-color: #ef4444;
      background: #fef2f2;
    }

    .password-toggle {
      position: absolute;
      right: 16px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #9ca3af;
      cursor: pointer;
      padding: 8px;
      font-size: 18px;
      transition: color 0.2s;
    }

    .password-toggle:hover {
      color: #10b981;
    }

    .invalid-feedback {
      color: #dc2626;
      font-size: 13px;
      margin-top: 6px;
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .btn-primary {
      width: 100%;
      height: 54px;
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      border: none;
      border-radius: 12px;
      color: white;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 14px rgba(16, 185, 129, 0.4);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      margin-top: 24px;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5);
    }

    .btn-primary:active {
      transform: translateY(0);
    }

    .divider {
      display: flex;
      align-items: center;
      margin: 30px 0;
      color: #9ca3af;
      font-size: 14px;
    }

    .divider::before,
    .divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: #e5e7eb;
    }

    .divider span {
      padding: 0 20px;
    }

    .btn-outline {
      width: 100%;
      height: 52px;
      background: white;
      border: 2px solid #e5e7eb;
      border-radius: 12px;
      color: #374151;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      text-decoration: none;
    }

    .btn-outline:hover {
      background: #f9fafb;
      border-color: #10b981;
      color: #10b981;
      transform: translateY(-2px);
    }

    .form-footer {
      text-align: center;
      margin-top: 30px;
      padding-top: 30px;
      border-top: 1px solid #e5e7eb;
    }

    .form-footer p {
      color: #6b7280;
      margin-bottom: 8px;
      font-size: 14px;
    }

    .form-footer a {
      color: #10b981;
      font-weight: 600;
      text-decoration: none;
      font-size: 15px;
      transition: color 0.2s;
    }

    .form-footer a:hover {
      color: #059669;
    }

    .spinner {
      width: 20px;
      height: 20px;
      border: 3px solid rgba(255, 255, 255, 0.3);
      border-top-color: white;
      border-radius: 50%;
      animation: spin 0.6s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    @media (max-width: 576px) {
      .auth-container {
        padding: 30px 20px;
      }

      .brand-logo {
        width: 70px;
        height: 70px;
        font-size: 35px;
      }

      .brand-header h1 {
        font-size: 26px;
      }

      .form-row {
        grid-template-columns: 1fr;
      }

      .form-control,
      .btn-primary,
      .btn-outline {
        height: 50px;
      }
    }
  </style>
</head>
<body>
  <div class="auth-container">
    <div class="brand-header">
      <div class="brand-logo">
        <i class="bi bi-cup-hot"></i>
      </div>
      <h1>Create Account</h1>
      <p>Start your eco-journey today</p>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
    <div class="alert alert-danger">
      <i class="bi bi-exclamation-triangle-fill" style="font-size: 20px;"></i>
      <div>
        <strong>Please fix the following errors:</strong>
        <ul style="margin: 8px 0 0 0; padding-left: 20px;">
          @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    </div>
    @endif

    <!-- Registration Form -->
    <form id="registerForm" action="{{ route('register.store') }}" method="POST" novalidate>
      @csrf

      <!-- Name Row -->
      <div class="form-row">
        <div class="form-group">
          <label for="first_name" class="form-label">First Name</label>
          <div class="input-group">
            <i class="bi bi-person input-icon"></i>
            <input
              type="text"
              id="first_name"
              name="first_name"
              value="{{ old('first_name') }}"
              class="form-control @error('first_name') is-invalid @enderror"
              placeholder="John"
              required
            >
          </div>
          @error('first_name')
          <div class="invalid-feedback">
            <i class="bi bi-exclamation-circle"></i> {{ $message }}
          </div>
          @enderror
        </div>

        <div class="form-group">
          <label for="last_name" class="form-label">Last Name</label>
          <div class="input-group">
            <i class="bi bi-person input-icon"></i>
            <input
              type="text"
              id="last_name"
              name="last_name"
              value="{{ old('last_name') }}"
              class="form-control @error('last_name') is-invalid @enderror"
              placeholder="Doe"
              required
            >
          </div>
          @error('last_name')
          <div class="invalid-feedback">
            <i class="bi bi-exclamation-circle"></i> {{ $message }}
          </div>
          @enderror
        </div>
      </div>

      <!-- Email -->
      <div class="form-group">
        <label for="email" class="form-label">Email Address</label>
        <div class="input-group">
          <i class="bi bi-envelope input-icon"></i>
          <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email') }}"
            class="form-control @error('email') is-invalid @enderror"
            placeholder="your@email.com"
            required
            autocomplete="email"
          >
        </div>
        @error('email')
        <div class="invalid-feedback">
          <i class="bi bi-exclamation-circle"></i> {{ $message }}
        </div>
        @enderror
      </div>

      <!-- Phone -->
      <div class="form-group">
        <label for="phone_number" class="form-label">Phone Number</label>
        <div class="input-group">
          <i class="bi bi-phone input-icon"></i>
          <input
            type="tel"
            id="phone_number"
            name="phone_number"
            value="{{ old('phone_number') }}"
            class="form-control @error('phone_number') is-invalid @enderror"
            placeholder="+1234567890"
            required
          >
        </div>
        @error('phone_number')
        <div class="invalid-feedback">
          <i class="bi bi-exclamation-circle"></i> {{ $message }}
        </div>
        @enderror
      </div>

      <!-- Password -->
      <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
          <i class="bi bi-lock input-icon"></i>
          <input
            type="password"
            id="password"
            name="password"
            class="form-control @error('password') is-invalid @enderror"
            placeholder="Create a strong password"
            required
          >
          <button type="button" class="password-toggle" onclick="togglePassword('password', 'toggleIcon1')">
            <i class="bi bi-eye" id="toggleIcon1"></i>
          </button>
        </div>
        @error('password')
        <div class="invalid-feedback">
          <i class="bi bi-exclamation-circle"></i> {{ $message }}
        </div>
        @enderror
      </div>

      <!-- Confirm Password -->
      <div class="form-group">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <div class="input-group">
          <i class="bi bi-lock input-icon"></i>
          <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            class="form-control @error('password_confirmation') is-invalid @enderror"
            placeholder="Confirm your password"
            required
          >
          <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', 'toggleIcon2')">
            <i class="bi bi-eye" id="toggleIcon2"></i>
          </button>
        </div>
        @error('password_confirmation')
        <div class="invalid-feedback">
          <i class="bi bi-exclamation-circle"></i> {{ $message }}
        </div>
        @enderror
      </div>

      <!-- Submit Button -->
      <button type="submit" class="btn-primary" id="submitBtn">
        <span id="btnText">Create Account</span>
        <i class="bi bi-arrow-right" id="btnIcon"></i>
      </button>
    </form>

    <!-- Divider -->
    <div class="divider">
      <span>or</span>
    </div>

    <!-- Guest Mode -->
    <a href="{{ route('guest.dashboard') }}" class="btn-outline">
      <i class="bi bi-eye"></i>
      Continue as Guest
    </a>

    <!-- Footer -->
    <div class="form-footer">
      <p>Already have an account?</p>
      <a href="{{ route('login') }}">Sign In <i class="bi bi-arrow-right"></i></a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function togglePassword(inputId, iconId) {
      const input = document.getElementById(inputId);
      const icon = document.getElementById(iconId);

      if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
      } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
      }
    }

    document.getElementById('registerForm').addEventListener('submit', function() {
      const btn = document.getElementById('submitBtn');
      const btnText = document.getElementById('btnText');
      const btnIcon = document.getElementById('btnIcon');

      btnText.textContent = 'Creating Account...';
      btnIcon.outerHTML = '<div class="spinner"></div>';
      btn.style.opacity = '0.7';
      btn.style.pointerEvents = 'none';
    });

    // Auto-dismiss alerts
    setTimeout(() => {
      document.querySelectorAll('.alert').forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
      });
    }, 8000);
  </script>
</body>
</html>