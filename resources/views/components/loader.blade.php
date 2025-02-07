<div id="loader" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-white bg-opacity-80 transition-opacity duration-300">
    <style>
        .loader-text {
            font-size: 4rem;
            font-weight: bold;
            color: #009BB9;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .cog-icon {
            color: #333333;
            animation: spin 2s linear infinite;
            font-size: 3.5rem;
            display: inline-block;
        }

        @keyframes spin {
            100% { transform: rotate(360deg); }
        }

        /* Animasi untuk teks */
        .loading-text {
            color: #009BB9;
            font-size: 1.25rem;
            margin-top: 2rem;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>

    <div class="loader-text mb-1">
        M<i class="fas fa-cog cog-icon"></i>NDAY
    </div>
    <div class="loading-text mt-1">Monday sedang di muat...</div>
</div>