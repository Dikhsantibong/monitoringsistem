@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                    <a href="{{ url('/') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                        Kembali ke Beranda
                    </a>
                </div>
            @endif

            @if ($token)
                <form method="POST" action="{{ route('attendance.submit') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    
                    <div class="form-group">
                        <label>Token</label>
                        <input type="text" class="form-control" value="{{ $token }}" readonly>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Submit Attendance</button>
                </form>
            @else
                <div class="text-center text-red-600">
                    <p>QR Code tidak valid atau sudah kadaluarsa.</p>
                    <p class="mt-2">Silakan scan ulang QR Code yang baru.</p>
                    <a href="{{ url('/') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                        Kembali ke Beranda
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 