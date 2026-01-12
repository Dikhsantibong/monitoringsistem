<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use PDO;
use PDOException;

class MaximoController extends Controller
{
    public function index()
    {
        try {
            // Cek apakah extension oci8 atau pdo_oci tersedia
            if (!extension_loaded('oci8') && !extension_loaded('pdo_oci')) {
                $error = 'Extension PHP untuk Oracle tidak ditemukan. ' .
                         'Silakan install extension oci8 atau pdo_oci. ' .
                         'Atau install package Laravel: composer require yajra/laravel-oci8';
                $formattedData = collect([]);
                return view('admin.maximo.index', compact('formattedData', 'error'));
            }

            // Cek apakah package yajra/laravel-oci8 tersedia
            if (class_exists('Yajra\Oci8\Oci8Connection')) {
                // Gunakan Laravel DB facade dengan connection 'oracle'
                return $this->useLaravelOci8();
            }

            // Fallback: Gunakan PDO langsung
            return $this->usePDODirect();
        } catch (\Exception $e) {
            Log::error('Maximo Controller Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            $error = 'Terjadi kesalahan: ' . $e->getMessage();
            $formattedData = collect([]);
            
            return view('admin.maximo.index', compact('formattedData', 'error'));
        }
    }

    /**
     * Menggunakan Laravel OCI8 package (yajra/laravel-oci8)
     */
    private function useLaravelOci8()
    {
        try {
            // Query untuk mengambil 5 data terakhir dari MAXIMO.WORKORDER
            $workOrders = DB::connection('oracle')
                ->table('MAXIMO.WORKORDER')
                ->select([
                    'WONUM',
                    'PARENT',
                    'STATUS',
                    'STATUSDATE',
                    'WORKTYPE',
                    'DESCRIPTION',
                    'ASSETNUM',
                    'LOCATION',
                    'SITEID'
                ])
                ->where('SITEID', 'KD')
                ->orderBy('STATUSDATE', 'desc')
                ->limit(5)
                ->get()
                ->toArray();

            $formattedData = $this->formatWorkOrders($workOrders);
            return view('admin.maximo.index', compact('formattedData'));
        } catch (\Exception $e) {
            Log::error('Laravel OCI8 Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Menggunakan PDO langsung
     */
    private function usePDODirect()
    {
        // Ambil konfigurasi Oracle dari .env
        $host = env('ORACLE_DB_HOST', '127.0.0.1');
        $port = env('ORACLE_DB_PORT', '1521');
        $serviceName = env('ORACLE_DB_SERVICE_NAME', '');
        $username = env('ORACLE_DB_USERNAME', '');
        $password = env('ORACLE_DB_PASSWORD', '');
        $charset = env('ORACLE_DB_CHARSET', 'AL32UTF8');

        if (empty($serviceName) || empty($username) || empty($password)) {
            throw new \Exception('Konfigurasi Oracle database tidak lengkap. Pastikan ORACLE_DB_SERVICE_NAME, ORACLE_DB_USERNAME, dan ORACLE_DB_PASSWORD sudah diatur di file .env');
        }

        // Buat connection string untuk Oracle
        $tns = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST={$host})(PORT={$port}))(CONNECT_DATA=(SERVICE_NAME={$serviceName})))";
        
        // Coba koneksi menggunakan PDO
        $pdo = new PDO("oci:dbname={$tns};charset={$charset}", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // Query untuk mengambil 5 data terakhir dari MAXIMO.WORKORDER
        // Filter hanya SITEID = 'KD'
        // Menggunakan subquery dengan ROWNUM untuk kompatibilitas Oracle
        $sql = "SELECT * FROM (
                    SELECT 
                        WONUM,
                        PARENT,
                        STATUS,
                        STATUSDATE,
                        WORKTYPE,
                        DESCRIPTION,
                        ASSETNUM,
                        LOCATION,
                        SITEID
                    FROM MAXIMO.WORKORDER 
                    WHERE SITEID = 'KD'
                    ORDER BY STATUSDATE DESC
                ) WHERE ROWNUM <= 5";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $workOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $formattedData = $this->formatWorkOrders($workOrders);
        return view('admin.maximo.index', compact('formattedData'));
    }

    /**
     * Format data work orders untuk view
     */
    private function formatWorkOrders($workOrders)
    {
        return collect($workOrders)->map(function ($wo) {
            // Handle tanggal Oracle (bisa berupa string atau object)
            $statusDate = $wo['STATUSDATE'] ?? null;
            if ($statusDate) {
                try {
                    // Jika berupa string, parse ke Carbon
                    if (is_string($statusDate)) {
                        $statusDate = Carbon::parse($statusDate);
                    } elseif (is_object($statusDate) && method_exists($statusDate, 'format')) {
                        // Jika sudah object datetime
                        $statusDate = Carbon::instance($statusDate);
                    }
                } catch (\Exception $e) {
                    $statusDate = $wo['STATUSDATE'];
                }
            }

            return [
                'wonum' => $wo['WONUM'] ?? '-',
                'parent' => $wo['PARENT'] ?? '-',
                'status' => $wo['STATUS'] ?? '-',
                'statusdate' => $statusDate,
                'worktype' => $wo['WORKTYPE'] ?? '-',
                'description' => $wo['DESCRIPTION'] ?? '-',
                'assetnum' => $wo['ASSETNUM'] ?? '-',
                'location' => $wo['LOCATION'] ?? '-',
                'siteid' => $wo['SITEID'] ?? '-',
            ];
        });
    }
}
