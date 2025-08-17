<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KatalogFile;

class InventoryKatalogController extends Controller
{
    public function index()
    {
        $files = KatalogFile::all();
        return view('inventory.katalog.index', compact('files'));
    }
}
