<?php

namespace App\Http\Controllers\Pemeliharaan;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\MasterLabor;

class MasterLaborController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));
        $userName = auth()->user()->name;
        $labors = MasterLabor::when($q !== '', function ($query) use ($q) {
                $like = "%{$q}%";
                $query->where(function ($sub) use ($like) {
                    $sub->where('nama', 'LIKE', $like)
                        ->orWhere('bidang', 'LIKE', $like);
                });
            })
            ->where('unit', $userName)
            ->orderBy('id')
            ->get();
        return view('pemeliharaan.master-labor', compact('labors', 'q'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'bidang' => 'required|in:listrik,mesin,kontrol,alat bantu',
        ]);
        MasterLabor::create([
            'nama' => $request->nama,
            'bidang' => $request->bidang,
            'unit' => auth()->user()->name,
        ]);
        return redirect()->route('pemeliharaan.master-labor')->with('success', 'Labor berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'bidang' => 'required|in:listrik,mesin,kontrol,alat bantu',
        ]);
        $labor = MasterLabor::findOrFail($id);
        $labor->update([
            'nama' => $request->nama,
            'bidang' => $request->bidang,
        ]);
        return redirect()->route('pemeliharaan.master-labor')->with('success', 'Labor berhasil diupdate');
    }

    public function destroy($id)
    {
        $labor = MasterLabor::findOrFail($id);
        $labor->delete();
        return redirect()->route('pemeliharaan.master-labor')->with('success', 'Labor berhasil dihapus');
    }
}

