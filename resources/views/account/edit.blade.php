@extends('master')

@section('content')
    <div class="settings-page-container page-content">
        <div class="settings-container">
            <!-- Header with back button -->
            <div class="settings-header">
                <div class="header-nav">
                    <a href="{{ route('account') }}" class="back-btn" aria-label="Back to Account">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="m15 18-6-6 6-6" />
                        </svg>
                    </a>
                    <h1>Edit Profile</h1>
                    <div class="placeholder-btn"></div>
                </div>
            </div>

            <div class="edit-form-container">
                <form action="{{ route('account.profile.update') }}" method="POST" class="edit-form">
                    @csrf
                    @method('PUT')

                    <div class="form-section">
                        <h2 class="form-section-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                            Personal Information
                        </h2>

                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" id="full_name" name="full_name"
                                value="{{ old('full_name', $consumer->full_name) }}" required>
                            @error('full_name')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ $consumer->email }}" readonly
                                class="readonly-field">
                            <small class="field-note">Email cannot be changed</small>
                        </div>

                        <div class="form-group">
                            <label for="phone_number">Phone Number (Optional)</label>
                            <input type="tel" id="phone_number" name="phone_number"
                                value="{{ old('phone_number', $consumer->phone_number) }}">
                            @error('phone_number')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $consumer->gender) == 'male' ? 'selected' : '' }}>
                                    Male</option>
                                <option value="female" {{ old('gender', $consumer->gender) == 'female' ? 'selected' : '' }}>
                                    Female</option>
                                <option value="other" {{ old('gender', $consumer->gender) == 'other' ? 'selected' : '' }}>
                                    Other</option>
                            </select>
                            @error('gender')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="date_of_birth">Date of Birth (Optional)</label>
                            <input type="date" id="date_of_birth" name="date_of_birth"
                                value="{{ old('date_of_birth', $consumer->date_of_birth ? $consumer->date_of_birth->format('Y-m-d') : '') }}"
                                max="{{ date('Y-m-d', strtotime('-1 day')) }}">
                            @error('date_of_birth')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('account') }}" class="btn-cancel">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M18 6 6 18" />
                                <path d="m6 6 12 12" />
                            </svg>
                            Cancel
                        </a>
                        <button type="submit" class="btn-save">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="m9 12 2 2 4-4" />
                                <path
                                    d="M21 12c.552 0 1-.448 1-1V5c0-.552-.448-1-1-1H3c-.552 0-1 .448-1 1v6c0 .552.448 1 1 1h18z" />
                            </svg>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* CSS Variables for Responsive Design */
        :root {
            --primary-color: #1dd1a1;
            --primary-dark: #10ac84;
            --secondary-color: #2e8b57;
            --background-color: #f8f9fa;
            --card-bg: #ffffff;
            --text-primary: #2c3e50;
            --text-secondary: #7f8c8d;
            --text-muted: #95a5a6;
            --border-color: #e8eaed;
            --hover-bg: #f8f9fa;
            --shadow-light: 0 2px 4px rgba(0, 0, 0, 0.08);
            --shadow-medium: 0 4px 12px rgba(0, 0, 0, 0.12);
            --border-radius: 12px;
            --border-radius-lg: 16px;

            /* Responsive spacing */
            --spacing-xs: clamp(4px, 1vw, 8px);
            --spacing-sm: clamp(8px, 2vw, 12px);
            --spacing-md: clamp(12px, 3vw, 16px);
            --spacing-lg: clamp(16px, 4vw, 24px);
            --spacing-xl: clamp(24px, 5vw, 32px);

            /* Responsive font sizes */
            --font-sm: clamp(12px, 2.5vw, 14px);
            --font-base: clamp(14px, 3vw, 16px);
            --font-lg: clamp(16px, 3.5vw, 18px);
            --font-xl: clamp(18px, 4vw, 22px);
            --font-xxl: clamp(20px, 5vw, 24px);
        }

        /* Reset and Base Styles */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        /* Universal box-sizing and overflow prevention */
        * {
            box-sizing: border-box;
        }

        body, html {
            overflow-x: hidden;
            width: 100%;
        }

        /* Main Container */
        .settings-page-container {
            min-height: calc(100vh - var(--navbar-height));
            background: var(--background-color);
            padding: var(--spacing-md);
        }

        .settings-container {
            max-width: 600px;
            margin: 0 auto;
            background: var(--card-bg);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-medium);
            overflow: hidden;
        }

        /* Header */
        .settings-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            padding: var(--spacing-lg);
            color: white;
            position: sticky;
            top: var(--navbar-height);
            z-index: 10;
        }

        .header-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: var(--spacing-md);
            max-width: 100%;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            flex-shrink: 0;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-1px);
            color: white;
        }

        .header-nav h1 {
            margin: 0;
            font-size: var(--font-xl);
            font-weight: 600;
            flex: 1;
            text-align: center;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            min-width: 0;
        }

        .placeholder-btn {
            width: 44px;
            height: 44px;
            flex-shrink: 0;
        }

        /* Form Container */
        .edit-form-container {
            padding: var(--spacing-xl);
        }

        .edit-form {
            width: 100%;
        }

        /* Form Section */
        .form-section {
            margin-bottom: var(--spacing-xl);
        }

        .form-section-title {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            margin-bottom: var(--spacing-lg);
            font-size: var(--font-lg);
            font-weight: 600;
            color: var(--text-primary);
            padding-bottom: var(--spacing-sm);
            border-bottom: 2px solid var(--border-color);
        }

        /* Form Groups */
        .form-group {
            margin-bottom: var(--spacing-lg);
        }

        .form-group label {
            display: block;
            margin-bottom: var(--spacing-sm);
            font-weight: 600;
            color: var(--text-primary);
            font-size: var(--font-base);
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: var(--spacing-md);
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: var(--font-base);
            transition: all 0.2s ease;
            background: white;
            min-height: 48px;
            box-sizing: border-box;
            /* Touch-friendly */
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(29, 209, 161, 0.1);
        }

        .form-group input.readonly-field {
            background: var(--hover-bg);
            color: var(--text-muted);
            cursor: not-allowed;
        }

        .field-note {
            display: block;
            margin-top: var(--spacing-xs);
            font-size: var(--font-sm);
            color: var(--text-muted);
            font-style: italic;
        }

        .error-message {
            display: block;
            margin-top: var(--spacing-xs);
            color: #e74c3c;
            font-size: var(--font-sm);
            font-weight: 500;
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            gap: var(--spacing-md);
            padding-top: var(--spacing-lg);
            border-top: 1px solid var(--border-color);
        }

        .btn-cancel,
        .btn-save {
            flex: 1;
            padding: var(--spacing-md) var(--spacing-lg);
            border-radius: var(--border-radius);
            font-size: var(--font-base);
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
            min-height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--spacing-sm);
        }

        .btn-cancel {
            background: var(--hover-bg);
            color: var(--text-secondary);
            border: 2px solid var(--border-color);
        }

        .btn-cancel:hover {
            background: var(--border-color);
            color: var(--text-primary);
            text-decoration: none;
        }

        .btn-save {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border: 2px solid transparent;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(29, 209, 161, 0.3);
        }

        .btn-save:active {
            transform: translateY(0);
        }

        /* Responsive Design */
        @media (max-width: 991.98px) {
            .settings-page-container {
                padding: var(--spacing-md);
            }

            .edit-form-container {
                padding: var(--spacing-lg);
            }
        }

        @media (max-width: 767.98px) {
            .settings-page-container {
                padding: var(--spacing-sm);
            }

            .settings-container {
                border-radius: var(--border-radius);
                margin: 0;
            }

            .settings-header {
                padding: var(--spacing-md);
            }

            .header-nav {
                padding: 0 var(--spacing-xs);
            }

            .header-nav h1 {
                font-size: var(--font-lg);
            }

            .edit-form-container {
                padding: var(--spacing-md);
            }

            .form-section {
                margin-bottom: var(--spacing-lg);
            }

            .form-section-title {
                font-size: var(--font-base);
                margin-bottom: var(--spacing-md);
            }

            .form-group {
                margin-bottom: var(--spacing-md);
            }

            .form-group input,
            .form-group select {
                min-height: 48px;
                padding: var(--spacing-md);
                font-size: var(--font-base);
                width: 100%;
                box-sizing: border-box;
            }

            .form-actions {
                flex-direction: column;
                gap: var(--spacing-sm);
                padding-top: var(--spacing-md);
            }

            .btn-cancel,
            .btn-save {
                width: 100%;
                min-height: 48px;
                font-size: var(--font-base);
                padding: var(--spacing-md);
            }
        }

        @media (max-width: 575.98px) {
            .settings-page-container {
                padding: var(--spacing-xs);
            }

            .header-nav h1 {
                font-size: var(--font-base);
            }

            .form-section-title {
                font-size: var(--font-sm);
                flex-direction: column;
                text-align: center;
                gap: var(--spacing-xs);
            }

            .edit-form-container {
                padding: var(--spacing-sm);
            }

            .form-group label {
                font-size: var(--font-sm);
                margin-bottom: var(--spacing-xs);
            }

            .form-group input,
            .form-group select {
                font-size: var(--font-sm);
                min-height: 44px;
                padding: var(--spacing-sm) var(--spacing-md);
            }

            .field-note {
                font-size: var(--font-xs);
            }

            .error-message {
                font-size: var(--font-xs);
            }
        }

        @media (max-width: 390px) {
            .header-nav {
                padding: 0;
            }

            .back-btn,
            .placeholder-btn {
                width: 36px;
                height: 36px;
            }

            .header-nav h1 {
                font-size: var(--font-sm);
            }

            .edit-form-container {
                padding: var(--spacing-xs);
            }

            .form-section {
                margin-bottom: var(--spacing-md);
            }

            .btn-cancel,
            .btn-save {
                min-height: 40px;
                font-size: var(--font-sm);
                padding: var(--spacing-sm);
            }
        }

        /* Loading States */
        .btn-save:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .btn-save:disabled:hover {
            transform: none !important;
            box-shadow: none !important;
        }

        /* Success/Error States */
        .form-group.success input {
            border-color: #27ae60;
        }

        .form-group.error input {
            border-color: #e74c3c;
        }

        /* Animation for smooth interactions */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .edit-form-container {
            animation: fadeIn 0.3s ease;
        }
    </style>
@endsection
