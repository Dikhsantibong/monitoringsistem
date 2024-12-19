@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Data</h1>
    <form action="{{ route('admin.machine-monitor.update', $item->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Nama:</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $item->name }}" placeholder="Masukkan nama mesin" required>
        </div>
        <div class="form-group">
            <label for="code">Kode Mesin:</label>
            <input type="text" class="form-control" id="code" name="code" value="{{ $item->code }}" placeholder="Masukkan kode mesin" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('admin.machine-monitor') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection 