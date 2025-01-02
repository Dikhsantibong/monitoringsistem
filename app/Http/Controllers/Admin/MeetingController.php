<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function index()
    {
        $meetings = Meeting::with('creator')->latest()->paginate(10);
        return view('admin.meetings.index', compact('meetings'));
    }




    public function create()
    {
        return view('admin.meetings.create');
    }




    public function upload(Request $request)
    {
        // Logika untuk menangani upload rapat
    }


    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string',
            'tanggal' => 'required|date',
            'durasi' => 'required|integer|min:1',
        ]);

        $token = ZoomHelper::generateZoomToken();
        $response = Http::withToken($token)->post('https://api.zoom.us/v2/users/me/meetings', [
            'topic' => $request->judul,
            'type' => 2,
            'start_time' => $request->tanggal,
            'duration' => $request->durasi,
            'timezone' => 'Asia/Jakarta',
        ]);

        if ($response->successful()) {
            $data = $response->json();

            $meeting = Meeting::create([
                'judul' => $request->judul,
                'tanggal' => $request->tanggal,
                'durasi' => $request->durasi,
                'link_zoom' => $data['join_url'],
                'zoom_meeting_id' => $data['id'],
            ]);

            return redirect()->route('admin.meetings.index')->with('success', 'Meeting created successfully!');
        }

        return back()->withErrors(['error' => 'Failed to create Zoom meeting.']);
    }
    public function userIndex()
    {
        $meetings = Meeting::where('scheduled_at', '>=', now())->get();
        return view('user.meetings.index', compact('meetings'));
    }

    private function createZoomMeeting($title, $scheduledAt, $duration)
    {
        // Logika untuk mengintegrasikan API Zoom
        return 'https://zoom.us/j/123456789'; // Ganti dengan link yang dihasilkan dari API
    }

    // public function print()
    // {
    //     $logs = // ambil data yang diperlukan
    //     return view('admin.pembangkit.report-print', compact('logs'));
    // }

    // ... tambahkan method lainnya sesuai kebutuhan
} 
