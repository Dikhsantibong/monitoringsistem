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
        $warehouseKeywords = [
            'UPDK' => '2020',
            'PLTD WUA WUA' => '3011',
            'PLTD BAU BAU' => '3012',
            'PLTD KOLAKA' => '3013',
            'PLTD POASIA' => '3014',
            'PLTU TANASA' => '3015',
            'PLTD RAHA' => '3016',
            'PLTD WANGI' => '3017',
            'PLTD LAMBUYA' => '3018',
            'PLTMG TANASA' => '3022',
            'PLTM MIKUASI' => '3023',
            'PLTD PASARWAJO' => '3035',
            'PLTD LADUMPI' => '3047',
            'PLTD LANIPA' => '4048',
            'PLTD EREKE' => '3049',
            'PLTD LANGARA' => '3050',
            'PLTM RONGI' => '3054',
            'PLTMG BAU BAU' => '3053',
        ];
        $warehouseCode = null;
        if ($search) {
            foreach ($warehouseKeywords as $keyword => $code) {
                if (stripos($search, $keyword) !== false || stripos($keyword, $search) !== false) {
                    $warehouseCode = $code;
                    break;
                }
            }
        }
        if ($search) {
            $query->where(function($q) use ($search, $warehouseCode) {
                if ($warehouseCode) {
                    $q->orWhere('warehouse', $warehouseCode);
                }
                $q->orWhere('discritc_code', 'like', "%$search%")
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
        $materials = $query->paginate(25);
        $lastUpdate = MaterialMaster::max('updated_at');
        return view('inventory.material.index', compact('materials', 'search', 'lastUpdate'));
    }
}
