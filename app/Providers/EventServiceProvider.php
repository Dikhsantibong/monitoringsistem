<?php

namespace App\Providers;

use App\Models\ServiceRequest;
use App\Observers\ServiceRequestObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\MachineStatusUpdated;
use App\Listeners\SyncMachineStatusToUpKendari;
use App\Events\OtherDiscussionUpdated;
use App\Listeners\SyncOtherDiscussionToUpKendari;
use App\Events\ScoreCardDailyUpdated;
use App\Listeners\SyncScoreCardDailyToUpKendari;
use App\Events\PesertaUpdated;
use App\Listeners\SyncPesertaToUpKendari;

class EventServiceProvider extends ServiceProvider
{
    public function boot()
    {
        parent::boot();
        ServiceRequest::observe(ServiceRequestObserver::class);
    }

    protected $listen = [
        MachineStatusUpdated::class => [
            SyncMachineStatusToUpKendari::class,
        ],
        OtherDiscussionUpdated::class => [
            SyncOtherDiscussionToUpKendari::class,  
        ],
        ScoreCardDailyUpdated::class => [
            SyncScoreCardDailyToUpKendari::class,
        ],
        PesertaUpdated::class => [
            SyncPesertaToUpKendari::class,
        ],
    ];
} 