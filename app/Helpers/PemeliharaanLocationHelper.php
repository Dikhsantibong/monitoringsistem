<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class PemeliharaanLocationHelper
{
    /**
     * Mapping email prefix (sebelum @) ke 4 huruf awal kode LOCATION di Maximo.
     * 
     * Contoh: mikuasi@pemeliharaan.com => MIKU
     *         wangi-wangi@pemeliharaan.com => WANG
     */
    protected static $emailToLocationPrefix = [
        'wangi-wangi'  => 'WANG',
        'kolaka'       => 'KLKA',
        'lanipa'       => 'LANI',
        'sabilambo'    => 'SABI',
        'mikuasi'      => 'MIKU',
        'bau-bau'      => 'BBAU',
        'raha'         => 'RAHA',
        'ereke'        => 'EREK',
        'rongi'        => 'RONG',
        'winning'      => 'WINN',
        'poasia'       => 'POAS',
        'langara'      => 'LANG',
        'wua-wua'      => 'WUAW',
    ];

    /**
     * Mendapatkan 4 huruf prefix LOCATION berdasarkan email user yang login.
     * 
     * @return string|null  Prefix 4 huruf (e.g. 'MIKU') atau null jika tidak ditemukan / bukan role pemeliharaan.
     */
    public static function getLocationPrefix(): ?string
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'pemeliharaan') {
            return null;
        }

        $email = strtolower(trim($user->email));
        // Ambil bagian sebelum @
        $emailPrefix = explode('@', $email)[0] ?? '';

        foreach (static::$emailToLocationPrefix as $emailKey => $locationCode) {
            if ($emailPrefix === $emailKey) {
                return $locationCode;
            }
        }

        return null;
    }

    /**
     * Apply filter LOCATION LIKE 'PREFIX%' ke query builder Oracle.
     * Hanya diterapkan jika user role pemeliharaan dan email ter-mapping.
     * 
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public static function applyLocationFilter($query)
    {
        $prefix = static::getLocationPrefix();

        if ($prefix) {
            $query->where('LOCATION', 'LIKE', $prefix . '%');
        }

        return $query;
    }
}
