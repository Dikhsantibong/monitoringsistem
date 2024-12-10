<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Machine;

class AdminController extends Controller
{
    public function index()
{
    return view('admin.dashboard');
}

public function performance()
{
    $machines = Machine::all(); // Ambil data mesin
    return view('admin.performance', compact('machines'));
}

}
