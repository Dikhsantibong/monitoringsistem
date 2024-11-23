<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Illuminate\Http\Request;

class AdminMeetingController extends Controller
{
    public function index()
    {
        $meetings = Meeting::with(['department', 'participants'])
            ->withCount('participants')
            ->latest()
            ->paginate(10);
            
        $departments = \App\Models\Department::all();
        
        return view('admin.meetings.index', compact('meetings', 'departments'));
    }

    public function show(Meeting $meeting)
    {
        $meeting->load(['department', 'participants']);
        return response()->json($meeting);
    }

    public function export()
    {
        // Implementasi export meetings
        return response()->download('path/to/exported/file.xlsx');
    }
} 