<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function index()
    {
        $meetings = Meeting::with('creator')->latest()->paginate(10);
        return view('admin.meetings.index', compact('meetings'));
    }

    // ... tambahkan method lainnya sesuai kebutuhan
} 