@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Terjadi Kesalahan</h2>
                <p class="text-gray-600 mb-4">{{ session('error') ?? 'QR Code tidak valid atau sudah kadaluarsa.' }}</p>
                <audio id="errorSound" src="{{ asset('audio/error.MP3') }}" preload="auto"></audio>
                <script>
                    document.getElementById('errorSound').play();
                </script>
                <a href="/" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-[#0A749B] hover:bg-[#009BB9] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0A749B]">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
