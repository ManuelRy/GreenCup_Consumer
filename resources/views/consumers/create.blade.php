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
      --form-width: clamp(320px, 95vw, 520px);
      --form-padding: clamp(20px, 4vw, 40px);

      /* Responsive elements */
      --button-height: clamp(44px, 8vw, 56px);
      --input-height: clamp(44px, 8vw, 56px);
      --icon-size: clamp(40px, 8vw, 56px);
      --back-btn-size: clamp(40px, 7vw, 48px);
      --border-radius: clamp(8px, 1.5vw, 12px);
      --border-radius-lg: clamp(12px, 2vw, 20px);
      --border-radius-xl: clamp(16px, 3vw, 24px);

      /* Form layout */
      --form-gap: clamp(16px, 3vw, 24px);
      --section-gap: clamp(24px, 4vw, 32px);
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
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
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
      0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.7; }
      50% { transform: translateY(-20px) rotate(180deg); opacity: 1; }
    }
    
    /* Glassmorphism effect - Enhanced and Responsive */
    .glass {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    /* Mobile Back Button - Responsive */
    .mobile-back-btn {
      position: fixed;
      top: var(--spacing-lg);
      left: var(--spacing-lg);
      z-index: 50;
      display: none;
      align-items: center;
      justify-content: center;
      width: var(--back-btn-size);
      height: var(--back-btn-size);
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      color: white;
      text-decoration: none;
      transition: all 0.3s ease;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }

    .mobile-back-btn:hover {
      background: rgba(255, 255, 255, 0.25);
      transform: translateX(-3px);
      color: white;
    }

    .mobile-back-btn i {
      font-size: clamp(16px, 3vw, 20px);
    }

    /* Main Container - Fully Responsive */
    .register-container {
      width: 100%;
      max-width: var(--form-width);
      margin: 0 auto;
    }

    .register-card {
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
    .register-header {
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
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }

    .logo-icon {
      font-size: clamp(20px, 4vw, 28px);
      color: white;
    }

    .register-title {
      font-size: var(--font-display);
      font-weight: 700;
      color: white;
      margin-bottom: var(--spacing-sm);
      line-height: 1.2;
    }

    .register-subtitle {
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
    .register-form {
      display: flex;
      flex-direction: column;
      gap: var(--form-gap);
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
      font-size: var(--font-base);
      gap: var(--spacing-sm);
    }

    .label-icon {
      color: #10b981;
      font-size: var(--font-sm);
      flex-shrink: 0;
    }

    .label-optional {
      color: #d1fae5;
      font-weight: 400;
      font-size: var(--font-sm);
    }

    .form-input-wrapper {
      position: relative;
    }

    .form-input, .form-select {
      width: 100%;
      height: var(--input-height);
      padding: 0 var(--spacing-lg);
      border: 2px solid rgba(16, 185, 129, 0.3);
      border-radius: var(--border-radius-lg);
      background: rgba(255, 255, 255, 0.1);
      color: white !important;
      font-size: var(--font-base);
      outline: none;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      backdrop-filter: blur(10px);
    }

    .form-input::placeholder {
      color: rgba(255, 255, 255, 0.7) !important;
      font-size: var(--font-sm);
    }

    .form-input:focus, .form-select:focus {
      border-color: #10B981 !important;
      box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2) !important;
      transform: translateY(-1px);
      background: rgba(255, 255, 255, 0.15) !important;
    }

    .form-input.error, .form-select.error {
      border-color: #ef4444 !important;
      box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.2) !important;
    }

    /* Select styling - Responsive */
    .form-select {
      cursor: pointer;
      appearance: none;
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23ffffff' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
      background-position: right var(--spacing-lg) center;
      background-repeat: no-repeat;
      background-size: 1.5em 1.5em;
      padding-right: calc(var(--spacing-lg) * 2.5);
    }

    .form-select option {
      background: #047857 !important;
      color: white !important;
      padding: var(--spacing-sm);
    }

    /* Password Input - Responsive */
    .password-input {
      padding-right: calc(var(--spacing-lg) * 2.5);
    }

    .password-toggle {
      position: absolute;
      right: var(--spacing-lg);
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #10b981;
      font-size: var(--font-base);
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

    /* Login Link Section - Responsive */
    .login-section {
      margin-top: var(--section-gap);
      text-align: center;
    }

    .login-text {
      color: #d1fae5;
      margin-bottom: var(--spacing-lg);
      font-size: var(--font-lg);
    }

    .login-link {
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

    .login-link:after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: -4px;
      left: 50%;
      background: #10B981;
      transition: all 0.3s ease;
    }

    .login-link:hover:after {
      width: 100%;
      left: 0;
    }

    .login-link:hover {
      transform: translateY(-2px);
      color: #d1fae5;
    }

    /* Guest Link Button - Responsive */
    .guest-link {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: var(--spacing-md);
      padding: var(--spacing-lg) var(--spacing-xxxl);
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-radius: var(--border-radius-lg);
      color: white;
      font-weight: 600;
      font-size: var(--font-lg);
      text-decoration: none;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      min-height: 44px;
    }

    .guest-link:hover {
      background: rgba(255, 255, 255, 0.3);
      border-color: rgba(255, 255, 255, 0.5);
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .guest-link i {
      font-size: var(--font-xl);
    }

    /* Desktop Back Button - Responsive */
    .desktop-back-section {
      margin-top: var(--spacing-xl);
      text-align: center;
    }

    .desktop-back-link {
      display: inline-flex;
      align-items: center;
      gap: var(--spacing-md);
      color: #d1fae5;
      font-size: var(--font-lg);
      text-decoration: none;
      transition: all 0.3s ease;
      position: relative;
    }

    .desktop-back-link:after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: -2px;
      left: 50%;
      background: #10B981;
      transition: all 0.3s ease;
    }

    .desktop-back-link:hover:after {
      width: 100%;
      left: 0;
    }

    .desktop-back-link:hover {
      color: white;
      transform: translateY(-1px);
    }

    /* Form Layout - Two Column on Larger Screens */
    .form-row {
      display: grid;
      grid-template-columns: 1fr;
      gap: var(--form-gap);
    }

    /* Responsive Breakpoints */

    /* Large screens */
    @media (min-width: 1200px) {
      .register-card {
        padding: clamp(40px, 6vw, 60px);
      }

      .form-row {
        grid-template-columns: 1fr 1fr;
      }
    }

    /* Medium to large screens */
    @media (min-width: 768px) {
      .mobile-back-btn {
        display: none;
      }

      .desktop-back-section {
        display: block;
      }

      .form-row.two-col {
        grid-template-columns: 1fr 1fr;
      }
    }

    /* Medium screens (tablets) */
    @media (max-width: 768px) {
      .bg-animated {
        padding: var(--spacing-md);
        align-items: flex-start;
        padding-top: clamp(60px, 10vh, 80px);
        padding-bottom: var(--spacing-xl);
      }

      .mobile-back-btn {
        display: flex;
      }

      .desktop-back-section {
        display: none;
      }

      .particle {
        display: none; /* Hide particles on mobile for better performance */
      }

      .register-card {
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
      }

      .form-input, .form-select {
        font-size: var(--font-sm);
      }

      .form-input::placeholder {
        font-size: var(--font-xs);
      }

      .form-row {
        grid-template-columns: 1fr;
      }
    }

    /* Small screens (mobile portrait) */
    @media (max-width: 480px) {
      :root {
        --form-gap: clamp(12px, 3vw, 20px);
        --section-gap: clamp(20px, 4vw, 28px);
      }

      .bg-animated {
        padding: var(--spacing-sm);
        padding-top: clamp(50px, 8vh, 60px);
      }

      .register-card {
        padding: clamp(16px, 4vw, 28px);
      }

      .register-header {
        margin-bottom: var(--spacing-xxl);
      }

      .logo-container {
        margin-bottom: var(--spacing-lg);
      }

      .register-form {
        gap: var(--form-gap);
      }

      .login-section {
        margin-top: var(--section-gap);
      }

      /* Ensure forms are not cut off on very small screens */
      .form-input, .form-select {
        min-height: 44px;
      }

      .submit-button {
        min-height: 48px;
      }

      .mobile-back-btn {
        top: var(--spacing-md);
        left: var(--spacing-md);
      }
    }

    /* Extra small screens */
    @media (max-width: 360px) {
      :root {
        --form-padding: clamp(12px, 3vw, 20px);
        --form-gap: clamp(10px, 2.5vw, 16px);
      }

      .bg-animated {
        padding: var(--spacing-xs);
      }

      .alert {
        padding: var(--spacing-md);
      }

      .alert-content {
        gap: var(--spacing-sm);
      }
    }

    /* Landscape orientation on mobile */
    @media (max-height: 600px) and (orientation: landscape) {
      .bg-animated {
        align-items: flex-start;
        padding-top: var(--spacing-md);
        padding-bottom: var(--spacing-md);
      }

      .register-header {
        margin-bottom: var(--spacing-lg);
      }

      .logo-container {
        margin-bottom: var(--spacing-sm);
      }

      .login-section {
        margin-top: var(--spacing-lg);
      }

      .desktop-back-section {
        margin-top: var(--spacing-md);
      }
    }

    /* Very short screens */
    @media (max-height: 500px) {
      .register-header {
        margin-bottom: var(--spacing-md);
      }

      .logo-container {
        width: clamp(32px, 6vw, 40px);
        height: clamp(32px, 6vw, 40px);
        margin-bottom: var(--spacing-sm);
      }

      .logo-icon {
        font-size: clamp(16px, 3vw, 20px);
      }

      .register-subtitle {
        display: none;
      }
    }

    /* High DPI displays */
    @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
      .register-card {
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
      }
    }

    /* Touch device optimizations */
    @media (hover: none) and (pointer: coarse) {
      .submit-button,
      .password-toggle,
      .login-link,
      .desktop-back-link,
      .mobile-back-btn {
        min-height: 44px;
        min-width: 44px;
      }

      .form-input, .form-select {
        min-height: 48px;
      }

      /* Increase touch targets */
      .password-toggle {
        padding: var(--spacing-sm);
      }

      /* Improve form interactions on touch devices */
      .form-input:focus, .form-select:focus {
        transform: none; /* Remove transform on touch devices to prevent zooming issues */
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
      .register-card {
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.2);
      }

      .form-input, .form-select {
        background: rgba(0, 0, 0, 0.2);
      }
    }

    /* Focus styles for accessibility */
    .form-input:focus,
    .form-select:focus,
    .submit-button:focus,
    .login-link:focus,
    .desktop-back-link:focus,
    .mobile-back-btn:focus,
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

    .space-y-6-fallback > * + * {
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
  
  <!-- Mobile Back Button -->
  <a href="#" onclick="goBack()" class="mobile-back-btn" aria-label="Go back">
    <i class="fas fa-arrow-left"></i>
  </a>
  
  <!-- Main Registration Container -->
  <div class="register-container">
    <div class="register-card">
      <!-- Logo and Header -->
      <div class="register-header">
        <div class="logo-container">
          <i class="fas fa-leaf logo-icon"></i>
        </div>
        <h1 class="register-title">GreenCup</h1>
        <p class="register-subtitle">Join our sustainable community</p>
      </div>

      <!-- Success Message -->
      @if(session('success'))
        <div class="alert alert-success">
          <div class="alert-content">
            <i class="fas fa-check-circle alert-icon" style="color: #10b981;"></i>
            <span class="alert-text">{{ session('success') }}</span>
          </div>
        </div>
      @endif

      <!-- Error Messages -->
      @if($errors->any())
        <div class="alert alert-error">
          <div class="alert-content">
            <i class="fas fa-exclamation-triangle alert-icon" style="color: #ef4444;"></i>
            <div>
              <p class="alert-text">Please fix the following errors:</p>
              <ul class="alert-list">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          </div>
        </div>
      @endif

      <form id="registrationForm" action="{{ route('consumers.store') }}" method="POST" novalidate class="register-form">
        @csrf
        
        <!-- Full Name -->
        <div class="form-group">
          <label for="full_name" class="form-label">
            <i class="fas fa-user label-icon"></i>
            Full Name
          </label>
          <div class="form-input-wrapper">
            <input
              type="text"
              id="full_name"
              name="full_name"
              value="{{ old('full_name') }}"
              class="form-input @error('full_name') error @enderror"
              placeholder="Enter your full name"
              required
              autocomplete="name"
              aria-describedby="full_name-error"
            />
          </div>
          @error('full_name')
            <div class="form-error" id="full_name-error">
              <i class="fas fa-exclamation-circle error-icon"></i>
              <p>{{ $message }}</p>
            </div>
          @enderror
        </div>

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
              value="{{ old('email') }}"
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

        <!-- Phone Number and Gender Row -->
        <div class="form-row two-col">
          <!-- Phone Number -->
          <div class="form-group">
            <label for="phone_number" class="form-label">
              <i class="fas fa-phone label-icon"></i>
              Phone Number 
              <span class="label-optional">(optional)</span>
            </label>
            <div class="form-input-wrapper">
              <input
                type="tel"
                id="phone_number"
                name="phone_number"
                value="{{ old('phone_number') }}"
                class="form-input @error('phone_number') error @enderror"
                placeholder="Enter phone number"
                autocomplete="tel"
                aria-describedby="phone_number-error"
              />
            </div>
            @error('phone_number')
              <div class="form-error" id="phone_number-error">
                <i class="fas fa-exclamation-circle error-icon"></i>
                <p>{{ $message }}</p>
              </div>
            @enderror
          </div>

          <!-- Gender -->
          <div class="form-group">
            <label for="gender" class="form-label">
              <i class="fas fa-venus-mars label-icon"></i>
              Gender
            </label>
            <div class="form-input-wrapper">
              <select
                id="gender"
                name="gender"
                class="form-select @error('gender') error @enderror"
                required
                aria-describedby="gender-error"
              >
                <option value="">-- Select Gender --</option>
                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
              </select>
            </div>
            @error('gender')
              <div class="form-error" id="gender-error">
                <i class="fas fa-exclamation-circle error-icon"></i>
                <p>{{ $message }}</p>
              </div>
            @enderror
          </div>
        </div>

        <!-- Date of Birth -->
        <div class="form-group">
          <label for="date_of_birth" class="form-label">
            <i class="fas fa-calendar label-icon"></i>
            Date of Birth 
            <span class="label-optional">(optional)</span>
          </label>
          <div class="form-input-wrapper">
            <input
              type="date"
              id="date_of_birth"
              name="date_of_birth"
              value="{{ old('date_of_birth') }}"
              class="form-input @error('date_of_birth') error @enderror"
              autocomplete="bday"
              aria-describedby="date_of_birth-error"
            />
          </div>
          @error('date_of_birth')
            <div class="form-error" id="date_of_birth-error">
              <i class="fas fa-exclamation-circle error-icon"></i>
              <p>{{ $message }}</p>
            </div>
          @enderror
        </div>

        <!-- Password and Confirm Password Row -->
        <div class="form-row two-col">
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
                placeholder="Create a strong password"
                required
                autocomplete="new-password"
                aria-describedby="password-error password-help"
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
            <div id="password-help" class="sr-only">Password must be at least 8 characters long</div>
            @error('password')
              <div class="form-error" id="password-error">
                <i class="fas fa-exclamation-circle error-icon"></i>
                <p>{{ $message }}</p>
              </div>
            @enderror
          </div>

          <!-- Confirm Password -->
          <div class="form-group">
            <label for="password_confirmation" class="form-label">
              <i class="fas fa-lock label-icon"></i>
              Confirm Password
            </label>
            <div class="form-input-wrapper">
              <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="form-input password-input @error('password_confirmation') error @enderror"
                placeholder="Confirm your password"
                required
                autocomplete="new-password"
                aria-describedby="password_confirmation-error"
              />
              <button
                type="button"
                onclick="togglePassword('password_confirmation')"
                class="password-toggle"
                aria-label="Toggle password confirmation visibility"
              >
                <i id="confirmPasswordToggle" class="fas fa-eye"></i>
              </button>
            </div>
            @error('password_confirmation')
              <div class="form-error" id="password_confirmation-error">
                <i class="fas fa-exclamation-circle error-icon"></i>
                <p>{{ $message }}</p>
              </div>
            @enderror
          </div>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          class="submit-button"
          aria-describedby="submit-help"
        >
          <i class="fas fa-user-plus button-icon"></i>
          Create Account
        </button>
        <div id="submit-help" class="sr-only">Submit the registration form to create your account</div>
      </form>

      <!-- Guest Mode Button -->
      <div style="margin-top: var(--spacing-xxl); text-align: center;">
        <a href="{{ route('guest.dashboard') }}" class="guest-link">
          <i class="fas fa-eye"></i>
          Continue as Guest
        </a>
      </div>

      <!-- Login Link -->
      <div class="login-section">
        <p class="login-text">Already have an account?</p>
        <a href="{{ route('login') }}" class="login-link">
          <i class="fas fa-sign-in-alt"></i>
          Sign In Instead
        </a>
      </div>

      <!-- Desktop Back Button -->
      <div class="desktop-back-section">
        <a href="#" onclick="goBack()" class="desktop-back-link">
          <i class="fas fa-arrow-left"></i>
          Go Back
        </a>
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
        console.log('Tailwind fallback activated');
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

      // Initialize form enhancements
      initializeFormEnhancements();
    });

    // Enhanced form functionality
    function initializeFormEnhancements() {
      const form = document.getElementById('registrationForm');
      const inputs = form.querySelectorAll('input, select');
      
      // Add real-time validation feedback
      inputs.forEach(input => {
        input.addEventListener('blur', validateField);
        input.addEventListener('input', clearErrors);
      });

      // Handle form submission with better UX
      form.addEventListener('submit', handleFormSubmit);

      // Auto-focus first empty field
      const firstEmptyField = Array.from(inputs).find(input => !input.value && input.hasAttribute('required'));
      if (firstEmptyField) {
        firstEmptyField.focus();
      }

      // Add ripple effect to submit button
      addRippleEffect();

      // Password strength indicator
      const passwordField = document.getElementById('password');
      if (passwordField) {
        passwordField.addEventListener('input', checkPasswordStrength);
      }

      // Password confirmation matching
      const confirmField = document.getElementById('password_confirmation');
      if (confirmField) {
        confirmField.addEventListener('input', checkPasswordMatch);
      }
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

      // Skip validation for optional fields that are empty
      if (!field.hasAttribute('required') && !field.value.trim()) {
        return;
      }

      // Full name validation
      if (field.name === 'full_name') {
        if (!field.value.trim()) {
          errors.push('Full name is required');
        } else if (field.value.trim().length < 2) {
          errors.push('Full name must be at least 2 characters');
        }
      }

      // Email validation
      if (field.name === 'email') {
        if (!field.value.trim()) {
          errors.push('Email address is required');
        } else if (!isValidEmail(field.value)) {
          errors.push('Please enter a valid email address');
        }
      }

      // Phone validation (if provided)
      if (field.name === 'phone_number' && field.value.trim()) {
        if (!isValidPhone(field.value)) {
          errors.push('Please enter a valid phone number');
        }
      }

      // Gender validation
      if (field.name === 'gender') {
        if (!field.value) {
          errors.push('Gender selection is required');
        }
      }

      // Password validation
      if (field.name === 'password') {
        if (!field.value) {
          errors.push('Password is required');
        } else if (field.value.length < 8) {
          errors.push('Password must be at least 8 characters long');
        }
      }

      // Password confirmation validation
      if (field.name === 'password_confirmation') {
        const passwordField = document.getElementById('password');
        if (!field.value) {
          errors.push('Password confirmation is required');
        } else if (field.value !== passwordField.value) {
          errors.push('Passwords do not match');
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

      // Also clear client-side errors
      const clientErrorAlert = document.getElementById('clientErrorAlert');
      if (clientErrorAlert) {
        clientErrorAlert.remove();
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
      const formData = new FormData(form);
      const errors = [];
      let hasErrors = false;

      // Clear all previous errors
      form.querySelectorAll('.form-input, .form-select').forEach(input => {
        input.classList.remove('error');
      });
      form.querySelectorAll('.form-error').forEach(error => {
        error.style.display = 'none';
      });

      // Remove any existing client error alerts
      const existingAlert = document.getElementById('clientErrorAlert');
      if (existingAlert) {
        existingAlert.remove();
      }

      // Validate required fields
      const fullName = formData.get('full_name').trim();
      const email = formData.get('email').trim();
      const gender = formData.get('gender');
      const password = formData.get('password');
      const passwordConfirmation = formData.get('password_confirmation');
      const phone = formData.get('phone_number').trim();

      // Full name validation
      if (!fullName) {
        errors.push({field: 'full_name', message: 'Full name is required'});
        hasErrors = true;
      } else if (fullName.length < 2) {
        errors.push({field: 'full_name', message: 'Full name must be at least 2 characters'});
        hasErrors = true;
      }

      // Email validation
      if (!email) {
        errors.push({field: 'email', message: 'Email address is required'});
        hasErrors = true;
      } else if (!isValidEmail(email)) {
        errors.push({field: 'email', message: 'Please enter a valid email address'});
        hasErrors = true;
      }

      // Phone validation (optional)
      if (phone && !isValidPhone(phone)) {
        errors.push({field: 'phone_number', message: 'Please enter a valid phone number'});
        hasErrors = true;
      }

      // Gender validation
      if (!gender) {
        errors.push({field: 'gender', message: 'Gender selection is required'});
        hasErrors = true;
      }

      // Password validation
      if (!password) {
        errors.push({field: 'password', message: 'Password is required'});
        hasErrors = true;
      } else if (password.length < 8) {
        errors.push({field: 'password', message: 'Password must be at least 8 characters long'});
        hasErrors = true;
      }

      // Password confirmation validation
      if (!passwordConfirmation) {
        errors.push({field: 'password_confirmation', message: 'Password confirmation is required'});
        hasErrors = true;
      } else if (password !== passwordConfirmation) {
        errors.push({field: 'password_confirmation', message: 'Passwords do not match'});
        hasErrors = true;
      }

      // Show errors and prevent submission if needed
      if (hasErrors) {
        e.preventDefault();
        
        // Show field-specific errors
        errors.forEach(error => {
          const field = form.querySelector(`[name="${error.field}"]`);
          field.classList.add('error');
          showFieldError(error.field, error.message);
        });

        // Create summary error alert
        showClientErrors(errors.map(e => e.message));

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
        Creating Account...
      `;
      submitButton.disabled = true;

      // Re-enable button after timeout (in case of server errors)
      setTimeout(() => {
        submitButton.innerHTML = originalHTML;
        submitButton.disabled = false;
      }, 15000);
    }

    function showClientErrors(errors) {
      // Create a temporary error display for client-side validation
      const existingAlert = document.getElementById('clientErrorAlert');
      if (existingAlert) {
        existingAlert.remove();
      }

      const errorAlert = document.createElement('div');
      errorAlert.id = 'clientErrorAlert';
      errorAlert.className = 'alert alert-error';
      errorAlert.innerHTML = `
        <div class="alert-content">
          <i class="fas fa-exclamation-triangle alert-icon" style="color: #ef4444;"></i>
          <div>
            <p class="alert-text">Please fix the following errors:</p>
            <ul class="alert-list">
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

    function checkPasswordStrength(event) {
      const password = event.target.value;
      const strengthIndicator = document.getElementById('password-strength');
      
      // Remove existing indicator if any
      if (strengthIndicator) {
        strengthIndicator.remove();
      }

      if (password.length === 0) return;

      let strength = 0;
      let feedback = [];

      // Length check
      if (password.length >= 8) strength += 1;
      else feedback.push('At least 8 characters');

      // Uppercase check
      if (/[A-Z]/.test(password)) strength += 1;
      else feedback.push('One uppercase letter');

      // Lowercase check
      if (/[a-z]/.test(password)) strength += 1;
      else feedback.push('One lowercase letter');

      // Number check
      if (/\d/.test(password)) strength += 1;
      else feedback.push('One number');

      // Special character check
      if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength += 1;
      else feedback.push('One special character');

      const strengthColors = ['#ef4444', '#f59e0b', '#10b981', '#059669'];
      const strengthLabels = ['Very Weak', 'Weak', 'Good', 'Strong'];
      
      // Only show if password is being typed
      if (password.length >= 1) {
        const indicator = document.createElement('div');
        indicator.id = 'password-strength';
        indicator.style.cssText = `
          margin-top: var(--spacing-sm);
          padding: var(--spacing-sm);
          border-radius: var(--border-radius);
          background: rgba(255, 255, 255, 0.1);
          font-size: var(--font-xs);
          color: white;
        `;
        
        const strengthIndex = Math.min(Math.floor(strength), 3);
        indicator.innerHTML = `
          <div style="display: flex; align-items: center; gap: var(--spacing-sm); margin-bottom: var(--spacing-xs);">
            <span>Strength:</span>
            <span style="color: ${strengthColors[strengthIndex]}; font-weight: 600;">${strengthLabels[strengthIndex]}</span>
          </div>
          ${feedback.length > 0 ? `<div style="font-size: var(--font-xs); opacity: 0.8;">Missing: ${feedback.join(', ')}</div>` : ''}
        `;
        
        event.target.parentNode.parentNode.appendChild(indicator);
      }
    }

    function checkPasswordMatch(event) {
      const confirmPassword = event.target.value;
      const password = document.getElementById('password').value;
      const matchIndicator = document.getElementById('password-match');
      
      // Remove existing indicator if any
      if (matchIndicator) {
        matchIndicator.remove();
      }

      if (confirmPassword.length === 0) return;

      const isMatch = password === confirmPassword;
      const indicator = document.createElement('div');
      indicator.id = 'password-match';
      indicator.style.cssText = `
        margin-top: var(--spacing-sm);
        padding: var(--spacing-sm);
        border-radius: var(--border-radius);
        background: rgba(255, 255, 255, 0.1);
        font-size: var(--font-xs);
        color: ${isMatch ? '#10b981' : '#ef4444'};
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
      `;
      
      indicator.innerHTML = `
        <i class="fas fa-${isMatch ? 'check' : 'times'}"></i>
        <span>${isMatch ? 'Passwords match' : 'Passwords do not match'}</span>
      `;
      
      event.target.parentNode.parentNode.appendChild(indicator);
    }

    // Toggle password visibility
    function togglePassword(inputId) {
      const input = document.getElementById(inputId);
      const toggleIcon = document.getElementById(inputId === 'password' ? 'passwordToggle' : 'confirmPasswordToggle');
      
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

    // Go back function
    function goBack() {
      if (window.history.length > 1) {
        window.history.back();
      } else {
        window.location.href = '/'; // Fallback to home page
      }
    }

    function isValidEmail(email) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(email);
    }

    function isValidPhone(phone) {
      // Basic phone validation - adjust regex based on your requirements
      const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
      const cleanPhone = phone.replace(/[\s\-\(\)]/g, '');
      return phoneRegex.test(cleanPhone) && cleanPhone.length >= 7;
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

      // Auto-advance from select fields on Enter
      if (e.key === 'Enter' && e.target.tagName === 'SELECT') {
        const formElements = Array.from(document.querySelectorAll('input, select, button'));
        const currentIndex = formElements.indexOf(e.target);
        const nextElement = formElements[currentIndex + 1];
        if (nextElement) {
          nextElement.focus();
        }
      }
    });

    // Handle window resize for responsive adjustments
    window.addEventListener('resize', function() {
      // Adjust viewport height on mobile browsers
      if (window.innerHeight < 600 && window.orientation !== undefined) {
        document.documentElement.style.setProperty('--vh', `${window.innerHeight * 0.01}px`);
      }

      // Close any open dropdowns on resize
      const activeElement = document.activeElement;
      if (activeElement && activeElement.tagName === 'SELECT') {
        activeElement.blur();
      }
    });

    // Add CSS for dynamic elements
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

      /* Improved select styling for different browsers */
      .form-select::-ms-expand {
        display: none;
      }

      .form-select option {
        padding: var(--spacing-sm) var(--spacing-lg);
      }

      /* Better focus management for mobile */
      @media (hover: none) and (pointer: coarse) {
        .form-input:focus,
        .form-select:focus {
          outline: none;
          border-color: #10B981 !important;
          box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3) !important;
        }
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

    // Auto-save form data to prevent data loss
    const form = document.getElementById('registrationForm');
    if (form && 'sessionStorage' in window) {
      // Save form data on input
      form.addEventListener('input', function() {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        sessionStorage.setItem('registrationFormData', JSON.stringify(data));
      });

      // Restore form data on page load
      const savedData = sessionStorage.getItem('registrationFormData');
      if (savedData) {
        try {
          const data = JSON.parse(savedData);
          Object.entries(data).forEach(([key, value]) => {
            const field = form.querySelector(`[name="${key}"]`);
            if (field && value) {
              field.value = value;
            }
          });
        } catch (e) {
          console.log('Could not restore form data');
        }
      }

      // Clear saved data on successful submission
      form.addEventListener('submit', function() {
        sessionStorage.removeItem('registrationFormData');
      });
    }
  </script>
</body>
</html>