@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.pemeliharaan-sidebar')
    <div id="main-content" class="flex-1 main-content">
        <header class="bg-white shadow-sm sticky top-0">
            <div class="flex justify-between items-center px-6 py-3">
                <div class="flex items-center gap-x-3">
                    <h1 class="text-xl font-semibold text-gray-800">Edit WO Backlog</h1>
                </div>
            </div>
        </header>
        <main class="px-6 pt-6">
            <div class="bg-white rounded-lg shadow p-6 sm:p-3 w-full">
                <form action="{{ route('pemeliharaan.labor-saya.update-backlog', $backlog->id) }}" method="POST" enctype="multipart/form-data" class="w-full">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="labor" value="{{ $backlog->labor }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
                        <div class="w-full">
                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">No WO</label>
                                <input type="text" value="{{ $backlog->no_wo }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" disabled readonly>
                                <input type="hidden" name="no_wo" value="{{ $backlog->no_wo }}">
                            </div>
                            <div class="mb-4">
                                <label for="status" class="block text-gray-700 font-medium mb-2">Status</label>
                                <select name="status" id="status" class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500" required disabled>
                                    <option value="Open" {{ $backlog->status == 'Open' ? 'selected' : '' }}>Open</option>
                                    <option value="Closed" {{ $backlog->status == 'Closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                                <input type="hidden" name="status" value="{{ $backlog->status }}">
                            </div>
                            <div class="mb-4">
                                <label for="deskripsi" class="block text-gray-700 font-medium mb-2">Deskripsi</label>
                                <textarea name="deskripsi" id="deskripsi" class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500 h-24" required readonly>{{ old('deskripsi', $backlog->deskripsi) }}</textarea>
                                <input type="hidden" name="deskripsi" value="{{ old('deskripsi', $backlog->deskripsi) }}">
                            </div>
                            <div class="mb-4">
                                <label for="keterangan" class="block text-gray-700 font-medium mb-2">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500 h-24">{{ old('keterangan', $backlog->keterangan) }}</textarea>
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="mb-4">
                                <label for="kendala" class="block text-gray-700 font-medium mb-2">Kendala</label>
                                <textarea name="kendala" id="kendala" class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500 h-24">{{ old('kendala', $backlog->kendala) }}</textarea>
                            </div>
                            <div class="mb-4">
                                <label for="tindak_lanjut" class="block text-gray-700 font-medium mb-2">Tindak Lanjut</label>
                                <textarea name="tindak_lanjut" id="tindak_lanjut" class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500 h-24">{{ old('tindak_lanjut', $backlog->tindak_lanjut) }}</textarea>
                            </div>
                            @if($backlog->status == 'WMATL')
                            <div id="materialsSection" class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">Material (dari Material Master)</label>
                                <div class="mb-2">
                                    <input type="text" id="materialSearch" placeholder="Cari material..." class="w-full px-3 py-2 border rounded-md" />
                                </div>
                                <div id="materialList" class="max-h-60 overflow-auto border rounded p-2 bg-white">
                                    @foreach($materials as $m)
                                        <div class="flex items-center justify-between py-1 border-b last:border-b-0">
                                            <div>
                                                <span class="font-mono text-sm">{{ $m->stock_code }}</span>
                                                <span class="ml-2">{{ $m->description }}</span>
                                            </div>
                                            <button type="button" class="text-blue-600 text-sm add-material"
                                                data-code="{{ $m->stock_code }}"
                                                data-desc="{{ $m->description }}"
                                                data-statdesc="{{ $m->inventory_statistic_desc }}"
                                                data-statcode="{{ $m->inventory_statistic_code }}">
                                                Tambah
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-3">
                                    <h4 class="font-semibold mb-2">Material dipilih</h4>
                                    <div id="selectedMaterials" class="space-y-2">
                                        @if(is_array($existingMaterials))
                                            @foreach($existingMaterials as $idx => $item)
                                                <div class="flex items-center gap-2">
                                                    <input type="hidden" name="materials[{{ $idx }}][code]" value="{{ $item['code'] ?? '' }}" />
                                                    <span class="px-2 py-1 bg-gray-100 rounded text-sm">{{ $item['code'] ?? '' }}</span>
                                                    <input type="number" step="0.01" name="materials[{{ $idx }}][qty]" value="{{ $item['qty'] ?? 1 }}" class="w-24 px-2 py-1 border rounded" placeholder="Qty" />
                                                    <button type="button" class="text-red-600 remove-material">Hapus</button>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
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
                                    @if($backlog->document_path)
                                    <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                                        <div class="flex-1 flex items-center">
                                            <i class="fas fa-file-alt text-blue-500 mr-2"></i>
                                            <span class="text-sm text-gray-600">Dokumen saat ini</span>
                                        </div>
                                        <a href="#" onclick="openPdfEditor('{{ url('storage/' . $backlog->document_path) }}'); return false;" class="ml-4 inline-flex items-center px-3 py-1.5 bg-yellow-500 text-white text-sm rounded-lg hover:bg-yellow-600 transition-colors">
                                            <i class="fas fa-edit mr-2"></i>
                                            Edit Dokumen
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">Pilih Nama Labor</label>
                                <div class="flex flex-wrap gap-4 bg-gray-50 p-4 rounded border border-gray-200">
                                    @foreach($masterLabors as $labor)
                                        <label class="flex items-center space-x-2 min-w-[200px]">
                                            <input type="checkbox" name="labors[]" value="{{ $labor->nama }} - {{ $labor->bidang }}" {{ (is_array(old('labors', $backlog->labors ?? [])) && in_array($labor->nama . ' - ' . $labor->bidang, old('labors', $backlog->labors ?? []))) ? 'checked' : '' }}>
                                            <span>{{ $labor->nama }} - {{ ucfirst($labor->bidang) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
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
@endsection

@section('scripts')
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
const pdfEditorModal = document.getElementById('pdfEditorModal');
if (pdfEditorModal) {
    pdfEditorModal.addEventListener('mousedown', function(e) {
        if (e.target === pdfEditorModal) {
            closePdfEditor();
        }
    });
}
window.addEventListener('keydown', function(e) {
    if (pdfEditorModal && !pdfEditorModal.classList.contains('hidden') && e.key === 'Escape') {
        closePdfEditor();
    }
});
function saveEditedPdf(blob) {
    const formData = new FormData();
    formData.append('document', blob, '{{ basename($backlog->document_path) }}');
    formData.append('_token', '{{ csrf_token() }}');
    fetch("{{ route('pemeliharaan.labor-saya.update-backlog', $backlog->id) }}", {
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
// Toggle materials section when status == WMATL
function toggleMaterials() {
  const status = document.getElementById('status').value;
  const section = document.getElementById('materialsSection');
  section.style.display = status === 'WMATL' ? 'block' : 'none';
}
document.getElementById('status').addEventListener('change', toggleMaterials);
toggleMaterials();

// Simple client-side filter for material list
const materialSearch = document.getElementById('materialSearch');
if (materialSearch) {
  materialSearch.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#materialList > div').forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  });
}

// Add/remove selected materials
let materialsIndex = document.querySelectorAll('#selectedMaterials > div').length;
document.querySelectorAll('.add-material').forEach(btn => {
  btn.addEventListener('click', function() {
    const code = this.dataset.code;
    const desc = this.dataset.desc;
    const statDesc = this.dataset.statdesc;
    const statCode = this.dataset.statcode;
    const wrap = document.createElement('div');
    wrap.className = 'flex items-center gap-2';
    wrap.innerHTML = `
      <input type="hidden" name="materials[${materialsIndex}][code]" value="${code}" />
      <input type="hidden" name="materials[${materialsIndex}][description]" value="${desc}" />
      <input type="hidden" name="materials[${materialsIndex}][inventory_statistic_desc]" value="${statDesc}" />
      <input type="hidden" name="materials[${materialsIndex}][inventory_statistic_code]" value="${statCode}" />
      <span class="px-2 py-1 bg-gray-100 rounded text-sm">${code} - ${desc}</span>
      <input type="number" step="0.01" name="materials[${materialsIndex}][qty]" value="1" class="w-24 px-2 py-1 border rounded" placeholder="Qty" />
      <button type="button" class="text-red-600 remove-material">Hapus</button>
    `;
    document.getElementById('selectedMaterials').appendChild(wrap);
    materialsIndex++;
    wrap.querySelector('.remove-material').addEventListener('click', () => wrap.remove());
  });
});
document.querySelectorAll('#selectedMaterials .remove-material').forEach(btn => {
  btn.addEventListener('click', function() { this.closest('div').remove(); });
});
</script>
@endsection
<div id="pdfEditorModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-[90vw] h-[90vh] flex flex-col">
        <div class="flex justify-between items-center p-2 border-b">
            <span class="font-bold">Edit Dokumen PDF</span>
            <button onclick="closePdfEditor()" class="text-gray-500 hover:text-red-600 text-xl">&times;</button>
        </div>
        <div class="flex-1 w-full h-full overflow-auto flex items-center justify-center">
            <iframe id="pdfjs-viewer" src="" style="width:100%;height:100%;border:none;"></iframe>
        </div>
    </div>
</div>
