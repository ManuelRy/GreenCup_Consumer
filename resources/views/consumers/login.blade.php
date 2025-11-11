<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <title>Login - GreenCup</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" integrity="sha512-dPXYcDub/aeb08c63jRq/k6GaKccl256JQy/AnOq7CAnEZ9FzSL9wSbcZkMp4R26vBsMLFYH4kQ67/bbV8XaCQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
      max-width: 480px;
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
      z-index: 10;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: color 0.3s ease;
    }

    .form-control {
      width: 100%;
      height: 54px;
      padding: 0 16px 0 50px;
      border: 2px solid #e5e7eb;
      border-radius: 12px;
      font-size: 15px;
      transition: all 0.3s ease;
      background: #f9fafb;
      position: relative;
      z-index: 1;
    }

    .form-control:focus {
      outline: none;
      border-color: #10b981;
      background: #ffffff;
      box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
    }
    
    .input-group:focus-within .input-icon {
      color: #10b981;
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
      <h1>Welcome Back</h1>
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
    <form id="loginForm" action="{{ route('login.store') }}" method="POST" novalidate autocomplete="off">
      @csrf
      <input type="hidden" id="formSubmitted" value="no">

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

    // Check if form was submitted (works with bfcache)
    const formSubmitted = document.getElementById('formSubmitted');

    function resetButtonState() {
      const btn = document.getElementById('submitBtn');
      const btnText = document.getElementById('btnText');
      const btnIcon = document.getElementById('btnIcon');

      // Reset button
      if (btn) {
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.style.pointerEvents = '';
      }

      // Reset text
      if (btnText) {
        btnText.textContent = 'Sign In';
      }

      // Reset icon - check if it's a spinner first
      if (btnIcon) {
        if (btnIcon.classList.contains('spinner')) {
          // Replace spinner with arrow icon
          const newIcon = document.createElement('i');
          newIcon.className = 'bi bi-arrow-right';
          newIcon.id = 'btnIcon';
          btnIcon.replaceWith(newIcon);
        }
      } else {
        // Icon might have been replaced with spinner div
        const spinnerDiv = btn.querySelector('.spinner');
        if (spinnerDiv) {
          const newIcon = document.createElement('i');
          newIcon.className = 'bi bi-arrow-right';
          newIcon.id = 'btnIcon';
          spinnerDiv.replaceWith(newIcon);
        }
      }

      // Reset form submitted flag
      if (formSubmitted) {
        formSubmitted.value = 'no';
      }
    }

    // ALWAYS reset on page show (most reliable for bfcache)
    window.addEventListener('pageshow', function(event) {
      // Check if user came back from authenticated page (back button pressed after login)
      if (event.persisted && formSubmitted && formSubmitted.value === 'yes') {
        // User pressed back button after successful login - logout and redirect
        window.location.href = '{{ route("logout.get") }}';
        return;
      }

      // Otherwise just reset button state
      setTimeout(resetButtonState, 10);
    });

    // Form submission handler
    document.getElementById('loginForm').addEventListener('submit', function(e) {
      // Mark form as submitted
      if (formSubmitted) {
        formSubmitted.value = 'yes';
      }

      const btn = document.getElementById('submitBtn');
      const btnText = document.getElementById('btnText');
      const btnIcon = document.getElementById('btnIcon');

      if (btnText) btnText.textContent = 'Signing In...';
      if (btnIcon) btnIcon.outerHTML = '<div class="spinner"></div>';
      if (btn) {
        btn.style.opacity = '0.7';
        btn.style.pointerEvents = 'none';
        btn.disabled = true;
      }
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