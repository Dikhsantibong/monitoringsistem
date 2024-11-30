<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PembangkitController extends Controller
{
    public function ready()
    {
        // Logika untuk menampilkan kesiapan pembangkit
        $units = [
            (object) ['name' => 'Unit 1', 'status' => 'Aktif', 'capacity' => 100, 'availability' => true],
            (object) ['name' => 'Unit 2', 'status' => 'Tidak Aktif', 'capacity' => 50, 'availability' => false],
            (object) ['name' => 'Unit 3', 'status' => 'Aktif', 'capacity' => 75, 'availability' => true],
            (object) ['name' => 'Unit 4', 'status' => 'Tidak Aktif', 'capacity' => 60, 'availability' => false],
            // Tambahkan data sementara lainnya sesuai kebutuhan
        ];
        return view('admin.pembangkit.ready', compact('units'));
    }

    // Tambahkan metode lain sesuai kebutuhan
}
