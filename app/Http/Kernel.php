<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // Mengelola kesalahan server jika terlalu sibuk
        \Illuminate\Http\Middleware\TrustHosts::class,
        // Menentukan proxy agar aplikasi Laravel dapat mengenali header proxy
        \Illuminate\Http\Middleware\TrustProxies::class,
        // Memastikan permintaan berjalan dengan aman (HTTPS)
        \Illuminate\Http\Middleware\HandleCors::class,
        // Memeriksa ukuran body permintaan
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        // Trim spasi di input
        \App\Http\Middleware\TrimStrings::class,
        // Mengkonversi string kosong menjadi NULL
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\Cors::class,
        \App\Http\Middleware\HandleDocumentDownload::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        // Middleware untuk rute web
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // Middleware untuk CSRF token
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\SetDatabaseConnection::class,
            \App\Http\Middleware\SetUnitConnection::class,
        ],

        // Middleware untuk rute API
        'api' => [
            // Membatasi jumlah permintaan API (throttling)
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
       'user' => \App\Http\Middleware\UserMiddleware::class,
        'role' => \App\Http\Middleware\CheckRole::class,
        'json.response' => \App\Http\Middleware\ForceJsonResponse::class,
    ];

    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'user' => \App\Http\Middleware\UserMiddleware::class,
    ];
}
