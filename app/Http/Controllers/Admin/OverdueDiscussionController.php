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
    public function destroy($id)
    {
        try {
            $discussion = OverdueDiscussion::findOrFail($id);
            $discussion->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting overdue discussion: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $overdueDiscussion = OverdueDiscussion::findOrFail($id);
            $originalDiscussion = OtherDiscussion::findOrFail($overdueDiscussion->original_id);

            // Update status di kedua tabel
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
} 