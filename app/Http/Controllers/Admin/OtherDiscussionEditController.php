<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OtherDiscussion;
use App\Models\Commitment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\PicGeneratorService;
use App\Events\OtherDiscussionUpdated;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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

            // Handle file uploads jika ada
            if ($request->hasFile('documents')) {
                $this->handleFileUploads($request, $discussion);
            }

            // Update komitmen
            $this->updateCommitments($discussion, $request);
            
            DB::commit();

            // Trigger event secara manual
            event(new OtherDiscussionUpdated($discussion, 'update'));
            
            return redirect()->route('admin.other-discussions.index')
                ->with('success', 'Data berhasil diperbarui');
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating discussion:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    protected function validateDiscussion(Request $request)
    {
        $rules = [
            'sr_number' => 'nullable|string|max:20',
            'unit' => 'required|string|max:255',
            'topic' => 'required|string|max:255',
            'target' => 'required|string',
            'target_deadline' => 'required|date',
            'department_id' => 'required|exists:departments,id',
            'section_id' => 'required|exists:sections,id',
            'risk_level' => 'required|in:R,MR,MT,T',
            'priority_level' => 'required|in:Low,Medium,High',
            'status' => 'required|in:Open,Closed',
        ];

        return $request->validate($rules);
    }

    protected function handleFileUploads($request, $discussion)
    {
        try {
            // Validasi file
            $request->validate([
                'documents.*' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
                'document_descriptions.*' => 'required|string|max:255',
            ]);

            // Ambil data dokumen yang sudah ada
            $existingPaths = json_decode($discussion->document_path) ?? [];
            $existingDescriptions = json_decode($discussion->document_description) ?? [];

            // Konversi ke array jika string tunggal
            if (!is_array($existingPaths)) {
                $existingPaths = $discussion->document_path ? [$discussion->document_path] : [];
                $existingDescriptions = $discussion->document_description ? [$discussion->document_description] : [];
            }

            // Buat direktori jika belum ada
            $uploadPath = public_path('storage/discussion-documents');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            foreach ($request->file('documents') as $index => $file) {
                $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                // Simpan langsung ke public/storage
                $file->move($uploadPath, $fileName);
                
                $path = 'discussion-documents/' . $fileName;
                $existingPaths[] = $path;
                $existingDescriptions[] = $request->input('document_descriptions.' . $index, $file->getClientOriginalName());
            }

            // Update discussion dengan data file baru
            $discussion->document_path = json_encode($existingPaths);
            $discussion->document_description = json_encode($existingDescriptions);
            $discussion->save();

            return true;
        } catch (\Exception $e) {
            Log::error('File upload error: ' . $e->getMessage());
            throw new \Exception('Gagal mengupload file: ' . $e->getMessage());
        }
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

    public function removeFile(Request $request, $id)
    {
        try {
            $discussion = OtherDiscussion::findOrFail($id);
            $fileIndex = $request->input('file_index');
            
            $paths = json_decode($discussion->document_path) ?? [];
            $descriptions = json_decode($discussion->document_description) ?? [];
            
            if (isset($paths[$fileIndex])) {
                // Hapus file dari storage
                Storage::disk('public')->delete($paths[$fileIndex]);
                
                // Hapus dari array
                array_splice($paths, $fileIndex, 1);
                array_splice($descriptions, $fileIndex, 1);
                
                // Update database
                $discussion->update([
                    'document_path' => json_encode($paths),
                    'document_description' => json_encode($descriptions)
                ]);
                
                return response()->json(['success' => true]);
            }
            
            return response()->json(['success' => false, 'message' => 'File tidak ditemukan']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
} 