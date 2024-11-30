<?php

namespace App\Http\Controllers;
use App\Models\Unit;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $units = Unit::all();

        return view('homepage', compact('units'));
    }
} 