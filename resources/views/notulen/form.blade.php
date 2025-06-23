@extends('layouts.app')

@section('styles')
<style>
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

    .form-select, .form-input, .form-textarea {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #D1D5DB;
        border-radius: 0.375rem;
        background-color: #F9FAFB;
    }

    .form-select:focus, .form-input:focus, .form-textarea:focus {
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

    .notes-section {
        display: none;
        margin-top: 2rem;
    }

    .notes-section.active {
        display: block;
    }

    .form-textarea {
        min-height: 150px;
        resize: vertical;
    }

    .btn-next {
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
    }

    .btn-next:hover {
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

<!-- Main Content -->
<div class="pt-20"> <!-- Added padding top to account for fixed navbar -->
    <div class="form-container">
        <h2 class="form-title">Form Notulen</h2>

        <form id="notulenForm">
            <!-- Number Format Section -->
            <div id="formatSection">
                <div class="form-group">
                    <label class="form-label" for="no_urut">No Urut</label>
                    <input type="text" id="no_urut" class="form-input" placeholder="Masukkan nomor urut">
                </div>

                <div class="form-group">
                    <label class="form-label" for="unit">Unit</label>
                    <select id="unit" class="form-select">
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
                    <select id="bidang" class="form-select">
                        <option value="">Pilih Bidang</option>
                        <option value="Operasi">Operasi</option>
                        <option value="Pemeliharaan">Pemeliharaan</option>
                        <option value="K3">K3</option>
                        <option value="Lingkungan">Lingkungan</option>
                        <option value="Enjiniring">Enjiniring</option>
                        <option value="Bussinest Support">Bussinest Support</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="sub_bidang">Sub Bidang</label>
                    <select id="sub_bidang" class="form-select">
                        <option value="">Pilih Sub Bidang</option>
                        <option value="Rendal har">Rendal har</option>
                        <option value="rendal outage">rendal outage</option>
                        <option value="icc">icc</option>
                        <option value="pengadaan">pengadaan</option>
                        <option value="cbm">cbm</option>
                        <option value="so">so</option>
                        <option value="mmrk">mmrk</option>
                        <option value="rendalop">rendalop</option>
                        <option value="ep">ep</option>
                        <option value="sdm">sdm</option>
                        <option value="akt">akt</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="bulan">Bulan</label>
                    <select id="bulan" class="form-select">
                        <option value="">Pilih Bulan</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="tahun">Tahun</label>
                    <select id="tahun" class="form-select">
                        <option value="">Pilih Tahun</option>
                        @for($year = 2025; $year <= 2030; $year++)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>

                <div class="preview-container">
                    <div class="preview-title">Format Nomor:</div>
                    <div class="preview-text" id="formatPreview">0000/UNIT/BIDANG/SUB-BIDANG/00/0000</div>
                </div>

                <button type="button" class="btn-next" id="btnNext">Lanjut ke Pembahasan</button>
            </div>

            <!-- Notes Section -->
            <div id="notesSection" class="notes-section">
                <div class="form-group">
                    <label class="form-label" for="pembahasan">A. Pembahasan</label>
                    <textarea id="pembahasan" class="form-textarea" placeholder="Masukkan poin-poin pembahasan..."></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="tindak_lanjut">B. Tindak Lanjut</label>
                    <textarea id="tindak_lanjut" class="form-textarea" placeholder="Masukkan poin-poin tindak lanjut..."></textarea>
                </div>

                <button type="button" class="btn-next" id="btnBack" style="background-color: #4a5568;">Kembali</button>
                <button type="button" class="btn-next" style="margin-top: 0.5rem;">Simpan Notulen</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('notulenForm');
    const preview = document.getElementById('formatPreview');
    const formatSection = document.getElementById('formatSection');
    const notesSection = document.getElementById('notesSection');
    const btnNext = document.getElementById('btnNext');
    const btnBack = document.getElementById('btnBack');
    const inputs = ['no_urut', 'unit', 'bidang', 'sub_bidang', 'bulan', 'tahun'];

    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    mobileMenuButton.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });

    inputs.forEach(id => {
        document.getElementById(id).addEventListener('change', updatePreview);
        document.getElementById(id).addEventListener('input', updatePreview);
    });

    function updatePreview() {
        const no_urut = document.getElementById('no_urut').value || '0000';
        const unit = document.getElementById('unit').value || 'UNIT';
        const bidang = document.getElementById('bidang').value || 'BIDANG';
        const sub_bidang = document.getElementById('sub_bidang').value || 'SUB-BIDANG';
        const bulan = document.getElementById('bulan').value || '00';
        const tahun = document.getElementById('tahun').value || '0000';

        preview.textContent = `${no_urut}/${unit}/${bidang}/${sub_bidang}/${bulan}/${tahun}`;
    }

    btnNext.addEventListener('click', function() {
        // Validate all fields are filled
        let isValid = true;
        inputs.forEach(id => {
            if (!document.getElementById(id).value) {
                isValid = false;
            }
        });

        if (!isValid) {
            alert('Mohon lengkapi semua field terlebih dahulu');
            return;
        }

        formatSection.style.display = 'none';
        notesSection.classList.add('active');
    });

    btnBack.addEventListener('click', function() {
        formatSection.style.display = 'block';
        notesSection.classList.remove('active');
    });
});
</script>
@endsection
