<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotulenFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NotulenFileController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'file' => 'required|mimes:pdf,doc,docx|max:10240', // Max 10MB
                'caption' => 'nullable|string|max:500',
                'temp_notulen_id' => 'required|string'
            ]);

            $sessionId = Str::uuid()->toString();

            $file = $request->file('file');
            $filename = $sessionId . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('notulen-files', $filename, 'public');

            if (!$path) {
                throw new \Exception('Gagal menyimpan file dokumen');
            }

            try {
                $notulenFile = NotulenFile::create([
                    'session_id' => $sessionId,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                    'caption' => $validated['caption'] ?? null
                ]);
            } catch (\Exception $e) {
                Storage::disk('public')->delete($path);
                throw new \Exception('Gagal menyimpan data ke database: ' . $e->getMessage());
            }

            $files = Cache::get("notulen_files_{$validated['temp_notulen_id']}", []);

            $baseUrl = $request->getSchemeAndHttpHost();
            if (str_contains($request->getRequestUri(), '/public')) {
                $baseUrl .= '/public';
            }

            $fileData = [
                'id' => $notulenFile->id,
                'session_id' => $sessionId,
                'file_path' => $path,
                'file_url' => $baseUrl . '/storage/' . str_replace('public/', '', $path),
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getClientMimeType(),
                'caption' => $validated['caption'] ?? null
            ];

            $files[] = $fileData;
            Cache::put("notulen_files_{$validated['temp_notulen_id']}", $files, now()->addHours(2));

            return response()->json([
                'success' => true,
                'message' => 'File dokumen berhasil diupload',
                'file' => $fileData
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading notulen file: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $file = NotulenFile::findOrFail($id);
        \Illuminate\Support\Facades\Storage::disk('public')->delete($file->file_path);
        $file->delete();
        return response()->json(['success' => true, 'message' => 'File berhasil dihapus']);
    }

    public function storeForExisting(Request $request)
    {
        try {
            $validated = $request->validate([
                'file' => 'required|mimes:pdf,doc,docx|max:10240', // Max 10MB
                'caption' => 'nullable|string|max:500',
                'notulen_id' => 'required|exists:notulens,id'  // Fix table name from notulen to notulens
            ]);

            $sessionId = Str::uuid()->toString();

            $file = $request->file('file');
            $filename = $sessionId . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('notulen-files', $filename, 'public');

            if (!$path) {
                throw new \Exception('Gagal menyimpan file dokumen');
            }

            try {
                $notulenFile = NotulenFile::create([
                    'notulen_id' => $validated['notulen_id'],
                    'session_id' => $sessionId,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                    'caption' => $validated['caption'] ?? null
                ]);
            } catch (\Exception $e) {
                Storage::disk('public')->delete($path);
                throw new \Exception('Gagal menyimpan data ke database: ' . $e->getMessage());
            }

            $baseUrl = $request->getSchemeAndHttpHost();
            if (str_contains($request->getRequestUri(), '/public')) {
                $baseUrl .= '/public';
            }

            $fileData = [
                'id' => $notulenFile->id,
                'session_id' => $sessionId,
                'file_path' => $path,
                'file_url' => $baseUrl . '/storage/' . str_replace('public/', '', $path),
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getClientMimeType(),
                'caption' => $validated['caption'] ?? null
            ];

            return response()->json([
                'success' => true,
                'message' => 'File dokumen berhasil diupload',
                'file' => $fileData
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading notulen file: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
} 