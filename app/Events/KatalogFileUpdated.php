<?php
namespace App\Events;
use App\Models\KatalogFile;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KatalogFileUpdated
{
    use Dispatchable, SerializesModels;
    public $katalogFile;
    public $action;
    public function __construct(KatalogFile $katalogFile, $action)
    {
        $this->katalogFile = $katalogFile;
        $this->action = $action;
    }
}
