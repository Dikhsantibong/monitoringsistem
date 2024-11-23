<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantera</title>
    
    <!-- Untuk Laravel Mix -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <!-- Atau untuk Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    @yield('content')
    
    @stack('scripts')
</body>
</html>
