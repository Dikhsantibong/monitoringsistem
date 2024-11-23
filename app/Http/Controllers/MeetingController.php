<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function index()
    {
        return view('user.daily-meeting'); // Ganti dengan view yang sesuai
    }
}
