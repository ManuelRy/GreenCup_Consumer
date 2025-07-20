@extends('master')

@section('content')
    <div class="scanner-fullscreen">
        <!-- Video Background -->
        <video id="qr-video" autoplay muted playsinline></video>
        
        <!-- Header -->
        <div class="scanner-header">
            <h1 class="app-title">GreenCup Scan</h1>
            <a href="{{ route('dashboard') }}" class="close-btn">×</a>
        </div>

        <!-- Scan Frame -->
        <div class="scan-overlay">
            <div class="scan-frame">
                <div class="frame-corner corner-tl"></div>
                <div class="frame-corner corner-tr"></div>
                <div class="frame-corner corner-bl"></div>
                <div class="frame-corner corner-br"></div>
            </div>
        </div>

        <!-- Bottom Controls -->
        <div class="bottom-controls">
            <button id="flash-btn" class="control-button">
                <div class="control-icon">🔦</div>
                <div class="control-label">Flash</div>
            </button>
            
            <button id="gallery-btn" class="control-button">
                <div class="control-icon">🖼️</div>
                <div class="control-label">Gallery</div>
            </button>
        </div>

        <!-- Status Message -->
        <div class="scan-status" id="scan-status">
            <p>Position QR code within the frame</p>
        </div>

        <!-- Success Modal -->
        <div id="success-modal" class="success-modal" style="display: none;">
            <div class="success-content">
                <div class="success-animation">
                    <div class="success-icon">🎉</div>
                    <div class="success-title">Scan Complete!</div>
                </div>
                
                <div class="points-display">
                    <div class="points-animation">
                        <div class="current-points">
                            <span class="points-label">Current Points</span>
                            <span class="points-value" id="current-points">0</span>
                        </div>
                        <div class="plus-sign">+</div>
                        <div class="earned-points">
                            <span class="points-label">Earned</span>
                            <span class="points-value new-points" id="earned-points">0</span>
                        </div>
                        <div class="equals-sign">=</div>
                        <div class="total-points">
                            <span class="points-label">New Total</span>
                            <span class="points-value total" id="total-points">0</span>
                        </div>
                    </div>
                </div>

                <div class="seller-info" id="seller-info">
                    <!-- Seller details will be populated here -->
                </div>

                <div class="success-actions">
                    <button onclick="goToDashboard()" class="btn-dashboard">
                        <span class="btn-icon">🏠</span>
                        Go to Dashboard
                    </button>
                    <button onclick="scanAnother()" class="btn-scan-more">
                        <span class="btn-icon">📱</span>
                        Scan Another
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .scanner-fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: #000;
            z-index: 9999;
            overflow: hidden;
        }

        #qr-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
        }

        .scanner-header {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            z-index: 10;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 50px 20px 20px;
            background: linear-gradient(180deg, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.4) 70%, transparent 100%);
        }

        .app-title {
            color: white;
            font-size: 24px;
            font-weight: 600;
            margin: 0;
            text-shadow: 0 2px 8px rgba(0,0,0,0.5);
        }

        .close-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            text-decoration: none;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .close-btn:hover {
            background: rgba(255, 255, 255, 0.5);
            color: white;
            text-decoration: none;
        }

        .scan-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 5;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .scan-frame {
            position: relative;
            width: 280px;
            height: 280px;
        }

        .frame-corner {
            position: absolute;
            width: 40px;
            height: 40px;
            border: 4px solid white;
            border-radius: 8px;
        }

        .corner-tl {
            top: 0;
            left: 0;
            border-right: none;
            border-bottom: none;
            border-top-left-radius: 12px;
        }

        .corner-tr {
            top: 0;
            right: 0;
            border-left: none;
            border-bottom: none;
            border-top-right-radius: 12px;
        }

        .corner-bl {
            bottom: 0;
            left: 0;
            border-right: none;
            border-top: none;
            border-bottom-left-radius: 12px;
        }

        .corner-br {
            bottom: 0;
            right: 0;
            border-left: none;
            border-top: none;
            border-bottom-right-radius: 12px;
        }

        .bottom-controls {
            position: absolute;
            bottom: 120px;
            left: 0;
            right: 0;
            z-index: 10;
            display: flex;
            justify-content: center;
            gap: 60px;
        }

        .control-button {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            background: rgba(0, 0, 0, 0.6);
            border: none;
            border-radius: 16px;
            padding: 16px 20px;
            color: white;
            cursor: pointer;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            min-width: 80px;
        }

        .control-button:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .control-button:active {
            transform: scale(0.95);
        }

        .control-icon {
            font-size: 24px;
            margin-bottom: 4px;
        }

        .control-label {
            font-size: 14px;
            font-weight: 500;
        }

        .scan-status {
            position: absolute;
            bottom: 40px;
            left: 20px;
            right: 20px;
            z-index: 10;
            text-align: center;
        }

        .scan-status p {
            color: white;
            font-size: 16px;
            font-weight: 500;
            margin: 0;
            text-shadow: 0 2px 8px rgba(0,0,0,0.5);
        }

        /* Success Modal Styles */
        .success-modal {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 20;
            background: linear-gradient(135deg, #2E8B57, #3CB371);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: slideUp 0.5s ease;
        }

        .success-content {
            width: 100%;
            max-width: 400px;
            text-align: center;
            color: white;
        }

        .success-animation {
            margin-bottom: 30px;
        }

        .success-icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: bounce 0.8s ease;
        }

        .success-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .points-display {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 25px;
            margin: 30px 0;
            color: #333;
        }

        .points-animation {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .current-points, .earned-points, .total-points {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 80px;
        }

        .points-label {
            font-size: 12px;
            font-weight: 600;
            color: #666;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .points-value {
            font-size: 24px;
            font-weight: 700;
            color: #2E8B57;
        }

        .new-points {
            color: #FF6B35;
            animation: glow 1.5s ease-in-out infinite alternate;
        }

        .total {
            color: #2E8B57;
            animation: pulse 2s ease-in-out infinite;
        }

        .plus-sign, .equals-sign {
            font-size: 24px;
            font-weight: 700;
            color: #2E8B57;
            margin: 0 10px;
        }

        .seller-info {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            padding: 20px;
            margin: 20px 0;
            color: #333;
            text-align: left;
        }

        .seller-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .seller-logo {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: linear-gradient(135deg, #2E8B57, #3CB371);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            font-weight: bold;
        }

        .seller-details h4 {
            margin: 0 0 5px 0;
            font-size: 18px;
            color: #2E8B57;
        }

        .seller-location {
            font-size: 14px;
            color: #666;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .seller-rank {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: linear-gradient(135deg, #FFD700, #FFA500);
            color: #333;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 8px;
        }

        .item-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 12px;
            margin-top: 15px;
            border-left: 4px solid #2E8B57;
        }

        .success-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn-dashboard, .btn-scan-more {
            flex: 1;
            padding: 16px 20px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-dashboard {
            background: white;
            color: #2E8B57;
        }

        .btn-dashboard:hover {
            background: #f0f0f0;
            transform: translateY(-2px);
        }

        .btn-scan-more {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid white;
        }

        .btn-scan-more:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .btn-icon {
            font-size: 18px;
        }

        /* Animations */
        @keyframes slideUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes bounce {
            0%, 20%, 60%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-30px);
            }
            80% {
                transform: translateY(-15px);
            }
        }

        @keyframes glow {
            from {
                text-shadow: 0 0 5px #FF6B35;
            }
            to {
                text-shadow: 0 0 20px #FF6B35, 0 0 30px #FF6B35;
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }

        /* Mobile responsive */
        @media (max-width: 480px) {
            .scanner-header {
                padding: 40px 15px 15px;
            }

            .app-title {
                font-size: 20px;
            }

            .scan-frame {
                width: 240px;
                height: 240px;
            }

            .frame-corner {
                width: 35px;
                height: 35px;
            }

            .bottom-controls {
                gap: 40px;
                bottom: 100px;
            }

            .control-button {
                padding: 12px 16px;
                min-width: 70px;
            }

            .control-icon {
                font-size: 20px;
            }

            .control-label {
                font-size: 12px;
            }

            .points-animation {
                flex-direction: column;
                gap: 10px;
            }

            .plus-sign, .equals-sign {
                transform: rotate(90deg);
            }

            .success-actions {
                flex-direction: column;
            }
        }

        /* Hide scroll bars */
        .scanner-fullscreen {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .scanner-fullscreen::-webkit-scrollbar {
            display: none;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/qr-scanner@1.4.2/qr-scanner.umd.min.js"></script>
    <script>
        let scanner = null;
        let isScanning = false;

        const video = document.getElementById('qr-video');
        const flashBtn = document.getElementById('flash-btn');
        const galleryBtn = document.getElementById('gallery-btn');
        const statusEl = document.getElementById('scan-status');
        const successModal = document.getElementById('success-modal');

        // Initialize scanner on page load
        window.addEventListener('load', initializeScanner);
        flashBtn.addEventListener('click', toggleFlash);
        galleryBtn.addEventListener('click', selectFromGallery);

        async function initializeScanner() {
            try {
                updateStatus('Starting camera...');
                
                scanner = new QrScanner(video, result => handleScanResult(result), {
                    returnDetailedScanResult: true,
                    highlightScanRegion: false,
                    highlightCodeOutline: false,
                    preferredCamera: 'environment',
                    maxScansPerSecond: 5
                });

                await scanner.start();
                isScanning = true;
                updateStatus('Position QR code within the frame');
                
            } catch (error) {
                console.error('Scanner initialization failed:', error);
                updateStatus('Camera access denied. Please allow camera access.');
            }
        }

        function updateStatus(message) {
            statusEl.innerHTML = `<p>${message}</p>`;
        }

        function handleScanResult(result) {
            console.log('QR Code detected:', result.data);
            
            // Haptic feedback
            if (navigator.vibrate) {
                navigator.vibrate([100, 50, 100]);
            }
            
            // Stop scanning temporarily
            if (scanner) {
                scanner.stop();
                isScanning = false;
            }
            
            // Process QR code with database
            processQRCode(result.data);
        }

        async function processQRCode(data) {
            try {
                updateStatus('Processing QR code...');
                
                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // Call your Laravel API to process and confirm transaction
                const response = await fetch('/qr/process-and-confirm', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        qr_data: data
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Show success modal with animation
                    showSuccessModal(result);
                } else {
                    // Show error and restart scanner
                    updateStatus('Error: ' + result.message);
                    setTimeout(() => {
                        updateStatus('Position QR code within the frame');
                        restartScanner();
                    }, 3000);
                }
                
            } catch (error) {
                console.error('Error processing QR code:', error);
                updateStatus('Network error. Please try again.');
                setTimeout(() => {
                    updateStatus('Position QR code within the frame');
                    restartScanner();
                }, 3000);
            }
        }

        function showSuccessModal(data) {
            // Update points display
            document.getElementById('current-points').textContent = data.current_points.toLocaleString();
            document.getElementById('earned-points').textContent = data.points_earned.toLocaleString();
            document.getElementById('total-points').textContent = data.new_total_points.toLocaleString();
            
            // Calculate seller rank based on their total points (this would come from your backend)
            const sellerTotalPoints = data.seller.total_points || 0;
            const sellerRank = getRankInfo(sellerTotalPoints);
            
            // Update seller info with enhanced ranking display
            const sellerInfo = document.getElementById('seller-info');
            sellerInfo.innerHTML = `
                <div class="seller-header">
                    <div class="seller-logo">
                        ${data.seller.business_name.charAt(0)}
                    </div>
                    <div class="seller-details">
                        <h4>${data.seller.business_name}</h4>
                        <div class="seller-location">
                            <span>📍</span>
                            <span>${data.seller.location || 'Location not specified'}</span>
                        </div>
                        <div class="seller-rank">
                            <span>${sellerRank.icon}</span>
                            <span>${sellerRank.name} Store</span>
                        </div>
                    </div>
                </div>
                
                <div class="item-info">
                    <strong>Item:</strong> ${data.item.name}<br>
                    <strong>Points Earned:</strong> ${data.item.points_per_unit} pts<br>
                    <strong>Store Points:</strong> ${sellerTotalPoints.toLocaleString()} pts
                </div>
            `;
            
            // Show modal
            successModal.style.display = 'flex';
        }

        function getRankInfo(totalPoints) {
            if (totalPoints >= 10000) return { name: 'Diamond', icon: '💎' };
            if (totalPoints >= 5000) return { name: 'Platinum', icon: '👑' };
            if (totalPoints >= 2500) return { name: 'Gold', icon: '🥇' };
            if (totalPoints >= 1000) return { name: 'Silver', icon: '🥈' };
            if (totalPoints >= 500) return { name: 'Bronze', icon: '🥉' };
            return { name: 'Standard', icon: '⭐' };
        }

        async function restartScanner() {
            if (!isScanning) {
                try {
                    await scanner.start();
                    isScanning = true;
                    updateStatus('Position QR code within the frame');
                } catch (error) {
                    updateStatus('Error restarting camera');
                }
            }
        }

        async function toggleFlash() {
            if (scanner && scanner.hasFlash()) {
                try {
                    await scanner.toggleFlash();
                    const isOn = scanner.isFlashOn();
                    flashBtn.style.background = isOn ? 'rgba(255, 255, 255, 0.3)' : 'rgba(0, 0, 0, 0.6)';
                } catch (error) {
                    console.log('Flash toggle failed:', error);
                }
            }
        }

        function selectFromGallery() {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.onchange = handleImageUpload;
            input.click();
        }

        async function handleImageUpload(event) {
            const file = event.target.files[0];
            if (file) {
                try {
                    updateStatus('Scanning image...');
                    const result = await QrScanner.scanImage(file);
                    handleScanResult({ data: result });
                } catch (error) {
                    updateStatus('No QR code found in image');
                    setTimeout(() => {
                        updateStatus('Position QR code within the frame');
                    }, 2000);
                }
            }
        }

        function goToDashboard() {
            window.location.href = '{{ route("dashboard") }}';
        }

        function scanAnother() {
            successModal.style.display = 'none';
            restartScanner();
        }

        // Handle page visibility changes
        document.addEventListener('visibilitychange', function() {
            if (document.hidden && scanner && isScanning) {
                scanner.stop();
                isScanning = false;
            } else if (!document.hidden && scanner && !isScanning && successModal.style.display === 'none') {
                restartScanner();
            }
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (scanner) {
                scanner.stop();
            }
        });
    </script>
@endsection