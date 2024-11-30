<?php 

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\News;

class HomeController extends Controller
{
    public function index()
    {
        // Data unit pembangkit listrik
        $units = Unit::all();

        // Statistik
        $total_units = $units->count();
        $total_capacity = $units->sum('capacity');
        $active_units = $units->where('status', 'Aktif')->count();

        // Berita
        $news = News::latest()->take(5)->get();

        return view('homepage', compact('units', 'total_units', 'total_capacity', 'active_units', 'news'));
    }
}
