<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\UnitStatus;

class UnitStatusUpdated
{
    use Dispatchable, SerializesModels;

    public $unitStatus;
    public $sourceUnit;
    public $action;

    public function __construct(UnitStatus $unitStatus, string $action)
    {
        $this->unitStatus = $unitStatus;
        $this->sourceUnit = session('unit', 'mysql');
        $this->action = $action;
    }
}
