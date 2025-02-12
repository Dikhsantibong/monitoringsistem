@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-6 flex flex-col justify-center sm:py-12">
    <div class="relative py-3 sm:max-w-xl sm:mx-auto">
        <div class="relative px-4 py-10 bg-white mx-8 md:mx-0 shadow rounded-3xl sm:p-10">
            <div class="max-w-md mx-auto">
                <div class="divide-y divide-gray-200">
                    <div class="py-8 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
                        <div class="text-center">
                            <i class="fas fa-exclamation-circle text-red-500 text-5xl mb-4"></i>
                            <h2 class="text-2xl font-bold mb-2 text-red-500">Error</h2>
                            <p class="text-gray-600">{{ session('error') ?? 'Terjadi kesalahan dalam memproses permintaan Anda.' }}</p>
                            
                            <div class="mt-6">
                                <a href="{{ route('homepage') }}" 
                                   class="bg-[#0A749B] text-white px-4 py-2 rounded-lg hover:bg-[#009BB9] transition duration-300">
                                    Kembali ke Homepage
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 