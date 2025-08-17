<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PengajuanMaterialFile;

class PemeliharaanPengajuanMaterialController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $files = PengajuanMaterialFile::where('user_id', $userId)->orderByDesc('id')->get();
        return view('pemeliharaan.pengajuan-material-index', compact('files'));
    }

    public function create()
    {
        return view('pemeliharaan.pengajuan-material-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:5120',
        ]);
        $userId = session('unit', 'unknown_unit');
        $filename = 'pengajuan_material_' . $userId . '_' . time() . '.pdf';
        $path = $request->file('pdf')->storeAs('pengajuan_material', $filename, 'public');
        \App\Models\PengajuanMaterialFile::create([
            'user_id' => $userId,
            'filename' => $filename,
            'path' => $path,
        ]);
        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $userId = Auth::id();
        $file = \App\Models\PengajuanMaterialFile::where('id', $id)->where('user_id', $userId)->firstOrFail();
        $pdfUrl = asset('storage/' . $file->path);
        return view('pemeliharaan.pengajuan-material-edit', compact('pdfUrl', 'file'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:5120',
        ]);
        $userId = Auth::id();
        $file = \App\Models\PengajuanMaterialFile::where('id', $id)->where('user_id', $userId)->firstOrFail();
        $filename = 'pengajuan_material_' . $userId . '_' . time() . '.pdf';
        $path = $request->file('pdf')->storeAs('pengajuan_material', $filename, 'public');
        $file->filename = $filename;
        $file->path = $path;
        $file->save();
        return response()->json(['success' => true]);
    }
}
