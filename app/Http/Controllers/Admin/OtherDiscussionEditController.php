<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OtherDiscussion;
use App\Models\Commitment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\PicGeneratorService;
use App\Events\OtherDiscussionUpdated;

class OtherDiscussionEditController extends Controller
{
    protected $picGenerator;

    public function __construct(PicGeneratorService $picGenerator)
    {
        $this->picGenerator = $picGenerator;
    }

    public function edit($id)
    {
        try {
            $discussion = OtherDiscussion::with(['commitments' => function($query) {
                $query->with(['department', 'section'])->orderBy('created_at');
            }])->findOrFail($id);
            
            return view('admin.other-discussions.edit', compact('discussion'));
        } catch (\Exception $e) {
            return redirect()->route('admin.other-discussions.index')
                ->with('error', 'Pembahasan tidak ditemukan');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $discussion = OtherDiscussion::findOrFail($id);
            
            // Validasi request menggunakan method validateDiscussion
            $validated = $this->validateDiscussion($request);
            
            // Generate PIC
            $pic = $this->picGenerator->generate(
                $validated['department_id'],
                $validated['section_id']
            );

            // Update discussion
            $discussion->fill([
                'sr_number' => $validated['sr_number'],
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

            // Set closed_at jika status Closed
            if ($validated['status'] === 'Closed' && !$discussion->closed_at) {
                $discussion->closed_at = now();
            }

            $discussion->saveQuietly();

            // Update komitmen
            $this->updateCommitments($discussion, $request);
            
            DB::commit();

            // Trigger event secara manual
            event(new OtherDiscussionUpdated($discussion, 'update'));
            
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

    protected function validateDiscussion(Request $request)
    {
        return $request->validate([
            'sr_number' => 'nullable|numeric',
            'unit' => 'required|string|max:255',
            'topic' => 'required|string|max:255',
            'target' => 'required|string',
            'target_deadline' => 'required|date',
            'department_id' => 'required|exists:departments,id',
            'section_id' => 'required|exists:sections,id',
            'risk_level' => 'required|in:R,MR,MT,T',
            'priority_level' => 'required|in:Low,Medium,High',
            'status' => 'required|in:Open,Closed',
            
            // Validasi untuk komitmen yang ada
            'commitments' => 'array',
            'commitment_deadlines' => 'array',
            'commitment_department_ids' => 'array',
            'commitment_section_ids' => 'array',
            'commitment_status' => 'array',
            
            // Validasi untuk komitmen baru
            'new_commitments' => 'array',
            'new_commitment_deadlines' => 'array',
            'new_commitment_department_ids' => 'array',
            'new_commitment_section_ids' => 'array',
            'new_commitment_status' => 'array'
        ]);
    }

    protected function updateDiscussion(OtherDiscussion $discussion, array $validated)
    {
        $pic = $this->picGenerator->generate(
            $validated['department_id'], 
            $validated['section_id']
        );

        $discussion->update([
            'sr_number' => $validated['sr_number'],
            'unit' => $validated['unit'],
            'topic' => $validated['topic'],
            'target' => $validated['target'],
            'target_deadline' => $validated['target_deadline'],
            'department_id' => $validated['department_id'],
            'section_id' => $validated['section_id'],
            'pic' => $pic,
            'risk_level' => $validated['risk_level'],
            'priority_level' => $validated['priority_level'],
            'status' => $validated['status'],
            'closed_at' => $validated['status'] === 'Closed' ? now() : null
        ]);
    }

    protected function updateCommitments(OtherDiscussion $discussion, Request $request)
    {
        // Hapus komitmen lama
        $discussion->commitments()->delete();
        
        // Update komitmen yang ada
        if ($request->has('commitments')) {
            foreach ($request->commitments as $index => $description) {
                $this->createCommitment($discussion, [
                    'description' => $description,
                    'deadline' => $request->commitment_deadlines[$index],
                    'department_id' => $request->commitment_department_ids[$index],
                    'section_id' => $request->commitment_section_ids[$index],
                    'status' => $request->commitment_status[$index]
                ]);
            }
        }
        
        // Tambah komitmen baru
        if ($request->has('new_commitments')) {
            foreach ($request->new_commitments as $index => $description) {
                $this->createCommitment($discussion, [
                    'description' => $description,
                    'deadline' => $request->new_commitment_deadlines[$index],
                    'department_id' => $request->new_commitment_department_ids[$index],
                    'section_id' => $request->new_commitment_section_ids[$index],
                    'status' => $request->new_commitment_status[$index] ?? 'Open'
                ]);
            }
        }
    }

    protected function createCommitment(OtherDiscussion $discussion, array $data)
    {
        $pic = $this->picGenerator->generate(
            $data['department_id'],
            $data['section_id']
        );

        $discussion->commitments()->create([
            'description' => $data['description'],
            'deadline' => $data['deadline'],
            'department_id' => $data['department_id'],
            'section_id' => $data['section_id'],
            'status' => $data['status'],
            'pic' => $pic
        ]);
    }
} 