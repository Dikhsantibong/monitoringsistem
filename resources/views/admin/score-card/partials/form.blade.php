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
    <div class="flex justify-between items-center mb-4">
        <h3 class="font-bold">Jumlah Peserta Meeting</h3>
        <button type="button" id="managePesertaBtn" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
            <i class="fas fa-users-cog mr-1"></i> Kelola Peserta
        </button>
    </div>
    <!-- Daftar peserta yang sudah ada -->
    <div id="pesertaContainer">
        <!-- Peserta akan di-render di sini secara dinamis -->
    </div>
</div>

<!-- Modal Kelola Peserta -->
<div id="pesertaModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-3/4 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Kelola Peserta Rapat</h3>
            <button type="button" id="closeModal" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="mb-4">
            <button type="button" id="addPesertaBtn" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition-colors duration-150">
                <i class="fas fa-plus mr-1"></i> Tambah Peserta
            </button>
            <span class="ml-2 text-gray-600">Total Peserta: <span id="pesertaCount">0</span></span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border-b w-16">No</th>
                        <th class="px-4 py-2 border-b">Jabatan</th>
                        <th class="px-4 py-2 border-b w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody id="pesertaTableBody">
                    <!-- Data peserta akan di-render di sini -->
                </tbody>
            </table>
        </div>
        <div class="mt-4 flex justify-end">
            <button type="button" id="savePesertaBtn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors duration-150">
                <i class="fas fa-save mr-1"></i> Simpan Perubahan
            </button>
        </div>
    </div>
</div>

<!-- Template untuk baris peserta baru -->
<template id="pesertaRowTemplate">
    <tr class="peserta-row">
        <td class="px-4 py-2 nomor"></td>
        <td class="px-4 py-2">
            <input type="text" class="w-full border rounded px-2 py-1 jabatan-input" placeholder="Masukkan Jabatan">
        </td>
        <td class="px-4 py-2">
            <button class="text-red-500 hover:text-red-700 delete-btn">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

<!-- Setelah bagian peserta meeting, tambahkan section baru -->
<div class="border p-4 rounded mt-6">
    <h3 class="font-bold mb-4">Ketentuan Rapat</h3>
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium mb-2">Aktifitas di Luar Kegiatan Meeting</label>
            <input type="number" name="aktifitas_meeting" min="0" max="100" class="w-full border rounded px-3 py-2" placeholder="Skor">
            <p class="text-sm text-gray-500 mt-1">
                100 Jika tidak ada gangguan HP/LAPTOP/Etc. 
                Setiap 1 gangguan dari peserta maka skor dikurangi 30.
            </p>
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">Ketepatan Waktu Memulai Meeting</label>
            <select name="ketepatan_memulai" class="w-full border rounded px-3 py-2">
                <option value="1">START</option>
                <option value="0">TIDAK START</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">Ketepatan Waktu Mengakhiri Meeting</label>
            <select name="ketepatan_mengakhiri" class="w-full border rounded px-3 py-2">
                <option value="1">FINISH</option>
                <option value="0">TIDAK FINISH</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">Gangguan berupa Forum diskusi kecil (berbicara sendiri-sendiri)</label>
            <input type="number" name="gangguan_diskusi" min="0" max="100" class="w-full border rounded px-3 py-2" placeholder="Skor">
            <p class="text-sm text-gray-500 mt-1">
                100 Jika semua peserta terfokus pada agenda meeting.
                Setiap 1 gangguan obrolan (diskusi kecil) dari peserta maka skor dikurangi 20.
            </p>
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">Gangguan berupa keluar-masuk ruangan</label>
            <input type="number" name="gangguan_keluar_masuk" min="0" max="100" class="w-full border rounded px-3 py-2" placeholder="Skor">
            <p class="text-sm text-gray-500 mt-1">
                100 Jika semua peserta tetap berada di ruangan sampai akhir
            </p>
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">Gangguan berupa interupsi dari pihak lain (bukan peserta meeting)</label>
            <input type="number" name="gangguan_interupsi" min="0" max="100" class="w-full border rounded px-3 py-2" placeholder="Skor">
            <p class="text-sm text-gray-500 mt-1">
                100 Jika tidak ada interupsi dari pihak lainnya.
                Setiap 1 interupsi dari pihak luar maka skor dikurangi 20.
            </p>
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">Ketegasan moderator atau Time Keeper</label>
            <input type="number" name="ketegasan_moderator" min="0" max="100" class="w-full border rounded px-3 py-2" placeholder="Skor">
            <p class="text-sm text-gray-500 mt-1">Obyektif</p>
        </div>
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
    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 flex items-center">
        <i class="fas fa-save mr-2"></i> Simpan Score Card
    </button>
    <a href="{{ route('admin.score-card.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Batal
    </a>
</div>
