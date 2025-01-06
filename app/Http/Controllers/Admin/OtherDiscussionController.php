<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OtherDiscussion;
use Illuminate\Http\Request;

class OtherDiscussionController extends Controller
{
    public function index()
    {
        $discussions = OtherDiscussion::latest()->paginate(10);
        return view('admin.other-discussions.index', compact('discussions'));
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
        $discussion = OtherDiscussion::findOrFail($id);
        return view('admin.other-discussions.edit', compact('discussion'));
    }

    public function update(Request $request, $id)
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

        $discussion = OtherDiscussion::findOrFail($id);
        $discussion->update($validated);

        return redirect()
            ->route('admin.other-discussions.index')
            ->with('success', 'Data pembahasan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $discussion = OtherDiscussion::findOrFail($id);
        $discussion->delete();

        return redirect()
            ->route('admin.other-discussions.index')
            ->with('success', 'Data pembahasan berhasil dihapus');
    }
} 