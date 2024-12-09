<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Department; // Pastikan model Department di-import
use Illuminate\Http\Request;

class AdminMeetingController extends Controller
{
    public function index()
    {
        $meetings = Meeting::with(['department', 'participants'])
            ->withCount('participants')
            ->latest()
            ->paginate(10);
        
        $departments = Department::all(); // Ambil semua departemen
        
        return view('admin.meetings.index', compact('meetings', 'departments'));
    }

    public function create()
    {
        
        return view('admin.meetings.create');
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

    public function dailyMeeting()
    {
        // Ambil semua pertemuan yang dijadwalkan untuk hari ini
        $meetings = Meeting::whereDate('scheduled_at', today())
            ->with(['department', 'participants'])
            ->get();

        return view('user.daily-meeting', compact('meetings'));
    }
}