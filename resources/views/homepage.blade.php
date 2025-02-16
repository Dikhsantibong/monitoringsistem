@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/homepage.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .navbar.hidden {
            transform: translateY(-100%);
        }

        .navbar-brand img {
            height: 50px;
        }

        .nav-link {
            color: white !important;
        }

        .nav-link:hover {
            color: #A8D600 !important;
        }

        /* Desktop menu styles */
        .desktop-menu {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .desktop-menu a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }

        /* Mobile menu styles */
        .mobile-menu {
            display: none;
            position: fixed;
            top: 60px; /* Sesuaikan dengan tinggi navbar */
            left: 0;
            right: 0;
            background-color: #1a1a1a;
            padding: 1rem;
            z-index: 50;
            transition: transform 0.3s ease-in-out;
            transform: translateY(-100%);
        }

        .mobile-menu.show {
            transform: translateY(0);
            display: block;
        }

        .mobile-menu a {
            display: block;
            color: white;
            padding: 0.75rem 1rem;
            text-decoration: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .mobile-menu a:last-child {
            border-bottom: none;
        }

        .mobile-menu a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #A8D600;
        }

        @media (max-width: 768px) {
            .navbar-toggler {
                display: block;
            }
            
            .desktop-menu {
                display: none;
            }
        }

        /* Responsive breakpoints */
        @media (max-width: 991px) {
            .desktop-menu {
                display: none;
            }

            .navbar-toggler {
                display: block;
            }

            .mobile-menu.show {
                display: block;
            }
        }

        /* Adjust content padding to prevent overlap with fixed navbar */
        h3 {
            font-size: 1.25rem;
            margin: 5px 0;
            text-align: center;
            color: #0095B7;
        }

        /* Hexagon styles */
        /* Background for the hexagon section */
        .hexagon-background {
            background-image: url('{{ asset('background/backgorund.jpg') }}');
            background-size: cover;
            background-position: center;
        }
        .hexagon {
            position: relative;
            margin: 28.87px 0;
            clip-path: polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            transform-style: preserve-3d;
            text-shadow: 1px 1px 2px #000000;
            
        }

        .hexagon:hover {
            transform: translateY(-10px) rotateX(10deg) rotateY(10deg);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .hexagon:focus {
            transform: translateY(-10px) rotateX(10deg) rotateY(10deg);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .nav-link {
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background-color: #0095B7;
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .nav-link.active {
            color: #0095B7 !important;
        }

        .nav-link.active::after {
            width: 100%;
        }

        /* Dark mode adjustments */
        .dark .nav-link {
            color: white !important;
        }

        /* Ensure content doesn't hide behind navbar */
        main {
            margin-top: 120px; /* Sesuaikan dengan total tinggi kedua navbar */
        }

        /* Loader styles */
        .loader-hidden {
            opacity: 0;
            visibility: hidden;
        }
        
        .page-transition {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        
        .page-visible {
            opacity: 1;
        }

        /* Navbar Styles */
        .nav-link {
            color: #1a202c;
            text-decoration: none;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
            position: relative;
        }

        .nav-link:hover {
            color: #4299e1;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: #4299e1;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .nav-link.active {
            color: #4299e1;
        }

        .nav-link.active::after {
            width: 100%;
        }

        /* Mobile Menu Styles */
        .nav-link-mobile {
            display: block;
            padding: 0.75rem 1rem;
            color: #1a202c;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .nav-link-mobile:hover {
            background-color: #f7fafc;
            color: #4299e1;
        }

        @media (prefers-color-scheme: dark) {
            .nav-link {
                color: #fff;
            }
            
            .nav-link-mobile {
                color: #fff;
            }

            .nav-link-mobile:hover {
                background-color: #2d3748;
            }
        }

        /* Navbar Background */
        .nav-background {
            background-color: #1a1a1a; /* Dark background */
            background-image: linear-gradient(to right, #1a1a1a, #2d3748);
        }

        /* Navbar Styles */
        .nav-link {
            color: #ffffff;
            text-decoration: none;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
            position: relative;
            font-weight: 500;
        }

        .nav-link:hover {
            color: #4299e1;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 50%;
            background-color: #4299e1;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .nav-link.active {
            color: #4299e1;
        }

        .nav-link.active::after {
            width: 100%;
        }

        /* Mobile Menu Styles */
        .nav-link-mobile {
            display: block;
            padding: 0.75rem 1rem;
            color: #ffffff;
            text-decoration: none;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .nav-link-mobile:hover {
            background-color: #2d3748;
            color: #4299e1;
            border-left-color: #4299e1;
        }

        /* Dark mode is now default */
        .nav-link, .nav-link-mobile {
            color: #ffffff;
        }

        .nav-link-mobile:hover {
            background-color: #2d3748;
        }

        /* Glassmorphism effect */
        .nav-background {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        /* Shadow effect */
        .shadow-lg {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 
                        0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Tambahan style untuk tombol login */
        .login-button {
            background-color: #4299e1;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .login-button:hover {
            background-color: #3182ce;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 
                        0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Style untuk login di mobile menu */
        .login-mobile {
            background-color: #4299e1;
            color: white !important;
            border-radius: 0.375rem;
            margin: 0.5rem 1rem;
        }

        .login-mobile:hover {
            background-color: #3182ce !important;
            border-left-color: transparent !important;
        }

        /* Memastikan icon font-awesome sejajar dengan teks */
        .fas {
            display: inline-flex;
            align-items: center;
        }

        /* Adjust body and main content positioning */
        body {
            margin: 0;
            padding-top: 100px;
            min-height: calc(100vh - 100px);
            overflow-x: hidden;
        }

        main {
            margin-top: 80px; /* Sesuaikan dengan tinggi navbar */
        }

        /* Hero section adjustments */
        .hexagon-background {
            position: relative;
            margin-top: -80px;
            background-image: url('{{ asset('background/backgorund.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        /* Tambahkan overlay gelap untuk meningkatkan kontras */
        .hexagon-background::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            /* background: rgba(0, 0, 0, 0.5); Sesuaikan opacity sesuai kebutuhan */
            z-index: 1;
        }

        /* Pastikan konten hero berada di atas overlay */
        .hexagon-background > * {
            position: relative;
            z-index: 2;
        }

        /* Pastikan section di bawah hero memiliki background putih */
        #map, #grafik, #live-data {
            background-color: white;
            position: relative;
            z-index: 1;
        }

        /* Adjust content positioning */
        main {
            margin-top: 0;
        }

        /* Remove spacer */
        .h-[80px] {
            display: none;
        }

        /* Ensure map section starts after hero */
        #map {
            margin-top: 0;
            position: relative;
            z-index: 1;
        }

        /* Additional responsive adjustments */
        @media (max-height: 768px) {
            .hexagon-background {
                padding: 120px 0;
            }
        }

        /* Navbar container styles */
        .nav-background .container {
            max-width: 1200px; /* Sesuaikan dengan kebutuhan */
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Logo and menu container */
        .flex.justify-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 2rem; /* Tambahkan gap antara logo dan menu */
        }

        /* Menu items container */
        .hidden.md\:flex.items-center {
            flex: 1; /* Tambahkan ini */
            justify-content: center; /* Tambahkan ini */
        }

        .hidden.md\:flex.items-center ul {
            display: flex;
            justify-content: center; /* Tambahkan ini */
            align-items: center;
            width: 100%; /* Tambahkan ini */
        }

        /* Mobile menu transition styles */
        #mobile-menu {
            transition: max-height 0.3s ease-in-out;
            overflow: hidden;
            max-height: 0;
        }

        #mobile-menu.hidden {
            display: none;
        }

        /* Improved mobile menu styles */
        .nav-link-mobile {
            display: block;
            padding: 0.75rem 1rem;
            color: white;
            text-decoration: none;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .nav-link-mobile:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: #4299e1;
            padding-left: 1.25rem;
        }

        /* Hamburger button hover effect */
        #mobile-menu-button {
            padding: 0.5rem;
            border-radius: 0.375rem;
            transition: background-color 0.2s ease;
        }

        #mobile-menu-button:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        #mobile-menu-button:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(66, 153, 225, 0.5);
        }

        /* Dark background for chart sections */
        #machineReadinessChart,
        #powerDeliveryChart,
        #unservedLoadChart {
            background-color: rgba(168, 214, 0, 0.35);  /* Meningkatkan opacity ke 0.35 */
            border-radius: 8px;
            padding: 16px;
        }

        /* Container styles for charts */
        .bg-gray-50 {
            background-color: rgba(168, 214, 0, 0.4) !important;  /* Meningkatkan opacity ke 0.4 */
        }

        /* Chart container hover effect */
        .bg-gray-50:hover {
            background-color: rgba(168, 214, 0, 0.45) !important;  /* Meningkatkan opacity ke 0.45 */
            transition: background-color 0.3s ease;
        }

        /* Ensure text remains readable */
        .bg-gray-50 .text-gray-700,
        .bg-gray-50 .text-gray-500 {
            color: #374151 !important;  /* Darker text for better contrast */
        }

        /* Chart grid lines */
        .apexcharts-grid line {
            stroke: rgba(255, 255, 255, 0.15);  /* Slightly more visible grid lines */
        }

        /* Chart tooltip */
        .apexcharts-tooltip {
            background: rgba(17, 24, 39, 0.97) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2), 0 2px 4px -1px rgba(0, 0, 0, 0.1);
        }

        .apexcharts-tooltip-title {
            background: rgba(17, 24, 39, 0.9) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15) !important;
        }

        /* Chart legend */
        .apexcharts-legend-text {
            color: #374151 !important;  /* Darker text for better readability */
        }

        /* Mobile-specific adjustments - Further Refined */
        @media (max-width: 768px) {
            /* Hero section adjustments */
            .hexagon-background {
                padding: 40px 0 !important;
                min-height: 100vh !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }

            /* Title adjustments */
            .text-9xl {
                font-size: 2.5rem !important;
                line-height: 1 !important;
                margin-bottom: 0.5rem !important;
            }
            
            .text-6xl {
                font-size: 1.5rem !important;
                margin-top: 0.25rem !important;
            }
            
            .text-3xl {
                font-size: 1rem !important;
            }

            /* Hexagon container adjustments */
            .flex.gap-2.lg\:gap-0.lg\:grid {
                width: 100% !important;
                max-width: 320px !important;
                margin: 0 auto !important;
                display: flex !important;
                flex-direction: column !important;
                gap: 20px !important;
            }

            /* Hexagon layout */
            .flex.gap-2.lg\:gap-0.lg\:grid > div {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                gap: 20px !important;
            }

            /* Individual hexagon */
            .hexagon {
                width: 130px !important;
                height: 75px !important;
                margin: 0 !important;
                position: relative !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                background-color: rgba(10, 116, 155, 0.8) !important;
                transition: transform 0.3s ease !important;
            }

            /* Center hexagon (UP KENDARI) */
            .hidden.lg\:block.md\:block {
                display: block !important;
                order: 2 !important; /* Reorder to middle */
            }

            .hidden.lg\:block.md\:block .hexagon {
                background-color: rgba(10, 116, 155, 1) !important;
                width: 150px !important;
                height: 85px !important;
            }

            /* Text inside hexagons */
            .hexagon h5 {
                font-size: 0.85rem !important;
                line-height: 1.2 !important;
                padding: 0 8px !important;
                text-align: center !important;
                color: white !important;
                font-weight: bold !important;
                margin: 0 !important;
            }

            /* Reorder hexagons for better mobile layout */
            .flex.gap-2.lg\:gap-0.lg\:grid > div:nth-child(1) {
                order: 1 !important;
            }
            .flex.gap-2.lg\:gap-0.lg\:grid > div:nth-child(3) {
                order: 3 !important;
            }

            /* Map section adjustments */
            #map {
                margin: 10px !important;
                height: 350px !important;
                border-radius: 15px !important;
            }

            /* Navigation adjustments */
            .nav-background .container {
                padding: 0 0.5rem !important;
            }

            /* Content spacing */
            .container {
                padding: 0 1rem !important;
                margin: 0 auto !important;
            }
        }

        /* Extra small devices */
        @media (max-width: 480px) {
            /* Further size reductions */
            .text-9xl {
                font-size: 2rem !important;
            }
            
            .text-6xl {
                font-size: 1.25rem !important;
            }

            /* Smaller hexagons */
            .hexagon {
                width: 120px !important;
                height: 70px !important;
            }

            .hidden.lg\:block.md\:block .hexagon {
                width: 140px !important;
                height: 80px !important;
            }

            /* Smaller text */
            .hexagon h5 {
                font-size: 0.8rem !important;
            }

            /* Tighter spacing */
            .flex.gap-2.lg\:gap-0.lg\:grid {
                gap: 15px !important;
                max-width: 280px !important;
            }

            /* Adjust map */
            #map {
                margin: 8px !important;
                height: 300px !important;
            }
        }

        /* Touch device optimizations */
        @media (hover: none) {
            .hexagon {
                cursor: pointer !important;
            }

            .hexagon:active {
                transform: scale(0.95) !important;
            }
        }

        /* Ensure smooth transitions */
        .hexagon {
            transition: all 0.3s ease-in-out !important;
            backdrop-filter: blur(5px) !important;
            -webkit-backdrop-filter: blur(5px) !important;
        }

        /* Prevent text overflow */
        .hexagon h5 {
            white-space: normal !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        /* Status Information Grid Improvements */
        .status-info-container {
            background: #e8f5e9 !important;
            border-radius: 15px !important;
            padding: 20px !important;
            margin: 15px 0 !important;
        }

        .status-info-title {
            text-align: center !important;
            color: #2c3e50 !important;
            font-size: 1.2rem !important;
            font-weight: bold !important;
            margin-bottom: 5px !important;
        }

        .status-info-subtitle {
            text-align: center !important;
            color: #666 !important;
            font-size: 0.9rem !important;
            margin-bottom: 20px !important;
        }

        .status-grid-container {
            display: grid !important;
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 10px !important;
            margin-top: 15px !important;
        }

        .status-item {
            background: white !important;
            border-radius: 8px !important;
            padding: 10px !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05) !important;
        }

        /* Status Colors */
        .status-operasi {
            border-left: 4px solid #4CAF50 !important;
        }

        .status-standby {
            border-left: 4px solid #2196F3 !important;
        }

        .status-gangguan {
            border-left: 4px solid #f44336 !important;
        }

        .status-pemeliharaan {
            border-left: 4px solid #FF9800 !important;
        }

        .status-mothballed {
            border-left: 4px solid #9C27B0 !important;
        }

        .status-overhaul {
            border-left: 4px solid #795548 !important;
        }

        .status-label {
            font-weight: 500 !important;
            font-size: 0.9rem !important;
        }

        .status-value {
            background: #f8f9fa !important;
            padding: 4px 8px !important;
            border-radius: 4px !important;
            font-weight: bold !important;
            font-size: 0.9rem !important;
            min-width: 60px !important;
            text-align: center !important;
        }

        /* Status Text Colors */
        .status-operasi .status-label { color: #4CAF50 !important; }
        .status-standby .status-label { color: #2196F3 !important; }
        .status-gangguan .status-label { color: #f44336 !important; }
        .status-pemeliharaan .status-label { color: #FF9800 !important; }
        .status-mothballed .status-label { color: #9C27B0 !important; }
        .status-overhaul .status-label { color: #795548 !important; }

        /* Percentage Circle Styles */
        .percentage-circle {
            width: 120px !important;
            height: 120px !important;
            margin: 0 auto 20px auto !important;
            position: relative !important;
            background: white !important;
            border-radius: 50% !important;
            padding: 20px !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
        }

        .percentage-value {
            font-size: 1.8rem !important;
            font-weight: bold !important;
            color: #0095B7 !important;
            text-align: center !important;
            margin-bottom: 5px !important;
        }

        .percentage-label {
            font-size: 0.8rem !important;
            color: #666 !important;
            text-align: center !important;
        }

        /* Mobile Adjustments */
        @media (max-width: 480px) {
            .status-grid-container {
                grid-template-columns: 1fr !important;
            }

            .status-item {
                padding: 8px !important;
            }

            .status-label {
                font-size: 0.85rem !important;
            }

            .status-value {
                font-size: 0.85rem !important;
                min-width: 50px !important;
            }

            .percentage-circle {
                width: 100px !important;
                height: 100px !important;
            }

            .percentage-value {
                font-size: 1.5rem !important;
            }
        }

        /* Filter Buttons Container */
        .filter-buttons-container {
            width: 100% !important;
            padding: 10px !important;
            margin-bottom: 20px !important;
        }

        /* Filter Buttons Wrapper */
        .filter-buttons {
            display: flex !important;
            flex-direction: column !important;
            gap: 8px !important;
            width: 100% !important;
            max-width: 400px !important;
            margin: 0 auto !important;
        }

        /* Individual Button Style */
        .filter-btn {
            width: 100% !important;
            padding: 12px 20px !important;
            border: none !important;
            border-radius: 8px !important;
            background-color: #f0f0f0 !important;
            color: #333 !important;
            font-size: 16px !important;
            font-weight: 500 !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
        }

        /* Active Button State */
        .filter-btn.active {
            background-color: #0095B7 !important;
            color: white !important;
        }

        /* Hover State */
        .filter-btn:hover {
            opacity: 0.9 !important;
        }

        /* Active State */
        .filter-btn:active {
            transform: scale(0.98) !important;
        }

        /* Icon Style */
        .filter-btn i {
            font-size: 18px !important;
        }

        /* Tablet and Desktop */
        @media (min-width: 768px) {
            .filter-buttons {
                flex-direction: row !important;
                justify-content: center !important;
            }

            .filter-btn {
                width: auto !important;
                min-width: 150px !important;
            }
        }

        /* Small Mobile Devices */
        @media (max-width: 480px) {
            .filter-buttons-container {
                padding: 8px !important;
            }

            .filter-btn {
                padding: 10px 16px !important;
                font-size: 14px !important;
            }

            .filter-btn i {
                font-size: 16px !important;
            }
        }

        /* Chart Container Improvements */
        .chart-container {
            background: rgba(255, 255, 255, 0.95) !important;
            border-radius: 15px !important;
            padding: 15px !important;
            margin: 10px !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
        }

        /* Add horizontal scroll for unserved load chart on mobile */
        @media (max-width: 768px) {
            #unservedLoadChart-wrapper {
                width: 100% !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
            }
            
            #unservedLoadChart {
                width: 1024px !important; /* Fixed desktop width */
            }

            /* Move unit names to the right side */
            .apexcharts-yaxis {
                position: absolute !important;
                right: 0 !important;
                top: 0 !important;
                bottom: 0 !important;
                background: rgba(255, 255, 255, 0.9) !important; /* Slight background to ensure readability */
            }

            .apexcharts-yaxis-label {
                text-align: left !important;
                padding-left: 10px !important;
            }

            /* Ensure bar labels remain visible */
            .apexcharts-bar-series .apexcharts-datalabel {
                padding-right: 150px !important; /* Give space for unit names */
            }
        }

        /* Chart Title */
        .chart-title {
            font-size: 1rem !important;
            font-weight: 600 !important;
            color: #2c3e50 !important;
            margin-bottom: 10px !important;
            padding: 0 5px !important;
            text-align: center !important;
        }

        /* Chart Content */
        .chart-content {
            width: 100% !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
            scrollbar-width: none !important; /* Firefox */
            position: relative !important;
        }

        /* Hide scrollbar but keep functionality */
        .chart-content::-webkit-scrollbar {
            display: none !important;
        }

        /* Chart Canvas Container */
        .chart-canvas-container {
            min-width: 100% !important;
            padding-bottom: 10px !important;
        }

        /* Mobile Specific Chart Adjustments */
        @media (max-width: 768px) {
            /* Adjust chart container */
            .chart-container {
                margin: 8px !important;
                padding: 12px 8px !important;
            }

            /* Adjust chart size */
            .apexcharts-canvas {
                width: 100% !important;
                height: auto !important;
                max-height: 300px !important;
            }

            /* Adjust legend */
            .apexcharts-legend {
                position: relative !important;
                padding: 5px !important;
                display: flex !important;
                flex-wrap: wrap !important;
                justify-content: center !important;
                gap: 10px !important;
            }

            .apexcharts-legend-series {
                margin: 2px 8px !important;
            }

            /* Adjust axis labels */
            .apexcharts-xaxis-label, 
            .apexcharts-yaxis-label {
                font-size: 10px !important;
            }

            /* Adjust tooltip */
            .apexcharts-tooltip {
                font-size: 12px !important;
                padding: 5px 8px !important;
            }

            /* Chart Grid */
            .apexcharts-grid line {
                stroke-width: 0.5 !important;
            }

            /* Data Labels */
            .apexcharts-datalabel {
                font-size: 10px !important;
            }

            /* Unit Labels */
            .unit-label {
                font-size: 0.8rem !important;
                margin-right: 5px !important;
            }

            /* Date Labels */
            .date-label {
                font-size: 0.75rem !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }

            /* Value Labels */
            .value-label {
                font-size: 0.85rem !important;
                font-weight: 600 !important;
            }
        }

        /* Extra Small Devices */
        @media (max-width: 480px) {
            .chart-container {
                padding: 8px 5px !important;
            }

            .chart-title {
                font-size: 0.9rem !important;
            }

            /* Adjust chart dimensions */
            .apexcharts-canvas {
                max-height: 250px !important;
            }

            /* Smaller text for very small screens */
            .apexcharts-xaxis-label, 
            .apexcharts-yaxis-label {
                font-size: 9px !important;
            }

            .unit-label {
                font-size: 0.7rem !important;
            }

            .value-label {
                font-size: 0.8rem !important;
            }
        }

        /* Ensure chart responsiveness */
        .apexcharts-canvas {
            margin: 0 auto !important;
        }

        /* Fix for chart overflow */
        .apexcharts-inner {
            transform-origin: left center !important;
        }

        /* Mobile Chart Specific Styles */
        @media (max-width: 768px) {
            /* Chart Container */
            .chart-box {
                background: #f8fdf5 !important;
                border-radius: 12px !important;
                padding: 15px 10px !important;
                margin: 10px !important;
                box-shadow: 0 2px 6px rgba(0,0,0,0.05) !important;
            }

            /* Chart Title */
            .chart-title {
                font-size: 14px !important;
                font-weight: 600 !important;
                color: #1a5d1a !important;
                margin-bottom: 10px !important;
                text-align: left !important;
                padding: 0 5px !important;
            }

            /* Chart Area */
            .apexcharts-canvas {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }

            /* Y-axis Labels */
            .apexcharts-yaxis-label {
                font-size: 11px !important;
                font-weight: normal !important;
            }

            /* X-axis Labels */
            .apexcharts-xaxis-label {
                font-size: 11px !important;
                transform: rotate(-45deg) !important;
                transform-origin: right !important;
            }

            /* Bar Labels */
            .apexcharts-bar-area {
                stroke-width: 1px !important;
            }

            /* Data Labels */
            .apexcharts-datalabel {
                font-size: 10px !important;
                font-weight: 500 !important;
            }

            /* Legend */
            .apexcharts-legend {
                padding: 5px !important;
                font-size: 12px !important;
            }

            .apexcharts-legend-text {
                margin-left: 5px !important;
            }

            /* Grid Lines */
            .apexcharts-grid line {
                stroke-width: 0.5 !important;
                stroke-dasharray: 3 !important;
            }

            /* Chart Tooltip */
            .apexcharts-tooltip {
                font-size: 11px !important;
                padding: 5px 8px !important;
                background: rgba(255, 255, 255, 0.98) !important;
                box-shadow: 0 2px 6px rgba(0,0,0,0.1) !important;
            }

            /* Unit Names */
            .unit-name {
                font-size: 11px !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                max-width: 120px !important;
            }

            /* Value Display */
            .value-display {
                font-size: 12px !important;
                font-weight: 600 !important;
                color: #1a5d1a !important;
            }
        }

        /* Extra Small Devices */
        @media (max-width: 480px) {
            .chart-box {
                padding: 10px 5px !important;
                margin: 8px !important;
            }

            .chart-title {
                font-size: 13px !important;
            }

            .apexcharts-yaxis-label,
            .apexcharts-xaxis-label {
                font-size: 10px !important;
            }

            .unit-name {
                max-width: 100px !important;
                font-size: 10px !important;
            }
        }

        /* Live Data Table Styles */
        @media (max-width: 768px) {
            #live-data {
                width: 100% !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
            }

            #live-data table {
                min-width: 800px !important; /* Maintain desktop table width */
                width: 100% !important;
            }

            /* Maintain table styles */
            .table {
                border-collapse: collapse !important;
                width: 100% !important;
            }

            /* Ensure horizontal scroll is smooth */
            .table-responsive {
                -webkit-overflow-scrolling: touch !important;
                scrollbar-width: thin !important;
            }

            /* Hide scrollbar but maintain functionality */
            .table-responsive::-webkit-scrollbar {
                height: 6px !important;
            }

            .table-responsive::-webkit-scrollbar-thumb {
                background-color: rgba(0, 0, 0, 0.2) !important;
                border-radius: 3px !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile-specific chart options
            const mobileChartOptions = {
                chart: {
                    height: 'auto',
                    toolbar: {
                        show: false
                    },
                    animations: {
                        enabled: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: '80%',
                        distributed: true
                    }
                },
                dataLabels: {
                    enabled: true,
                    textAnchor: 'start',
                    style: {
                        fontSize: '11px'
                    },
                    formatter: function(val) {
                        return val.toFixed(2);
                    },
                    offsetX: 0
                },
                xaxis: {
                    labels: {
                        show: true,
                        style: {
                            fontSize: '11px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        show: true,
                        style: {
                            fontSize: '11px'
                        }
                    }
                },
                grid: {
                    xaxis: {
                        lines: {
                            show: true
                        }
                    },
                    yaxis: {
                        lines: {
                            show: false
                        }
                    }
                },
                legend: {
                    show: false
                }
            };

            // Apply mobile options only on mobile devices
            if (window.innerWidth <= 768) {
                if (typeof chart !== 'undefined') {
                    chart.updateOptions(mobileChartOptions);
                }
            }

            // Handle resize
            window.addEventListener('resize', function() {
                if (window.innerWidth <= 768) {
                    if (typeof chart !== 'undefined') {
                        chart.updateOptions(mobileChartOptions);
                    }
                }
            });
        });
    </script>
@endsection

@section('content')
    <!-- Include loader component -->
    @include('components.loader')

    <!-- Wrap content in transition div -->
    <div id="page-content" class="page-transition">
        <div class="w-full">
            <!-- Navbar -->
            <nav class="fixed w-full top-0 z-50">
                <div class="nav-background shadow-lg">
                    <div class="container mx-auto px-4">
                        <div class="flex justify-between items-center h-16">
                            <!-- Logo -->
                            <div class="flex items-center">
                                <a href="#" class="flex items-center">
                                    <img src="{{ asset('logo/navlogo.png') }}" alt="Logo" class="h-8">
                                </a>
                            </div>

                            <!-- Menu Desktop -->
                            <div class="hidden md:flex items-center ">
                                <ul class="flex space-x-8">
                                    <li><a href="#" class="nav-link">Home</a></li>
                                    <li><a href="#map" class="nav-link">Peta Pembangkit</a></li>
                                    {{-- <li><a href="#grafik" class="nav-link">Grafik Kinerja</a></li> --}}
                                    <li><a href="#live-data" class="nav-link">Live Data Unit Operasional</a></li>
                                    <li><a href="{{ route('dashboard.pemantauan') }}" class="nav-link">Dashboard Pemantauan</a></li>
                                    <!-- Tambah Menu Login -->
                                    <li>
                                        <a href="{{ route('login') }}" class="login-button">
                                            <i class="fas fa-user mr-2"></i> Login
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <!-- Menu Mobile -->
                            <div class="md:hidden">
                                <button id="mobile-menu-button" class="text-white">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Mobile Menu -->
                        <div id="mobile-menu" class="hidden md:hidden pb-4">
                            <ul class="space-y-4">
                                <li><a href="#" class="nav-link-mobile">Home</a></li>
                                <li><a href="#map" class="nav-link-mobile">Peta Pembangkit</a></li>
                                {{-- <li><a href="#grafik" class="nav-link-mobile">Grafik Kinerja</a></li> --}}
                                <li><a href="#live-data" class="nav-link-mobile">Live Data Unit Operasional</a></li>
                                <li><a href="{{ route('dashboard.pemantauan') }}" class="nav-link-mobile">Dashboard Pemantauan</a></li>
                                <!-- Tambah Menu Login di Mobile -->
                                <li>
                                    <a href="{{ route('login') }}" class="nav-link-mobile login-mobile">
                                        <i class="fas fa-user mr-2"></i> Login
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            <div class="h-[80px]"></div>
            <div class="w-full">
                {{-- Hero section --}}
                <div class="min-h-screen flex flex-col justify-center items-center hexagon-background">
                    <!-- Content wrapper -->
                    <div class="relative z-10 mt-16">
                        <!-- Header -->
                        <h2 class="text-9xl font-bold mb-8 text-center" style="color: #FFCC00; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); line-height: 1;">
                            M<span style="display: inline-flex; align-items: center;"><i class="fas fa-cog fa-spin" style="color: #333333;"></i></span>NDAY
                            <span class="text-6xl block mt-2" style="color: #FFCC00; font-weight: 600; letter-spacing: 2px;">
                                MONITORING DAILY
                                <span class="block text-3xl mt-1" style="color: #FFFFFF; letter-spacing: 1px;">UP KENDARI</span>
                            </span>
                        </h2>
                        <div class="flex gap-2 lg:gap-0 lg:grid grid-cols-2 lg:grid-cols-3">
                            <div>
                                <a href="{{ route('login', ['unit' => 'mysql_wua_wua']) }}" class="block">
                                    <div class="hexagon bg-[#0A749B] bg-opacity-55 flex flex-col items-center justify-center hover:bg-opacity-100 h-36 w-40 md:w-56 md:h-44">
                                        <h5 class="text-sm lg:text-2xl md:text-xl font-bold text-gray-50 text-center">ULPLTD <br> WUA-WUA</h5>
                                    </div>
                                </a>
                                <a href="{{ route('login', ['unit' => 'mysql_poasia']) }}" class="block">
                                    <div class="hexagon bg-[#0A749B] bg-opacity-55 flex flex-col items-center justify-center hover:bg-opacity-100 h-36 w-40 md:w-56 md:h-44">
                                        <h5 class="text-sm lg:text-2xl md:text-xl font-bold text-gray-50 text-center">ULPLTD <br> POASIA</h5>
                                    </div>
                                </a>
                            </div>
                            <div class="flex items-center justify-center">
                                <div class="hidden lg:block md:block">
                                    <a href="{{ route('login', ['unit' => 'mysql']) }}" class="block">
                                        <div class="hexagon bg-[#0A749B] flex flex-col items-center justify-center h-36 w-40 md:w-56 md:h-44">
                                            <h5 class="text-sm lg:text-2xl md:text-xl font-bold text-gray-50 text-center">UP <br> KENDARI</h5>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('login', ['unit' => 'mysql_kolaka']) }}" class="block">
                                    <div class="hexagon bg-[#0A749B] bg-opacity-55 flex flex-col items-center justify-center hover:bg-opacity-100 h-36 w-40 md:w-56 md:h-44 border">
                                        <h5 class="text-sm lg:text-2xl md:text-xl font-bold text-gray-50 text-center">ULPLTD <br> KOLAKA</h5>
                                    </div>
                                </a>
                                <a href="{{ route('login', ['unit' => 'mysql_bau_bau']) }}" class="block">
                                    <div class="hexagon bg-[#0A749B] bg-opacity-55 flex flex-col items-center justify-center hover:bg-opacity-100 h-36 w-40 md:w-56 md:h-44 border">
                                        <h5 class="text-sm lg:text-2xl md:text-xl font-bold text-gray-50 text-center">ULPLTD <br> BAU-BAU</h5>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Map --}}
            <h1 class="text-2xl font-semibold text-center mt-10  text-[#0A749B]">Peta Pembangkit</h1>
            <div id="map"
                style="height: 500px; border-radius: 20px; position: relative; margin: 30px 30px 0; padding: 0; "
                class="z-0">
            </div>

            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest/dist/apexcharts.min.js"></script>

            <div class="container mx-auto px-4 py-6">
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200" style="background-color: rgba(17, 24, 39, 0.08);">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 px-2">MONITORING KESIAPAN PEMBANGKIT</h2>
                        <!-- Tambahkan tombol switch periode -->
                        <div class="flex flex-col md:flex-row gap-2">
                            <button onclick="switchPeriod('daily')" 
                                    id="dailyBtn"
                                    class="period-btn bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 w-full md:w-auto">
                                <i class="fas fa-calendar-day mr-2"></i>Harian
                            </button>
                            <button onclick="switchPeriod('weekly')" 
                                    id="weeklyBtn"
                                    class="period-btn bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 w-full md:w-auto">
                                <i class="fas fa-calendar-week mr-2"></i>Mingguan
                            </button>
                            <button onclick="switchPeriod('monthly')" 
                                    id="monthlyBtn"
                                    class="period-btn bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 w-full md:w-auto">
                                <i class="fas fa-calendar-alt mr-2"></i>Bulanan
                            </button>
                        </div>
                    </div>
                    
                    <!-- Grid untuk diagram circle berdampingan -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <!-- Ring Progress Kesiapan Mesin -->
                        <div class="bg-gray-50 rounded-xl p-5 shadow-sm border border-gray-100">
                            <div class="text-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-700">Kesiapan Mesin</h3>
                                <p class="text-sm text-gray-500">Status operasional unit pembangkit</p>
                            </div>
                            <div id="machineReadinessChart" class="mx-auto" style="height: 200px;"></div>
                            
                            <!-- Detail status mesin -->
                            <div class="mt-4 space-y-2 text-sm">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <div class="flex justify-between items-center p-2 bg-green-50 rounded">
                                        <span class="text-green-700 text-xs sm:text-sm">Operasi</span>
                                        <span class="font-semibold text-green-800 text-xs sm:text-sm">{{ $chartData['statusDetails']['breakdown']['Operasi'] }} Unit</span>
                                    </div>
                                    <div class="flex justify-between items-center p-2 bg-blue-50 rounded">
                                        <span class="text-blue-700 text-xs sm:text-sm">Standby</span>
                                        <span class="font-semibold text-blue-800 text-xs sm:text-sm">{{ $chartData['statusDetails']['breakdown']['Standby'] }} Unit</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <div class="flex justify-between items-center p-2 bg-red-50 rounded">
                                        <span class="text-red-700 text-xs sm:text-sm">Gangguan</span>
                                        <span class="font-semibold text-red-800 text-xs sm:text-sm">{{ $chartData['statusDetails']['breakdown']['Gangguan'] }} Unit</span>
                                    </div>
                                    <div class="flex justify-between items-center p-2 bg-yellow-50 rounded">
                                        <span class="text-yellow-700 text-xs sm:text-sm">Pemeliharaan</span>
                                        <span class="font-semibold text-yellow-800 text-xs sm:text-sm">{{ $chartData['statusDetails']['breakdown']['Pemeliharaan'] }} Unit</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <div class="flex justify-between items-center p-2 bg-purple-50 rounded">
                                        <span class="text-purple-700 text-xs sm:text-sm">Mothballed</span>
                                        <span class="font-semibold text-purple-800 text-xs sm:text-sm">{{ $chartData['statusDetails']['breakdown']['Mothballed'] }} Unit</span>
                                    </div>
                                    <div class="flex justify-between items-center p-2 bg-orange-50 rounded">
                                        <span class="text-orange-700 text-xs sm:text-sm">Overhaul</span>
                                        <span class="font-semibold text-orange-800 text-xs sm:text-sm">{{ $chartData['statusDetails']['breakdown']['Overhaul'] }} Unit</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Ring Progress Beban Tersalur -->
                        <div class="bg-gray-50 rounded-xl p-5 shadow-sm border border-gray-100">
                            <div class="text-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-700">Kesiapan Daya</h3>
                                <p class="text-sm text-gray-500">Kapasitas daya yang tersedia</p>
                            </div>
                            <div id="powerDeliveryChart" class="mx-auto" style="height: 200px;"></div>
                            
                            <!-- Detail beban tersalur -->
                            <div class="mt-4 space-y-2 text-sm">
                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    <div class="flex justify-between items-center p-2 bg-green-50 rounded overflow-hidden">
                                        <span class="text-green-700 text-xs sm:text-sm truncate">Kesiapan Daya</span>
                                        <span class="font-semibold text-green-800 text-xs sm:text-sm ml-2 shrink-0">{{ number_format($chartData['powerDeliveryDetails']['delivered'], 1) }} MW</span>
                                    </div>
                                    <div class="flex justify-between items-center p-2 bg-red-50 rounded overflow-hidden">
                                        <span class="text-red-700 text-xs sm:text-sm truncate">Daya Tidak Siap</span>
                                        <span class="font-semibold text-red-800 text-xs sm:text-sm ml-2 shrink-0">{{ number_format($chartData['powerDeliveryDetails']['undelivered'], 1) }} MW</span>
                                    </div>
                                    <div class="flex justify-between items-center p-2 bg-blue-50 rounded overflow-hidden sm:col-span-2">
                                        <span class="text-blue-700 text-xs sm:text-sm truncate">Total Kapasitas</span>
                                        <span class="font-semibold text-blue-800 text-xs sm:text-sm ml-2 shrink-0">{{ number_format($chartData['powerDeliveryDetails']['total'], 1) }} MW</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Grafik Bar memanjang horizontal -->
                    <div class="bg-gray-50 rounded-xl p-3 sm:p-5 shadow-sm border border-gray-100">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-700 mb-2 sm:mb-4 text-center">Ketidak Siapan Daya Mesin Per Unit</h3>
                        <div id="unservedLoadChart-wrapper">
                            <div id="unservedLoadChart"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Live Data Unit Operasional -->
            <div class="flex justify-center items-center mt-10 mb-4 gap-4">
                <h3 class="text-xl font-semibold">Live Data Unit Operasional</h3>
                <div class="flex items-center text-gray-600">
                    <i class="far fa-clock mr-2"></i>
                    <span id="liveTime"></span>
                </div>
            </div>

            <div class="w-full flex justify-center flex-col items-center mb-5">
                <div id="live-data" class="bg-white border border-gray-300 rounded-lg p-4 w-4/5">
                    <div class="overflow-auto">
                        <table class="table table-striped table-bordered min-w-full">
                            <thead>
                                <tr>
                                    <th class="text-center">Nama Unit</th>
                                    <th class="text-center">Mesin</th>
                                    <th class="text-center">DMN</th>
                                    <th class="text-center">DMP</th>
                                    <th class="text-center">Beban</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Waktu Update</th>
                                </tr>
                            </thead>
                            <tbody id="unit-table-body">
                                @foreach($powerPlants as $plant)
                                    @foreach($plant->machines as $machine)
                                        @php
                                            // Ambil status log terbaru untuk mesin ini
                                            $latestStatus = $machine->statusLogs()
                                                ->latest('created_at')
                                                ->first();
                                            
                                            // Skip jika tidak ada status
                                            if (!$latestStatus) continue;

                                            // Definisikan status non-operasional
                                            $nonOperationalStatuses = ['Gangguan', 'Pemeliharaan', 'Mothballed', 'Overhaul'];
                                            
                                            // Skip jika status bukan non-operasional
                                            if (!in_array($latestStatus->status, $nonOperationalStatuses)) continue;

                                            // Set style berdasarkan status
                                            $statusStyle = match($latestStatus->status) {
                                                'Gangguan' => [
                                                    'bg' => '#FEE2E2',
                                                    'text' => '#DC2626',
                                                    'border' => '#FCA5A5',
                                                    'icon' => '⚠️'
                                                ],
                                                'Pemeliharaan' => [
                                                    'bg' => '#FEF3C7',
                                                    'text' => '#D97706',
                                                    'border' => '#FCD34D',
                                                    'icon' => '🔧'
                                                ],
                                                'Mothballed' => [
                                                    'bg' => '#E0F2FE',
                                                    'text' => '#0369A1',
                                                    'border' => '#7DD3FC',
                                                    'icon' => '🔒'
                                                ],
                                                'Overhaul' => [
                                                    'bg' => '#FFE4B5',
                                                    'text' => '#FF8C00',
                                                    'border' => '#FFB74D',
                                                    'icon' => '🔧'
                                                ],
                                                default => [
                                                    'bg' => '#F3F4F6',
                                                    'text' => '#6B7280',
                                                    'border' => '#D1D5DB',
                                                    'icon' => '❔'
                                                ]
                                            };
                                        @endphp
                                        <tr class="table-row">
                                            <td class="text-center">{{ $plant->name }}</td>
                                            <td class="text-center">{{ $machine->name }}</td>
                                            <td class="text-center">{{ number_format($latestStatus->dmn, 1) }}</td>
                                            <td class="text-center">{{ number_format($latestStatus->dmp, 1) }}</td>
                                            <td class="text-center">{{ number_format($latestStatus->load_value, 1) }} MW</td>
                                            <td class="text-center">
                                                <span style="
                                                    background: {{ $statusStyle['bg'] }}; 
                                                    color: {{ $statusStyle['text'] }}; 
                                                    padding: 4px 12px;
                                                    border-radius: 12px;
                                                    font-size: 0.85em;
                                                    border: 1px solid {{ $statusStyle['border'] }};
                                                ">
                                                    {{ $statusStyle['icon'] }} {{ $latestStatus->status }}
                                                </span>
                                            </td>
                                            <td class="text-center text-sm text-gray-500">
                                                {{ $latestStatus->created_at->format('d/m/Y H:i:s') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- </div> --}}

            <!-- Setelah section peta pembangkit -->
          

            <!-- Footer -->
            <footer class="footer w-full">
                <div class="content">
                    <div class="column">
                        <img src="{{ asset('logo/navlogo.png') }}" alt="Logo" style="height: 40px; margin-bottom: 10px">
                        <p>PLN Nusantara Power terdepan dan terpercaya dalam bisnis energi berkelanjutan di Asia Tenggara.</p>
                        <p>The foremost and reliable sustainable energy business on SEA.</p>
                        <div class="social-icons">
                            <!-- Tambahkan ikon media sosial -->
                            <a href="#"><i class="fab fa-youtube"></i></a>
                            <a href="#"><i class="fab fa-facebook"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                    <div class="column">
                        <h4>Kontak/Contact</h4>
                        <p>Jl. Chairil Anwar No. 01, Kendari, Sulawesi tenggara, Indonesia</p>
                        <p>Email: info@plnnusantarapower.co.id</p>
                        <p>Telepon: +62 31 8283180</p>
                    </div>
                    {{-- <div class="column">
                        <h4>Strategic Office</h4>
                        <p>18 Office Park, Lt.2 ABCD</p>
                        <p>Jl. TB Simatupang No.18, Jakarta Selatan, Indonesia</p>
                    </div> --}}
                    <div class="column">
                        <h4>Newsletter</h4>
                        <form>
                            <input type="email" placeholder="Email" required />
                            <button type="submit">Subscribe</button>
                        </form>
                    </div>
                </div>
                <div class="copyright">
                    Copyright © 2025 <a href="#">PT PLN Nusantara Power UP KENDARI</a>. All Rights Reserved.
                </div>
            </footer>

            <script>
                var options = {
                    series: [{
                            name: 'Total Kapasitas Listrik',
                            data: [{{ implode(',', $total_capacity_data) }}]
                        },
                        {
                            name: 'Total Unit Pembangkit',
                            data: [{{ implode(',', $total_units_data) }}]
                        },
                        {
                            name: 'Unit Pembangkit Aktif',
                            data: [{{ implode(',', $active_units_data) }}]
                        },
                        {
                            name: 'DMN',
                            data: [{{ implode(',', $dmn_data) }}]
                        },
                        {
                            name: 'DMP',
                            data: [{{ implode(',', $dmp_data) }}]
                        },
                        {
                            name: 'Beban',
                            data: [{{ implode(',', $load_value_data) }}]
                        },
                        {
                            name: 'Kapasitas Unit',
                            data: [{{ implode(',', $capacity_data) }}]
                        }
                    ],
                    chart: {
                        type: 'line',
                        height: 350
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: [3, 3, 3, 2, 2, 2, 2],
                        dashArray: [0, 0, 0, 0, 0, 0, 0]
                    },
                    xaxis: {
                        categories: ["{{ implode('","', $dates) }}"]
                    },
                    yaxis: [{
                        title: {
                            text: 'Nilai'
                        }
                    }],
                    title: {
                        text: 'Grafik Kinerja Pembangkit',
                        align: 'center'
                    },
                    colors: [
                        '#FF1E1E', // Merah untuk Total Kapasitas Listrik
                        '#00B050', // Hijau untuk Total Unit Pembangkit
                        '#0070C0', // Biru untuk Unit Pembangkit Aktif
                        '#7030A0', // Ungu untuk DMN
                        '#FFC000', // Kuning untuk DMP
                        '#ED7D31', // Oranye untuk Beban
                        '#4472C4' // Biru Tua untuk Kapasitas Unit
                    ],
                    tooltip: {
                        enabled: true,
                        y: {
                            formatter: function(value, {
                                seriesIndex
                            }) {
                                if (seriesIndex <= 2) return value + ' Unit';
                                if (seriesIndex === 6) return value + ' MW';
                                return value;
                            }
                        }
                    },
                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'center',
                        markers: {
                            width: 12,
                            height: 12,
                            strokeWidth: 0,
                            radius: 12,
                            offsetX: 0,
                            offsetY: 0
                        },
                        itemMargin: {
                            horizontal: 15,
                            vertical: 8
                        }
                    }
                };

                var chart = new ApexCharts(document.querySelector("#line-chart"), options);
                chart.render();

                var map = L.map('map', {
                    zoomControl: true,
                    scrollWheelZoom: true,
                    doubleClickZoom: true,
                    dragging: true,
                }).setView([-4.0435, 122.4972], 8);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                // Tambahkan custom info control
                var info = L.control({position: 'bottomleft'});

                info.onAdd = function (map) {
                    this._div = L.DomUtil.create('div', 'map-info');
                    this._div.innerHTML = `
                        <div style="
                            background: rgba(255, 255, 255, 0.9);
                            padding: 10px;
                            border-radius: 5px;
                            box-shadow: 0 1px 5px rgba(0,0,0,0.2);
                            font-size: 13px;
                            max-width: 200px;
                        ">
                            <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                <span style="color: #0095B7; margin-right: 5px;">ℹ️</span>
                                <span style="font-weight: bold; color: #0095B7;">Petunjuk Peta</span>
                            </div>
                            <div style="color: #4B5563; line-height: 1.4;">
                                Klik marker <img src="{{ asset('images/marker-icon.png') }}" alt="marker" style="height: 16px; display: inline; vertical-align: middle;"> untuk melihat informasi detail unit pembangkit
                            </div>
                            <div style="margin-top: 8px; font-size: 12px; color: #6B7280;">
                                Status Unit:
                                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 4px; margin-top: 4px;">
                                    <div style="display: flex; align-items: center;">
                                        <span style="
                                            display: inline-block;
                                            width: 8px;
                                            height: 8px;
                                            border-radius: 50%;
                                            background: #059669;
                                            margin-right: 4px;
                                        "></span>
                                        <span>Operasi</span>
                                    </div>
                                    <div style="display: flex; align-items: center;">
                                        <span style="
                                            display: inline-block;
                                            width: 8px;
                                            height: 8px;
                                            border-radius: 50%;
                                            background: #6B7280;
                                            margin-right: 4px;
                                        "></span>
                                        <span>Standby</span>
                                    </div>
                                    <div style="display: flex; align-items: center;">
                                        <span style="
                                            display: inline-block;
                                            width: 8px;
                                            height: 8px;
                                            border-radius: 50%;
                                            background: #DC2626;
                                            margin-right: 4px;
                                        "></span>
                                        <span>Gangguan</span>
                                    </div>
                                    <div style="display: flex; align-items: center;">
                                        <span style="
                                            display: inline-block;
                                            width: 8px;
                                            height: 8px;
                                            border-radius: 50%;
                                            background: #0369A1;
                                            margin-right: 4px;
                                        "></span>
                                        <span>Pemeliharaan</span>
                                    </div>
                                    <div style="display: flex; align-items: center;">
                                        <span style="
                                            display: inline-block;
                                            width: 8px;
                                            height: 8px;
                                            border-radius: 50%;
                                            background: #D97706;
                                            margin-right: 4px;
                                        "></span>
                                        <span>Mothballed</span>
                                    </div>
                                    <div style="display: flex; align-items: center;">
                                        <span style="
                                            display: inline-block;
                                            width: 8px;
                                            height: 8px;
                                            border-radius: 50%;
                                            background: #FF8C00;
                                            margin-right: 4px;
                                        "></span>
                                        <span>Overhaul</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    return this._div;
                };

                info.addTo(map);

                // Buat array untuk menyimpan semua koordinat marker
                var markers = [];
                var bounds = L.latLngBounds();

                @php
                    use App\Models\MachineStatusLog;
                    use Carbon\Carbon;
                @endphp

                @foreach ($powerPlants as $plant)
                    var markerLatLng = [{{ $plant->latitude }}, {{ $plant->longitude }}];
                    var marker = L.marker(markerLatLng)
                        .addTo(map)
                        .bindPopup(`
                            <div style="min-width: 350px;">
                                <h3 style="margin: 0 0 10px 0; text-align: center; color: #0095B7;">{{ $plant->name }}</h3>
                                
                                <!-- Info Derating dan HOP -->
                                <div style="display: flex; justify-content: center; gap: 15px; margin-bottom: 10px; font-size: 0.9em;">
                                    @php
                                        $totalDMN = $plant->machines->sum(function($machine) {
                                            return $machine->statusLogs->first()->dmn ?? 0;
                                        });
                                        $totalDMP = $plant->machines->sum(function($machine) {
                                            return $machine->statusLogs->first()->dmp ?? 0;
                                        });
                                        $totalDerating = $totalDMN + $totalDMP;
                                        $totalCapacity = $plant->machines->sum('capacity');
                                        $deratingPercentage = $totalCapacity > 0 ? round(($totalDerating / $totalCapacity) * 100, 2) : 0;
                                    @endphp
                                    <div style="
                                        background: #FEF3C7; 
                                        color: #D97706; 
                                        padding: 4px 10px; 
                                        border-radius: 8px; 
                                        border: 1px solid #FCD34D;
                                        font-weight: 600;
                                        display: flex;
                                        align-items: center;
                                        gap: 5px;
                                    ">
                                        <span>Derating:</span>
                                        <span>{{ $totalDerating }} MW</span>
                                        <span>({{ $deratingPercentage }}%)</span>
                                    </div>
                                    <div style="
                                        background: #E0F2FE; 
                                        color: #0369A1; 
                                        padding: 4px 10px; 
                                        border-radius: 8px; 
                                        border: 1px solid #7DD3FC;
                                        font-weight: 600;
                                    ">
                                        HOP: {{ $plant->hop ?? 0 }}
                                    </div>
                                </div>

                                <!-- Status Unit Summary -->
                                @php
                                    $unitOperasi = $plant->machines->filter(function($machine) {
                                        $latestStatus = $machine->statusLogs->first();
                                        return $latestStatus && $latestStatus->status === 'Operasi';
                                    })->count();

                                    $unitStandby = $plant->machines->filter(function($machine) {
                                        $latestStatus = $machine->statusLogs->first();
                                        return $latestStatus && $latestStatus->status === 'Standby';
                                    })->count();

                                    $unitAktif = $unitOperasi + $unitStandby; // Gabungan Operasi dan Standby

                                    $unitTidakSiap = $plant->machines->filter(function($machine) {
                                        $latestStatus = $machine->statusLogs->first();
                                        return $latestStatus && in_array($latestStatus->status, [
                                            'Gangguan', 
                                            'Pemeliharaan', 
                                            'Mothballed', 
                                            'Overhaul'
                                        ]);
                                    })->count();
                                @endphp
                                
                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 15px;">
                                    <div style="background: #D1FAE5; padding: 8px; border-radius: 8px; text-align: center; border: 1px solid #6EE7B7;">
                                        <div style="font-size: 0.8em; color: #059669;">Unit Aktif</div>
                                        <div style="font-weight: bold; color: #059669; font-size: 1.2em;">{{ $unitAktif }}</div>
                                        <div style="font-size: 0.7em; color: #059669;">
                                            ({{ $unitOperasi }} Operasi, {{ $unitStandby }} Standby)
                                        </div>
                                    </div>
                                    <div style="background: #FEE2E2; padding: 8px; border-radius: 8px; text-align: center; border: 1px solid #FCA5A5;">
                                        <div style="font-size: 0.8em; color: #DC2626;">Tidak Siap Operasi</div>
                                        <div style="font-weight: bold; color: #DC2626; font-size: 1.2em;">{{ $unitTidakSiap }}</div>
                                    </div>
                                    <div style="background: #F3F4F6; padding: 8px; border-radius: 8px; text-align: center; border: 1px solid #D1D5DB;">
                                        <div style="font-size: 0.8em; color: #6B7280;">Total Unit</div>
                                        <div style="font-weight: bold; color: #6B7280; font-size: 1.2em;">{{ $plant->machines->count() }}</div>
                                    </div>
                                </div>

                                <!-- Info Grid Utama -->
                                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 15px;">
                                    <div style="background: #f8f9fa; padding: 8px; border-radius: 5px; text-align: center;">
                                        <div style="font-weight: bold; color: #0095B7;">Total Mesin</div>
                                        <div>{{ $plant->machines->count() }}</div>
                                    </div>
                                    <div style="background: #f8f9fa; padding: 8px; border-radius: 5px; text-align: center;">
                                        <div style="font-weight: bold; color: #0095B7;">Mesin Aktif</div>
                                        <div>{{ $plant->machines->where('status', 'Aktif')->count() }}</div>
                                    </div>
                                </div>

                                <!-- Status Mesin Grid -->
                                <div style="margin: 10px 0;">
                                    <h4 style="color: #0095B7; margin: 5px 0;">Status Mesin</h4>
                                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; max-height: 200px; overflow-y: auto;">
                                        @foreach($plant->machines as $machine)
                                            @php
                                                $latestStatus = $machine->statusLogs->first();
                                                // Tentukan warna background dan teks berdasarkan status
                                                $statusStyle = match($latestStatus->status ?? '') {
                                                    'Gangguan' => [
                                                        'bg' => '#FEE2E2',
                                                        'text' => '#DC2626',
                                                        'border' => '#FCA5A5',
                                                        'icon' => '⚠️'
                                                    ],
                                                    'Mothballed' => [
                                                        'bg' => '#FEF3C7',
                                                        'text' => '#D97706',
                                                        'border' => '#FCD34D',
                                                        'icon' => '🔒'
                                                    ],
                                                    'Overhaul' => [
                                                        'bg' => '#FFE4B5',
                                                        'text' => '#FF8C00',
                                                        'border' => '#FFB74D',
                                                        'icon' => '🔧'
                                                    ],
                                                    'Pemeliharaan' => [
                                                        'bg' => '#E0F2FE',
                                                        'text' => '#0369A1',
                                                        'border' => '#7DD3FC',
                                                        'icon' => '🔨'
                                                    ],
                                                    'Standby' => [
                                                        'bg' => '#F3F4F6',
                                                        'text' => '#6B7280',
                                                        'border' => '#D1D5DB',
                                                        'icon' => '⏸️'
                                                    ],
                                                    'Operasi' => [
                                                        'bg' => '#D1FAE5',
                                                        'text' => '#059669',
                                                        'border' => '#6EE7B7',
                                                        'icon' => '✅'
                                                    ],
                                                    default => [
                                                        'bg' => '#F3F4F6',
                                                        'text' => '#6B7280',
                                                        'border' => '#D1D5DB',
                                                        'icon' => '❔'
                                                    ]
                                                };
                                            @endphp
                                            <div style="background: #fff; padding: 8px; border-radius: 5px; border: 1px solid {{ $statusStyle['border'] }};">
                                                <div style="font-weight: bold; color: {{ $statusStyle['text'] }};">{{ $machine->name }}</div>
                                                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 5px; font-size: 0.9em;">
                                                    <div>
                                                        <span style="color: #666;">Status:</span>
                                                        <span style="
                                                            background: {{ $statusStyle['bg'] }}; 
                                                            color: {{ $statusStyle['text'] }}; 
                                                            padding: 2px 8px;
                                                            border-radius: 12px;
                                                            font-size: 0.85em;
                                                            border: 1px solid {{ $statusStyle['border'] }};
                                                            display: inline-block;
                                                            margin-top: 2px;
                                                        ">
                                                            {{ $statusStyle['icon'] }} {{ $latestStatus ? $latestStatus->status : 'N/A' }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span style="color: #666;">Beban:</span>
                                                        <span style="color: {{ $statusStyle['text'] }}; font-weight: 600;">
                                                            {{ $latestStatus ? $latestStatus->load_value : 0 }} MW
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span style="color: #666;">DMN:</span>
                                                        <span style="color: {{ $statusStyle['text'] }}; font-weight: 600;">
                                                            {{ $latestStatus ? $latestStatus->dmn : 0 }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span style="color: #666;">DMP:</span>
                                                        <span style="color: {{ $statusStyle['text'] }}; font-weight: 600;">
                                                            {{ $latestStatus ? $latestStatus->dmp : 0 }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Grafik Trend -->
                                <div style="margin: 15px 0;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                        <div style="font-weight: bold; color: #0095B7;">Trend 7 Hari Terakhir</div>
                                        <div class="chart-legend" style="font-size: 12px;">
                                            <span style="color: #0095B7;">● Beban</span>
                                            <span style="color: #FF4560; margin-left: 10px;">● Kapasitas</span>
                                        </div>
                                    </div>
                                    <div id="chart-{{ $plant->id }}" style="height: 200px;"></div>
                                </div>

                                <button onclick="showAccumulationData({{ $plant->id }})" 
                                        style="background: #0095B7; color: white; border: none; 
                                               padding: 8px 15px; border-radius: 5px; 
                                               cursor: pointer; width: 100%; margin-top: 10px;">
                                    Lihat Detail Lengkap
                                </button>
                            </div>
                        `, {
                            maxWidth: 600,
                            maxHeight: 400
                        });

                    // Event listener saat popup dibuka
                    marker.on('popupopen', function() {
                        setTimeout(() => {
                            createLineChart({{ $plant->id }});
                        }, 100);
                    });
                @endforeach

                function createLineChart(plantId) {
                    const chartContainer = document.querySelector("#chart-" + plantId);
                    
                    // Tampilkan loading state
                    chartContainer.innerHTML = '<div style="text-align: center; padding: 20px;">Loading chart data...</div>';

                    // Gunakan route() helper dari Laravel untuk membuat URL yang benar
                    const url = "{{ route('plant.chart.data', ['plantId' => ':plantId']) }}".replace(':plantId', plantId);

                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error(text || 'Network response was not ok');
                                });
                            }
                            return response.json();
                        })
                        .then(chartData => {
                            console.log('Chart Data:', chartData); // Debug log

                            if (!chartData.dates || !chartData.beban || !chartData.kapasitas ||
                                chartData.dates.length === 0) {
                                throw new Error('No data available for this period');
                            }

                            var options = {
                                series: [{
                                    name: 'Beban',
                                    data: chartData.beban
                                }, {
                                    name: 'Kapasitas',
                                    data: chartData.kapasitas
                                }],
                                chart: {
                                    type: 'line',
                                    height: 200,
                                    toolbar: {
                                        show: false
                                    },
                                    animations: {
                                        enabled: true,
                                        easing: 'easeinout',
                                        speed: 800
                                    }
                                },
                                stroke: {
                                    curve: 'smooth',
                                    width: [3, 2]
                                },
                                colors: ['#0095B7', '#FF4560'],
                                xaxis: {
                                    categories: chartData.dates,
                                    labels: {
                                        style: {
                                            fontSize: '10px'
                                        },
                                        rotate: -45,
                                        rotateAlways: false
                                    }
                                },
                                yaxis: {
                                    title: {
                                        text: 'MW'
                                    },
                                    labels: {
                                        formatter: function(val) {
                                            return val.toFixed(1);
                                        }
                                    }
                                },
                                tooltip: {
                                    shared: true,
                                    intersect: false,
                                    followCursor: true,
                                    custom: function({ series, seriesIndex, dataPointIndex, w }) {
                                        let content = `
                                            <div class="custom-tooltip" style="padding: 10px; background: rgba(255, 255, 255, 0.95); border: 1px solid #e2e8f0; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                <div style="font-weight: bold; margin-bottom: 8px; color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">
                                                    ${chartData.dates[dataPointIndex]}
                                                </div>
                                                <div style="max-height: 200px; overflow-y: auto;">
                                        `;

                                        let total = 0;
                                        series.forEach((value, index) => {
                                            if (value[dataPointIndex] > 0) {
                                                const name = w.globals.seriesNames[index];
                                                const val = value[dataPointIndex].toFixed(2);
                                                total += parseFloat(val);
                                                const color = w.globals.colors[index];
                                                
                                                content += `
                                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; padding: 3px 0;">
                                                        <div style="display: flex; align-items: center;">
                                                            <span style="display: inline-block; width: 8px; height: 8px; background: ${color}; margin-right: 8px; border-radius: 50%;"></span>
                                                            <span style="color: #475569;">${name}:</span>
                                                        </div>
                                                        <span style="font-weight: 600; color: #1e293b;">${val} MW</span>
                                                    </div>
                                                `;
                                            }
                                        });

                                        if (total > 0) {
                                            content += `
                                                <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #e2e8f0;">
                                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                                        <span style="font-weight: 600; color: #1e293b;">Total:</span>
                                                        <span style="font-weight: 700; color: #1e293b;">${total.toFixed(2)} MW</span>
                                                    </div>
                                                </div>
                                            `;
                                        }

                                        content += `
                                                </div>
                                            </div>
                                        `;

                                        return content;
                                    },
                                    style: {
                                        fontSize: '12px'
                                    },
                                    onDatasetHover: {
                                        highlightDataSeries: true,
                                    },
                                    y: {
                                        formatter: function(value) {
                                            return value.toFixed(2) + ' MW';
                                        }
                                    }
                                },
                                legend: {
                                    position: 'top',
                                    horizontalAlign: 'right',
                                    markers: {
                                        width: 8,
                                        height: 8,
                                        radius: 12
                                    }
                                },
                                grid: {
                                    borderColor: '#e2e8f0',
                                    strokeDashArray: 4,
                                    padding: {
                                        top: 0,
                                        right: 20,
                                        bottom: 0,
                                        left: 20
                                    }
                                },
                                noData: {
                                    text: 'No data available',
                                    align: 'center',
                                    verticalAlign: 'middle'
                                }
                            };

                            // Hapus loading state
                            chartContainer.innerHTML = '';
                            
                            var chart = new ApexCharts(chartContainer, options);
                            chart.render();
                        })
                        .catch(error => {
                            console.error('Error details:', error);
                            chartContainer.innerHTML = `
                                <div style="text-align: center; padding: 20px; color: #DC2626;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>Error loading chart data</div>
                                    <div style="font-size: 0.8em; margin-top: 5px;">${error.message}</div>
                                </div>
                            `;
                        });
                }

                function createDonutChart(type, markerId, value) {
                    var options = {
                        series: [value],
                        chart: {
                            type: 'radialBar',
                            height: 120,
                            width: 120
                        },
                        plotOptions: {
                            radialBar: {
                                hollow: {
                                    size: '70%'
                                },
                                dataLabels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        fontSize: '14px',
                                        offsetY: -10
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '16px',
                                        offsetY: 2,
                                        formatter: function(val) {
                                            return val + '%';
                                        }
                                    }
                                }
                            }
                        },
                        labels: [type.toUpperCase()],
                        colors: [type === 'dmn' ? '#0095B7' : '#FF4560']
                    };

                    var chart = new ApexCharts(document.querySelector(`#${type}-chart-${markerId}`), options);
                    chart.render();
                }

                document.addEventListener('DOMContentLoaded', function() {
                    const navLinks = document.querySelectorAll('.nav-link');
                    const sections = {
                        'home': 0, // Untuk home, gunakan posisi 0
                        'map': document.querySelector('#map'),
                        'grafik': document.querySelector('#line-chart'),
                        'live-data': document.querySelector('#live-data')
                    };

                    
                    // Fungsi untuk smooth scroll
                    navLinks.forEach(link => {
                        link.addEventListener('click', function(e) {
                            if (this.getAttribute('href').startsWith('#')) {
                                e.preventDefault();
                                const targetId = this.getAttribute('href').substring(1); // Hapus karakter '#'
                                const targetElement = sections[targetId];
                                
                                if (targetElement) {
                                    // Hapus kelas active dari semua link
                                    navLinks.forEach(link => link.classList.remove('active'));
                                    
                                    // Tambah kelas active ke link yang diklik
                                    this.classList.add('active');

                                    // Jika home, scroll ke atas
                                    if (targetId === 'home') {
                                        window.scrollTo({
                                            top: 0,
                                            behavior: 'smooth'
                                        });
                                    } else {
                                        // Smooth scroll ke target dengan offset
                                        const headerOffset = 100; // Sesuaikan dengan tinggi navbar
                                        const elementPosition = targetElement.getBoundingClientRect().top;
                                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                                        window.scrollTo({
                                            top: offsetPosition,
                                            behavior: 'smooth'
                                        });
                                    }
                                }
                            }
                        });
                    });

                    // Update active state berdasarkan posisi scroll
                    function updateActiveLink() {
                        const scrollPosition = window.scrollY;
                        
                        // Cek untuk home section
                        if (scrollPosition < 100) { // Sesuaikan dengan kebutuhan
                            navLinks.forEach(link => link.classList.remove('active'));
                            document.querySelector('a[href="#"]').classList.add('active');
                            return;
                        }

                        // Cek untuk section lainnya
                        Object.entries(sections).forEach(([id, element]) => {
                            if (element && id !== 'home') {
                                const rect = element.getBoundingClientRect();
                                const elementTop = rect.top + window.pageYOffset;
                                const elementBottom = elementTop + rect.height;
                                
                                if (scrollPosition >= elementTop - 200 && scrollPosition < elementBottom) {
                                    navLinks.forEach(link => link.classList.remove('active'));
                                    document.querySelector(`a[href="#${id}"]`).classList.add('active');
                                }
                            }
                        });
                    }

                    // Tambahkan event listener untuk scroll
                    window.addEventListener('scroll', updateActiveLink);
                    
                    // Panggil updateActiveLink saat halaman dimuat
                    updateActiveLink();
                });

                // Tambahkan fungsi untuk update waktu
                function updateLiveTime() {
                    const now = new Date();
                    const options = { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: false
                    };
                    document.getElementById('liveTime').textContent = now.toLocaleDateString('id-ID', options);
                }

                // Update waktu setiap detik
                setInterval(updateLiveTime, 1000);
                // Panggil sekali saat halaman dimuat
                updateLiveTime();

                // Mobile menu functionality
                const mobileMenuButton = document.getElementById('mobile-menu-button');
                const mobileMenu = document.getElementById('mobile-menu');

                mobileMenuButton.addEventListener('click', function() {
                    // Toggle menu visibility
                    mobileMenu.classList.toggle('hidden');
                    
                    // Optional: Add slide animation
                    if (!mobileMenu.classList.contains('hidden')) {
                        mobileMenu.style.maxHeight = mobileMenu.scrollHeight + 'px';
                    } else {
                        mobileMenu.style.maxHeight = '0';
                    }
                });

                // Close mobile menu when clicking menu items
                const mobileMenuItems = mobileMenu.querySelectorAll('a');
                mobileMenuItems.forEach(item => {
                    item.addEventListener('click', () => {
                        mobileMenu.classList.add('hidden');
                        mobileMenu.style.maxHeight = '0';
                    });
                });

                // Close mobile menu when clicking outside
                document.addEventListener('click', function(event) {
                    const isClickInsideMenu = mobileMenu.contains(event.target);
                    const isClickOnButton = mobileMenuButton.contains(event.target);
                    
                    if (!isClickInsideMenu && !isClickOnButton && !mobileMenu.classList.contains('hidden')) {
                        mobileMenu.classList.add('hidden');
                        mobileMenu.style.maxHeight = '0';
                    }
                });
            </script>
            @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const chartData = @json($chartData);
                
                // Konfigurasi grafik bar
                const barOptions = {
                    series: chartData.datasets,
                    chart: {
                        type: 'bar',
                        height: 450,
                        stacked: true,
                        toolbar: {
                            show: true,
                            tools: {
                                download: true,
                                selection: false,
                                zoom: false,
                                zoomin: false,
                                zoomout: false,
                                pan: false,
                            }
                        },
                        background: 'transparent'
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            borderRadius: 4,
                            barHeight: '70%',
                            dataLabels: {
                                total: {
                                    enabled: true,
                                    offsetX: 10,
                                    style: {
                                        fontSize: '13px',
                                        fontWeight: 600
                                    }
                                }
                            }
                        },
                    },
                    colors: ['#0284c7', '#0891b2', '#0d9488', '#059669', '#65a30d', '#92400e'],
                    dataLabels: {
                        enabled: false  // Nonaktifkan dataLabels default
                    },
                    xaxis: {
                        categories: chartData.dates,
                        labels: {
                            style: {
                                fontSize: '12px',
                                fontWeight: 500
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Tanggal',
                            style: {
                                fontSize: '13px',
                                fontWeight: 500
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        shared: true,
                        intersect: false,
                        followCursor: true,
                        custom: function({ series, seriesIndex, dataPointIndex, w }) {
                            let content = `
                                <div class="custom-tooltip" style="padding: 10px; background: rgba(255, 255, 255, 0.95); border: 1px solid #e2e8f0; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    <div style="font-weight: bold; margin-bottom: 8px; color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">
                                        ${chartData.dates[dataPointIndex]}
                                    </div>
                                    <div style="max-height: 200px; overflow-y: auto;">
                            `;

                            let total = 0;
                            series.forEach((value, index) => {
                                if (value[dataPointIndex] > 0) {
                                    const name = w.globals.seriesNames[index];
                                    const val = value[dataPointIndex].toFixed(2);
                                    total += parseFloat(val);
                                    const color = w.globals.colors[index];
                                    
                                    content += `
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; padding: 3px 0;">
                                            <div style="display: flex; align-items: center;">
                                                <span style="display: inline-block; width: 8px; height: 8px; background: ${color}; margin-right: 8px; border-radius: 50%;"></span>
                                                <span style="color: #475569;">${name}:</span>
                                            </div>
                                            <span style="font-weight: 600; color: #1e293b;">${val} MW</span>
                                        </div>
                                    `;
                                }
                            });

                            if (total > 0) {
                                content += `
                                    <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #e2e8f0;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <span style="font-weight: 600; color: #1e293b;">Total:</span>
                                            <span style="font-weight: 700; color: #1e293b;">${total.toFixed(2)} MW</span>
                                        </div>
                                    </div>
                                `;
                            }

                            content += `
                                    </div>
                                </div>
                            `;

                            return content;
                        },
                        style: {
                            fontSize: '12px'
                        },
                        onDatasetHover: {
                            highlightDataSeries: true,
                        },
                        y: {
                            formatter: function(value) {
                                return value.toFixed(2) + ' MW';
                            }
                        }
                    },
                    legend: {
                        position: 'right',
                        offsetY: 40,
                        markers: {
                            width: 12,
                            height: 12,
                            radius: 6
                        },
                        itemMargin: {
                            horizontal: 10,
                            vertical: 5
                        }
                    },
                    grid: {
                        show: true,
                        borderColor: '#e2e8f0',
                        strokeDashArray: 4,
                        padding: {
                            top: 0,
                            right: 20,
                            bottom: 0,
                            left: 20
                        }
                    }
                };
                
                // Konfigurasi ring progress tetap sama
                const readinessOptions = {
                    series: [chartData.machineReadiness],
                    chart: {
                        height: 180,
                        type: 'radialBar',
                        background: 'transparent',
                        offsetY: -10 // Sesuaikan offset untuk posisi yang lebih baik
                    },
                    plotOptions: {
                        radialBar: {
                            hollow: {
                                size: '75%',
                                margin: 15, // Tambahkan margin
                                background: '#fff',
                            },
                            track: {
                                background: '#e2e8f0',
                                strokeWidth: '97%',
                            },
                            dataLabels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '14px',
                                    fontWeight: 600,
                                    color: '#475569',
                                    offsetY: 30, // Sesuaikan posisi label nama
                                },
                                value: {
                                    show: true,
                                    fontSize: '24px',
                                    fontWeight: 700,
                                    color: '#0284c7',
                                    offsetY: -10, // Sesuaikan posisi label nilai
                                    formatter: function(val) {
                                        return val + '%';
                                    }
                                },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    fontSize: '14px',
                                    fontWeight: 600,
                                    color: '#475569',
                                    formatter: function(w) {
                                        return chartData.machineReadiness + '%';
                                    }
                                }
                            }
                        }
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'dark',
                            type: 'horizontal',
                            shadeIntensity: 0.5,
                            gradientToColors: ['#0284c7'],
                            stops: [0, 100]
                        }
                    },
                    stroke: {
                        lineCap: 'round',
                        dashArray: 0
                    },
                    labels: ['Kesiapan'],
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                height: 150
                            }
                        }
                    }]
                };
                
                const powerDeliveryOptions = {
                    ...readinessOptions,
                    series: [chartData.powerDeliveryPercentage],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            gradientToColors: ['#059669']
                        }
                    },
                    plotOptions: {
                        ...readinessOptions.plotOptions,
                        radialBar: {
                            ...readinessOptions.plotOptions.radialBar,
                            dataLabels: {
                                ...readinessOptions.plotOptions.radialBar.dataLabels,
                                value: {
                                    ...readinessOptions.plotOptions.radialBar.dataLabels.value,
                                    color: '#059669',
                                    formatter: function(val) {
                                        return val + '%';
                                    }
                                },
                                total: {
                                    ...readinessOptions.plotOptions.radialBar.dataLabels.total,
                                    formatter: function(w) {
                                        return chartData.powerDeliveryPercentage + '%';
                                    }
                                }
                            }
                        }
                    },
                    labels: ['Tersalur']
                };
                
                // Render charts
                const charts = {
                    bar: new ApexCharts(document.querySelector("#unservedLoadChart"), barOptions),
                    readiness: new ApexCharts(document.querySelector("#machineReadinessChart"), readinessOptions),
                    power: new ApexCharts(document.querySelector("#powerDeliveryChart"), powerDeliveryOptions)
                };
                
                Object.values(charts).forEach(chart => chart.render());
            });
            </script>
            @endpush
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loader = document.getElementById('loader');
        const pageContent = document.getElementById('page-content');
        
        // Show loader initially
        loader.classList.remove('loader-hidden');
        
        // Hide loader and show content when page is fully loaded
        window.addEventListener('load', function() {
            setTimeout(() => {
                loader.classList.add('loader-hidden');
                pageContent.classList.add('page-visible');
            }, 500);
        });

        // Add loader for navigation to dashboard
        document.querySelectorAll('a[href*="dashboard"]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                
                // Show loader
                loader.classList.remove('loader-hidden');
                
                // Navigate after small delay
                setTimeout(() => {
                    window.location.href = href;
                }, 300);
            });
        });
    });
</script>
@endsection

<script>
let currentPeriod = 'daily';
let charts = {};

function switchPeriod(period) {
    if (currentPeriod === period) return;
    
    // Update button styles
    document.querySelectorAll('.period-btn').forEach(btn => {
        btn.classList.replace('bg-blue-500', 'bg-gray-500');
        btn.classList.replace('hover:bg-blue-600', 'hover:bg-gray-600');
    });
    document.getElementById(`${period}Btn`).classList.replace('bg-gray-500', 'bg-blue-500');
    document.getElementById(`${period}Btn`).classList.replace('hover:bg-gray-600', 'hover:bg-blue-600');
    
    // Show loading state
    showLoading();
    
    // Gunakan URL yang benar sesuai dengan base URL aplikasi
    const baseUrl = window.location.origin; // Mendapatkan base URL dinamis
    
    // Fetch new data
    fetch(`${baseUrl}/monitoring-data/${period}`)
        .then(async response => {
            const contentType = response.headers.get('content-type');
            
            if (!response.ok) {
                let errorMessage = '';
                if (response.status === 404) {
                    errorMessage = `Endpoint tidak ditemukan (404)\n\nURL: ${baseUrl}/monitoring-data/${period}\n\nMohon periksa konfigurasi route di server.`;
                } else {
                    errorMessage = `Status: ${response.status}`;
                    try {
                        const responseText = await response.text();
                        errorMessage += `\n\nResponse:\n${responseText.substring(0, 200)}...`;
                    } catch (e) {
                        errorMessage += '\n\nTidak dapat membaca response body';
                    }
                }
                throw new Error(errorMessage);
            }

            if (!contentType || !contentType.includes('application/json')) {
                throw new Error(`Invalid response type: ${contentType || 'unknown'}`);
            }

            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            updateCharts(data);
            hideLoading();
        })
        .catch(error => {
            console.error('Error:', error);
            hideLoading();
            
            // Tampilkan error detail dengan SweetAlert2
            Swal.fire({
                icon: 'error',
                title: 'Gagal memuat data',
                html: `
                    <div class="text-left">
                        <p class="font-bold mb-2">Detail Error:</p>
                        <pre class="bg-gray-100 p-3 rounded text-sm overflow-auto max-h-60" style="white-space: pre-wrap;">
${error.message}
                        </pre>
                        <p class="mt-2 text-sm text-gray-600">
                            Waktu: ${new Date().toLocaleString()}
                        </p>
                        <p class="mt-2 text-sm text-gray-600">
                            Environment: ${process.env.NODE_ENV || 'production'}
                        </p>
                    </div>
                `,
                confirmButtonText: 'Tutup',
                customClass: {
                    container: 'error-modal',
                    popup: 'error-popup',
                    content: 'error-content'
                },
                width: '600px'
            });
        });
    
    currentPeriod = period;
}

// Tambahkan style untuk error modal
const style = document.createElement('style');
style.textContent = `
    .error-modal .error-popup {
        font-family: 'Arial', sans-serif;
    }
    .error-modal .error-content {
        text-align: left;
    }
    .error-modal pre {
        margin: 10px 0;
        font-family: monospace;
        font-size: 12px;
        line-height: 1.4;
    }
`;
document.head.appendChild(style);

function showLoading() {
    // Tambahkan overlay loading di atas charts
    const charts = document.querySelectorAll('#machineReadinessChart, #powerDeliveryChart, #unservedLoadChart');
    charts.forEach(chart => {
        chart.style.opacity = '0.5';
        chart.insertAdjacentHTML('beforeend', `
            <div class="loading-overlay flex items-center justify-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            </div>
        `);
    });
}

function hideLoading() {
    // Hapus overlay loading
    document.querySelectorAll('.loading-overlay').forEach(overlay => overlay.remove());
    const charts = document.querySelectorAll('#machineReadinessChart, #powerDeliveryChart, #unservedLoadChart');
    charts.forEach(chart => chart.style.opacity = '1');
}

function updateCharts(data) {
    try {
        // Update Machine Readiness Chart
        if (charts.machineReadiness) {
            charts.machineReadiness.updateOptions({
                series: [data.machineReadiness]
            });
        }
        
        // Update Power Delivery Chart
        if (charts.powerDelivery) {
            charts.powerDelivery.updateOptions({
                series: [data.powerDeliveryPercentage]
            });
        }
        
        // Update Unserved Load Chart
        if (charts.unservedLoad) {
            charts.unservedLoad.updateOptions({
                xaxis: {
                    categories: data.dates
                },
                series: data.datasets
            });
        }

        // Update status details jika ada
        if (data.statusDetails) {
            updateStatusDetails(data.statusDetails);
        }

        // Update power delivery details jika ada
        if (data.powerDeliveryDetails) {
            updatePowerDetails(data.powerDeliveryDetails);
        }
    } catch (error) {
        console.error('Error updating charts:', error);
        Swal.fire({
            icon: 'error',
            title: 'Gagal memperbarui grafik',
            text: 'Terjadi kesalahan saat memperbarui tampilan grafik',
            confirmButtonText: 'Tutup'
        });
    }
}

function updateStatusDetails(details) {
    try {
        // Update status breakdown numbers
        if (details.breakdown) {
            Object.keys(details.breakdown).forEach(status => {
                const element = document.querySelector(`[data-status="${status}"]`);
                if (element) {
                    element.textContent = `${details.breakdown[status]} Unit`;
                }
            });
        }

        // Update ready/not ready percentages jika ada
        const readyElement = document.querySelector('[data-readiness="ready"]');
        const notReadyElement = document.querySelector('[data-readiness="not-ready"]');
        
        if (readyElement && details.ready) {
            readyElement.textContent = `${details.ready.count} Unit (${details.ready.percentage}%)`;
        }
        if (notReadyElement && details.notReady) {
            notReadyElement.textContent = `${details.notReady.count} Unit (${details.notReady.percentage}%)`;
        }
    } catch (error) {
        console.error('Error updating status details:', error);
    }
}

function updatePowerDetails(details) {
    try {
        // Update power delivery details
        const deliveredElement = document.querySelector('[data-power="delivered"]');
        const undeliveredElement = document.querySelector('[data-power="undelivered"]');
        const totalElement = document.querySelector('[data-power="total"]');
        
        if (deliveredElement) {
            deliveredElement.textContent = `${Number(details.delivered).toFixed(1)} MW`;
        }
        if (undeliveredElement) {
            undeliveredElement.textContent = `${Number(details.undelivered).toFixed(1)} MW`;
        }
        if (totalElement) {
            totalElement.textContent = `${Number(details.total).toFixed(1)} MW`;
        }
    } catch (error) {
        console.error('Error updating power details:', error);
    }
}

// Initialize charts object when charts are created
document.addEventListener('DOMContentLoaded', () => {
    // Inisialisasi chart dengan konfigurasi yang sudah ada
    charts = {
        machineReadiness: new ApexCharts(document.querySelector("#machineReadinessChart"), readinessOptions),
        powerDelivery: new ApexCharts(document.querySelector("#powerDeliveryChart"), powerDeliveryOptions),
        unservedLoad: new ApexCharts(document.querySelector("#unservedLoadChart"), barOptions)
    };

    // Render semua chart
    Object.values(charts).forEach(chart => chart.render());
});
</script>