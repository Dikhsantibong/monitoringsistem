<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearLogsCommand extends Command
{
    protected $signature = 'logs:clear';
    protected $description = 'Clear Laravel log files';

    public function handle()
    {
        exec('rm ' . storage_path('logs/*.log'));
        $this->info('Logs have been cleared!');
    }
} 