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
        'pasarwajo'    => 'PASR',
    ];

    /**
     * Mapping akun "parent" ke daftar email prefix "child" yang boleh dilihat.
     * Akun parent otomatis juga melihat datanya sendiri.
     * Akun yang TIDAK terdaftar sebagai parent hanya bisa melihat data sendiri.
     */
    protected static $parentChildMapping = [
        'bau-bau' => ['bau-bau', 'winning', 'rongi', 'pasarwajo', 'ereke', 'wangi-wangi'],
        'kolaka'  => ['kolaka', 'sabilambo', 'mikuasi', 'lanipa'],
        'poasia'  => ['poasia'],
        'wua-wua' => ['wua-wua', 'langara'],
    ];

    /**
     * Mendapatkan email prefix dari user yang login.
     *
     * @return string|null
     */
    public static function getEmailPrefix(): ?string
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'pemeliharaan') {
            return null;
        }

        $email = strtolower(trim($user->email));
        return explode('@', $email)[0] ?? null;
    }

    /**
     * Mendapatkan SEMUA location prefix yang boleh diakses user yang login.
     * 
     * - Jika user adalah akun "parent" (bau-bau, kolaka, poasia, wua-wua),
     *   maka return semua prefix child + miliknya sendiri.
     * - Jika user adalah akun "child" biasa, hanya return prefix milik sendiri.
     * - Jika tidak ditemukan mapping, return null.
     *
     * @return array|null  Array of prefix strings, atau null jika bukan role pemeliharaan.
     */
    public static function getLocationPrefixes(): ?array
    {
        $emailPrefix = static::getEmailPrefix();

        if (!$emailPrefix) {
            return null;
        }

        // Cek apakah user ini adalah akun parent
        if (isset(static::$parentChildMapping[$emailPrefix])) {
            $childEmails = static::$parentChildMapping[$emailPrefix];
            $prefixes = [];

            // Tambahkan dukungan filter dari URL ?unit_prefix=
            $requestedPrefix = request('unit_prefix');

            foreach ($childEmails as $childEmail) {
                if (isset(static::$emailToLocationPrefix[$childEmail])) {
                    $prefix = static::$emailToLocationPrefix[$childEmail];
                    // Jika ada request filter dan cocok, kembalikan HANYA prefix ini
                    if ($requestedPrefix && $requestedPrefix === $prefix) {
                        return [$prefix];
                    }
                    $prefixes[] = $prefix;
                }
            }

            return !empty($prefixes) ? $prefixes : null;
        }

        // Akun child biasa: hanya return prefix sendiri
        if (isset(static::$emailToLocationPrefix[$emailPrefix])) {
            // Walaupun ada request unit_prefix, kita abaikan karena akun child hanya bisa lihat miliknya sendiri
            return [static::$emailToLocationPrefix[$emailPrefix]];
        }

        return null;
    }

    /**
     * Mendapatkan daftar unit cabang (child) untuk ditampilkan di sidebar filter.
     * Hanya berlaku untuk akun parent. Return format: [ 'PREFIX' => 'Nama Unit' ]
     *
     * @return array
     */
    public static function getChildUnits(): array
    {
        $emailPrefix = static::getEmailPrefix();
        $units = [];

        if ($emailPrefix && isset(static::$parentChildMapping[$emailPrefix])) {
            $childEmails = static::$parentChildMapping[$emailPrefix];
            foreach ($childEmails as $childEmail) {
                if (isset(static::$emailToLocationPrefix[$childEmail])) {
                    $prefix = static::$emailToLocationPrefix[$childEmail];
                    // Format nama: "bau-bau" -> "Bau-bau"
                    $units[$prefix] = ucwords(str_replace('-', ' ', $childEmail));
                }
            }
        }

        return $units;
    }

    /**
     * Backward compatibility: Mendapatkan 1 prefix saja (untuk akun child).
     * 
     * @return string|null
     */
    public static function getLocationPrefix(): ?string
    {
        $prefixes = static::getLocationPrefixes();
        return $prefixes ? $prefixes[0] : null;
    }

    /**
     * Apply filter LOCATION ke query builder Oracle.
     * Mendukung multi-prefix untuk akun parent.
     * 
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $locationColumn  Nama kolom LOCATION (default: 'LOCATION')
     * @return \Illuminate\Database\Query\Builder
     */
    public static function applyLocationFilter($query, string $locationColumn = 'LOCATION')
    {
        $prefixes = static::getLocationPrefixes();

        if ($prefixes && count($prefixes) > 0) {
            if (count($prefixes) === 1) {
                // Single prefix — pakai LIKE biasa
                $query->where($locationColumn, 'LIKE', $prefixes[0] . '%');
            } else {
                // Multi prefix — pakai OR
                $query->where(function ($q) use ($prefixes, $locationColumn) {
                    foreach ($prefixes as $i => $prefix) {
                        if ($i === 0) {
                            $q->where($locationColumn, 'LIKE', $prefix . '%');
                        } else {
                            $q->orWhere($locationColumn, 'LIKE', $prefix . '%');
                        }
                    }
                });
            }
        }

        return $query;
    }
}
