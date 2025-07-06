@extends('layouts.app')

@section('styles')
<style>
    .notulen-form {
        max-width: 800px;
        margin: 2rem auto;
        padding: 2rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .notulen-header {
        border: 1px solid #000;
        display: flex;
        margin-bottom: 2rem;
    }

    .header-logo {
        display: flex;
        align-items: center;
        border-right: 1px solid #000;
        justify-content: space-between;
    }

    .header-logo img {
        height: 60px;
    }

    .header-text {
        text-align: center;
        justify-content: center;
        font-size: 12px;
        border-right: 1px solid #000;
        width: 50%;
    }

    .header-number {
        padding-left: 0.5rem;
        font-size: 12px;
        width: 60%;
    }

    .header-number .border-bottom {
        margin-left: -0.5rem;
        padding-left: 0.5rem;
        border-bottom: 1px solid #000;
    }

    .header-info {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 0.5rem;
        margin-top: 1rem;
        margin-bottom: 1rem;
    }

    .header-info-item {
        display: contents;
    }

    .header-info-label {
        font-weight: normal;
    }

    .header-info-value {
        margin-left: 0.5rem;
    }

    .notulen-content {
        margin-top: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #374151;
    }

    /* Editor Styles */
    .editor-container {
        background: #ffffff;
        border: 1px solid #D1D5DB;
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .toolbar {
        border-bottom: 1px solid #D1D5DB;
        padding: 10px;
        background: #f9fafb;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }

    .toolbar button {
        background-color: #ffffff;
        border: 1px solid #D1D5DB;
        border-radius: 4px;
        margin: 2px;
        padding: 6px 10px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s;
    }

    .toolbar button:hover {
        background-color: #f3f4f6;
        border-color: #9ca3af;
    }

    .toolbar select {
        margin: 2px;
        padding: 5px;
        border: 1px solid #D1D5DB;
        border-radius: 4px;
    }

    .editor-content {
        min-height: 150px;
        padding: 1rem;
        outline: none;
    }

    .btn-submit {
        background-color: #0095B7;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.375rem;
        border: none;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        background-color: #007a94;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    @if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Oops!</strong>
        <span class="block sm:inline">Ada beberapa kesalahan:</span>
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('notulen.store') }}" method="POST" class="notulen-form" id="notulenForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="nomor_urut" value="{{ request('nomor_urut') }}">
        <input type="hidden" name="unit" value="{{ request('unit') }}">
        <input type="hidden" name="bidang" value="{{ request('bidang') }}">
        <input type="hidden" name="sub_bidang" value="{{ request('sub_bidang') }}">
        <input type="hidden" name="bulan" value="{{ request('bulan') }}">
        <input type="hidden" name="tahun" value="{{ request('tahun') }}">
        <input type="hidden" name="pembahasan" id="pembahasanInput">
        <input type="hidden" name="tindak_lanjut" id="tindakLanjutInput">

        <div class="notulen-header">
            <div class="header-logo">
                <img src="{{ asset('logo/navlogo.png') }}" alt="PLN Logo">
            </div>
            <div class="header-text">
                <div class="border-bottom border-black">PT PLN NUSANTARA POWER</div>
                <div class="border-bottom border-black">INTEGRATED MANAGEMENT SYSTEM</div>
                <div style="font-weight: bold">FORMULIR NOTULEN RAPAT</div>
            </div>
            <div class="header-number">
                <div class="border-bottom border-black">Nomor Dokumen : FMKP - 145 - 13.3.4.a.a.i - 001</div>
                <div class="border-bottom border-black">Tanggal Terbit : {{ now()->format('d-m-Y') }}</div>
                <div>Halaman : 1 dari 1</div>
            </div>
        </div>

        <div class="header-info">
            <div class="header-info-item">
                <span class="header-info-label">Agenda</span>
                <span class="header-info-value">: <input type="text" name="agenda" class="border p-1 w-[620px]" required></span>
            </div>

            <div class="header-info-item">
                <span class="header-info-label">Tempat</span>
                <span class="header-info-value">: <input type="text" name="tempat" class="border p-1 w-[620px]" required></span>
            </div>
            <div class="header-info-item">
                <span class="header-info-label">Peserta</span>
                <span class="header-info-value">: <input type="text" name="peserta" class="border p-1 w-[620px]" required></span>
            </div>
            <div class="header-info-item">
                <span class="header-info-label">Waktu</span>
                <span class="header-info-value">: <input type="time" name="waktu_mulai" class="border p-1" required> - <input type="time" name="waktu_selesai" class="border p-1" required> WIB</span>
            </div>
            <div class="header-info-item">
                <span class="header-info-label">Hari/Tanggal</span>
                <span class="header-info-value">: <input type="date" name="tanggal" class="border p-1" value="{{ now()->format('Y-m-d') }}" required></span>
            </div>


        </div>

        <div class="notulen-content">
            <div class="form-group">
                <label class="form-label">A. Pembahasan</label>
                <div class="editor-container">
                    <div class="toolbar">
                        <button type="button" onclick="execCmd('bold', 'pembahasan')"><b>Bold</b></button>
                        <button type="button" onclick="execCmd('italic', 'pembahasan')"><i>Italic</i></button>
                        <button type="button" onclick="execCmd('underline', 'pembahasan')"><u>Underline</u></button>
                        <button type="button" onclick="execCmd('strikeThrough', 'pembahasan')"><s>Strike</s></button>
                        <button type="button" onclick="execCmd('insertUnorderedList', 'pembahasan')">• List</button>
                        <button type="button" onclick="execCmd('insertOrderedList', 'pembahasan')">1. List</button>
                        <button type="button" onclick="execCmd('justifyLeft', 'pembahasan')">⯇</button>
                        <button type="button" onclick="execCmd('justifyCenter', 'pembahasan')">⬌</button>
                        <button type="button" onclick="execCmd('justifyRight', 'pembahasan')">⯈</button>
                        <button type="button" onclick="execCmd('removeFormat', 'pembahasan')">🧹 Clear</button>
                        <select onchange="execCmd('formatBlock', this.value, 'pembahasan')"
                        class="w-[100px]">
                            <option value="">Format</option>
                            <option value="h1">Judul H1</option>
                            <option value="h2">Judul H2</option>
                            <option value="h3">Judul H3</option>
                            <option value="p">Paragraf</option>
                            <option value="blockquote">Kutipan</option>
                        </select>
                    </div>
                    <div id="pembahasanEditor" class="editor-content" contenteditable="true"></div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">B. Tindak Lanjut</label>
                <div class="editor-container">
                    <div class="toolbar">
                        <button type="button" onclick="execCmd('bold', 'tindakLanjut')"><b>Bold</b></button>
                        <button type="button" onclick="execCmd('italic', 'tindakLanjut')"><i>Italic</i></button>
                        <button type="button" onclick="execCmd('underline', 'tindakLanjut')"><u>Underline</u></button>
                        <button type="button" onclick="execCmd('strikeThrough', 'tindakLanjut')"><s>Strike</s></button>
                        <button type="button" onclick="execCmd('insertUnorderedList', 'tindakLanjut')">• List</button>
                        <button type="button" onclick="execCmd('insertOrderedList', 'tindakLanjut')">1. List</button>
                        <button type="button" onclick="execCmd('justifyLeft', 'tindakLanjut')">⯇</button>
                        <button type="button" onclick="execCmd('justifyCenter', 'tindakLanjut')">⬌</button>
                        <button type="button" onclick="execCmd('justifyRight', 'tindakLanjut')">⯈</button>
                        <button type="button" onclick="execCmd('removeFormat', 'tindakLanjut')">🧹 Clear</button>
                        <select onchange="execCmd('formatBlock', this.value, 'tindakLanjut')" class="w-[100px]">
                            <option value="">Format</option>
                            <option value="h1">Judul H1</option>
                            <option value="h2">Judul H2</option>
                            <option value="h3">Judul H3</option>
                            <option value="p">Paragraf</option>
                            <option value="blockquote">Kutipan</option>
                        </select>
                    </div>
                    <div id="tindakLanjutEditor" class="editor-content" contenteditable="true"></div>
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <div>
                    <p class="font-medium">Mengetahui,</p>
                    <p class="mt-4">Pimpinan Rapat</p>
                    <div class="mt-20">
                        <input type="text" name="pimpinan_rapat_nama" class="border-b border-black text-center" style="min-width: 200px;" required>
                    </div>
                </div>

                <div class="text-right">
                    <p>Kendari, <input type="date" name="tanggal_tanda_tangan" class="border-b border-black" value="{{ now()->format('Y-m-d') }}" required></p>
                    <p class="mt-4">Notulis</p>
                    <div class="mt-20">
                        <input type="text" name="notulis_nama" class="border-b border-black text-center" style="min-width: 200px;" required>
                    </div>
                </div>
            </div>

            <div class="mt-8 text-center">
                <!-- QR Code Button -->
                <button type="button" id="generateQrBtn" onclick="generateQR()" class="bg-green-600 text-white px-4 py-2 rounded-lg flex items-center hover:bg-green-700 mb-4 mx-auto">
                    <i class="fas fa-qrcode mr-2"></i>
                    Generate QR Code Absensi
                </button>

                <!-- Modal QR Code -->
                <div id="qrModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
                    <div class="bg-white p-8 rounded-lg shadow-lg">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold flex items-center">
                                <i class="fas fa-qrcode mr-2"></i>QR Code Absensi
                            </h3>
                            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div id="qrcode-container" class="flex justify-center min-h-[256px] min-w-[256px]"></div>
                        <p class="mt-4 text-sm text-gray-600 text-center">QR Code ini hanya berlaku untuk 24 jam</p>
                    </div>
                </div>

                <!-- Documentation Images Upload -->
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Dokumentasi Rapat
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="documentation_images" class="relative cursor-pointer bg-white rounded-md font-medium text-[#0095B7] hover:text-[#007a94] focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-[#0095B7]">
                                    <span>Upload gambar</span>
                                    <input id="documentation_images" name="documentation_images[]" type="file" class="sr-only" multiple accept="image/*" onchange="previewImages(event)">
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, JPEG up to 2MB</p>
                        </div>
                    </div>
                    <!-- Preview Container -->
                    <div id="imagePreviewContainer" class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4"></div>
                </div>

                <button type="submit" class="btn-submit">
                    Simpan Notulen
                </button>
            </div>
        </div>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    function execCmd(command, editorId, value = null) {
        try {
            const editor = document.getElementById(editorId + 'Editor');
            editor.focus();

            if (command === 'insertUnorderedList' || command === 'insertOrderedList') {
                const selection = window.getSelection();
                const range = selection.getRangeAt(0);

                if (range.collapsed) {
                    const currentNode = range.startContainer;
                    if (currentNode === editor) {
                        const p = document.createElement('p');
                        p.appendChild(document.createTextNode('\u200B'));
                        editor.appendChild(p);
                        range.selectNodeContents(p);
                        selection.removeAllRanges();
                        selection.addRange(range);
                    }
                }
            }

            const result = document.execCommand(command, false, value);

            if (!result) {
                console.warn(`Command ${command} failed to execute`);
            }

            editor.focus();
        } catch (error) {
            console.error('Error executing command:', error);
        }
    }

    document.getElementById('notulenForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const pembahasanContent = document.getElementById('pembahasanEditor').innerHTML;
        const tindakLanjutContent = document.getElementById('tindakLanjutEditor').innerHTML;

        document.getElementById('pembahasanInput').value = pembahasanContent;
        document.getElementById('tindakLanjutInput').value = tindakLanjutContent;

        // Tambahkan data absensi yang tersimpan di session (jika ada)
        const tempToken = sessionStorage.getItem('notulen_temp_token');
        if (tempToken) {
            const attendanceInput = document.createElement('input');
            attendanceInput.type = 'hidden';
            attendanceInput.name = 'temp_token';
            attendanceInput.value = tempToken;
            this.appendChild(attendanceInput);
        }

        this.submit();
    });

    document.addEventListener('DOMContentLoaded', function() {
        const editors = ['pembahasan', 'tindakLanjut'];
        editors.forEach(editorId => {
            const editor = document.getElementById(editorId + 'Editor');
            if (!editor.innerHTML.trim()) {
                editor.innerHTML = '<p></p>';
            }
        });
    });

    function generateQR() {
        const container = document.getElementById('qrcode-container');
        container.innerHTML = '<div class="text-center">Generating QR Code...</div>';
        document.getElementById('qrModal').classList.remove('hidden');

        // Ambil data form yang diperlukan
        const formData = {
            agenda: document.querySelector('input[name="agenda"]').value,
            tempat: document.querySelector('input[name="tempat"]').value,
            tanggal: document.querySelector('input[name="tanggal"]').value,
            waktu_mulai: document.querySelector('input[name="waktu_mulai"]').value,
            waktu_selesai: document.querySelector('input[name="waktu_selesai"]').value,
            pimpinan_rapat_nama: document.querySelector('input[name="pimpinan_rapat_nama"]').value
        };

        // Generate temporary token untuk QR
        const tempToken = 'TEMP-' + Math.random().toString(36).substr(2, 9);

        // Simpan data form ke sessionStorage
        sessionStorage.setItem('notulen_temp_data', JSON.stringify(formData));
        sessionStorage.setItem('notulen_temp_token', tempToken);

        // Generate QR Code dengan URL yang benar termasuk /public
        const baseUrl = '{{ url("/") }}';
        const qrUrl = `${baseUrl}/notulen/attendance/scan/${tempToken}`;

        container.innerHTML = '';
        new QRCode(container, {
            text: qrUrl,
            width: 256,
            height: 256
        });
    }

    function closeModal() {
        // Hapus event listener sebelum menutup modal
        const modal = document.getElementById('qrModal');
        modal.classList.add('hidden');
    }

    // Tambahkan event listener untuk menutup modal saat klik di luar
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('qrModal');
        const modalContent = modal.querySelector('.bg-white');
        const generateBtn = document.getElementById('generateQrBtn');

        if (!modal.classList.contains('hidden') &&
            !modalContent.contains(event.target) &&
            !generateBtn.contains(event.target)) {
            closeModal();
        }
    });

    // Tambahkan event listener untuk tombol escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });

    function previewImages(event) {
        const container = document.getElementById('imagePreviewContainer');
        container.innerHTML = '';

        Array.from(event.target.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg">
                    <button type="button" onclick="this.parentElement.remove()" class="absolute top-0 right-0 bg-red-500 text-white rounded-full p-1 m-1">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                container.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    }
</script>
@push('scripts')
@endpush
@endsection
