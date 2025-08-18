<?php
namespace App\Listeners;

use App\Events\KatalogFileUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncKatalogFileToUpKendari
{
    public function handle(KatalogFileUpdated $event)
    {
        $file = $event->katalogFile;
        $action = $event->action;
        $currentSession = session('unit', 'mysql');
        if ($currentSession === 'mysql') {
            // Jangan sinkronisasi jika sudah di DB utama
            return;
        }
        $targetDB = DB::connection('mysql');
        $data = [
            'id' => $file->id,
            'user_id' => $file->user_id, // Sudah nama user
            'filename' => $file->filename,
            'path' => $file->path,
            'no_part' => $file->no_part,
            'nama_material' => $file->nama_material,
            'created_at' => $file->created_at,
            'updated_at' => $file->updated_at,
        ];
        switch ($action) {
            case 'create':
                $targetDB->table('katalog_files')->insertOrIgnore($data);
                break;
            case 'update':
                $targetDB->table('katalog_files')->where('id', $file->id)->update($data);
                break;
            case 'delete':
                $targetDB->table('katalog_files')->where('id', $file->id)->delete();
                break;
        }
        Log::info('SyncKatalogFileToUpKendari', [
            'action' => $action,
            'file_id' => $file->id,
            'session' => $currentSession,
            'filename' => $file->filename
        ]);
    }
}
