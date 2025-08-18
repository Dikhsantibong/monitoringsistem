@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.pemeliharaan-sidebar')
    <div class="flex-1 main-content">
        <header class="bg-white shadow-sm sticky top-0">
            <div class="flex justify-between items-center px-6 py-3">
                <h1 class="text-xl font-semibold text-gray-800">Edit Katalog</h1>
            </div>
        </header>
        <main class="px-6 pt-6">
            <div class="bg-white rounded-lg shadow p-6 w-full">
                <div class="mb-4">Edit file PDF katalog di bawah ini, lalu klik Simpan untuk memperbarui.</div>
                <div class="w-full h-[80vh] flex justify-center items-center">
                    <iframe id="pdfjs-viewer" src="{{ asset('pdf.js/web/viewer.html') }}?file={{ $pdfUrl }}" style="width:100%;height:100%;border:none;"></iframe>
                </div>
                <div class="mt-4 flex justify-end">
                    <button id="savePdfBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan Perubahan</button>
                </div>
            </div>
        </main>
    </div>
</div>
<script>
document.getElementById('savePdfBtn').addEventListener('click', function() {
    const iframe = document.getElementById('pdfjs-viewer').contentWindow;
    iframe.postMessage({ type: 'request-save-pdf' }, '*');
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
            const formData = new FormData();
            formData.append('pdf', blob, 'katalog.pdf');
            fetch("{{ route('pemeliharaan.katalog.update', $file->id) }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Katalog berhasil diperbarui!');
                    window.location.href = "{{ route('pemeliharaan.katalog.index') }}";
                } else {
                    alert('Gagal menyimpan perubahan katalog.');
                }
            })
            .catch(() => alert('Gagal upload PDF ke server.'));
        } else {
            alert('Gagal membaca data PDF hasil edit.');
        }
    }
});
</script>
@endsection
