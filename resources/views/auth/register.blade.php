@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card register-card">
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
                    <h5 class="card-title text-center mb-4">{{ __('Register Now') }}</h5>
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Name -->
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Enter Full Name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="form-group mt-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Enter Email Address" value="{{ old('email') }}" required autocomplete="email">
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
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Enter Password" required autocomplete="new-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group mt-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                </div>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" required autocomplete="new-password">
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary w-100">
                                {{ __('Register') }}
                            </button>
                            <div class="text-center mt-3">
                                <a class="btn btn-link" href="{{ route('login') }}">{{ __('Already have an account? Login here') }}</a>
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
        background-image: url('/background/backgorund.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        margin: 0;
        opacity: 0; /* Tidak terlihat saat awal */
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

    .register-card {
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
