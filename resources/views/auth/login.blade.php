@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center vh-100">
    <!-- Tambahkan div untuk background -->
    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;">
        <img src="{{ asset('images/bg.jpg') }}" alt="Background" 
             style="width: 100%; height: 100%; object-fit: cover; position: absolute;">
    </div>

    <div class="card login-card">
        <div class="row no-gutters">
            <!-- Kolom kiri -->
            <div class="col-md-6 text-center left-section d-flex align-items-center justify-content-center">
                <div>
                    <div class="mb-3">
                        <img src="{{ asset('logo/pjb-logo.png') }}" alt="Logo" class="logo">
                    </div>

                </div>
            </div>
            <!-- Kolom kanan -->
            <div class="col-md-6 right-section">
                <div class="card-body">
                    <h5 class="card-title text-center mb-4">{{ __('Login Now') }}</h5>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Username -->
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Enter Username" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="form-group mt-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                </div>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Enter Password" required autocomplete="current-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Unit Selection -->
                        <div class="form-group mt-3">
                            <label for="unit" class="text-primary">Pilih Unit:</label>
                            <select name="unit" id="unit" required class="form-select block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="mysql" {{ $selectedUnit == 'mysql' ? 'selected' : '' }} class="text-success">
                                    <i class="fas fa-building"></i> UP Kendari
                                </option>
                                <option value="mysql_bau_bau" {{ $selectedUnit == 'mysql_bau_bau' ? 'selected' : '' }} class="text-info">
                                    <i class="fas fa-building"></i> ULPLTD Bau-Bau
                                </option>
                                <option value="mysql_kolaka" {{ $selectedUnit == 'mysql_kolaka' ? 'selected' : '' }} class="text-warning">
                                    <i class="fas fa-building"></i> ULPLTD Kolaka
                                </option>
                                <option value="mysql_poasia" {{ $selectedUnit == 'mysql_poasia' ? 'selected' : '' }} class="text-danger">
                                    <i class="fas fa-building"></i> ULPLTD Poasia
                                </option>
                                <option value="mysql_wua_wua" {{ $selectedUnit == 'mysql_wua_wua' ? 'selected' : '' }} class="text-primary">
                                    <i class="fas fa-building"></i> ULPLTD Wua-Wua
                                </option>
                            </select>
                        </div>

                        <!-- Remember Me -->
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>

                        <!-- Buttons -->
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary w-100">
                                {{ __('Login') }}
                            </button>
                            <div class="text-center mt-3">
                                {{-- <a class="btn btn-link" href="{{ route('password.request') }}">{{ __('Forgot Password?') }}</a> --}}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    body {
        margin: 0;
        opacity: 0;
        animation: fadeIn ease 1s;
        animation-iteration-count: 1;
        animation-fill-mode: forwards;
    }

    @keyframes fadeIn {
        0% {opacity:0;}
        100% {opacity:1;}
    }

    .container {
        height: 100vh; /* Full height of the viewport */
    }

    .login-card {
        width: 100%;
        max-width: 900px;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .left-section {
        background: #2575fc;
        color: white;
    }

    .right-section {
        background: #fff;
        padding: 40px;
    }

    .logo-container {
        background: #fff;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: bold;
        color: #2575fc;
    }

    .welcome-text {
        font-size: 24px;
        font-weight: 600;
    }

    .input-group-text {
        background: #f4f6f9;
    }

    .btn-primary {
        background-color: #2575fc;
        border: none;
    }

    .btn-primary:hover {
        background-color: #1e5ecc;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: '{{ session("error") }}',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Coba Lagi',
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session("success") }}',
                confirmButtonColor: '#28a745',
                confirmButtonText: 'OK',
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: 'Email atau password yang Anda masukkan salah!',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Coba Lagi',
                timer: 3000,
                timerProgressBar: true
            });
        @endif
    });
</script>
@endsection
