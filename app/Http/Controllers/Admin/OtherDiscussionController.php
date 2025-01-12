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
            $activeDiscussions = OtherDiscussion::where('status', 'Open');
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
                'risk_level' => 'required',
                'priority_level' => 'required',
                'previous_commitment' => 'required',
                'next_commitment' => 'required',
                'pic' => 'required',
                'deadline' => 'required|date',
            ]);

            // Tambahkan status default
            $validated['status'] = 'Open';

            OtherDiscussion::create($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil ditambahkan'
                ]);
            }

            return redirect()
                ->route('admin.other-discussions.index')
                ->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan data'
                ], 500);
            }
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan data');
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
            $discussion = OtherDiscussion::findOrFail($id);

            $validated = $request->validate([
                'sr_number' => 'required',
                'wo_number' => 'required',
                'unit' => 'required',
                'topic' => 'required',
                'target' => 'required',
                'risk_level' => 'required',
                'priority_level' => 'required',
                'previous_commitment' => 'required',
                'next_commitment' => 'required',
                'pic' => 'required',
                'deadline' => 'required|date',
            ]);

            $discussion->update($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil diperbarui'
                ]);
            }

            return redirect()
                ->route('admin.other-discussions.index')
                ->with('success', 'Data berhasil diperbarui');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui data'
                ], 500);
            }
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data');
        }
    }
}