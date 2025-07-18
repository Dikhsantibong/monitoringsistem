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

    .qr-code-container {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 3rem;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        display: none;
        min-width: 400px;
        text-align: center;
    }

    .qr-code-container h2 {
        font-size: 1.5rem;
        color: #1a1a1a;
        margin-bottom: 2rem;
    }

    .qr-code-container #qrcode {
        display: flex;
        justify-content: center;
        margin: 1rem 0;
    }

    .qr-code-container #qrcode img {
        width: 350px !important;
        height: 350px !important;
    }

    .qr-code-container button {
        margin-top: 2rem;
        padding: 0.75rem 2rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .qr-code-container button:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .attendance-list {
        margin-top: 1rem;
        max-height: 300px;
        overflow-y: auto;
    }

    .attendance-item {
        padding: 0.5rem;
        border-bottom: 1px solid #eee;
    }

    .signature-pad {
        border: 1px solid #ccc;
        border-radius: 4px;
        margin-top: 0.5rem;
    }

    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
        display: none;
    }

    .documentation-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .documentation-item {
        border: 1px solid #eee;
        border-radius: 8px;
        overflow: hidden;
    }

    .documentation-item img {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }

    .documentation-item .caption {
        padding: 0.5rem;
        font-size: 0.875rem;
        color: #666;
    }

    .draft-status {
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 10px 20px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        border-radius: 5px;
        z-index: 1000;
    }

    /* Add loading spinner animation */
    .spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Add image style for editor */
    .editor-content {
        min-height: 150px;
        padding: 1rem;
        outline: none;
    }

    .editor-content img {
        max-width: 100%;
        height: auto;
        margin: 10px 0;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        cursor: pointer;
    }

    .editor-content .image-wrapper {
        position: relative;
        display: inline-block;
        margin: 10px 0;
        max-width: 100%;
    }

    .editor-content .image-wrapper img {
        margin: 0;
    }

    .editor-content .image-wrapper .image-actions {
        position: absolute;
        top: 5px;
        right: 5px;
        display: none;
        background: rgba(0, 0, 0, 0.5);
        border-radius: 4px;
        padding: 5px;
    }

    .editor-content .image-wrapper:hover .image-actions {
        display: flex;
        gap: 5px;
    }

    .editor-content .image-wrapper .image-actions button {
        background: white;
        border: none;
        border-radius: 4px;
        padding: 4px 8px;
        font-size: 12px;
        cursor: pointer;
        color: #333;
    }

    .editor-content .image-wrapper .image-actions button:hover {
        background: #f0f0f0;
    }

    /* Image preview modal */
    .image-preview-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.8);
        z-index: 1100;
        padding: 2rem;
    }

    .image-preview-modal img {
        max-width: 90%;
        max-height: 90vh;
        margin: auto;
        display: block;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .image-preview-modal .close-button {
        position: absolute;
        top: 1rem;
        right: 1rem;
        color: white;
        font-size: 2rem;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0.5rem;
    }

    .image-preview-modal .close-button:hover {
        color: #ddd;
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
        <input type="hidden" name="temp_notulen_id" id="tempNotulenId" value="{{ request('temp_notulen_id') }}">
        <input type="hidden" name="unit" value="{{ $unit }}">
        <input type="hidden" name="bidang" value="{{ $bidang }}">
        <input type="hidden" name="sub_bidang" value="{{ $sub_bidang }}">
        <input type="hidden" name="bulan" value="{{ $bulan }}">
        <input type="hidden" name="tahun" value="{{ $tahun }}">
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
            <div class="header-info-item mt-4">
                <span class="header-info-label">Absensi</span>
                <span class="header-info-value">
                    <button type="button" onclick="showQRCode()" class="bg-blue-500 text-white px-4 py-2 rounded">
                        Buka QR Code Absensi
                    </button>
                </span>
            </div>
            <div class="header-info-item mt-4">
                <span class="header-info-label">Dokumentasi</span>
                <span class="header-info-value">
                    <button type="button" onclick="showDocumentationUpload()" class="bg-green-500 text-white px-4 py-2 rounded">
                        Upload Dokumentasi
                    </button>
                    <button type="button" onclick="showFileUpload()" class="bg-purple-600 text-white px-4 py-2 rounded ml-2">
                        Upload Dokumen (Word/PDF)
                    </button>
                </span>
            </div>
            </div>

        <div id="documentationList" class="documentation-list mt-4">
            <!-- Documentation items will be dynamically added here -->
        </div>
        <div id="fileList" class="documentation-list mt-4">
            <!-- File items will be dynamically added here -->
        </div>

        <div id="attendanceList" class="attendance-list">
            <!-- Attendance items will be dynamically added here -->
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
                <button type="submit" class="btn-submit">
                    Simpan Notulen
                </button>
            </div>
        </div>
    </form>

    <div id="saveStatus" class="draft-status"></div>
</div>

<!-- QR Code Modal -->
<div class="overlay" id="overlay"></div>
<div class="qr-code-container" id="qrCodeContainer">
    <h2 class="text-xl font-bold mb-4">Scan QR Code untuk Absensi</h2>
    <div id="qrcode"></div>
    <button onclick="closeQRCode()" class="mt-4 bg-red-500 text-white px-4 py-2 rounded">
        Tutup QR Code
    </button>
</div>

<!-- Documentation Upload Modal -->
<div class="qr-code-container" id="documentationFormContainer" style="display: none;">
    <h2 class="text-xl font-bold mb-4">Upload Dokumentasi</h2>
    <form id="documentationForm" enctype="multipart/form-data">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Foto</label>
            <input type="file" name="image" accept="image/*" class="border rounded w-full py-2 px-3" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Keterangan</label>
            <textarea name="caption" class="border rounded w-full py-2 px-3" rows="3"></textarea>
        </div>
        <div class="flex justify-end gap-2">
            <button type="button" onclick="closeDocumentationUpload()" class="bg-gray-500 text-white px-4 py-2 rounded">Tutup</button>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Upload</button>
        </div>
    </form>
</div>

<!-- File Upload Modal -->
<div class="qr-code-container" id="fileFormContainer" style="display: none;">
    <h2 class="text-xl font-bold mb-4">Upload Dokumen (Word/PDF)</h2>
    <form id="fileForm" enctype="multipart/form-data">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Pilih File (Word/PDF)</label>
            <input type="file" name="file" accept=".pdf,.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf" class="border rounded w-full py-2 px-3" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Keterangan</label>
            <textarea name="caption" class="border rounded w-full py-2 px-3" rows="3"></textarea>
        </div>
        <div class="flex justify-end gap-2">
            <button type="button" onclick="closeFileUpload()" class="bg-gray-500 text-white px-4 py-2 rounded">Tutup</button>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded">Upload</button>
        </div>
    </form>
</div>

<!-- Attendance Form Modal -->
<div class="qr-code-container" id="attendanceFormContainer" style="display: none;">
    <h2 class="text-xl font-bold mb-4">Form Absensi</h2>
    <form id="attendanceForm">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Nama</label>
            <input type="text" name="name" class="border rounded w-full py-2 px-3" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Jabatan</label>
            <input type="text" name="position" class="border rounded w-full py-2 px-3" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Tanda Tangan</label>
            <canvas id="signaturePad" class="signature-pad" width="400" height="200"></canvas>
            <button type="button" onclick="clearSignature()" class="mt-2 text-sm text-gray-600">Clear</button>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Submit</button>
        </div>
    </form>
</div>

<div class="draft-status" id="draftStatus">
    Draft tersimpan
</div>

<!-- Add modal for image preview -->
<div class="image-preview-modal" id="imagePreviewModal">
    <button class="close-button" onclick="closeImagePreview()">&times;</button>
    <img id="previewModalImage" src="" alt="Preview">
</div>

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

    // Add this function to handle pasted images
    function handlePastedImage(e, editorId) {
        const items = e.clipboardData.items;
        const editor = document.getElementById(editorId);

        for (let i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image') !== -1) {
                // Prevent default paste
                e.preventDefault();

                // Get the pasted image as a blob
                const blob = items[i].getAsFile();
                const reader = new FileReader();

                reader.onload = function(event) {
                    // Show loading indicator
                    const loadingId = 'loading-' + Date.now();
                    const loadingHtml = `<div id="${loadingId}" style="padding: 10px; background: #f0f0f0; border-radius: 4px; margin: 5px 0;">
                        Mengupload gambar... <span class="spinner"></span>
                    </div>`;
                    editor.insertAdjacentHTML('beforeend', loadingHtml);

                    // Upload the image
                    fetch('{{ route("notulen.paste-image") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            image: event.target.result,
                            temp_notulen_id: document.getElementById('tempNotulenId').value
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove loading indicator
                            document.getElementById(loadingId).remove();
                            
                            // Create image wrapper
                            const wrapper = document.createElement('div');
                            wrapper.className = 'image-wrapper';
                            
                            // Create image element
                            const img = document.createElement('img');
                            img.src = data.url;
                            img.style.maxWidth = '100%';
                            img.style.height = 'auto';
                            img.onclick = () => showImagePreview(data.url);
                            
                            // Create actions div
                            const actions = document.createElement('div');
                            actions.className = 'image-actions';
                            actions.innerHTML = `
                                <button onclick="showImagePreview('${data.url}')">
                                    <i class="fas fa-search-plus"></i> Lihat
                                </button>
                                <button onclick="removeImage(this.parentElement.parentElement)">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            `;
                            
                            // Assemble the elements
                            wrapper.appendChild(img);
                            wrapper.appendChild(actions);
                            editor.appendChild(wrapper);
                            editor.appendChild(document.createElement('br'));
                            
                            // Trigger change for auto-save
                            editor.dispatchEvent(new Event('input'));
                        } else {
                            throw new Error(data.message || 'Failed to upload image');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Remove loading indicator and show error
                        document.getElementById(loadingId).innerHTML = `
                            <div style="color: red;">
                                Gagal mengupload gambar: ${error.message}
                                <button onclick="this.parentElement.remove()" style="float: right;">&times;</button>
                            </div>
                        `;
                    });
                };

                reader.readAsDataURL(blob);
                return;
            }
        }
    }

    // Add paste event listeners to editors
    document.addEventListener('DOMContentLoaded', function() {
        const pembahasanEditor = document.getElementById('pembahasanEditor');
        const tindakLanjutEditor = document.getElementById('tindakLanjutEditor');

        pembahasanEditor.addEventListener('paste', function(e) {
            handlePastedImage(e, 'pembahasanEditor');
        });

        tindakLanjutEditor.addEventListener('paste', function(e) {
            handlePastedImage(e, 'tindakLanjutEditor');
        });
    });

    let qrcode;
    let signaturePad;
    let tempNotulenId;

    // Initialize when document loads
    document.addEventListener('DOMContentLoaded', function() {
        // Generate temporary ID for this notulen session if not exists
        const urlParams = new URLSearchParams(window.location.search);
        tempNotulenId = urlParams.get('temp_notulen_id') || localStorage.getItem('lastDraftId') || Date.now().toString();
        document.getElementById('tempNotulenId').value = tempNotulenId;
        console.log('Initialized with tempNotulenId:', tempNotulenId);

        const editors = ['pembahasan', 'tindakLanjut'];
        editors.forEach(editorId => {
            const editor = document.getElementById(editorId + 'Editor');
            if (!editor.innerHTML.trim()) {
                editor.innerHTML = '<p></p>';
            }
        });

        initSignaturePad();

        // Load draft if temp_notulen_id exists
        if (tempNotulenId) {
            loadDraft();
        }

        // Set up form change tracking and auto-save
        setupFormChangeTracking();
    });

    function showQRCode() {
        const overlay = document.getElementById('overlay');
        const container = document.getElementById('qrCodeContainer');

        overlay.style.display = 'block';
        container.style.display = 'block';

        // Clear previous QR code if exists
        const qrcodeDiv = document.getElementById("qrcode");
        qrcodeDiv.innerHTML = '';

        // Generate QR code with the full URL
        const qrUrl = `${window.location.origin}/public/notulen-attendance/${tempNotulenId}`;
        new QRCode(qrcodeDiv, {
            text: qrUrl,
            width: 256,
            height: 256,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    }

    function closeQRCode() {
        const overlay = document.getElementById('overlay');
        const container = document.getElementById('qrCodeContainer');

        overlay.style.display = 'none';
        container.style.display = 'none';
    }

    function initSignaturePad() {
        const canvas = document.getElementById('signaturePad');
        signaturePad = new SignaturePad(canvas);
    }

    function clearSignature() {
        signaturePad.clear();
    }

    document.getElementById('attendanceForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('signature', signaturePad.toDataURL());
        formData.append('temp_notulen_id', tempNotulenId);

        fetch('{{ url("/public/api/notulen-attendance") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add attendance to the list
                addAttendanceToList(data.attendance);
                // Close the form
                document.getElementById('attendanceFormContainer').style.display = 'none';
                // Clear the form
                this.reset();
                signaturePad.clear();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan absensi');
        });
    });

    function addAttendanceToList(attendance) {
        const list = document.getElementById('attendanceList');
        const item = document.createElement('div');
        item.className = 'attendance-item';
        item.innerHTML = `
            <strong>${attendance.name}</strong> - ${attendance.position}
            <input type="hidden" name="attendances[]" value='${JSON.stringify(attendance)}'>
        `;
        list.appendChild(item);
    }

    function showDocumentationUpload() {
        const overlay = document.getElementById('overlay');
        const container = document.getElementById('documentationFormContainer');

        overlay.style.display = 'block';
        container.style.display = 'block';
    }

    function closeDocumentationUpload() {
        const overlay = document.getElementById('overlay');
        const container = document.getElementById('documentationFormContainer');

        overlay.style.display = 'none';
        container.style.display = 'none';
    }

    document.getElementById('documentationForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('temp_notulen_id', tempNotulenId);

        // Show loading state
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = 'Mengupload...';

        // Create error message container if it doesn't exist
        let errorContainer = document.getElementById('documentationErrorContainer');
        if (!errorContainer) {
            errorContainer = document.createElement('div');
            errorContainer.id = 'documentationErrorContainer';
            errorContainer.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4';
            this.appendChild(errorContainer);
        }
        errorContainer.style.display = 'none';

        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').content;
        if (!token) {
            errorContainer.innerHTML = 'CSRF token tidak ditemukan';
            errorContainer.style.display = 'block';
            return;
        }

        // Use absolute path for API endpoint
        fetch('{{ url("/public/api/notulen-documentation") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(async response => {
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Invalid response:', text);
                throw new Error(`Response tidak valid (${response.status}): ${text.substring(0, 100)}`);
            }

            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || `Error ${response.status}: ${data.error || 'Unknown error'}`);
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                // Add documentation to the list
                addDocumentationToList(data.documentation);
                // Close the form
                closeDocumentationUpload();
                // Clear the form and error message
                this.reset();
                errorContainer.style.display = 'none';
            } else {
                throw new Error(data.message || 'Gagal menyimpan dokumentasi');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            errorContainer.innerHTML = `
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">${error.message}</span>
                ${error.stack ? `<pre class="mt-2 text-sm overflow-auto">${error.stack}</pre>` : ''}
            `;
            errorContainer.style.display = 'block';
        })
        .finally(() => {
            // Reset button state
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
    });

    function addDocumentationToList(documentation) {
        const list = document.getElementById('documentationList');
        const item = document.createElement('div');
        item.className = 'documentation-item';

        // Get base URL dynamically
        const baseUrl = window.location.pathname.includes('/public') ? '/public' : '';

        // Handle image path for both environments
        let imageUrl;
        if (documentation.image_url) {
            // Use image_url if provided by the server
            imageUrl = documentation.image_url;
        } else {
            // Construct URL based on image_path
            const storagePath = documentation.image_path.replace('public/', '');
            imageUrl = `${baseUrl}/storage/${storagePath}`;
        }

        item.innerHTML = `
            <img src="${imageUrl}" alt="Documentation" onerror="this.src='${baseUrl}/images/error-image.jpg'">
            <div class="caption">${documentation.caption || ''}</div>
            <input type="hidden" name="documentations[]" value='${JSON.stringify(documentation)}'>
        `;
        list.appendChild(item);
    }

    function showFileUpload() {
        const overlay = document.getElementById('overlay');
        const container = document.getElementById('fileFormContainer');
        overlay.style.display = 'block';
        container.style.display = 'block';
    }

    function closeFileUpload() {
        const overlay = document.getElementById('overlay');
        const container = document.getElementById('fileFormContainer');
        overlay.style.display = 'none';
        container.style.display = 'none';
    }

    document.getElementById('fileForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('temp_notulen_id', tempNotulenId);

        // Show loading state
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = 'Mengupload...';

        // Create error message container if it doesn't exist
        let errorContainer = document.getElementById('fileErrorContainer');
        if (!errorContainer) {
            errorContainer = document.createElement('div');
            errorContainer.id = 'fileErrorContainer';
            errorContainer.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4';
            this.appendChild(errorContainer);
        }
        errorContainer.style.display = 'none';

        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').content;
        if (!token) {
            errorContainer.innerHTML = 'CSRF token tidak ditemukan';
            errorContainer.style.display = 'block';
            return;
        }

        // Use absolute path for API endpoint
        fetch('{{ url("/public/api/notulen-file") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(async response => {
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Invalid response:', text);
                throw new Error(`Response tidak valid (${response.status}): ${text.substring(0, 100)}`);
            }

            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || `Error ${response.status}: ${data.error || 'Unknown error'}`);
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                // Add file to the list
                addFileToList(data.file);
                // Close the form
                closeFileUpload();
                // Clear the form and error message
                this.reset();
                errorContainer.style.display = 'none';
            } else {
                throw new Error(data.message || 'Gagal menyimpan file');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            errorContainer.innerHTML = `
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">${error.message}</span>
                ${error.stack ? `<pre class="mt-2 text-sm overflow-auto">${error.stack}</pre>` : ''}
            `;
            errorContainer.style.display = 'block';
        })
        .finally(() => {
            // Reset button state
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
    });

    function addFileToList(file) {
        const list = document.getElementById('fileList');
        const item = document.createElement('div');
        item.className = 'documentation-item';
        // Icon based on file type
        let icon = '📄';
        if (file.file_type && file.file_type.includes('pdf')) icon = '📰';
        else if (file.file_type && file.file_type.includes('word')) icon = '📝';
        item.innerHTML = `
            <div style="font-size:2rem;text-align:center;">${icon}</div>
            <a href="${file.file_url}" target="_blank" style="font-weight:bold;display:block;">${file.file_name}</a>
            <div class="caption">${file.caption || ''}</div>
            <input type="hidden" name="files[]" value='${JSON.stringify(file)}'>
        `;
        list.appendChild(item);
    }

    let autoSaveTimeout;
    let isAutoSaving = false;
    let hasChanges = false;
    const draftStatus = document.getElementById('draftStatus');

    // Function to show draft status
    function showDraftStatus(message) {
        draftStatus.textContent = message;
        draftStatus.style.display = 'block';
        setTimeout(() => {
            draftStatus.style.display = 'none';
        }, 3000);
    }

    // Function to save draft
    async function saveDraft() {
        if (isAutoSaving || !hasChanges) return; // Don't save if no changes or already saving
        isAutoSaving = true;

        try {
            const formData = new FormData();

            // Get all form inputs
            const agenda = document.querySelector('input[name="agenda"]').value;
            const tempat = document.querySelector('input[name="tempat"]').value;
            const peserta = document.querySelector('input[name="peserta"]').value;
            const waktuMulai = document.querySelector('input[name="waktu_mulai"]').value;
            const waktuSelesai = document.querySelector('input[name="waktu_selesai"]').value;
            const tanggal = document.querySelector('input[name="tanggal"]').value;
            const pembahasan = document.getElementById('pembahasanEditor').innerHTML;
            const tindakLanjut = document.getElementById('tindakLanjutEditor').innerHTML;
            const pimpinanRapatNama = document.querySelector('input[name="pimpinan_rapat_nama"]').value;
            const notulisNama = document.querySelector('input[name="notulis_nama"]').value;
            const tanggalTandaTangan = document.querySelector('input[name="tanggal_tanda_tangan"]').value;

            // Only save if there's actual content
            if (!agenda && !tempat && !peserta && !pembahasan && !tindakLanjut) {
                console.log('No content to save');
                return;
            }

            // Append all form data
            formData.append('temp_notulen_id', tempNotulenId);
            formData.append('agenda', agenda);
            formData.append('tempat', tempat);
            formData.append('peserta', peserta);
            formData.append('waktu_mulai', waktuMulai);
            formData.append('waktu_selesai', waktuSelesai);
            formData.append('tanggal', tanggal);
            formData.append('pembahasan', pembahasan);
            formData.append('tindak_lanjut', tindakLanjut);
            formData.append('pimpinan_rapat_nama', pimpinanRapatNama);
            formData.append('notulis_nama', notulisNama);
            formData.append('tanggal_tanda_tangan', tanggalTandaTangan);

            // Send draft to server
            const baseUrl = window.location.pathname.includes('/public') ? '/public' : '';
            const response = await fetch(`${baseUrl}/api/notulen-draft/save`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                showDraftStatus('Draft tersimpan');
                localStorage.setItem('lastDraftId', tempNotulenId);
                console.log('Draft saved successfully:', tempNotulenId);
                hasChanges = false; // Reset changes flag after successful save
            } else {
                throw new Error(data.message || 'Failed to save draft');
            }
        } catch (error) {
            console.error('Error saving draft:', error);
            showDraftStatus('Gagal menyimpan draft');
        } finally {
            isAutoSaving = false;
        }
    }

    // Track form changes
    function setupFormChangeTracking() {
        const form = document.getElementById('notulenForm');
        const inputs = form.querySelectorAll('input, textarea');
        const editors = [document.getElementById('pembahasanEditor'), document.getElementById('tindakLanjutEditor')];

        // Watch for changes in regular inputs
        inputs.forEach(input => {
            input.addEventListener('input', () => {
                hasChanges = true;
            });
        });

        // Watch for changes in editors
        editors.forEach(editor => {
            editor.addEventListener('input', () => {
                hasChanges = true;
            });
        });

        // Save only when closing window/tab with unsaved changes
        window.addEventListener('beforeunload', async (e) => {
            if (hasChanges && !isAutoSaving) {
                e.preventDefault();
                e.returnValue = '';
                await saveDraft();
            }
        });

        // Set up auto-save every 30 seconds
        setInterval(async () => {
            if (hasChanges) {
                await saveDraft();
            }
        }, 30000); // 30 seconds
    }

    // Function to load draft data
    async function loadDraft() {
        try {
            const response = await fetch(`{{ url('/api/notulen-draft/load') }}/${tempNotulenId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success && data.draft) {
                const draft = data.draft;

                // Fill in form fields with draft data
                if (draft.agenda) document.querySelector('input[name="agenda"]').value = draft.agenda;
                if (draft.tempat) document.querySelector('input[name="tempat"]').value = draft.tempat;
                if (draft.peserta) document.querySelector('input[name="peserta"]').value = draft.peserta;
                if (draft.waktu_mulai) document.querySelector('input[name="waktu_mulai"]').value = draft.waktu_mulai.substring(11, 16);
                if (draft.waktu_selesai) document.querySelector('input[name="waktu_selesai"]').value = draft.waktu_selesai.substring(11, 16);

                // Format tanggal from database (YYYY-MM-DD) to input date format
                if (draft.tanggal) {
                    const tanggalDate = new Date(draft.tanggal);
                    const formattedTanggal = tanggalDate.toISOString().split('T')[0];
                    document.querySelector('input[name="tanggal"]').value = formattedTanggal;
                }

                if (draft.pembahasan) document.getElementById('pembahasanEditor').innerHTML = draft.pembahasan;
                if (draft.tindak_lanjut) document.getElementById('tindakLanjutEditor').innerHTML = draft.tindak_lanjut;
                if (draft.pimpinan_rapat_nama) document.querySelector('input[name="pimpinan_rapat_nama"]').value = draft.pimpinan_rapat_nama;
                if (draft.notulis_nama) document.querySelector('input[name="notulis_nama"]').value = draft.notulis_nama;

                // Format tanggal_tanda_tangan from database (YYYY-MM-DD) to input date format
                if (draft.tanggal_tanda_tangan) {
                    const tandaTanganDate = new Date(draft.tanggal_tanda_tangan);
                    const formattedTandaTangan = tandaTanganDate.toISOString().split('T')[0];
                    document.querySelector('input[name="tanggal_tanda_tangan"]').value = formattedTandaTangan;
                }

                // Update hidden inputs
                document.querySelector('input[name="unit"]').value = draft.unit || '';
                document.querySelector('input[name="bidang"]').value = draft.bidang || '';
                document.querySelector('input[name="sub_bidang"]').value = draft.sub_bidang || '';
                document.querySelector('input[name="bulan"]').value = draft.bulan || '';
                document.querySelector('input[name="tahun"]').value = draft.tahun || '';

                console.log('Draft loaded successfully');
            } else {
                console.warn('No draft data found');
            }
        } catch (error) {
            console.error('Error loading draft:', error);
            // Optionally show error message to user
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Terjadi kesalahan saat memuat draft: ' + error.message
            });
        }
    }

    // Handle form submission
    document.getElementById('notulenForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        // Disable the submit button to prevent double submission
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;

        try {
            const pembahasanContent = document.getElementById('pembahasanEditor').innerHTML;
            const tindakLanjutContent = document.getElementById('tindakLanjutEditor').innerHTML;

            document.getElementById('pembahasanInput').value = pembahasanContent;
            document.getElementById('tindakLanjutInput').value = tindakLanjutContent;

            // Submit the form data using fetch
            const formData = new FormData(this);
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                // Remove draft after successful submission
                const baseUrl = window.location.pathname.includes('/public') ? '/public' : '';
                await fetch(`${baseUrl}/api/notulen-draft/delete/${tempNotulenId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                localStorage.removeItem('lastDraftId');
                hasChanges = false; // Reset changes flag
                console.log('Draft deleted after submission:', tempNotulenId);

                // Redirect to show page
                window.location.href = result.redirect_url;
            } else {
                throw new Error(result.message || 'Gagal menyimpan notulen');
            }
        } catch (error) {
            console.error('Error submitting form:', error);
            alert('Terjadi kesalahan saat menyimpan notulen: ' + error.message);
            // Re-enable the submit button on error
            submitButton.disabled = false;
        }
    });

    function showImagePreview(src) {
        const modal = document.getElementById('imagePreviewModal');
        const modalImg = document.getElementById('previewModalImage');
        modal.style.display = 'block';
        modalImg.src = src;
        document.body.style.overflow = 'hidden';
    }

    function closeImagePreview() {
        const modal = document.getElementById('imagePreviewModal');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function removeImage(wrapper) {
        if (confirm('Apakah Anda yakin ingin menghapus gambar ini?')) {
            wrapper.remove();
            // Trigger change for auto-save
            wrapper.closest('.editor-content').dispatchEvent(new Event('input'));
        }
    }

    // Close preview modal when clicking outside the image
    document.getElementById('imagePreviewModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeImagePreview();
        }
    });

    // Close preview modal with escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImagePreview();
        }
    });
</script>
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
@endpush
@endsection
