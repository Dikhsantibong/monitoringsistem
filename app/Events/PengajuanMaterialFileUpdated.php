<?php
namespace App\Events;
use App\Models\PengajuanMaterialFile;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PengajuanMaterialFileUpdated
{
    use Dispatchable, SerializesModels;
    public $pengajuanMaterialFile;
    public $action;
    public function __construct(PengajuanMaterialFile $pengajuanMaterialFile, $action)
    {
        $this->pengajuanMaterialFile = $pengajuanMaterialFile;
        $this->action = $action;
    }
}
