{{-- Simple Guest Tour - Card-based walkthrough --}}
<div id="guestTour">
    <!-- Simple Welcome Card -->
    <div class="modal fade" id="guestWelcomeModal" tabindex="-1" aria-labelledby="guestWelcomeLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
                <div class="modal-body p-0">
                    <div class="text-center p-4" style="background: linear-gradient(135deg, #1dd1a1 0%, #10ac84 100%);">
                        <div class="mb-3">
                            <i class="bi bi-cup-hot" style="font-size: 4rem; color: white;"></i>
                        </div>
                        <h2 class="text-white fw-bold mb-2">Welcome to GreenCup! 🌱</h2>
                        <p class="text-white mb-4">Discover how you can earn rewards by making eco-friendly choices!</p>
                        <button class="btn btn-light btn-lg fw-semibold mb-2" style="border-radius: 50px; padding: 12px 36px; width: 100%; max-width: 280px;" onclick="startGuestTour()">
                            <i class="bi bi-play-fill me-2"></i>Quick Tour (20 sec)
                        </button>
                        <div class="mt-2">
                            <button class="btn btn-link text-white text-decoration-none fw-semibold" onclick="skipGuestTour()">
                                Skip for now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tour Card (floating center) -->
    <div id="guestTourCard" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10055; background: white; border-radius: 20px; padding: 24px; max-width: 90vw; width: 400px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="badge bg-success" style="font-size: 0.8rem; padding: 6px 14px;">
                <span id="guestCurrentStep">1</span>/<span id="guestTotalSteps">4</span>
            </div>
            <button class="btn-close guest-tour-close" onclick="skipGuestTour()" aria-label="Close"></button>
        </div>

        <div class="text-center mb-3">
            <div id="guestTourIcon" style="font-size: 3rem; margin-bottom: 12px;">📊</div>
            <h4 class="fw-bold mb-2" id="guestTourTitle">Dashboard</h4>
            <p class="text-muted" id="guestTourDescription">Explore eco-friendly features</p>
        </div>

        <!-- Progress Dots -->
        <div class="d-flex justify-content-center gap-2 mb-4">
            <div class="guest-dot active" data-step="0"></div>
            <div class="guest-dot" data-step="1"></div>
            <div class="guest-dot" data-step="2"></div>
            <div class="guest-dot" data-step="3"></div>
        </div>

        <!-- Navigation -->
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary flex-1" onclick="previousGuestStep()" id="guestPrevBtn" style="border-radius: 50px; flex: 1;" disabled>
                <i class="bi bi-arrow-left"></i> Back
            </button>
            <button class="btn btn-success flex-1" onclick="nextGuestStep()" id="guestNextBtn" style="border-radius: 50px; flex: 1; background: linear-gradient(135deg, #1dd1a1, #10ac84); border: none;">
                Next <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- Light overlay -->
    <div id="guestTourOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.15); z-index: 10050; pointer-events: auto;"></div>
</div>

<style>
    .guest-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #e0e0e0;
        transition: all 0.3s ease;
    }

    .guest-dot.active {
        background: linear-gradient(135deg, #1dd1a1, #10ac84);
        width: 24px;
        border-radius: 4px;
    }

    .guest-tour-close {
        position: relative;
        z-index: 1;
    }

    .guest-tour-close::before,
    .guest-tour-close::after {
        display: none !important;
    }

    #guestTourCard {
        animation: slideUp 0.4s ease-out;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translate(-50%, -40%); }
        to { opacity: 1; transform: translate(-50%, -50%); }
    }

    /* Ensure no scroll lock for guests */
    body.guest-tour-active {
        overflow: auto !important;
    }
</style>

<script>
    const guestTourSteps = [
        {
            icon: '📊',
            title: 'Dashboard',
            description: 'See how GreenCup rewards eco-friendly choices! Track your potential impact.'
        },
        {
            icon: '🛍️',
            title: 'Products',
            description: 'Browse eco-friendly products from partner stores. Sign up to start earning points!'
        },
        {
            icon: '📍',
            title: 'Stores',
            description: 'Find participating stores near you on the interactive map.'
        },
        {
            icon: '🎉',
            title: 'Sign Up Free',
            description: 'Create your free account and start earning rewards for every reusable cup purchase!'
        }
    ];

    let guestCurrentIndex = 0;

    function initGuestTour() {
        const hasSeenTour = localStorage.getItem('greencup_guest_tour_completed');

        if (!hasSeenTour) {
            setTimeout(() => {
                const modalEl = document.getElementById('guestWelcomeModal');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                }
            }, 1500);
        }
    }

    function startGuestTour() {
        // Close welcome modal
        const welcomeModalEl = document.getElementById('guestWelcomeModal');
        if (welcomeModalEl) {
            const modal = bootstrap.Modal.getInstance(welcomeModalEl);
            if (modal) modal.hide();
        }

        setTimeout(() => {
            document.body.classList.add('guest-tour-active');

            const overlay = document.getElementById('guestTourOverlay');
            const card = document.getElementById('guestTourCard');
            const totalSteps = document.getElementById('guestTotalSteps');

            if (overlay) overlay.style.display = 'block';
            if (card) card.style.display = 'block';
            if (totalSteps) totalSteps.textContent = guestTourSteps.length;

            guestCurrentIndex = 0;
            showGuestStep(0);
        }, 400);
    }

    function showGuestStep(index) {
        if (index < 0 || index >= guestTourSteps.length) return;

        guestCurrentIndex = index;
        const step = guestTourSteps[index];

        const icon = document.getElementById('guestTourIcon');
        const title = document.getElementById('guestTourTitle');
        const description = document.getElementById('guestTourDescription');
        const currentStep = document.getElementById('guestCurrentStep');
        const prevBtn = document.getElementById('guestPrevBtn');
        const nextBtn = document.getElementById('guestNextBtn');

        if (icon) icon.textContent = step.icon;
        if (title) title.textContent = step.title;
        if (description) description.textContent = step.description;
        if (currentStep) currentStep.textContent = index + 1;

        // Update buttons
        if (prevBtn) prevBtn.disabled = index === 0;
        if (nextBtn) {
            nextBtn.innerHTML = index === guestTourSteps.length - 1
                ? '<i class="bi bi-check-circle me-1"></i> Get Started'
                : 'Next <i class="bi bi-arrow-right"></i>';
        }

        // Update dots
        document.querySelectorAll('.guest-dot').forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });
    }

    function nextGuestStep() {
        if (guestCurrentIndex < guestTourSteps.length - 1) {
            showGuestStep(guestCurrentIndex + 1);
        } else {
            completeGuestTour();
        }
    }

    function previousGuestStep() {
        if (guestCurrentIndex > 0) {
            showGuestStep(guestCurrentIndex - 1);
        }
    }

    function skipGuestTour() {
        // Close welcome modal if open
        const welcomeModalEl = document.getElementById('guestWelcomeModal');
        if (welcomeModalEl) {
            const modal = bootstrap.Modal.getInstance(welcomeModalEl);
            if (modal) modal.hide();
        }

        completeGuestTour();
    }

    function completeGuestTour() {
        document.body.classList.remove('guest-tour-active');

        const overlay = document.getElementById('guestTourOverlay');
        const card = document.getElementById('guestTourCard');

        if (overlay) overlay.style.display = 'none';
        if (card) card.style.display = 'none';

        localStorage.setItem('greencup_guest_tour_completed', 'true');
        localStorage.setItem('greencup_guest_tour_completed_at', new Date().toISOString());
    }

    function restartGuestTour() {
        localStorage.removeItem('greencup_guest_tour_completed');
        localStorage.removeItem('greencup_guest_tour_completed_at');

        setTimeout(() => {
            const modalEl = document.getElementById('guestWelcomeModal');
            if (modalEl && typeof bootstrap !== 'undefined') {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        }, 300);
    }

    // Initialize on page load (guest only)
    @guest('consumer')
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initGuestTour);
        } else {
            initGuestTour();
        }
    @endguest
</script>
