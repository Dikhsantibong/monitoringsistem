<div id="loader" class="fixed inset-0 z-[9999] flex items-center justify-center bg-white bg-opacity-80 transition-opacity duration-300">
    <style>
        .loader {
            width: 100px; /* Ukuran diperbesar dari 50px ke 100px */
            aspect-ratio: 1;
            display: grid;
            color: #FFFF00;
            background: radial-gradient(farthest-side, currentColor calc(100% - 12px),#0000 calc(100% - 10px) 0); /* Sesuaikan ukuran gradient */
            -webkit-mask: radial-gradient(farthest-side,#0000 calc(100% - 26px),#000 calc(100% - 24px)); /* Sesuaikan ukuran mask */
            border-radius: 50%;
            animation: l19 2s infinite linear;
           
        }
        .loader::before,
        .loader::after {    
            content: "";
            grid-area: 1/1;
            background:
                linear-gradient(currentColor 0 0) center,
                linear-gradient(currentColor 0 0) center;
            background-size: 100% 20px,20px 100%; /* Sesuaikan ketebalan garis */
            background-repeat: no-repeat;
        }
        .loader::after {
            transform: rotate(45deg);
        }

        @keyframes l19 { 
            100%{transform: rotate(1turn)}
        }
    </style>
    <div class="loader"></div>
</div>