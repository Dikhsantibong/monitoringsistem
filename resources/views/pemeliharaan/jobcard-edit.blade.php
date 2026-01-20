@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-hidden">
    @include('components.pemeliharaan-sidebar')
    <div id="main-content" class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm">
            <div class="flex justify-between items-center px-6 py-3">
                <div class="flex items-center gap-x-3">
                    <button id="mobile-menu-toggle"
                        class="md:hidden relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-[#009BB9] hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                        aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-800">Edit Jobcard - {{ $wonum }}</h1>
                </div>
                <div class="flex items-center gap-x-4 relative">
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

        <!-- Toolbar -->
        <div class="bg-white border-b px-4 py-2 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ route('pemeliharaan.labor-saya') }}" 
                   class="inline-flex items-center px-3 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <span class="text-gray-600 ml-4">Dokumen: JOBCARD_{{ $wonum }}.pdf</span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('pemeliharaan.jobcard.download', ['path' => $jobcardPath]) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 text-sm">
                    <i class="fas fa-download mr-2"></i> Download
                </a>
                <button id="savePdfBtn" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                </button>
            </div>
        </div>

        <!-- PDF Viewer (Full Height) -->
        <div class="flex-1 bg-gray-200 overflow-hidden">
            <iframe id="pdfjs-viewer" 
                    src="{{ asset('pdf.js/web/viewer.html') }}?file={{ urlencode($jobcardUrl) }}" 
                    style="width:100%;height:100%;border:none;">
            </iframe>
        </div>
    </div>
</div>

<script>
let pdfSaved = false;
const currentPdfPath = '{{ $jobcardPath }}';

function toggleDropdown() {
    var dropdown = document.getElementById('dropdown');
    dropdown.classList.toggle('hidden');
}

document.addEventListener('click', function(event) {
    var userDropdown = document.getElementById('dropdown');
    var userBtn = document.getElementById('dropdownToggle');
    if (userDropdown && !userDropdown.classList.contains('hidden') && !userBtn.contains(event.target) && !userDropdown.contains(event.target)) {
        userDropdown.classList.add('hidden');
    }
});

// Simpan PDF ke server
function saveEditedPdf(blob) {
    console.log('[Jobcard] Uploading edited PDF to server...');
    
    const formData = new FormData();
    formData.append('document', blob, 'JOBCARD_{{ $wonum }}.pdf');
    formData.append('path', currentPdfPath);
    formData.append('_token', '{{ csrf_token() }}');
    
    fetch("{{ route('pemeliharaan.jobcard.update') }}", {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        console.log('[Jobcard] Server response:', data);
        if (data.success) {
            pdfSaved = true;
            alert('Jobcard berhasil disimpan!');
        } else {
            alert('Gagal menyimpan jobcard: ' + (data.message || 'Unknown error'));
        }
    })
    .catch((err) => {
        console.error('[Jobcard] Upload error:', err);
        alert('Gagal menyimpan jobcard. Silakan coba lagi.');
    });
}

// Listen untuk message dari PDF.js viewer
window.addEventListener('message', function(event) {
    console.log('[Jobcard] Received postMessage:', event.data);
    
    // Handle error dari viewer
    if (event.data && event.data.type === 'save-pdf-error') {
        alert('Error dari PDF viewer: ' + (event.data.message || 'Unknown error'));
        return;
    }
    
    if (event.data && event.data.type === 'save-pdf' && event.data.data) {
        let blob = null;
        try {
            if (event.data.data instanceof ArrayBuffer) {
                blob = new Blob([event.data.data], { type: 'application/pdf' });
            } else if (event.data.data instanceof Uint8Array) {
                blob = new Blob([event.data.data], { type: 'application/pdf' });
            } else if (event.data.data instanceof Object) {
                // Convert object to Uint8Array
                const arr = new Uint8Array(Object.values(event.data.data));
                blob = new Blob([arr], { type: 'application/pdf' });
            }
        } catch (err) {
            console.error('[Jobcard] Error creating blob:', err);
        }
        
        if (blob && blob.size > 0) {
            console.log('[Jobcard] Got blob from viewer, size:', blob.size);
            saveEditedPdf(blob);
        } else {
            console.error('[Jobcard] Failed to create blob from viewer data or blob is empty');
            alert('Gagal membaca data PDF hasil edit. Pastikan PDF sudah dimuat dengan benar.');
        }
    }
});

// Tombol Simpan Perubahan
document.getElementById('savePdfBtn').addEventListener('click', function() {
    const iframe = document.getElementById('pdfjs-viewer');
    if (iframe && iframe.contentWindow) {
        console.log('[Jobcard] Requesting PDF save from viewer...');
        iframe.contentWindow.postMessage({ type: 'request-save-pdf' }, '*');
    } else {
        alert('PDF viewer tidak tersedia.');
    }
});

// Warn sebelum meninggalkan halaman jika belum disimpan
window.addEventListener('beforeunload', function(e) {
    if (!pdfSaved) {
        e.preventDefault();
        e.returnValue = 'Perubahan belum disimpan. Yakin ingin meninggalkan halaman?';
        return e.returnValue;
    }
});
</script>
@endsection