<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\OtherDiscussion;

class OtherDiscussionUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $discussion;
    public $action;
    public $sourceUnit;

    public function __construct($discussion, $action)
    {
        $this->discussion = $discussion;
        $this->action = $action;
        $this->sourceUnit = session('unit', 'mysql');
    }
} 