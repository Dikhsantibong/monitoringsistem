<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        $logs = Log::all(); // Ambil semua log aktivitas
        return view('admin.logs.index', compact('logs'));
    }
} 