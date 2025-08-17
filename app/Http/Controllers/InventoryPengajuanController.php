<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanMaterialFile;

class InventoryPengajuanController extends Controller
{
    public function index()
    {
        $files = PengajuanMaterialFile::all();
        return view('inventory.pengajuan-material.index', compact('files'));
    }
}
