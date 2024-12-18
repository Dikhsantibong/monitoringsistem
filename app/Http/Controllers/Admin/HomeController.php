<?php 

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\News;
use App\Models\MachineStatusLog;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // Mengambil data unit pembangkit listrik dari database up_kendari
        $units = DB::connection('up_kendari')->table('units')->get();

        // Mengambil data dari MachineStatusLog dengan relasi ke PowerPlant dari database up_kendari
        $machineStatusLogs = DB::connection('up_kendari')->table('machine_status_logs')->with('powerPlant')->get();

        // Statistik
        $total_units = $units->count();
        $total_capacity = $units->sum('capacity');
        $active_units = $units->where('status', 'Aktif')->count();

        // Berita
        $news = DB::connection('up_kendari')->table('news')->latest()->take(5)->get();

        // Mengembalikan tampilan dengan data yang diperlukan
        return view('homepage', compact('units', 'total_units', 'total_capacity', 'active_units', 'news', 'machineStatusLogs'));
    }
}
