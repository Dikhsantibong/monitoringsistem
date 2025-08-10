<?php

namespace App\Http\Controllers\Pemeliharaan;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class MasterLaborController extends Controller
{
    public function index()
    {
        $labors = DB::table('master_labors')->orderBy('id')->get();
        return view('pemeliharaan.master-labor', compact('labors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'bidang' => 'required|in:listrik,mesin,kontrol,alat bantu',
        ]);
        DB::table('master_labors')->insert([
            'nama' => $request->nama,
            'bidang' => $request->bidang,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->route('pemeliharaan.master-labor')->with('success', 'Labor berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'bidang' => 'required|in:listrik,mesin,kontrol,alat bantu',
        ]);
        DB::table('master_labors')->where('id', $id)->update([
            'nama' => $request->nama,
            'bidang' => $request->bidang,
            'updated_at' => now(),
        ]);
        return redirect()->route('pemeliharaan.master-labor')->with('success', 'Labor berhasil diupdate');
    }

    public function destroy($id)
    {
        DB::table('master_labors')->where('id', $id)->delete();
        return redirect()->route('pemeliharaan.master-labor')->with('success', 'Labor berhasil dihapus');
    }
}

