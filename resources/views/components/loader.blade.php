<div id="loader" class="fixed inset-0 z-[9999] flex items-center justify-center bg-white">
    <style>
        /* Logo Wrapper */
        .logo-wrapper {
            position: relative;
            width: 150px;
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: logoFadeInOut 2s ease-in-out infinite;
        }

        .logo-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* Animations */
        @keyframes logoFadeInOut {
            0%, 100% {
                opacity: 0;
                transform: scale(0.9);
            }
            50% {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .logo-wrapper {
                width: 120px;
                height: 120px;
            }
        }

        @media (max-width: 320px) {
            .logo-wrapper {
                width: 100px;
                height: 100px;
            }
        }
    </style>

    <!-- Logo -->
    <div class="logo-wrapper">
        <img src="{{ asset('logo/monday.png') }}" alt="Logo" class="logo-image">
    </div>
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