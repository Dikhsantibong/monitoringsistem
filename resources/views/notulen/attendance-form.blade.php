@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Form Absensi Rapat</h2>

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
                <canvas id="signaturePad" class="border rounded" width="400" height="200"></canvas>
                <button type="button" onclick="clearSignature()" class="mt-2 text-sm text-gray-600">Clear</button>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Submit</button>
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

        document.getElementById('attendanceForm').addEventListener('submit', function(e) {
            e.preventDefault();

            if (signaturePad.isEmpty()) {
                alert('Mohon berikan tanda tangan');
                return;
            }

            const formData = new FormData(this);
            formData.append('signature', signaturePad.toDataURL());
            formData.append('temp_notulen_id', '{{ $temp_notulen_id }}');

            fetch('{{ url("/api/notulen-attendance") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        window.close();
                    }
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
@endsection
