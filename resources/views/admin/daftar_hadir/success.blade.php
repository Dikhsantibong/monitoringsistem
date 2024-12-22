@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-6 flex flex-col justify-center sm:py-12">
    <div class="relative py-3 sm:max-w-xl sm:mx-auto">
        <div class="relative px-4 py-10 bg-white mx-8 md:mx-0 shadow rounded-3xl sm:p-10">
            <div class="max-w-md mx-auto text-center">
                <div class="divide-y divide-gray-200">
                    <div class="py-8 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
                        <i class="fas fa-check-circle text-5xl text-green-500 mb-4"></i>
                        <h2 class="text-2xl font-bold text-gray-800">Absensi Berhasil!</h2>
                        <p class="text-gray-600">Terima kasih, absensi Anda telah berhasil dicatat.</p>
                        <div class="pt-6">
                            <a href="{{ url('/') }}" class="bg-[#0A749B] text-white px-6 py-2 rounded-lg hover:bg-[#009BB9]">
                                Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 