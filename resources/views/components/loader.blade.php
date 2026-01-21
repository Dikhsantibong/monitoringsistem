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

        /* Logo Image Wrapper */
        .logo-wrapper {
            position: relative;
            width: 150px;
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: logoFadeInOut 3s ease-in-out infinite;
        }

        .logo-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 10px 30px rgba(0, 155, 185, 0.3));
            animation: logoFloat 3s ease-in-out infinite;
        }

        /* Background Circle (optional, bisa dihapus jika tidak perlu) */
        .logo-bg-circle {
            position: absolute;
            width: 120%;
            height: 120%;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(0, 155, 185, 0.1) 0%, rgba(0, 122, 148, 0.1) 100%);
            animation: pulse 3s ease-in-out infinite;
            z-index: -1;
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
            white-space: nowrap;
        }

        .loading-text::after {
            content: '';
            animation: dots 1.5s steps(4, end) infinite;
        }

        /* Decorative Rings */
        .ring {
            position: absolute;
            border-radius: 50%;
            border: 2px solid #009BB9;
            opacity: 0;
        }

        .ring-1 {
            width: 180px;
            height: 180px;
            animation: ripple 3s ease-out infinite;
        }

        .ring-2 {
            width: 220px;
            height: 220px;
            animation: ripple 3s ease-out 0.5s infinite;
        }

        .ring-3 {
            width: 260px;
            height: 260px;
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

        @keyframes logoFloat {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
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
                opacity: 0.5;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }

        @keyframes ripple {
            0% {
                opacity: 0;
                transform: scale(1);
            }
            50% {
                opacity: 0.3;
            }
            100% {
                opacity: 0;
                transform: scale(1.5);
            }
        }

        @keyframes dots {
            0%, 20% {
                content: '';
            }
            40% {
                content: '.';
            }
            60% {
                content: '..';
            }
            80%, 100% {
                content: '...';
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .logo-wrapper {
                width: 120px;
                height: 120px;
            }

            .brand-name {
                font-size: 2rem;
                letter-spacing: 0.3rem;
            }

            .ring-1 {
                width: 150px;
                height: 150px;
            }

            .ring-2 {
                width: 180px;
                height: 180px;
            }

            .ring-3 {
                width: 210px;
                height: 210px;
            }
        }

        @media (max-width: 320px) {
            .logo-wrapper {
                width: 100px;
                height: 100px;
            }

            .brand-name {
                font-size: 1.5rem;
                letter-spacing: 0.2rem;
            }

            .loading-text {
                font-size: 0.875rem;
                bottom: 2rem;
            }

            .ring-1 {
                width: 120px;
                height: 120px;
            }

            .ring-2 {
                width: 150px;
                height: 150px;
            }

            .ring-3 {
                width: 180px;
                height: 180px;
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
            <div class="logo-bg-circle"></div>
            <!-- GANTI dengan path logo Anda -->
            <img src="{{ asset('logo/monday.png') }}" alt="Logo" class="logo-image">
        </div>

        <!-- Brand Name (optional, bisa dihapus jika tidak perlu) -->
        <div class="brand-name">MONDAY</div>
    </div>

    <!-- Loading Text -->
    <div class="loading-text">Memuat</div>
</div>

<script>
    // Hide loader when page is fully loaded
    window.addEventListener('load', function() {
        const loader = document.getElementById('loader');
        if (loader) {
            setTimeout(() => {
                loader.style.opacity = '0';
                loader.style.transition = 'opacity 0.5s ease-out';
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 500);
            }, 300);
        }
    });

    // Fallback: hide loader after 5 seconds
    setTimeout(() => {
        const loader = document.getElementById('loader');
        if (loader && loader.style.display !== 'none') {
            loader.style.opacity = '0';
            loader.style.transition = 'opacity 0.5s ease-out';
            setTimeout(() => {
                loader.style.display = 'none';
            }, 500);
        }
    }, 5000);
</script>