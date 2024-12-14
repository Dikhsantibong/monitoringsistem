<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SetDatabaseConnection
{
    public function handle($request, Closure $next)
    {
        // Ambil nama unit dari session
        $unit = session('unit', 'up_kendari'); // Default ke up_kendari jika session kosong

        // Validasi unit database
        $validUnits = [
            'up_kendari',
            'ulpltd_bau_bau',
            'ulpltd_kolaka',
            'ulpltd_poasia',
            'ulpltd_wua_wua',
        ];

        if (!in_array($unit, $validUnits)) {
            abort(403, 'Unit database tidak valid.');
        }

        // Set koneksi database
        Config::set('database.connections.mysql.database', $unit);

        // Purge koneksi agar perubahan diterapkan
        DB::purge('mysql');

        return $next($request);
    }
}
