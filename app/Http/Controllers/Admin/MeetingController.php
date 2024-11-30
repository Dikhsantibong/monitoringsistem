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

    public function upload(Request $request)
    {
        // Logika untuk menangani upload rapat
        
        // Misalnya, validasi dan simpan data ke database

        // Contoh validasi
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:2048', // Sesuaikan dengan jenis file yang diizinkan
        ]);

        // Simpan file atau data ke database
        // ...

        return redirect()->route('admin.meetings.index')->with('success', 'Rapat berhasil di-upload.');
    }

    // ... tambahkan method lainnya sesuai kebutuhan
} 