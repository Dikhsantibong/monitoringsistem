@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Form Absensi Terlambat</h2>

        <div class="mb-4 p-4 bg-blue-50 rounded-lg">
            <h3 class="font-bold text-blue-800">Detail Rapat:</h3>
            <p class="text-blue-600">Agenda: {{ $notulen->agenda }}</p>
            <p class="text-blue-600">Tanggal: {{ $notulen->tanggal->format('d/m/Y') }}</p>
            <p class="text-blue-600">Waktu: {{ \Carbon\Carbon::parse($notulen->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($notulen->waktu_selesai)->format('H:i') }} WIB</p>
        </div>

        <form id="lateAttendanceForm">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama</label>
                <input type="text" name="name" class="border rounded w-full py-2 px-3" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Jabatan</label>
                <input type="text" name="position" class="border rounded w-full py-2 px-3" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Divisi</label>
                <input type="text" name="division" class="border rounded w-full py-2 px-3" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Tanda Tangan</label>
                <canvas id="signaturePad" class="border rounded" width="400" height="200"></canvas>
                <button type="button" onclick="clearSignature()" class="mt-2 text-sm text-gray-600">Clear</button>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Submit</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    let signaturePad;

    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('signaturePad');
        signaturePad = new SignaturePad(canvas);

        document.getElementById('lateAttendanceForm').addEventListener('submit', function(e) {
            e.preventDefault();

            if (signaturePad.isEmpty()) {
                alert('Mohon berikan tanda tangan');
                return;
            }

            const formData = new FormData(this);
            formData.append('signature', signaturePad.toDataURL());

            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').content;

            fetch('{{ url("/public/api/notulen/" . $notulen->id . "/late-attendance") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Absensi berhasil disimpan');
                    window.close();
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Terjadi kesalahan saat menyimpan absensi');
            });
        });
    });

    function clearSignature() {
        signaturePad.clear();
    }
</script>
@endpush

<style>
    .container {
        min-height: 100vh;
        background-color: #f7fafc;
    }

    .max-w-md {
        max-width: 28rem;
    }

    canvas {
        width: 100%;
        height: 200px;
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
    }

    button {
        transition: all 0.2s;
    }

    button:hover {
        transform: translateY(-1px);
    }

    .bg-blue-50 {
        background-color: #ebf5ff;
    }

    .text-blue-800 {
        color: #2c5282;
    }

    .text-blue-600 {
        color: #3182ce;
    }

    input:focus {
        outline: none;
        border-color: #4299e1;
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.15);
    }
</style>
@endsection
