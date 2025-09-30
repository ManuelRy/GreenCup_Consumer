<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - GreenCup</title>
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
      height: 100vh;
      overflow: hidden;
    }

    .auth-container {
      display: flex;
      height: 100vh;
    }

    /* Left Side - Image/Branding */
    .auth-left {
      flex: 1;
      background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 50%, #10b981 100%);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 60px;
      position: relative;
      overflow: hidden;
    }

    .auth-left::before {
      content: '';
      position: absolute;
      width: 500px;
      height: 500px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      top: -200px;
      right: -200px;
      animation: float 8s ease-in-out infinite;
    }

    .auth-left::after {
      content: '';
      position: absolute;
      width: 300px;
      height: 300px;
      background: rgba(255, 255, 255, 0.08);
      border-radius: 50%;
      bottom: -100px;
      left: -100px;
      animation: float 10s ease-in-out infinite reverse;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0) rotate(0deg); }
      50% { transform: translateY(-30px) rotate(180deg); }
    }

    .brand-content {
      text-align: center;
      color: white;
      position: relative;
      z-index: 10;
    }

    .brand-logo {
      width: 120px;
      height: 120px;
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      border-radius: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 50px;
      margin: 0 auto 30px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
      animation: logoFloat 3s ease-in-out infinite;
    }

    @keyframes logoFloat {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }

    .brand-content h1 {
      font-size: 48px;
      font-weight: 700;
      margin-bottom: 20px;
      text-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
    }

    .brand-content p {
      font-size: 18px;
      opacity: 0.95;
      line-height: 1.6;
      max-width: 400px;
      margin: 0 auto;
    }

    .features-list {
      margin-top: 50px;
      text-align: left;
    }

    .feature-item {
      display: flex;
      align-items: center;
      gap: 15px;
      margin-bottom: 20px;
      padding: 15px;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      transition: all 0.3s ease;
    }

    .feature-item:hover {
      background: rgba(255, 255, 255, 0.15);
      transform: translateX(10px);
    }

    .feature-icon {
      width: 50px;
      height: 50px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      flex-shrink: 0;
    }

    /* Right Side - Form */
    .auth-right {
      flex: 1;
      background: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px;
      overflow-y: auto;
    }

    .form-container {
      width: 100%;
      max-width: 450px;
    }

    .form-header {
      margin-bottom: 40px;
    }

    .form-header h2 {
      font-size: 32px;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 10px;
    }

    .form-header p {
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

    .alert-success {
      background: #d1fae5;
      color: #065f46;
    }

    .alert-danger {
      background: #fee2e2;
      color: #991b1b;
    }

    .form-group {
      margin-bottom: 24px;
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
      height: 56px;
      padding: 0 16px 0 50px;
      border: 2px solid #e5e7eb;
      border-radius: 14px;
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

    .form-options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
    }

    .form-check {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .form-check-input {
      width: 18px;
      height: 18px;
      border: 2px solid #d1d5db;
      border-radius: 5px;
      cursor: pointer;
      margin: 0;
    }

    .form-check-input:checked {
      background-color: #10b981;
      border-color: #10b981;
    }

    .form-check-label {
      color: #6b7280;
      font-size: 14px;
      cursor: pointer;
      margin: 0;
      user-select: none;
    }

    .btn-primary {
      width: 100%;
      height: 56px;
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      border: none;
      border-radius: 14px;
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
      height: 54px;
      background: white;
      border: 2px solid #e5e7eb;
      border-radius: 14px;
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
      margin-bottom: 16px;
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
      margin-bottom: 12px;
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

    /* Responsive */
    @media (max-width: 992px) {
      .auth-left {
        display: none;
      }

      .auth-right {
        flex: 1;
      }
    }

    @media (max-width: 576px) {
      .auth-right {
        padding: 20px;
      }

      .form-header h2 {
        font-size: 26px;
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
    <!-- Left Side - Branding -->
    <div class="auth-left">
      <div class="brand-content">
        <div class="brand-logo">
          <i class="bi bi-cup-hot"></i>
        </div>
        <h1>GreenCup</h1>
        <p>Join the sustainable revolution. Track your impact, earn rewards, and make a difference.</p>

        <div class="features-list">
          <div class="feature-item">
            <div class="feature-icon">
              <i class="bi bi-leaf"></i>
            </div>
            <div>
              <strong>Eco-Friendly</strong><br>
              <small>Track your environmental impact</small>
            </div>
          </div>
          <div class="feature-item">
            <div class="feature-icon">
              <i class="bi bi-gift"></i>
            </div>
            <div>
              <strong>Earn Rewards</strong><br>
              <small>Get points for sustainable choices</small>
            </div>
          </div>
          <div class="feature-item">
            <div class="feature-icon">
              <i class="bi bi-shop"></i>
            </div>
            <div>
              <strong>Partner Stores</strong><br>
              <small>Discover eco-friendly businesses</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Side - Form -->
    <div class="auth-right">
      <div class="form-container">
        <div class="form-header">
          <h2>Welcome Back!</h2>
          <p>Sign in to continue your eco-journey</p>
        </div>

        <!-- Success Messages -->
        @if(session('registration_success'))
        <div class="alert alert-success">
          <i class="bi bi-check-circle-fill" style="font-size: 20px;"></i>
          <div>{{ session('registration_success') }}</div>
        </div>
        @endif

        @if(session('success') && !session('registration_success'))
        <div class="alert alert-success">
          <i class="bi bi-check-circle-fill" style="font-size: 20px;"></i>
          <div>{{ session('success') }}</div>
        </div>
        @endif

        <!-- Error Messages -->
        @if($errors->any())
        <div class="alert alert-danger">
          <i class="bi bi-exclamation-triangle-fill" style="font-size: 20px;"></i>
          <div>
            <strong>Login failed</strong>
            <ul style="margin: 8px 0 0 0; padding-left: 20px;">
              @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        </div>
        @endif

        <!-- Login Form -->
        <form id="loginForm" action="{{ route('login.store') }}" method="POST" novalidate>
          @csrf

          <!-- Email -->
          <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group">
              <i class="bi bi-envelope input-icon"></i>
              <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', session('registration_email')) }}"
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
                placeholder="Enter your password"
                required
                autocomplete="current-password"
              >
              <button type="button" class="password-toggle" onclick="togglePassword()">
                <i class="bi bi-eye" id="toggleIcon"></i>
              </button>
            </div>
            @error('password')
            <div class="invalid-feedback">
              <i class="bi bi-exclamation-circle"></i> {{ $message }}
            </div>
            @enderror
          </div>

          <!-- Remember Me -->
          <div class="form-options">
            <div class="form-check">
              <input
                type="checkbox"
                id="remember"
                name="remember"
                class="form-check-input"
                {{ old('remember') ? 'checked' : '' }}
              >
              <label for="remember" class="form-check-label">Remember me</label>
            </div>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="btn-primary" id="submitBtn">
            <span id="btnText">Sign In</span>
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
          <p>Don't have an account?</p>
          <a href="{{ route('register') }}">Create Account <i class="bi bi-arrow-right"></i></a>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function togglePassword() {
      const input = document.getElementById('password');
      const icon = document.getElementById('toggleIcon');

      if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
      } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
      }
    }

    document.getElementById('loginForm').addEventListener('submit', function() {
      const btn = document.getElementById('submitBtn');
      const btnText = document.getElementById('btnText');
      const btnIcon = document.getElementById('btnIcon');

      btnText.textContent = 'Signing In...';
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
    }, 5000);
  </script>
</body>
</html>