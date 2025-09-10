@extends('master')

@section('content')
<div class="page-content">
    <div class="error-container">
        <div class="error-content">
            <div class="error-icon">
                <h1>500</h1>
            </div>
            <div class="error-message">
                <h2>Server Error</h2>
                <p>Something went wrong on our server. Please try again later.</p>
                <a href="{{ route('dashboard') }}" class="btn-primary">
                    Return to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.error-container {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 80vh;
    padding: 2rem;
}

.error-content {
    text-align: center;
    max-width: 500px;
}

.error-icon h1 {
    font-size: 6rem;
    font-weight: 700;
    color: #ff6b6b;
    margin: 0;
    line-height: 1;
}

.error-message h2 {
    font-size: 1.5rem;
    color: #1a1a1a;
    margin: 1rem 0 0.5rem 0;
}

.error-message p {
    color: #666;
    margin-bottom: 2rem;
    font-size: 1.1rem;
}

.btn-primary {
    display: inline-block;
    background: #ff6b6b;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-primary:hover {
    background: #ff5252;
    transform: translateY(-1px);
    color: white;
    text-decoration: none;
}
</style>
@endsection
