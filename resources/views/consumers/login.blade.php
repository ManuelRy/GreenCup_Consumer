<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - GreenCup</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            'inter': ['Inter', 'sans-serif'],
          }
        }
      }
    }
  </script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    /* CSS Variables for Responsive Design */
    :root {
      /* Responsive spacing */
      --spacing-xs: clamp(2px, 0.5vw, 4px);
      --spacing-sm: clamp(4px, 1vw, 8px);
      --spacing-md: clamp(8px, 1.5vw, 12px);
      --spacing-lg: clamp(12px, 2vw, 16px);
      --spacing-xl: clamp(16px, 3vw, 24px);
      --spacing-xxl: clamp(24px, 4vw, 32px);
      --spacing-xxxl: clamp(32px, 5vw, 48px);

      /* Responsive typography */
      --font-xs: clamp(10px, 2vw, 12px);
      --font-sm: clamp(12px, 2.5vw, 14px);
      --font-base: clamp(14px, 3vw, 16px);
      --font-lg: clamp(16px, 3.5vw, 18px);
      --font-xl: clamp(18px, 4vw, 20px);
      --font-xxl: clamp(20px, 5vw, 24px);
      --font-xxxl: clamp(24px, 6vw, 32px);
      --font-display: clamp(28px, 7vw, 40px);

      /* Responsive container */
      --container-padding: clamp(16px, 4vw, 32px);
      --form-width: clamp(320px, 90vw, 480px);
      --form-padding: clamp(24px, 5vw, 40px);

      /* Responsive elements */
      --button-height: clamp(44px, 8vw, 56px);
      --input-height: clamp(48px, 9vw, 60px);
      --icon-size: clamp(40px, 8vw, 56px);
      --border-radius: clamp(8px, 1.5vw, 12px);
      --border-radius-lg: clamp(12px, 2vw, 20px);
      --border-radius-xl: clamp(16px, 3vw, 24px);
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      font-size: var(--font-base);
    }

    /* Ensure Tailwind loads */
    .test-tailwind {
      display: block;
    }

    /* Animated Background - Enhanced and Responsive */
    .bg-animated {
      background: linear-gradient(-45deg, #10B981, #059669, #047857, #064E3B, #10B981);
      background-size: 400% 400%;
      animation: gradientShift 15s ease infinite;
      position: relative;
      min-height: 100vh;
      width: 100%;
      overflow-x: hidden;
      overflow-y: auto;
      padding: var(--container-padding);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    @keyframes gradientShift {
      0% {
        background-position: 0% 50%;
      }

      50% {
        background-position: 100% 50%;
      }

      100% {
        background-position: 0% 50%;
      }
    }

    /* Success/Error message animations */
    .alert-slide {
      animation: slideInAlert 0.5s ease-out;
    }

    @keyframes slideInAlert {
      from {
        opacity: 0;
        transform: translateX(-20px);
      }

      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    /* Floating particles - Responsive */
    .particle {
      position: absolute;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      pointer-events: none;
      animation: float 6s ease-in-out infinite;
    }

    .particle:nth-child(1) {
      width: clamp(60px, 12vw, 80px);
      height: clamp(60px, 12vw, 80px);
      top: 20%;
      left: 20%;
      animation-delay: 0s;
    }

    .particle:nth-child(2) {
      width: clamp(80px, 16vw, 120px);
      height: clamp(80px, 16vw, 120px);
      top: 60%;
      left: 80%;
      animation-delay: 2s;
    }

    .particle:nth-child(3) {
      width: clamp(40px, 8vw, 60px);
      height: clamp(40px, 8vw, 60px);
      top: 80%;
      left: 40%;
      animation-delay: 4s;
    }

    .particle:nth-child(4) {
      width: clamp(70px, 14vw, 100px);
      height: clamp(70px, 14vw, 100px);
      top: 30%;
      left: 70%;
      animation-delay: 1s;
    }

    .particle:nth-child(5) {
      width: clamp(30px, 6vw, 40px);
      height: clamp(30px, 6vw, 40px);
      top: 10%;
      left: 60%;
      animation-delay: 3s;
    }

    @keyframes float {

      0%,
      100% {
        transform: translateY(0px) rotate(0deg);
        opacity: 0.7;
      }

      50% {
        transform: translateY(-20px) rotate(180deg);
        opacity: 1;
      }
    }

    /* Glassmorphism effect - Enhanced and Responsive */
    .glass {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    /* Main Container - Fully Responsive */
    .login-container {
      width: 100%;
      max-width: var(--form-width);
      margin: 0 auto;
    }

    .login-card {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      border-radius: var(--border-radius-xl);
      padding: var(--form-padding);
      animation: slideInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
      width: 100%;
    }

    @keyframes slideInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Header Section - Responsive */
    .login-header {
      text-align: center;
      margin-bottom: var(--spacing-xxxl);
    }

    .logo-container {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: var(--icon-size);
      height: var(--icon-size);
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      margin-bottom: var(--spacing-xl);
      animation: logoSpin 20s linear infinite;
    }

    @keyframes logoSpin {
      from {
        transform: rotate(0deg);
      }
      to {
        transform: rotate(360deg);
      }
    }

    .logo-icon {
      font-size: clamp(20px, 4vw, 28px);
      color: white;
    }

    .login-title {
      font-size: var(--font-display);
      font-weight: 700;
      color: white;
      margin-bottom: var(--spacing-sm);
      line-height: 1.2;
    }

    .login-subtitle {
      color: #d1fae5;
      font-size: var(--font-lg);
      margin: 0;
    }

    /* Alert Messages - Responsive */
    .alert {
      margin-bottom: var(--spacing-xl);
      padding: var(--spacing-lg);
      border-radius: var(--border-radius-lg);
      border-left: 4px solid;
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(20px);
    }

    .alert-success {
      border-left-color: #10b981;
    }

    .alert-error {
      border-left-color: #ef4444;
    }

    .alert-content {
      display: flex;
      align-items: flex-start;
      gap: var(--spacing-md);
    }

    .alert-icon {
      flex-shrink: 0;
      margin-top: 2px;
    }

    .alert-text {
      color: white;
      font-size: var(--font-sm);
      font-weight: 500;
    }

    .alert-list {
      color: rgba(255, 255, 255, 0.9);
      font-size: var(--font-xs);
      margin-top: var(--spacing-sm);
      padding-left: var(--spacing-lg);
    }

    .alert-list li {
      margin-bottom: var(--spacing-xs);
    }

    /* Form Styles - Fully Responsive */
    .login-form {
      display: flex;
      flex-direction: column;
      gap: clamp(20px, 4vw, 28px);
    }

    .form-group {
      display: flex;
      flex-direction: column;
    }

    .form-label {
      display: flex;
      align-items: center;
      color: white;
      font-weight: 500;
      margin-bottom: var(--spacing-sm);
      font-size: var(--font-lg);
      gap: var(--spacing-sm);
    }

    .label-icon {
      color: #10b981;
      font-size: var(--font-base);
    }

    .form-input-wrapper {
      position: relative;
    }

    .form-input {
      width: 100%;
      height: var(--input-height);
      padding: 0 var(--spacing-lg);
      border: 2px solid rgba(16, 185, 129, 0.3);
      border-radius: var(--border-radius-lg);
      background: rgba(255, 255, 255, 0.1);
      color: white !important;
      font-size: var(--font-lg);
      outline: none;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      backdrop-filter: blur(10px);
    }

    .form-input::placeholder {
      color: rgba(255, 255, 255, 0.7) !important;
      font-size: var(--font-base);
    }

    .form-input:focus {
      border-color: #10B981 !important;
      box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2) !important;
      transform: translateY(-2px);
      background: rgba(255, 255, 255, 0.15) !important;
    }

    .form-input.error {
      border-color: #ef4444 !important;
      box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.2) !important;
    }

    .password-input {
      padding-right: clamp(48px, 10vw, 56px);
    }

    .password-toggle {
      position: absolute;
      right: var(--spacing-lg);
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #10b981;
      font-size: var(--font-lg);
      cursor: pointer;
      transition: color 0.2s ease;
      width: clamp(32px, 6vw, 40px);
      height: clamp(32px, 6vw, 40px);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .password-toggle:hover {
      color: white;
    }

    .form-error {
      margin-top: var(--spacing-sm);
      display: flex;
      align-items: flex-start;
      gap: var(--spacing-sm);
      color: #fca5a5;
      font-size: var(--font-sm);
      font-weight: 500;
    }

    .error-icon {
      flex-shrink: 0;
      margin-top: 2px;
    }

    /* Remember Me Checkbox - Responsive */
    .checkbox-container {
      display: flex;
      align-items: center;
      gap: var(--spacing-md);
    }

    .checkbox-custom {
      appearance: none;
      width: clamp(18px, 3.5vw, 22px);
      height: clamp(18px, 3.5vw, 22px);
      border: 2px solid rgba(16, 185, 129, 0.5);
      border-radius: 4px;
      background: rgba(255, 255, 255, 0.1);
      cursor: pointer;
      position: relative;
      transition: all 0.3s ease;
      flex-shrink: 0;
    }

    .checkbox-custom:checked {
      background: #10B981;
      border-color: #10B981;
    }

    .checkbox-custom:checked:after {
      content: '✓';
      position: absolute;
      color: white;
      font-size: clamp(10px, 2vw, 14px);
      font-weight: bold;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
    }

    .checkbox-label {
      color: white;
      font-size: var(--font-lg);
      cursor: pointer;
      user-select: none;
    }

    /* Submit Button - Responsive */
    .submit-button {
      background: linear-gradient(135deg, #10B981, #059669);
      box-shadow: 0 8px 32px rgba(16, 185, 129, 0.3);
      color: white;
      border: none;
      height: var(--button-height);
      border-radius: var(--border-radius-lg);
      font-size: var(--font-lg);
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: var(--spacing-md);
      position: relative;
      overflow: hidden;
      min-height: 44px; /* Ensure touch-friendly height */
    }

    .submit-button:before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }

    .submit-button:hover:before {
      left: 100%;
    }

    .submit-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 40px rgba(16, 185, 129, 0.4);
    }

    .submit-button:active {
      transform: translateY(0);
    }

    .button-icon {
      font-size: var(--font-lg);
    }

    /* Register Link Section - Responsive */
    .register-section {
      margin-top: var(--spacing-xxxl);
      text-align: center;
    }

    .register-text {
      color: #d1fae5;
      margin-bottom: var(--spacing-lg);
      font-size: var(--font-lg);
    }

    .register-link {
      display: inline-flex;
      align-items: center;
      gap: var(--spacing-md);
      color: white;
      font-weight: 600;
      font-size: var(--font-xl);
      text-decoration: none;
      position: relative;
      transition: all 0.3s ease;
    }

    .register-link:after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: -4px;
      left: 50%;
      background: #10B981;
      transition: all 0.3s ease;
    }

    .register-link:hover:after {
      width: 100%;
      left: 0;
    }

    .register-link:hover {
      transform: translateY(-2px);
      color: #d1fae5;
    }

    /* Responsive Breakpoints */

    /* Large screens */
    @media (min-width: 1200px) {
      .login-card {
        padding: clamp(40px, 6vw, 60px);
      }
    }

    /* Medium screens (tablets) */
    @media (max-width: 768px) {
      .bg-animated {
        padding: var(--spacing-lg);
        align-items: flex-start;
        padding-top: clamp(20px, 5vh, 40px);
      }

      .particle {
        display: none; /* Hide particles on mobile for better performance */
      }

      .login-card {
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
      }

      .form-input {
        font-size: var(--font-base);
      }

      .form-input::placeholder {
        font-size: var(--font-sm);
      }
    }

    /* Small screens (mobile portrait) */
    @media (max-width: 480px) {
      .bg-animated {
        padding: var(--spacing-md);
        padding-top: var(--spacing-lg);
      }

      .login-card {
        padding: clamp(20px, 5vw, 32px);
      }

      .login-header {
        margin-bottom: var(--spacing-xxl);
      }

      .logo-container {
        margin-bottom: var(--spacing-lg);
      }

      .login-form {
        gap: clamp(16px, 4vw, 24px);
      }

      .register-section {
        margin-top: var(--spacing-xxl);
      }

      /* Ensure forms are not cut off on very small screens */
      .form-input {
        min-height: 44px;
      }

      .submit-button {
        min-height: 48px;
      }
    }

    /* Extra small screens */
    @media (max-width: 360px) {
      :root {
        --form-padding: clamp(16px, 4vw, 24px);
      }

      .bg-animated {
        padding: var(--spacing-sm);
      }

      .alert {
        padding: var(--spacing-md);
      }

      .alert-content {
        gap: var(--spacing-sm);
      }
    }

    /* Landscape orientation on mobile */
    @media (max-height: 500px) and (orientation: landscape) {
      .bg-animated {
        align-items: flex-start;
        padding-top: var(--spacing-md);
        padding-bottom: var(--spacing-md);
      }

      .login-header {
        margin-bottom: var(--spacing-lg);
      }

      .logo-container {
        margin-bottom: var(--spacing-md);
      }

      .register-section {
        margin-top: var(--spacing-lg);
      }
    }

    /* High DPI displays */
    @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
      .login-card {
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
      }
    }

    /* Touch device optimizations */
    @media (hover: none) and (pointer: coarse) {
      .submit-button,
      .password-toggle,
      .checkbox-custom,
      .register-link {
        min-height: 44px;
        min-width: 44px;
      }

      .form-input {
        min-height: 48px;
      }

      /* Increase touch targets */
      .password-toggle {
        padding: var(--spacing-sm);
      }
    }

    /* Accessibility - Reduced Motion */
    @media (prefers-reduced-motion: reduce) {
      *, *::before, *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
      }

      .bg-animated {
        background: #10B981;
        animation: none;
      }

      .logo-container {
        animation: none;
      }
    }

    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
      .login-card {
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.2);
      }

      .form-input {
        background: rgba(0, 0, 0, 0.2);
      }
    }

    /* Focus styles for accessibility */
    .form-input:focus,
    .submit-button:focus,
    .register-link:focus,
    .checkbox-custom:focus,
    .password-toggle:focus {
      outline: 2px solid #10B981;
      outline-offset: 2px;
    }

    /* Fallback styles if Tailwind doesn't load */
    .container-fallback {
      min-height: 100vh;
      padding: 2rem 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .form-container-fallback {
      width: 100%;
      max-width: 32rem;
      margin: 0 auto;
    }

    .form-card-fallback {
      padding: 2.5rem;
      border-radius: 1.5rem;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .input-fallback {
      width: 100%;
      padding: 0.75rem 1rem;
      border-radius: 0.75rem;
      border: 2px solid rgba(16, 185, 129, 0.3);
      background: rgba(255, 255, 255, 0.1);
      color: white;
      margin-bottom: 1rem;
    }

    .button-fallback {
      width: 100%;
      padding: 1rem;
      border-radius: 0.75rem;
      background: linear-gradient(135deg, #10B981, #059669);
      color: white;
      font-weight: 600;
      border: none;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .text-white-fallback {
      color: white;
    }

    .text-center-fallback {
      text-align: center;
    }

    .mb-4-fallback {
      margin-bottom: 1rem;
    }

    .space-y-6-fallback>*+* {
      margin-top: 1.5rem;
    }
  </style>
</head>

<body class="bg-animated">
  <!-- Tailwind Loading Test -->
  <div class="hidden test-tailwind"></div>

  <!-- Floating Particles -->
  <div class="particle"></div>
  <div class="particle"></div>
  <div class="particle"></div>
  <div class="particle"></div>
  <div class="particle"></div>

  <!-- Main Login Container -->
  <div class="login-container">
    <div class="login-card">
      <!-- Logo and Header -->
      <div class="login-header">
        <div class="logo-container">
          <i class="fas fa-leaf logo-icon"></i>
        </div>
        <h1 class="login-title">Welcome Back</h1>
        <p class="login-subtitle">Sign in to your GreenCup account</p>
      </div>

      <!-- Registration Success Message -->
      @if(session('registration_success'))
      <div class="alert alert-success alert-slide">
        <div class="alert-content">
          <i class="fas fa-check-circle alert-icon" style="color: #10b981;"></i>
          <span class="alert-text">{{ session('registration_success') }}</span>
        </div>
      </div>
      @endif

      <!-- Success Message -->
      @if(session('success') && !session('registration_success'))
      <div class="alert alert-success alert-slide">
        <div class="alert-content">
          <i class="fas fa-check-circle alert-icon" style="color: #10b981;"></i>
          <span class="alert-text">{{ session('success') }}</span>
        </div>
      </div>
      @endif

      <!-- Error Messages -->
      @if($errors->any())
      <div class="alert alert-error alert-slide">
        <div class="alert-content">
          <i class="fas fa-exclamation-triangle alert-icon" style="color: #ef4444;"></i>
          <div>
            <p class="alert-text">Login failed:</p>
            <ul class="alert-list">
              @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>
      @endif

      <form id="loginForm" action="{{ route('login.store') }}" method="POST" novalidate class="login-form">
        @csrf
        
        <!-- Email Address -->
        <div class="form-group">
          <label for="email" class="form-label">
            <i class="fas fa-envelope label-icon"></i>
            Email Address
          </label>
          <div class="form-input-wrapper">
            <input 
              type="email" 
              id="email" 
              name="email" 
              value="{{ old('email', session('registration_email')) }}"
              class="form-input @error('email') error @enderror"
              placeholder="Enter your email address"
              required 
              autocomplete="email"
              aria-describedby="email-error"
            />
          </div>
          @error('email')
          <div class="form-error" id="email-error">
            <i class="fas fa-exclamation-circle error-icon"></i>
            <p>{{ $message }}</p>
          </div>
          @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
          <label for="password" class="form-label">
            <i class="fas fa-lock label-icon"></i>
            Password
          </label>
          <div class="form-input-wrapper">
            <input 
              type="password" 
              id="password" 
              name="password"
              class="form-input password-input @error('password') error @enderror"
              placeholder="Enter your password" 
              required 
              autocomplete="current-password"
              aria-describedby="password-error"
            />
            <button 
              type="button" 
              onclick="togglePassword('password')"
              class="password-toggle"
              aria-label="Toggle password visibility"
            >
              <i id="passwordToggle" class="fas fa-eye"></i>
            </button>
          </div>
          @error('password')
          <div class="form-error" id="password-error">
            <i class="fas fa-exclamation-circle error-icon"></i>
            <p>{{ $message }}</p>
          </div>
          @enderror
        </div>

        <!-- Remember Me -->
        <div class="checkbox-container">
          <input 
            type="checkbox" 
            id="remember_me" 
            name="remember_me" 
            class="checkbox-custom" 
            aria-describedby="remember-help"
          />
          <label for="remember_me" class="checkbox-label">Remember me</label>
          <div id="remember-help" class="sr-only">Keep me signed in on this device</div>
        </div>

        <!-- Submit Button -->
        <button 
          id="loginSubmit" 
          name="loginSubmit" 
          type="submit" 
          aria-label="Sign In"
          class="submit-button"
        >
          <i class="fas fa-sign-in-alt button-icon" aria-hidden="true"></i>
          Sign In
        </button>

      </form>

      <!-- Register Link -->
      <div class="register-section">
        <p class="register-text">Don't have an account?</p>
        <a href="{{ route('consumers.create') }}" class="register-link">
          <i class="fas fa-user-plus"></i>
          Create Account
        </a>
      </div>
    </div>
  </div>

  <script>
    // Check if Tailwind loaded and apply fallbacks if needed
    document.addEventListener('DOMContentLoaded', function () {
      const testElement = document.querySelector('.test-tailwind');
      const computedStyle = window.getComputedStyle(testElement);

      // If Tailwind didn't load properly, apply fallback classes
      if (computedStyle.display !== 'block') {
        console.log('Tailwind fallback activated');
        document.body.classList.add('container-fallback');

        // Apply fallback styles to key elements
        const inputs = document.querySelectorAll('input[type="email"], input[type="password"]');
        inputs.forEach(input => {
          input.classList.add('input-fallback');
        });

        const buttons = document.querySelectorAll('button[type="submit"]');
        buttons.forEach(button => {
          button.classList.add('button-fallback');
        });

        // Apply text styles
        const whiteTexts = document.querySelectorAll('.text-white');
        whiteTexts.forEach(el => el.classList.add('text-white-fallback'));

        const centerTexts = document.querySelectorAll('.text-center');
        centerTexts.forEach(el => el.classList.add('text-center-fallback'));

        const spaceY = document.querySelectorAll('.space-y-7');
        spaceY.forEach(el => el.classList.add('space-y-6-fallback'));
      }

      // Initialize form enhancements
      initializeFormEnhancements();
    });

    // Enhanced form functionality
    function initializeFormEnhancements() {
      const form = document.getElementById('loginForm');
      const inputs = form.querySelectorAll('input');
      
      // Add real-time validation feedback
      inputs.forEach(input => {
        input.addEventListener('blur', validateField);
        input.addEventListener('input', clearErrors);
      });

      // Handle form submission with better UX
      form.addEventListener('submit', handleFormSubmit);

      // Auto-focus first empty field
      const firstEmptyField = Array.from(inputs).find(input => !input.value);
      if (firstEmptyField) {
        firstEmptyField.focus();
      }

      // Add ripple effect to submit button
      addRippleEffect();
    }

    function validateField(event) {
      const field = event.target;
      const errors = [];

      // Clear previous error state
      field.classList.remove('error');
      const errorElement = document.getElementById(`${field.name}-error`);
      if (errorElement) {
        errorElement.style.display = 'none';
      }

      // Email validation
      if (field.name === 'email') {
        if (!field.value.trim()) {
          errors.push('Email address is required');
        } else if (!isValidEmail(field.value)) {
          errors.push('Please enter a valid email address');
        }
      }

      // Password validation
      if (field.name === 'password') {
        if (!field.value) {
          errors.push('Password is required');
        }
      }

      // Show errors if any
      if (errors.length > 0) {
        field.classList.add('error');
        showFieldError(field.name, errors[0]);
      }
    }

    function clearErrors(event) {
      const field = event.target;
      field.classList.remove('error');
      const errorElement = document.getElementById(`${field.name}-error`);
      if (errorElement) {
        errorElement.style.display = 'none';
      }
    }

    function showFieldError(fieldName, message) {
      let errorElement = document.getElementById(`${fieldName}-error`);
      if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.id = `${fieldName}-error`;
        errorElement.className = 'form-error';
        errorElement.innerHTML = `
          <i class="fas fa-exclamation-circle error-icon"></i>
          <p></p>
        `;
        const field = document.querySelector(`[name="${fieldName}"]`);
        field.parentNode.parentNode.appendChild(errorElement);
      }
      
      errorElement.querySelector('p').textContent = message;
      errorElement.style.display = 'flex';
    }

    function handleFormSubmit(e) {
      const form = e.target;
      const email = form.email.value.trim();
      const password = form.password.value;
      const errors = [];
      let hasErrors = false;

      // Clear all previous errors
      form.querySelectorAll('.form-input').forEach(input => {
        input.classList.remove('error');
      });
      form.querySelectorAll('.form-error').forEach(error => {
        error.style.display = 'none';
      });

      // Validate email
      if (!email) {
        errors.push({field: 'email', message: 'Email address is required'});
        hasErrors = true;
      } else if (!isValidEmail(email)) {
        errors.push({field: 'email', message: 'Please enter a valid email address'});
        hasErrors = true;
      }

      // Validate password
      if (!password) {
        errors.push({field: 'password', message: 'Password is required'});
        hasErrors = true;
      }

      // Show errors and prevent submission if needed
      if (hasErrors) {
        e.preventDefault();
        errors.forEach(error => {
          const field = form.querySelector(`[name="${error.field}"]`);
          field.classList.add('error');
          showFieldError(error.field, error.message);
        });

        // Focus first error field
        const firstErrorField = form.querySelector('.error');
        if (firstErrorField) {
          firstErrorField.focus();
          firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return;
      }

      // Show loading state
      const submitButton = form.querySelector('button[type="submit"]');
      const originalHTML = submitButton.innerHTML;
      submitButton.innerHTML = `
        <i class="fas fa-spinner fa-spin button-icon" aria-hidden="true"></i>
        Signing In...
      `;
      submitButton.disabled = true;

      // Re-enable button after timeout (in case of server errors)
      setTimeout(() => {
        submitButton.innerHTML = originalHTML;
        submitButton.disabled = false;
      }, 10000);
    }

    // Toggle password visibility
    function togglePassword(inputId) {
      const input = document.getElementById(inputId);
      const toggleIcon = document.getElementById('passwordToggle');

      if (input.type === 'password') {
        input.type = 'text';
        toggleIcon.className = 'fas fa-eye-slash';
        toggleIcon.parentElement.setAttribute('aria-label', 'Hide password');
      } else {
        input.type = 'password';
        toggleIcon.className = 'fas fa-eye';
        toggleIcon.parentElement.setAttribute('aria-label', 'Show password');
      }
    }

    function isValidEmail(email) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(email);
    }

    // Add ripple effect to buttons
    function addRippleEffect() {
      document.querySelectorAll('.submit-button').forEach(button => {
        button.addEventListener('click', function (e) {
          const ripple = document.createElement('span');
          const rect = this.getBoundingClientRect();
          const size = Math.max(rect.width, rect.height);
          const x = e.clientX - rect.left - size / 2;
          const y = e.clientY - rect.top - size / 2;

          ripple.style.width = ripple.style.height = size + 'px';
          ripple.style.left = x + 'px';
          ripple.style.top = y + 'px';
          ripple.classList.add('ripple');

          this.appendChild(ripple);

          setTimeout(() => {
            ripple.remove();
          }, 600);
        });
      });
    }

    // Keyboard navigation enhancements
    document.addEventListener('keydown', function(e) {
      // Allow Enter key to submit form when focused on submit button
      if (e.key === 'Enter' && e.target.type === 'submit') {
        e.target.click();
      }

      // Auto-advance on Tab in forms
      if (e.key === 'Tab') {
        const focusableElements = document.querySelectorAll('input, button, [tabindex]:not([tabindex="-1"])');
        const currentIndex = Array.from(focusableElements).indexOf(document.activeElement);
        
        if (e.shiftKey) {
          // Shift+Tab (backwards)
          if (currentIndex === 0) {
            e.preventDefault();
            focusableElements[focusableElements.length - 1].focus();
          }
        } else {
          // Tab (forwards)
          if (currentIndex === focusableElements.length - 1) {
            e.preventDefault();
            focusableElements[0].focus();
          }
        }
      }
    });

    // Handle window resize for responsive adjustments
    window.addEventListener('resize', function() {
      // Adjust viewport height on mobile browsers
      if (window.innerHeight < 600 && window.orientation !== undefined) {
        document.documentElement.style.setProperty('--vh', `${window.innerHeight * 0.01}px`);
      }
    });

    // Add CSS for ripple effect
    const style = document.createElement('style');
    style.textContent = `
      .ripple {
        position: absolute;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transform: scale(0);
        animation: ripple-animation 0.6s linear;
        pointer-events: none;
      }
      
      @keyframes ripple-animation {
        to {
          transform: scale(4);
          opacity: 0;
        }
      }

      .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border-width: 0;
      }
    `;
    document.head.appendChild(style);

    // Performance optimization: Lazy load background animation
    if ('IntersectionObserver' in window) {
      const backgroundObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.animationPlayState = 'running';
          } else {
            entry.target.style.animationPlayState = 'paused';
          }
        });
      });

      backgroundObserver.observe(document.body);
    }
  </script>
</body>

</html>