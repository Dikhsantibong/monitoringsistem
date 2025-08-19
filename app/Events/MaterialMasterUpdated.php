<?php
namespace App\Events;
use App\Models\MaterialMaster;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MaterialMasterUpdated
{
    use Dispatchable, SerializesModels;
    public $materialMaster;
    public $action;
    public function __construct(MaterialMaster $materialMaster, $action)
    {
        $this->materialMaster = $materialMaster;
        $this->action = $action;
    }
}
