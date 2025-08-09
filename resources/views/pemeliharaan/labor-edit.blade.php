@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.pemeliharaan-sidebar')
    <div id="main-content" class="flex-1 main-content">
    <header class="bg-white shadow-sm sticky top-0">
                <div class="flex justify-between items-center px-6 py-3">
                    <div class="flex items-center gap-x-3">
                        <button id="mobile-menu-toggle"
                            class="md:hidden relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-[#009BB9] hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                            aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" aria-hidden="true" data-slot="icon">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                        <button id="desktop-menu-toggle"
                            class="hidden md:block relative items-center justify-center rounded-md text-gray-400 hover:bg-[#009BB9] p-2 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                            aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" aria-hidden="true" data-slot="icon">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-800">Edit Work Order</h1>
                    </div>
                    <div class="flex items-center gap-x-4 relative">
                        <!-- User Dropdown -->
                        <div class="relative">
                            <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                                <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}"
                                    class="w-8 h-8 rounded-full mr-2">
                                <span class="text-gray-700">{{ Auth::user()->name }}</span>
                                <i class="fas fa-caret-down ml-2"></i>
                            </button>
                            <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">
                                <a href="{{ route('user.profile') }}"
                                    class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profile</a>
                                <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
        <main class="px-6 pt-6">
            <div class="bg-white rounded-lg shadow p-6 sm:p-3 w-full">
                <form id="editLaborWoForm" action="{{ route('pemeliharaan.labor-saya.update', $workOrder->id) }}" method="POST" enctype="multipart/form-data" class="w-full">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
                        <!-- Kolom Kiri -->
                        <div class="w-full">
                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">ID WO</label>
                                <input type="text" value="{{ $workOrder->id }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" disabled>
                            </div>
                            <div class="mb-4">
                                <label for="type" class="block text-gray-700 font-medium mb-2">Type WO</label>
                                <select name="type" id="type" class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                                    @foreach(['CM', 'PM', 'PDM', 'PAM', 'OH', 'EJ', 'EM'] as $type)
                                        <option value="{{ $type }}" {{ $workOrder->type == $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="priority" class="block text-gray-700 font-medium mb-2">Priority</label>
                                <select name="priority" id="priority" class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                                    @foreach(['emergency', 'normal', 'outage', 'urgent'] as $priority)
                                        <option value="{{ $priority }}" {{ $workOrder->priority == $priority ? 'selected' : '' }}>{{ ucfirst($priority) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="unit" class="block text-gray-700 font-medium mb-2">Unit</label>
                                <select name="unit" id="unit" class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                                    @foreach($powerPlants as $powerPlant)
                                        <option value="{{ $powerPlant->id }}" {{ $workOrder->power_plant_id == $powerPlant->id ? 'selected' : '' }}>{{ $powerPlant->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="mb-4">
                                    <label for="schedule_start" class="block text-gray-700 font-medium mb-2">Schedule Start</label>
                                    <input type="date" name="schedule_start" id="schedule_start" value="{{ date('Y-m-d', strtotime($workOrder->schedule_start)) }}" class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                                </div>
                                <div class="mb-4">
                                    <label for="schedule_finish" class="block text-gray-700 font-medium mb-2">Schedule Finish</label>
                                    <input type="date" name="schedule_finish" id="schedule_finish" value="{{ date('Y-m-d', strtotime($workOrder->schedule_finish)) }}" class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                                </div>
                            </div>
                        </div>
                        <!-- Kolom Kanan -->
                        <div class="w-full">
                            <div class="mb-4">
                                <label for="description" class="block text-gray-700 font-medium mb-2">Deskripsi</label>
                                <textarea name="description" id="description" class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500 h-24" required>{{ old('description', $workOrder->description) }}</textarea>
                            </div>
                            <div class="mb-4">
                                <label for="kendala" class="block text-gray-700 font-medium mb-2">Kendala</label>
                                <textarea name="kendala" id="kendala" class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500 h-24">{{ old('kendala', $workOrder->kendala) }}</textarea>
                            </div>
                            <div class="mb-4">
                                <label for="tindak_lanjut" class="block text-gray-700 font-medium mb-2">Tindak Lanjut</label>
                                <textarea name="tindak_lanjut" id="tindak_lanjut" class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500 h-24">{{ old('tindak_lanjut', $workOrder->tindak_lanjut) }}</textarea>
                            </div>
                            <div class="mb-4">
                                <label for="document" class="block text-gray-700 font-medium mb-2">Upload Dokumen</label>
                                <div class="flex flex-col space-y-4">
                                    <div class="relative">
                                        <input type="file" name="document" id="document" class="hidden" accept=".pdf,.doc,.docx,.xls,.xlsx">
                                        <label for="document" class="flex items-center justify-center w-full p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all cursor-pointer group">
                                            <div class="flex flex-col items-center">
                                                <i class="fas fa-cloud-upload-alt text-3xl mb-2 text-gray-400 group-hover:text-blue-500"></i>
                                                <span class="text-gray-600 group-hover:text-blue-500">Klik atau seret file ke sini</span>
                                                <span class="text-sm text-gray-500 mt-1">Format: PDF, DOC, DOCX, XLS, XLSX (Maks. 5MB)</span>
                                            </div>
                                        </label>
                                        <!-- File Preview -->
                                        <div id="filePreview" class="hidden mt-3 p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <i class="fas fa-file-alt text-blue-500 mr-2"></i>
                                                    <span id="fileName" class="text-sm text-gray-600"></span>
                                                </div>
                                                <button type="button" id="removeFile" class="text-red-500 hover:text-red-700">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @if($workOrder->document_path)
                                    <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                                        <div class="flex-1 flex items-center">
                                            <i class="fas fa-file-alt text-blue-500 mr-2"></i>
                                            <span class="text-sm text-gray-600">Dokumen saat ini</span>
                                        </div>
                                        <a href="#" onclick="openPdfEditor('{{ url('storage/' . $workOrder->document_path) }}')" class="ml-4 inline-flex items-center px-3 py-1.5 bg-yellow-500 text-white text-sm rounded-lg hover:bg-yellow-600 transition-colors">
                                            <i class="fas fa-edit mr-2"></i>
                                            Lihat Dokumen
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="status" class="block text-gray-700 font-medium mb-2">Status</label>
                                <select name="status" id="status" class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                                    @foreach(['Open', 'Closed', 'Comp', 'APPR', 'WAPPR', 'WMATL'] as $status)
                                        <option value="{{ $status }}" {{ $workOrder->status == $status ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- Tombol Submit dan Kembali -->
                    <div class="flex justify-between space-x-4 mt-6">
                        <a href="{{ route('pemeliharaan.labor-saya') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali
                        </a>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors flex items-center">
                            <i class="fas fa-save mr-2"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
<!-- Modal PDF Editor -->
<div id="pdfEditorModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-[90vw] h-[90vh] flex flex-col">
        <div class="flex justify-between items-center p-2 border-b">
            <span class="font-bold">Lihat Dokumen PDF</span>
            <button onclick="closePdfEditor()" class="text-gray-500 hover:text-red-600 text-xl">&times;</button>
        </div>
        <div class="flex-1 w-full h-full overflow-auto flex items-center justify-center">
            <iframe id="pdfjs-viewer" src="{{ asset('pdf.js/web/viewer.html') }}?file={{ asset('storage/' . $workOrder->document_path) }}" style="width:100%;height:100%;border:none;"></iframe>
        </div>
    </div>
</div>
<!-- Modal Signature -->
<div id="signatureModal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-4 flex flex-col items-center">
        <span class="font-bold mb-2">Gambar Tanda Tangan</span>
        <canvas id="signature-canvas" width="400" height="150" class="border mb-2"></canvas>
        <div class="flex gap-2">
            <button onclick="clearSignature()" class="bg-gray-400 text-white px-3 py-1 rounded hover:bg-gray-500">Bersihkan</button>
            <button onclick="saveSignature()" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Simpan Tanda Tangan</button>
            <button onclick="closeSignatureModal()" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Batal</button>
        </div>
    </div>
</div>
@push('scripts')
<script>
let pdfSaved = false;
function openPdfEditor(pdfUrl) {
    pdfSaved = false;
    document.getElementById('pdfEditorModal').classList.remove('hidden');
    document.getElementById('pdfjs-viewer').src = '{{ asset('pdf.js/web/viewer.html') }}?file=' + encodeURIComponent(pdfUrl);
    // Prevent closing with ESC
    document.body.style.overflow = 'hidden';
}
function closePdfEditor(force = false) {
    if (!pdfSaved && !force) {
        if (!confirm('Anda belum menyimpan perubahan PDF ke server. Yakin ingin keluar tanpa menyimpan?')) {
            return;
        }
    }
    document.getElementById('pdfEditorModal').classList.add('hidden');
    document.body.style.overflow = '';
}
// Cegah klik di luar modal menutup modal tanpa konfirmasi
const pdfEditorModal = document.getElementById('pdfEditorModal');
pdfEditorModal.addEventListener('mousedown', function(e) {
    if (e.target === pdfEditorModal) {
        closePdfEditor();
    }
});
// Cegah ESC menutup modal tanpa konfirmasi
window.addEventListener('keydown', function(e) {
    if (!pdfEditorModal.classList.contains('hidden') && e.key === 'Escape') {
        closePdfEditor();
    }
});
function saveEditedPdf(blob) {
    // Upload ke server
    console.log('saveEditedPdf called, uploading...');
    const formData = new FormData();
    formData.append('document', blob, '{{ basename($workOrder->document_path) }}');
    formData.append('_token', '{{ csrf_token() }}');
    fetch("{{ route('pemeliharaan.labor-saya.update', $workOrder->id) }}", {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        console.log('Server response:', data);
        if (data.success) {
            pdfSaved = true;
            alert('PDF berhasil diupdate di server!');
            closePdfEditor(true);
            window.location.reload();
        } else {
            alert('Gagal upload PDF ke server.');
        }
    })
    .catch((err) => { console.error('Upload error:', err); alert('Gagal upload PDF ke server. Silakan cek koneksi atau ulangi.'); });
}
window.addEventListener('message', function(event) {
    console.log('Received postMessage:', event);
    if (event.data && event.data.type === 'save-pdf' && event.data.data) {
        let blob = null;
        if (event.data.data instanceof ArrayBuffer) {
            blob = new Blob([event.data.data], { type: 'application/pdf' });
        } else if (event.data.data instanceof Object) {
            const arr = new Uint8Array(Object.values(event.data.data));
            blob = new Blob([arr], { type: 'application/pdf' });
        }
        if (blob) {
            console.log('Uploading blob to server:', blob);
            saveEditedPdf(blob);
        } else {
            alert('Gagal membaca data PDF hasil edit.');
        }
    }
});
</script>
@endpush
@endsection
