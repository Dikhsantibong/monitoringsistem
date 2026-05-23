<?php

namespace App\Http\Controllers\Pemeliharaan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebugMaximoController extends Controller
{
    public function debugWo(Request $request, $wonum)
    {
        $debug = [
            'status' => 'success',
            'wonum_requested' => $wonum,
            'wo_data' => null,
            'sr_data' => null,
            'wo_longdesc' => [],
            'sr_longdesc' => [],
            'all_longdesc_for_wonum' => []
        ];

        try {
            // 1. Ambil Data WO
            $wo = DB::connection('oracle')
                ->table('WORKORDER')
                ->where('WONUM', $wonum)
                ->where('SITEID', 'KD')
                ->first();
            
            if ($wo) {
                $debug['wo_data'] = $wo;
                
                // Cari LONGDESCRIPTION untuk WO (LDKEY = WORKORDERID)
                $woId = isset($wo->workorderid) ? $wo->workorderid : (isset($wo->WORKORDERID) ? $wo->WORKORDERID : null);
                if ($woId) {
                    $debug['wo_longdesc']['LONGDESCRIPTION'] = DB::connection('oracle')
                        ->table('LONGDESCRIPTION')
                        ->where('LDKEY', $woId)
                        ->get();
                        
                    $debug['wo_longdesc']['LONG_DESCRIPTION'] = DB::connection('oracle')
                        ->table('LONG_DESCRIPTION')
                        ->where('LDKEY', $woId)
                        ->get();
                }

                // Cek apakah ada ORIGRECORDID (biasanya ticketid dari SR)
                $origRecordId = isset($wo->origrecordid) ? $wo->origrecordid : (isset($wo->ORIGRECORDID) ? $wo->ORIGRECORDID : null);
                
                if ($origRecordId) {
                    // 2. Ambil Data SR
                    $sr = DB::connection('oracle')
                        ->table('SR')
                        ->where('TICKETID', $origRecordId)
                        ->where('SITEID', 'KD')
                        ->first();
                        
                    if ($sr) {
                        $debug['sr_data'] = $sr;
                        
                        // Cari LONGDESCRIPTION untuk SR (LDKEY = TICKETUID)
                        $ticketUid = isset($sr->ticketuid) ? $sr->ticketuid : (isset($sr->TICKETUID) ? $sr->TICKETUID : null);
                        $ticketId = isset($sr->ticketid) ? $sr->ticketid : (isset($sr->TICKETID) ? $sr->TICKETID : null);
                        
                        if ($ticketUid) {
                            $debug['sr_longdesc']['BY_TICKETUID']['LONGDESCRIPTION'] = DB::connection('oracle')
                                ->table('LONGDESCRIPTION')
                                ->where('LDKEY', $ticketUid)
                                ->get();
                                
                            $debug['sr_longdesc']['BY_TICKETUID']['LONG_DESCRIPTION'] = DB::connection('oracle')
                                ->table('LONG_DESCRIPTION')
                                ->where('LDKEY', $ticketUid)
                                ->get();
                        }
                        
                        if ($ticketId) {
                            $debug['sr_longdesc']['BY_TICKETID']['LONGDESCRIPTION'] = DB::connection('oracle')
                                ->table('LONGDESCRIPTION')
                                ->where('LDKEY', $ticketId)
                                ->get();
                                
                            $debug['sr_longdesc']['BY_TICKETID']['LONG_DESCRIPTION'] = DB::connection('oracle')
                                ->table('LONG_DESCRIPTION')
                                ->where('LDKEY', $ticketId)
                                ->get();
                        }

                    }
                }
            }

            // Fallback: Cari semua longdescription dengan LDKEY yang mungkin adalah ID dari WONUM tersebut
            $debug['all_longdesc_for_wonum'] = DB::connection('oracle')
                ->table('LONGDESCRIPTION')
                ->where('LDTEXT', 'LIKE', '%' . $wonum . '%')
                ->limit(5)
                ->get();

        } catch (\Exception $e) {
            $debug['status'] = 'error';
            $debug['error_message'] = $e->getMessage();
        }

        return response()->json($debug);
    }
}
