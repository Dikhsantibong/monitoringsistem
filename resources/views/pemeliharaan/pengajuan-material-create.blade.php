@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.pemeliharaan-sidebar')
    <div class="flex-1 main-content">
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
                    <h1 class="text-xl font-semibold text-gray-800">Pengajuan Material Baru</h1>
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
            <div class="bg-white rounded-lg shadow p-6 w-full">
                <div class="mb-4">Silakan isi form PDF pengajuan material di bawah ini, lalu klik Simpan.</div>
                <div class="w-full h-[80vh] flex justify-center items-center">
                    <iframe id="pdfjs-viewer" src="{{ asset('pdf.js/web/viewer.html') }}?file={{ asset('template_pengajuan_material/form_pengajuan_material.pdf') }}&hideSaveBtn=1" style="width:100%;height:100%;border:none;"></iframe>
                </div>
                <div class="mt-4 flex justify-end">
                    <button id="savePdfBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan Pengajuan</button>
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
            formData.append('pdf', blob, 'pengajuan_material.pdf');
            fetch("{{ route('pemeliharaan.pengajuan-material.store') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Pengajuan material berhasil disimpan!');
                    window.location.href = "{{ route('pemeliharaan.pengajuan-material.index') }}";
                } else {
                    alert('Gagal menyimpan pengajuan material.');
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
