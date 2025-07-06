@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-6 flex flex-col justify-center sm:py-12">
    <div class="relative py-3 sm:max-w-xl sm:mx-auto">
        <div class="relative px-4 py-10 bg-white mx-8 md:mx-0 shadow rounded-3xl sm:p-10">
            <div class="max-w-md mx-auto">
                <div class="divide-y divide-gray-200">
                    <div class="py-8 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
                        <div class="text-center mb-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">Form Absensi Rapat</h2>
                            @if(!$isTemporary && isset($notulen))
                            <p class="text-gray-600">{{ $notulen->agenda }}</p>
                            <p class="text-sm text-gray-500">{{ $notulen->tanggal->format('l, d F Y') }}</p>
                            @endif
                        </div>

                        <form id="attendanceForm" class="space-y-4">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input type="text" name="name" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0095B7] focus:ring focus:ring-[#0095B7] focus:ring-opacity-50">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jabatan</label>
                                <input type="text" name="position" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0095B7] focus:ring focus:ring-[#0095B7] focus:ring-opacity-50">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Divisi</label>
                                <input type="text" name="division" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0095B7] focus:ring focus:ring-[#0095B7] focus:ring-opacity-50">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanda Tangan</label>
                                <div class="border rounded-lg p-2">
                                    <canvas id="signatureCanvas" class="border rounded-lg w-full" height="200"></canvas>
                                </div>
                                <div class="mt-2 flex justify-end">
                                    <button type="button" onclick="clearSignature()" class="text-sm text-gray-600 hover:text-gray-900">
                                        Clear
                                    </button>
                                </div>
                                <input type="hidden" name="signature" id="signatureInput">
                            </div>

                            <div class="pt-4 flex justify-center">
                                <button type="submit"
                                    class="bg-[#0095B7] text-white px-6 py-2 rounded-lg hover:bg-[#007a94] focus:outline-none focus:ring-2 focus:ring-[#0095B7] focus:ring-opacity-50">
                                    Submit Absensi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<audio id="successSound" src="{{ asset('audio/success.MP3') }}" preload="auto"></audio>
<audio id="errorSound" src="{{ asset('audio/error.MP3') }}" preload="auto"></audio>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    let signaturePad;

    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('signatureCanvas');
        signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)'
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

        // Handle form submission
        document.getElementById('attendanceForm').addEventListener('submit', function(e) {
            e.preventDefault();

            if (signaturePad.isEmpty()) {
                alert('Mohon berikan tanda tangan Anda');
                return;
            }

            const signatureData = signaturePad.toDataURL();
            document.getElementById('signatureInput').value = signatureData;

            const formData = new FormData(this);

            fetch('{{ route("notulen.store-attendance") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('successSound').play();
                    window.location.href = '{{ route("notulen.attendance.success") }}';
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan');
                }
            })
            .catch(error => {
                document.getElementById('errorSound').play();
                alert(error.message);
                window.location.href = '{{ route("notulen.error") }}';
            });
        });
    });

    function clearSignature() {
        signaturePad.clear();
    }
</script>
@endpush
