<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\MaterialMaster;
use Illuminate\Support\Carbon;

class MasterMaterialController extends Controller
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
        $materials = $query->paginate(25);
        $lastUpdate = MaterialMaster::max('updated_at');
        return view('admin.material-master.index', compact('materials', 'search', 'lastUpdate'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('excel_file');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
        $sheet5 = $spreadsheet->getSheet(4); // Sheet ke-5 (index 4)
        $sheet6 = $spreadsheet->getSheet(5); // Sheet ke-6 (index 5)
        $rows5 = $sheet5->toArray();
        $rows6 = $sheet6->toArray();
        $rows = array_merge($rows5, $rows6);

        // Pastikan tabel material_master sudah ada dan ada kolom updated_at
        \DB::statement("CREATE TABLE IF NOT EXISTS material_master (
            id INT AUTO_INCREMENT PRIMARY KEY,
            discritc_code VARCHAR(255),
            warehouse VARCHAR(255),
            bin_code VARCHAR(255),
            inventory_statistic_code VARCHAR(255),
            inventory_statistic_desc VARCHAR(255),
            material_num VARCHAR(255),
            stock_code VARCHAR(255),
            description VARCHAR(255),
            stock_class VARCHAR(255),
            stock_type VARCHAR(255),
            inventory_category VARCHAR(255),
            unit_of_issue VARCHAR(255),
            minimum_soh VARCHAR(255),
            maximum_soh VARCHAR(255),
            quantity VARCHAR(255),
            inventory_price VARCHAR(255),
            inventory_value VARCHAR(255),
            updated_at TIMESTAMP NULL DEFAULT NULL
        )");
        // Tambahkan kolom jika belum ada
        $columns = collect(\DB::select("SHOW COLUMNS FROM material_master"))->pluck('Field');
        $fields = [
            'discritc_code',
            'warehouse',
            'bin_code',
            'inventory_statistic_code',
            'inventory_statistic_desc',
            'material_num',
            'stock_code',
            'description',
            'stock_class',
            'stock_type',
            'inventory_category',
            'unit_of_issue',
            'minimum_soh',
            'maximum_soh',
            'quantity',
            'inventory_price',
            'inventory_value',
            'updated_at',
        ];
        $fieldTypes = [
            'discritc_code' => 'VARCHAR(255)',
            'warehouse' => 'VARCHAR(255)',
            'bin_code' => 'VARCHAR(255)',
            'inventory_statistic_code' => 'VARCHAR(255)',
            'inventory_statistic_desc' => 'VARCHAR(255)',
            'material_num' => 'VARCHAR(255)',
            'stock_code' => 'VARCHAR(255)',
            'description' => 'VARCHAR(255)',
            'stock_class' => 'VARCHAR(255)',
            'stock_type' => 'VARCHAR(255)',
            'inventory_category' => 'VARCHAR(255)',
            'unit_of_issue' => 'VARCHAR(255)',
            'minimum_soh' => 'VARCHAR(255)',
            'maximum_soh' => 'VARCHAR(255)',
            'quantity' => 'VARCHAR(255)',
            'inventory_price' => 'VARCHAR(255)',
            'inventory_value' => 'VARCHAR(255)',
            'updated_at' => 'TIMESTAMP NULL DEFAULT NULL',
        ];
        foreach ($fields as $field) {
            if (!$columns->contains($field)) {
                \DB::statement("ALTER TABLE material_master ADD COLUMN $field {$fieldTypes[$field]}");
            }
        }

        // Truncate data lama di seluruh unit
        $unitConnections = [
            'mysql_bau_bau',
            'mysql_kolaka',
            'mysql_poasia',
            'mysql_wua_wua',
        ];
        foreach ($unitConnections as $unit) {
            \DB::connection($unit)->table('material_master')->truncate();
        }
        // Hapus data lama di database utama
        MaterialMaster::truncate();

        // Asumsi baris ke-13 (index 12) adalah awal data
        for ($i = 12; $i < count($rows); $i++) {
            $row = $rows[$i];
            if (count($row) < 17) continue;
            MaterialMaster::create([
                'discritc_code' => $row[0],
                'warehouse' => $row[1],
                'bin_code' => $row[2],
                'inventory_statistic_code' => $row[3],
                'inventory_statistic_desc' => $row[4],
                'material_num' => $row[5],
                'stock_code' => $row[6],
                'description' => $row[7],
                'stock_class' => $row[8],
                'stock_type' => $row[9],
                'inventory_category' => $row[10],
                'unit_of_issue' => $row[11],
                'minimum_soh' => $row[12],
                'maximum_soh' => $row[13],
                'quantity' => $row[14],
                'inventory_price' => $row[15],
                'inventory_value' => $row[16],
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('admin.material-master.index')->with('success', 'Data material master berhasil diupload.');
    }
}
