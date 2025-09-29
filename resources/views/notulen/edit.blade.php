@extends('layouts.app')

@section('styles')
<style>
    .editor-content {
        min-height: 500px;
        font-family: Arial, sans-serif;
        border: 1px solid #ccc;
        padding: 10px;
        border-radius: 5px;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 font-weight-bold text-primary">Edit Notulen</h5>
                        <a href="{{ route('notulen.show', $notulen->id) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger border-left-danger">
                            <ul class="mb-0 pl-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($notulen->revision_count > 0)
                        <div class="alert alert-info border-left-info mb-4">
                            <i class="fas fa-info-circle"></i>
                            Dokumen ini telah direvisi sebanyak {{ $notulen->revision_count }} kali.
                            Revisi terakhir pada {{ $notulen->revisions()->latest()->first()->created_at->format('d/m/Y H:i') }}
                            oleh {{ $notulen->revisions()->latest()->first()->user->name }}.
                        </div>
                    @endif

                    <form id="editNotulenForm" action="{{ route('notulen.update', $notulen->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tempat" class="form-label font-weight-bold">Tempat</label>
                                    <input type="text" class="form-control @error('tempat') is-invalid @enderror"
                                        id="tempat" name="tempat" value="{{ old('tempat', $notulen->tempat) }}" required>
                                    @error('tempat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="agenda" class="form-label font-weight-bold">Agenda</label>
                                    <input type="text" class="form-control @error('agenda') is-invalid @enderror"
                                        id="agenda" name="agenda" value="{{ old('agenda', $notulen->agenda) }}" required>
                                    @error('agenda')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="peserta" class="form-label font-weight-bold">Peserta</label>
                                    <textarea class="form-control @error('peserta') is-invalid @enderror"
                                        id="peserta" name="peserta" rows="3" required
                                        placeholder="Masukkan nama-nama peserta rapat">{{ old('peserta', $notulen->peserta) }}</textarea>
                                    @error('peserta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tanggal" class="form-label font-weight-bold">Tanggal</label>
                                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror"
                                        id="tanggal" name="tanggal" value="{{ old('tanggal', $notulen->tanggal->format('Y-m-d')) }}" required>
                                    @error('tanggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="waktu_mulai" class="form-label font-weight-bold">Waktu Mulai</label>
                                    <input type="time" class="form-control @error('waktu_mulai') is-invalid @enderror"
                                        id="waktu_mulai" name="waktu_mulai"
                                        value="{{ old('waktu_mulai', \Carbon\Carbon::parse($notulen->waktu_mulai)->format('H:i')) }}" required>
                                    @error('waktu_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="waktu_selesai" class="form-label font-weight-bold">Waktu Selesai</label>
                                    <input type="time" class="form-control @error('waktu_selesai') is-invalid @enderror"
                                        id="waktu_selesai" name="waktu_selesai"
                                        value="{{ old('waktu_selesai', \Carbon\Carbon::parse($notulen->waktu_selesai)->format('H:i')) }}" required>
                                    @error('waktu_selesai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold">Pembahasan</label>
                                    <div class="alert alert-info mb-2">
                                        <small>
                                            <i class="fas fa-info-circle"></i> Panduan Format Penulisan:
                                            <ul class="mb-0 pl-3">
                                                <li>Untuk poin utama, gunakan angka diikuti titik (contoh: "1.", "2.", "3.")</li>
                                                <li>Untuk sub-poin, gunakan huruf kecil diikuti titik (contoh: "a.", "b.", "c.")</li>
                                                <li>Untuk daftar tanpa urutan, gunakan tanda strip (-)</li>
                                                <li>Tekan <b>Enter dua kali</b> untuk membuat baris/point baru</li>
                                                <li>Bisa paste gambar langsung ke editor</li>
                                            </ul>
                                        </small>
                                    </div>
                                    <div id="pembahasanEditor" class="editor-content" contenteditable="true"></div>
                                    <input type="hidden" name="pembahasan" id="pembahasanInput">
                                    @error('pembahasan')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div style="color: #b91c1c; font-size: 0.95rem; margin-top: 0.5rem;">
                                        <i class="fas fa-info-circle"></i> Tekan <b>Enter dua kali</b> untuk membuat baris/point baru.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold">Tindak Lanjut</label>
                                    <div id="tindakLanjutEditor" class="editor-content" contenteditable="true"></div>
                                    <input type="hidden" name="tindak_lanjut" id="tindakLanjutInput">
                                    @error('tindak_lanjut')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div style="color: #b91c1c; font-size: 0.95rem; margin-top: 0.5rem;">
                                        <i class="fas fa-info-circle"></i> Tekan <b>Enter dua kali</b> untuk membuat baris/point baru.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pimpinan_rapat_nama" class="form-label font-weight-bold">Pimpinan Rapat</label>
                                    <input type="text" class="form-control @error('pimpinan_rapat_nama') is-invalid @enderror"
                                        id="pimpinan_rapat_nama" name="pimpinan_rapat_nama"
                                        value="{{ old('pimpinan_rapat_nama', $notulen->pimpinan_rapat_nama) }}" required>
                                    @error('pimpinan_rapat_nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="notulis_nama" class="form-label font-weight-bold">Notulis</label>
                                    <input type="text" class="form-control @error('notulis_nama') is-invalid @enderror"
                                        id="notulis_nama" name="notulis_nama"
                                        value="{{ old('notulis_nama', $notulen->notulis_nama) }}" required>
                                    @error('notulis_nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_tanda_tangan" class="form-label font-weight-bold">Tanggal Tanda Tangan</label>
                                    <input type="date" class="form-control @error('tanggal_tanda_tangan') is-invalid @enderror"
                                        id="tanggal_tanda_tangan" name="tanggal_tanda_tangan"
                                        value="{{ old('tanggal_tanda_tangan', $notulen->tanggal_tanda_tangan->format('Y-m-d')) }}" required>
                                    @error('tanggal_tanda_tangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="revision_reason" class="form-label font-weight-bold">Alasan Perubahan</label>
                                    <input type="text" class="form-control @error('revision_reason') is-invalid @enderror"
                                        id="revision_reason" name="revision_reason"
                                        value="{{ old('revision_reason') }}" required
                                        placeholder="Contoh: Perbaikan typo pada pembahasan">
                                    @error('revision_reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('notulen.show', $notulen->id) }}" class="btn btn-secondary mr-2">
                                        <i class="fas fa-times"></i> Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-save"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Dokumentasi Foto -->
                    <div class="card mt-4">
                        <div class="card-header bg-white py-2"><strong>Dokumentasi Foto</strong></div>
                        <div class="card-body">
                            <div class="row" id="documentationList">
                                @foreach($notulen->documentations as $doc)
                                    <div class="col-md-3 mb-3 documentation-item" data-id="{{ $doc->id }}">
                                        <div class="card h-100">
                                            <img src="{{ asset('storage/' . $doc->image_path) }}" class="card-img-top" style="height:150px;object-fit:cover;">
                                            <div class="card-body p-2">
                                                <div class="small text-muted mb-1">{{ $doc->caption }}</div>
                                                <button type="button" class="btn btn-danger btn-sm btn-block" onclick="deleteDocumentation({{ $doc->id }}, this)">Hapus</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-3">
                                <div class="form-row">
                                    <div class="col">
                                        <input type="file" name="image" accept="image/*" class="form-control" onchange="uploadDocumentation(this)">
                                    </div>
                                    <div class="col">
                                        <input type="text" id="documentationCaption" class="form-control" placeholder="Keterangan foto">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dokumentasi File -->
                    <div class="card mt-4">
                        <div class="card-header bg-white py-2"><strong>Dokumentasi File (Word/PDF)</strong></div>
                        <div class="card-body">
                            <div class="row" id="fileList">
                                @foreach($notulen->files as $file)
                                    <div class="col-md-3 mb-3 file-item" data-id="{{ $file->id }}">
                                        <div class="card h-100 text-center p-2">
                                            <div style="font-size:2rem;">
                                                @if(Str::contains($file->file_type, 'pdf')) 📰
                                                @elseif(Str::contains($file->file_type, 'word')) 📝
                                                @else 📄 @endif
                                            </div>
                                            <div class="font-weight-bold small">{{ $file->file_name }}</div>
                                            <div class="small text-muted mb-1">{{ $file->caption }}</div>
                                            <button type="button" class="btn btn-danger btn-sm btn-block" onclick="deleteFile({{ $file->id }}, this)">Hapus</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-3">
                                <div class="form-row">
                                    <div class="col">
                                        <input type="file" name="file" accept=".pdf,.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf" class="form-control" onchange="uploadFile(this)">
                                    </div>
                                    <div class="col">
                                        <input type="text" id="fileCaption" class="form-control" placeholder="Keterangan file">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code untuk Absensi Terlambat -->
                    <div class="card mt-4 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 font-weight-bold text-primary">
                                <i class="fas fa-qrcode"></i> QR Code Absensi Terlambat
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="qr-code-container text-center">
                                        <div id="qrcode"></div>
                                        <p class="mt-3 text-muted">
                                            <small>Scan QR code ini untuk melakukan absensi terlambat</small>
                                        </p>
                                        <button type="button" onclick="showQRCode()" class="btn btn-primary mt-2">
                                            <i class="fas fa-qrcode"></i> Tampilkan QR Code
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="alert alert-info border-left-info">
                                        <h6 class="font-weight-bold">Petunjuk Penggunaan:</h6>
                                        <ol class="pl-3 mb-0">
                                            <li>Tunjukkan QR code ini kepada peserta yang terlambat</li>
                                            <li>Peserta dapat scan menggunakan smartphone</li>
                                            <li>Isi form yang muncul dengan lengkap</li>
                                            <li>Absensi akan tercatat sebagai "Terlambat"</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Hadir (Attendance List) -->
        <div class="card mt-4 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 font-weight-bold text-primary">
                    <i class="fas fa-users"></i> Daftar Hadir Peserta
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Divisi</th>
                                <th>Waktu Hadir</th>
                                <th>Tanda Tangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($notulen->attendances as $index => $attendance)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $attendance->name }}</td>
                                <td>{{ $attendance->position }}</td>
                                <td>{{ $attendance->division }}</td>
                                <td>{{ $attendance->attended_at->format('H:i') }}</td>
                                <td class="text-center">
                                    @if($attendance->signature)
                                        <img src="{{ $attendance->signature }}" alt="Tanda tangan" style="max-height: 40px;">
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <div class="py-3">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Belum ada peserta yang melakukan absensi
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview Gambar -->
<div class="image-preview-modal" id="imagePreviewModal">
    <button class="close-button" onclick="closeImagePreview()">&times;</button>
    <img id="previewModalImage" src="" alt="Preview">
</div>

@section('styles')
<style>
    .form-label {
        color: #4a5568;
        margin-bottom: 0.5rem;
    }

    .card {
        border: none;
        border-radius: 0.5rem;
    }

    .card-header {
        border-bottom: 1px solid #e2e8f0;
    }

    .form-control {
        border-radius: 0.375rem;
        border: 1px solid #e2e8f0;
        padding: 0.75rem;
    }

    .form-control:focus {
        border-color: #4299e1;
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.15);
    }

    .btn {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
    }

    .alert {
        border-radius: 0.375rem;
        margin-bottom: 1.5rem;
    }

    .border-left-danger {
        border-left: 4px solid #e53e3e;
    }

    .border-left-info {
        border-left: 4px solid #4299e1;
    }

    .invalid-feedback {
        font-size: 0.875rem;
    }

    .qr-code-container {
        padding: 1rem;
        background: #fff;
        border-radius: 0.5rem;
    }

    #qrcode, #qrcodeModal {
        display: inline-block;
        padding: 1rem;
        background: #fff;
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    #qrcodeModal img {
        display: block !important;
        margin: 0 auto !important;
        max-width: 100% !important;
        height: auto !important;
    }

    .text-break {
        word-break: break-all;
    }

    .qr-code-modal {
        max-width: 90%;
        width: 500px;
    }

    /* Override CKEditor styles */
    .ck-editor__editable {
        min-height: 300px !important;
        max-height: none !important;
    }

    .ck-editor__editable_inline {
        overflow: visible !important;
    }

    /* Ensure content is fully visible */
    .ck.ck-editor__main>.ck-editor__editable {
        height: auto !important;
    }

    /* Remove scrollbars */
    .ck.ck-editor__editable:not(.ck-editor__nested-editable) {
        overflow: visible !important;
    }

    /* Textarea formatting styles */
    textarea#pembahasan,
    textarea#tindak_lanjut {
        white-space: pre-wrap;
        tab-size: 4;
        font-size: 14px;
        line-height: 1.6;
    }

    .alert ul {
        margin-top: 0.5rem;
    }

    .alert ul li {
        margin-bottom: 0.25rem;
    }

    .editor-content {
        min-height: 150px;
        padding: 1rem;
        outline: none;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 0.5rem;
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
</style>
@endsection

@push('scripts')
<!-- QR Code Generator Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cek login di awal halaman (server-side tetap harus pakai middleware, ini hanya pelengkap UX)
        @if(!Auth::check())
            // Simpan URL edit ke sessionStorage
            sessionStorage.setItem('redirectAfterLogin', window.location.href);
            // Redirect ke login
            window.location.href = '{{ route('login') }}';
        @endif
    });

    // Initialize CKEditor for rich text fields with proper configuration
    const editorConfig = {
        removePlugins: [
            'CKFinderUploadAdapter', 'CKFinder', 'EasyImage', 'Image', 'ImageCaption', 
            'ImageStyle', 'ImageToolbar', 'ImageUpload', 'Heading', 'Bold', 'Italic',
            'BlockQuote', 'Table', 'MediaEmbed', 'Link'
        ],
        toolbar: {
            items: [
                'bulletedList',
                'numberedList',
                '|',
                'outdent',
                'indent',
                '|',
                'undo',
                'redo'
            ]
        },
        enterMode: CKEDITOR.ENTER_BR,
        removeFormatAttributes: 'style,class,id',
        format_tags: '',
        removeButtons: 'Anchor,Strike,Subscript,Superscript',
        format_tags: '',
        height: '400px',
        width: '100%',
        removeFormatAttributes: 'style,class,id',
        enterMode: CKEDITOR.ENTER_BR,
        shiftEnterMode: CKEDITOR.ENTER_BR
    };

    // Function to clean text content
    function cleanText(text) {
        // Remove any HTML tags
        text = text.replace(/<[^>]*>/g, '');
        
        // Normalize line endings
        text = text.replace(/\r\n|\r|\n/g, '\n');
        
        // Remove multiple consecutive empty lines
        text = text.replace(/\n\s*\n\s*\n/g, '\n\n');
        
        // Trim whitespace
        text = text.trim();
        
        return text;
    }

    // Initialize CKEditor instances
    let pembahasan, tindakLanjut;

    ClassicEditor
        .create(document.querySelector('#pembahasan'), editorConfig)
        .then(editor => {
            pembahasan = editor;
            
            // Set initial data as plain text
            const textArea = document.querySelector('#pembahasan');
            editor.setData(textArea.value);
            
            // Listen for changes and update hidden field
            editor.model.document.on('change:data', () => {
                const data = editor.getData();
                textArea.value = cleanText(data);
            });
        })
        .catch(error => {
            console.error(error);
        });

    ClassicEditor
        .create(document.querySelector('#tindak_lanjut'), editorConfig)
        .then(editor => {
            tindakLanjut = editor;
            
            // Set initial data as plain text
            const textArea = document.querySelector('#tindak_lanjut');
            editor.setData(textArea.value);
            
            // Listen for changes and update hidden field
            editor.model.document.on('change:data', () => {
                const data = editor.getData();
                textArea.value = cleanText(data);
            });
        })
        .catch(error => {
            console.error(error);
        });

    function showQRCode() {
        const qrcodeDiv = document.getElementById('qrcode');
        qrcodeDiv.innerHTML = '';
        // Generate the URL for late attendance
        const baseUrl = window.location.origin;
        const attendanceUrl = `${baseUrl}/public/notulen/late-attendance/{{ $notulen->id }}`;
        // Create QR code
        new QRCode(qrcodeDiv, {
            text: attendanceUrl,
            width: 300,
            height: 300,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
        // Log for debugging
        console.log('QR Code generated with URL:', attendanceUrl);
    }

    function closeQRCode() {
        const overlay = document.getElementById('overlay');
        const container = document.getElementById('qrCodeContainer');

        overlay.style.display = 'none';
        container.style.display = 'none';
    }

    // Function to handle tab key in textareas
    function handleTabKey(e) {
        if (e.key === 'Tab') {
            e.preventDefault();
            const start = this.selectionStart;
            const end = this.selectionEnd;
            this.value = this.value.substring(0, start) + "    " + this.value.substring(end);
            this.selectionStart = this.selectionEnd = start + 4;
        }
    }

    // Add tab handling to textareas
    document.getElementById('pembahasan').addEventListener('keydown', handleTabKey);
    document.getElementById('tindak_lanjut').addEventListener('keydown', handleTabKey);

    // Form submission handling
    document.getElementById('editNotulenForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Basic form validation
        const requiredFields = [
            'tempat', 'agenda', 'peserta', 'tanggal', 'waktu_mulai', 'waktu_selesai',
            'pembahasan', 'tindak_lanjut', 'pimpinan_rapat_nama', 'notulis_nama',
            'tanggal_tanda_tangan', 'revision_reason'
        ];

        let isValid = true;
        requiredFields.forEach(field => {
            const element = document.getElementById(field);
            if (!element.value.trim()) {
                element.classList.add('is-invalid');
                isValid = false;
            } else {
                element.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            Swal.fire({
                title: 'Error!',
                text: 'Mohon lengkapi semua field yang wajib diisi',
                icon: 'error',
                confirmButtonText: 'Ok'
            });
            return;
        }

        // Continue with form submission
        this.submit();
    });

    // Show success message if exists in session
    @if(session('success'))
        Swal.fire({
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    // Show error message if exists in session
    @if(session('error'))
        Swal.fire({
            title: 'Error!',
            text: "{{ session('error') }}",
            icon: 'error',
            confirmButtonText: 'Ok'
        });
    @endif

    // Dokumentasi Foto - Hapus
    function deleteDocumentation(id, btn) {
        if (!confirm('Yakin ingin menghapus dokumentasi ini?')) return;
        const baseUrl = window.location.pathname.includes('/public') ? '/public' : '';
        fetch(`${baseUrl}/api/notulen-documentation/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                btn.closest('.documentation-item').remove();
            } else {
                alert(data.message || 'Gagal menghapus dokumentasi');
            }
        });
    }

    function uploadDocumentation(input) {
        if (!input.files || !input.files[0]) return;

        const formData = new FormData();
        formData.append('image', input.files[0]);
        formData.append('caption', document.getElementById('documentationCaption').value);
        formData.append('notulen_id', '{{ $notulen->id }}');

        // Get base URL dynamically
        const baseUrl = window.location.pathname.includes('/public') ? '/public' : '';

        // Show loading state
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'col-md-3 mb-3 documentation-item loading';
        loadingDiv.innerHTML = `
            <div class="card h-100 text-center p-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        `;
        document.getElementById('documentationList').appendChild(loadingDiv);

        // Use new endpoint for existing notulen
        fetch(`${baseUrl}/api/notulen/{{ $notulen->id }}/documentation`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove loading state
                loadingDiv.remove();
                
                // Add documentation to the list
                const item = document.createElement('div');
                item.className = 'col-md-3 mb-3 documentation-item';
                item.setAttribute('data-id', data.documentation.id);
                item.innerHTML = `
                    <div class="card h-100">
                        <img src="${data.documentation.image_url}" class="card-img-top" style="height:150px;object-fit:cover;">
                        <div class="card-body p-2">
                            <div class="small text-muted mb-1">${data.documentation.caption || ''}</div>
                            <button type="button" class="btn btn-danger btn-sm btn-block" onclick="deleteDocumentation(${data.documentation.id}, this)">Hapus</button>
                        </div>
                    </div>
                `;
                document.getElementById('documentationList').appendChild(item);
                
                // Clear inputs
                input.value = '';
                document.getElementById('documentationCaption').value = '';
            } else {
                throw new Error(data.message || 'Gagal upload dokumentasi');
            }
        })
        .catch(error => {
            loadingDiv.remove();
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: error.message
            });
        });
    }

    // Dokumentasi File - Hapus
    function deleteFile(id, btn) {
        if (!confirm('Yakin ingin menghapus file ini?')) return;
        const baseUrl = window.location.pathname.includes('/public') ? '/public' : '';
        fetch(`${baseUrl}/api/notulen-file/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                btn.closest('.file-item').remove();
            } else {
                alert(data.message || 'Gagal menghapus file');
            }
        });
    }

    function uploadFile(input) {
        if (!input.files || !input.files[0]) return;

        const formData = new FormData();
        formData.append('file', input.files[0]);
        formData.append('caption', document.getElementById('fileCaption').value);
        formData.append('notulen_id', '{{ $notulen->id }}');

        // Get base URL dynamically
        const baseUrl = window.location.pathname.includes('/public') ? '/public' : '';

        // Show loading state
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'col-md-3 mb-3 file-item loading';
        loadingDiv.innerHTML = `
            <div class="card h-100 text-center p-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        `;
        document.getElementById('fileList').appendChild(loadingDiv);

        // Use new endpoint for existing notulen
        fetch(`${baseUrl}/api/notulen/{{ $notulen->id }}/file`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove loading state
                loadingDiv.remove();
                
                // Add file to the list
                const item = document.createElement('div');
                item.className = 'col-md-3 mb-3 file-item';
                item.setAttribute('data-id', data.file.id);
                
                let icon = '📄';
                if (data.file.file_type && data.file.file_type.includes('pdf')) icon = '📰';
                else if (data.file.file_type && data.file.file_type.includes('word')) icon = '📝';
                
                item.innerHTML = `
                    <div class="card h-100 text-center p-2">
                        <div style="font-size:2rem;">${icon}</div>
                        <div class="font-weight-bold small">${data.file.file_name}</div>
                        <div class="small text-muted mb-1">${data.file.caption || ''}</div>
                        <button type="button" class="btn btn-danger btn-sm btn-block" onclick="deleteFile(${data.file.id}, this)">Hapus</button>
                    </div>
                `;
                document.getElementById('fileList').appendChild(item);
                
                // Clear inputs
                input.value = '';
                document.getElementById('fileCaption').value = '';
            } else {
                throw new Error(data.message || 'Gagal upload file');
            }
        })
        .catch(error => {
            loadingDiv.remove();
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: error.message
            });
        });
    }
</script>

@push('scripts')
<script>
// Load initial data to editor
window.addEventListener('DOMContentLoaded', function() {
    document.getElementById('pembahasanEditor').innerHTML = `{!! old('pembahasan', $notulen->getRawOriginal('pembahasan')) !!}`;
    document.getElementById('tindakLanjutEditor').innerHTML = `{!! old('tindak_lanjut', $notulen->getRawOriginal('tindak_lanjut')) !!}`;
    document.getElementById('pembahasanInput').value = document.getElementById('pembahasanEditor').innerHTML;
    document.getElementById('tindakLanjutInput').value = document.getElementById('tindakLanjutEditor').innerHTML;
});

// Sync editor content to hidden input before submit
function cleanEditorHtml(editor) {
    editor.querySelectorAll('.image-actions').forEach(el => el.remove());
}

// Form submission handling
const form = document.getElementById('editNotulenForm');
form.addEventListener('submit', function(e) {
    cleanEditorHtml(document.getElementById('pembahasanEditor'));
    cleanEditorHtml(document.getElementById('tindakLanjutEditor'));
    document.getElementById('pembahasanInput').value = document.getElementById('pembahasanEditor').innerHTML;
    document.getElementById('tindakLanjutInput').value = document.getElementById('tindakLanjutEditor').innerHTML;
});

// Paste image handler (copy dari create.blade.php)
function handlePastedImage(e, editorId) {
    const items = e.clipboardData.items;
    const editor = document.getElementById(editorId);
    for (let i = 0; i < items.length; i++) {
        if (items[i].type.indexOf('image') !== -1) {
            e.preventDefault();
            const blob = items[i].getAsFile();
            const reader = new FileReader();
            reader.onload = function(event) {
                const loadingId = 'loading-' + Date.now();
                const loadingHtml = `<div id="${loadingId}" style="padding: 10px; background: #f0f0f0; border-radius: 4px; margin: 5px 0;">
                    Mengupload gambar... <span class="spinner"></span>
                </div>`;
                editor.insertAdjacentHTML('beforeend', loadingHtml);
                fetch(`{{ route('notulen.paste-image') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        image: event.target.result,
                        temp_notulen_id: 'edit-' + {{ $notulen->id }}
                    })
                })
                .then(async response => {
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        const text = await response.text();
                        throw new Error('Server error: ' + text.substring(0, 200));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        document.getElementById(loadingId).remove();
                        const wrapper = document.createElement('div');
                        wrapper.className = 'image-wrapper';
                        const img = document.createElement('img');
                        img.src = data.url;
                        img.style.maxWidth = '100%';
                        img.style.height = 'auto';
                        img.onclick = () => showImagePreview(data.url);
                        const actions = document.createElement('div');
                        actions.className = 'image-actions';
                        actions.innerHTML = `
                            <button onclick="showImagePreview('${data.url}')">
                                <i class='fas fa-search-plus'></i> Lihat
                            </button>
                            <button onclick="removeImage(this.parentElement.parentElement)">
                                <i class='fas fa-trash'></i> Hapus
                            </button>
                        `;
                        wrapper.appendChild(img);
                        wrapper.appendChild(actions);
                        editor.appendChild(wrapper);
                        editor.appendChild(document.createElement('br'));
                        editor.dispatchEvent(new Event('input'));
                    } else {
                        throw new Error(data.message || 'Failed to upload image');
                    }
                })
                .catch(error => {
                    document.getElementById(loadingId).innerHTML = `<div style='color: red;'>Gagal mengupload gambar: ${error.message}<button onclick='this.parentElement.remove()' style='float: right;'>&times;</button></div>`;
                });
            };
            reader.readAsDataURL(blob);
            return;
        }
    }
}
document.getElementById('pembahasanEditor').addEventListener('paste', function(e) { handlePastedImage(e, 'pembahasanEditor'); });
document.getElementById('tindakLanjutEditor').addEventListener('paste', function(e) { handlePastedImage(e, 'tindakLanjutEditor'); });

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
        wrapper.closest('.editor-content').dispatchEvent(new Event('input'));
    }
}
document.getElementById('imagePreviewModal').addEventListener('click', function(e) {
    if (e.target === this) closeImagePreview();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeImagePreview();
});
</script>
@endpush
