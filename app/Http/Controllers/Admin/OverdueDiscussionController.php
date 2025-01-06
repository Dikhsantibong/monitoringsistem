<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OverdueDiscussion;
use App\Models\ClosedDiscussion;
use App\Models\OtherDiscussion;
use Illuminate\Http\Request;
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

    public function updateStatus(Request $request, OverdueDiscussion $discussion)
    {
        try {
            DB::beginTransaction();

            // Update status di tabel original
            if ($discussion->original_id) {
                $originalDiscussion = OtherDiscussion::find($discussion->original_id);
                if ($originalDiscussion) {
                    $originalDiscussion->update(['status' => 'Closed']);
                }
            }

            // Pindahkan ke tabel closed
            $closedDiscussion = ClosedDiscussion::create([
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
                'status' => 'Closed',
                'deadline' => $discussion->deadline,
                'closed_at' => now(),
                'original_id' => $discussion->original_id
            ]);

            if (!$closedDiscussion) {
                throw new \Exception('Gagal membuat record closed discussion');
            }

            // Hapus dari tabel overdue
            $discussion->delete();

            DB::commit();

            \Log::info('Successfully moved overdue discussion to closed', [
                'overdue_id' => $discussion->id,
                'closed_id' => $closedDiscussion->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui dan data dipindahkan ke Data Selesai'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating overdue discussion status: ' . $e->getMessage());
            \Log::error('Discussion data: ', [
                'id' => $discussion->id,
                'original_id' => $discussion->original_id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }
} 