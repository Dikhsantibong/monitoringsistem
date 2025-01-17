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
use Illuminate\Validation\ValidationException;

class OtherDiscussionController extends Controller
{
    public function index()
    {
        $search = request('search');
        $status = request('status');
        $unitSource = request('unit_source');

        // Ambil daftar nama unit dari power_plants berdasarkan unit_source
        $powerPlantNames = [];
        if ($unitSource) {
            $powerPlantNames = DB::table('power_plants')
                ->where('unit_source', $unitSource)
                ->pluck('name')
                ->toArray();
        }

        // Base query untuk semua tab
        $baseQuery = OtherDiscussion::with(['commitments' => function($q) {
            $q->with(['department', 'section']);
        }])
        ->when($search, function($q) use ($search) {
            $q->where(function($q) use ($search) {
                $q->where('topic', 'like', "%{$search}%")
                  ->orWhere('unit', 'like', "%{$search}%")
                  ->orWhere('pic', 'like', "%{$search}%");
            });
        })
        ->when($unitSource, function($q) use ($powerPlantNames) {
            $q->whereIn('unit', $powerPlantNames);
        });

        // Query untuk setiap tab dengan filter yang sama
        $activeDiscussions = (clone $baseQuery)
            ->active()
            ->paginate(10, ['*'], 'active_page');

        $targetOverdueDiscussions = (clone $baseQuery)
            ->targetOverdue()
            ->paginate(10, ['*'], 'target_page');

        $commitmentOverdueDiscussions = (clone $baseQuery)
            ->commitmentOverdue()
            ->paginate(10, ['*'], 'commitment_page');

        $closedDiscussions = (clone $baseQuery)
            ->closed()
            ->paginate(10, ['*'], 'closed_page');

        // Hitung total untuk badge
        $baseCountQuery = (clone $baseQuery);
        $counts = [
            'active' => (clone $baseCountQuery)->active()->count(),
            'target_overdue' => (clone $baseCountQuery)->targetOverdue()->count(),
            'commitment_overdue' => (clone $baseCountQuery)->commitmentOverdue()->count(),
            'closed' => (clone $baseCountQuery)->closed()->count()
        ];

        return view('admin.other-discussions.index', compact(
            'activeDiscussions',
            'commitmentOverdueDiscussions',
            'targetOverdueDiscussions',
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
        $discussion->status = ucfirst(strtolower($request->status));
        
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
            // Data untuk dropdown sections
            $sections = [
                '1' => [ // BAGIAN OPERASI
                    ['id' => 1, 'name' => 'SEKSI RENDAL OP & NIAGA'],
                    ['id' => 2, 'name' => 'SEKSI BAHAN BAKAR'],
                    ['id' => 3, 'name' => 'SEKSI OUTAGE MGT']
                ],
                '2' => [ // BAGIAN PEMELIHARAAN
                    ['id' => 4, 'name' => 'SEKSI PERENCANAAN PENGENDALIAN PEMELIHARAAN'],
                    ['id' => 5, 'name' => 'SEKSI INVENTORI KONTROL & GUDANG']
                ],
                '3' => [ // BAGIAN ENJINIRING & QUALITY ASSURANCE
                    ['id' => 6, 'name' => 'SEKSI SYSTEM OWNER'],
                    ['id' => 7, 'name' => 'SEKSI CONDITION BASED MAINTENANCE'],
                    ['id' => 8, 'name' => 'SEKSI MMRK']
                ],
                '4' => [ // BAGIAN BUSINESS SUPPORT
                    ['id' => 9, 'name' => 'SEKSI SDM, UMUM & CSR'],
                    ['id' => 10, 'name' => 'SEKSI KEUANGAN'],
                    ['id' => 11, 'name' => 'SEKSI PENGADAAN']
                ],
                '5' => [ // HSE
                    ['id' => 12, 'name' => 'SEKSI LINGKUNGAN'],
                    ['id' => 13, 'name' => 'SEKSI K3 & KEAMANAN']
                ],
                '6' => [ // UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL
                    ['id' => 14, 'name' => 'UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL BAU-BAU'],
                    ['id' => 15, 'name' => 'UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL KOLAKA'],
                    ['id' => 16, 'name' => 'UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL POASIA'],
                    ['id' => 17, 'name' => 'UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL WUA-WUA']
                ]
            ];
            
            return view('admin.other-discussions.create', compact('units', 'sections'));
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memuat halaman');
        }
    }

    public function store(Request $request)
    {
        try {
            // Debug log untuk melihat data yang dikirim
            \Log::info('Data yang dikirim:', [
                'department_ids' => $request->commitment_department_ids,
                'all_data' => $request->all()
            ]);

            // Ambil semua department_id yang valid dari database
            $validDepartmentIds = \App\Models\Department::pluck('id')->toArray();
            \Log::info('Department ID yang valid:', $validDepartmentIds);

            $validated = $request->validate([
                'sr_number' => 'nullable|string|max:20',
                'wo_number' => 'nullable|string|max:20',
                'unit' => 'required|string|max:255',
                'topic' => 'required|string|max:255',
                'target' => 'required|string',
                'target_deadline' => 'required|date',
                'department_id' => 'nullable|integer|exists:departments,id',
                'section_id' => 'nullable|integer|exists:sections,id',
                'risk_level' => 'required|in:R,MR,MT,T',
                'priority_level' => 'required|in:Low,Medium,High',
                'commitments' => 'required|array|min:1',
                'commitment_deadlines' => 'required|array|min:1',
                'commitment_deadlines.*' => 'required|date',
                'commitment_department_ids' => 'required|array|min:1',
                'commitment_department_ids.*' => 'required|integer|exists:departments,id',
                'commitment_section_ids' => 'required|array|min:1',
                'commitment_section_ids.*' => 'required|integer|exists:sections,id',
                'commitment_status' => 'required|array|min:1',
                'commitment_status.*' => 'required|in:Open,Closed'
            ]);

            // Generate PIC dari department dan section
            $pic = $this->generatePicString($validated['department_id'], $validated['section_id']);

            // Buat diskusi baru
            $discussion = OtherDiscussion::create([
                'sr_number' => $validated['sr_number'],
                'wo_number' => $validated['wo_number'],
                'unit' => $validated['unit'],
                'topic' => $validated['topic'],
                'target' => $validated['target'],
                'target_deadline' => $validated['target_deadline'],
                'deadline' => $validated['target_deadline'],
                'department_id' => $validated['department_id'],
                'section_id' => $validated['section_id'],
                'pic' => $pic,
                'risk_level' => $validated['risk_level'],
                'priority_level' => $validated['priority_level'],
                'status' => 'Open'
            ]);

            // Log sebelum menyimpan commitments
            \Log::info('Preparing to save commitments:', [
                'commitments' => $request->commitments,
                'deadlines' => $request->commitment_deadlines,
                'departments' => $request->commitment_department_ids,
                'sections' => $request->commitment_section_ids,
                'status' => $request->commitment_status
            ]);

            // Simpan komitmen
            if ($request->has('commitments')) {
                foreach ($request->commitments as $key => $commitment) {
                    // Generate PIC name untuk komitmen
                    $picName = $this->generatePicString(
                        $request->commitment_department_ids[$key], 
                        $request->commitment_section_ids[$key]
                    );

                    $discussion->commitments()->create([
                        'description' => $commitment,
                        'deadline' => $request->commitment_deadlines[$key],
                        'department_id' => $request->commitment_department_ids[$key],
                        'section_id' => $request->commitment_section_ids[$key],
                        'status' => $request->commitment_status[$key] ?? 'Open',
                        'pic' => $picName
                    ]);
                }
            }

            DB::commit();
            \Log::info('Transaction committed successfully');

            session()->flash('success', 'Data berhasil ditambahkan');
            return redirect()->route('admin.other-discussions.index');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error creating discussion:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Generate PIC string dari department dan section ID
     */
    private function generatePicString($departmentId, $sectionId)
    {
        // Array untuk mapping department
        $departments = [
            '1' => 'BAGIAN OPERASI',
            '2' => 'BAGIAN PEMELIHARAAN',
            '3' => 'BAGIAN ENJINIRING & QUALITY ASSURANCE',
            '4' => 'BAGIAN BUSINESS SUPPORT',
            '5' => 'HSE',
            '6' => 'UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL'
        ];

        // Array untuk mapping section
        $sections = [
            '1' => 'SEKSI RENDAL OP & NIAGA',
            '2' => 'SEKSI BAHAN BAKAR',
            '3' => 'SEKSI OUTAGE MGT',
            '4' => 'SEKSI PERENCANAAN PENGENDALIAN PEMELIHARAAN',
            '5' => 'SEKSI INVENTORI KONTROL & GUDANG',
            '6' => 'SEKSI SYSTEM OWNER',
            '7' => 'SEKSI CONDITION BASED MAINTENANCE',
            '8' => 'SEKSI MMRK',
            '9' => 'SEKSI SDM, UMUM & CSR',
            '10' => 'SEKSI KEUANGAN',
            '11' => 'SEKSI PENGADAAN',
            '12' => 'SEKSI LINGKUNGAN',
            '13' => 'SEKSI K3 & KEAMANAN',
            '14' => 'UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL BAU-BAU',
            '15' => 'UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL KOLAKA',
            '16' => 'UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL POASIA',
            '17' => 'UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL WUA-WUA'
        ];

        // Ambil nama department dan section
        $departmentName = $departments[$departmentId] ?? '';
        $sectionName = $sections[$sectionId] ?? '';

        // Gabungkan department dan section
        return $departmentName . ' - ' . $sectionName;
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
                'department_id' => 'required',
                'section_id' => 'required',
                'risk_level' => 'required',
                'priority_level' => 'required',
                'commitments' => 'required|array|min:1',
                'commitment_deadlines' => 'required|array|min:1',
                'commitment_deadlines.*' => 'required|date',
                'commitment_department_ids' => 'required|array|min:1',
                'commitment_department_ids.*' => 'required',
                'commitment_section_ids' => 'required|array|min:1',
                'commitment_section_ids.*' => 'required',
                'commitment_status' => 'required|array|min:1',
                'commitment_status.*' => 'required|in:open,closed',
                'status' => 'required|in:Open,Closed'
            ]);

            // Generate PIC dari department dan section
            $pic = $this->generatePicString($validated['department_id'], $validated['section_id']);

            // Update diskusi
            $discussion->update([
                'sr_number' => $validated['sr_number'],
                'wo_number' => $validated['wo_number'],
                'unit' => $validated['unit'],
                'topic' => $validated['topic'],
                'target' => $validated['target'],
                'target_deadline' => $validated['target_deadline'],
                'department_id' => $validated['department_id'],
                'section_id' => $validated['section_id'],
                'pic' => $pic,
                'risk_level' => $validated['risk_level'],
                'priority_level' => $validated['priority_level'],
                'status' => $validated['status']
            ]);

            // Update komitmen
            $discussion->commitments()->delete();
            
            // Tambah komitmen baru
            foreach ($request->commitments as $index => $commitment) {
                // Generate PIC untuk setiap komitmen
                $commitmentPic = $this->generatePicString(
                    $request->commitment_department_ids[$index],
                    $request->commitment_section_ids[$index]
                );

                $commitment->status = ucfirst(strtolower($request->commitment_status[$index] ?? 'Open'));

                $discussion->commitments()->create([
                    'description' => $commitment,
                    'deadline' => $request->commitment_deadlines[$index],
                    'department_id' => $request->commitment_department_ids[$index],
                    'section_id' => $request->commitment_section_ids[$index],
                    'pic' => $commitmentPic,
                    'status' => $request->commitment_status[$index] ?? 'open'
                ]);
            }

            DB::commit();
            return redirect()->route('admin.other-discussions.index')
                ->with('success', 'Data berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    private function getPICName($departmentId, $sectionId)
    {
        // Array untuk mapping department
        $departments = [
            '1' => 'BAGIAN OPERASI',
            '2' => 'BAGIAN PEMELIHARAAN',
            '3' => 'BAGIAN ENJINIRING & QUALITY ASSURANCE',
            '4' => 'BAGIAN BUSINESS SUPPORT',
            '5' => 'HSE',
            '6' => 'UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL BAU BAU',
            '7' => 'UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL KOLAKA',
            '8' => 'UNIT LAYANAN PUSAT TENAGA LISTRIK DIESEL WUA WUA'
        ];

        // Array untuk mapping section
        $sections = [
            '1' => 'SEKSI RENDAL OP & NIAGA',
            '2' => 'SEKSI BAHAN BAKAR',
            '3' => 'SEKSI OUTAGE MGT',
            '4' => 'SEKSI PERENCANAAN PENGENDALIAN PEMELIHARAAN',
            '5' => 'SEKSI INVENTORI KONTROL & GUDANG',
            '6' => 'SEKSI SYSTEM OWNER',
            '7' => 'SEKSI CONDITION BASED MAINTENANCE',
            '8' => 'SEKSI MMRK',
            '9' => 'SEKSI SDM, UMUM & CSR',
            '10' => 'SEKSI KEUANGAN',
            '11' => 'SEKSI PENGADAAN',
            '12' => 'SEKSI LINGKUNGAN',
            '13' => 'SEKSI K3 & KEAMANAN',
            '14' => 'UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL BAU-BAU',
            '15' => 'UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL KOLAKA',
            '16' => 'UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL POASIA',
            '17' => 'UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL WUA-WUA'
        ];

        // Ambil nama department dan section
        $departmentName = $departments[$departmentId] ?? '';
        $sectionName = $sections[$sectionId] ?? '';

        // Gabungkan department dan section
        return $departmentName . ' - ' . $sectionName;
    }
}   