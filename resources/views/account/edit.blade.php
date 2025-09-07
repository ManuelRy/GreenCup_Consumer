@extends('master')

@section('content')
    <div class="container">
        <!-- Header with back button -->
        <div class="account-header">
            <div class="header-nav">
                <a href="{{ route('account') }}" class="back-btn">
                    <span>←</span>
                </a>
                <h2>Edit Profile</h2>
                <div class="placeholder-btn"></div>
            </div>
        </div>

        <div class="edit-form-container">
            <form action="{{ route('account.profile.update') }}" method="POST" class="edit-form">
                @csrf
                @method('PUT')

                <div class="form-section">
                    <h3 class="form-section-title">Personal Information</h3>

                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $consumer->full_name) }}" required>
                        @error('full_name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ $consumer->email }}" readonly class="readonly-field">
                        <small class="field-note">Email cannot be changed</small>
                    </div>

                    <div class="form-group">
                        <label for="phone_number">Phone Number (Optional)</label>
                        <input type="tel" id="phone_number" name="phone_number" value="{{ old('phone_number', $consumer->phone_number) }}">
                        @error('phone_number')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender', $consumer->gender) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $consumer->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $consumer->gender) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth (Optional)</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $consumer->date_of_birth ? $consumer->date_of_birth->format('Y-m-d') : '') }}" max="{{ date('Y-m-d', strtotime('-1 day')) }}">
                        @error('date_of_birth')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('account') }}" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-save">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            background: #f8f8f8;
        }

        /* Container */
        .container {
            background: #ffffff;
            min-height: 100vh;
            position: relative;
            max-width: 100%;
            width: 100%;
        }

        @media (min-width: 768px) {
            .container {
                border-radius: 16px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            }
        }

        /* Header */
        .account-header {
            background: #1a1a1a;
            padding: 16px 20px;
            color: white;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            font-size: 20px;
            font-weight: bold;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }

        .header-nav h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            flex: 1;
            text-align: center;
        }

        .placeholder-btn {
            width: 40px;
            height: 40px;
        }

        /* Form Container */
        .edit-form-container {
            padding: 20px;
        }

        .edit-form {
            max-width: 500px;
            margin: 0 auto;
        }

        .form-section {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 20px;
            border: 1px solid #f0f0f0;
        }

        .form-section-title {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #f0f0f0;
            border-radius: 8px;
            font-size: 16px;
            background: #fff;
            transition: all 0.2s ease;
        }

        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #f0f0f0;
            border-radius: 8px;
            font-size: 16px;
            background: #fff;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #1a1a1a;
            background: #f8f9fa;
        }

        .readonly-field {
            background: #f5f5f5 !important;
            color: #666 !important;
            cursor: not-allowed !important;
        }

        .field-note {
            color: #666;
            font-size: 12px;
            margin-top: 4px;
            font-style: italic;
        }

        .error-message {
            color: #ef4444;
            font-size: 12px;
            margin-top: 4px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            padding: 20px;
        }

        .btn-cancel, .btn-save {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            min-width: 120px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-cancel {
            background: #f0f0f0;
            color: #666;
        }

        .btn-cancel:hover {
            background: #e0e0e0;
            text-decoration: none;
            color: #666;
        }

        .btn-save {
            background: #1a1a1a;
            color: white;
        }

        .btn-save:hover {
            background: #333;
        }

        .btn-cancel:active, .btn-save:active {
            transform: scale(0.95);
        }

        /* Mobile Responsive */
        @media (max-width: 480px) {
            .edit-form-container {
                padding: 16px;
            }

            .form-section {
                padding: 20px;
            }

            .form-actions {
                flex-direction: column;
                padding: 16px;
            }

            .btn-cancel, .btn-save {
                width: 100%;
            }
        }
    </style>
@endsection
