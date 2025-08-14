@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50">
    @include('components.pemeliharaan-sidebar')
    <div class="flex-1 overflow-auto p-6">
        <h1 class="text-2xl font-semibold text-gray-800 mb-4">Support</h1>
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800">Kontak Tim IT</h2>
            <p>Email: <a href="mailto:tibongdikhsan@gmail.com" class="text-blue-600 hover:underline">tibongdikhsan@gmail.com</a></p>
            <p>Telepon: <span class="text-gray-800">+62 822-9311-8410</span></p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800">FAQ</h2>
            <ul class="mt-2 list-disc list-inside text-gray-700">
                <li><strong>Bagaimana cara mengatur akun saya?</strong><br/>Anda dapat mengatur akun melalui halaman profil.</li>
                <li><strong>Di mana saya bisa menemukan dokumentasi?</strong><br/>Dokumentasi lengkap tersedia di halaman dokumentasi.</li>
            </ul>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800">Hubungi Admin</h2>
            <a href="https://wa.me/6282293118410" target="_blank" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 inline-block mt-2">
                <i class="fab fa-whatsapp mr-2"></i>Hubungi Admin via WhatsApp
            </a>
        </div>
    </div>
</div>
@endsection


