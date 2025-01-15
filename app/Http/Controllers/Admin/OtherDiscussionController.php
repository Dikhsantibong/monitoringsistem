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
    public function index(Request $request)
    {
        $query = OtherDiscussion::with('commitments');
        
        // Filter berdasarkan pencarian
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('topic', 'like', "%{$request->search}%")
                  ->orWhere('pic', 'like', "%{$request->search}%")
                  ->orWhere('unit', 'like', "%{$request->search}%");
            });
        }

        // Data aktif (status Open dan belum melewati deadline)
        $activeDiscussions = clone $query;
        $activeDiscussions = $activeDiscussions
            ->where('status', 'Open')
            ->where(function($q) {
                $q->where('target_deadline', '>', now())
                  ->orWhereNull('target_deadline');
            })
            ->paginate(10, ['*'], 'active_page');

        // Data yang melewati deadline target
        $targetOverdueDiscussions = clone $query;
        $targetOverdueDiscussions = $targetOverdueDiscussions
            ->where('status', 'Open')
            ->whereNotNull('target_deadline')
            ->where('target_deadline', '<', now())
            ->paginate(10, ['*'], 'target_page');

        // Data yang melewati deadline komitmen
        $commitmentOverdueDiscussions = clone $query;
        $commitmentOverdueDiscussions = $commitmentOverdueDiscussions
            ->where('status', 'Open')
            ->whereHas('commitments', function($q) {
                $q->where('deadline', '<', now());
            })
            ->paginate(10, ['*'], 'commitment_page');

        // Data selesai
        $closedDiscussions = ClosedDiscussion::with('commitments')
            ->orderBy('closed_at', 'desc')
            ->paginate(10, ['*'], 'closed_page');

        // Ambil data unit untuk filter
        $units = OtherDiscussion::getUnits();

        return view('admin.other-discussions.index', compact(
            'activeDiscussions',
            'targetOverdueDiscussions',
            'commitmentOverdueDiscussions',
            'closedDiscussions',
            'units'
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
        try {
            $discussion = OtherDiscussion::findOrFail($request->discussion_id);
            $discussion->status = $request->status;
            $discussion->save();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status: ' . $e->getMessage()
            ]);
        }
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

            DB::beginTransaction();

            // Buat diskusi baru dengan nilai default untuk field yang diperlukan
            $discussion = OtherDiscussion::create([
                'sr_number' => $validated['sr_number'],
                'wo_number' => $validated['wo_number'],
                'unit' => $validated['unit'],
                'topic' => $validated['topic'],
                'target' => $validated['target'],
                'target_deadline' => $validated['target_deadline'],
                'risk_level' => $validated['risk_level'],
                'priority_level' => $validated['priority_level'],
                'pic' => $validated['pic'],
                'status' => 'Open',
                'previous_commitment' => '-', // Tambahkan nilai default
                'next_commitment' => '-'      // Tambahkan nilai default
            ]);

            // Simpan komitmen
            foreach ($request->commitments as $index => $commitment) {
                $discussion->commitments()->create([
                    'description' => $commitment,
                    'deadline' => $request->commitment_deadlines[$index]
                ]);
            }

            DB::commit();
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
                    'deadline' => $request->commitment_deadlines[$index]
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