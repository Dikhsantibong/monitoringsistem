<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peserta;

class PesertaController extends Controller
{
    public function update(Request $request)
    {
        try {
            $pesertaData = $request->input('peserta');
            
            // Simpan perubahan ke database
            // Ini hanya contoh, sesuaikan dengan struktur database Anda
            foreach ($pesertaData as $peserta) {
                Peserta::updateOrCreate(
                    ['id' => $peserta['id']],
                    ['jabatan' => $peserta['jabatan']]
                );
            }

            // Hapus peserta yang tidak ada dalam list baru
            $pesertaIds = array_column($pesertaData, 'id');
            Peserta::whereNotIn('id', $pesertaIds)->delete();

            return response()->json(['message' => 'Berhasil update peserta'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal update peserta: ' . $e->getMessage()], 500);
        }
    }
} 