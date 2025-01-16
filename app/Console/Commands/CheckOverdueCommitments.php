<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Commitment;
use Carbon\Carbon;

class CheckOverdueCommitments extends Command
{
    protected $signature = 'commitments:check-overdue';
    protected $description = 'Check for overdue commitments and update their status';

    public function handle()
    {
        $overdueCommitments = Commitment::where('status', 'Open')
            ->where('deadline', '<', Carbon::now()->startOfDay())
            ->get();

        foreach ($overdueCommitments as $commitment) {
            $commitment->status = 'Overdue';
            $commitment->save();
        }

        $this->info("Found {$overdueCommitments->count()} overdue commitments");
    }
} 