<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotulenDocumentation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NotulenDocumentationController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'image' => 'required|image|max:5120', // Max 5MB
                'caption' => 'nullable|string|max:500',
                'temp_notulen_id' => 'required|string'
            ]);

            // Generate a session ID for this documentation
            $sessionId = Str::uuid()->toString();

            // Store the image in public storage
            $file = $request->file('image');
            $filename = $sessionId . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('notulen-documentation', $filename, 'public');

            if (!$path) {
                throw new \Exception('Gagal menyimpan file gambar');
            }

            // Save documentation using model
            try {
                $documentation = NotulenDocumentation::create([
                    'session_id' => $sessionId,
                    'image_path' => $path,
                    'caption' => $validated['caption'] ?? null
                ]);
            } catch (\Exception $e) {
                // If database insert fails, delete the uploaded file
                Storage::disk('public')->delete($path);
                throw new \Exception('Gagal menyimpan data ke database: ' . $e->getMessage());
            }

            // Store in cache for the notulen form
            $documentations = Cache::get("notulen_documentations_{$validated['temp_notulen_id']}", []);

            // Generate correct image URL for both environments
            $baseUrl = $request->getSchemeAndHttpHost();
            if (str_contains($request->getRequestUri(), '/public')) {
                $baseUrl .= '/public';
            }

            $documentationData = [
                'id' => $documentation->id,
                'session_id' => $sessionId,
                'image_path' => $path,
                'image_url' => $baseUrl . '/storage/' . str_replace('public/', '', $path),
                'caption' => $validated['caption'] ?? null
            ];

            $documentations[] = $documentationData;
            Cache::put("notulen_documentations_{$validated['temp_notulen_id']}", $documentations, now()->addHours(2));

            return response()->json([
                'success' => true,
                'message' => 'Dokumentasi berhasil disimpan',
                'documentation' => $documentationData
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading documentation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
