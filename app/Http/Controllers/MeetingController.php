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

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048', // Validasi file untuk foto dan dokumen
        ]);

        if ($request->file('file')->isValid()) {
            $filePath = $request->file('file')->store('uploads'); // Simpan file
            return response()->json(['message' => 'File uploaded successfully!', 'path' => $filePath]);
        }

        return response()->json(['message' => 'File upload failed.'], 500);
    }
}
