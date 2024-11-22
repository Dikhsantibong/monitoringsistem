
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel - Home</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                body {
                    background: linear-gradient(to right, #6a11cb, #2575fc);
                    color: #fff;
                    font-family: 'Figtree', sans-serif;
                }
                .container {
                    max-width: 1200px;
                    margin: auto;
                    padding: 20px;
                }
                .header {
                    text-align: center;
                    padding: 50px 0;
                }
                .button-container {
                    text-align: center;
                    margin: 20px 0;
                }
                .button {
                    background-color: #fff;
                    color: #6a11cb;
                    border: none;
                    border-radius: 5px;
                    padding: 10px 20px;
                    margin: 0 10px;
                    cursor: pointer;
                    transition: background-color 0.3s, color 0.3s;
                }
                .button:hover {
                    background-color: #6a11cb;
                    color: #fff;
                }
                .card {
                    background: rgba(255, 255, 255, 0.1);
                    border-radius: 10px;
                    padding: 20px;
                    margin: 20px;
                    transition: transform 0.3s;
                }
                .card:hover {
                    transform: scale(1.05);
                }
                .footer {
                    text-align: center;
                    padding: 20px 0;
                    background: rgba(0, 0, 0, 0.5);
                }
            </style>
        @endif
    </head>
    <body>
        <div class="container">
            <header class="header">
                <h1>Welcome to Laravel</h1>
                <p>Your journey to building amazing applications starts here.</p>
            </header>

            <div class="button-container">
                <a href="{{ route('login') }}" class="button">Login</a>
                <a href="{{ route('register') }}" class="button">Register</a>
            </div>

            <main class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="card">
                    <h2 class="text-xl font-semibold">Documentation</h2>
                    <p>Explore the comprehensive documentation to get started with Laravel.</p>
                    <a href="https://laravel.com/docs" class="text-blue-400 hover:underline">Read More</a>
                </div>
                <div class="card">
                    <h2 class="text-xl font-semibold">Laracasts</h2>
                    <p>Watch thousands of video tutorials on Laravel, PHP, and JavaScript.</p>
                    <a href="https://laracasts.com" class="text-blue-400 hover:underline">Watch Now</a>
                </div>
                <div class="card">
                    <h2 class="text-xl font-semibold">Laravel News</h2>
                    <p>Stay updated with the latest news and updates in the Laravel ecosystem.</p>
                    <a href="https://laravel-news.com" class="text-blue-400 hover:underline">Learn More</a>
                </div>
            </main>

            <footer class="footer">
                <p>Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})</p>
            </footer>
        </div>
    </body>
</html>
````
