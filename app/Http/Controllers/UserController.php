<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
