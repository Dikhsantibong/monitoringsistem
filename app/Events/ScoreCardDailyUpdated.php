<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ScoreCardDaily;

class ScoreCardDailyUpdated
{
    use Dispatchable, SerializesModels;

    public $scoreCard;
    public $sourceUnit;
    public $action;

    public function __construct(ScoreCardDaily $scoreCard, string $action)
    {
        $this->scoreCard = $scoreCard;
        $this->sourceUnit = session('unit');
        $this->action = $action;
    }
} 