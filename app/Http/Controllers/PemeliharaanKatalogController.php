<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\KatalogFile;

class PemeliharaanKatalogController extends Controller
{
    public function index()
    {
        $userName = Auth::user()->name;
        $files = KatalogFile::where('user_id', $userName)->orderByDesc('id')->paginate(10);
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
            'nama_material' => 'required|string|max:255',
            'no_part' => 'required|string|max:255',
        ]);
        $userName = Auth::user()->name;
        $filename = 'Pendaftaran-Katalog(' . $userName . ')_' . time() . '.pdf';
        $path = $request->file('pdf')->storeAs('katalog', $filename, 'public');
        \App\Models\KatalogFile::create([
            'user_id' => $userName,
            'filename' => $filename,
            'path' => $path,
            'nama_material' => $request->nama_material,
            'no_part' => $request->no_part,
        ]);
        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $userName = Auth::user()->name;
        $file = KatalogFile::where('id', $id)->where('user_id', $userName)->firstOrFail();
        $pdfUrl = asset('storage/' . $file->path);
        return view('pemeliharaan.katalog.edit', compact('pdfUrl', 'file'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:5120',
            'nama_material' => 'required|string|max:255',
            'no_part' => 'required|string|max:255',
        ]);
        $userName = Auth::user()->name;
        $file = KatalogFile::where('id', $id)->where('user_id', $userName)->firstOrFail();
        $filename = 'Pendaftaran-Katalog(' . $userName . ')_' . time() . '.pdf';
        $path = $request->file('pdf')->storeAs('katalog', $filename, 'public');
        $file->filename = $filename;
        $file->path = $path;
        $file->nama_material = $request->nama_material;
        $file->no_part = $request->no_part;
        $file->save();
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $userName = Auth::user()->name;
        $file = KatalogFile::where('id', $id)->where('user_id', $userName)->firstOrFail();
        if ($file->path && \Storage::disk('public')->exists($file->path)) {
            \Storage::disk('public')->delete($file->path);
        }
        $file->delete();
        return response()->json(['success' => true]);
    }
}
