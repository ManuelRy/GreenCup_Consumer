@extends('master')

@section('content')
    <div class="background-animation">
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
        <div class="particles">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <a href="{{ route('dashboard') }}" class="back-button">
                ← Green Cup
            </a>
            <div class="page-title">
                <h2>My QR Code</h2>
                <p>Your Digital Identity</p>
            </div>
            <div class="header-spacer"></div>
        </div>

        <div class="qr-main-card">
            <div class="qr-code-display">
                <div class="qr-code-frame">
                    <!-- Real QR Code -->
                    <img id="qr-code-img" 
                         src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($consumer->qrCode->code ?? 'GC_1_ABC12345') }}" 
                         alt="QR Code" 
                         class="qr-code-image"
                         onload="hideQRFallback()"
                         onerror="showQRFallback()">
                    
                    <!-- Fallback if QR code fails to load -->
                    <div id="qr-fallback" class="qr-fallback" style="display: none;">
                        <div class="qr-center-icon">📱</div>
                    </div>
                </div>
                <div class="qr-code-text">
                    {{ $consumer->qrCode->code ?? 'GC_1_ABC12345' }}
                </div>
            </div>

            <div class="qr-instructions">
                <div class="instruction-icon">🌱</div>
                <div class="instruction-content">
                    <h3>How Your QR Code Works</h3>
                    <div class="instruction-steps">
                        <div class="step">
                            <span class="step-number">1</span>
                            <span class="step-text">Show this QR code to participating Green Cup sellers</span>
                        </div>
                        <div class="step">
                            <span class="step-number">2</span>
                            <span class="step-text">Seller scans your code with their phone</span>
                        </div>
                        <div class="step">
                            <span class="step-number">3</span>
                            <span class="step-text">Points are instantly added to your account</span>
                        </div>
                        <div class="step">
                            <span class="step-number">4</span>
                            <span class="step-text">Use points for discounts and rewards!</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="qr-stats">
                <div class="stat-item">
                    <div class="stat-icon">💰</div>
                    <div class="stat-info">
                        <div class="stat-value">{{ number_format($consumer->getAvailablePoints() ?? 0) }}</div>
                        <div class="stat-label">Available Points</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">📈</div>
                    <div class="stat-info">
                        <div class="stat-value">{{ $consumer->pointTransactions()->count() ?? 0 }}</div>
                        <div class="stat-label">Transactions</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="qr-actions-grid">
            <div class="qr-action-item share-action" onclick="shareQR()">
                <div class="qr-action-icon">📤</div>
                <div class="qr-action-name">Share QR</div>
            </div>
            <div class="qr-action-item download-action" onclick="downloadQR()">
                <div class="qr-action-icon">💾</div>
                <div class="qr-action-name">Save Image</div>
            </div>
            <div class="qr-action-item refresh-action" onclick="refreshQR()">
                <div class="qr-action-icon">🔄</div>
                <div class="qr-action-name">Refresh</div>
            </div>
            <a href="{{ route('dashboard') }}" class="qr-action-item dashboard-action">
                <div class="qr-action-icon">🏠</div>
                <div class="qr-action-name">Dashboard</div>
            </a>
        </div>
    </div>

    <style>
        /* Header styling - with proper gaps */
        .header {
            display: flex;
            align-items: center;
            gap: 4rem;
            margin-bottom: 2rem;
            justify-content: space-between;
            width: 100%;
        }

        .back-button {
            color: white;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 600;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            flex-shrink: 0;
        }

        .back-button:hover {
            transform: translateX(-5px);
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4);
            color: #e8f5e8;
        }

        .page-title {
            text-align: center;
            color: white;
            flex: 1;
            margin: 0 2rem;
        }
            .container {
            background: rgba(255, 255, 255, 0.95);
            min-height: 100vh;
            position: relative;
            backdrop-filter: blur(10px);
            border-radius: 0;
            overflow: hidden;
            max-width: 1000px;
        }

        .page-title h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .page-title p {
            margin: 0.25rem 0 0 0;
            opacity: 0.9;
            font-size: 0.9rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .header-spacer {
            width: 120px;
            flex-shrink: 0;
        }

        /* Main QR Card */
        .qr-main-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            margin: 2rem 0;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            color: #333;
        }

        .qr-code-display {
            text-align: center;
            margin: 2rem 0;
        }

        .qr-code-frame {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin: 0 auto 1rem;
            max-width: 250px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: relative;
            border: 3px solid #4CAF50;
        }

        .qr-code-image {
            width: 200px;
            height: 200px;
            border-radius: 10px;
            display: block;
            margin: 0 auto;
        }

        .qr-fallback {
            width: 200px;
            height: 200px;
            margin: 0 auto;
            background: #f5f5f5;
            border: 3px solid #4CAF50;
            border-radius: 10px;
            display: none;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: 1.5rem;
            left: 1.5rem;
        }

        .qr-center-icon {
            font-size: 3rem;
            color: #4CAF50;
        }

        .qr-code-text {
            font-family: 'Courier New', monospace;
            color: #333;
            font-size: 0.9rem;
            font-weight: 600;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            word-break: break-all;
            border: 2px solid #e9ecef;
            margin-top: 1rem;
            letter-spacing: 1px;
        }

        /* Instructions section */
        .qr-instructions {
            background: rgba(76, 175, 80, 0.1);
            border: 2px solid rgba(76, 175, 80, 0.3);
            border-radius: 15px;
            padding: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-top: 2rem;
        }

        .instruction-icon {
            font-size: 2rem;
            flex-shrink: 0;
        }

        .instruction-content h3 {
            color: #2E7D32;
            margin: 0 0 1rem 0;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .instruction-steps {
            margin-top: 1rem;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.75rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 10px;
            border: 1px solid rgba(76, 175, 80, 0.2);
        }

        .step:last-child {
            margin-bottom: 0;
        }

        .step-number {
            width: 28px;
            height: 28px;
            background: #4CAF50;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            font-weight: 700;
            flex-shrink: 0;
            box-shadow: 0 2px 4px rgba(76, 175, 80, 0.3);
        }

        .step-text {
            color: #333;
            font-size: 0.95rem;
            font-weight: 500;
            line-height: 1.4;
        }

        /* Stats section */
        .qr-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 2rem;
        }

        .stat-item {
            background: rgba(76, 175, 80, 0.1);
            border: 2px solid rgba(76, 175, 80, 0.2);
            border-radius: 15px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            font-size: 2rem;
            flex-shrink: 0;
        }

        .stat-info {
            flex: 1;
        }

        .stat-value {
            color: #2E7D32;
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            color: #666;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        /* Action buttons */
        .qr-actions-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-top: 2rem;
        }

        .qr-action-item {
            border-radius: 15px;
            padding: 1.5rem 1rem;
            text-align: center;
            border: 2px solid rgba(255, 255, 255, 0.3);
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: white;
            backdrop-filter: blur(10px);
            font-weight: 600;
        }

        /* Colorful action buttons with better contrast */
        .share-action {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: rgba(102, 126, 234, 0.5);
        }

        .download-action {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border-color: rgba(240, 147, 251, 0.5);
        }

        .refresh-action {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            border-color: rgba(79, 172, 254, 0.5);
        }

        .dashboard-action {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            border-color: rgba(67, 233, 123, 0.5);
        }

        .qr-action-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            filter: brightness(1.1);
        }

        .qr-action-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .qr-action-name {
            font-size: 0.9rem;
            font-weight: 600;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .qr-actions-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
            }
            
            .qr-main-card {
                padding: 1.5rem;
                margin: 1rem 0;
            }
            
            .header {
                flex-direction: column;
                gap: 1rem;
                margin-bottom: 1rem;
                align-items: flex-start;
            }

            .back-button {
                font-size: 1.2rem;
                align-self: flex-start;
            }

            .page-title {
                align-self: center;
                margin-top: 0.5rem;
                flex: none;
            }

            .page-title h2 {
                font-size: 1.2rem;
            }

            .header-spacer {
                display: none;
            }

            .qr-stats {
                grid-template-columns: 1fr;
            }

            .stat-item {
                padding: 1rem;
            }

            .step {
                gap: 0.75rem;
                padding: 0.75rem;
            }

            .qr-action-item {
                padding: 1rem 0.75rem;
            }

            .qr-action-icon {
                font-size: 1.5rem;
            }

            .qr-action-name {
                font-size: 0.8rem;
            }
        }

        @media (max-width: 480px) {


            .qr-code-frame {
                padding: 1rem;
                max-width: 220px;
            }

            .qr-code-image {
                width: 180px;
                height: 180px;
            }

            .qr-fallback {
                width: 180px;
                height: 180px;
                top: 1rem;
                left: 1rem;
            }

            .instruction-content h3 {
                font-size: 1.1rem;
            }

            .step-text {
                font-size: 0.9rem;
            }
        }
    </style>

    <script>
        function showQRFallback() {
            document.getElementById('qr-code-img').style.display = 'none';
            document.getElementById('qr-fallback').style.display = 'flex';
        }

        function hideQRFallback() {
            document.getElementById('qr-code-img').style.display = 'block';
            document.getElementById('qr-fallback').style.display = 'none';
        }

        function shareQR() {
            const qrCodeData = '{{ $consumer->qrCode->code ?? "GC_1_ABC12345" }}';
            
            if (navigator.share) {
                navigator.share({
                    title: 'My Green Cup QR Code',
                    text: `My Green Cup QR Code: ${qrCodeData}`,
                    url: window.location.href
                }).catch(console.error);
            } else {
                // Fallback - copy QR code data to clipboard
                navigator.clipboard.writeText(qrCodeData).then(() => {
                    showNotification('QR code copied to clipboard!');
                }).catch(() => {
                    showNotification('Could not copy QR code');
                });
            }
        }

        function downloadQR() {
            // Create a download link for the QR code image
            const qrCodeData = '{{ $consumer->qrCode->code ?? "GC_1_ABC12345" }}';
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=${encodeURIComponent(qrCodeData)}`;
            
            // Create temporary link and trigger download
            const link = document.createElement('a');
            link.href = qrUrl;
            link.download = `green-cup-qr-${Date.now()}.png`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            showNotification('QR code download started!');
        }

        function refreshQR() {
            const qrImg = document.getElementById('qr-code-img');
            const qrCodeData = '{{ $consumer->qrCode->code ?? "GC_1_ABC12345" }}';
            
            // Show loading state
            showNotification('Refreshing QR code...');
            
            // Reset the image
            qrImg.style.display = 'none';
            document.getElementById('qr-fallback').style.display = 'none';
            
            // Create new image URL with cache buster
            const newUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(qrCodeData)}&t=${Date.now()}`;
            
            // Set up the image load handlers before changing src
            qrImg.onload = function() {
                hideQRFallback();
                showNotification('QR code refreshed successfully!');
            };
            
            qrImg.onerror = function() {
                showQRFallback();
                showNotification('Failed to load QR code');
            };
            
            // Load the new image
            setTimeout(() => {
                qrImg.src = newUrl;
            }, 500);
        }

        function showNotification(message) {
            // Create a notification
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 10px;
                z-index: 1000;
                animation: slideIn 0.3s ease, slideOut 0.3s ease 2.7s;
                backdrop-filter: blur(10px);
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
                font-weight: 600;
                border: 2px solid rgba(255, 255, 255, 0.3);
            `;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 3000);
        }

        // Add CSS for notifications
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    </script>
@endsection