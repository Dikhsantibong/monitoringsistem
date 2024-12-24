    @extends('layouts.app')

    @section('content')

    <style>
        canvas {
            background-color: white
        }
    </style>
    <div class="min-h-screen bg-gray-50 py-6 flex flex-col justify-center sm:py-12">
        <div class="relative py-3 sm:max-w-xl sm:mx-auto">
            <div class="relative px-4 py-10 bg-white mx-8 md:mx-0 shadow rounded-3xl sm:p-10">
                <div class="max-w-md mx-auto">
                    <div class="divide-y divide-gray-200">
                        <div class="py-8 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
                            <h2 class="text-2xl font-bold mb-8 text-center text-gray-800">Form Absensi</h2>
                            
                            @if(session('error'))
                                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                                    <span class="block sm:inline">{{ session('error') }}</span>
                                </div>
                            @endif

                            <form action="{{ route('attendance.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">
                                
                                <div class="space-y-2">
                                    <label class="text-gray-600">Nama</label>
                                    <input type="text" name="name" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#009BB9]"
                                        value="{{ old('name') }}">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-gray-600">Jabatan</label>
                                    <input type="text" name="position" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#009BB9]"
                                        value="{{ old('position') }}">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-gray-600">Divisi</label>
                                    <input type="text" name="division" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#009BB9]"
                                        value="{{ old('division') }}">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-gray-600">Waktu Kehadiran</label>
                                    <input type="datetime-local" name="time" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#009BB9]">
                                </div>

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

                                <button type="submit" 
                                    class="w-full bg-[#0A749B] text-white px-4 py-2 rounded-lg hover:bg-[#009BB9] transition duration-300">
                                    Submit Absensi
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('signature-pad');
            const signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor: 'rgb(0, 0, 0)'
            });

            // Atur ukuran canvas sesuai dengan container
            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                signaturePad.clear(); // Bersihkan setelah resize
            }

            window.addEventListener("resize", resizeCanvas);
            resizeCanvas();

            // Tombol Clear
            document.getElementById('clear').addEventListener('click', function() {
                signaturePad.clear();
            });

            // Tombol Undo
            document.getElementById('undo').addEventListener('click', function() {
                const data = signaturePad.toData();
                if (data) {
                    data.pop(); // Hapus goresan terakhir
                    signaturePad.fromData(data);
                }
            });

            // Saat form di-submit
            document.querySelector('form').addEventListener('submit', function(e) {
                if (signaturePad.isEmpty()) {
                    e.preventDefault();
                    alert('Mohon isi tanda tangan terlebih dahulu!');
                    return false;
                }

                // Simpan data tanda tangan ke input hidden
                const signatureData = signaturePad.toDataURL();
                document.getElementById('signature-data').value = signatureData;
            });
        });
    </script>
    @push('scripts')
        
    @endpush
    @endsection 