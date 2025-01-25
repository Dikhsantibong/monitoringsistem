<!-- Tombol Print dan Download -->
<div class="bg-gray-50 p-4 rounded-lg mb-4">
    <div class="flex justify-end gap-3">
        <button onclick="printTable()" 
                class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-md transition-colors duration-150 ease-in-out">
            <i class="fas fa-print mr-2"></i>
            Print
        </button>

        <!-- Tambahkan tombol Download PDF -->
        <button onclick="downloadPDF()" 
                class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-md transition-colors duration-150 ease-in-out">
            <i class="fas fa-download mr-2"></i>
            Download PDF
        </button>

        <script>
        function printTable() {
            const dateSelect = document.querySelector('#tanggal-filter');
            const date = dateSelect.value;
            
            if (!date) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Pilih tanggal terlebih dahulu'
                });
                return;
            }

            const printUrl = "{{ route('admin.meetings.print') }}?date=" + encodeURIComponent(date);
            const printWindow = window.open(printUrl, '_blank');
            
            // Tunggu halaman selesai dimuat
            printWindow.onload = function() {
                printWindow.print();
            }
        }

        // Tambahkan fungsi downloadPDF
        function downloadPDF() {
            const dateSelect = document.querySelector('#tanggal-filter');
            const date = dateSelect.value;
            
            if (!date) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Pilih tanggal terlebih dahulu'
                });
                return;
            }

            const downloadUrl = "{{ route('admin.meetings.download-pdf') }}?tanggal=" + encodeURIComponent(date);
            window.location.href = downloadUrl;
        }
        </script>
       
    </div>
</div>

@if($scoreCards->isNotEmpty())
    @php
        // Hitung total score peserta
        $totalScorePeserta = collect($scoreCards->first()['peserta'])->sum('skor');
        $maxScorePeserta = count($scoreCards->first()['peserta']) * 100;
        $persentasePeserta = ($totalScorePeserta / $maxScorePeserta) * 100;

        // Hitung total score ketentuan dengan pengecekan null
        $scoreCard = $scoreCards->first();
        $totalScoreKetentuan = 
            ($scoreCard['skor_waktu_mulai'] ?? 100) +
            ($scoreCard['kesiapan_panitia'] ?? 100) +
            ($scoreCard['kesiapan_bahan'] ?? 100) +
            ($scoreCard['aktivitas_luar'] ?? 100) +
            ($scoreCard['gangguan_diskusi'] ?? 100) +
            ($scoreCard['gangguan_keluar_masuk'] ?? 100) +
            ($scoreCard['gangguan_interupsi'] ?? 100) +
            ($scoreCard['ketegasan_moderator'] ?? 100) +
            ($scoreCard['kelengkapan_sr'] ?? 100);
        
        $maxScoreKetentuan = 9 * 100; // 9 item ketentuan
        $persentaseKetentuan = ($totalScoreKetentuan / $maxScoreKetentuan) * 100;

        // Hitung grand total dalam persentase
        $totalMaxScore = $maxScorePeserta + $maxScoreKetentuan;
        $grandTotal = $totalScorePeserta + $totalScoreKetentuan;
        $persentaseTotal = ($grandTotal / $totalMaxScore) * 100;
    @endphp

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Informasi Rapat -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-3">Informasi Rapat</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Tanggal:</p>
                    <p class="font-medium">{{ \Carbon\Carbon::parse($scoreCards->first()['tanggal'])->format('d F Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Lokasi:</p>
                    <p class="font-medium">{{ $scoreCards->first()['lokasi'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Waktu Mulai:</p>
                    <p class="font-medium">{{ \Carbon\Carbon::parse($scoreCards->first()['waktu_mulai'])->format('H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Waktu Selesai:</p>
                    <p class="font-medium">{{ \Carbon\Carbon::parse($scoreCards->first()['waktu_selesai'])->format('H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Ringkasan Score -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-3">Ringkasan Score</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Score Peserta:</p>
                    <p class="font-medium text-blue-600">{{ number_format($persentasePeserta, 1) }}%</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Score Ketentuan:</p>
                    <p class="font-medium text-green-600">{{ number_format($persentaseKetentuan, 1) }}%</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Score:</p>
                    <p class="font-medium text-red-600">{{ number_format($persentaseTotal, 1) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Table content -->
    <div class="overflow-auto bg-white p-4 rounded-lg mb-4 shadow-md ">
        <table class="min-w-full bg-white border">
            <thead>
                <tr style="background-color: #0A749B; color: white;" class="text-center">
                    <th class="border p-2">No</th>
                    <th class="border p-2">Peserta</th>
                    <th class="border p-2">Awal</th>
                    <th class="border p-2">Akhir</th>
                    <th class="border p-2">Skor</th>
                    <th class="border p-2">Keterangan</th>
                </tr>
            </thead>
            <!-- Loader tbody -->
            <tbody id="tableLoader" class="hidden">
                <tr>
                    <td colspan="6" class="text-center py-8">
                        <div class="flex justify-center items-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#009BB9]"></div>
                        </div>
                    </td>
                </tr>
            </tbody>
            <!-- Data tbody -->
            <tbody id="tableData">
                @foreach($scoreCards->first()['peserta'] as $index => $peserta)
                    <tr>
                        <td class="border p-2 text-center">{{ $loop->iteration }}</td>
                        <td class="border p-2">{{ $peserta['jabatan'] }}</td>
                        <td class="border p-2 text-center">{{ $peserta['skor'] == 50 ? 0 : '' }}</td>
                        <td class="border p-2 text-center">{{ $peserta['skor'] == 100 ? 1 : '' }}</td>
                        <td class="border p-2 text-center">{{ $peserta['skor'] }}</td>
                        <td class="border p-2">{{ $peserta['keterangan'] ?? '-' }}</td>
                    </tr>
                @endforeach

                <!-- Waktu Mulai dan Selesai -->
                <tr>
                    <td class="border p-2 text-center">{{ count($scoreCards->first()['peserta']) + 1 }}</td>
                    <td class="border p-2">Ketepatan waktu memulai meeting</td>
                    <td class="border p-2 text-center">Start</td>
                    <td class="border p-2 text-center">{{ \Carbon\Carbon::parse($scoreCards->first()['waktu_mulai'])->format('H:i') }}</td>
                    <td class="border p-2 text-center">{{ $scoreCards->first()['skor_waktu_mulai'] ?? 100 }}</td>
                    <td class="border p-2">100 Jika dimulai tepat waktu. Setiap 3 menit keterlambatan waktu maka skor dikurangi 10.</td>
                </tr>
                <tr>
                    <td class="border p-2 text-center">{{ count($scoreCards->first()['peserta']) + 2 }}</td>
                    <td class="border p-2">Ketepatan waktu mengakhiri meeting (30 menit)</td>
                    <td class="border p-2 text-center">Finish</td>
                    <td class="border p-2 text-center">{{ \Carbon\Carbon::parse($scoreCards->first()['waktu_selesai'])->format('H:i') }}</td>
                    <td class="border p-2 text-center">{{ $scoreCards->first()['skor_waktu_selesai'] ?? 100 }}</td>
                    <td class="border p-2">100 Jika di akhiri tepat waktu. Setiap 3 menit keterlambatan waktu maka skor dikurangi 10.</td>
                </tr>

                <!-- Ketentuan Rapat (dengan penomoran yang disesuaikan) -->
                <tr>
                    <td class="border p-2 text-center">{{ count($scoreCards->first()['peserta']) + 3 }}</td>
                    <td class="border p-2">Kesiapan Panitia</td>
                    <td class="border p-2" colspan="2" style="background-color: #f3f4f6"></td>
                    <td class="border p-2 text-center">{{ $scoreCards->first()['kesiapan_panitia'] }}</td>
                    <td class="border p-2">100 Jika tidak ada komplain dari peserta.</td>
                </tr>
                <tr>
                    <td class="border p-2 text-center">{{ count($scoreCards->first()['peserta']) + 4 }}</td>
                    <td class="border p-2">Kesiapan Bahan</td>
                    <td class="border p-2" colspan="2" style="background-color: #f3f4f6"></td>
                    <td class="border p-2 text-center">{{ $scoreCards->first()['kesiapan_bahan'] }}</td>
                    <td class="border p-2">100 Jika setiap peserta membawa bahan masing-masing.</td>
                </tr>
                <tr>
                    <td class="border p-2 text-center">{{ count($scoreCards->first()['peserta']) + 5 }}</td>
                    <td class="border p-2">Aktivitas Luar</td>
                    <td class="border p-2" colspan="2" style="background-color: #f3f4f6"></td>
                    <td class="border p-2 text-center">{{ $scoreCards->first()['aktivitas_luar'] }}</td>
                    <td class="border p-2">100 Jika tidak ada gangguan HP/LAPTOP/Etc</td>
                </tr>
                <tr>
                    <td class="border p-2 text-center">{{ count($scoreCards->first()['peserta']) + 6 }}</td>
                    <td class="border p-2">Gangguan Diskusi</td>
                    <td class="border p-2" colspan="2" style="background-color: #f3f4f6"></td>
                    <td class="border p-2 text-center">{{ $scoreCards->first()['gangguan_diskusi'] }}</td>
                    <td class="border p-2">100 Jika semua peserta terfokus pada agenda meeting. Setiap 1 gangguan obrolan (diskusi kecil) dari peserta maka skor dikurangi 20.</td>
                </tr>
                <tr>
                    <td class="border p-2 text-center">{{ count($scoreCards->first()['peserta']) + 7 }}</td>
                    <td class="border p-2">Gangguan Keluar Masuk</td>
                    <td class="border p-2" colspan="2" style="background-color: #f3f4f6"></td>
                    <td class="border p-2 text-center">{{ $scoreCards->first()['gangguan_keluar_masuk'] }}</td>
                    <td class="border p-2">100 Jika semua peserta tetap berada di ruangan sampai akhir</td>
                </tr>
                <tr>
                    <td class="border p-2 text-center">{{ count($scoreCards->first()['peserta']) + 8 }}</td>
                    <td class="border p-2">Gangguan Interupsi</td>
                    <td class="border p-2" colspan="2" style="background-color: #f3f4f6"></td>
                    <td class="border p-2 text-center">{{ $scoreCards->first()['gangguan_interupsi'] }}</td>
                    <td class="border p-2">100 Jika tidak ada interupsi dari pihak lainnya. Setiap 1 interupsi dari pihak luar maka skor dikurangi 20.</td>
                </tr>
                <tr>
                    <td class="border p-2 text-center">{{ count($scoreCards->first()['peserta']) + 9 }}</td>
                    <td class="border p-2">Ketegasan Moderator</td>
                    <td class="border p-2" colspan="2" style="background-color: #f3f4f6"></td>
                    <td class="border p-2 text-center">{{ $scoreCards->first()['ketegasan_moderator'] }}</td>
                    <td class="border p-2">Obyektif</td>
                </tr>
                <tr>
                    <td class="border p-2 text-center">{{ count($scoreCards->first()['peserta']) + 10 }}</td>
                    <td class="border p-2">Kelengkapan SR</td>
                    <td class="border p-2" colspan="2" style="background-color: #f3f4f6"></td>
                    <td class="border p-2 text-center">{{ $scoreCards->first()['kelengkapan_sr'] }}</td>
                    <td class="border p-2">Kaidah, Pelaporan Dokumentasi, Upload ke CMMS</td>
                </tr>

                <!-- Total Score -->
                <tr class="bg-gray-50">
                    <td colspan="4" class="border p-2 text-right font-bold">Total Score:</td>
                    <td class="border p-2 text-center font-bold">{{ number_format($persentaseTotal, 1) }}%</td>
                    <td class="border p-2">
                        Peserta: {{ number_format($persentasePeserta, 1) }}% | 
                        Ketentuan: {{ number_format($persentaseKetentuan, 1) }}%
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@else
    <div class="text-center py-4 text-gray-500">
        Tidak ada data yang tersedia untuk tanggal ini
    </div>
@endif