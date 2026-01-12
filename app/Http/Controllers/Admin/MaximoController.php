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
            // Ambil konfigurasi Oracle dari .env
            $host = env('ORACLE_DB_HOST', '127.0.0.1');
            $port = env('ORACLE_DB_PORT', '1521');
            $serviceName = env('ORACLE_DB_SERVICE_NAME', '');
            $username = env('ORACLE_DB_USERNAME', '');
            $password = env('ORACLE_DB_PASSWORD', '');
            $charset = env('ORACLE_DB_CHARSET', 'AL32UTF8');

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

            // Format data untuk view
            $formattedData = collect($workOrders)->map(function ($wo) {
                // Handle tanggal Oracle (bisa berupa string atau object)
                $statusDate = $wo['STATUSDATE'] ?? null;
                if ($statusDate) {
                    try {
                        // Jika berupa string, parse ke Carbon
                        if (is_string($statusDate)) {
                            $statusDate = \Carbon\Carbon::parse($statusDate);
                        } elseif (is_object($statusDate) && method_exists($statusDate, 'format')) {
                            // Jika sudah object datetime
                            $statusDate = \Carbon\Carbon::instance($statusDate);
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

            return view('admin.maximo.index', compact('formattedData'));

        } catch (PDOException $e) {
            Log::error('Oracle Connection Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // Jika error, return view dengan error message
            $error = 'Gagal terhubung ke database Oracle: ' . $e->getMessage();
            $formattedData = collect([]);
            
            return view('admin.maximo.index', compact('formattedData', 'error'));
        } catch (\Exception $e) {
            Log::error('Maximo Controller Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            $error = 'Terjadi kesalahan: ' . $e->getMessage();
            $formattedData = collect([]);
            
            return view('admin.maximo.index', compact('formattedData', 'error'));
        }
    }
}
