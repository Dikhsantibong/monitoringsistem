<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OverdueDiscussion;
use App\Models\ClosedDiscussion;
use App\Models\OtherDiscussion;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class OverdueDiscussionController extends Controller
{
    public function destroy(OverdueDiscussion $discussion)
    {
        try {
            $discussion->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting overdue discussion: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data'
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $overdueDiscussion = OverdueDiscussion::findOrFail($id);
            $originalDiscussion = OtherDiscussion::findOrFail($overdueDiscussion->original_id);

            // Update status menjadi 'Closed' saja
            $overdueDiscussion->update([
                'status' => 'Closed',
                'closed_at' => now()
            ]);

            $originalDiscussion->update([
                'status' => 'Closed'
            ]);

            // Pindahkan ke tabel closed_discussions
            ClosedDiscussion::updateOrCreate(
                ['original_id' => $overdueDiscussion->original_id],
                [
                    'sr_number' => $overdueDiscussion->sr_number,
                    'wo_number' => $overdueDiscussion->wo_number,
                    'unit' => $overdueDiscussion->unit,
                    'topic' => $overdueDiscussion->topic,
                    'target' => $overdueDiscussion->target,
                    'risk_level' => $overdueDiscussion->risk_level,
                    'priority_level' => $overdueDiscussion->priority_level,
                    'previous_commitment' => $overdueDiscussion->previous_commitment,
                    'next_commitment' => $overdueDiscussion->next_commitment,
                    'pic' => $overdueDiscussion->pic,
                    'status' => 'Closed',
                    'deadline' => $overdueDiscussion->deadline,
                    'closed_at' => now()
                ]
            );

            // Hapus data dari tabel overdue
            $overdueDiscussion->delete();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in updateStatus: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status'
            ], 500);
        }
    }

    public function checkAndMoveOverdue()
    {
        try {
            DB::beginTransaction();
            
            \Log::info('Checking for overdue discussions...');

            // Ambil semua diskusi aktif yang sudah melewati deadline
            $overdueDiscussions = OtherDiscussion::where('status', 'Open')
                ->where('deadline', '<', Carbon::now()->format('Y-m-d'))
                ->get();

            \Log::info('Found ' . count($overdueDiscussions) . ' overdue discussions', [
                'discussions' => $overdueDiscussions->toArray()
            ]);

            $count = 0;
            foreach ($overdueDiscussions as $discussion) {
                try {
                    // Cek apakah sudah ada di tabel overdue
                    $existingOverdue = OverdueDiscussion::where('original_id', $discussion->id)->first();
                    
                    if (!$existingOverdue) {
                        // Buat record baru di tabel overdue_discussions dengan status tetap 'Open'
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
                            'status' => 'Open', // Tetap gunakan status 'Open'
                            'deadline' => $discussion->deadline,
                            'overdue_at' => now(),
                            'original_id' => $discussion->id,
                            'unit_source' => session('unit', 'default')
                        ]);

                        // Status diskusi asli tetap 'Open'
                        $discussion->update(['status' => 'Open']);
                        $count++;
                        
                        \Log::info('Successfully moved discussion to overdue', [
                            'discussion_id' => $discussion->id
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error processing discussion: ' . $e->getMessage(), [
                        'discussion_id' => $discussion->id
                    ]);
                    continue;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil memindahkan data yang melewati deadline',
                'count' => $count
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error in checkAndMoveOverdue: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memindahkan data: ' . $e->getMessage()
            ], 500);
        }
    }   
} 