<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\KatalogFile;

class PemeliharaanKatalogController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $files = KatalogFile::where('user_id', $userId)->orderByDesc('id')->get();
        return view('pemeliharaan.katalog.index', compact('files'));
    }

    public function create()
    {
        $templateUrl = asset('template_katalog/form_stock_kode.pdf');
        return view('pemeliharaan.katalog.create', compact('templateUrl'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:5120',
        ]);
        $userId = Auth::id();
        $filename = 'katalog_' . $userId . '_' . time() . '.pdf';
        $path = $request->file('pdf')->storeAs('katalog', $filename, 'public');
        KatalogFile::create([
            'user_id' => $userId,
            'filename' => $filename,
            'path' => $path,
        ]);
        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $userId = Auth::id();
        $file = KatalogFile::where('id', $id)->where('user_id', $userId)->firstOrFail();
        $pdfUrl = asset('storage/' . $file->path);
        return view('pemeliharaan.katalog.edit', compact('pdfUrl', 'file'));
    }
}
