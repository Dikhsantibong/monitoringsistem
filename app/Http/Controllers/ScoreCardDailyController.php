<?php

namespace App\Http\Controllers;

use App\Models\ScoreCardDaily;
use Illuminate\Http\Request;

class ScoreCardDailyController extends Controller
{
    public function index()
    {
        $scoreCards = ScoreCardDaily::latest()->get();
        return view('admin.score-card.index', compact('scoreCards'));
    }

    public function create()
    {
        return view('admin.score-card.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'lokasi' => 'required|string',
            'peserta' => 'required|array',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
        ]);

        ScoreCardDaily::create($validated);
        return redirect()->route('admin.score-card.index')->with('success', 'Score Card berhasil dibuat');
    }
} 