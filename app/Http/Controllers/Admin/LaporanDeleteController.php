<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\WorkOrder;
use App\Models\WoBacklog;
use App\Models\PowerPlant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LaporanDeleteController extends Controller
{
    public function destroy($type, $id)
    {
        try {
            DB::beginTransaction();

            switch ($type) {
                case 'sr':
                    $item = ServiceRequest::findOrFail($id);
                    $message = 'Service Request berhasil dihapus';
                    break;
                case 'wo':
                    $item = WorkOrder::findOrFail($id);
                    $message = 'Work Order berhasil dihapus';
                    break;
                case 'backlog':
                    $item = WoBacklog::findOrFail($id);
                    $powerPlant = PowerPlant::find($item->power_plant_id);
                    
                    // Jika bukan database utama, lakukan sinkronisasi
                    if ($powerPlant && $powerPlant->unit_source !== 'mysql') {
                        $targetConnection = PowerPlant::getConnectionByUnitSource($powerPlant->unit_source);
                        
                        Log::info('Deleting backlog from target database:', [
                            'id' => $id,
                            'connection' => $targetConnection,
                            'unit' => $powerPlant->name
                        ]);

                        // Hapus di database target
                        DB::connection($targetConnection)
                            ->table('wo_backlogs')
                            ->where('id', $id)
                            ->delete();
                    }
                    
                    $message = 'WO Backlog berhasil dihapus';
                    break;
                default:
                    throw new \Exception('Tipe data tidak valid');
            }

            // Hapus di database utama
            $item->delete();
            
            DB::commit();

            Log::info("Successfully deleted {$type}", [
                'id' => $id,
                'type' => $type
            ]);

            return redirect()
                ->route('admin.laporan.manage')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error deleting {$type}:", [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->route('admin.laporan.manage')
                ->with('error', 'Terjadi kesalahan saat menghapus data');
        }
    }
} 