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

<div class="container mx-auto px-4 py-8 mt-16">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="initialForm" class="form-container">
        <h2 class="form-title">Form Notulen</h2>

        <div class="form-group">
            <label class="form-label" for="nomor_urut">No Urut</label>
            <input type="text" id="nomor_urut" name="nomor_urut" class="form-input" required value="{{ $nextNomorUrut }}" readonly>
        </div>

        <div class="form-group">
            <label class="form-label" for="unit">Unit</label>
            <select id="unit" name="unit" class="form-select" required>
                <option value="">Pilih Unit</option>
                <option value="UPKD" {{ old('unit') == 'UPKD' ? 'selected' : '' }}>UPKD</option>
                <option value="PLTD Bau Bau" {{ old('unit') == 'PLTD Bau Bau' ? 'selected' : '' }}>PLTD Bau Bau</option>
                <option value="PLTD Wua Wua" {{ old('unit') == 'PLTD Wua Wua' ? 'selected' : '' }}>PLTD Wua Wua</option>
                <option value="PLTD Poasia" {{ old('unit') == 'PLTD Poasia' ? 'selected' : '' }}>PLTD Poasia</option>
                <option value="PLTD Kolaka" {{ old('unit') == 'PLTD Kolaka' ? 'selected' : '' }}>PLTD Kolaka</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="bidang">Bidang</label>
            <select id="bidang" name="bidang" class="form-select" required>
                <option value="">Pilih Bidang</option>
                <option value="Operasi" {{ old('bidang') == 'Operasi' ? 'selected' : '' }}>Operasi</option>
                <option value="Pemeliharaan" {{ old('bidang') == 'Pemeliharaan' ? 'selected' : '' }}>Pemeliharaan</option>
                <option value="K3" {{ old('bidang') == 'K3' ? 'selected' : '' }}>K3</option>
                <option value="Lingkungan" {{ old('bidang') == 'Lingkungan' ? 'selected' : '' }}>Lingkungan</option>
                <option value="Enjiniring" {{ old('bidang') == 'Enjiniring' ? 'selected' : '' }}>Enjiniring</option>
                <option value="Business Support" {{ old('bidang') == 'Business Support' ? 'selected' : '' }}>Business Support</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="sub_bidang">Sub Bidang</label>
            <select id="sub_bidang" name="sub_bidang" class="form-select" required>
                <option value="">Pilih Sub Bidang</option>
                <option value="Rendal har" {{ old('sub_bidang') == 'Rendal har' ? 'selected' : '' }}>Rendal har</option>
                <option value="Rendal outage" {{ old('sub_bidang') == 'Rendal outage' ? 'selected' : '' }}>Rendal outage</option>
                <option value="ICC" {{ old('sub_bidang') == 'ICC' ? 'selected' : '' }}>ICC</option>
                <option value="Pengadaan" {{ old('sub_bidang') == 'Pengadaan' ? 'selected' : '' }}>Pengadaan</option>
                <option value="CBM" {{ old('sub_bidang') == 'CBM' ? 'selected' : '' }}>CBM</option>
                <option value="SO" {{ old('sub_bidang') == 'SO' ? 'selected' : '' }}>SO</option>
                <option value="MMRK" {{ old('sub_bidang') == 'MMRK' ? 'selected' : '' }}>MMRK</option>
                <option value="Rendalop" {{ old('sub_bidang') == 'Rendalop' ? 'selected' : '' }}>Rendalop</option>
                <option value="EP" {{ old('sub_bidang') == 'EP' ? 'selected' : '' }}>EP</option>
                <option value="SDM" {{ old('sub_bidang') == 'SDM' ? 'selected' : '' }}>SDM</option>
                <option value="AKT" {{ old('sub_bidang') == 'AKT' ? 'selected' : '' }}>AKT</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="bulan">Bulan</label>
            <input type="text" id="bulan" name="bulan" class="form-input" value="{{ date('n') }}" readonly>
        </div>

        <div class="form-group">
            <label class="form-label" for="tahun">Tahun</label>
            <input type="text" id="tahun" name="tahun" class="form-input" value="{{ date('Y') }}" readonly>
        </div>

        <div class="form-group">
            <label class="form-label">Format Nomor Preview:</label>
            <div id="formatPreview" class="p-2 bg-gray-100 rounded">-</div>
        </div>

        <button type="button" id="createNotulenBtn" class="btn-create">
            Buat Notulen
        </button>
    </form>
</div>

@endsection

@section('scripts')
<script>
    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Handle create notulen button click
        document.getElementById('createNotulenBtn').addEventListener('click', function() {
            const nomor_urut = document.getElementById('nomor_urut').value;
            const unit = document.getElementById('unit').value;
            const bidang = document.getElementById('bidang').value;
            const sub_bidang = document.getElementById('sub_bidang').value;
            const bulan = document.getElementById('bulan').value;
            const tahun = document.getElementById('tahun').value;

            // Validate all fields are filled
            if (!unit || !bidang || !sub_bidang || !bulan || !tahun) {
                alert('Mohon lengkapi semua field terlebih dahulu');
                return;
            }

            // Redirect to the notulen detail form with parameters
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
    document.querySelectorAll('#initialForm select').forEach(element => {
        element.addEventListener('change', updateFormatPreview);
        element.addEventListener('input', updateFormatPreview);
    });

    // Initial preview update
    updateFormatPreview();
</script>
@endsection
