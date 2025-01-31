<?php

namespace App\Observers;

use App\Models\WoBacklog;

class WoBacklogObserver
{
    public function updated(WoBacklog $woBacklog)
    {
        if ($woBacklog->isDirty('status') && $woBacklog->status === 'Closed') {
            $woBacklog->moveBackToWorkOrder();
        }
    }
}
