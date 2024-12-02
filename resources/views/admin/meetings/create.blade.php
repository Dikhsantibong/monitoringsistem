@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-6">
    <h1 class="text-2xl font-bold">Daftar Rapat</h1>
    <form action="{{ route('admin.meetings.store') }}" method="POST" class="mt-4">
        @csrf
        <div class="mb-4">
            <label for="title" class="block">Judul Rapat</label>
            <input type="text" id="title" name="title" class="mt-1 block w-full" required>
        </div>
        <div class="mb-4">
            <label for="scheduled_at" class="block">Tanggal dan Waktu</label>
            <input type="datetime-local" id="scheduled_at" name="scheduled_at" class="mt-1 block w-full" required>
        </div>
        <div class="mb-4">
            <label for="duration" class="block">Durasi (menit)</label>
            <select id="duration" name="duration" class="mt-1 block w-full" required>
                <option value="30">30 menit</option>
                <option value="60">60 menit</option>
                <option value="90">90 menit</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2">Jadwalkan Rapat</button>
    </form>

    <table class="mt-6 w-full">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Tanggal & Waktu</th>
                <th>Durasi</th>
                <th>Link Zoom</th>
                <th>Status Notulen</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($meetings as $meeting)
            <tr>
                <td>{{ $meeting->title }}</td>
                <td>{{ $meeting->scheduled_at }}</td>
                <td>{{ $meeting->duration }} menit</td>
                <td><a href="{{ $meeting->zoom_link }}" target="_blank">Link</a></td>
                <td>{{ $meeting->minutes ? 'Sudah dibuat' : 'Belum dibuat' }}</td>
                <td>
                    <a href="{{ route('admin.meetings.edit', $meeting->id) }}">Edit</a>
                    <form action="{{ route('admin.meetings.destroy', $meeting->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection