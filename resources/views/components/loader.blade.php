<div id="loader" class="fixed inset-0 z-[9999] flex items-center justify-center bg-gradient-to-br from-gray-50 to-white">
    <style>
        /* Logo Container */
        .logo-container {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2rem;
        }

        /* Logo Animation */
        .logo-wrapper {
            position: relative;
            width: 120px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: logoFadeInOut 3s ease-in-out infinite;
        }

        .logo-circle {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: linear-gradient(135deg, #009BB9 0%, #007A94 100%);
            box-shadow: 0 10px 40px rgba(0, 155, 185, 0.3);
            animation: pulse 3s ease-in-out infinite;
        }

        .logo-text {
            position: relative;
            z-index: 10;
            font-size: 3rem;
            font-weight: 900;
            color: white;
            letter-spacing: -2px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .logo-cog {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 2.5rem;
            color: rgba(255, 255, 255, 0.9);
            animation: rotateCog 4s linear infinite;
        }

        /* Brand Name */
        .brand-name {
            font-size: 2.5rem;
            font-weight: 800;
            color: #009BB9;
            letter-spacing: 0.5rem;
            animation: textFadeInOut 3s ease-in-out infinite;
            text-transform: uppercase;
        }

        /* Loading Text */
        .loading-text {
            position: absolute;
            bottom: 3rem;
            left: 50%;
            transform: translateX(-50%);
            font-size: 1rem;
            color: #666;
            font-weight: 500;
            animation: dots 1.5s steps(4, end) infinite;
            white-space: nowrap;
        }

        /* Decorative Rings */
        .ring {
            position: absolute;
            border-radius: 50%;
            border: 2px solid #009BB9;
            opacity: 0;
        }

        .ring-1 {
            width: 140px;
            height: 140px;
            animation: ripple 3s ease-out infinite;
        }

        .ring-2 {
            width: 160px;
            height: 160px;
            animation: ripple 3s ease-out 0.5s infinite;
        }

        .ring-3 {
            width: 180px;
            height: 180px;
            animation: ripple 3s ease-out 1s infinite;
        }

        /* Animations */
        @keyframes logoFadeInOut {
            0%, 100% {
                opacity: 0;
                transform: scale(0.8);
            }
            50% {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes textFadeInOut {
            0%, 100% {
                opacity: 0;
                transform: translateY(20px);
            }
            50% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 10px 40px rgba(0, 155, 185, 0.3);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 15px 50px rgba(0, 155, 185, 0.5);
            }
        }

        @keyframes rotateCog {
            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        @keyframes ripple {
            0% {
                opacity: 0;
                transform: scale(1);
            }
            50% {
                opacity: 0.4;
            }
            100% {
                opacity: 0;
                transform: scale(1.5);
            }
        }

        @keyframes dots {
            0%, 20% {
                content: 'Memuat';
            }
            40% {
                content: 'Memuat.';
            }
            60% {
                content: 'Memuat..';
            }
            80%, 100% {
                content: 'Memuat...';
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .logo-wrapper {
                width: 100px;
                height: 100px;
            }

            .logo-text {
                font-size: 2.5rem;
            }

            .logo-cog {
                font-size: 2rem;
            }

            .brand-name {
                font-size: 2rem;
                letter-spacing: 0.3rem;
            }

            .ring-1 {
                width: 120px;
                height: 120px;
            }

            .ring-2 {
                width: 140px;
                height: 140px;
            }

            .ring-3 {
                width: 160px;
                height: 160px;
            }
        }

        @media (max-width: 320px) {
            .logo-wrapper {
                width: 80px;
                height: 80px;
            }

            .logo-text {
                font-size: 2rem;
            }

            .logo-cog {
                font-size: 1.5rem;
            }

            .brand-name {
                font-size: 1.5rem;
                letter-spacing: 0.2rem;
            }

            .loading-text {
                font-size: 0.875rem;
            }
        }
    </style>

    <div class="logo-container">
        <!-- Decorative Rings -->
        <div class="ring ring-1"></div>
        <div class="ring ring-2"></div>
        <div class="ring ring-3"></div>

        <!-- Logo -->
        <div class="logo-wrapper">
            <div class="logo-circle"></div>
            <div class="logo-text">M</div>
            <i class="fas fa-cog logo-cog"></i>
        </div>

        <!-- Brand Name -->
        <div class="brand-name">MONDAY</div>
    </div>

    <!-- Loading Text -->
    <div class="loading-text">Memuat...</div>
</div>

<script>
    // Hide loader when page is fully loaded
    window.addEventListener('load', function() {
        const loader = document.getElementById('loader');
        if (loader) {
            setTimeout(() => {
                loader.style.opacity = '0';
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 300);
            }, 500); // Delay sedikit untuk memastikan konten siap
        }
    });

    // Fallback: hide loader after 5 seconds
    setTimeout(() => {
        const loader = document.getElementById('loader');
        if (loader && loader.style.display !== 'none') {
            loader.style.opacity = '0';
            setTimeout(() => {
                loader.style.display = 'none';
            }, 300);
        }
    }, 5000);
</script>