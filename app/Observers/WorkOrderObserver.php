<?php

namespace App\Observers;

use App\Models\WorkOrder;

class WorkOrderObserver
{
    public function retrieved(WorkOrder $workOrder)
    {
        $workOrder->checkAndMoveToBacklog();
    }

    public function updated(WorkOrder $workOrder)
    {
        if ($workOrder->isDirty('schedule_finish')) {
            $workOrder->checkAndMoveToBacklog();
        }
    }
} 