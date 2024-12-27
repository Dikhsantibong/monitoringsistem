<!-- Tombol Print dan Download -->
<div class="bg-gray-50 p-4 rounded-lg mb-4">
    <div class="flex justify-end gap-3">
        <button onclick="printTable()" 
                class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-md transition-colors duration-150 ease-in-out">
            <i class="fas fa-print mr-2"></i>
            Print
        </button>
        <button onclick="downloadPDF()" 
                class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-md transition-colors duration-150 ease-in-out">
            <i class="fas fa-file-pdf mr-2"></i>
            PDF
        </button>
        <button onclick="downloadExcel()" 
                class="inline-flex items-center px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-medium rounded-md transition-colors duration-150 ease-in-out">
            <i class="fas fa-file-excel mr-2"></i>
            Excel
        </button>
    </div>
</div>

@if($scoreCards->isNotEmpty())
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
                    <p class="text-sm ">Kesiapan Panitia:</p>
                    <p class="font-medium text-blue-600">{{ $scoreCards->first()['kesiapan_panitia'] }}%</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Kesiapan Bahan:</p>
                    <p class="font-medium text-green-600">{{ $scoreCards->first()['kesiapan_bahan'] }}%</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Aktivitas Luar:</p>
                    <p class="font-medium text-purple-600">{{ $scoreCards->first()['aktivitas_luar'] }}%</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Score:</p>
                    <p class="font-medium text-red-600">
                        {{ number_format(($scoreCards->first()['kesiapan_panitia'] + 
                           $scoreCards->first()['kesiapan_bahan'] + 
                           $scoreCards->first()['aktivitas_luar']) / 3, 2) }}%
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Table content -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 border-collapse border border-gray-200">
            <thead>
                <tr style="background-color: #0A749B; color: white">
                    <th class="px-6 py-3 text-center text-sm font-medium uppercase">No</th>
                    <th class="px-6 py-3 text-center text-sm font-medium uppercase">Peserta</th>
                    <th class="px-6 py-3 text-center text-sm font-medium uppercase">Awal</th>
                    <th class="px-6 py-3 text-center text-sm font-medium uppercase">Akhir</th>
                    <th class="px-6 py-3 text-center text-sm font-medium uppercase">Score</th>
                    <th class="px-6 py-3 text-center text-sm font-medium uppercase">Keterangan</th>
                </tr>
            </thead>
            <tbody id="score-card-body">
                @foreach($scoreCards->first()['peserta'] as $index => $peserta)
                    <tr>
                        <td class="text-center py-2 whitespace-nowrap border border-gray-300">
                            {{ $loop->iteration }}
                        </td>
                        <td class="py-2 whitespace-nowrap border border-gray-300 px-4">
                            {{ $peserta['jabatan'] }}
                        </td>
                        <td class="text-center py-2 whitespace-nowrap border border-gray-300">
                            {{ $peserta['awal'] }}
                        </td>
                        <td class="text-center py-2 whitespace-nowrap border border-gray-300">
                            {{ $peserta['akhir'] }}
                        </td>
                        <td class="text-center py-2 whitespace-nowrap border border-gray-300">
                            {{ $peserta['skor'] }}
                        </td>
                        <td class="py-2 whitespace-nowrap border border-gray-300 px-4">
                            -
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-center py-4 text-gray-500">
        Tidak ada data yang tersedia untuk tanggal ini
    </div>
@endif