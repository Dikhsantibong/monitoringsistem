<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\OtherDiscussion;

class OtherDiscussionUpdated
{
    use Dispatchable, SerializesModels;

    public $discussion;
    public $sourceUnit;
    public $action;

    public function __construct(OtherDiscussion $discussion, string $action)
    {
        $this->discussion = $discussion;
        $this->sourceUnit = session('unit');
        $this->action = $action;
    }
} 