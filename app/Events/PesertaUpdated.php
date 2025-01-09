<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Peserta;

class PesertaUpdated
{
    use Dispatchable, SerializesModels;

    public $peserta;
    public $sourceUnit;
    public $action;

    public function __construct(Peserta $peserta, string $action)
    {
        $this->peserta = $peserta;
        $this->sourceUnit = session('unit');
        $this->action = $action;
    }
} 