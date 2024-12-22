@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-6 flex flex-col justify-center sm:py-12">
    <div class="relative py-3 sm:max-w-xl sm:mx-auto">
        <div class="relative px-4 py-10 bg-white mx-8 md:mx-0 shadow rounded-3xl sm:p-10">
            <div class="max-w-md mx-auto">
                <div class="divide-y divide-gray-200">
                    <div class="py-8 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
                        <h2 class="text-2xl font-bold mb-8 text-center text-gray-800">Form Absensi</h2>
                        <form action="{{ route('attendance.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            
                            <div class="space-y-2">
                                <label class="text-gray-600">Nama</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#009BB9]">
                            </div>

                            <div class="space-y-2">
                                <label class="text-gray-600">Jabatan</label>
                                <input type="text" name="position" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#009BB9]">
                            </div>

                            <div class="space-y-2">
                                <label class="text-gray-600">Divisi</label>
                                <input type="text" name="division" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#009BB9]">
                            </div>

                            <button type="submit" class="w-full bg-[#0A749B] text-white px-4 py-2 rounded-lg hover:bg-[#009BB9]">
                                Submit Absensi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection