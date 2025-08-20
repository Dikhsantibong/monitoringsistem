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
                $q->where('inventory_statistic_code', 'like', "%$search%")
                  ->orWhere('inventory_statistic_desc', 'like', "%$search%")
                  ->orWhere('stock_code', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhere('quantity', 'like', "%$search%")
                  ->orWhere('inventory_price', 'like', "%$search%")
                  ->orWhere('inventory_value', 'like', "%$search%")
                ;
            });
        }
        $materials = $query->get();
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
        $sheet = $spreadsheet->getSheet(1); // Sheet kedua
        $rows = $sheet->toArray();

        // Pastikan tabel material_master sudah ada dan ada kolom updated_at
        \DB::statement("CREATE TABLE IF NOT EXISTS material_master (
            id INT AUTO_INCREMENT PRIMARY KEY,
            inventory_statistic_code VARCHAR(255),
            inventory_statistic_desc VARCHAR(255),
            stock_code VARCHAR(255),
            description VARCHAR(255),
            quantity VARCHAR(255),
            inventory_price VARCHAR(255),
            inventory_value VARCHAR(255),
            updated_at TIMESTAMP NULL DEFAULT NULL
        )");
        // Tambahkan kolom jika belum ada
        $columns = collect(\DB::select("SHOW COLUMNS FROM material_master"))->pluck('Field');
        $fields = [
            'inventory_statistic_code',
            'inventory_statistic_desc',
            'stock_code',
            'description',
            'quantity',
            'inventory_price',
            'inventory_value',
            'updated_at',
        ];
        $fieldTypes = [
            'inventory_statistic_code' => 'VARCHAR(255)',
            'inventory_statistic_desc' => 'VARCHAR(255)',
            'stock_code' => 'VARCHAR(255)',
            'description' => 'VARCHAR(255)',
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
            if (count($row) < 7) continue;
            MaterialMaster::create([
                'inventory_statistic_code' => $row[0],
                'inventory_statistic_desc' => $row[1],
                'stock_code' => $row[2],
                'description' => $row[3],
                'quantity' => $row[4],
                'inventory_price' => $row[5],
                'inventory_value' => $row[6],
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('admin.material-master.index')->with('success', 'Data material master berhasil diupload.');
    }
}
