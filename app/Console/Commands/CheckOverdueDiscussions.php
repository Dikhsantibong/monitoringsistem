<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OtherDiscussion;
use App\Models\OverdueDiscussion;
use Carbon\Carbon;
use DB;

class CheckOverdueDiscussions extends Command
{
    protected $signature = 'discussions:check-overdue';
    protected $description = 'Check and move overdue discussions to overdue table';

    public function handle()
    {
        $this->info('Checking for overdue discussions...');

        try {
            DB::beginTransaction();

            // Ambil semua diskusi aktif yang sudah melewati deadline
            $overdueDiscussions = OtherDiscussion::where('status', 'Open')
                ->where('deadline', '<', Carbon::today())
                ->get();

            $count = 0;

            foreach ($overdueDiscussions as $discussion) {
                // Pindahkan ke tabel overdue
                OverdueDiscussion::create([
                    'sr_number' => $discussion->sr_number,
                    'wo_number' => $discussion->wo_number,
                    'unit' => $discussion->unit,
                    'topic' => $discussion->topic,
                    'target' => $discussion->target,
                    'risk_level' => $discussion->risk_level,
                    'priority_level' => $discussion->priority_level,
                    'previous_commitment' => $discussion->previous_commitment,
                    'next_commitment' => $discussion->next_commitment,
                    'pic' => $discussion->pic,
                    'status' => 'Overdue',
                    'deadline' => $discussion->deadline,
                    'overdue_at' => now(),
                    'original_id' => $discussion->id
                ]);

                // Update status di tabel original
                $discussion->update(['status' => 'Overdue']);
                
                $count++;
            }

            DB::commit();

            $this->info("Successfully moved {$count} overdue discussions.");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: ' . $e->getMessage());
            \Log::error('Error in CheckOverdueDiscussions: ' . $e->getMessage());
        }
    }
} 