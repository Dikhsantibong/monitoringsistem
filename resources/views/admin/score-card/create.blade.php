@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    <!-- Sidebar -->
    <aside class="w-64 bg-[#0A749B] shadow-md">
        <div class="p-4">
            <img src="{{ asset('logo/navlogo.png') }}" alt="Logo Aplikasi Rapat Harian" class="w-40 h-15">
        </div>
        <nav class="mt-4">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-[#F3F3F3] text-white' : 'text-white  hover:bg-[#F3F3F3] hover:text-black' }}">
                <i class="fas fa-home mr-3"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.score-card.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.score-card.*') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3] hover:text-black' }}">
                <i class="fas fa-clipboard-list mr-3"></i>
                <span>Score Card Daily</span>
            </a>
            <a href="{{ route('admin.daftar_hadir.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.daftar_hadir.index') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-list mr-3"></i>
                <span>Daftar Hadir</span>
            </a>
            <a href="{{ route('admin.pembangkit.ready') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.pembangkit.ready') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-check mr-3"></i>
                <span>Kesiapan Pembangkit</span>
            </a>
            <a href="{{ route('admin.laporan.sr_wo') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.laporan.sr_wo') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-file-alt mr-3"></i>
                <span>Laporan SR/WO</span>
            </a>
            <a href="{{ route('admin.machine-monitor') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.machine-monitor') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-cogs mr-3"></i>
                <span>Monitor Mesin</span>
            </a>
            <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.users') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-users mr-3"></i>
                <span>Manajemen Pengguna</span>
            </a>
            <a href="{{ route('admin.meetings') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.meetings') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-chart-bar mr-3"></i>
                <span>Laporan Rapat</span>
            </a>
            <a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.settings') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-cog mr-3"></i>
                <span>Pengaturan</span>
            </a>
        </nav>
    </aside>
    

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-6">Tambah Score Card Daily</h2>
                @include('components.timer')
                
                <form action="{{ route('admin.score-card.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 mb-2">Tanggal</label>
                            <input type="date" name="tanggal" class="w-full px-3 py-2 border rounded" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Lokasi</label>
                            <input type="text" name="lokasi" value="Ruang Rapat Rongi" class="w-full px-3 py-2 border rounded" required>
                        </div>
                    </div>

                    <!-- Peserta Meeting -->
                    <div class="border p-4 rounded">
                        <h3 class="font-bold mb-4">Jumlah Peserta Meeting</h3>
                        <div class="space-y-2">
                            <!-- Header kolom -->
                            <div class="grid grid-cols-6 gap-4 items-center mb-2">
                                <div class="text-sm font-bold">Peserta</div>
                                <div class="text-sm font-bold text-center">Awal</div>
                                <div class="text-sm font-bold text-center">Akhir</div>
                                <div class="text-sm font-bold text-center">Skor</div>
                                <div class="text-sm font-bold col-span-2">Keterangan</div>
                            </div>

                            <!-- MANAGER UP -->
                            <div class="grid grid-cols-6 gap-4 items-center">
                                <label class="text-sm">1. MANAGER UP</label>
                                <div class="text-center">
                                    <select name="peserta[manager_up][awal]" class="w-20 border rounded text-center peserta-select" data-group="manager_up">
                                        <option value="1">✓</option>
                                        <option value="0" selected>✗</option>
                                    </select>
                                </div>
                                <div class="text-center">
                                    <select name="peserta[manager_up][akhir]" class="w-20 border rounded text-center peserta-select" data-group="manager_up">
                                        <option value="1">✓</option>
                                        <option value="0" selected>✗</option>
                                    </select>
                                </div>
                                <div class="text-center">
                                    <input type="number" name="peserta[manager_up][skor]" class="w-20 border rounded text-center skor-input" readonly>
                                </div>
                                <div class="col-span-2">
                                    <input type="text" name="peserta[manager_up][keterangan]" class="w-full border rounded px-2 py-1 text-sm keterangan-input" placeholder="Keterangan">
                                </div>
                            </div>

                            <!-- ASMAN OPERASI -->
                            <div class="grid grid-cols-6 gap-4 items-center">
                                <label class="text-sm">2. ASMAN OPERASI</label>
                                <div class="text-center">
                                    <select name="peserta[asman_operasi][awal]" class="w-20 border rounded text-center peserta-select" data-group="asman_operasi">
                                        <option value="1">✓</option>
                                        <option value="0" selected>✗</option>
                                    </select>
                                </div>
                                <div class="text-center">
                                    <select name="peserta[asman_operasi][akhir]" class="w-20 border rounded text-center peserta-select" data-group="asman_operasi">
                                        <option value="1">✓</option>
                                        <option value="0" selected>✗</option>
                                    </select>
                                </div>
                                <div class="text-center">
                                    <input type="number" name="peserta[asman_operasi][skor]" class="w-20 border rounded text-center skor-input" readonly>
                                </div>
                                <div class="col-span-2">
                                    <input type="text" name="peserta[asman_operasi][keterangan]" class="w-full border rounded px-2 py-1 text-sm" placeholder="Keterangan" readonly>
                                </div>
                            </div>

                            <!-- ASMAN PEMELIHARAAN -->
                            <div class="grid grid-cols-6 gap-4 items-center">
                                <label class="text-sm">3. ASMAN PEMELIHARAAN</label>
                                <div class="text-center">
                                    <select name="peserta[asman_pemeliharaan][awal]" class="w-20 border rounded text-center peserta-select" data-group="asman_pemeliharaan">
                                        <option value="1">✓</option>
                                        <option value="0" selected>✗</option>
                                    </select>
                                </div>
                                <div class="text-center">
                                    <select name="peserta[asman_pemeliharaan][akhir]" class="w-20 border rounded text-center peserta-select" data-group="asman_pemeliharaan">
                                        <option value="1">✓</option>
                                        <option value="0" selected>✗</option>
                                    </select>
                                </div>
                                <div class="text-center">
                                    <input type="number" name="peserta[asman_pemeliharaan][skor]" class="w-20 border rounded text-center skor-input" readonly>
                                </div>
                                <div class="col-span-2">
                                    <input type="text" name="peserta[asman_pemeliharaan][keterangan]" class="w-full border rounded px-2 py-1 text-sm" placeholder="Keterangan" readonly>
                                </div>
                            </div>

                            <!-- ASMAN ENJINIRING -->
                            <div class="grid grid-cols-6 gap-4 items-center">
                                <label class="text-sm">4. ASMAN ENJINIRING</label>
                                <div class="text-center">
                                    <select name="peserta[asman_enjiniring][awal]" class="w-20 border rounded text-center peserta-select" data-group="asman_enjiniring">
                                        <option value="1">✓</option>
                                        <option value="0" selected>✗</option>
                                    </select>
                                </div>
                                <div class="text-center">
                                    <select name="peserta[asman_enjiniring][akhir]" class="w-20 border rounded text-center peserta-select" data-group="asman_enjiniring">
                                        <option value="1">✓</option>
                                        <option value="0" selected>✗</option>
                                    </select>
                                </div>
                                <div class="text-center">
                                    <input type="number" name="peserta[asman_enjiniring][skor]" class="w-20 border rounded text-center skor-input" readonly>
                                </div>
                                <div class="col-span-2">
                                    <input type="text" name="peserta[asman_enjiniring][keterangan]" class="w-full border rounded px-2 py-1 text-sm" placeholder="Keterangan" readonly>
                                </div>
                            </div>

                            <!-- TL RENDAL HAR -->
                            <div class="grid grid-cols-6 gap-4 items-center">
                                <label class="text-sm">5. TL RENDAL HAR</label>
                                <div class="text-center">
                                    <select name="peserta[tl_rendal_har][awal]" class="w-20 border rounded text-center peserta-select" data-group="tl_rendal_har">
                                        <option value="1">✓</option>
                                        <option value="0" selected>✗</option>
                                    </select>
                                </div>
                                <div class="text-center">
                                    <select name="peserta[tl_rendal_har][akhir]" class="w-20 border rounded text-center peserta-select" data-group="tl_rendal_har">
                                        <option value="1">✓</option>
                                        <option value="0" selected>✗</option>
                                    </select>
                                </div>
                                <div class="text-center">
                                    <input type="number" name="peserta[tl_rendal_har][skor]" class="w-20 border rounded text-center skor-input" readonly>
                                </div>
                                <div class="col-span-2">
                                    <input type="text" name="peserta[tl_rendal_har][keterangan]" class="w-full border rounded px-2 py-1 text-sm" placeholder="Keterangan" readonly>
                                </div>
                            </div>

                            <!-- TL ICC -->
                            <div class="grid grid-cols-6 gap-4 items-center">
                                <label class="text-sm">6. TL ICC</label>
                                <div class="text-center">
                                    <select name="peserta[tl_icc][awal]" class="w-20 border rounded text-center peserta-select" data-group="tl_icc">
                                        <option value="1">✓</option>
                                        <option value="0" selected>✗</option>
                                    </select>
                                </div>
                                <div class="text-center">
                                    <select name="peserta[tl_icc][akhir]" class="w-20 border rounded text-center peserta-select" data-group="tl_icc">
                                        <option value="1">✓</option>
                                        <option value="0" selected>✗</option>
                                    </select>
                                </div>
                                <div class="text-center">
                                    <input type="number" name="peserta[tl_icc][skor]" class="w-20 border rounded text-center skor-input" readonly>
                                </div>
                                <div class="col-span-2">
                                    <input type="text" name="peserta[tl_icc][keterangan]" class="w-full border rounded px-2 py-1 text-sm" placeholder="Keterangan" readonly>
                                </div>
                            </div>

                            <!-- TL OUTAGE MANAGEMENT -->
                            <div class="grid grid-cols-6 gap-4 items-center">
                                <label class="text-sm">7. TL OUTAGE MANAGEMENT</label>
                                <div class="text-center">
                                    <select name="peserta[tl_outage][awal]" class="w-20 border rounded text-center peserta-select" data-group="tl_outage">
                                        <option value="1">✓</option>
                                        <option value="0" selected>✗</option>
                                    </select>
                                </div>
                                <div class="text-center">
                                    <select name="peserta[tl_outage][akhir]" class="w-20 border rounded text-center peserta-select" data-group="tl_outage">
                                        <option value="1">✓</option>
                                        <option value="0" selected>✗</option>
                                    </select>
                                </div>
                                <div class="text-center">
                                    <input type="number" name="peserta[tl_outage][skor]" class="w-20 border rounded text-center skor-input" readonly>
                                </div>
                                <div class="col-span-2">
                                    <input type="text" name="peserta[tl_outage][keterangan]" class="w-full border rounded px-2 py-1 text-sm" placeholder="Keterangan" readonly>
                                </div>
                            </div>

                            <!-- TL K3 DAN KAM -->
                            <div class="grid grid-cols-6 gap-4 items-center">
                                <label class="text-sm">8. TL K3 DAN KAM</label>
                                <div class="text-center">
                                    <select name="peserta[tl_k3][awal]" class="w-20 border rounded text-center peserta-select" data-group="tl_k3">
                                        <option value="1">✓</option>
                                        <option value="0" selected>✗</option>
                                    </select>
                                </div>
                                <div class="text-center">
                                    <select name="peserta[tl_k3][akhir]" class="w-20 border rounded text-center peserta-select" data-group="tl_k3">
                                        <option value="1">✓</option>
                                        <option value="0" selected>✗</option>
                                    </select>
                                </div>
                                <div class="text-center">
                                    <input type="number" name="peserta[tl_k3][skor]" class="w-20 border rounded text-center skor-input" readonly>
                                </div>
                                <div class="col-span-2">
                                    <input type="text" name="peserta[tl_k3][keterangan]" class="w-full border rounded px-2 py-1 text-sm" placeholder="Keterangan" readonly>
                                </div>
                            </div>

                            <!-- TL LINGKUNGAN -->
                            <div class="grid grid-cols-6 gap-4 items-center">
                                <label class="text-sm">9. TL LINGKUNGAN</label>
                                <div class="text-center">
                                    <select name="peserta[tl_lingkungan][awal]" class="w-20 border rounded text-center peserta-select" data-group="tl_lingkungan">
                                        <option value="1">✓</option>
                                        <option value="0" selected>✗</option>
                                    </select>
                                </div>
                                <div class="text-center">
                                    <select name="peserta[tl_lingkungan][akhir]" class="w-20 border rounded text-center peserta-select" data-group="tl_lingkungan">
                                        <option value="1">✓</option>
                                        <option value="0" selected>✗</option>
                                    </select>
                                </div>
                                <div class="text-center">
                                    <input type="number" name="peserta[tl_lingkungan][skor]" class="w-20 border rounded text-center skor-input" readonly>
                                </div>
                                <div class="col-span-2">
                                    <input type="text" name="peserta[tl_lingkungan][keterangan]" class="w-full border rounded px-2 py-1 text-sm" placeholder="Keterangan" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

              

                    <!-- Setelah bagian peserta meeting, tambahkan section baru -->
                    <div class="border p-4 rounded mt-6">
                        <h3 class="font-bold mb-4">Ketentuan Rapat</h3>
                        
                        <!-- Aktifitas di Luar Kegiatan Meeting -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Aktifitas di Luar Kegiatan Meeting</label>
                                <input type="number" name="aktifitas_meeting" min="0" max="100" class="w-full border rounded px-3 py-2" placeholder="Skor">
                                <p class="text-sm text-gray-500 mt-1">
                                    100 Jika tidak ada gangguan HP/LAPTOP/Etc. 
                                    Setiap 1 gangguan dari peserta maka skor dikurangi 30.
                                </p>
                            </div>

                            <!-- Ketepatan Waktu Memulai Meeting -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Ketepatan Waktu Memulai Meeting</label>
                                <select name="ketepatan_memulai" class="w-full border rounded px-3 py-2">
                                    <option value="1">START</option>
                                    <option value="0">TIDAK START</option>
                                </select>
                            </div>

                            <!-- Ketepatan Waktu Mengakhiri Meeting -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Ketepatan Waktu Mengakhiri Meeting</label>
                                <select name="ketepatan_mengakhiri" class="w-full border rounded px-3 py-2">
                                    <option value="1">FINISH</option>
                                    <option value="0">TIDAK FINISH</option>
                                </select>
                            </div>

                            <!-- Gangguan Forum Diskusi -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Gangguan berupa Forum diskusi kecil (berbicara sendiri-sendiri)</label>
                                <input type="number" name="gangguan_diskusi" min="0" max="100" class="w-full border rounded px-3 py-2" placeholder="Skor">
                                <p class="text-sm text-gray-500 mt-1">
                                    100 Jika semua peserta terfokus pada agenda meeting.
                                    Setiap 1 gangguan obrolan (diskusi kecil) dari peserta maka skor dikurangi 20.
                                </p>
                            </div>

                            <!-- Gangguan Keluar-Masuk -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Gangguan berupa keluar-masuk ruangan</label>
                                <input type="number" name="gangguan_keluar_masuk" min="0" max="100" class="w-full border rounded px-3 py-2" placeholder="Skor">
                                <p class="text-sm text-gray-500 mt-1">
                                    100 Jika semua peserta tetap berada di ruangan sampai akhir
                                </p>
                            </div>

                            <!-- Gangguan Interupsi -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Gangguan berupa interupsi dari pihak lain (bukan peserta meeting)</label>
                                <input type="number" name="gangguan_interupsi" min="0" max="100" class="w-full border rounded px-3 py-2" placeholder="Skor">
                                <p class="text-sm text-gray-500 mt-1">
                                    100 Jika tidak ada interupsi dari pihak lainnya.
                                    Setiap 1 interupsi dari pihak luar maka skor dikurangi 20.
                                </p>
                            </div>

                            <!-- Ketegasan Moderator -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Ketegasan moderator atau Time Keeper</label>
                                <input type="number" name="ketegasan_moderator" min="0" max="100" class="w-full border rounded px-3 py-2" placeholder="Skor">
                                <p class="text-sm text-gray-500 mt-1">Obyektif</p>
                            </div>

                            <!-- Kelengkapan SR -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Kelengkapan SR</label>
                                <input type="number" name="kelengkapan_sr" min="0" max="100" class="w-full border rounded px-3 py-2" placeholder="Skor">
                                <p class="text-sm text-gray-500 mt-1">Kaidah, Pelaporan Dokumentasi, Upload ke CMMS</p>
                            </div>
                        </div>
                    </div>

                    <!-- Waktu -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 mb-2">Waktu Mulai</label>
                            <input type="time" name="waktu_mulai" 
                                   value="{{ $waktuMulai ? \Carbon\Carbon::parse($waktuMulai)->format('H:i') : '' }}" 
                                   class="w-full px-3 py-2 border rounded" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Waktu Selesai</label>
                            <input type="time" name="waktu_selesai" 
                                   value="{{ $waktuSelesai ? \Carbon\Carbon::parse($waktuSelesai)->format('H:i') : '' }}" 
                                   class="w-full px-3 py-2 border rounded" required>
                        </div>
                    </div>

                    <!-- Penilaian -->
                    <div class="border p-4 rounded">
                        <h3 class="font-bold mb-4">Kriteria Penilaian</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm">Kesiapan Panitia Pelaksana Meeting</label>
                                <input type="number" name="kesiapan_panitia" min="0" max="100" class="w-full border rounded">
                            </div>
                            <div>
                                <label class="block text-sm">Kesiapan Bahan/Data Peserta Meeting</label>
                                <input type="number" name="kesiapan_bahan" min="0" max="100" class="w-full border rounded">
                            </div>
                            <!-- Tambahkan kriteria penilaian lainnya -->
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Simpan Score Card
                        </button>
                        <a href="{{ route('admin.score-card.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function calculateScore(group) {
            const awalSelect = document.querySelector(`select[name="peserta[${group}][awal]"]`);
            const akhirSelect = document.querySelector(`select[name="peserta[${group}][akhir]"]`);
            const skorInput = document.querySelector(`input[name="peserta[${group}][skor]"]`);
            const keteranganInput = document.querySelector(`input[name="peserta[${group}][keterangan]"]`);
            
            const awalValue = parseInt(awalSelect.value);
            const akhirValue = parseInt(akhirSelect.value);
            const totalChecks = awalValue + akhirValue;
            
            let score = 0;
            let keterangan = '';
            
            if (totalChecks === 0) {
                score = 0;
                keterangan = `${formatJabatan(group)} tidak hadir dalam rapat`;
            } else if (totalChecks === 1) {
                score = 50;
                if (awalValue === 1) {
                    keterangan = `${formatJabatan(group)} hanya hadir di awal rapat`;
                } else {
                    keterangan = `${formatJabatan(group)} hanya hadir di akhir rapat`;
                }
            } else if (totalChecks === 2) {
                score = 100;
                keterangan = `${formatJabatan(group)} hadir penuh`;
            }
            
            skorInput.value = score;
            keteranganInput.value = keterangan;

            // Buat keterangan bisa diedit manual
            keteranganInput.readOnly = false;
        }

        function formatJabatan(jabatan) {
            return jabatan
                .split('_')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
        }

        // Add event listeners to all select elements
        document.querySelectorAll('.peserta-select').forEach(select => {
            select.addEventListener('change', function() {
                const group = this.getAttribute('data-group');
                calculateScore(group);
            });
        });

        // Initial calculation for all groups
        document.querySelectorAll('.peserta-select').forEach(select => {
            const group = select.getAttribute('data-group');
            calculateScore(group);
        });
    });
    </script>
    @push('scripts')
        
    @endpush
@endsection