<?php

namespace App\Http\Controllers\Pemeliharaan;

use App\Http\Controllers\Controller;

class SupportController extends Controller
{
    public function index()
    {
        return view('pemeliharaan.support');
    }
}


