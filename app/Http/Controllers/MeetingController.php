<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function index()
    {
        return view('user.daily-meeting'); // Ganti dengan view yang sesuai
    }

    public function create()
    {
        return view('admin.meetings.create');
    }

    public function store(Request $request)
    {
        // Validasi dan simpan meeting baru
    }
}
