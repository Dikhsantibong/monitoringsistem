@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            Form Kehadiran
        </h2>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <form class="space-y-6" action="{{ route('attendance.submit') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Nama Lengkap
                    </label>
                    <div class="mt-1">
                        <input id="name" name="name" type="text" required
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#009BB9] focus:border-[#009BB9] sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="division" class="block text-sm font-medium text-gray-700">
                        Divisi
                    </label>
                    <div class="mt-1">
                        <select id="division" name="division" required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#009BB9] focus:border-[#009BB9] sm:text-sm">
                            <option value="">Pilih Divisi</option>
                            <option value="IT">IT</option>
                            <option value="HR">HR</option>
                            <option value="Finance">Finance</option>
                            <option value="Marketing">Marketing</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700">
                        Jabatan
                    </label>
                    <div class="mt-1">
                        <select id="position" name="position" required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#009BB9] focus:border-[#009BB9] sm:text-sm">
                            <option value="">Pilih Jabatan</option>
                            <option value="Staff">Staff</option>
                            <option value="Supervisor">Supervisor</option>
                            <option value="Manager">Manager</option>
                            <option value="Director">Director</option>
                        </select>
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#009BB9] hover:bg-[#007A99] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#009BB9]">
                        Submit Kehadiran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 