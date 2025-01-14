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
        try {
            // Query dasar
            $activeDiscussions = OtherDiscussion::with('commitments')->where('status', 'Open');
            $closedDiscussions = ClosedDiscussion::query();
            $overdueDiscussions = OverdueDiscussion::query();

            // Filter pencarian jika ada
            if ($request->filled('search')) {
                $search = $request->search;
                $searchCondition = function ($q) use ($search) {
                    $q->where('topic', 'like', "%{$search}%")
                        ->orWhere('pic', 'like', "%{$search}%")
                        ->orWhere('unit', 'like', "%{$search}%");
                };

                $activeDiscussions->where($searchCondition);
                $closedDiscussions->where($searchCondition);
                $overdueDiscussions->where($searchCondition);
            }

            // Ambil data
            $data = [
                'activeDiscussions' => $activeDiscussions->latest()->paginate(10, ['*'], 'active_page'),
                'closedDiscussions' => $closedDiscussions->latest()->paginate(10, ['*'], 'closed_page'),
                'overdueDiscussions' => $overdueDiscussions->latest()->paginate(10, ['*'], 'overdue_page'),
                'units' => PowerPlant::pluck('name')->toArray()
            ];

            return view('admin.other-discussions.index', $data);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memuat data');
        }
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

    public function updateStatus(Request $request, OtherDiscussion $discussion)
    {
        try {
            if (!in_array($request->status, ['Closed', 'Overdue'])) {
                throw new \Exception('Status tidak valid');
            }

            DB::transaction(function () use ($discussion, $request) {
                // Update status diskusi utama
                $discussion->update(['status' => $request->status]);

                // Data untuk tabel terkait
                $commonData = [
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
                    'deadline' => $discussion->deadline,
                    'original_id' => $discussion->id,
                    'status' => $request->status
                ];

                // Buat record baru sesuai status
                if ($request->status === 'Closed') {
                    ClosedDiscussion::create($commonData + ['closed_at' => now()]);
                } else {
                    OverdueDiscussion::create($commonData + ['overdue_at' => now()]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status'
            ], 500);
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

            // Buat diskusi baru
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
                'status' => 'Open'
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
        try {
            $discussion = OtherDiscussion::findOrFail($id);
            $units = PowerPlant::pluck('name')->toArray();
            
           
            return view('admin.other-discussions.edit', compact('discussion', 'units'));
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memuat data');
        }
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