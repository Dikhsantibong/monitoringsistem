<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebugOracleController extends Controller
{
    public function index(Request $request)
    {
        $table = strtoupper($request->input('table', 'LONGDESCRIPTION'));
        $column = strtoupper($request->input('column', 'LDKEY'));
        $value = $request->input('value', '');
        $results = [];
        $error = null;

        if ($value) {
            try {
                // Determine if value is numeric or string
                $queryValue = is_numeric($value) ? (int)$value : $value;

                $query = DB::connection('oracle')->table($table);
                if ($column) {
                    // Try exact match first
                    $query->where($column, $queryValue);
                }
                $results = $query->take(20)->get();

                // Convert any CLOB/resources to string
                $results = $results->map(function ($item) {
                    $arr = (array) $item;
                    foreach ($arr as $k => $v) {
                        if (is_resource($v)) {
                            $arr[$k] = stream_get_contents($v);
                        } elseif (is_object($v) && method_exists($v, 'load')) {
                            $arr[$k] = $v->load(); // for Oracle OCI-Lob objects
                        }
                    }
                    return (object) $arr;
                });
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        return view('pemeliharaan.debug-oracle', compact('table', 'column', 'value', 'results', 'error'));
    }
}
