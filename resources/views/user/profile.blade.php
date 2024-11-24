@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold">Profil Pengguna</h1>
    <p>Nama: {{ Auth::user()->name }}</p>
    <p>Email: {{ Auth::user()->email }}</p>
    <!-- Tambahkan informasi lain yang relevan -->
</div>
@endsection 