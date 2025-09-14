{{-- resources/views/partials/green-background.blade.php --}}
<div class="green-background-wrapper">
    <!-- Subtle gradient background -->
    <div class="bg-gradient-layer"></div>

    <!-- Optional: Subtle pattern overlay -->
    <div class="bg-pattern-layer"></div>
</div>

<style>
.green-background-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100vh;
    z-index: -1;
    overflow: hidden;
}

.bg-gradient-layer {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        135deg,
        #f8fffe 0%,
        #f4fdf9 15%,
        #ebfaf4 30%,
        #e3f8ee 45%,
        #daf5e8 60%,
        #d1f2e2 75%,
        #c8efdc 90%,
        #bfecd6 100%
    );
}

.bg-pattern-layer {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0.03;
    background-image:
        radial-gradient(circle at 25% 25%, #1dd1a1 1px, transparent 1px),
        radial-gradient(circle at 75% 75%, #10ac84 1px, transparent 1px);
    background-size: 60px 60px;
    background-position: 0 0, 30px 30px;
}

/* Ensure content has proper background on top */
body {
    background: transparent !important;
}

/* Optional: Add subtle texture on cards/containers */
.card, .container, .scanner-card, .activity-card, .manual-section {
    background: rgba(255, 255, 255, 0.95) !important;
    backdrop-filter: blur(10px);
}

/* Keep navbar clean */
.navbar {
    background: rgba(255, 255, 255, 0.98) !important;
    backdrop-filter: blur(10px);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .bg-gradient-layer {
        background: linear-gradient(
            180deg,
            #f8fffe 0%,
            #f4fdf9 20%,
            #ebfaf4 40%,
            #e3f8ee 60%,
            #daf5e8 80%,
            #d1f2e2 100%
        );
    }

    .bg-pattern-layer {
        opacity: 0.02;
        background-size: 40px 40px;
        background-position: 0 0, 20px 20px;
    }
}

/* Dark mode compatibility (if needed later) */
@media (prefers-color-scheme: dark) {
    .bg-gradient-layer {
        background: linear-gradient(
            135deg,
            #0a1f1a 0%,
            #0d2620 15%,
            #102d26 30%,
            #13352c 45%,
            #163c32 60%,
            #194338 75%,
            #1c4a3e 90%,
            #1f5144 100%
        );
    }

    .card, .container, .scanner-card, .activity-card, .manual-section {
        background: rgba(26, 26, 26, 0.95) !important;
        color: #ffffff;
    }
}
</style>
