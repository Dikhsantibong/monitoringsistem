@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-8 text-center">
            <div class="error-page">
                <img src="{{ asset('images/404 error.jpg') }}" alt="404 Illustration" class="img-fluid mb-4" style="max-width: 300px;">
                <h1 class="display-4 text-primary mb-4">Oops! Halaman Tidak Ditemukan</h1>
                <div class="error-content">
                    <p class="lead text-muted mb-4">Maaf, halaman yang Anda cari tidak ditemukan.<br>Halaman mungkin tidak valid atau sudah kadaluarsa.</p>
                    <a href="{{ url('/') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-home me-2"></i>Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.error-page {
    padding: 40px 0;
}

.error-content {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
}

.display-4 {
    font-weight: 600;
}

.btn-primary {
    padding: 12px 30px;
    border-radius: 50px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
</style>
@endsection 