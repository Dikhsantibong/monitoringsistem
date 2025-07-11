@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Notulen</h5>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="editNotulenForm" action="{{ route('notulen.update', $notulen->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tempat" class="form-label">Tempat</label>
                                <input type="text" class="form-control @error('tempat') is-invalid @enderror"
                                    id="tempat" name="tempat" value="{{ old('tempat', $notulen->tempat) }}" required>
                                @error('tempat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="agenda" class="form-label">Agenda</label>
                                <input type="text" class="form-control @error('agenda') is-invalid @enderror"
                                    id="agenda" name="agenda" value="{{ old('agenda', $notulen->agenda) }}" required>
                                @error('agenda')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="peserta" class="form-label">Peserta</label>
                                <textarea class="form-control @error('peserta') is-invalid @enderror"
                                    id="peserta" name="peserta" rows="3" required>{{ old('peserta', $notulen->peserta) }}</textarea>
                                @error('peserta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control @error('tanggal') is-invalid @enderror"
                                    id="tanggal" name="tanggal" value="{{ old('tanggal', $notulen->tanggal->format('Y-m-d')) }}" required>
                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                                                        <div class="col-md-4">
                                <label for="waktu_mulai" class="form-label">Waktu Mulai</label>
                                <input type="time" class="form-control @error('waktu_mulai') is-invalid @enderror"
                                    id="waktu_mulai" name="waktu_mulai"
                                    value="{{ old('waktu_mulai', \Carbon\Carbon::parse($notulen->waktu_mulai)->format('H:i')) }}" required>
                                @error('waktu_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="waktu_selesai" class="form-label">Waktu Selesai</label>
                                <input type="time" class="form-control @error('waktu_selesai') is-invalid @enderror"
                                    id="waktu_selesai" name="waktu_selesai"
                                    value="{{ old('waktu_selesai', \Carbon\Carbon::parse($notulen->waktu_selesai)->format('H:i')) }}" required>
                                @error('waktu_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="pembahasan" class="form-label">Pembahasan</label>
                                <textarea class="form-control @error('pembahasan') is-invalid @enderror"
                                    id="pembahasan" name="pembahasan" rows="5" required>{{ old('pembahasan', $notulen->pembahasan) }}</textarea>
                                @error('pembahasan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="tindak_lanjut" class="form-label">Tindak Lanjut</label>
                                <textarea class="form-control @error('tindak_lanjut') is-invalid @enderror"
                                    id="tindak_lanjut" name="tindak_lanjut" rows="5" required>{{ old('tindak_lanjut', $notulen->tindak_lanjut) }}</textarea>
                                @error('tindak_lanjut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="pimpinan_rapat_nama" class="form-label">Pimpinan Rapat</label>
                                <input type="text" class="form-control @error('pimpinan_rapat_nama') is-invalid @enderror"
                                    id="pimpinan_rapat_nama" name="pimpinan_rapat_nama" value="{{ old('pimpinan_rapat_nama', $notulen->pimpinan_rapat_nama) }}" required>
                                @error('pimpinan_rapat_nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="notulis_nama" class="form-label">Notulis</label>
                                <input type="text" class="form-control @error('notulis_nama') is-invalid @enderror"
                                    id="notulis_nama" name="notulis_nama" value="{{ old('notulis_nama', $notulen->notulis_nama) }}" required>
                                @error('notulis_nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tanggal_tanda_tangan" class="form-label">Tanggal Tanda Tangan</label>
                                <input type="date" class="form-control @error('tanggal_tanda_tangan') is-invalid @enderror"
                                    id="tanggal_tanda_tangan" name="tanggal_tanda_tangan"
                                    value="{{ old('tanggal_tanda_tangan', $notulen->tanggal_tanda_tangan->format('Y-m-d')) }}" required>
                                @error('tanggal_tanda_tangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="revision_reason" class="form-label">Alasan Perubahan</label>
                                <input type="text" class="form-control @error('revision_reason') is-invalid @enderror"
                                    id="revision_reason" name="revision_reason"
                                    value="{{ old('revision_reason') }}" required
                                    placeholder="Contoh: Perbaikan typo pada pembahasan">
                                @error('revision_reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary" id="submitBtn">Simpan Perubahan</button>
                                <a href="{{ route('notulen.show', $notulen->id) }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize CKEditor for rich text fields
    ClassicEditor
        .create(document.querySelector('#pembahasan'), {
            removePlugins: ['CKFinderUploadAdapter', 'CKFinder', 'EasyImage', 'Image', 'ImageCaption', 'ImageStyle', 'ImageToolbar', 'ImageUpload'],
        })
        .catch(error => {
            console.error(error);
        });

    ClassicEditor
        .create(document.querySelector('#tindak_lanjut'), {
            removePlugins: ['CKFinderUploadAdapter', 'CKFinder', 'EasyImage', 'Image', 'ImageCaption', 'ImageStyle', 'ImageToolbar', 'ImageUpload'],
        })
        .catch(error => {
            console.error(error);
        });

    // Form submission handling with SweetAlert
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
                // Show loading state
                Swal.fire({
                    title: 'Menyimpan...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit the form
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
</script>
@endpush
