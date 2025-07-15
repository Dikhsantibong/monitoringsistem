@extends('layouts.app')

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
                                    <label for="pembahasan" class="form-label font-weight-bold">Pembahasan</label>
                                    <textarea class="form-control @error('pembahasan') is-invalid @enderror"
                                        id="pembahasan" name="pembahasan" style="min-height: 300px;" required>{!! old('pembahasan', $notulen->pembahasan) !!}</textarea>
                                    @error('pembahasan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="tindak_lanjut" class="form-label font-weight-bold">Tindak Lanjut</label>
                                    <textarea class="form-control @error('tindak_lanjut') is-invalid @enderror"
                                        id="tindak_lanjut" name="tindak_lanjut" style="min-height: 300px;" required>{!! old('tindak_lanjut', $notulen->tindak_lanjut) !!}</textarea>
                                    @error('tindak_lanjut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
    </div>
</div>

<!-- QR Code Modal -->
<div class="overlay" id="overlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); z-index: 999;"></div>
<div class="qr-code-modal" id="qrCodeContainer" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; border-radius: 8px; z-index: 1000; min-width: 400px; text-align: center;">
    <h2 class="text-xl font-weight-bold mb-4">Scan QR Code untuk Absensi</h2>
    <div id="qrcodeModal" class="d-flex justify-content-center"></div>
    <p class="mt-3 text-muted">
        <small>URL: <span id="qrUrl" class="text-break"></span></small>
    </p>
    <button onclick="closeQRCode()" class="btn btn-danger mt-4">
        <i class="fas fa-times"></i> Tutup QR Code
    </button>
</div>

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
            removePlugins: ['CKFinderUploadAdapter', 'CKFinder', 'EasyImage', 'Image', 'ImageCaption', 'ImageStyle', 'ImageToolbar', 'ImageUpload'],
        toolbar: {
            items: [
                'heading',
                '|',
                'bold',
                'italic',
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
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' }
                ]
        },
        removeFormatAttributes: 'style,class,id', // Remove all formatting attributes
    };

    // Function to clean HTML content
    function cleanHtml(html) {
        const div = document.createElement('div');
        div.innerHTML = html;

        // Remove all style attributes
        div.querySelectorAll('*').forEach(el => {
            el.removeAttribute('style');
            el.removeAttribute('class');
            el.removeAttribute('id');
        });

        // Convert specific elements to paragraphs
        ['div'].forEach(tag => {
            div.querySelectorAll(tag).forEach(el => {
                const p = document.createElement('p');
                p.innerHTML = el.innerHTML;
                el.parentNode.replaceChild(p, el);
            });
        });

        return div.innerHTML;
    }

    // Initialize CKEditor instances
    let pembahasan, tindakLanjut;

    ClassicEditor
        .create(document.querySelector('#pembahasan'), editorConfig)
        .then(editor => {
            pembahasan = editor;
        })
        .catch(error => {
            console.error(error);
        });

    ClassicEditor
        .create(document.querySelector('#tindak_lanjut'), editorConfig)
        .then(editor => {
            tindakLanjut = editor;
        })
        .catch(error => {
            console.error(error);
        });

    function showQRCode() {
        const overlay = document.getElementById('overlay');
        const container = document.getElementById('qrCodeContainer');
        const qrcodeDiv = document.getElementById('qrcodeModal');
        const qrUrlSpan = document.getElementById('qrUrl');

        overlay.style.display = 'block';
        container.style.display = 'block';

        // Clear previous QR code if exists
        qrcodeDiv.innerHTML = '';

        // Generate the URL for late attendance
        const baseUrl = window.location.origin;
        const attendanceUrl = `${baseUrl}/public/notulen/late-attendance/{{ $notulen->id }}`;

        // Show the URL for verification
        qrUrlSpan.textContent = attendanceUrl;

        // Create QR code with larger size and better error correction
        new QRCode(qrcodeDiv, {
            text: attendanceUrl,
            width: 300,
            height: 300,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H // Highest error correction level
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

    // Form submission handling
    document.getElementById('editNotulenForm').addEventListener('submit', function(e) {
        // Cek login sebelum submit (client-side UX, server tetap harus pakai middleware)
        @if(!Auth::check())
            e.preventDefault();
            sessionStorage.setItem('redirectAfterLogin', window.location.href);
            window.location.href = '{{ route('login') }}';
            return;
        @endif

        // Validasi waktu selesai > waktu mulai
        const waktuMulai = document.getElementById('waktu_mulai').value;
        const waktuSelesai = document.getElementById('waktu_selesai').value;
        if (waktuMulai && waktuSelesai && waktuSelesai <= waktuMulai) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Validasi Waktu',
                text: 'Waktu selesai harus setelah waktu mulai!'
            });
            return;
        }

        e.preventDefault();

        // Clean HTML content before submission
        if (pembahasan) {
            const cleanedPembahasan = cleanHtml(pembahasan.getData());
            document.querySelector('#pembahasan').value = cleanedPembahasan;
        }

        if (tindakLanjut) {
            const cleanedTindakLanjut = cleanHtml(tindakLanjut.getData());
            document.querySelector('#tindak_lanjut').value = cleanedTindakLanjut;
        }

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

        Swal.fire({
            title: 'Menyimpan Perubahan',
            text: 'Apakah Anda yakin ingin menyimpan perubahan?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menyimpan...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                this.submit();
            }
        });
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
        fetch('/public/api/notulen-documentation/' + id, {
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

        fetch('{{ url("/public/api/notulen-documentation") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
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
        fetch('/public/api/notulen-file/' + id, {
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

        fetch('{{ url("/public/api/notulen-file") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
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

@push('styles')
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
</style>
@endpush
