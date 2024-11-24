<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Machine;

class UserController extends Controller
{
    public function dashboard()
    {
        return view('user.dashboard'); // Pastikan file view ini ada
    }

    public function monitoring()
    {
        return view('user.monitoring'); // Pastikan file view ini ada
    }

    public function dailyMeeting()
    {
        return view('user.daily-Meeting'); // Pastikan file view ini ada
    }

    public function support()
    {
        return view('user.support'); // Pastikan file view ini ada
    }

    public function documentation()
    {
        return view('user.documentation'); // Pastikan file view ini ada
    }

    public function profile()
    {
        return view('user.profile'); // Pastikan Anda memiliki view untuk profil pengguna
    }

    public function machineMonitor()
    {
        // Ambil data mesin dari model atau sumber data lainnya
        $machines = Machine::all(); // Ganti dengan logika yang sesuai untuk mendapatkan data mesin
        return view('user.machine-monitor', compact('machines'));
    }
}
