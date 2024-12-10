<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        // Validasi data yang diterima
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        // Logika untuk menyimpan data atau mengirim email
        // Misalnya, Anda bisa menyimpan data ke database atau mengirim email

        return redirect()->back()->with('success', 'Pesan Anda telah dikirim!');
    }
} 