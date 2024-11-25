<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function index()
    {
        $contents = Content::all(); // Ambil semua konten
        return view('admin.content.index', compact('contents'));
    }
} 