<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaterialMaster;

class InventoryMaterialController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = MaterialMaster::query();
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%$search%")
                  ->orWhere('deskripsi', 'like', "%$search%")
                  ->orWhere('kategori', 'like', "%$search%");
            });
        }
        $materials = $query->get();
        $lastUpdate = MaterialMaster::max('updated_at');
        return view('inventory.material.index', compact('materials', 'search', 'lastUpdate'));
    }
}
