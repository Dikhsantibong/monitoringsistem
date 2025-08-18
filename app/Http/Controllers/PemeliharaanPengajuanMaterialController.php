<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PengajuanMaterialFile;

class PemeliharaanPengajuanMaterialController extends Controller
{
    public function index()
    {
        $userName = Auth::user()->name;
        $files = PengajuanMaterialFile::where('user_id', $userName)->orderByDesc('id')->paginate(10);
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
        $userName = Auth::user()->name;
        $filename = 'Pengajuan-Material(' . $userName . ')_' . time() . '.pdf';
        $path = $request->file('pdf')->storeAs('pengajuan_material', $filename, 'public');
        \App\Models\PengajuanMaterialFile::create([
            'user_id' => $userName,
            'filename' => $filename,
            'path' => $path,
        ]);
        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $userName = Auth::user()->name;
        $file = \App\Models\PengajuanMaterialFile::where('id', $id)->where('user_id', $userName)->firstOrFail();
        $pdfUrl = asset('storage/' . $file->path);
        return view('pemeliharaan.pengajuan-material-edit', compact('pdfUrl', 'file'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:5120',
        ]);
        $userName = Auth::user()->name;
        $file = \App\Models\PengajuanMaterialFile::where('id', $id)->where('user_id', $userName)->firstOrFail();
        $filename = 'Pengajuan-Material(' . $userName . ')_' . time() . '.pdf';
        $path = $request->file('pdf')->storeAs('pengajuan_material', $filename, 'public');
        $file->filename = $filename;
        $file->path = $path;
        $file->save();
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $userName = Auth::user()->name;
        $file = \App\Models\PengajuanMaterialFile::where('id', $id)->where('user_id', $userName)->firstOrFail();
        if ($file->path && \Storage::disk('public')->exists($file->path)) {
            \Storage::disk('public')->delete($file->path);
        }
        $file->delete();
        return response()->json(['success' => true]);
    }
}
