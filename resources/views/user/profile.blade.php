@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('user.dashboard') }}" class="text-yellow-500 hover:text-yellow-700 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back
        </a>
        <h1 class="text-2xl font-semibold text-gray-800">Edit Profile</h1>
    </div>
    <div class="bg-white rounded-lg shadow-lg p-6">
        <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Profile Picture -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">Profile Picture</label>
                <input type="file" name="avatar" class="mt-2 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
            </div>
            <!-- Name -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">Name</label>
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
            <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Update Profile</button>
        </form>
    </div>
</div>
@endsection