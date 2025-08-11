<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\MaterialMaster;

class MasterMaterialController extends Controller
{
    public function index()
    {
        $materials = MaterialMaster::all();
        return view('admin.material-master.index', compact('materials'));
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

        // Pastikan tabel material_master sudah ada
        DB::statement("CREATE TABLE IF NOT EXISTS material_master (
            id INT AUTO_INCREMENT PRIMARY KEY,
            code VARCHAR(255),
            deskripsi VARCHAR(255),
            kategori VARCHAR(255)
        )");

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
            ]);
        }

        return redirect()->route('admin.material-master.index')->with('success', 'Data material master berhasil diupload.');
    }
}
