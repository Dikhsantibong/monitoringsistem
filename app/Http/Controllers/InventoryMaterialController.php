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
                $q->where('discritc_code', 'like', "%$search%")
                  ->orWhere('warehouse', 'like', "%$search%")
                  ->orWhere('bin_code', 'like', "%$search%")
                  ->orWhere('inventory_statistic_code', 'like', "%$search%")
                  ->orWhere('inventory_statistic_desc', 'like', "%$search%")
                  ->orWhere('material_num', 'like', "%$search%")
                  ->orWhere('stock_code', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhere('stock_class', 'like', "%$search%")
                  ->orWhere('stock_type', 'like', "%$search%")
                  ->orWhere('inventory_category', 'like', "%$search%")
                  ->orWhere('unit_of_issue', 'like', "%$search%")
                  ->orWhere('minimum_soh', 'like', "%$search%")
                  ->orWhere('maximum_soh', 'like', "%$search%")
                  ->orWhere('quantity', 'like', "%$search%")
                  ->orWhere('inventory_price', 'like', "%$search%")
                  ->orWhere('inventory_value', 'like', "%$search%")
                ;
            });
        }
        $materials = $query->get();
        $lastUpdate = MaterialMaster::max('updated_at');
        return view('inventory.material.index', compact('materials', 'search', 'lastUpdate'));
    }
}
