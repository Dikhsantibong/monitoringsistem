@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.pemeliharaan-sidebar')
    <div class="flex-1">
        <div class="container mx-auto px-6 py-8">
            <h1 class="text-2xl font-bold mb-4">Master Labor</h1>
            @if(session('success'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
            @endif
            <form action="{{ route('pemeliharaan.master-labor.store') }}" method="POST" class="mb-6 bg-white p-4 rounded shadow">
                @csrf
                <div class="mb-2">
                    <label class="block text-gray-700">Nama Labor</label>
                    <input type="text" name="nama" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="mb-2">
                    <label class="block text-gray-700">Bidang</label>
                    <select name="bidang" class="w-full border rounded px-3 py-2" required>
                        <option value="">Pilih Bidang</option>
                        <option value="listrik">Listrik</option>
                        <option value="mesin">Mesin</option>
                        <option value="kontrol">Kontrol</option>
                        <option value="alat bantu">Alat Bantu</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Tambah Labor</button>
            </form>
            <div class="bg-white rounded shadow p-4">
                <h2 class="text-lg font-semibold mb-2">Daftar Labor</h2>
                <table class="w-full table-auto">
                    <thead>
                        <tr>
                            <th class="border px-2 py-1 text-center">No</th>
                            <th class="border px-2 py-1 text-center">Nama</th>
                            <th class="border px-2 py-1 text-center">Bidang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($labors as $labor)
                        <tr>
                            <td class="border px-2 py-1">{{ $labor->id }}</td>
                            <td class="border px-2 py-1">{{ $labor->nama }}</td>
                            <td class="border px-2 py-1 capitalize">{{ $labor->bidang }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
