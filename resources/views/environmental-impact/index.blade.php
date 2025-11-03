@extends('master')

@section('content')
<div class="environmental-impact-container">
    <!-- Guest Banner -->
    @if(!auth('consumer')->check())
      <div class="container py-3">
        @include('partials.guest-banner')
      </div>
    @endif

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <div class="hero-icon">🌱</div>
            <h1 class="hero-title">Your Environmental Impact</h1>
            <p class="hero-subtitle">Every eco-friendly choice you make creates a positive ripple effect for our planet</p>
        </div>
        <div class="hero-decoration">
            <div class="floating-element leaf1">🍃</div>
            <div class="floating-element leaf2">🌿</div>
            <div class="floating-element earth">🌍</div>
        </div>
    </div>

    <!-- Impact Stats -->
    <div class="impact-stats">
        <div class="stats-grid">
            <!-- Total Cups Saved -->
            <div class="stat-card cups-card">
                <div class="stat-icon">♻️</div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($total_cups) }}</div>
                    <div class="stat-label">Cups Saved</div>
                    <div class="stat-description">Disposable cups prevented from landfills</div>
                </div>
                <div class="stat-decoration">
                    <div class="ripple"></div>
                </div>
            </div>

            <!-- Stores Visited -->
            <div class="stat-card stores-card">
                <div class="stat-icon">🏪</div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($store_visited) }}</div>
                    <div class="stat-label">{{ $store_visited == 1 ? 'Store' : 'Stores' }} Visited</div>
                    <div class="stat-description">Partner locations supported</div>
                </div>
                <div class="stat-decoration">
                    <div class="ripple"></div>
                </div>
            </div>

            <!-- CO2 Saved -->
            <div class="stat-card co2-card">
                <div class="stat-icon">🌱</div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($total_cups * 20) }}<span class="unit">g</span></div>
                    <div class="stat-label">CO₂ Saved</div>
                    <div class="stat-description">Carbon footprint reduction</div>
                </div>
                <div class="stat-decoration">
                    <div class="ripple"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Environmental Benefits -->
    <div class="benefits-section">
        <h2 class="section-title">🌟 Your Environmental Benefits</h2>
        <div class="benefits-grid">
            @php
                $co2_kg = ($total_cups * 20) / 1000; // Convert grams to kg
            @endphp

            <div class="benefit-card">
                <div class="benefit-icon">🗑️</div>
                <div class="benefit-content">
                    <div class="benefit-number">{{ number_format($total_cups * 8) }}<span class="small-unit">g</span></div>
                    <div class="benefit-label">Waste Prevented</div>
                    <div class="benefit-description">Landfill waste avoided</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="quick-actions-section">
        <h2 class="section-title">⚡ Quick Actions</h2>
        <div class="quick-actions-grid">
            <a href="{{ route('account') }}" class="quick-action-card account-card">
                <div class="action-icon">👤</div>
                <div class="action-label">Account</div>
            </a>
            <a href="{{ route('reward.index') }}" class="quick-action-card rewards-card">
                <div class="action-icon">🎁</div>
                <div class="action-label">Rewards</div>
            </a>
            <a href="{{ route('gallery') }}" class="quick-action-card gallery-card">
                <div class="action-icon">🛍️</div>
                <div class="action-label">Shop Gallery</div>
            </a>
            <a href="{{ route('scan.receipt') }}" class="quick-action-card scan-card highlighted">
                <div class="action-icon">📱</div>
                <div class="action-label">Scan QR</div>
            </a>
            <a href="{{ route('map') }}" class="quick-action-card locations-card">
                <div class="action-icon">📍</div>
                <div class="action-label">Locations</div>
            </a>
            <a href="{{ route('environmental-impact.index') }}" class="quick-action-card environmental-card active">
                <div class="action-icon">🌱</div>
                <div class="action-label">Environmental Tracking</div>
            </a>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="cta-section">
        <div class="cta-content">
            <h2>Keep Making a Difference! 🌟</h2>
            <p>Every sustainable choice counts. Continue your eco-friendly journey and help create a greener future.</p>
            <div class="cta-buttons">
                <a href="{{ route('map') }}" class="btn btn-primary">
                    <span>🗺️</span>
                    Find More Stores
                </a>
                <a href="{{ route('scan.receipt') }}" class="btn btn-secondary">
                    <span>📱</span>
                    Scan Next Receipt
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.environmental-impact-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%);
    padding: 0;
}

/* Hero Section */
.hero-section {
    position: relative;
    background: linear-gradient(135deg, #2d5a27 0%, #4a7c59 100%);
    color: white;
    padding: 60px 20px;
    text-align: center;
    overflow: hidden;
}

.hero-content {
    max-width: 600px;
    margin: 0 auto;
    position: relative;
    z-index: 2;
}

.hero-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    display: inline-block;
    animation: bounce 2s infinite;
}

.hero-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.hero-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    max-width: 500px;
    margin: 0 auto;
    line-height: 1.6;
}

.hero-decoration {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
}

.floating-element {
    position: absolute;
    font-size: 2rem;
    animation: float 3s ease-in-out infinite;
    opacity: 0.3;
}

.leaf1 { top: 20%; left: 10%; animation-delay: 0s; }
.leaf2 { top: 60%; right: 15%; animation-delay: 1s; }
.earth { bottom: 20%; left: 20%; animation-delay: 2s; }

/* Impact Stats */
.impact-stats {
    padding: 40px 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.cups-card {
    border-color: #22c55e;
    background: linear-gradient(135deg, #fff 0%, #f0fdf4 100%);
}

.stores-card {
    border-color: #3b82f6;
    background: linear-gradient(135deg, #fff 0%, #eff6ff 100%);
}

.co2-card {
    border-color: #f59e0b;
    background: linear-gradient(135deg, #fff 0%, #fffbeb 100%);
}

.stat-icon {
    font-size: 3rem;
    margin-bottom: 16px;
    display: block;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1f2937;
    margin-bottom: 8px;
    line-height: 1;
}

.stat-number .unit {
    font-size: 1.5rem;
    color: #6b7280;
    margin-left: 4px;
}

.stat-label {
    font-size: 1.1rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 4px;
}

.stat-description {
    font-size: 0.9rem;
    color: #6b7280;
    line-height: 1.4;
}

.stat-decoration {
    position: absolute;
    top: -50px;
    right: -50px;
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(45deg, rgba(34, 197, 94, 0.1), rgba(34, 197, 94, 0.05));
    pointer-events: none;
}

/* Benefits Section */
.benefits-section {
    padding: 40px 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.section-title {
    text-align: center;
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 30px;
}

.benefits-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.benefit-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb;
}

.benefit-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.benefit-icon {
    font-size: 2.5rem;
    margin-bottom: 16px;
}

.benefit-number {
    font-size: 1.8rem;
    font-weight: 700;
    color: #059669;
    margin-bottom: 8px;
}

.benefit-number .small-unit {
    font-size: 1rem;
    color: #6b7280;
    margin-left: 2px;
}

.benefit-label {
    font-size: 1rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 4px;
}

.benefit-description {
    font-size: 0.85rem;
    color: #6b7280;
    line-height: 1.4;
}

/* Achievements Section */
.achievements-section {
    padding: 40px 20px;
    max-width: 1200px;
    margin: 0 auto;
    background: white;
    border-radius: 20px;
    margin-bottom: 40px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

.achievements-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.achievement-badge {
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
    border-radius: 16px;
    padding: 20px;
    text-align: center;
    transition: all 0.3s ease;
    border: 2px solid #e5e7eb;
}

.achievement-badge.earned {
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    border-color: #22c55e;
    transform: scale(1.02);
}

.achievement-badge.locked {
    opacity: 0.6;
    border-style: dashed;
}

.badge-icon {
    font-size: 2.5rem;
    margin-bottom: 12px;
}

.badge-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 6px;
}

.badge-description {
    font-size: 0.85rem;
    color: #6b7280;
    line-height: 1.4;
}

/* Quick Actions Section */
.quick-actions-section {
    padding: 40px 20px;
    max-width: 1200px;
    margin: 0 auto;
    background: white;
    border-radius: 20px;
    margin-bottom: 40px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 16px;
    margin-top: 24px;
}

.quick-action-card {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 16px;
    padding: 24px 16px;
    text-align: center;
    text-decoration: none;
    color: #374151;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.quick-action-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    text-decoration: none;
    color: #374151;
}

.quick-action-card.highlighted {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: white;
    border-color: #22c55e;
}

.quick-action-card.highlighted:hover {
    color: white;
    box-shadow: 0 8px 25px rgba(34, 197, 94, 0.3);
}

.quick-action-card.active {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    border-color: #3b82f6;
}

.quick-action-card.active:hover {
    color: white;
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
}

.action-icon {
    font-size: 2.5rem;
    margin-bottom: 12px;
    display: block;
}

.action-label {
    font-size: 0.9rem;
    font-weight: 600;
    line-height: 1.3;
}

/* Specific card styling */
.account-card:hover { border-color: #6b7280; }
.rewards-card:hover { border-color: #f59e0b; }
.gallery-card:hover { border-color: #8b5cf6; }
.locations-card:hover { border-color: #ef4444; }
.environmental-card { border-color: #3b82f6; }

/* CTA Section */
.cta-section {
    background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
    color: white;
    padding: 50px 20px;
    text-align: center;
    margin-top: 40px;
}

.cta-content {
    max-width: 600px;
    margin: 0 auto;
}

.cta-content h2 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 16px;
}

.cta-content p {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 30px;
    line-height: 1.6;
}

.cta-buttons {
    display: flex;
    gap: 16px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.btn-primary {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(34, 197, 94, 0.3);
}

.btn-secondary {
    background: transparent;
    color: white;
    border-color: rgba(255, 255, 255, 0.3);
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.5);
}

/* Animations */
@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
    60% { transform: translateY(-5px); }
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

@keyframes ripple {
    0% { transform: scale(0.8); opacity: 1; }
    100% { transform: scale(2.4); opacity: 0; }
}

.ripple {
    animation: ripple 2s infinite;
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }

    .hero-subtitle {
        font-size: 1rem;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .benefits-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }

    .achievements-grid {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }

    .quick-actions-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }

    .btn {
        width: 100%;
        max-width: 250px;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .hero-section {
        padding: 40px 15px;
    }

    .impact-stats,
    .benefits-section,
    .achievements-section,
    .quick-actions-section {
        padding: 30px 15px;
    }

    .stat-card,
    .benefit-card,
    .achievement-badge,
    .quick-action-card {
        padding: 20px;
    }

    .quick-actions-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .action-icon {
        font-size: 2rem;
        margin-bottom: 8px;
    }

    .action-label {
        font-size: 0.8rem;
    }

    .section-title {
        font-size: 1.6rem;
    }
}
</style>
@endsection
