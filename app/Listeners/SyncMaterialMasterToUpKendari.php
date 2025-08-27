<?php
namespace App\Listeners;

use App\Events\MaterialMasterUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncMaterialMasterToUpKendari
{
    public function handle(MaterialMasterUpdated $event)
    {
        $material = $event->materialMaster;
        $action = $event->action;
        $currentSession = session('unit', 'mysql');
        if ($currentSession !== 'mysql') {
            // Jangan sinkronisasi jika sudah di DB unit
            return;
        }
        $unitConnections = [
            'mysql_bau_bau',
            'mysql_kolaka',
            'mysql_poasia',
            'mysql_wua_wua',
        ];
        foreach ($unitConnections as $unit) {
            try {
                $targetDB = \DB::connection($unit);
                $data = [
                    'id' => $material->id,
                    'discritc_code' => $material->discritc_code,
                    'warehouse' => $material->warehouse,
                    'bin_code' => $material->bin_code,
                    'inventory_statistic_code' => $material->inventory_statistic_code,
                    'inventory_statistic_desc' => $material->inventory_statistic_desc,
                    'material_num' => $material->material_num,
                    'stock_code' => $material->stock_code,
                    'description' => $material->description,
                    'stock_class' => $material->stock_class,
                    'stock_type' => $material->stock_type,
                    'inventory_category' => $material->inventory_category,
                    'unit_of_issue' => $material->unit_of_issue,
                    'minimum_soh' => $material->minimum_soh,
                    'maximum_soh' => $material->maximum_soh,
                    'quantity' => $material->quantity,
                    'inventory_price' => $material->inventory_price,
                    'inventory_value' => $material->inventory_value,
                    'updated_at' => $material->updated_at,
                ];
                switch ($action) {
                    case 'create':
                        $targetDB->table('material_master')->insertOrIgnore($data);
                        $logMsg = 'create success';
                        break;
                    case 'update':
                        $targetDB->table('material_master')->where('id', $material->id)->update($data);
                        $logMsg = 'update success';
                        break;
                    case 'delete':
                        $targetDB->table('material_master')->where('id', $material->id)->delete();
                        $logMsg = 'delete success';
                        break;
                }
                \Log::info('SyncMaterialMasterToUpKendari', [
                    'unit' => $unit,
                    'action' => $action,
                    'material_id' => $material->id,
                    'stock_code' => $material->stock_code,
                    'status' => $logMsg
                ]);
            } catch (\Throwable $e) {
                \Log::error('SyncMaterialMasterToUpKendari FAILED', [
                    'unit' => $unit,
                    'action' => $action,
                    'material_id' => $material->id,
                    'stock_code' => $material->stock_code,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
