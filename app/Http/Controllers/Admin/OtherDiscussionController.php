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
use App\Events\OtherDiscussionUpdated;
use Illuminate\Support\Facades\Log;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OtherDiscussionsExport;
use App\Exports\SingleOtherDiscussionExport;
use Illuminate\Support\Facades\Storage;
use App\Models\Commitment;

class OtherDiscussionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $status = $request->status;
        $unit = $request->unit;

        // Base query untuk semua tab
        $query = OtherDiscussion::query()
            ->with(['commitments' => function($q) {
                $q->with(['department', 'section']);
            }]);

        // Filter berdasarkan pencarian
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('topic', 'like', "%{$search}%")
                  ->orWhere('unit', 'like', "%{$search}%")
                  ->orWhere('pic', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan unit (nama unit)
        if ($unit) {
            // Debug log untuk melihat nilai unit yang dipilih
            \Log::info('Selected unit:', ['unit' => $unit]);
            
            $query->where('unit', 'like', "%{$unit}%");
            
            // Debug log untuk melihat SQL query yang dijalankan
            \Log::info('SQL Query:', ['sql' => $query->toSql()]);
        }

        // Filter berdasarkan status
        if ($status) {
            $query->where('status', $status);
        }

        // Active Discussions
        $activeDiscussions = (clone $query)
            ->where('status', 'Open')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'active_page');

        // Target Overdue
        $targetOverdueDiscussions = (clone $query)
            ->where('status', 'Open')
            ->whereDate('target_deadline', '<', now())
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'target_page');

        // Commitment Overdue
        $commitmentOverdueDiscussions = (clone $query)
            ->whereHas('commitments', function ($query) {
                $query->where('status', 'Open')
                    ->whereDate('deadline', '<', now());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'commitment_page');

        // Closed Discussions
        $closedDiscussions = (clone $query)
            ->where('status', 'Closed')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'closed_page');

        // Ambil data PowerPlant untuk dropdown filter
        $powerPlants = PowerPlant::select('name', 'unit_source')
            ->orderBy('name')
            ->get();

        // Debug log untuk melihat hasil query
        \Log::info('Active Discussions Count:', ['count' => $activeDiscussions->count()]);
        \Log::info('Target Overdue Count:', ['count' => $targetOverdueDiscussions->count()]);
        \Log::info('Closed Discussions Count:', ['count' => $closedDiscussions->count()]);

        return view('admin.other-discussions.index', [
            'activeDiscussions' => $activeDiscussions,
            'targetOverdueDiscussions' => $targetOverdueDiscussions,
            'commitmentOverdueDiscussions' => $commitmentOverdueDiscussions,
            'closedDiscussions' => $closedDiscussions,
            'powerPlants' => $powerPlants,
            'counts' => [
                'active' => $activeDiscussions->total(),
                'target_overdue' => $targetOverdueDiscussions->total(),
                'commitment_overdue' => (clone $query)->whereHas('commitments', function ($query) {
                    $query->where('status', 'Open')->whereDate('deadline', '<', now());
                })->count(),
                'closed' => $closedDiscussions->total()
            ]
        ]);
    }

    public function destroy($id)
    {
        try {
            $otherDiscussion = OtherDiscussion::findOrFail($id);
            
            DB::beginTransaction();

            try {
                // Hapus files
                if ($otherDiscussion->document_path) {
                    $paths = json_decode($otherDiscussion->document_path, true) ?? [];
                    foreach ($paths as $path) {
                        if (Storage::exists('public/' . $path)) {
                            Storage::delete('public/' . $path);
                            \Log::info('File deleted', ['path' => $path]);
                        }
                    }
                }

                // Hapus commitments
                $otherDiscussion->commitments()->delete();
                
                // Hapus discussion (akan trigger event delete otomatis)
                $otherDiscussion->delete();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Pembahasan berhasil dihapus'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            \Log::error('Delete failed', [
                'error' => $e->getMessage(),
                'discussion_id' => $id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pembahasan: ' . $e->getMessage(),
                'debug_info' => [
                    'error_message' => $e->getMessage(),
                    'discussion_id' => $id
                ]
            ], 500);
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
            DB::beginTransaction();

            $discussion = OtherDiscussion::findOrFail($request->discussion_id);
            $discussion->status = ucfirst(strtolower($request->status));
            
            if ($request->status === 'Closed') {
                $discussion->closed_at = now();
            }
            
            // Gunakan save() biasa untuk trigger event
            $discussion->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status'
            ], 500);
        }
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
            DB::beginTransaction();
            
            $validated = $request->validate([
                'sr_number' => 'required',
                'no_pembahasan' => 'required',
                'unit' => 'required',
                'topic' => 'required',
                'target' => 'required',
                'target_deadline' => 'required|date',
                'department_id' => 'required',
                'section_id' => 'required',
                'risk_level' => 'required',
                'priority_level' => 'required',
                'status' => 'required|in:Open,Closed',
                'commitments' => 'required|array',
                'commitment_deadlines' => 'required|array',
                'commitment_department_ids' => 'required|array',
                'commitment_section_ids' => 'required|array',
                'commitment_status' => 'required|array',
                'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // max 10MB
            ]);

            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                
                // Generate nama file yang unik dengan ekstensi asli
                $fileName = uniqid() . '_' . time() . '.' . $extension;
                
                // Simpan file dengan ekstensi asli
                $path = $file->storeAs('discussion-documents', $fileName, 'public');
                
                // Log untuk debugging
                \Log::info('File upload details:', [
                    'original_name' => $originalName,
                    'stored_name' => $fileName,
                    'extension' => $extension,
                    'mime_type' => $file->getMimeType(),
                    'path' => $path
                ]);

                $validated['document_path'] = $path;
                $validated['document_description'] = $originalName; // Simpan nama asli file
            }

            // Ambil semua department_id yang valid dari database
            $validDepartmentIds = \App\Models\Department::pluck('id')->toArray();
            \Log::info('Department ID yang valid:', $validDepartmentIds);

            // Generate PIC dari department dan section
            $pic = $this->generatePicString($validated['department_id'], $validated['section_id']);

            // Buat diskusi baru
            $discussion = OtherDiscussion::create([
                'sr_number' => $validated['sr_number'],
                'no_pembahasan' => $validated['no_pembahasan'],
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
                'status' => $validated['status'],
                'unit_source' => session('unit', 'mysql'), // Tambahkan unit_source
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

            return redirect()
                ->route('admin.other-discussions.index')
                ->with('success', 'Pembahasan berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error storing discussion:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Gagal menambahkan pembahasan: ' . $e->getMessage());
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

            // Validasi request
            $request->validate([
                'sr_number' => 'required',
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
                'commitment_status.*' => 'required|in:Open,Closed',
                'status' => 'required|in:Open,Closed',
                // Tambahkan validasi untuk dokumen
                'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // max 10MB
                'document_description' => 'required|string|max:255',
            ]);

            // Handle file upload
            if ($request->hasFile('document')) {
                // Hapus dokumen lama jika ada
                if ($discussion->document_path) {
                    Storage::disk('public')->delete($discussion->document_path);
                }

                // Upload dokumen baru
                $file = $request->file('document');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                
                // Generate nama file yang unik dengan ekstensi asli
                $fileName = uniqid() . '_' . time() . '.' . $extension;
                
                // Simpan file dengan ekstensi asli
                $path = $file->storeAs('discussion-documents', $fileName, 'public');
                
                // Log untuk debugging
                \Log::info('File upload details:', [
                    'original_name' => $originalName,
                    'stored_name' => $fileName,
                    'extension' => $extension,
                    'mime_type' => $file->getMimeType(),
                    'path' => $path
                ]);

                // Update path dokumen dan deskripsi
                $discussion->document_path = $path;
                $discussion->document_description = $originalName;
            }

            // Generate PIC
            $pic = $this->getPICName($request->department_id, $request->section_id);

            // Update diskusi
            $discussion->fill([
                'sr_number' => $request->sr_number,
                'unit' => $request->unit,
                'topic' => $request->topic,
                'target' => $request->target,
                'target_deadline' => $request->target_deadline,
                'department_id' => $request->department_id,
                'section_id' => $request->section_id,
                'pic' => $pic,
                'risk_level' => $request->risk_level,
                'priority_level' => $request->priority_level,
                'status' => $request->status,
                'unit_source' => session('unit', 'mysql'), // Tambahkan unit_source
            ]);

            if ($request->status === 'Closed' && !$discussion->closed_at) {
                $discussion->closed_at = now();
            }

            $discussion->save();

            // Update komitmen
            $discussion->commitments()->delete();
            
            foreach ($request->commitments as $index => $commitment) {
                $commitmentPic = $this->getPICName(
                    $request->commitment_department_ids[$index],
                    $request->commitment_section_ids[$index]
                );

                $discussion->commitments()->create([
                    'description' => $commitment,
                    'deadline' => $request->commitment_deadlines[$index],
                    'department_id' => $request->commitment_department_ids[$index],
                    'section_id' => $request->commitment_section_ids[$index],
                    'pic' => $commitmentPic,
                    'status' => $request->commitment_status[$index]
                ]);
            }

            DB::commit();

            return redirect()->route('admin.other-discussions.index')
                ->with('success', 'Data berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error updating discussion:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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

    public function generateNoPembahasan(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'unit' => 'required|string'
            ]);

            // Log untuk tracking
            Log::info('Generating no pembahasan', [
                'unit' => $request->unit,
                'user' => auth()->user()->name
            ]);

            // Generate nomor pembahasan menggunakan model
            $noPembahasan = OtherDiscussion::generateNoPembahasan($request->unit);

            // Generate SR number
            $year = date('Y');
            $month = date('m');
            
            // Cari SR number terakhir untuk bulan ini
            $lastSR = OtherDiscussion::where('sr_number', 'like', "SR/{$year}/{$month}/%")
                ->orderBy('sr_number', 'desc')
                ->first();

            if ($lastSR) {
                $lastNumber = (int) substr($lastSR->sr_number, -4);
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }
            
            $srNumber = sprintf("SR/%s/%s/%04d", $year, $month, $nextNumber);

            // Log hasil generate
            Log::info('Generated numbers', [
                'no_pembahasan' => $noPembahasan,
                'sr_number' => $srNumber
            ]);

            return response()->json([
                'success' => true,
                'number' => $noPembahasan,
                'sr_number' => $srNumber
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating numbers', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal generate nomor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateNumbers(Request $request)
    {
        try {
            $unit = $request->query('unit');
            
            // Generate no pembahasan
            $noPembahasan = OtherDiscussion::generateNoPembahasan($unit);
            
            // Generate SR number (format: SR/TAHUN/BULAN/NOMOR URUT)
            $year = date('Y');
            $month = date('m');
            
            // Ambil nomor urut SR terakhir untuk bulan ini
            $lastSR = OtherDiscussion::where('sr_number', 'like', "SR/$year/$month/%")
                ->orderBy('sr_number', 'desc')
                ->first();
                
            if ($lastSR) {
                $lastNumber = (int) substr($lastSR->sr_number, -4);
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }
            
            $srNumber = sprintf("SR/%s/%s/%04d", $year, $month, $nextNumber);
            
            return response()->json([
                'success' => true,
                'no_pembahasan' => $noPembahasan,
                'sr_number' => $srNumber
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function print(Request $request)
    {
        try {
            $query = OtherDiscussion::query()
                ->with(['commitments' => function($q) {
                    $q->with(['department', 'section']);
                }]);

            // Filter berdasarkan tanggal
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            // Filter berdasarkan pencarian
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $search = $request->search;
                    $q->where('topic', 'like', "%{$search}%")
                      ->orWhere('unit', 'like', "%{$search}%")
                      ->orWhere('pic', 'like', "%{$search}%");
                });
            }

            // Filter berdasarkan unit
            if ($request->filled('unit')) {
                $query->where('unit', $request->unit);
            }

            // Filter berdasarkan status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $discussions = $query->orderBy('created_at', 'desc')->get();

            return view('admin.other-discussions.print', [
                'discussions' => $discussions,
                'filters' => [
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'search' => $request->search,
                    'unit' => $request->unit,
                    'status' => $request->status
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in print method:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Terjadi kesalahan saat mencetak data');
        }
    }

    public function export(Request $request)
    {
        try {
            $query = OtherDiscussion::query()
                ->with(['commitments' => function($q) {
                    $q->with(['department', 'section']);
                }]);

            // Filter berdasarkan tanggal
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            // Filter berdasarkan pencarian
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $search = $request->search;
                    $q->where('topic', 'like', "%{$search}%")
                      ->orWhere('unit', 'like', "%{$search}%")
                      ->orWhere('pic', 'like', "%{$search}%");
                });
            }

            // Filter berdasarkan unit
            if ($request->filled('unit')) {
                $query->where('unit', $request->unit);
            }

            // Filter berdasarkan status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $format = $request->format ?? 'xlsx';
            $fileName = 'pembahasan_lain_' . date('Y-m-d_His');

            if ($format === 'xlsx') {
                return Excel::download(
                    new OtherDiscussionsExport($query), 
                    $fileName . '.xlsx'
                );
            } else {
                $discussions = $query->orderBy('created_at', 'desc')->get();
                $pdf = PDF::loadView('admin.other-discussions.pdf', [
                    'discussions' => $discussions,
                    'filters' => [
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date,
                        'search' => $request->search,
                        'unit' => $request->unit,
                        'status' => $request->status
                    ]
                ]);

                return $pdf->download($fileName . '.pdf');
            }

        } catch (\Exception $e) {
            \Log::error('Error in export method:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Terjadi kesalahan saat mengexport data');
        }
    }

    public function show(Request $request)
    {
        try {
            $query = OtherDiscussion::query()
                ->with(['commitments' => function($q) {
                    $q->with(['department', 'section']);
                }]);

            // Filter berdasarkan tanggal
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            // Filter berdasarkan pencarian
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $search = $request->search;
                    $q->where('topic', 'like', "%{$search}%")
                      ->orWhere('unit', 'like', "%{$search}%")
                      ->orWhere('pic', 'like', "%{$search}%");
                });
            }

            // Filter berdasarkan unit
            if ($request->filled('unit')) {
                $query->where('unit', $request->unit);
            }

            // Filter berdasarkan status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $discussions = $query->orderBy('created_at', 'desc')->get();

            if ($discussions->isEmpty()) {
                return back()->with('warning', 'Tidak ada data yang ditemukan untuk filter yang dipilih');
            }

            return view('admin.other-discussions.show', [
                'discussions' => $discussions,
                'filters' => [
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'search' => $request->search,
                    'unit' => $request->unit,
                    'status' => $request->status
                ]
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Terjadi kesalahan pada database: ' . $e->getMessage());
        
        } catch (\InvalidArgumentException $e) {
            \Log::error('Invalid argument error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Format data tidak valid: ' . $e->getMessage());
        
        } catch (\Exception $e) {
            \Log::error('Unexpected error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Terjadi kesalahan yang tidak terduga: ' . $e->getMessage());
        }
    }

    public function show_single($id)
    {
        try {
            $discussion = OtherDiscussion::with(['commitments' => function($q) {
                $q->with(['department', 'section']);
            }])->findOrFail($id);

            return view('admin.other-discussions.show_single', [
                'discussion' => $discussion
            ]);

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Data tidak ditemukan');
        } catch (\Exception $e) {
            \Log::error('Error in show_single method:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Terjadi kesalahan saat menampilkan data');
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $query = OtherDiscussion::query()
                ->with(['commitments' => function($q) {
                    $q->with(['department', 'section']);
                }]);

            // Terapkan filter yang sama seperti di index
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('topic', 'like', "%{$request->search}%")
                      ->orWhere('unit', 'like', "%{$request->search}%")
                      ->orWhere('pic', 'like', "%{$request->search}%");
                });
            }
            if ($request->filled('unit')) {
                $query->where('unit', $request->unit);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $discussions = $query->orderBy('created_at', 'desc')->get();
            $fileName = 'pembahasan_lain_' . now()->format('Y-m-d_His') . '.xlsx';

            return Excel::download(new OtherDiscussionsExport($discussions), $fileName);

        } catch (\Exception $e) {
            \Log::error('Error exporting excel:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Terjadi kesalahan saat mengexport data: ' . $e->getMessage());
        }
    }

    public function printSingle($id)
    {
        $discussion = OtherDiscussion::with('commitments')->findOrFail($id);
        return view('admin.other-discussions.print-single', compact('discussion'));
    }

    public function exportSingle($id, $format)
    {
        $discussion = OtherDiscussion::with('commitments')->findOrFail($id);
        
        if ($format === 'pdf') {
            $pdf = PDF::loadView('admin.other-discussions.export-single-pdf', compact('discussion'));
            return $pdf->download('pembahasan-' . $discussion->no_pembahasan . '.pdf');
        }
        
        if ($format === 'xlsx') {
            return Excel::download(new SingleOtherDiscussionExport($discussion), 'pembahasan-' . $discussion->no_pembahasan . '.xlsx');
        }
        
        abort(404);
    }

    public function downloadDocument($id)
    {
        try {
            $discussion = OtherDiscussion::findOrFail($id);
            
            if (!$discussion->document_path) {
                return back()->with('error', 'Dokumen tidak ditemukan');
            }

            $path = storage_path('app/public/' . $discussion->document_path);
            
            if (!file_exists($path)) {
                \Log::error('Document file not found:', ['path' => $path]);
                return back()->with('error', 'File tidak ditemukan di server');
            }

            // Dapatkan ekstensi file dari path
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            
            // Tentukan mime type berdasarkan ekstensi
            $mime = $this->getMimeType($extension);

            // Gunakan nama file asli dari document_description atau nama file di path
            $fileName = $discussion->document_description ?? basename($path);

            \Log::info('Downloading document:', [
                'path' => $path,
                'mime' => $mime,
                'extension' => $extension,
                'filename' => $fileName
            ]);

            return response()->file($path, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="' . $fileName . '"'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error downloading document:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal mengunduh dokumen');
        }
    }

    private function getMimeType($extension)
    {
        $mimes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif'
        ];

        return $mimes[strtolower($extension)] ?? 'application/octet-stream';
    }

    public function removeFile(OtherDiscussion $discussion, $index)
    {
        try {
            $paths = json_decode($discussion->document_path) ?? [];
            $descriptions = json_decode($discussion->document_description) ?? [];

            if (!isset($paths[$index])) {
                session()->flash('error', 'File tidak ditemukan');
                return response()->json(['success' => false]);
            }

            Storage::delete('public/' . $paths[$index]);
            
            unset($paths[$index]);
            unset($descriptions[$index]);

            $discussion->update([
                'document_path' => json_encode(array_values($paths)),
                'document_description' => json_encode(array_values($descriptions))
            ]);

            session()->flash('success', 'File berhasil dihapus');
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            \Log::error('Error deleting file: ' . $e->getMessage());
            session()->flash('error', 'Gagal menghapus file');
            return response()->json(['success' => false]);
        }
    }

    public function removeCommitment(OtherDiscussion $discussion, $commitmentId)
    {
        try {
            \Log::info('Attempting to remove commitment', [
                'discussion_id' => $discussion->id,
                'commitment_id' => $commitmentId,
                'user' => auth()->user()->email
            ]);

            $commitment = Commitment::where('id', $commitmentId)
                ->where('other_discussion_id', $discussion->id)
                ->firstOrFail();

            \Log::info('Commitment found', [
                'commitment' => $commitment->toArray()
            ]);

            $commitment->delete();

            \Log::info('Commitment deleted successfully');
            
            session()->flash('success', 'Komitmen berhasil dihapus');
            return response()->json([
                'success' => true,
                'message' => 'Komitmen berhasil dihapus',
                'debug_info' => [
                    'discussion_id' => $discussion->id,
                    'commitment_id' => $commitmentId,
                    'timestamp' => now()->toIso8601String()
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error deleting commitment', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'discussion_id' => $discussion->id,
                'commitment_id' => $commitmentId
            ]);

            session()->flash('error', 'Gagal menghapus komitmen');
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus komitmen',
                'debug_info' => [
                    'error' => $e->getMessage(),
                    'discussion_id' => $discussion->id,
                    'commitment_id' => $commitmentId
                ]
            ], 500);
        }
    }
}   