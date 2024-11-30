<?php

namespace App\Http\Controllers;

use App\Models\News; // Pastikan model News diimpor
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function show($id)
    {
        $newsItem = News::findOrFail($id); // Ambil berita berdasarkan ID
        return view('news.show', compact('newsItem')); // Kirim data ke view
    }
} 