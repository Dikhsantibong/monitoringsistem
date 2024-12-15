<?php 

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\News;
use App\Models\MachineStatusLog;

class HomeController extends Controller
{
    public function index()
    {
        // Mengambil data unit pembangkit listrik
        $units = Unit::all();

        // Mengambil data dari MachineStatusLog dengan relasi ke PowerPlant
        $machineStatusLogs = MachineStatusLog::with('powerPlant')->get();

        // Statistik
        $total_units = $units->count();
        $total_capacity = $units->sum('capacity');
        $active_units = $units->where('status', 'Aktif')->count();

        // Berita
        $news = News::latest()->take(5)->get();

        // Mengembalikan tampilan dengan data yang diperlukan
        return view('homepage', compact('units', 'total_units', 'total_capacity', 'active_units', 'news', 'machineStatusLogs'));
    }
}
