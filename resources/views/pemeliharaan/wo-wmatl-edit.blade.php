@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.pemeliharaan-sidebar')
    <div class="flex-1 main-content">
        <header class="bg-white shadow-sm sticky top-0">
            <div class="flex justify-between items-center px-6 py-3">
                <h1 class="text-xl font-semibold text-gray-800">Edit WO Material (WMATL)</h1>
            </div>
        </header>
        <main class="px-6 pt-6">
            <div class="bg-white rounded-lg shadow p-6 w-full">
                <form id="editWoForm" action="{{ route('pemeliharaan.wo-wmatl.update', $workOrder->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">ID WO</label>
                                <input type="text" value="{{ $workOrder->id }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" disabled>
                            </div>
                            <div class="mb-4">
                                <label for="type" class="block text-gray-700 font-medium mb-2">Type WO</label>
                                <input type="text" name="type" id="type" value="{{ $workOrder->type }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" readonly>
                            </div>
                            <div class="mb-4">
                                <label for="priority" class="block text-gray-700 font-medium mb-2">Priority</label>
                                <input type="text" name="priority" id="priority" value="{{ $workOrder->priority }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" readonly>
                            </div>
                            <div class="mb-4">
                                <label for="labor" class="block text-gray-700 font-medium mb-2">Labor</label>
                                <input type="text" name="labor" id="labor" value="{{ $workOrder->labor }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" readonly>
                            </div>
                            <div class="mb-4">
                                <label for="schedule_start" class="block text-gray-700 font-medium mb-2">Schedule Start</label>
                                <input type="date" name="schedule_start" id="schedule_start" value="{{ date('Y-m-d', strtotime($workOrder->schedule_start)) }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" readonly>
                            </div>
                            <div class="mb-4">
                                <label for="schedule_finish" class="block text-gray-700 font-medium mb-2">Schedule Finish</label>
                                <input type="date" name="schedule_finish" id="schedule_finish" value="{{ date('Y-m-d', strtotime($workOrder->schedule_finish)) }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" readonly>
                            </div>
                        </div>
                        <div>
                            <div class="mb-4">
                                <label for="description" class="block text-gray-700 font-medium mb-2">Deskripsi</label>
                                <textarea name="description" id="description" class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500 h-24" required readonly>{{ $workOrder->description }}</textarea>
                            </div>
                            <div class="mb-4">
                                <label for="kendala" class="block text-gray-700 font-medium mb-2">Kendala</label>
                                <textarea name="kendala" id="kendala" class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500 h-24">{{ $workOrder->kendala }}</textarea>
                            </div>
                            <div class="mb-4">
                                <label for="tindak_lanjut" class="block text-gray-700 font-medium mb-2">Tindak Lanjut</label>
                                <textarea name="tindak_lanjut" id="tindak_lanjut" class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500 h-24">{{ $workOrder->tindak_lanjut }}</textarea>
                            </div>
                            <div class="mb-4">
                                <label for="status" class="block text-gray-700 font-medium mb-2">Status</label>
                                <select name="status" id="status" class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                                    @foreach(['Open', 'Closed', 'Comp', 'APPR', 'WAPPR', 'WMATL'] as $status)
                                    <option value="{{ $status }}" {{ $workOrder->status == $status ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
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
                                    </div>
                                    @if($workOrder->document_path)
                                    <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                                        <div class="flex-1 flex items-center">
                                            <i class="fas fa-file-alt text-blue-500 mr-2"></i>
                                            <span class="text-sm text-gray-600">Dokumen saat ini</span>
                                        </div>
                                        <a href="#" onclick="openPdfEditor('{{ url('storage/' . $workOrder->document_path) }}'); return false;" class="ml-4 inline-flex items-center px-3 py-1.5 bg-yellow-500 text-white text-sm rounded-lg hover:bg-yellow-600 transition-colors">
                                            <i class="fas fa-edit mr-2"></i>
                                            Edit Dokumen
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between space-x-4 mt-6">
                        <a href="{{ route('pemeliharaan.wo-wmatl.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
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
            <span class="font-bold">Edit Dokumen PDF</span>
            <button onclick="closePdfEditor()" class="text-gray-500 hover:text-red-600 text-xl">&times;</button>
        </div>
        <div class="flex-1 w-full h-full overflow-auto flex items-center justify-center">
            <iframe id="pdfjs-viewer" src="" style="width:100%;height:100%;border:none;"></iframe>
        </div>
        <div class="flex justify-end p-4 border-t">
            <button id="savePdfBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan ke Server</button>
        </div>
    </div>
</div>
<script>
let pdfSaved = false;
function openPdfEditor(pdfUrl) {
    pdfSaved = false;
    document.getElementById('pdfEditorModal').classList.remove('hidden');
    document.getElementById('pdfjs-viewer').src = '/pdf.js/web/viewer.html?file=' + encodeURIComponent(pdfUrl);
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
window.addEventListener('keydown', function(e) {
    const pdfEditorModal = document.getElementById('pdfEditorModal');
    if (pdfEditorModal && !pdfEditorModal.classList.contains('hidden') && e.key === 'Escape') {
        closePdfEditor();
    }
});
window.addEventListener('message', function(event) {
    if (event.data && event.data.type === 'save-pdf' && event.data.data) {
        let blob = null;
        if (event.data.data instanceof ArrayBuffer) {
            blob = new Blob([event.data.data], { type: 'application/pdf' });
        } else if (event.data.data instanceof Object) {
            const arr = new Uint8Array(Object.values(event.data.data));
            blob = new Blob([arr], { type: 'application/pdf' });
        }
        if (blob) {
            saveEditedPdf(blob);
        } else {
            alert('Gagal membaca data PDF hasil edit.');
        }
    }
});
function saveEditedPdf(blob) {
    const formData = new FormData();
    formData.append('document', blob, '{{ basename($workOrder->document_path) }}');
    formData.append('_token', '{{ csrf_token() }}');
    fetch("{{ route('pemeliharaan.wo-wmatl.update', $workOrder->id) }}", {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            pdfSaved = true;
            alert('PDF berhasil diupdate di server!');
            closePdfEditor(true);
            window.location.reload();
        } else {
            alert('Gagal upload PDF ke server.');
        }
    })
    .catch(() => { alert('Gagal upload PDF ke server. Silakan cek koneksi atau ulangi.'); });
}
document.getElementById('savePdfBtn').addEventListener('click', function() {
    const iframe = document.getElementById('pdfjs-viewer').contentWindow;
    iframe.postMessage({ type: 'request-save-pdf' }, '*');
});
</script>
@endsection
