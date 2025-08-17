<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaterialMaster;
use App\Models\KatalogFile;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        // Statistik Material
        $totalMaterial = MaterialMaster::count();
        $totalKategori = MaterialMaster::distinct('kategori')->count('kategori');
        // Statistik Katalog
        $totalKatalog = KatalogFile::count();
        // Katalog per bulan (12 bulan terakhir)
        $katalogPerBulan = KatalogFile::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as bulan'),
            DB::raw('COUNT(*) as total')
        )
        ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
        ->groupBy('bulan')
        ->orderBy('bulan')
        ->get();
        return view('inventory.dashboard', compact('totalMaterial', 'totalKategori', 'totalKatalog', 'katalogPerBulan'));
    }
}
