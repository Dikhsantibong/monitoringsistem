@extends('layouts.app')

@section('content')
<div class="container my-4">
    <h1>{{ $blogItem->title }}</h1>
    <p>{{ $blogItem->content }}</p> <!-- Pastikan ada kolom 'content' di model Blog -->
    <a href="{{ route('homepage') }}" class="btn btn-primary">Kembali ke Beranda</a>
</div>
@endsection 