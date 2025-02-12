<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\MachineStatusLog;

class MachineStatusUpdated
{
    use Dispatchable, SerializesModels;

    public $machineStatus;
    public $sourceUnit;
    public $action;

    public function __construct(MachineStatusLog $machineStatus, string $action)
    {
        $this->machineStatus = $machineStatus;
        $this->sourceUnit = session('unit', 'mysql');
        $this->action = $action;
    }
} 