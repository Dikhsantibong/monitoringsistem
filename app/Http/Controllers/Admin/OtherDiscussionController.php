<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OtherDiscussion;
use App\Models\ClosedDiscussion;
use App\Models\OverdueDiscussion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\PowerPlant;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OtherDiscussionController extends Controller
{
    public function index()
    {
        // Data aktif
        $activeDiscussions = OtherDiscussion::active()
            ->with('commitments')
            ->paginate(10, ['*'], 'active_page');
        
        // Data melewati target
        $targetOverdueDiscussions = OtherDiscussion::targetOverdue()
            ->with('commitments')
            ->paginate(10, ['*'], 'target_page');
        
        // Data melewati komitmen
        $commitmentOverdueDiscussions = OtherDiscussion::commitmentOverdue()
            ->with('commitments')
            ->paginate(10, ['*'], 'commitment_page');
        
        // Data selesai
        $closedDiscussions = OtherDiscussion::closed()
            ->with('commitments')
            ->paginate(10, ['*'], 'closed_page');

        // Hitung total untuk badge
        $counts = [
            'active' => OtherDiscussion::active()->count(),
            'target_overdue' => OtherDiscussion::targetOverdue()->count(),
            'commitment_overdue' => OtherDiscussion::commitmentOverdue()->count(),
            'closed' => OtherDiscussion::closed()->count()
        ];

        return view('admin.other-discussions.index', compact(
            'activeDiscussions',
            'targetOverdueDiscussions',
            'commitmentOverdueDiscussions',
            'closedDiscussions',
            'counts'
        ));
    }

    public function destroy($id)
    {
        try {
            $discussion = OtherDiscussion::findOrFail($id);
            
            DB::beginTransaction();
            
            $discussion->delete();
            
            DB::commit();
            
            return redirect()
                ->route('admin.other-discussions.index')
                ->with('success', 'Data berhasil dihapus');
                
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error deleting discussion: ' . $e->getMessage());
            
            return redirect()
                ->route('admin.other-discussions.index')
                ->with('error', 'Gagal menghapus data');
        }
    }

    public function destroyOverdue($id)
    {
        try {
            $discussion = OtherDiscussion::where('id', $id)
                ->where('status', 'Overdue')
                ->firstOrFail();
                
            DB::beginTransaction();
            
            $discussion->delete();
            
            DB::commit();
            
            return redirect()
                ->route('admin.other-discussions.index')
                ->with('success', 'Data berhasil dihapus');
                
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error deleting overdue discussion: ' . $e->getMessage());
            
            return redirect()
                ->route('admin.other-discussions.index')
                ->with('error', 'Gagal menghapus data');
        }
    }

    public function updateStatus(Request $request)
    {
        $discussion = OtherDiscussion::findOrFail($request->discussion_id);
        $discussion->status = $request->status;
        
        if ($request->status === 'Closed') {
            $discussion->closed_at = now();
        }
        
        $discussion->save();

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui'
        ]);
    }

    public function create()
    {
        try {
            $units = PowerPlant::pluck('name')->toArray();
            return view('admin.other-discussions.create', compact('units'));
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memuat halaman');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'sr_number' => 'required',
                'wo_number' => 'required',
                'unit' => 'required',
                'topic' => 'required',
                'target' => 'required',
                'target_deadline' => 'required|date',
                'risk_level' => 'required',
                'priority_level' => 'required',
                'commitments' => 'required|array|min:1',
                'commitment_deadlines' => 'required|array|min:1',
                'commitment_deadlines.*' => 'required|date',
                'pic' => 'required',
            ]);

            DB::transaction(function () use ($request, $discussion) {
                // Simpan discussion
                $discussion = OtherDiscussion::create($request->except('commitments', 'commitment_deadlines', 'commitment_pics'));
                
                // Simpan commitments dengan PIC
                foreach ($request->commitments as $index => $commitment) {
                    $discussion->commitments()->create([
                        'description' => $commitment,
                        'deadline' => $request->commitment_deadlines[$index],
                        'pic_id' => $request->commitment_pics[$index]
                    ]);
                }
            });

            return redirect()
                ->route('admin.other-discussions.index')
                ->with('success', 'Data berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $discussion = OtherDiscussion::findOrFail($id);
        return view('admin.other-discussions.edit', compact('discussion'));
    }


    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $discussion = OtherDiscussion::findOrFail($id);

            $validated = $request->validate([
                'sr_number' => 'required',
                'wo_number' => 'required',
                'unit' => 'required',
                'topic' => 'required',
                'target' => 'required',
                'target_deadline' => 'required|date',
                'risk_level' => 'required',
                'priority_level' => 'required',
                'commitments' => 'required|array|min:1',
                'commitment_deadlines' => 'required|array|min:1',
                'commitment_deadlines.*' => 'required|date',
                'pic' => 'required',
                'status' => 'required|in:Open,Closed'
            ]);

            // Update diskusi
            $discussion->update([
                'sr_number' => $validated['sr_number'],
                'wo_number' => $validated['wo_number'],
                'unit' => $validated['unit'],
                'topic' => $validated['topic'],
                'target' => $validated['target'],
                'target_deadline' => $validated['target_deadline'],
                'risk_level' => $validated['risk_level'],
                'priority_level' => $validated['priority_level'],
                'pic' => $validated['pic'],
                'status' => $validated['status']
            ]);

            // Update komitmen
            // Hapus komitmen lama
            $discussion->commitments()->delete();
            
            // Tambah komitmen baru
            foreach ($request->commitments as $index => $commitment) {
                $discussion->commitments()->create([
                    'description' => $commitment,
                    'deadline' => $request->commitment_deadlines[$index],
                    'pic' => $request->commitment_pics[$index]
                ]);
            }

            DB::commit();
            return redirect()->route('admin.other-discussions.index')->with('success', 'Data berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }
}