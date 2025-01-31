<?php

namespace App\Observers;

use App\Models\WoBacklog;
use Illuminate\Support\Facades\Log;

class WoBacklogObserver
{
    public function updated(WoBacklog $woBacklog)
    {
        Log::info('WoBacklog status updated', [
            'no_wo' => $woBacklog->no_wo,
            'old_status' => $woBacklog->getOriginal('status'),
            'new_status' => $woBacklog->status
        ]);

        if ($woBacklog->isDirty('status') && $woBacklog->status === 'Closed') {
            try {
                Log::info('Attempting to move WO back to WorkOrders', [
                    'no_wo' => $woBacklog->no_wo,
                    'type_wo' => $woBacklog->type_wo,
                    'priority' => $woBacklog->priority
                ]);
                
                $result = $woBacklog->moveBackToWorkOrder();
                
                Log::info('Move result', [
                    'success' => $result,
                    'no_wo' => $woBacklog->no_wo
                ]);
            } catch (\Exception $e) {
                Log::error('Error moving back to WorkOrder', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
    }
}
