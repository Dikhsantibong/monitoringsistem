@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-6 flex flex-col justify-center sm:py-12">
    <div class="relative py-3 sm:max-w-xl sm:mx-auto">
        <div class="relative px-4 py-10 bg-white mx-8 md:mx-0 shadow rounded-3xl sm:p-10">
            <div class="max-w-md mx-auto">
                <div class="divide-y divide-gray-200">
                    <div class="py-8 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
                        <h2 class="text-2xl font-bold mb-8 text-center text-gray-800">Form Absensi</h2>
                        
                        <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <span class="block sm:inline"></span>
                        </div>

                        <form id="attendance-form" class="space-y-4">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            
                            <!-- Form fields -->
                            <div class="space-y-2">
                                <label class="text-gray-600">Nama</label>
                                <input type="text" name="name" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#009BB9]">
                            </div>

                            <div class="space-y-2">
                                <label class="text-gray-600">Jabatan</label>
                                <input type="text" name="position" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#009BB9]">
                            </div>

                            <div class="space-y-2">
                                <label class="text-gray-600">Divisi</label>
                                <input type="text" name="division" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#009BB9]">
                            </div>

                            <!-- Signature Pad -->
                            <div class="space-y-2">
                                <label class="text-gray-600">Tanda Tangan</label>
                                <div class="border border-gray-300 rounded-lg p-2">
                                    <canvas id="signature-pad" class="border rounded-lg w-full h-48"></canvas>
                                    <div class="flex justify-between mt-2">
                                        <button type="button" id="clear" class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash-alt mr-1"></i> Hapus
                                        </button>
                                        <button type="button" id="undo" class="text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-undo mr-1"></i> Undo
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="signature" id="signature-data">
                            </div>

                            <button type="submit" class="w-full bg-[#0A749B] text-white px-4 py-2 rounded-lg hover:bg-[#009BB9] transition duration-300">
                                Submit Absensi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SignaturePad Script -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('signature-pad');
    const signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(255, 255, 255)',
        penColor: 'rgb(0, 0, 0)'
    });

    // Resize canvas
    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        signaturePad.clear();
    }

    window.addEventListener("resize", resizeCanvas);
    resizeCanvas();

    // Clear button
    document.getElementById('clear').addEventListener('click', function() {
        signaturePad.clear();
        document.getElementById('signature-data').value = '';
    });

    // Undo button
    document.getElementById('undo').addEventListener('click', function() {
        const data = signaturePad.toData();
        if (data) {
            data.pop();
            signaturePad.fromData(data);
        }
    });

    // Form submission dengan fetch API
    document.getElementById('attendance-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (signaturePad.isEmpty()) {
            showError('Mohon isi tanda tangan terlebih dahulu!');
            return;
        }

        // Save signature data
        const signatureData = signaturePad.toDataURL('image/png');
        document.getElementById('signature-data').value = signatureData;

        try {
            const formData = new FormData(this);
            
            const response = await fetch('{{ route("attendance.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                window.location.href = '{{ route("attendance.success") }}';
            } else {
                showError(data.message || 'Gagal menyimpan absensi');
            }
        } catch (error) {
            console.error('Submit Error:', error);
            
            let errorMessage = 'Terjadi kesalahan saat menyimpan.';
            
            if (error.response) {
                const data = await error.response.json();
                
                switch(data.error_type) {
                    case 'validation_error':
                        errorMessage = Object.values(data.errors).flat().join('\n');
                        break;
                    case 'invalid_token':
                        errorMessage = 'Token tidak valid atau sudah kadaluarsa';
                        break;
                    case 'token_used':
                        errorMessage = 'Token ini sudah digunakan untuk absensi';
                        break;
                    case 'system_error':
                        errorMessage = data.message;
                        if (data.debug_info) {
                            console.error('Debug Info:', data.debug_info);
                        }
                        break;
                    default:
                        errorMessage = data.message || 'Terjadi kesalahan yang tidak diketahui';
                }
            }
            
            showError(errorMessage);
        }
    });

    function showError(message) {
        const errorDiv = document.getElementById('error-message');
        errorDiv.querySelector('span').textContent = message;
        errorDiv.classList.remove('hidden');
        
        setTimeout(() => {
            errorDiv.classList.add('hidden');
        }, 5000);
    }
});
</script>
@endsection 