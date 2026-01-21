@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/homepage.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
      

        /* Hero section adjustments */
        .hexagon-background {
            position: relative;
            margin-top: -80px;
            background-image: url('{{ asset('background/background.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.5);
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

        /* Notulen Table Styles */
        .notulen-table {
            font-size: 14px;
            background-color: white;
            margin-bottom: 0;
            color: #000000
        }

        .notulen-table thead th {
            font-weight: 600;
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            padding: 1rem;
            color: #000000;
        }

        .notulen-table tbody tr {
            transition: all 0.2s ease-in-out;
            border-bottom: 1px solid #dee2e6;
            color: #000000;
        }

        .notulen-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .notulen-table td, .notulen-table th {
            vertical-align: middle;
            border-left: 1px solid #dee2e6;
            padding: 1rem;
            color: #000000;
        }

        .notulen-table td:last-child, .notulen-table th:last-child {
            border-right: 1px solid #dee2e6;
        }

        .notulen-card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: box-shadow 0.3s ease-in-out;
        }

        .notulen-card:hover {
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        .notulen-header {
            background: linear-gradient(45deg, #0d6efd, #0a58ca);
            border-radius: 8px 8px 0 0;
            padding: 1.25rem;
        }

        .notulen-badge {
            background-color: rgba(255, 255, 255, 0.9);
            color: #0d6efd;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 500;
        }

        .notulen-text-wrap {
            word-break: break-word;
            line-height: 1.5;
            max-width: 250px;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            .notulen-table {
                font-size: 13px;
            }

            .notulen-table td, .notulen-table th {
                padding: 0.75rem !important;
            }

            .notulen-text-wrap {
                max-width: 150px;
            }
        }

        /* Notulen section styles */
        .notulen-section {
            padding: 2rem;
            background-color: #f8fafc;
            margin: 2rem 0;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .notulen-title {
            color: #0095B7;
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            text-align: center;
        }

        .notulen-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background-color: white;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .notulen-table th,
        .notulen-table td {
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            text-align: left;
        }

        .notulen-table th {
            background-color: #0095B7;
            color: white;
            font-weight: 500;
        }

        .notulen-table tr:hover {
            background-color: #f1f5f9;
        }

        .btn-view {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background-color: #0095B7;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-view:hover {
            background-color: #007a94;
            color: white;
            transform: translateY(-1px);
        }

        .btn-view i {
            font-size: 0.875rem;
        }

        @media (max-width: 768px) {
            .notulen-table {
                display: block;
                overflow-x: auto;
            }
        }

        .btn-edit {
            padding: 5px 10px;
            background-color: #ffc107;
            color: #000;
            border-radius: 4px;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 14px;
        }

        .btn-edit:hover {
            background-color: #ffb300;
            color: #000;
        }

        .gap-2 {
            gap: 0.5rem;
        }

        .badge {
            padding: 5px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        .bg-info {
            background-color: #17a2b8;
            color: white;
        }
        /* Popup animation for Leaflet */
        @keyframes fadeUpPopup { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
        .popup-anim { animation: fadeUpPopup .3s ease; }
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
           @include('components.navbar')
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
                style="height: 650px; border-radius: 20px; position: relative; margin: 30px 30px 0; padding: 0; "
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
                                    <div class="flex justify-between items-center p-2 bg-green-50 rounded hover:bg-green-100 transition-colors duration-200 cursor-help"
                                         data-status="Operasi"
                                         onmouseover="showMachineTooltip(event, 'Operasi')"
                                         onmouseout="hideMachineTooltip()">
                                        <span class="text-green-700 text-xs sm:text-sm">Operasi</span>
                                        <span class="font-semibold text-green-800 text-xs sm:text-sm">{{ $chartData['statusDetails']['breakdown']['Operasi'] }} Unit</span>
                                    </div>
                                    <div class="flex justify-between items-center p-2 bg-blue-50 rounded hover:bg-blue-100 transition-colors duration-200 cursor-help"
                                         data-status="Standby"
                                         onmouseover="showMachineTooltip(event, 'Standby')"
                                         onmouseout="hideMachineTooltip()">
                                        <span class="text-blue-700 text-xs sm:text-sm">Standby</span>
                                        <span class="font-semibold text-blue-800 text-xs sm:text-sm">{{ $chartData['statusDetails']['breakdown']['Standby'] }} Unit</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <div class="flex justify-between items-center p-2 bg-red-50 rounded hover:bg-red-100 transition-colors duration-200 cursor-help"
                                         data-status="Gangguan"
                                         onmouseover="showMachineTooltip(event, 'Gangguan')"
                                         onmouseout="hideMachineTooltip()">
                                        <span class="text-red-700 text-xs sm:text-sm">Gangguan</span>
                                        <span class="font-semibold text-red-800 text-xs sm:text-sm">{{ $chartData['statusDetails']['breakdown']['Gangguan'] }} Unit</span>
                                    </div>
                                    <div class="flex justify-between items-center p-2 bg-yellow-50 rounded hover:bg-yellow-100 transition-colors duration-200 cursor-help"
                                         data-status="Pemeliharaan"
                                         onmouseover="showMachineTooltip(event, 'Pemeliharaan')"
                                         onmouseout="hideMachineTooltip()">
                                        <span class="text-yellow-700 text-xs sm:text-sm">Pemeliharaan</span>
                                        <span class="font-semibold text-yellow-800 text-xs sm:text-sm">{{ $chartData['statusDetails']['breakdown']['Pemeliharaan'] }} Unit</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <div class="flex justify-between items-center p-2 bg-purple-50 rounded hover:bg-purple-100 transition-colors duration-200 cursor-help"
                                         data-status="Mothballed"
                                         onmouseover="showMachineTooltip(event, 'Mothballed')"
                                         onmouseout="hideMachineTooltip()">
                                        <span class="text-purple-700 text-xs sm:text-sm">Mothballed</span>
                                        <span class="font-semibold text-purple-800 text-xs sm:text-sm">{{ $chartData['statusDetails']['breakdown']['Mothballed'] }} Unit</span>
                                    </div>
                                    <div class="flex justify-between items-center p-2 bg-orange-50 rounded hover:bg-orange-100 transition-colors duration-200 cursor-help"
                                         data-status="Overhaul"
                                         onmouseover="showMachineTooltip(event, 'Overhaul')"
                                         onmouseout="hideMachineTooltip()">
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

            <!-- Tambahkan grafik kehadiran & scorecard -->
            <div class="container mx-auto px-4 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Persentase Kehadiran Harian</h3>
                        </div>
                        <canvas id="attendanceChart" height="180"></canvas>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Score Daily Meeting</h3>
                        </div>
                        <canvas id="scoreChart" height="180"></canvas>
                    </div>
                </div>
                <!-- Tambahkan grafik status SR, WO, WO Backlog -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Presentasi Status SR</h3>
                        </div>
                        <canvas id="srStatusChart" height="180"></canvas>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Presentasi Status WO</h3>
                        </div>
                        <canvas id="woStatusChart" height="180"></canvas>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">WO Backlog Status</h3>
                        </div>
                        <canvas id="woBacklogStatusChart" height="180"></canvas>
                    </div>
                </div>
            </div>
            <!-- Live Data Unit Operasional -->
            <div class="flex justify-center items-center mt-10 mb-4 gap-4">
                <h3 class="text-xl font-semibold">Kinerja Pemeliharaan</h3>
                <div class="flex items-center text-gray-600">
                    <i class="far fa-clock mr-2"></i>
                    <span id="liveTime"></span>
                </div>
            </div>

            <div class="w-full flex justify-center flex-col items-center mb-5">
                <div id="live-data" class="bg-white border border-gray-300 rounded-lg p-4 w-4/5">
                    <div class="flex justify-end mb-4 gap-2">
                        <button onclick="switchView('disruption')"
                                id="disruptionBtn"
                                class="view-btn bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Data Gangguan
                        </button>
                        <button onclick="switchView('engine')"
                                id="engineBtn"
                                class="view-btn bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                            <i class="fas fa-cogs mr-2"></i>Issue Engine
                        </button>
                    </div>
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
                                    <!-- Kolom untuk Issue Engine View -->
                                    <th class="text-center issue-column" style="display: none;">Issue Engine</th>
                                    <th class="text-center issue-column" style="display: none;">Catatan Issue</th>
                                    <th class="text-center issue-column" style="display: none;">Progres Pembahasan</th>
                                    <!-- Kolom untuk Data Gangguan View -->

                                    <th class="text-center disruption-column">Catatan Issue</th>
                                    <th class="text-center">Waktu Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($powerPlants as $plant)
                                    @foreach ($plant->machines as $machine)
                                        @php
                                            $latestStatus = $machine->statusLogs->first();
                                            if (!$latestStatus) continue;

                                            $statusStyle = match($latestStatus->status) {
                                                'Gangguan' => [
                                                    'bg' => '#FEE2E2',
                                                    'text' => '#991B1B',
                                                    'border' => '#FECACA',
                                                    'icon' => '⚠️'
                                                ],
                                                'Pemeliharaan' => [
                                                    'bg' => '#E0F2FE',
                                                    'text' => '#075985',
                                                    'border' => '#BAE6FD',
                                                    'icon' => '🔧'
                                                ],
                                                'Mothballed' => [
                                                    'bg' => '#F3F4F6',
                                                    'text' => '#374151',
                                                    'border' => '#D1D5DB',
                                                    'icon' => '💤'
                                                ],
                                                'Operasi' => [
                                                    'bg' => '#DCFCE7',
                                                    'text' => '#166534',
                                                    'border' => '#BBF7D0',
                                                    'icon' => '⚡'
                                                ],
                                                'Standby' => [
                                                    'bg' => '#FEF9C3',
                                                    'text' => '#854D0E',
                                                    'border' => '#FEF08A',
                                                    'icon' => '🔆'
                                                ],
                                                default => [
                                                    'bg' => '#F3F4F6',
                                                    'text' => '#374151',
                                                    'border' => '#D1D5DB',
                                                    'icon' => '❓'
                                                ]
                                            };
                                        @endphp
                                        <tr class="table-row" data-plant-id="{{ $plant->id }}">
                                            <td class="text-center">{{ $plant->name }}</td>
                                            <td class="text-center">{{ $machine->name }}</td>
                                            <td class="text-center">{{ number_format($latestStatus->dmn, 1) }}</td>
                                            <td class="text-center">{{ number_format($latestStatus->dmp, 1) }}</td>
                                            <td class="text-center">{{ number_format($latestStatus->load_value, 1) }} MW</td>
                                            <td class="text-center w-[150px]">
                                                <span style="
                                                    background: {{ $statusStyle['bg'] }};
                                                    color: {{ $statusStyle['text'] }};
                                                    padding: 4px 12px;
                                                    border-radius: 12px;
                                                    width: 150px;
                                                    font-size: 0.85em;
                                                    border: 1px solid {{ $statusStyle['border'] }};
                                                ">
                                                    {{ $statusStyle['icon'] }} {{ $latestStatus->status }}
                                                </span>
                                            </td>
                                            <!-- Kolom untuk Issue Engine View -->
                                            <td class="text-center w-40 issue-column " style="display: none;">
                                                <span style="
                                                    background: {{ $latestStatus->component ? '#E0F2FE' : '#F3F4F6' }};
                                                    color: {{ $latestStatus->component ? '#0369A1' : '#6B7280' }};
                                                    padding: 4px 12px;
                                                    border-radius: 12px;
                                                    font-size: 0.85em;
                                                    border: 1px solid {{ $latestStatus->component ? '#7DD3FC' : '#D1D5DB' }};
                                                ">
                                                    {{ $latestStatus->component ?: 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="text-center w-40 issue-column" style="display: none;">
                                                {{ $latestStatus->equipment ?: 'N/A' }}
                                            </td>
                                            <td class="issue-column px-4 py-2 w-[600px]" style="display: none;">
                                                @if($latestStatus->component === 'Ada')
                                                    @php
                                                        $discussion = \App\Models\OtherDiscussion::where('status', '!=', 'Deleted')
                                                            ->where(function($query) use ($machine, $plant) {
                                                                $query->where('machine_id', $machine->id)
                                                                      ->orWhere(function($q) use ($machine, $plant) {
                                                                          $q->where('machine_reference', $machine->name)
                                                                            ->where('unit_asal', $plant->name);
                                                                    });
                                                            })
                                                            ->where('issue_active', true)
                                                            ->latest()
                                                            ->first();
                                                    @endphp

                                                    @if($discussion)
                                                        <div class="bg-white rounded-lg shadow p-4 mb-4">
                                                            <div class="flex justify-between items-start mb-3">
                                                                <div>
                                                                    <span class="text-sm font-semibold text-gray-700">No. Pembahasan:</span>
                                                                    <a href="{{ route('admin.other-discussions.show', $discussion->id) }}"
                                                                       class="ml-2 text-blue-600 hover:text-blue-800">
                                                                        {{ $discussion->no_pembahasan }}
                                                                    </a>
                                                                </div>
                                                                <span class="px-2 py-1 text-xs rounded-full {{ $discussion->status === 'Open' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                                                        {{ $discussion->status }}
                                                                    </span>
                                                            </div>

                                                            <div class="mb-3">
                                                                <span class="text-sm font-semibold text-gray-700">Topic:</span>
                                                                <p class="text-sm text-gray-600">{{ $discussion->topic }}</p>
                                                            </div>

                                                            <div class="mb-3">
                                                                <span class="text-sm font-semibold text-gray-700">PIC:</span>
                                                                <p class="text-sm text-gray-600">{{ $discussion->pic }}</p>
                                                                            </div>

                                                            @if($discussion->commitments->isNotEmpty())
                                                                <div class="mb-3">
                                                                    <span class="text-sm font-semibold text-gray-700">Commitments:</span>
                                                                    <div class="mt-2 space-y-2">
                                                                        @foreach($discussion->commitments as $commitment)
                                                                            <div class="border-l-4 {{ $commitment->status === 'Open' ? 'border-yellow-400' : 'border-green-400' }} pl-3 py-2">
                                                                                <div class="flex justify-between items-start">
                                                                                    <div class="text-sm text-gray-600">{{ $commitment->description }}</div>
                                                                                    <span class="px-2 py-1 text-xs rounded {{ $commitment->status === 'Open' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                                                                {{ $commitment->status }}
                                                                            </span>
                                                                        </div>
                                                                                <div class="mt-1 flex items-center text-xs text-gray-500">
                                                                                    <span class="mr-2">{{ $commitment->section->department->name }} - {{ $commitment->section->name }}</span>
                                                                                    <span>Deadline: {{ \Carbon\Carbon::parse($commitment->deadline)->format('d M Y') }}</span>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                                </div>
                                                            @endif

                                                            <div class="text-xs text-gray-500 mt-3 pt-3 border-t border-gray-200">
                                                                <div class="flex justify-between">
                                                                    <span>Created: {{ $discussion->created_at->format('d M Y H:i') }}</span>
                                                                    @if($discussion->target_deadline)
                                                                        <span>Target: {{ \Carbon\Carbon::parse($discussion->target_deadline)->format('d M Y') }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <a href="{{ route('admin.other-discussions.create', [
                                                            'unit' => $plant->name,
                                                            'unit_asal' => $plant->name,
                                                            'machine_id' => $machine->id,
                                                            'machine_name' => $machine->name,
                                                            'issue_active' => 1
                                                        ]) }}" class="text-blue-600 hover:text-blue-800">
                                                            + Buat Pembahasan
                                                        </a>
                                                    @endif
                                                @endif
                                            </td>
                                            <!-- Kolom untuk Data Gangguan View -->

                                            <td class="text-center text-sm text-gray-500 disruption-column">
                                                <div class="max-w-[400px] mx-auto break-words">
                                                    {{ $latestStatus->equipment }}
                                                </div>
                                            </td>
                                            <td class="text-center text-sm text-gray-500">
                                                {{ $latestStatus->created_at ? $latestStatus->created_at->format('d/m/Y H:i:s') : '-' }}
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



    
<style>
    .table {
        font-size: 14px;
    }

    .table thead th {
        font-weight: 600;
        border-top: none;
        background-color: #f8f9fa;
    }

    .table tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05) !important;
    }

    .table td, .table th {
        vertical-align: middle;
        border-left: 1px solid #dee2e6;
    }

    .table td:last-child, .table th:last-child {
        border-right: 1px solid #dee2e6;
    }

    .card {
        border: none;
        border-radius: 8px;
    }

    .card-header {
        border-radius: 8px 8px 0 0 !important;
    }

    .text-wrap {
        word-break: break-word;
        line-height: 1.5;
    }

    .font-weight-medium {
        font-weight: 500;
    }

    .badge {
        padding: 0.5em 1em;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .table {
            font-size: 13px;
        }

        .table td, .table th {
            padding: 0.5rem !important;
        }
    }
</style>
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
                        <p>Telepon: +62 82293118410</p>
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
                        categories: ["{{ implode('","', is_array(
                            $dates) ? $dates : (method_exists($dates, 'toArray') ? $dates->toArray() : []) ) }}"]
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
                var _autoCycleTimer = null;
                var _autoIndex = 0;
                var _userLastInteractAt = 0;
                var _initialPopupShown = false;

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
                                                        <span style="color: #666;">DMP:</span>
                                                        <span style="color: {{ $statusStyle['text'] }}; font-weight: 600;">
                                                            {{ $latestStatus ? $latestStatus->dmn : 0 }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span style="color: #666;">DMN:</span>
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
                    // Simpan marker untuk rotasi otomatis
                    markers.push(marker);
                    bounds.extend(markerLatLng);
                @endforeach

                // Animasi popup saat terbuka (tanpa ganggu fungsi lain)
                map.on('popupopen', function() {
                    setTimeout(function(){
                        var popups = document.querySelectorAll('.leaflet-popup');
                        popups.forEach(function(p){
                            p.classList.remove('popup-anim');
                            // reflow untuk restart animasi
                            void p.offsetWidth;
                            p.classList.add('popup-anim');
                        });
                    }, 0);
                });

                // Deteksi interaksi pengguna untuk jeda rotasi
                ['click','mousedown','touchstart','movestart','zoomstart','dragstart'].forEach(function(evt){
                    map.on(evt, function(){ _userLastInteractAt = Date.now(); });
                });

                function startAutoCyclePopups() {
                    if (!markers.length) return;
                    if (_autoCycleTimer) { clearInterval(_autoCycleTimer); }
                    _autoCycleTimer = setInterval(function(){
                        // Jeda 10 detik setelah interaksi user
                        if (Date.now() - _userLastInteractAt < 10000) return;
                        try {
                            if (_autoIndex >= markers.length) _autoIndex = 0;
                            markers[_autoIndex].openPopup();
                            _autoIndex = (_autoIndex + 1) % markers.length;
                        } catch(e) { /* ignore */ }
                    }, 4000);
                }

                function openNextPopupNow() {
                    if (!markers.length) return;
                    try {
                        if (_autoIndex >= markers.length) _autoIndex = 0;
                        markers[_autoIndex].openPopup();
                        _autoIndex = (_autoIndex + 1) % markers.length;
                    } catch(e) { /* ignore */ }
                }

                function isElementInViewport(el) {
                    if (!el) return false;
                    const rect = el.getBoundingClientRect();
                    const vw = window.innerWidth || document.documentElement.clientWidth;
                    const vh = window.innerHeight || document.documentElement.clientHeight;
                    return (
                        rect.bottom > 0 &&
                        rect.right > 0 &&
                        rect.left < vw &&
                        rect.top < vh
                    );
                }

                function maybeShowInitialPopup() {
                    if (_initialPopupShown) return;
                    _initialPopupShown = true;
                    openNextPopupNow();
                }

                // Mulai rotasi popup otomatis dan sesuaikan tampilan
                try { if (markers.length) { map.fitBounds(bounds, { padding: [30,30] }); } } catch(e) {}
                startAutoCyclePopups();
                // Tampilkan popup segera saat halaman dimuat (tanpa menunggu interval)
                maybeShowInitialPopup();

                // Jika pengguna scroll dan peta terlihat, pastikan popup muncul saat itu juga (sekali saja)
                (function setupMapVisibilityTrigger(){
                    var mapEl = document.getElementById('map');
                    if (!mapEl) return;
                    if (isElementInViewport(mapEl)) {
                        maybeShowInitialPopup();
                        return;
                    }
                    try {
                        var io = new IntersectionObserver(function(entries){
                            if (entries && entries[0] && entries[0].isIntersecting) {
                                maybeShowInitialPopup();
                                io.disconnect();
                            }
                        }, { threshold: 0.15 });
                        io.observe(mapEl);
                    } catch(e) {
                        // Fallback: cek saat scroll
                        var onScroll = function(){
                            if (isElementInViewport(mapEl)) {
                                maybeShowInitialPopup();
                                window.removeEventListener('scroll', onScroll, true);
                            }
                        };
                        window.addEventListener('scroll', onScroll, true);
                    }
                })();

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
                        offsetY: -10, // Sesuaikan offset untuk posisi yang lebih baik
                        events: {
                            dataPointMouseEnter: function(event, chartContext, config) {
                                const status = config.w.config.labels[config.dataPointIndex];
                                const machineNames = chartData.statusDetails.machineNames[status];
                                if (machineNames && machineNames.length > 0) {
                                    const tooltip = document.createElement('div');
                                    tooltip.className = 'apexcharts-tooltip';
                                    tooltip.style.position = 'absolute';
                                    tooltip.style.backgroundColor = 'rgba(255, 255, 255, 0.96)';
                                    tooltip.style.padding = '10px';
                                    tooltip.style.borderRadius = '5px';
                                    tooltip.style.boxShadow = '0 2px 6px rgba(0,0,0,0.2)';
                                    tooltip.style.maxWidth = '200px';
                                    tooltip.style.zIndex = '12000';
                                    tooltip.innerHTML = `
                                        <div style="font-weight: bold; margin-bottom: 5px;">${status}</div>
                                        <div style="font-size: 12px;">${machineNames.join('<br>')}</div>
                                    `;

                                    const chart = document.querySelector('#machineReadinessChart');
                                    chart.appendChild(tooltip);

                                    // Position tooltip near mouse
                                    tooltip.style.left = event.pageX - chart.getBoundingClientRect().left + 10 + 'px';
                                    tooltip.style.top = event.pageY - chart.getBoundingClientRect().top + 10 + 'px';
                                }
                            },
                            dataPointMouseLeave: function() {
                                const tooltips = document.querySelectorAll('#machineReadinessChart .apexcharts-tooltip');
                                tooltips.forEach(tooltip => tooltip.remove());
                            }
                        }
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
                    tooltip: {
                        enabled: true,
                        custom: function({ series, seriesIndex, dataPointIndex, w }) {
                            const status = w.config.labels[dataPointIndex];
                            const machineNames = chartData.statusDetails.machineNames[status];
                            if (!machineNames || machineNames.length === 0) return '';

                            return `
                                <div class="apexcharts-tooltip-title">${status}</div>
                                <div class="apexcharts-tooltip-series-group">
                                    <div class="apexcharts-tooltip-text">
                                        ${machineNames.join('<br>')}
                                    </div>
                                </div>
                            `;
                        }
                    },
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

<script>
let currentView = 'disruption';
let allRows = [];

document.addEventListener('DOMContentLoaded', () => {
    // Store all rows initially
    allRows = Array.from(document.querySelectorAll('tr.table-row'));
    switchView('disruption');
});

function switchView(view) {
    currentView = view;

    // Update button styles
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.classList.remove('bg-blue-500', 'bg-gray-500', 'hover:bg-blue-600', 'hover:bg-gray-600');
        btn.classList.add('bg-gray-500', 'hover:bg-gray-600');
    });

    const activeBtn = document.getElementById(`${view}Btn`);
    activeBtn.classList.remove('bg-gray-500', 'hover:bg-gray-600');
    activeBtn.classList.add('bg-blue-500', 'hover:bg-blue-600');

    // Toggle column visibility based on view
    const issueColumns = document.querySelectorAll('.issue-column');
    const disruptionColumns = document.querySelectorAll('.disruption-column');

    if (view === 'engine') {
        // Show issue columns, hide disruption columns
        issueColumns.forEach(col => col.style.display = '');
        disruptionColumns.forEach(col => col.style.display = 'none');

        // Filter rows to only show machines with component "Ada"
        allRows.forEach(row => {
            const componentSpan = row.querySelector('td.issue-column span');
            if (!componentSpan) return;

            const componentText = componentSpan.textContent.trim();

            // Only show rows where component is "Ada", regardless of status
            if (componentText === 'Ada') {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    } else {
        // Show disruption columns, hide issue columns
        issueColumns.forEach(col => col.style.display = 'none');
        disruptionColumns.forEach(col => col.style.display = '');

        // Filter rows to only show machines with non-operational status
        allRows.forEach(row => {
            const statusSpan = row.querySelector('td:nth-child(6) span');
            if (!statusSpan) return;

            const statusText = statusSpan.textContent.trim();
            // Remove emoji and extra spaces from status text
            const cleanStatus = statusText.replace(/[\u{1F300}-\u{1F9FF}]/gu, '').trim();

            // Define operational statuses that should be hidden
            const operationalStatuses = ['Operasi', 'Standby', '⚡ Operasi', '🔆 Standby'];

            // Hide rows with operational status
            if (operationalStatuses.includes(cleanStatus) || operationalStatuses.includes(statusText)) {
                row.style.display = 'none';
            } else {
                row.style.display = '';
            }
        });
    }
}

function updateTableData() {
    const url = currentView === 'disruption'
        ? '{{ route("getAccumulationData", ["markerId" => ":id"]) }}'
        : '{{ route("getEngineIssues", ["markerId" => ":id"]) }}';

    // Update data for each power plant
    document.querySelectorAll('[data-plant-id]').forEach(row => {
        const plantId = row.getAttribute('data-plant-id');
        fetch(url.replace(':id', plantId))
            .then(response => response.json())
            .then(data => {
                // Update table cells based on the view type
                if (currentView === 'disruption') {
                    updateDisruptionData(row, data);
                } else {
                    updateEngineIssueData(row, data);
                }
            })
            .catch(error => console.error('Error:', error));
    });
}

function updateDisruptionData(row, data) {
    if (data && data.length > 0) {
        const issue = data[0];
        const cells = row.querySelectorAll('td');

        // Update status if needed
        const statusCell = cells[5];
        const statusText = statusCell.querySelector('span').textContent.trim();
        // Remove emoji and extra spaces from status text
        const cleanStatus = statusText.replace(/[\u{1F300}-\u{1F9FF}]/gu, '').trim();

        // Define operational statuses that should be hidden
        const operationalStatuses = ['Operasi', 'Standby', '⚡ Operasi', '🔆 Standby'];

        // Only show and update rows with non-operational status
        if (!operationalStatuses.includes(cleanStatus) && !operationalStatuses.includes(statusText)) {
            cells[8].querySelector('div').textContent = issue.progres || 'N/A';
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    } else {
        row.style.display = 'none';
    }
}

function updateEngineIssueData(row, data) {
    // Update cells for engine issue view
    const cells = row.querySelectorAll('td');
    if (data && data.length > 0) {
        const issue = data[0];
        if (issue.component === 'Ada') {
            cells[6].innerHTML = `<span style="
                background: #E0F2FE;
                color: #0369A1;
                padding: 4px 12px;
                border-radius: 12px;
                font-size: 0.85em;
                border: 1px solid #7DD3FC;
            ">Ada</span>`;
            cells[7].textContent = issue.equipment || 'N/A';
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    } else {
        row.style.display = 'none';
    }
}
</script>

<script>
// ... existing code ...

function createDiscussion(plant, machine, equipment) {
    const baseUrl = window.location.origin + '/public';
    const issueDescription = `Issue pada ${machine}: ${equipment}`;
    const defaultCommitment = `Penyelesaian issue ${equipment} pada ${machine}`;

    const discussionParams = {
        unit: 'UP KENDARI',
        topic: issueDescription,
        default_commitment: defaultCommitment,
        machine_name: machine
    };

    @auth
        // Jika sudah login, langsung ke halaman create
        window.location.href = `${baseUrl}/admin/other-discussions/create?${new URLSearchParams(discussionParams).toString()}`;
    @else
        // Jika belum login, simpan parameter ke session storage
        sessionStorage.setItem('pendingDiscussion', JSON.stringify({
            params: discussionParams,
            returnUrl: '/admin/other-discussions/create'
        }));

        // Tampilkan pesan dan arahkan ke halaman login
        Swal.fire({
            title: 'Login Diperlukan',
            text: 'Anda harus login terlebih dahulu untuk membuat pembahasan',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Login',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `${baseUrl}/login`;
            }
        });
    @endauth
}

// Cek pendingDiscussion setelah login
document.addEventListener('DOMContentLoaded', function() {
    @auth
        const pendingDiscussion = sessionStorage.getItem('pendingDiscussion');
        if (pendingDiscussion) {
            const data = JSON.parse(pendingDiscussion);
            const baseUrl = window.location.origin + '/public';

            // Redirect ke halaman create dengan parameter yang tersimpan
            window.location.href = `${baseUrl}${data.returnUrl}?${new URLSearchParams(data.params).toString()}`;

            // Hapus data dari session storage
            sessionStorage.removeItem('pendingDiscussion');
        }
    @endauth
});

// ... existing code ...
</script>

<script>
// Add this at the beginning of your script section to make machine names data available
const machineNamesByStatus = @json($chartData['statusDetails']['machineNames'] ?? []);

function showMachineTooltip(event, status) {
    const machineNames = machineNamesByStatus[status] || [];
    if (machineNames.length === 0) return;

    // Remove any existing tooltips
    hideMachineTooltip();

    const tooltip = document.createElement('div');
    tooltip.id = 'machine-tooltip';
    tooltip.className = 'fixed bg-white p-4 rounded-lg shadow-lg z-50 text-sm min-w-[200px] max-w-[300px] border border-gray-200';

    // Get the clicked element's position
    const element = event.currentTarget;
    const rect = element.getBoundingClientRect();

    // Position tooltip to the right of the element
    tooltip.style.left = (rect.right + 10) + 'px';
    tooltip.style.top = rect.top + 'px';

    tooltip.innerHTML = `
        <div class="font-semibold mb-2 pb-2 border-b border-gray-200">${status}</div>
        <div class="text-gray-600 max-h-[200px] overflow-y-auto">
            ${machineNames.map(name => `<div class="py-1">${name}</div>`).join('')}
        </div>
    `;

    document.body.appendChild(tooltip);

    // Adjust position if tooltip would go off screen
    const tooltipRect = tooltip.getBoundingClientRect();
    const viewportWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;

    if (tooltipRect.right > viewportWidth) {
        // If tooltip would go off right edge, show it to the left of the element instead
        tooltip.style.left = (rect.left - tooltipRect.width - 10) + 'px';
    }

    if (tooltipRect.bottom > viewportHeight) {
        // If tooltip would go off bottom edge, align it with bottom of viewport
        tooltip.style.top = (viewportHeight - tooltipRect.height - 10) + 'px';
    }

    // Add smooth fade-in animation
    tooltip.style.opacity = '0';
    tooltip.style.transition = 'opacity 0.2s ease-in-out';
    requestAnimationFrame(() => {
        tooltip.style.opacity = '1';
    });
}

function hideMachineTooltip() {
    const tooltip = document.getElementById('machine-tooltip');
    if (tooltip) {
        // Add fade-out animation
        tooltip.style.opacity = '0';
        setTimeout(() => tooltip.remove(), 200);
    }
}

// Add global event listener to hide tooltip when clicking outside
document.addEventListener('click', function(event) {
    const tooltip = document.getElementById('machine-tooltip');
    if (tooltip && !event.target.closest('#machine-tooltip') && !event.target.closest('[data-status]')) {
        hideMachineTooltip();
    }
});

// Add global event listener to hide tooltip when scrolling
document.addEventListener('scroll', function() {
    hideMachineTooltip();
}, true);

// ... rest of the existing code ...
</script>

<script>
// Add this to your existing JavaScript
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.create-discussion-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.href;

            // Check if user is logged in
            @guest
                Swal.fire({
                    title: 'Login Diperlukan',
                    text: 'Anda harus login terlebih dahulu untuk membuat pembahasan',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Login',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Store the target URL in session storage
                        sessionStorage.setItem('redirectAfterLogin', url);
                        window.location.href = '{{ route("login") }}';
                    }
                });
            @else
                window.location.href = url;
            @endguest
        });
    });
});
</script>

<script>
function handleCreateDiscussion(plantName, machineName) {
    const discussionParams = {
        unit: 'UP KENDARI',
        machine_name: machineName,
        machine_id: '{{ $machine->id }}',
        machine_reference: machineName,
        topic: 'Issue pada ' + machineName,
        default_commitment: 'Penyelesaian issue pada ' + machineName,
        issue_active: 1
    };

    // Store the target URL in session storage
    const targetUrl = '{{ route('admin.other-discussions.create') }}?' + new URLSearchParams(discussionParams).toString();
    sessionStorage.setItem('redirectAfterLogin', targetUrl);

    // Show login prompt
    Swal.fire({
        title: 'Login Diperlukan',
        text: 'Anda harus login terlebih dahulu untuk membuat pembahasan',
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Login',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Force logout if user is already logged in
            @auth
                // Perform logout via AJAX
                fetch('{{ route('logout') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(() => {
                    window.location.href = '{{ route('login') }}';
                });
            @else
                window.location.href = '{{ route('login') }}';
            @endauth
        }
    });
}
</script>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const attendanceChartData = @json($attendanceChartData);
    const scoreChartData = @json($scoreChartData);
    const srStatusData = @json($srStatusData);
    const woStatusData = @json($woStatusData);
    const woBacklogStatusData = @json($woBacklogStatusData);
    // Attendance Chart
    new Chart(document.getElementById('attendanceChart'), {
        type: 'bar',
        data: {
            labels: attendanceChartData.labels,
            datasets: [{
                label: 'Jumlah Peserta Hadir',
                data: attendanceChartData.data,
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1,
                maxBarThickness: 40
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Jumlah Peserta Hadir per Hari' }
            },
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true }
            }
        }
    });
    // Score Chart
    new Chart(document.getElementById('scoreChart'), {
        type: 'line',
        data: {
            labels: scoreChartData.labels,
            datasets: [{
                label: 'Score Rata-rata',
                data: scoreChartData.data,
                fill: false,
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.2)',
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Score Rata-rata Daily Meeting' }
            },
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, max: 100 }
            }
        }
    });
    // SR Status Chart
    new Chart(document.getElementById('srStatusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Open', 'Closed'],
            datasets: [{
                data: srStatusData.counts,
                backgroundColor: ['#f59e42', '#10b981'],
                borderColor: ['#f59e42', '#10b981'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                title: { display: true, text: 'Status Service Request' }
            }
        }
    });
    // WO Status Chart
    new Chart(document.getElementById('woStatusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Open', 'Closed'],
            datasets: [{
                data: woStatusData.counts,
                backgroundColor: ['#f43f5e', '#6366f1'],
                borderColor: ['#f43f5e', '#6366f1'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                title: { display: true, text: 'Status Work Order' }
            }
        }
    });
    // WO Backlog Status Chart (stacked, 1 bar saja)
    const woBacklogLabels = Object.keys(woBacklogStatusData);
    const woBacklogValues = Object.values(woBacklogStatusData);
    const woBacklogColors = ['#f59e42','#16a34a','#f43f5e','#6366f1','#818cf8'];
    new Chart(document.getElementById('woBacklogStatusChart'), {
        type: 'bar',
        data: {
            labels: ['Total Backlog'],
            datasets: woBacklogLabels.map((label, idx) => ({
                label: label,
                data: [woBacklogValues[idx]],
                backgroundColor: woBacklogColors[idx % woBacklogColors.length],
                stack: 'backlog',
            }))
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                title: { display: true, text: 'WO Backlog Status' }
            },
            scales: {
                x: { stacked: true, grid: { display: false } },
                y: { beginAtZero: true, stacked: true }
            }
        }
    });
});
</script>
@endpush

