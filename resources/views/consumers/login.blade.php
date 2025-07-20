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

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    /* Ensure Tailwind loads */
    .test-tailwind {
      display: block;
    }

    /* Animated Background - Enhanced */
    .bg-animated {
      background: linear-gradient(-45deg, #10B981, #059669, #047857, #064E3B, #10B981);
      background-size: 400% 400%;
      animation: gradientShift 15s ease infinite;
      position: relative;
      min-height: 100vh;
      width: 100%;
      overflow-x: hidden;
      overflow-y: auto;
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

    /* Floating particles */
    .particle {
      position: absolute;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      pointer-events: none;
      animation: float 6s ease-in-out infinite;
    }

    .particle:nth-child(1) {
      width: 80px;
      height: 80px;
      top: 20%;
      left: 20%;
      animation-delay: 0s;
    }

    .particle:nth-child(2) {
      width: 120px;
      height: 120px;
      top: 60%;
      left: 80%;
      animation-delay: 2s;
    }

    .particle:nth-child(3) {
      width: 60px;
      height: 60px;
      top: 80%;
      left: 40%;
      animation-delay: 4s;
    }

    .particle:nth-child(4) {
      width: 100px;
      height: 100px;
      top: 30%;
      left: 70%;
      animation-delay: 1s;
    }

    .particle:nth-child(5) {
      width: 40px;
      height: 40px;
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

    /* Glassmorphism effect - Enhanced */
    .glass {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    /* Premium button animations */
    .btn-premium {
      background: linear-gradient(135deg, #10B981, #059669);
      box-shadow: 0 8px 32px rgba(16, 185, 129, 0.3);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }

    .btn-premium:before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }

    .btn-premium:hover:before {
      left: 100%;
    }

    .btn-premium:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 40px rgba(16, 185, 129, 0.4);
    }

    /* Secondary button */
    .btn-secondary {
      background: rgba(255, 255, 255, 0.1);
      border: 2px solid rgba(255, 255, 255, 0.3);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }

    .btn-secondary:hover {
      background: rgba(255, 255, 255, 0.2);
      border-color: rgba(255, 255, 255, 0.5);
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(255, 255, 255, 0.1);
    }

    /* Input focus animations - Enhanced */
    .input-premium {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      border: 2px solid rgba(16, 185, 129, 0.3);
      background: rgba(255, 255, 255, 0.1);
      color: white !important;
    }

    .input-premium::placeholder {
      color: rgba(255, 255, 255, 0.7) !important;
    }

    .input-premium:focus {
      border-color: #10B981 !important;
      box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2) !important;
      transform: translateY(-1px);
      background: rgba(255, 255, 255, 0.15) !important;
    }

    /* Logo animation */
    .logo-spin {
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

    /* Form slide-in animation */
    .form-slide {
      animation: slideInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
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

    /* Mobile responsive adjustments */
    @media (max-width: 768px) {
      .particle {
        display: none;
      }

      .glass {
        margin: 1rem;
        backdrop-filter: blur(10px);
      }
    }

    /* Link hover effects */
    .link-hover {
      position: relative;
      transition: all 0.3s ease;
    }

    .link-hover:after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: -2px;
      left: 50%;
      background: #10B981;
      transition: all 0.3s ease;
    }

    .link-hover:hover:after {
      width: 100%;
      left: 0;
    }

    .link-hover:hover {
      transform: translateY(-1px);
    }

    /* Remember me checkbox styling */
    .checkbox-custom {
      appearance: none;
      width: 20px;
      height: 20px;
      border: 2px solid rgba(16, 185, 129, 0.5);
      border-radius: 4px;
      background: rgba(255, 255, 255, 0.1);
      cursor: pointer;
      position: relative;
      transition: all 0.3s ease;
    }

    .checkbox-custom:checked {
      background: #10B981;
      border-color: #10B981;
    }

    .checkbox-custom:checked:after {
      content: '✓';
      position: absolute;
      color: white;
      font-size: 14px;
      font-weight: bold;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
    }

    /* Fallback styles if Tailwind doesn't load */
    .container-fallback {
      min-height: 100vh;
      padding: 2rem 1rem;
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

<body class="bg-animated min-h-screen py-8 px-4">
  <!-- Tailwind Loading Test -->
  <div class="hidden test-tailwind"></div>

  <!-- Floating Particles -->
  <div class="particle"></div>
  <div class="particle"></div>
  <div class="particle"></div>
  <div class="particle"></div>
  <div class="particle"></div>

  <!-- Main Form Container -->
  <div class="w-full max-w-lg mx-auto">
    <div class="glass rounded-3xl p-10 shadow-2xl form-slide w-full">
      <!-- Logo and Header -->
      <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-24 h-24 glass rounded-full mb-6 logo-spin">
          <i class="fas fa-leaf text-4xl text-white"></i>
        </div>
        <h1 class="text-4xl font-bold text-white mb-3">Welcome Back</h1>
        <p class="text-green-100 text-lg">Sign in to your GreenCup account</p>
      </div>

      <!-- Registration Success Message -->
      @if(session('registration_success'))
      <div class="mb-6 px-4 py-3 glass rounded-lg border-l-4 border-green-400 alert-slide">
      <div class="flex items-center">
        <i class="fas fa-check-circle text-green-400 mr-3"></i>
        <span class="text-white">{{ session('registration_success') }}</span>
      </div>
      </div>
    @endif

      <!-- Success Message -->
      @if(session('success') && !session('registration_success'))
      <div class="mb-6 px-4 py-3 glass rounded-lg border-l-4 border-green-400 alert-slide">
      <div class="flex items-center">
        <i class="fas fa-check-circle text-green-400 mr-3"></i>
        <span class="text-white">{{ session('success') }}</span>
      </div>
      </div>
    @endif

      <!-- Error Messages -->
      @if($errors->any())
      <div class="mb-6 px-4 py-3 glass rounded-lg border-l-4 border-red-400 alert-slide">
      <div class="flex items-start">
        <i class="fas fa-exclamation-triangle text-red-400 mr-3 mt-0.5"></i>
        <div>
        <p class="text-white font-medium mb-2">Login failed:</p>
        <ul class="text-red-100 text-sm space-y-1 list-disc list-inside">
          @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
        </ul>
        </div>
      </div>
      </div>
    @endif

      <form id="loginForm" action="{{ route('login.store') }}" method="POST" novalidate class="space-y-7">
        @csrf
        <!-- Email Address -->
        <div>
          <label for="email" class="block text-white font-medium mb-2 text-lg">
            <i class="fas fa-envelope mr-2 text-green-400"></i>Email Address
          </label>
          <input type="email" id="email" name="email" value="{{ old('email', session('registration_email')) }}"
            class="w-full input-premium glass rounded-xl px-5 py-4 text-white placeholder-green-200 focus:outline-none text-lg @error('email') !border-red-400 !shadow-red-400/20 @enderror"
            placeholder="Enter your email" required />
          @error('email')
        <div class="mt-2 flex items-start">
        <i class="fas fa-exclamation-circle text-red-400 mr-2 mt-0.5 text-sm"></i>
        <p class="text-red-300 text-sm font-medium">{{ $message }}</p>
        </div>
      @enderror
        </div>

        <!-- Password -->
        <div>
          <label for="password" class="block text-white font-medium mb-2 text-lg">
            <i class="fas fa-lock mr-2 text-green-400"></i>Password
          </label>
          <div class="relative">
            <input type="password" id="password" name="password"
              class="w-full input-premium glass rounded-xl px-5 py-4 pr-14 text-white placeholder-green-200 focus:outline-none text-lg @error('password') !border-red-400 !shadow-red-400/20 @enderror"
              placeholder="Enter your password" required />
            <button type="button" onclick="togglePassword('password')"
              class="absolute right-3 top-1/2 transform -translate-y-1/2 text-green-300 hover:text-white transition-colors">
              <i id="passwordToggle" class="fas fa-eye"></i>
            </button>
          </div>
          @error('password')
        <div class="mt-2 flex items-start">
        <i class="fas fa-exclamation-circle text-red-400 mr-2 mt-0.5 text-sm"></i>
        <p class="text-red-300 text-sm font-medium">{{ $message }}</p>
        </div>
      @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
          <input type="checkbox" id="remember_me" name="remember_me" class="checkbox-custom mr-3" />
          <label for="remember_me" class="text-white text-lg cursor-pointer">Remember me</label>
        </div>

        <!-- Submit Button -->
        <button id="loginSubmit" name="loginSubmit" type="submit" aria-label="Sign In"
          class="w-full btn-premium text-white font-semibold py-5 rounded-xl transition-all duration-300 flex items-center justify-center text-lg">
          <i class="fas fa-sign-in-alt mr-3" aria-hidden="true"></i>
          Sign In
        </button>

      </form>

      <!-- Register Link -->
      <div class="mt-10 text-center">
        <p class="text-green-100 mb-5 text-lg">Don't have an account?</p>
        <a href="{{ route('consumers.create') }}" onclick="goToRegister()"
          class="link-hover inline-flex items-center text-white font-semibold text-xl hover:text-green-200">
          <i class="fas fa-user-plus mr-3"></i>
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
    });

    // Toggle password visibility
    function togglePassword(inputId) {
      const input = document.getElementById(inputId);
      const toggleIcon = document.getElementById('passwordToggle');

      if (input.type === 'password') {
        input.type = 'text';
        toggleIcon.className = 'fas fa-eye-slash';
      } else {
        input.type = 'password';
        toggleIcon.className = 'fas fa-eye';
      }
    }

    // Go to register page
    function goToRegister() {
      // In a real application, this would navigate to your register route
      window.location.href = "{{ route('consumers.create') }}";
    }

    // Form validation and submission

    document.getElementById('loginForm').addEventListener('submit', function (e) {
      const form = this;
      const email = form.email.value.trim();
      const password = form.password.value;
      const errors = [];

      // Basic validation
      if (!email) {
        errors.push('Email address is required');
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        errors.push('Please enter a valid email address');
      }

      if (!password) {
        errors.push('Password is required');
      }

      if (errors.length > 0) {
        e.preventDefault();           // only block submit on errors
        showErrors(errors);           // your existing function
      }
      // if no errors, don't call preventDefault()—form submits normally
    });


    function showErrors(errors) {
      const errorAlert = document.getElementById('errorAlert');
      const errorList = document.getElementById('errorList');
      const successAlert = document.getElementById('successAlert');

      successAlert.classList.add('hidden');
      errorList.innerHTML = errors.map(error => `<li>${error}</li>`).join('');
      errorAlert.classList.remove('hidden');

      // Scroll to top to show errors
      errorAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function showSuccess() {
      const errorAlert = document.getElementById('errorAlert');
      const successAlert = document.getElementById('successAlert');

      errorAlert.classList.add('hidden');
      successAlert.classList.remove('hidden');

      // Scroll to top to show success
      successAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

      // Simulate redirect after 2 seconds
      setTimeout(() => {
        alert('Redirecting to dashboard...');
      }, 2000);
    }

    function isValidEmail(email) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(email);
    }

    // Add ripple effect to buttons
    document.querySelectorAll('.btn-premium').forEach(button => {
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

    // Add ripple CSS
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
    `;
    document.head.appendChild(style);
  </script>
</body>

</html>