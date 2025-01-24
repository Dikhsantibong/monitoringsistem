<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Machine;
use Illuminate\Support\Facades\Storage;

class AdminPembangkitController extends Controller
{
    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'machine_id' => 'required|exists:machines,id'
            ]);

            $machine = Machine::findOrFail($request->machine_id);

            // Delete old image if exists
            if ($machine->image_url) {
                Storage::delete('public/' . $machine->image_url);
            }

            // Store new image
            $path = $request->file('image')->store('machine-images', 'public');
            
            // Update machine record
            $machine->image_url = $path;
            $machine->save();

            return response()->json([
                'success' => true,
                'message' => 'Gambar berhasil diunggah',
                'image_url' => Storage::url($path)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah gambar: ' . $e->getMessage()
            ], 500);
        }
    }
} 