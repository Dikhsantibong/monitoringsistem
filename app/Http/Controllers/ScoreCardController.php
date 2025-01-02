<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScoreCard;
use PDF;

class ScoreCardController extends Controller
{
    public function getData(Request $request)
    {
        $tanggal = $request->tanggal ?? now()->format('Y-m-d');
        
        $scoreCard = ScoreCard::where('tanggal', $tanggal)
            ->latest()
            ->first();

        if (!$scoreCard) {
            return response()->json([
                'peserta' => [],
                'ketentuan' => []
            ]);
        }

        return response()->json([
            'peserta' => json_decode($scoreCard->peserta, true),
            'ketentuan' => json_decode($scoreCard->ketentuan_rapat, true)
        ]);
    }

    public function download(Request $request)
    {
        $tanggal = $request->tanggal ?? now()->format('Y-m-d');
        
        $scoreCard = ScoreCard::where('tanggal', $tanggal)
            ->latest()
            ->first();

        // Buat PDF atau Excel sesuai kebutuhan
        $pdf = PDF::loadView('admin.score-card.download', [
            'scoreCard' => $scoreCard,
            'tanggal' => $tanggal
        ]);

        return $pdf->download("score-card-{$tanggal}.pdf");
    }s

    public function index()
    {
        $scoreCards = ScoreCardDaily::latest()->get();
        $latestScoreCard = $scoreCards->first();
        
        return view('admin.score-card.index', compact('scoreCards', 'latestScoreCard'));
    }
}