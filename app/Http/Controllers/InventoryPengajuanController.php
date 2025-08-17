<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanMaterialFile;

class InventoryPengajuanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = PengajuanMaterialFile::query();
        if ($search) {
            $query->where('filename', 'like', "%$search%");
        }
        $files = $query->orderByDesc('id')->paginate(15);
        return view('inventory.pengajuan-material.index', compact('files', 'search'));
    }
}
