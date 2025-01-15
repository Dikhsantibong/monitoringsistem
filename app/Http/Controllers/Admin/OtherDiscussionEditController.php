<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OtherDiscussion;
use Illuminate\Http\Request;

class OtherDiscussionEditController extends Controller
{
    public function edit($id)
    {
        try {
            $discussion = OtherDiscussion::findOrFail($id);
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
                'risk_level' => 'required|string',
                'priority_level' => 'required|string',
                'pic' => 'required|string',
                'status' => 'required|in:Open,Closed',
                'commitments' => 'required|array',
                'commitment_deadlines' => 'required|array',
                'target_deadline' => 'required|date'
            ]);

            if ($validated['status'] === 'Closed' && $discussion->status !== 'Closed') {
                $validated['closed_at'] = now();
            }

            $discussion->update($validated);

            // Update komitmen
            $discussion->commitments()->delete(); // Hapus komitmen lama
            foreach ($request->commitments as $index => $commitment) {
                $discussion->commitments()->create([
                    'description' => $commitment,
                    'deadline' => $request->commitment_deadlines[$index],
                    'pic' => $request->commitment_pics[$index] ?? null
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
} 