<?php

namespace App\Http\Controllers;

use App\Models\Blog; // Pastikan model Blog diimpor
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function show($id)
    {
        $blogItem = Blog::findOrFail($id); // Ambil blog berdasarkan ID
        return view('blog.show', compact('blogItem')); // Kirim data ke view
    }
} 