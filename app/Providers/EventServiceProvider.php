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
use App\Events\KatalogFileUpdated;
use App\Listeners\SyncKatalogFileToUpKendari;
use App\Events\PengajuanMaterialFileUpdated;
use App\Listeners\SyncPengajuanMaterialFileToUpKendari;
use App\Events\MaterialMasterUpdated;
use App\Listeners\SyncMaterialMasterToUpKendari;
use App\Events\UnitStatusUpdated;
use App\Listeners\SyncUnitStatusToUpKendari;

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
        \App\Events\KatalogFileUpdated::class => [
            \App\Listeners\SyncKatalogFileToUpKendari::class,
        ],
        \App\Events\PengajuanMaterialFileUpdated::class => [
            \App\Listeners\SyncPengajuanMaterialFileToUpKendari::class,
        ],
        \App\Events\MaterialMasterUpdated::class => [
            \App\Listeners\SyncMaterialMasterToUpKendari::class,
        ],
    ];
} 