<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KatalogFile;

class InventoryKatalogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = KatalogFile::query();
        if ($search) {
            $query->where('filename', 'like', "%$search%");
        }
        $files = $query->orderByDesc('id')->paginate(15);
        return view('inventory.katalog.index', compact('files', 'search'));
    }
}
