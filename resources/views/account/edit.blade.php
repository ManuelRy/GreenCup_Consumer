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
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $consumer->name) }}" required>
                        @error('name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $consumer->email) }}" required>
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number (Optional)</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', $consumer->phone) }}">
                        @error('phone')
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

        .form-group input:focus {
            outline: none;
            border-color: #1a1a1a;
            background: #f8f9fa;
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
