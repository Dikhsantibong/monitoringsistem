<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OtherDiscussion;
use App\Models\Commitment;
use Illuminate\Http\Request;

class OtherDiscussionEditController extends Controller
{
    public function edit($id)
    {
        try {
            // Load discussion dengan relasi commitments
            $discussion = OtherDiscussion::with('commitments')->findOrFail($id);
            return view('admin.other-discussions.edit', compact('discussion'));
        } catch (\Exception $e) {
            return redirect()->route('admin.other-discussions.index')
                ->with('error', 'Pembahasan tidak ditemukan');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $discussion = OtherDiscussion::findOrFail($id);
            
            $validated = $request->validate([
                'sr_number' => 'nullable|numeric',
                'wo_number' => 'nullable|numeric',
                'unit' => 'required|string',
                'topic' => 'required|string',
                'target' => 'required|string',
                'target_deadline' => 'required|date',
                'department_id' => 'nullable|numeric',
                'section_id' => 'nullable|numeric',
                'risk_level' => 'required|string',
                'priority_level' => 'required|string',
                'status' => 'required|in:Open,Closed',
                'commitments' => 'required|array',
                'commitment_deadlines' => 'required|array',
                'commitment_department_ids' => 'required|array',
                'commitment_section_ids' => 'required|array',
                'commitment_status' => 'required|array'
            ]);

            // Update status closed_at jika status berubah menjadi Closed
            if ($validated['status'] === 'Closed' && $discussion->status !== 'Closed') {
                $validated['closed_at'] = now();
            }

            // Update discussion
            $discussion->update([
                'sr_number' => $validated['sr_number'],
                'wo_number' => $validated['wo_number'],
                'unit' => $validated['unit'],
                'topic' => $validated['topic'],
                'target' => $validated['target'],
                'target_deadline' => $validated['target_deadline'],
                'department_id' => $validated['department_id'],
                'section_id' => $validated['section_id'],
                'risk_level' => $validated['risk_level'],
                'priority_level' => $validated['priority_level'],
                'status' => $validated['status'],
                'closed_at' => $validated['closed_at'] ?? $discussion->closed_at
            ]);

            // Hapus komitmen lama
            $discussion->commitments()->delete();

            // Buat komitmen baru
            foreach ($request->commitments as $index => $commitment) {
                $discussion->commitments()->create([
                    'description' => $commitment,
                    'deadline' => $request->commitment_deadlines[$index],
                    'department_id' => $request->commitment_department_ids[$index],
                    'section_id' => $request->commitment_section_ids[$index],
                    'status' => $request->commitment_status[$index]
                ]);
            }

            return redirect()->route('admin.other-discussions.index')
                ->with('success', 'Data berhasil diperbarui');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
} 