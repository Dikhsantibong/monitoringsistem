@extends('layouts.app')

@section('content')
<div class="container my-4">
    <h1>{{ $newsItem->title }}</h1>
    <img src="{{ $newsItem->thumbnail }}" alt="Thumbnail" class="img-fluid mb-3">
    <p>{{ $newsItem->content }}</p> <!-- Pastikan ada kolom 'content' di model News -->
    <a href="{{ route('homepage') }}" class="btn btn-primary">Kembali ke Beranda</a>
</div>
@endsection 