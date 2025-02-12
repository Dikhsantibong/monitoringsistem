s<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peserta;
use Illuminate\Http\Request;

class ScoreCardController extends Controller
{
    public function create()
    {
        // Ambil data peserta dari database
        $pesertaList = Peserta::all();
        
        return view('admin.score-card.create', [
            'pesertaList' => $pesertaList,
            'waktuMulai' => null,
            'waktuSelesai' => null
        ]);
    }
} 