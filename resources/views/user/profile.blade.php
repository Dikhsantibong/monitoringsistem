@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    @include('layouts.breadcrumbs', ['breadcrumbs' => [
        ['title' => 'Dashboard', 'url' => route('user.dashboard')],
        ['title' => 'Profile']
    ]])
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('user.dashboard') }}" class="text-yellow-500 hover:text-yellow-700 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
        <h1 class="text-2xl font-semibold text-gray-800">Edit Profil</h1>
    </div>
    <div class="flex">
        <div class="w-1/4 bg-white rounded-lg shadow-lg p-6 mr-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Menu</h2>
            <ul class="text-sm text-gray-600">
                <li class="mb-2"><a href="{{ route('user.dashboard') }}" class="text-yellow-500 hover:text-yellow-700">Dashboard</a></li>
                <li class="mb-2"><a href="{{ route('user.profile') }}" class="text-yellow-500 hover:text-yellow-700">Edit Profil</a></li>
                <li class="mb-2"><a href="{{ route('user.settings') }}" class="text-yellow-500 hover:text-yellow-700">Pengaturan</a></li>
            </ul>
        </div>
        <div class="w-3/4 bg-white rounded-lg shadow-lg p-6">
            <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <!-- Profile Picture -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Foto Profil</label>
                    <input type="file" name="avatar" class="mt-2 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                </div>
                <!-- Name -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Nama</label>
                    <input type="text" name="name" value="{{ Auth::user()->name }}" class="mt-2 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500">
                </div>
                <!-- Email -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Email</label>
                    <input type="email" name="email" value="{{ Auth::user()->email }}" class="mt-2 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500">
                </div>
                <!-- Username -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Username</label>
                    <input type="text" name="username" value="{{ Auth::user()->username }}" class="mt-2 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500">
                </div>
                <!-- Password -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Password</label>
                    <input type="password" name="password" class="mt-2 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500">
                </div>
                <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Perbarui Profil</button>
            </form>
        </div>
    </div>
</div>
@endsection