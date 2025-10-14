{{-- Onboarding Tour for New Users & Guests --}}
<div id="onboardingTour">
    <!-- Welcome Modal -->
    <div class="modal fade" id="welcomeModal" tabindex="-1" aria-labelledby="welcomeModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-body p-0">
                    <!-- Welcome Screen -->
                    <div id="welcomeScreen" class="text-center p-5" style="background: linear-gradient(135deg, #1dd1a1 0%, #10ac84 100%);">
                        <div class="mb-4 animate-bounce-in">
                            <i class="bi bi-cup-hot" style="font-size: 5rem; color: white;"></i>
                        </div>
                        <h1 class="text-white fw-bold mb-3 animate-fade-in-up" style="animation-delay: 0.2s;">Welcome to GreenCup! 🌱</h1>
                        <p class="text-white fs-5 mb-4 animate-fade-in-up" style="animation-delay: 0.4s;">
                            @auth('consumer')
                                Hi {{ auth('consumer')->user()->full_name ?? 'there' }}! Let's take a quick tour to get you started.
                            @else
                                Discover eco-friendly rewards and track your environmental impact!
                            @endauth
                        </p>
                        <button class="btn btn-light btn-lg fw-semibold animate-fade-in-up" style="animation-delay: 0.6s; border-radius: 50px; padding: 12px 40px;" onclick="startTour()">
                            <i class="bi bi-play-fill me-2"></i>Start Tour (30 seconds)
                        </button>
                        <div class="mt-3">
                            <button class="btn btn-link text-white text-decoration-none" onclick="skipTour()">
                                Skip for now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tour Overlay -->
    <div id="tourOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.7); z-index: 10050; pointer-events: none; transition: all 0.3s ease;"></div>

    <!-- Tour Spotlight -->
    <div id="tourSpotlight" style="display: none; position: fixed; z-index: 10051; pointer-events: none; border: 4px solid #1dd1a1; border-radius: 12px; box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.85), 0 0 40px rgba(29, 209, 161, 1), inset 0 0 20px rgba(29, 209, 161, 0.3); transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); background: rgba(29, 209, 161, 0.08);"></div>

    <!-- Tour Tooltip -->
    <div id="tourTooltip" style="display: none; position: fixed; z-index: 10052; background: white; border-radius: 16px; padding: 24px; max-width: 400px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); pointer-events: auto;">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="tour-step-badge" style="background: linear-gradient(135deg, #1dd1a1, #10ac84); color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">
                Step <span id="currentStep">1</span> of <span id="totalSteps">5</span>
            </div>
            <button class="btn-close" onclick="skipTour()" aria-label="Close"></button>
        </div>
        <h4 class="fw-bold mb-2" id="tourTitle">Feature Title</h4>
        <p class="text-muted mb-3" id="tourDescription">Feature description goes here.</p>
        <div class="d-flex justify-content-between align-items-center">
            <button class="btn btn-outline-secondary" onclick="previousStep()" id="prevBtn" style="border-radius: 50px;">
                <i class="bi bi-arrow-left me-1"></i>Back
            </button>
            <div class="tour-progress">
                <div class="d-flex gap-2">
                    <div class="progress-dot" data-step="1"></div>
                    <div class="progress-dot" data-step="2"></div>
                    <div class="progress-dot" data-step="3"></div>
                    <div class="progress-dot" data-step="4"></div>
                    <div class="progress-dot" data-step="5"></div>
                </div>
            </div>
            <button class="btn btn-success" onclick="nextStep()" id="nextBtn" style="border-radius: 50px; background: linear-gradient(135deg, #1dd1a1, #10ac84); border: none;">
                Next<i class="bi bi-arrow-right ms-1"></i>
            </button>
        </div>
    </div>
</div>

<style>
    @keyframes bounce-in {
        0% { transform: scale(0); opacity: 0; }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); opacity: 1; }
    }

    @keyframes fade-in-up {
        0% { transform: translateY(20px); opacity: 0; }
        100% { transform: translateY(0); opacity: 1; }
    }

    @keyframes pulse-glow {
        0%, 100% { 
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.85), 
                        0 0 40px rgba(29, 209, 161, 1), 
                        0 0 60px rgba(29, 209, 161, 0.6),
                        inset 0 0 20px rgba(29, 209, 161, 0.3);
            border-color: #1dd1a1;
        }
        50% { 
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.85), 
                        0 0 60px rgba(29, 209, 161, 1), 
                        0 0 80px rgba(29, 209, 161, 0.8),
                        inset 0 0 30px rgba(29, 209, 161, 0.5);
            border-color: #10ac84;
        }
    }

    .animate-bounce-in {
        animation: bounce-in 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .animate-fade-in-up {
        animation: fade-in-up 0.6s ease-out;
        opacity: 0;
        animation-fill-mode: forwards;
    }

    #tourSpotlight {
        animation: pulse-glow 2s ease-in-out infinite;
    }

    .progress-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #e0e0e0;
        transition: all 0.3s ease;
    }

    .progress-dot.active {
        background: linear-gradient(135deg, #1dd1a1, #10ac84);
        width: 24px;
        border-radius: 5px;
    }

    #tourTooltip {
        transform-origin: center;
    }

    .tour-enter {
        animation: tooltip-enter 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @keyframes tooltip-enter {
        0% { opacity: 0; transform: scale(0.9) translateY(10px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }

    /* Mobile Responsive Styles */
    @media (max-width: 991px) {
        #tourTooltip {
            max-width: 90vw !important;
            padding: 20px !important;
            border-radius: 12px !important;
            left: 50% !important;
            transform: translateX(-50%);
        }

        #tourTooltip h4 {
            font-size: 1.1rem;
        }

        #tourTooltip p {
            font-size: 0.9rem;
        }

        .tour-step-badge {
            font-size: 0.7rem !important;
            padding: 3px 10px !important;
        }

        #tourTooltip .btn {
            font-size: 0.85rem;
            padding: 8px 16px;
        }

        .progress-dot {
            width: 8px;
            height: 8px;
        }

        .progress-dot.active {
            width: 20px;
        }

        /* Ensure tour elements are above offcanvas */
        #tourOverlay {
            z-index: 10060 !important;
        }

        #tourSpotlight {
            z-index: 10061 !important;
        }

        #tourTooltip {
            z-index: 10062 !important;
        }

        /* Welcome modal adjustments for mobile */
        .modal-dialog {
            margin: 1rem !important;
        }

        #welcomeScreen {
            padding: 2rem 1.5rem !important;
        }

        #welcomeScreen h1 {
            font-size: 1.75rem !important;
        }

        #welcomeScreen p {
            font-size: 1rem !important;
        }

        #welcomeScreen .btn-lg {
            padding: 10px 30px !important;
            font-size: 1rem !important;
        }
    }

    @media (max-width: 576px) {
        #tourTooltip {
            padding: 16px !important;
            max-width: 95vw !important;
            position: fixed !important;
            bottom: 20px !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            top: auto !important;
        }

        #tourTooltip h4 {
            font-size: 1rem;
            margin-bottom: 8px !important;
        }

        #tourTooltip p {
            font-size: 0.85rem;
        }

        #tourTooltip .btn {
            font-size: 0.8rem;
            padding: 6px 12px;
        }

        #tourSpotlight {
            border-width: 2px !important;
        }

        .tour-step-badge {
            font-size: 0.65rem !important;
        }

        /* Fixed bottom position for mobile tooltip */
        #tourTooltip .d-flex {
            flex-wrap: wrap;
            gap: 8px;
        }

        #tourTooltip .btn-outline-secondary,
        #tourTooltip .btn-success {
            flex: 1 1 45%;
            min-width: 80px;
        }

        .tour-progress {
            order: -1;
            width: 100%;
            text-align: center;
            margin-bottom: 12px;
        }
    }

    /* Ensure offcanvas is visible during tour */
    .offcanvas.show {
        visibility: visible !important;
    }

    /* Prevent body scroll during tour */
    body.tour-active {
        overflow: hidden !important;
    }
</style>

<script>
    // Define tour steps based on user authentication
    let tourSteps = [];

    @auth('consumer')
        tourSteps = [
            {
                target: '.nav-link[href*="dashboard"]',
                mobileTarget: '#mobileNav .nav-link[href*="dashboard"]',
                title: '📊 Dashboard',
                description: 'Your central hub to view points, recent activity, and environmental impact at a glance.',
                position: 'bottom'
            },
            {
                target: '.nav-link[href*="reward"]',
                mobileTarget: '#mobileNav .nav-link[href*="reward"]',
                title: '🎁 Rewards',
                description: 'Redeem your points for exciting rewards and exclusive offers from our partner stores.',
                position: 'bottom'
            },
            {
                target: '.nav-link[href*="gallery"], .nav-link[href*="products"]',
                mobileTarget: '#mobileNav .nav-link[href*="gallery"]',
                title: '🛍️ Products Gallery',
                description: 'Browse eco-friendly products and rewards from all participating stores.',
                position: 'bottom'
            },
            {
                target: '.nav-link[href*="map"]',
                mobileTarget: '#mobileNav .nav-link[href*="map"]',
                title: '📍 Store Locator',
                description: 'Find nearby participating stores on the map and discover their available eco-products.',
                position: 'bottom'
            },
            {
                target: '.nav-link[href*="scan"]',
                mobileTarget: '#mobileNav .nav-link[href*="scan"]',
                title: '📱 Scan QR Code',
                description: 'Scan your receipt QR codes here to earn points instantly! Every eco-friendly purchase counts.',
                position: 'bottom'
            },
            {
                target: '.nav-link[href*="environmental-impact"]',
                mobileTarget: '#mobileNav .nav-link[href*="environmental-impact"]',
                title: '🌱 Environmental Impact',
                description: 'Track your positive impact! See how many cups saved, CO₂ reduced, and achievements earned.',
                position: 'bottom'
            }
        ];
    @else
        tourSteps = [
            {
                target: '.nav-link[href*="dashboard"]',
                mobileTarget: '#guestNav .nav-link[href*="dashboard"]',
                title: '📊 Dashboard',
                description: 'View your dashboard and see how GreenCup rewards eco-friendly choices!',
                position: 'bottom'
            },
            {
                target: '.nav-link[href*="gallery"], .nav-link[href*="products"]',
                mobileTarget: '#guestNav .nav-link[href*="gallery"]',
                title: '🛍️ Products Gallery',
                description: 'Browse eco-friendly products from all participating stores. Sign up to start earning rewards!',
                position: 'bottom'
            },
            {
                target: '.nav-link[href*="map"]',
                mobileTarget: '#guestNav .nav-link[href*="map"]',
                title: '📍 Store Locator',
                description: 'Find nearby participating stores on the map and discover their available eco-products.',
                position: 'bottom'
            },
            {
                target: '.nav-link[href*="environmental-impact"]',
                mobileTarget: '#guestNav .nav-link[href*="environmental-impact"]',
                title: '🌱 Environmental Impact',
                description: 'Learn about the environmental benefits of using reusable cups. Create an account to track your own impact!',
                position: 'bottom'
            },
            {
                target: '.btn-success[href*="register"], a[href*="register"]',
                mobileTarget: '#guestNav a[href*="register"]',
                title: '🎉 Create Free Account',
                description: 'Sign up to start earning points, track your environmental impact, and unlock exclusive rewards!',
                position: 'left'
            },
            {
                target: '.btn-outline-success[href*="login"], a[href*="login"]:not(.btn-success)',
                mobileTarget: '#guestNav a[href*="login"]',
                title: '🔐 Login',
                description: 'Already have an account? Login here to access all your rewards and track your progress.',
                position: 'left'
            }
        ];
    @endauth

    console.log('Tour steps loaded:', tourSteps.length);

    let currentStepIndex = 0;
    let tourActive = false;

    function initializeTour() {
        console.log('Initializing tour...');

        // Check if user has seen the tour
        const hasSeenTour = localStorage.getItem('greencup_tour_completed');
        console.log('Has seen tour:', hasSeenTour);

        @auth('consumer')
            const isNewUser = {{ auth('consumer')->user()->created_at > now()->subMinutes(5) ? 'true' : 'false' }};
        @else
            const isNewUser = true; // Show for all guests
        @endauth

        console.log('Is new user:', isNewUser);

        if (!hasSeenTour && isNewUser) {
            console.log('Showing welcome modal in 1 second...');
            setTimeout(() => {
                showWelcomeModal();
            }, 1000);
        } else {
            console.log('Tour already completed or user is not new');
        }
    }

    function showWelcomeModal() {
        console.log('Showing welcome modal...');
        if (typeof bootstrap === 'undefined') {
            console.error('Bootstrap is not loaded yet! Retrying in 500ms...');
            setTimeout(showWelcomeModal, 500);
            return;
        }

        const modalElement = document.getElementById('welcomeModal');
        if (!modalElement) {
            console.error('Welcome modal element not found!');
            return;
        }

        console.log('Creating Bootstrap modal...');
        const welcomeModal = new bootstrap.Modal(modalElement);
        welcomeModal.show();
        console.log('Welcome modal shown');
    }

    function startTour() {
        console.log('Starting tour...');
        const modalElement = document.getElementById('welcomeModal');
        const welcomeModal = bootstrap.Modal.getInstance(modalElement);

        if (welcomeModal) {
            console.log('Hiding welcome modal...');
            welcomeModal.hide();
        }

        setTimeout(() => {
            console.log('Initializing tour display...');
            tourActive = true;
            currentStepIndex = 0;

            // Add body class for tour
            document.body.classList.add('tour-active');

            const overlay = document.getElementById('tourOverlay');
            overlay.style.display = 'block';
            console.log('Overlay displayed');

            document.getElementById('totalSteps').textContent = tourSteps.length;

            // On mobile, open the offcanvas sidebar to show navigation
            const isMobile = window.innerWidth < 992;
            if (isMobile) {
                console.log('Mobile detected, opening offcanvas...');

                // Determine which offcanvas to open (guest or authenticated user)
                @auth('consumer')
                    const offcanvasElement = document.getElementById('mobileNav');
                @else
                    const offcanvasElement = document.getElementById('guestNav');
                @endauth

                if (offcanvasElement) {
                    console.log('Opening offcanvas:', offcanvasElement.id);

                    // Make offcanvas visible for tour
                    offcanvasElement.classList.add('show');
                    offcanvasElement.style.visibility = 'visible';
                    offcanvasElement.style.transform = 'none';

                    // Create backdrop
                    const backdrop = document.createElement('div');
                    backdrop.className = 'offcanvas-backdrop fade show';
                    backdrop.id = 'tour-offcanvas-backdrop';
                    backdrop.style.zIndex = '10049';
                    document.body.appendChild(backdrop);

                    // Set offcanvas z-index to be above backdrop but below tour elements
                    offcanvasElement.style.zIndex = '10050';

                    // Add body class to prevent scrolling
                    document.body.classList.add('offcanvas-open');
                    document.body.style.overflow = 'hidden';

                    // Wait for offcanvas animation
                    setTimeout(() => {
                        console.log('Showing first step...');
                        showStep(currentStepIndex);
                    }, 350);
                    return;
                } else {
                    console.error('Offcanvas element not found');
                }
            }

            console.log('Showing first step...');
            showStep(currentStepIndex);
        }, 500);
    }

    function showStep(index) {
        if (index < 0 || index >= tourSteps.length) {
            console.log('Invalid step index:', index);
            return;
        }

        const step = tourSteps[index];
        console.log('Showing step', index + 1, ':', step.title);

        const isMobile = window.innerWidth < 992;
        const targetSelector = isMobile && step.mobileTarget ? step.mobileTarget : step.target;
        console.log('Looking for target:', targetSelector, '(mobile:', isMobile, ')');

        let targetElement = document.querySelector(targetSelector);

        // Fallback: try the other selector if not found
        if (!targetElement) {
            console.log('Primary target not found, trying fallback...');
            const fallbackSelector = isMobile ? step.target : (step.mobileTarget || step.target);
            targetElement = document.querySelector(fallbackSelector);
            console.log('Fallback result:', targetElement ? 'Found' : 'Not found');
        }

        if (!targetElement) {
            console.error('Target element not found for selector:', targetSelector);
            console.log('Available navigation links:', document.querySelectorAll('.nav-link'));

            // Try to skip this step and move to next
            if (index < tourSteps.length - 1) {
                console.log('Skipping to next step...');
                currentStepIndex++;
                setTimeout(() => showStep(currentStepIndex), 100);
            } else {
                console.log('No more steps, finishing tour');
                finishTour();
            }
            return;
        }

        console.log('Target element found:', targetElement);

        // Update step counter
        document.getElementById('currentStep').textContent = index + 1;

        // Update progress dots
        document.querySelectorAll('.progress-dot').forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });

        // Update tooltip content
        document.getElementById('tourTitle').innerHTML = step.title;
        document.getElementById('tourDescription').textContent = step.description;

        // Position spotlight
        const rect = targetElement.getBoundingClientRect();
        const spotlight = document.getElementById('tourSpotlight');
        spotlight.style.display = 'block';
        spotlight.style.top = `${rect.top - 8}px`;
        spotlight.style.left = `${rect.left - 8}px`;
        spotlight.style.width = `${rect.width + 16}px`;
        spotlight.style.height = `${rect.height + 16}px`;

        // Position tooltip
        const tooltip = document.getElementById('tourTooltip');
        tooltip.style.display = 'block';
        tooltip.style.opacity = '0';

        // Force reflow to get correct dimensions
        tooltip.offsetHeight;

        let tooltipTop, tooltipLeft;
        const tooltipRect = tooltip.getBoundingClientRect();
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;

        console.log('Tooltip dimensions:', tooltipRect.width, 'x', tooltipRect.height);
        console.log('Viewport:', viewportWidth, 'x', viewportHeight);

        // Mobile specific positioning (fixed bottom)
        if (isMobile && viewportWidth <= 576) {
            console.log('Mobile mode: positioning tooltip at bottom');
            tooltip.style.position = 'fixed';
            tooltip.style.bottom = '20px';
            tooltip.style.left = '50%';
            tooltip.style.top = 'auto';
            tooltip.style.transform = 'translateX(-50%)';
        } else {
            // Desktop/tablet positioning
            tooltip.style.position = 'fixed';
            tooltip.style.transform = 'none';

            switch(step.position) {
                case 'bottom':
                    tooltipTop = rect.bottom + 20;
                    tooltipLeft = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
                    break;
                case 'top':
                    tooltipTop = rect.top - tooltipRect.height - 20;
                    tooltipLeft = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
                    break;
                case 'left':
                    tooltipTop = rect.top + (rect.height / 2) - (tooltipRect.height / 2);
                    tooltipLeft = rect.left - tooltipRect.width - 20;
                    break;
                case 'right':
                    tooltipTop = rect.top + (rect.height / 2) - (tooltipRect.height / 2);
                    tooltipLeft = rect.right + 20;
                    break;
                default:
                    tooltipTop = rect.bottom + 20;
                    tooltipLeft = rect.left;
            }

            // Keep tooltip within viewport
            const maxLeft = viewportWidth - tooltipRect.width - 20;
            const maxTop = viewportHeight - tooltipRect.height - 20;
            tooltipLeft = Math.max(20, Math.min(tooltipLeft, maxLeft));
            tooltipTop = Math.max(20, Math.min(tooltipTop, maxTop));

            tooltip.style.top = `${tooltipTop}px`;
            tooltip.style.left = `${tooltipLeft}px`;
        }

        console.log('Tooltip positioned');

        // Make tooltip visible with animation
        setTimeout(() => {
            tooltip.style.opacity = '1';
            tooltip.style.transition = 'opacity 0.3s ease';
            tooltip.classList.add('tour-enter');
        }, 50);

        // Update buttons
        document.getElementById('prevBtn').disabled = index === 0;
        document.getElementById('nextBtn').innerHTML = index === tourSteps.length - 1
            ? 'Finish<i class="bi bi-check-lg ms-1"></i>'
            : 'Next<i class="bi bi-arrow-right ms-1"></i>';

        // Scroll element into view
        targetElement.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Remove enter animation class after animation completes
        setTimeout(() => tooltip.classList.remove('tour-enter'), 500);
    }

    function nextStep() {
        if (currentStepIndex < tourSteps.length - 1) {
            currentStepIndex++;
            showStep(currentStepIndex);
        } else {
            finishTour();
        }
    }

    function previousStep() {
        if (currentStepIndex > 0) {
            currentStepIndex--;
            showStep(currentStepIndex);
        }
    }

    function skipTour() {
        finishTour();
    }

    function finishTour() {
        tourActive = false;

        // Remove tour body class
        document.body.classList.remove('tour-active');

        document.getElementById('tourOverlay').style.display = 'none';
        document.getElementById('tourSpotlight').style.display = 'none';
        document.getElementById('tourTooltip').style.display = 'none';

        const welcomeModal = bootstrap.Modal.getInstance(document.getElementById('welcomeModal'));
        if (welcomeModal) welcomeModal.hide();

        // Close mobile offcanvas if it's open (check both authenticated and guest)
        @auth('consumer')
            const offcanvasElement = document.getElementById('mobileNav');
        @else
            const offcanvasElement = document.getElementById('guestNav');
        @endauth

        if (offcanvasElement && offcanvasElement.classList.contains('show')) {
            console.log('Closing offcanvas:', offcanvasElement.id);
            offcanvasElement.classList.remove('show');
            offcanvasElement.style.visibility = '';
            offcanvasElement.style.zIndex = '';
            offcanvasElement.style.transform = '';
        }

        // Remove backdrop if exists
        const backdrop = document.getElementById('tour-offcanvas-backdrop');
        if (backdrop) {
            backdrop.remove();
        }

        // Re-enable body scrolling
        document.body.classList.remove('offcanvas-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';

        // Mark tour as completed
        localStorage.setItem('greencup_tour_completed', 'true');
        localStorage.setItem('greencup_tour_completed_at', new Date().toISOString());

        // Show completion message
        showCompletionToast();
    }

    function showCompletionToast() {
        const toast = document.createElement('div');
        toast.className = 'toast show position-fixed bottom-0 end-0 m-3';
        toast.style.zIndex = '10001';
        toast.innerHTML = `
            <div class="toast-body bg-success text-white rounded-3 shadow-lg p-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                    <div>
                        <strong>Tour Complete! 🎉</strong>
                        <p class="mb-0 small">You're all set to start your eco-journey!</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-3" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }

    // Initialize tour on page load
    document.addEventListener('DOMContentLoaded', initializeTour);

    // Add keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (!tourActive) return;

        if (e.key === 'ArrowRight' || e.key === 'Enter') {
            nextStep();
        } else if (e.key === 'ArrowLeft') {
            previousStep();
        } else if (e.key === 'Escape') {
            skipTour();
        }
    });

    // Add touch gesture support for mobile
    let touchStartX = 0;
    let touchEndX = 0;

    document.addEventListener('touchstart', (e) => {
        if (!tourActive) return;
        touchStartX = e.changedTouches[0].screenX;
    }, false);

    document.addEventListener('touchend', (e) => {
        if (!tourActive) return;
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, false);

    function handleSwipe() {
        const swipeThreshold = 50; // minimum distance for swipe
        const diff = touchStartX - touchEndX;

        if (Math.abs(diff) < swipeThreshold) return;

        if (diff > 0) {
            // Swipe left - next step
            console.log('Swipe left detected - next step');
            nextStep();
        } else {
            // Swipe right - previous step
            console.log('Swipe right detected - previous step');
            previousStep();
        }
    }

    // Restart tour function (can be called manually)
    window.restartTour = function() {
        console.log('Restarting tour...');
        localStorage.removeItem('greencup_tour_completed');
        localStorage.removeItem('greencup_tour_completed_at');
        showWelcomeModal();
    };

    // Quick start tour without modal (for testing)
    window.quickStartTour = function() {
        console.log('Quick starting tour...');
        tourActive = true;
        currentStepIndex = 0;
        document.getElementById('tourOverlay').style.display = 'block';
        document.getElementById('totalSteps').textContent = tourSteps.length;
        showStep(currentStepIndex);
    };
</script>
