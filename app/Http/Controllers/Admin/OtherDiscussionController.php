<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OtherDiscussion;
use Illuminate\Http\Request;

class OtherDiscussionController extends Controller
{
    public function index(Request $request)
    {
        $query = OtherDiscussion::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('topic', 'like', "%{$search}%")
                  ->orWhere('pic', 'like', "%{$search}%")
                  ->orWhere('unit', 'like', "%{$search}%");
            });
        }

        // Filter unit
        if ($request->filled('unit')) {
            $query->where('unit', $request->unit);
        }

        // Filter date range
        if ($request->filled('start_date')) {
            $query->whereDate('deadline', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('deadline', '<=', $request->end_date);
        }

        $discussions = $query->latest()->paginate(10);

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