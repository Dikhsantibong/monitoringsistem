<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OtherDiscussion;
use App\Models\ClosedDiscussion;
use App\Models\OverdueDiscussion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OtherDiscussionController extends Controller
{   
    public function index(Request $request)
    {
        try {
            DB::beginTransaction();

            // Cek dan update status overdue terlebih dahulu
            $overdueCount = $this->checkAndUpdateOverdueStatus();

            $baseQuery = OtherDiscussion::query();
            $closedQuery = ClosedDiscussion::query();
            $overdueQuery = OverdueDiscussion::query();

            // Filter yang sudah ada
            if ($request->filled('search')) {
                $search = $request->search;
                $baseQuery->where(function($q) use ($search) {
                    $q->where('topic', 'like', "%{$search}%")
                      ->orWhere('pic', 'like', "%{$search}%")
                      ->orWhere('unit', 'like', "%{$search}%");
                });
                
                $closedQuery->where(function($q) use ($search) {
                    $q->where('topic', 'like', "%{$search}%")
                      ->orWhere('pic', 'like', "%{$search}%")
                      ->orWhere('unit', 'like', "%{$search}%");
                });

                $overdueQuery->where(function($q) use ($search) {
                    $q->where('topic', 'like', "%{$search}%")
                      ->orWhere('pic', 'like', "%{$search}%")
                      ->orWhere('unit', 'like', "%{$search}%");
                });
            }

            $activeDiscussions = $baseQuery->where('status', 'Open')->latest()->paginate(10, ['*'], 'active_page');
            $closedDiscussions = $closedQuery->latest()->paginate(10, ['*'], 'closed_page');
            $overdueDiscussions = $overdueQuery->latest()->paginate(10, ['*'], 'overdue_page');

            DB::commit();

            if ($overdueCount > 0) {
                session()->flash('info', "Terdapat $overdueCount diskusi yang telah dipindahkan ke status overdue");
            }

            return view('admin.other-discussions.index', compact('activeDiscussions', 'closedDiscussions', 'overdueDiscussions'));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in OtherDiscussionController@index: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()->with('error', 'Terjadi kesalahan saat memuat data. Silakan coba lagi.');
        }
    }

    private function checkAndUpdateOverdueStatus()
    {
        $count = 0;

        try {
            // Ambil semua diskusi yang masih Open dan sudah melewati deadline
            $overdueDiscussions = OtherDiscussion::where('status', 'Open')
                ->whereDate('deadline', '<', now()->format('Y-m-d'))
                ->get();

            foreach ($overdueDiscussions as $discussion) {
                try {
                    // Buat record di tabel overdue
                    $overdueData = [
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
                        'overdue_at' => now(),
                        'original_id' => $discussion->id
                        // Status akan menggunakan default 'Open'
                    ];

                    OverdueDiscussion::create($overdueData);

                    // Update status diskusi original
                    $discussion->update(['status' => 'Overdue']);

                    $count++;
                    
                    \Log::info("Successfully moved discussion ID {$discussion->id} to overdue");
                } catch (\Exception $e) {
                    \Log::error("Error moving discussion ID {$discussion->id} to overdue: " . $e->getMessage());
                    continue;
                }
            }

            return $count;

        } catch (\Exception $e) {
            \Log::error('Error in checkAndUpdateOverdueStatus: ' . $e->getMessage());
            return 0;
        }
    }

    public function create()
    {
        return view('admin.other-discussions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sr_number' => 'nullable|string|max:255',
            'wo_number' => 'nullable|string|max:255',
            'unit' => 'required|string|max:255',
            'topic' => 'required|string|max:255',
            'target' => 'required|string',
            'risk_level' => 'required|string|max:255',
            'priority_level' => 'required|string|max:255',
            'previous_commitment' => 'required|string',
            'next_commitment' => 'required|string',
            'pic' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'deadline' => 'required|date',
        ]);

        OtherDiscussion::create($validated);

        return redirect()
            ->route('admin.other-discussions.index')
            ->with('success', 'Data pembahasan berhasil ditambahkan');
    }

    public function edit($id)
    {
        try {
            $discussion = OtherDiscussion::findOrFail($id);
            return view('admin.other-discussions.edit', compact('discussion'));
        } catch (\Exception $e) {
            \Log::error('Error in edit: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat form edit');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $discussion = OtherDiscussion::findOrFail($id);
            
            $validated = $request->validate([
                'sr_number' => 'nullable|string',
                'wo_number' => 'nullable|string',
                'unit' => 'required|string',
                'topic' => 'required|string',
                'target' => 'required|string',
                'risk_level' => 'required|string',
                'priority_level' => 'required|string',
                'previous_commitment' => 'required|string',
                'next_commitment' => 'required|string',
                'pic' => 'required|string',
                'status' => 'required|string',
                'deadline' => 'required|date'
            ]);

            $discussion->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui',
                'redirect_url' => route('admin.other-discussions.index')
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in update: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $discussion = OtherDiscussion::findOrFail($id);
        $discussion->delete();

        return redirect()
            ->route('admin.other-discussions.index')
            ->with('success', 'Data pembahasan berhasil dihapus');
    }

    public function updateStatus(Request $request, OtherDiscussion $discussion)
    {
        try {
            DB::beginTransaction();

            $status = $request->status;
            
            if (!in_array($status, ['Closed', 'Overdue'])) {
                throw new \Exception('Status tidak valid');
            }

            // Debug log
            \Log::info('Current discussion data:', [
                'id' => $discussion->id,
                'deadline' => $discussion->deadline,
                'status' => $status
            ]);

            // Update status diskusi
            $discussion->status = $status;
            $discussion->save();

            // Persiapkan data dasar
            $baseData = [
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
                'deadline' => $discussion->deadline instanceof Carbon 
                    ? $discussion->deadline->format('Y-m-d')
                    : Carbon::parse($discussion->deadline)->format('Y-m-d'),
                'original_id' => $discussion->id
            ];

            // Jika status Closed
            if ($status === 'Closed') {
                $closedData = array_merge($baseData, [
                    'status' => 'Closed',
                    'closed_at' => now()->format('Y-m-d H:i:s')
                ]);

                \Log::info('Creating closed discussion with data:', $closedData);
                
                $closedDiscussion = ClosedDiscussion::create($closedData);
                
                if (!$closedDiscussion) {
                    throw new \Exception('Gagal membuat record closed discussion');
                }
            }
            // Jika status Overdue
            else if ($status === 'Overdue') {
                $overdueData = array_merge($baseData, [
                    'status' => 'Overdue',
                    'overdue_at' => now()->format('Y-m-d H:i:s')
                ]);

                \Log::info('Creating overdue discussion with data:', $overdueData);
                
                $overdueDiscussion = OverdueDiscussion::create($overdueData);
                
                if (!$overdueDiscussion) {
                    throw new \Exception('Gagal membuat record overdue discussion');
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in updateStatus: ' . $e->getMessage());
            \Log::error('Discussion data: ', [
                'id' => $discussion->id,
                'deadline' => $discussion->deadline,
                'raw_data' => $discussion->toArray()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
} 