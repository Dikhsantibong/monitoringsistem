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
                $q->where('code', 'like', "%$search%")
                  ->orWhere('deskripsi', 'like', "%$search%")
                  ->orWhere('kategori', 'like', "%$search%");
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
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Pastikan tabel material_master sudah ada dan ada kolom updated_at
        DB::statement("CREATE TABLE IF NOT EXISTS material_master (
            id INT AUTO_INCREMENT PRIMARY KEY,
            code VARCHAR(255),
            deskripsi VARCHAR(255),
            kategori VARCHAR(255),
            updated_at TIMESTAMP NULL DEFAULT NULL
        )");
        // Tambahkan kolom updated_at jika belum ada
        $columns = collect(DB::select("SHOW COLUMNS FROM material_master"))->pluck('Field');
        if (!$columns->contains('updated_at')) {
            DB::statement("ALTER TABLE material_master ADD COLUMN updated_at TIMESTAMP NULL DEFAULT NULL");
        }

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            if (count($row) < 3) continue;
            $code = $row[0];
            $deskripsi = $row[1];
            $kategori = $row[2];
            MaterialMaster::create([
                'code' => $code,
                'deskripsi' => $deskripsi,
                'kategori' => $kategori,
                'updated_at' => Carbon::now(),
            ]);
        }

        return redirect()->route('admin.material-master.index')->with('success', 'Data material master berhasil diupload.');
    }
}
