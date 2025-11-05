{{-- Clean & Fresh Onboarding Tour - Redesigned for Better Mobile Support --}}
<div id="onboardingTour">
    <!-- Welcome Modal -->
    <div class="modal fade" id="welcomeModal" tabindex="-1" aria-labelledby="welcomeModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
                <div class="modal-body p-0">
                    <div class="text-center p-4" style="background: linear-gradient(135deg, #1dd1a1 0%, #10ac84 100%);">
                        <div class="mb-3">
                            <i class="bi bi-cup-hot" style="font-size: 4rem; color: white;"></i>
                        </div>
                        <h2 class="text-white fw-bold mb-2">Welcome to GreenCup! 🌱</h2>
                        <p class="text-white mb-4">
                            @auth('consumer')
                                Hi {{ auth('consumer')->user()->full_name ?? 'there' }}! Ready for a quick tour?
                            @else
                                Discover eco-friendly rewards!
                            @endauth
                        </p>
                        <button class="btn btn-light btn-lg fw-semibold mb-2" style="border-radius: 50px; padding: 12px 36px; width: 100%; max-width: 280px;" onclick="startTour()">
                            <i class="bi bi-play-fill me-2"></i>Start Tour (30 sec)
                        </button>
                        <div class="mt-2">
                            <button class="btn btn-link text-white text-decoration-none fw-semibold" onclick="skipTour()">
                                Skip for now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4R Strategy Educational Modal -->
    <div class="modal fade" id="fourRModal" tabindex="-1" aria-labelledby="fourRModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
                <div class="modal-header border-0" style="background: linear-gradient(135deg, #1dd1a1 0%, #10ac84 100%);">
                    <h4 class="modal-title text-white fw-bold">♻️ Cambodia's 4R Strategy</h4>
                    <button type="button" class="btn-close btn-close-white" onclick="close4RModal()" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3" style="max-height: 70vh; overflow-y: auto;">
                    <!-- Introduction -->
                    <div class="alert alert-success border-0 mb-3" style="background-color: #e8f8f5;">
                        <p class="small mb-0">
                            <strong>GreenCup supports Cambodia's Ministry of Environment 4R Strategy</strong> -
                            a comprehensive approach to sustainable waste management.
                        </p>
                    </div>

                    <!-- The 4Rs - Compact Cards -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);">
                                <div class="card-body text-white p-3">
                                    <div class="text-center mb-2">
                                        <div class="fs-1">🚫</div>
                                        <h6 class="fw-bold mb-0">REFUSE</h6>
                                    </div>
                                    <p class="small mb-0">Say NO to single-use plastics</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #f9ca24 0%, #f0932b 100%);">
                                <div class="card-body text-white p-3">
                                    <div class="text-center mb-2">
                                        <div class="fs-1">📉</div>
                                        <h6 class="fw-bold mb-0">REDUCE</h6>
                                    </div>
                                    <p class="small mb-0">Minimize waste generation</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #1dd1a1 0%, #10ac84 100%);">
                                <div class="card-body text-white p-3">
                                    <div class="text-center mb-2">
                                        <div class="fs-1">♻️</div>
                                        <h6 class="fw-bold mb-0">REUSE</h6>
                                    </div>
                                    <p class="small mb-0">Use items multiple times</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #5f27cd 0%, #341f97 100%);">
                                <div class="card-body text-white p-3">
                                    <div class="text-center mb-2">
                                        <div class="fs-1">🔄</div>
                                        <h6 class="fw-bold mb-0">RECYCLE</h6>
                                    </div>
                                    <p class="small mb-0">Process waste properly</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Impact Icons -->
                    <div class="card border-0 mb-3" style="background: linear-gradient(135deg, #e8f8f5 0%, #d4edda 100%);">
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-2 text-success">
                                <i class="bi bi-graph-up-arrow me-1"></i>Your Impact with Reusable Cups
                            </h6>
                            <div class="row text-center g-2">
                                <div class="col-3">
                                    <div class="p-2 bg-white rounded shadow-sm">
                                        <div class="fs-4">🌳</div>
                                        <small class="fw-bold d-block">Trees</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="p-2 bg-white rounded shadow-sm">
                                        <div class="fs-4">💧</div>
                                        <small class="fw-bold d-block">Water</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="p-2 bg-white rounded shadow-sm">
                                        <div class="fs-4">⚡</div>
                                        <small class="fw-bold d-block">Energy</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="p-2 bg-white rounded shadow-sm">
                                        <div class="fs-4">🌍</div>
                                        <small class="fw-bold d-block">CO₂</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Call to Action -->
                    <div class="text-center">
                        <p class="small mb-2"><strong>Every reusable cup counts!</strong></p>
                        <button class="btn btn-success fw-semibold w-100" style="border-radius: 50px; padding: 12px; background: linear-gradient(135deg, #1dd1a1, #10ac84); border: none;" onclick="close4RModalAndContinueTour()">
                            <i class="bi bi-check-circle me-2"></i>Got it! Let's Continue
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tour Overlay - Lighter (blocks clicks except highlighted elements) -->
    <div id="tourOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.4); z-index: 10050; pointer-events: auto; transition: opacity 0.3s ease;"></div>

    <!-- Tour Spotlight - Cleaner border (allows clicks on highlighted elements) -->
    <div id="tourSpotlight" style="display: none; position: fixed; z-index: 10051; pointer-events: none; border: 3px solid #1dd1a1; border-radius: 12px; box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.5), 0 0 30px rgba(29, 209, 161, 0.8); transition: all 0.4s ease; background: rgba(255, 255, 255, 0.02);"></div>

    <!-- Tour Tooltip - Modern & Clean -->
    <div id="tourTooltip" style="display: none; position: fixed; z-index: 10052; background: white; border-radius: 16px; padding: 20px; max-width: 360px; box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25); transition: all 0.4s ease; pointer-events: auto;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="badge bg-success" style="font-size: 0.75rem; padding: 4px 12px;">
                <span id="currentStep">1</span>/<span id="totalSteps">7</span>
            </div>
            <button class="btn-close btn-sm" onclick="skipTour()" aria-label="Close"></button>
        </div>
        <h5 class="fw-bold mb-2" id="tourTitle">Feature Title</h5>
        <p class="text-muted small mb-3" id="tourDescription">Feature description.</p>

        <!-- Progress Dots -->
        <div class="tour-progress mb-3">
            <div class="d-flex justify-content-center gap-2">
                <div class="progress-dot" data-step="1"></div>
                <div class="progress-dot" data-step="2"></div>
                <div class="progress-dot" data-step="3"></div>
                <div class="progress-dot" data-step="4"></div>
                <div class="progress-dot" data-step="5"></div>
                <div class="progress-dot" data-step="6"></div>
                <div class="progress-dot" data-step="7"></div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-between align-items-center gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="previousStep()" id="prevBtn" style="border-radius: 50px; flex: 1;">
                <i class="bi bi-arrow-left"></i> Back
            </button>
            <button class="btn btn-success btn-sm" onclick="nextStep()" id="nextBtn" style="border-radius: 50px; background: linear-gradient(135deg, #1dd1a1, #10ac84); border: none; flex: 1;">
                Next <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </div>
</div>

<style>
    /* Clean animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes spotlightPulse {
        0%, 100% {
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.5), 0 0 30px rgba(29, 209, 161, 0.8);
            border-color: #1dd1a1;
        }
        50% {
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.5), 0 0 40px rgba(29, 209, 161, 1);
            border-color: #10ac84;
        }
    }

    #tourSpotlight {
        animation: spotlightPulse 2s ease-in-out infinite;
    }

    #tourTooltip {
        animation: fadeIn 0.3s ease-out;
    }

    .progress-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #e0e0e0;
        transition: all 0.3s ease;
    }

    .progress-dot.active {
        background: linear-gradient(135deg, #1dd1a1, #10ac84);
        width: 20px;
        border-radius: 4px;
    }

    /* Mobile responsive */
    @media (max-width: 768px) {
        #tourTooltip {
            max-width: 85vw !important;
            padding: 20px !important;
            margin: 0 !important;
        }

        #tourTooltip h5 {
            font-size: 1.1rem;
        }

        #tourTooltip p {
            font-size: 0.9rem;
        }

        .modal-dialog {
            margin: 1rem;
        }

        #tourSpotlight {
            border-width: 2px !important;
        }
    }

    /* Prevent body scroll during tour ONLY for authenticated users */
    body.tour-active {
        @guest('consumer')
            /* Guests can scroll */
            overflow: auto !important;
        @else
            /* Authenticated users: no scroll */
            overflow: hidden !important;
        @endguest
    }

    /* Highlighted element - Better visibility and clickable */
    .tour-highlighted {
        position: relative;
        z-index: 10052 !important;
        pointer-events: auto !important;
    }

    /* Allow clicks on highlighted element children */
    .tour-highlighted * {
        pointer-events: auto !important;
    }
</style>

<script>
    let tourSteps = [];
    let currentStepIndex = 0;
    let tourActive = false;

    // Define simplified tour steps
    @auth('consumer')
        tourSteps = [
            {
                target: '#navbarNav .nav-link[href*="dashboard"]',
                mobileTarget: '#mobileNav .nav-link[href*="dashboard"]',
                title: '📊 Dashboard',
                description: 'View your points, activity, and impact at a glance.'
            },
            {
                target: '#navbarNav .nav-link[href*="reward"]',
                mobileTarget: '#mobileNav .nav-link[href*="reward"]',
                title: '🎁 Rewards',
                description: 'Redeem points for exciting rewards and offers.'
            },
            {
                target: '#navbarNav .nav-link[href*="gallery"]',
                mobileTarget: '#mobileNav .nav-link[href*="gallery"]',
                title: '🛍️ Products',
                description: 'Browse eco-friendly products from partner stores.'
            },
            {
                target: '#navbarNav .nav-link[href*="map"]',
                mobileTarget: '#mobileNav .nav-link[href*="map"]',
                title: '📍 Stores',
                description: 'Find nearby participating stores on the map.'
            },
            {
                target: '#navbarNav .nav-link[href*="scan"]',
                mobileTarget: '#mobileNav .nav-link[href*="scan"]',
                title: '📱 Scan',
                description: 'Scan receipt QR codes to earn points instantly!'
            },
            {
                target: '#navbarNav .nav-link[href*="environmental"]',
                mobileTarget: '#mobileNav .nav-link[href*="environmental"]',
                title: '🌱 Impact',
                description: 'Track your positive environmental contributions.'
            },
            {
                target: '.user-dropdown .dropdown-item[href$="/account"]',
                mobileTarget: '#mobileNav .nav-link[href$="/account"]',
                title: '👤 Account',
                description: 'Manage your profile and view your transactions.'
            }
        ];
    @else
        tourSteps = [
            {
                target: '#navbarNav .nav-link[href*="dashboard"]',
                mobileTarget: '#guestNav .nav-link[href*="dashboard"]',
                title: '📊 Dashboard',
                description: 'See how GreenCup rewards eco-friendly choices!'
            },
            {
                target: '#navbarNav .nav-link[href*="gallery"]',
                mobileTarget: '#guestNav .nav-link[href*="gallery"]',
                title: '🛍️ Products',
                description: 'Browse products. Sign up to start earning!'
            },
            {
                target: '#navbarNav .nav-link[href*="map"]',
                mobileTarget: '#guestNav .nav-link[href*="map"]',
                title: '📍 Stores',
                description: 'Find participating stores near you.'
            },
            {
                target: '#navbarNav .nav-link[href*="environmental"]',
                mobileTarget: '#guestNav .nav-link[href*="environmental"]',
                title: '🌱 Impact',
                description: 'Learn about environmental benefits of reusable cups.'
            },
            {
                target: 'a[href*="register"]',
                mobileTarget: '.offcanvas a[href*="register"]',
                title: '🎉 Sign Up',
                description: 'Create a free account to earn points and rewards!'
            }
        ];
    @endauth

    function initializeTour() {
        const hasSeenTour = localStorage.getItem('greencup_tour_completed');
        const showOnboardingFlag = {{ session('show_onboarding') ? 'true' : 'false' }};

        @auth('consumer')
            const isNewUser = {{ auth('consumer')->user()->created_at->diffInHours(now()) < 24 ? 'true' : 'false' }};
        @else
            const isNewUser = true;
        @endauth

        if (showOnboardingFlag || (!hasSeenTour && isNewUser)) {
            if (showOnboardingFlag) {
                localStorage.removeItem('greencup_tour_completed');
            }
            setTimeout(showWelcomeModal, 1000);
        }
    }

    function showWelcomeModal() {
        if (typeof bootstrap === 'undefined') {
            setTimeout(showWelcomeModal, 500);
            return;
        }
        const welcomeModalEl = document.getElementById('welcomeModal');
        if (welcomeModalEl) {
            const modal = new bootstrap.Modal(welcomeModalEl);
            modal.show();
        }
    }

    function startTour() {
        const welcomeModalEl = document.getElementById('welcomeModal');
        if (welcomeModalEl) {
            const welcomeModal = bootstrap.Modal.getInstance(welcomeModalEl);
            if (welcomeModal) welcomeModal.hide();
        }

        setTimeout(() => {
            const fourRModalEl = document.getElementById('fourRModal');
            if (fourRModalEl) {
                const fourRModal = new bootstrap.Modal(fourRModalEl);
                fourRModal.show();
            }
        }, 400);
    }

    function show4REducation() {
        // Show just the 4R education modal without starting the tour
        const fourRModalEl = document.getElementById('fourRModal');
        if (fourRModalEl) {
            const fourRModal = new bootstrap.Modal(fourRModalEl);
            fourRModal.show();
        }
    }

    function close4RModal() {
        const fourRModalEl = document.getElementById('fourRModal');
        if (fourRModalEl) {
            const modal = bootstrap.Modal.getInstance(fourRModalEl);
            if (modal) modal.hide();
        }
        skipTour();
    }

    function close4RModalAndContinueTour() {
        const fourRModalEl = document.getElementById('fourRModal');
        if (fourRModalEl) {
            const modal = bootstrap.Modal.getInstance(fourRModalEl);
            if (modal) modal.hide();
        }

        setTimeout(() => {
            tourActive = true;
            currentStepIndex = 0;
            document.body.classList.add('tour-active');

            const tourOverlay = document.getElementById('tourOverlay');
            const totalStepsEl = document.getElementById('totalSteps');

            if (totalStepsEl) totalStepsEl.textContent = tourSteps.length;

            // Check if mobile and guest
            const isMobile = window.innerWidth < 992;
            @auth('consumer')
                const isGuest = false;
            @else
                const isGuest = true;
            @endauth

            // For guests, use a much lighter overlay (or no overlay)
            if (tourOverlay) {
                if (isGuest) {
                    // Very light overlay for guests - almost invisible
                    tourOverlay.style.display = 'block';
                    tourOverlay.style.background = 'rgba(0, 0, 0, 0.15)';
                } else {
                    // Normal overlay for authenticated users
                    tourOverlay.style.display = 'block';
                    tourOverlay.style.background = 'rgba(0, 0, 0, 0.4)';
                }
            }

            if (isMobile && !isGuest) {
                // Authenticated mobile: open sidebar
                const offcanvas = document.getElementById('mobileNav');
                if (offcanvas) {
                    const bsOffcanvas = new bootstrap.Offcanvas(offcanvas);
                    bsOffcanvas.show();

                    // Prevent offcanvas from closing during tour
                    preventOffcanvasClose(offcanvas);

                    setTimeout(() => showStep(0), 600);
                } else {
                    showStep(0);
                }
            } else {
                // Desktop or Guest mobile: show tour normally
                showStep(0);
            }
        }, 400);
    }

    function preventOffcanvasClose(offcanvasElement) {
        // Prevent clicking links from closing offcanvas during tour
        const links = offcanvasElement.querySelectorAll('[data-bs-dismiss="offcanvas"]');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                if (tourActive) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            }, true);
        });

        // Prevent backdrop click from closing
        offcanvasElement.addEventListener('hide.bs.offcanvas', function(e) {
            if (tourActive) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
    }

    function showStep(index) {
        if (index < 0 || index >= tourSteps.length) return;

        currentStepIndex = index;
        const step = tourSteps[index];
        const isMobile = window.innerWidth < 992;

        @auth('consumer')
            const isGuest = false;
        @else
            const isGuest = true;
        @endauth

        // For guest mobile, use centered mode (no spotlight, no target element needed)
        if (isMobile && isGuest) {
            showCenteredStep(index, step);
            return;
        }

        // Normal mode for desktop or authenticated mobile
        const targetSelector = isMobile && step.mobileTarget ? step.mobileTarget : step.target;
        let targetElement = document.querySelector(targetSelector);

        if (!targetElement) {
            console.warn('Target not found:', targetSelector);
            console.log('Attempted selector:', targetSelector);
            console.log('Available dropdowns:', document.querySelectorAll('.dropdown'));
            nextStep();
            return;
        }

        // Special handling: Open dropdown if targeting dropdown item on desktop
        if (!isMobile && targetElement.classList.contains('dropdown-item')) {
            const dropdown = targetElement.closest('.dropdown');
            if (dropdown) {
                const dropdownToggle = dropdown.querySelector('[data-bs-toggle="dropdown"]');
                if (dropdownToggle) {
                    // Get or create Bootstrap dropdown instance
                    let bsDropdown = bootstrap.Dropdown.getInstance(dropdownToggle);
                    if (!bsDropdown) {
                        bsDropdown = new bootstrap.Dropdown(dropdownToggle);
                    }

                    // Open dropdown if not already open
                    if (!dropdown.classList.contains('show')) {
                        bsDropdown.show();

                        // Prevent dropdown from closing during tour
                        dropdown.addEventListener('hide.bs.dropdown', function(e) {
                            if (tourActive) {
                                e.preventDefault();
                                return false;
                            }
                        });
                    }

                    // Wait for dropdown animation and recalculate element position
                    setTimeout(() => {
                        // Re-query the element to get updated position
                        targetElement = document.querySelector(targetSelector);
                        if (targetElement) {
                            continueShowStep(index, step, targetElement, isMobile);
                        } else {
                            console.error('Target element lost after dropdown open');
                            nextStep();
                        }
                    }, 400);
                    return;
                }
            }
        }

        continueShowStep(index, step, targetElement, isMobile);
    }

    function continueShowStep(index, step, targetElement, isMobile) {
        // Update tooltip content
        const currentStep = document.getElementById('currentStep');
        const tourTitle = document.getElementById('tourTitle');
        const tourDescription = document.getElementById('tourDescription');

        if (currentStep) currentStep.textContent = index + 1;
        if (tourTitle) tourTitle.textContent = step.title;
        if (tourDescription) tourDescription.textContent = step.description;

        // Update progress dots
        document.querySelectorAll('.progress-dot').forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });

        // Update buttons
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        if (prevBtn) prevBtn.disabled = index === 0;
        if (nextBtn) {
            nextBtn.innerHTML = index === tourSteps.length - 1
                ? '<i class="bi bi-check-circle"></i> Finish'
                : 'Next <i class="bi bi-arrow-right"></i>';
        }

        // Highlight element
        document.querySelectorAll('.tour-highlighted').forEach(el =>
            el.classList.remove('tour-highlighted')
        );
        targetElement.classList.add('tour-highlighted');

        // Position spotlight
        const rect = targetElement.getBoundingClientRect();
        const spotlight = document.getElementById('tourSpotlight');
        if (spotlight) {
            spotlight.style.display = 'block';
            spotlight.style.top = `${rect.top - 8}px`;
            spotlight.style.left = `${rect.left - 8}px`;
            spotlight.style.width = `${rect.width + 16}px`;
            spotlight.style.height = `${rect.height + 16}px`;
        }

        // Position tooltip
        const tooltip = document.getElementById('tourTooltip');
        if (!tooltip) return;
        tooltip.style.display = 'block';

        if (isMobile) {
            tooltip.style.left = '50%';
            tooltip.style.bottom = '20px';
            tooltip.style.top = 'auto';
            tooltip.style.transform = 'translateX(-50%)';
        } else {
            const tooltipWidth = 360;
            const spaceRight = window.innerWidth - rect.right;
            const spaceLeft = rect.left;

            if (spaceRight > tooltipWidth + 20) {
                tooltip.style.left = `${rect.right + 20}px`;
                tooltip.style.top = `${rect.top}px`;
                tooltip.style.transform = 'none';
            } else if (spaceLeft > tooltipWidth + 20) {
                tooltip.style.left = `${rect.left - tooltipWidth - 20}px`;
                tooltip.style.top = `${rect.top}px`;
                tooltip.style.transform = 'none';
            } else {
                tooltip.style.left = '50%';
                tooltip.style.top = `${rect.bottom + 20}px`;
                tooltip.style.transform = 'translateX(-50%)';
            }
        }

        // Scroll into view
        targetElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function showCenteredStep(index, step) {
        // Guest mobile mode: centered tooltip, no spotlight
        const currentStep = document.getElementById('currentStep');
        const tourTitle = document.getElementById('tourTitle');
        const tourDescription = document.getElementById('tourDescription');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const spotlight = document.getElementById('tourSpotlight');
        const tooltip = document.getElementById('tourTooltip');

        if (!tooltip) {
            console.error('Tour tooltip not found');
            return;
        }

        if (currentStep) currentStep.textContent = index + 1;
        if (tourTitle) tourTitle.textContent = step.title;
        if (tourDescription) tourDescription.textContent = step.description;

        // Update progress dots
        document.querySelectorAll('.progress-dot').forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });

        // Update buttons
        if (prevBtn) prevBtn.disabled = index === 0;
        if (nextBtn) {
            nextBtn.innerHTML = index === tourSteps.length - 1
                ? '<i class="bi bi-check-circle"></i> Finish'
                : 'Next <i class="bi bi-arrow-right"></i>';
        }

        // Hide spotlight completely
        if (spotlight) {
            spotlight.style.display = 'none';
            spotlight.style.visibility = 'hidden';
        }

        // Remove all highlights
        document.querySelectorAll('.tour-highlighted').forEach(el =>
            el.classList.remove('tour-highlighted')
        );

        // Position tooltip in center of screen (simple card-like display)
        tooltip.style.display = 'block';
        tooltip.style.visibility = 'visible';
        tooltip.style.position = 'fixed';
        tooltip.style.left = '50%';
        tooltip.style.top = '50%';
        tooltip.style.transform = 'translate(-50%, -50%)';
        tooltip.style.maxWidth = '90vw';
        tooltip.style.width = 'auto';
        tooltip.style.zIndex = '10055';
    }

    function nextStep() {
        if (currentStepIndex < tourSteps.length - 1) {
            showStep(currentStepIndex + 1);
        } else {
            completeTour();
        }
    }

    function previousStep() {
        if (currentStepIndex > 0) {
            showStep(currentStepIndex - 1);
        }
    }

    function skipTour() {
        // Close welcome modal if open
        const welcomeModalEl = document.getElementById('welcomeModal');
        if (welcomeModalEl) {
            const welcomeModal = bootstrap.Modal.getInstance(welcomeModalEl);
            if (welcomeModal) welcomeModal.hide();
        }

        // Close 4R modal if open
        const fourRModalEl = document.getElementById('fourRModal');
        if (fourRModalEl) {
            const fourRModal = bootstrap.Modal.getInstance(fourRModalEl);
            if (fourRModal) fourRModal.hide();
        }

        completeTour();
    }

    function completeTour() {
        tourActive = false;
        document.body.classList.remove('tour-active');

        const tourOverlay = document.getElementById('tourOverlay');
        const tourSpotlight = document.getElementById('tourSpotlight');
        const tourTooltip = document.getElementById('tourTooltip');

        if (tourOverlay) tourOverlay.style.display = 'none';
        if (tourSpotlight) tourSpotlight.style.display = 'none';
        if (tourTooltip) tourTooltip.style.display = 'none';

        document.querySelectorAll('.tour-highlighted').forEach(el =>
            el.classList.remove('tour-highlighted')
        );

        // Close any open dropdowns
        document.querySelectorAll('.dropdown.show').forEach(dropdown => {
            const dropdownToggle = dropdown.querySelector('[data-bs-toggle="dropdown"]');
            if (dropdownToggle) {
                const bsDropdown = bootstrap.Dropdown.getInstance(dropdownToggle);
                if (bsDropdown) bsDropdown.hide();
            }
        });

        // Close mobile offcanvas if open (authenticated users)
        @auth('consumer')
            const mobileNav = document.getElementById('mobileNav');
            if (mobileNav && mobileNav.classList.contains('show')) {
                const bsOffcanvas = bootstrap.Offcanvas.getInstance(mobileNav);
                if (bsOffcanvas) bsOffcanvas.hide();
            }
        @endauth

        localStorage.setItem('greencup_tour_completed', 'true');
        localStorage.setItem('greencup_tour_completed_at', new Date().toISOString());
    }

    function restartTour() {
        // Clear completed status
        localStorage.removeItem('greencup_tour_completed');
        localStorage.removeItem('greencup_tour_completed_at');

        // Close any open offcanvas
        const openOffcanvas = document.querySelector('.offcanvas.show');
        if (openOffcanvas) {
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(openOffcanvas);
            if (bsOffcanvas) {
                bsOffcanvas.hide();
            }
        }

        // Start tour after a short delay
        setTimeout(() => {
            showWelcomeModal();
        }, 300);
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeTour);
    } else {
        initializeTour();
    }
</script>
