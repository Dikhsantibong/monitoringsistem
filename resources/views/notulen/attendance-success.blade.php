@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Absensi Berhasil!</h2>
                <p class="text-gray-600 mb-8">Terima kasih telah mengisi daftar hadir.</p>
                <audio id="successSound" src="{{ asset('audio/success.MP3') }}" preload="auto"></audio>
                <script>
                    document.getElementById('successSound').play();
                </script>
            </div>
        </div>
    </div>
</div>
@endsection
