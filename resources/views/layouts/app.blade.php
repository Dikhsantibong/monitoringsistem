<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
     @yield('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- SweetAlert2 -->
    <link href="{{ asset('vendor/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
</head>
<body>
    @include('sweetalert::alert')
    <div id="app">
        @yield('content')
    </div>
    @yield('scripts')
</body>
</html>
