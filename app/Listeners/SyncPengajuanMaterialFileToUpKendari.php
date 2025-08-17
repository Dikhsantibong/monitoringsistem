<?php
namespace App\Listeners;

use App\Events\PengajuanMaterialFileUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncPengajuanMaterialFileToUpKendari
{
    public function handle(PengajuanMaterialFileUpdated $event)
    {
        $file = $event->pengajuanMaterialFile;
        $action = $event->action;
        $currentSession = session('unit', 'mysql');
        if ($currentSession === 'mysql') {
            // Jangan sinkronisasi jika sudah di DB utama
            return;
        }
        $targetDB = DB::connection('mysql');
        $data = [
            'id' => $file->id,
            'user_id' => session('unit', 'unknown_unit'),
            'filename' => $file->filename,
            'path' => $file->path,
            'created_at' => $file->created_at,
            'updated_at' => $file->updated_at,
        ];
        switch ($action) {
            case 'create':
                $targetDB->table('pengajuan_material_files')->insertOrIgnore($data);
                break;
            case 'update':
                $targetDB->table('pengajuan_material_files')->where('id', $file->id)->update($data);
                break;
            case 'delete':
                $targetDB->table('pengajuan_material_files')->where('id', $file->id)->delete();
                break;
        }
        Log::info('SyncPengajuanMaterialFileToUpKendari', [
            'action' => $action,
            'file_id' => $file->id,
            'session' => $currentSession,
            'filename' => $file->filename
        ]);
    }
}
