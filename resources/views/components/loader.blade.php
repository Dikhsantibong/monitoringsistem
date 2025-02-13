<div id="loader" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-white bg-opacity-80 transition-opacity duration-300">
    <style>
        .loader-text {
            font-size: clamp(2.5rem, 8vw, 4rem); /* Responsive font size */
            font-weight: bold;
            color: #009BB9;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center; /* Center horizontally */
            gap: 0.25rem; /* Reduced gap for mobile */
            width: 100%; /* Full width */
            padding: 0 1rem; /* Add padding */
            text-align: center; /* Center text */
        }

        
        .cog-icon {
            color: #333333;
            animation: spin 2s linear infinite;
            font-size: 0.9em; /* Relative to parent font size */
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes spin {
            100% { transform: rotate(360deg); }
        }

        /* Animasi untuk teks */
        .loading-text {
            color: #009BB9;
            font-size: clamp(1rem, 4vw, 1.25rem); /* Responsive font size */
            margin-top: 1rem;
            animation: pulse 1.5s infinite;
            text-align: center; /* Center text */
            width: 100%; /* Full width */
            padding: 0 1rem; /* Add padding */
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Container for better mobile positioning */
        .loader-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        /* Media query for very small devices */
        @media (max-width: 320px) {
            .loader-text {
                font-size: 2rem;
            }
            .loading-text {
                font-size: 0.875rem;
            }
        }
    </style>

    <div class="loader-container">
        <div class="loader-text">
            M<i class="fas fa-cog cog-icon"></i>NDAY
        </div>
        <div class="loading-text">Monday sedang di muat...</div>
    </div>
</div>