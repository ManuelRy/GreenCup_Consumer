<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register - GreenCup</title>
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
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    
    /* Floating particles */
    .particle {
      position: absolute;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      pointer-events: none;
      animation: float 6s ease-in-out infinite;
    }
    
    .particle:nth-child(1) { width: 80px; height: 80px; top: 20%; left: 20%; animation-delay: 0s; }
    .particle:nth-child(2) { width: 120px; height: 120px; top: 60%; left: 80%; animation-delay: 2s; }
    .particle:nth-child(3) { width: 60px; height: 60px; top: 80%; left: 40%; animation-delay: 4s; }
    .particle:nth-child(4) { width: 100px; height: 100px; top: 30%; left: 70%; animation-delay: 1s; }
    .particle:nth-child(5) { width: 40px; height: 40px; top: 10%; left: 60%; animation-delay: 3s; }
    
    @keyframes float {
      0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.7; }
      50% { transform: translateY(-20px) rotate(180deg); opacity: 1; }
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
    
    /* Select styling */
    select.input-premium {
      color: white !important;
      background: rgba(255, 255, 255, 0.1) !important;
    }
    
    select.input-premium option {
      background: #047857 !important;
      color: white !important;
    }
    
    /* Logo animation */
    .logo-spin {
      animation: logoSpin 20s linear infinite;
    }
    
    @keyframes logoSpin {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
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
    
    /* Mobile back button animation */
    .back-btn {
      transition: all 0.3s ease;
    }
    
    .back-btn:hover {
      transform: translateX(-3px);
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
    
    /* Mobile responsive adjustments */
    @media (max-width: 768px) {
      .particle { display: none; }
      .glass {
        margin: 1rem;
        backdrop-filter: blur(10px);
      }
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
    
    .space-y-6-fallback > * + * {
      margin-top: 1.5rem;
    }
    
    /* Login link hover effect */
    .login-link {
      position: relative;
      transition: all 0.3s ease;
    }
    
    .login-link:after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: -2px;
      left: 50%;
      background: #10B981;
      transition: all 0.3s ease;
    }
    
    .login-link:hover:after {
      width: 100%;
      left: 0;
    }
    
    .login-link:hover {
      transform: translateY(-1px);
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
  
  <!-- Mobile Back Button -->
  <div class="md:hidden fixed top-4 left-4 z-50">
    <button onclick="history.back()" class="back-btn flex items-center justify-center w-12 h-12 glass rounded-full text-white hover:bg-white hover:bg-opacity-20">
      <i class="fas fa-arrow-left text-lg"></i>
    </button>
  </div>
  
  <!-- Main Form Container -->
  <div class="w-full max-w-lg mx-auto">
    <div class="glass rounded-3xl p-10 shadow-2xl form-slide w-full">
      <!-- Logo and Header -->
      <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-24 h-24 glass rounded-full mb-6 logo-spin">
          <i class="fas fa-leaf text-4xl text-white"></i>
        </div>
        <h1 class="text-4xl font-bold text-white mb-3">GreenCup</h1>
        <p class="text-green-100 text-lg">Join our sustainable community</p>
      </div>

      <!-- Success Message -->
      @if(session('success'))
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
              <p class="text-white font-medium mb-2">Please fix the following errors:</p>
              <ul class="text-red-100 text-sm space-y-1 list-disc list-inside">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          </div>
        </div>
      @endif

      <form id="registrationForm" action="{{ route('consumers.store') }}" method="POST" novalidate class="space-y-7">
        @csrf
        <!-- Full Name -->
        <div>
          <label for="full_name" class="block text-white font-medium mb-2">
            <i class="fas fa-user mr-2 text-green-400"></i>Full Name
          </label>
          <input
            type="text"
            id="full_name"
            name="full_name"
            value="{{ old('full_name') }}"
            class="w-full input-premium glass rounded-xl px-5 py-4 text-white placeholder-green-200 focus:outline-none text-lg @error('full_name') border-red-400 @enderror"
            placeholder="Enter your full name"
            required
          />
          @error('full_name')
            <p class="mt-1 text-red-300 text-sm">{{ $message }}</p>
          @enderror
        </div>

        <!-- Email Address -->
        <div>
          <label for="email" class="block text-white font-medium mb-2">
            <i class="fas fa-envelope mr-2 text-green-400"></i>Email Address
          </label>
          <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email') }}"
            class="w-full input-premium glass rounded-xl px-5 py-4 text-white placeholder-green-200 focus:outline-none text-lg @error('email') border-red-400 @enderror"
            placeholder="Enter your email"
            required
          />
          @error('email')
            <p class="mt-1 text-red-300 text-sm">{{ $message }}</p>
          @enderror
        </div>

        <!-- Phone Number -->
        <div>
          <label for="phone_number" class="block text-white font-medium mb-2">
            <i class="fas fa-phone mr-2 text-green-400"></i>Phone Number 
            <span class="text-green-200 font-normal">(optional)</span>
          </label>
          <input
            type="tel"
            id="phone_number"
            name="phone_number"
            value="{{ old('phone_number') }}"
            class="w-full input-premium glass rounded-xl px-5 py-4 text-white placeholder-green-200 focus:outline-none text-lg @error('phone_number') border-red-400 @enderror"
            placeholder="Enter your phone number"
          />
          @error('phone_number')
            <p class="mt-1 text-red-300 text-sm">{{ $message }}</p>
          @enderror
        </div>

        <!-- Gender -->
        <div>
          <label for="gender" class="block text-white font-medium mb-2">
            <i class="fas fa-venus-mars mr-2 text-green-400"></i>Gender
          </label>
          <select
            id="gender"
            name="gender"
            class="w-full input-premium glass rounded-xl px-5 py-4 text-white focus:outline-none text-lg @error('gender') border-red-400 @enderror"
            style="color-scheme: dark;"
            required
          >
            <option value="">-- Select Gender --</option>
            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
          </select>
          @error('gender')
            <p class="mt-1 text-red-300 text-sm">{{ $message }}</p>
          @enderror
        </div>

        <!-- Date of Birth -->
        <div>
          <label for="date_of_birth" class="block text-white font-medium mb-2">
            <i class="fas fa-calendar mr-2 text-green-400"></i>Date of Birth 
            <span class="text-green-200 font-normal">(optional)</span>
          </label>
          <input
            type="date"
            id="date_of_birth"
            name="date_of_birth"
            value="{{ old('date_of_birth') }}"
            class="w-full input-premium glass rounded-xl px-5 py-4 text-white focus:outline-none text-lg @error('date_of_birth') border-red-400 @enderror"
            style="color-scheme: dark;"
          />
          @error('date_of_birth')
            <p class="mt-1 text-red-300 text-sm">{{ $message }}</p>
          @enderror
        </div>

        <!-- Password -->
        <div>
          <label for="password" class="block text-white font-medium mb-2">
            <i class="fas fa-lock mr-2 text-green-400"></i>Password
          </label>
          <div class="relative">
            <input
              type="password"
              id="password"
              name="password"
              class="w-full input-premium glass rounded-xl px-5 py-4 pr-14 text-white placeholder-green-200 focus:outline-none text-lg @error('password') border-red-400 @enderror"
              placeholder="Create a strong password (min 8 characters)"
              required
            />
            <button
              type="button"
              onclick="togglePassword('password')"
              class="absolute right-3 top-1/2 transform -translate-y-1/2 text-green-300 hover:text-white transition-colors"
            >
              <i id="passwordToggle" class="fas fa-eye"></i>
            </button>
          </div>
          @error('password')
            <p class="mt-1 text-red-300 text-sm">{{ $message }}</p>
          @enderror
        </div>

        <!-- Confirm Password -->
        <div>
          <label for="password_confirmation" class="block text-white font-medium mb-2">
            <i class="fas fa-lock mr-2 text-green-400"></i>Confirm Password
          </label>
          <div class="relative">
            <input
              type="password"
              id="password_confirmation"
              name="password_confirmation"
              class="w-full input-premium glass rounded-xl px-5 py-4 pr-14 text-white placeholder-green-200 focus:outline-none text-lg @error('password_confirmation') border-red-400 @enderror"
              placeholder="Confirm your password"
              required
            />
            <button
              type="button"
              onclick="togglePassword('password_confirmation')"
              class="absolute right-3 top-1/2 transform -translate-y-1/2 text-green-300 hover:text-white transition-colors"
            >
              <i id="confirmPasswordToggle" class="fas fa-eye"></i>
            </button>
          </div>
          @error('password_confirmation')
            <p class="mt-1 text-red-300 text-sm">{{ $message }}</p>
          @enderror
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          class="w-full btn-premium text-white font-semibold py-5 rounded-xl transition-all duration-300 flex items-center justify-center text-lg"
        >
          <i class="fas fa-user-plus mr-3"></i>
          Create Account
        </button>
      </form>

      <!-- Login Link -->
      <div class="mt-10 text-center">
        <p class="text-green-100 mb-5 text-lg">Already have an account?</p>
        <a href="#" onclick="goToLogin()" class="login-link inline-flex items-center text-white font-semibold text-xl hover:text-green-200">
          <i class="fas fa-sign-in-alt mr-3"></i>
          Sign In Instead
        </a>
      </div>

      <!-- Desktop Back Button -->
      <div class="hidden md:block mt-8 text-center">
        <button onclick="history.back()" class="login-link inline-flex items-center text-green-200 hover:text-white transition-colors text-lg">
          <i class="fas fa-arrow-left mr-3"></i>
          Go Back
        </button>
      </div>
    </div>
  </div>

  <script>
    // Check if Tailwind loaded and apply fallbacks if needed
    document.addEventListener('DOMContentLoaded', function() {
      const testElement = document.querySelector('.test-tailwind');
      const computedStyle = window.getComputedStyle(testElement);
      
      // If Tailwind didn't load properly, apply fallback classes
      if (computedStyle.display !== 'block') {
        document.body.classList.add('container-fallback');
        
        // Apply fallback styles to key elements
        const inputs = document.querySelectorAll('input, select');
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
      const toggleIcon = document.getElementById(inputId === 'password' ? 'passwordToggle' : 'confirmPasswordToggle');
      
      if (input.type === 'password') {
        input.type = 'text';
        toggleIcon.className = 'fas fa-eye-slash';
      } else {
        input.type = 'password';
        toggleIcon.className = 'fas fa-eye';
      }
    }

    // Go to login page
    function goToLogin() {
      // In a real application, this would navigate to your login route
      alert('Redirecting to login page...');
      // window.location.href = '/login';
    }

    // Form validation and submission - Updated for Laravel
    document.getElementById('registrationForm').addEventListener('submit', function(e) {
      // Let Laravel handle validation, but still do basic client-side checks
      const formData = new FormData(this);
      const errors = [];

      // Basic validation to provide immediate feedback
      if (!formData.get('full_name').trim()) {
        errors.push('Full name is required');
      }

      if (!formData.get('email').trim()) {
        errors.push('Email address is required');
      } else if (!isValidEmail(formData.get('email'))) {
        errors.push('Please enter a valid email address');
      }

      if (!formData.get('gender')) {
        errors.push('Gender selection is required');
      }

      if (!formData.get('password')) {
        errors.push('Password is required');
      } else if (formData.get('password').length < 8) {
        errors.push('Password must be at least 8 characters long');
      }

      if (formData.get('password') !== formData.get('password_confirmation')) {
        errors.push('Passwords do not match');
      }

      // If there are client-side errors, show them and prevent submission
      if (errors.length > 0) {
        e.preventDefault();
        showClientErrors(errors);
        return false;
      }

      // Otherwise, let the form submit to Laravel for server-side validation
      // Add loading state to button
      const submitButton = this.querySelector('button[type="submit"]');
      const originalText = submitButton.innerHTML;
      submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-3"></i>Creating Account...';
      submitButton.disabled = true;

      // Re-enable button after 3 seconds in case of errors
      setTimeout(() => {
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
      }, 3000);
    });

    function showClientErrors(errors) {
      // Create a temporary error display for client-side validation
      const existingAlert = document.getElementById('clientErrorAlert');
      if (existingAlert) {
        existingAlert.remove();
      }

      const errorAlert = document.createElement('div');
      errorAlert.id = 'clientErrorAlert';
      errorAlert.className = 'mb-6 px-4 py-3 glass rounded-lg border-l-4 border-red-400 alert-slide';
      errorAlert.innerHTML = `
        <div class="flex items-start">
          <i class="fas fa-exclamation-triangle text-red-400 mr-3 mt-0.5"></i>
          <div>
            <p class="text-white font-medium mb-2">Please fix the following errors:</p>
            <ul class="text-red-100 text-sm space-y-1 list-disc list-inside">
              ${errors.map(error => `<li>${error}</li>`).join('')}
            </ul>
          </div>
        </div>
      `;

      const form = document.getElementById('registrationForm');
      form.parentNode.insertBefore(errorAlert, form);
      
      // Scroll to show errors
      errorAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function isValidEmail(email) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(email);
    }

    // Add ripple effect to buttons
    document.querySelectorAll('.btn-premium').forEach(button => {
      button.addEventListener('click', function(e) {
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