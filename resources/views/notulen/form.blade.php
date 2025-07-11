@extends('layouts.app')

@section('styles')
<style>
    .form-input {
        background-image: url('{{ asset('background/bg.png') }}');
        border: 1px solid #D1D5DB;
        border-radius: 0.375rem;
        padding: 0.5rem;
        width: 100%;
    }

    /* Navbar Styles */
    .nav-background {
        background-color: #1a1a1a;
        background-image: linear-gradient(to right, #1a1a1a, #2d3748);
    }

    .nav-link {
        color: white !important;
        text-decoration: none;
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
        position: relative;
    }

    .nav-link:hover {
        color: #A8D600 !important;
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
    }

    /* Form Styles */
    .form-container {
        max-width: 800px;
        margin: 2rem auto;
        padding: 2rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .form-title {
        color: #0095B7;
        text-align: center;
        margin-bottom: 2rem;
        font-size: 1.5rem;
        font-weight: bold;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        color: #374151;
        font-weight: 500;
    }

    .form-select, .form-input {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #D1D5DB;
        border-radius: 0.375rem;
        background-color: #F9FAFB;
    }

    .form-select:focus, .form-input:focus {
        outline: none;
        border-color: #0095B7;
        ring: 2px solid #0095B7;
    }

    .preview-container {
        margin-top: 1.5rem;
        padding: 1rem;
        background: #F3F4F6;
        border-radius: 0.375rem;
    }

    .preview-title {
        color: #374151;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .preview-text {
        color: #0095B7;
        font-family: monospace;
        font-size: 1.1rem;
    }

    .btn-create {
        background-color: #0095B7;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.375rem;
        border: none;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        display: block;
        width: 100%;
        margin-top: 1rem;
        text-align: center;
        text-decoration: none;
    }

    .btn-create:hover {
        background-color: #007a94;
    }

    /* Mobile menu styles */
    @media (max-width: 768px) {
        .mobile-menu {
            display: none;
            position: fixed;
            top: 60px;
            left: 0;
            right: 0;
            background-color: #1a1a1a;
            padding: 1rem;
            z-index: 50;
        }

        .mobile-menu.show {
            display: block;
        }

        .nav-link-mobile {
            display: block;
            color: white;
            padding: 0.75rem 1rem;
            text-decoration: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
    }

    /* New styles for improved UI */
    .page-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .tabs {
        display: flex;
        border-bottom: 2px solid #e5e7eb;
        margin-bottom: 2rem;
    }

    .tab {
        padding: 1rem 2rem;
        font-weight: 500;
        color: #6b7280;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        transition: all 0.3s ease;
    }

    .tab.active {
        color: #0095B7;
        border-bottom-color: #0095B7;
    }

    .tab:hover {
        color: #0095B7;
    }

    .search-container {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }

    .search-input {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.5rem;
        font-size: 1rem;
        transition: border-color 0.3s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: #0095B7;
        box-shadow: 0 0 0 3px rgba(0, 149, 183, 0.1);
    }

    .filter-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .results-container {
        margin-top: 2rem;
    }

    .loading-spinner {
        display: none;
        justify-content: center;
        align-items: center;
        padding: 2rem;
    }

    .loading-spinner.active {
        display: flex;
    }

    /* Enhanced form styles */
    .form-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 2rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #374151;
    }

    .form-input, .form-select {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.5rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-input:focus, .form-select:focus {
        outline: none;
        border-color: #0095B7;
        box-shadow: 0 0 0 3px rgba(0, 149, 183, 0.1);
    }

    .btn-create {
        background: linear-gradient(to right, #0095B7, #00b4d8);
        color: white;
        padding: 1rem 2rem;
        border-radius: 0.5rem;
        font-weight: 500;
        text-align: center;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        width: 100%;
        margin-top: 1.5rem;
    }

    .btn-create:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 149, 183, 0.2);
    }

    .format-preview {
        background: #f8fafc;
        padding: 1rem;
        border-radius: 0.5rem;
        border: 2px solid #e5e7eb;
        margin-top: 1rem;
    }

    .format-preview-label {
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .format-preview-content {
        color: #0095B7;
        font-family: monospace;
        font-size: 1.1rem;
    }
</style>
@endsection

@section('content')
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
                <div class="hidden md:flex items-center">
                    <ul class="flex space-x-8">
                        <li><a href="/" class="nav-link">Home</a></li>
                        <li><a href="/#map" class="nav-link">Peta Pembangkit</a></li>
                        <li><a href="/#live-data" class="nav-link">Live Data Unit Operasional</a></li>
                        <li><a href="{{ route('dashboard.pemantauan') }}" class="nav-link">Dashboard Pemantauan</a></li>
                        <li><a href="https://sites.google.com/view/pemeliharaan-upkendari" class="nav-link" target="_blank">Bid. Pemeliharaan</a></li>
                        <li><a href="{{ route('notulen.form') }}" class="nav-link">Notulen</a></li>

                        <!-- Login button -->
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
                    <li><a href="/" class="nav-link-mobile">Home</a></li>
                    <li><a href="/#map" class="nav-link-mobile">Peta Pembangkit</a></li>
                    <li><a href="/#live-data" class="nav-link-mobile">Live Data Unit Operasional</a></li>
                    <li><a href="{{ route('dashboard.pemantauan') }}" class="nav-link-mobile">Dashboard Pemantauan</a></li>
                    <li><a href="https://sites.google.com/view/pemeliharaan-upkendari" class="nav-link-mobile" target="_blank">Bid. Pemeliharaan</a></li>
                    <li><a href="{{ route('notulen.form') }}" class="nav-link-mobile">Notulen</a></li>
                    <!-- Login button in mobile -->
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

<div class="page-container mt-20">
    <div class="tabs">
        <div class="tab active" data-tab="create">Buat Notulen</div>
        <div class="tab" data-tab="search">Cari Notulen</div>
    </div>

    <div id="createTab" class="tab-content">
        <div class="form-container">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Form Notulen Baru</h2>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="nomor_urut">No Urut</label>
                    <input type="text" id="nomor_urut" name="nomor_urut" class="form-input" required value="{{ $nextNomorUrut }}" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label" for="unit">Unit</label>
                    <select id="unit" name="unit" class="form-select" required>
                        <option value="">Pilih Unit</option>
                        <option value="UPKD">UPKD</option>
                        <option value="PLTD Bau Bau">PLTD Bau Bau</option>
                        <option value="PLTD Wua Wua">PLTD Wua Wua</option>
                        <option value="PLTD Poasia">PLTD Poasia</option>
                        <option value="PLTD Kolaka">PLTD Kolaka</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="bidang">Bidang</label>
                    <select id="bidang" name="bidang" class="form-select" required>
                        <option value="">Pilih Bidang</option>
                        <option value="Operasi">Operasi</option>
                        <option value="Pemeliharaan">Pemeliharaan</option>
                        <option value="K3">K3</option>
                        <option value="Lingkungan">Lingkungan</option>
                        <option value="Enjiniring">Enjiniring</option>
                        <option value="Business Support">Business Support</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="sub_bidang">Sub Bidang</label>
                    <select id="sub_bidang" name="sub_bidang" class="form-select" required>
                        <option value="">Pilih Sub Bidang</option>
                        <option value="Rendal har">Rendal har</option>
                        <option value="Rendal outage">Rendal outage</option>
                        <option value="ICC">ICC</option>
                        <option value="Pengadaan">Pengadaan</option>
                        <option value="CBM">CBM</option>
                        <option value="SO">SO</option>
                        <option value="MMRK">MMRK</option>
                        <option value="Rendalop">Rendalop</option>
                        <option value="EP">EP</option>
                        <option value="SDM">SDM</option>
                        <option value="AKT">AKT</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="bulan">Bulan</label>
                    <select id="bulan" name="bulan" class="form-select" required>
                        <option value="">Pilih Bulan</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="tahun">Tahun</label>
                    <select id="tahun" name="tahun" class="form-select" required>
                        <option value="">Pilih Tahun</option>
                        @for($year = 2025; $year <= 2030; $year++)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="format-preview">
                <div class="format-preview-label">Format Nomor Preview:</div>
                <div id="formatPreview" class="format-preview-content">-</div>
            </div>

            <button type="button" id="createNotulenBtn" class="btn-create">
                <i class="fas fa-plus-circle mr-2"></i>Buat Notulen
            </button>
        </div>
    </div>

    <div id="searchTab" class="tab-content hidden">
        <div class="search-container">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Cari Notulen</h2>

            <input type="text"
                   id="searchInput"
                   class="search-input"
                   placeholder="Cari berdasarkan nomor, agenda, tempat..."
                   autocomplete="off">

            <div class="filter-container">
                <select id="filterUnit" class="form-select">
                    <option value="">Semua Unit</option>
                    <option value="UPKD">UPKD</option>
                    <option value="PLTD Bau Bau">PLTD Bau Bau</option>
                    <option value="PLTD Wua Wua">PLTD Wua Wua</option>
                    <option value="PLTD Poasia">PLTD Poasia</option>
                    <option value="PLTD Kolaka">PLTD Kolaka</option>
                </select>

                <select id="filterBidang" class="form-select">
                    <option value="">Semua Bidang</option>
                    <option value="Operasi">Operasi</option>
                    <option value="Pemeliharaan">Pemeliharaan</option>
                    <option value="K3">K3</option>
                    <option value="Lingkungan">Lingkungan</option>
                    <option value="Enjiniring">Enjiniring</option>
                    <option value="Business Support">Business Support</option>
                </select>

                <select id="filterTahun" class="form-select">
                    <option value="">Semua Tahun</option>
                    @for($year = 2025; $year <= 2030; $year++)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <div class="loading-spinner">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
        </div>

        <div id="searchResults" class="results-container">
            @include('notulen._search_results', ['notulen' => $notulen])
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Set default bulan dan tahun sesuai tanggal hari ini
        const now = new Date();
        const bulan = now.getMonth() + 1; // getMonth() 0-based
        const tahun = now.getFullYear();
        const bulanSelect = document.getElementById('bulan');
        const tahunSelect = document.getElementById('tahun');
        if (bulanSelect && !bulanSelect.value) {
            bulanSelect.value = bulan;
        }
        if (tahunSelect && !tahunSelect.value) {
            // Cek apakah tahun sekarang ada di opsi
            let found = false;
            for (let i = 0; i < tahunSelect.options.length; i++) {
                if (tahunSelect.options[i].value == tahun) {
                    found = true;
                    break;
                }
            }
            if (found) tahunSelect.value = tahun;
        }

        // Update preview setelah set default
        updateFormatPreview();

        // Tab switching
        const tabs = document.querySelectorAll('.tab');
        const tabContents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const targetTab = tab.dataset.tab;

                // Update active tab
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                // Show/hide content
                tabContents.forEach(content => {
                    content.id === `${targetTab}Tab`
                        ? content.classList.remove('hidden')
                        : content.classList.add('hidden');
                });
            });
        });

        // Search functionality
        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        const filterUnit = document.getElementById('filterUnit');
        const filterBidang = document.getElementById('filterBidang');
        const filterTahun = document.getElementById('filterTahun');
        const searchResults = document.getElementById('searchResults');
        const loadingSpinner = document.querySelector('.loading-spinner');

        function performSearch() {
            const searchTerm = searchInput.value.trim();
            const unit = filterUnit.value.trim();
            const bidang = filterBidang.value.trim();
            const tahun = filterTahun.value.trim();

            loadingSpinner.classList.add('active');

            // Build query string, only include non-empty values
            const params = new URLSearchParams();
            if (searchTerm) params.append('search', searchTerm);
            if (unit) params.append('unit', unit);
            if (bidang) params.append('bidang', bidang);
            if (tahun) params.append('tahun', tahun);

            fetch(`/notulen/search?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    searchResults.innerHTML = data.html;
                } else {
                    searchResults.innerHTML = '<div class="text-center text-red-600 py-4">Terjadi kesalahan saat memuat data</div>';
                }
                loadingSpinner.classList.remove('active');
            })
            .catch(error => {
                console.error('Error:', error);
                loadingSpinner.classList.remove('active');
                searchResults.innerHTML = '<div class="text-center text-red-600 py-4">Terjadi kesalahan saat mencari data</div>';
            });
        }

        // Debounced search
        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 300);
        }

        // Add event listeners for search and filters
        searchInput.addEventListener('input', debounceSearch);
        filterUnit.addEventListener('change', performSearch);
        filterBidang.addEventListener('change', performSearch);
        filterTahun.addEventListener('change', performSearch);

        // Show initial results when switching to search tab
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                if (tab.dataset.tab === 'search') {
                    performSearch();
                }
            });
        });

        // Function to handle pagination
        window.changePage = function(url) {
            loadingSpinner.classList.add('active');

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    searchResults.innerHTML = data.html;
                    // Update URL without page reload
                    window.history.pushState({}, '', url);
                } else {
                    searchResults.innerHTML = '<div class="text-center text-red-600 py-4">Terjadi kesalahan saat memuat data</div>';
                }
                loadingSpinner.classList.remove('active');
                // Scroll to top of results
                searchResults.scrollIntoView({ behavior: 'smooth' });
            })
            .catch(error => {
                console.error('Error:', error);
                loadingSpinner.classList.remove('active');
                searchResults.innerHTML = '<div class="text-center text-red-600 py-4">Terjadi kesalahan saat memuat halaman</div>';
            });
        };

        // Create notulen button handler
        document.getElementById('createNotulenBtn').addEventListener('click', function() {
            const nomor_urut = document.getElementById('nomor_urut').value;
            const unit = document.getElementById('unit').value;
            const bidang = document.getElementById('bidang').value;
            const sub_bidang = document.getElementById('sub_bidang').value;
            const bulan = document.getElementById('bulan').value;
            const tahun = document.getElementById('tahun').value;

            if (!unit || !bidang || !sub_bidang || !bulan || !tahun) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Mohon lengkapi semua field terlebih dahulu!'
                });
                return;
            }

            const params = new URLSearchParams({
                nomor_urut,
                unit,
                bidang,
                sub_bidang,
                bulan,
                tahun
            });

            window.location.href = `{{ route('notulen.create') }}?${params.toString()}`;
        });
    });

    // Format preview update function
    function updateFormatPreview() {
        const nomor = document.getElementById('nomor_urut').value || '-';
        const unit = document.getElementById('unit').value || '-';
        const bidang = document.getElementById('bidang').value || '-';
        const subBidang = document.getElementById('sub_bidang').value || '-';
        const bulan = document.getElementById('bulan').value || '-';
        const tahun = document.getElementById('tahun').value || '-';

        const formatNomor = `${nomor}/${unit}/${bidang}/${subBidang}/${bulan}/${tahun}`;
        document.getElementById('formatPreview').textContent = formatNomor;
    }

    // Add event listeners to all form inputs
    document.querySelectorAll('#createTab select').forEach(element => {
        element.addEventListener('change', updateFormatPreview);
    });

    // Initial preview update
    updateFormatPreview();
</script>
@endsection
