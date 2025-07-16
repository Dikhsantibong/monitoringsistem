<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DraftNotulen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NotulenDraftController extends Controller
{
    public function list()
    {
        try {
            // Get all drafts ordered by latest first
            $drafts = DraftNotulen::orderBy('updated_at', 'desc')->get();

            // Transform the data to include formatted dates
            $drafts = $drafts->map(function ($draft) {
                $draft->updated_at_formatted = $draft->updated_at->format('d M Y H:i:s');
                return $draft;
            });

            return response()->json([
                'success' => true,
                'drafts' => $drafts
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to list drafts: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar draft',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function save(Request $request)
    {
        try {
            // Validate required field
            $validator = Validator::make($request->all(), [
                'temp_notulen_id' => 'required|string',
                'unit' => 'nullable|string',
                'bidang' => 'nullable|string',
                'sub_bidang' => 'nullable|string',
                'bulan' => 'nullable|numeric',
                'tahun' => 'nullable|numeric'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Find or create draft
            $draft = DraftNotulen::firstOrNew([
                'temp_notulen_id' => $request->temp_notulen_id
            ]);

            // Update draft data
            $draft->fill([
                // Basic notulen data - ensure these fields are always saved
                'unit' => $request->unit ?? $draft->unit,
                'bidang' => $request->bidang ?? $draft->bidang,
                'sub_bidang' => $request->sub_bidang ?? $draft->sub_bidang,
                'bulan' => $request->bulan ?? $draft->bulan,
                'tahun' => $request->tahun ?? $draft->tahun,

                // Form data
                'agenda' => $request->agenda,
                'tempat' => $request->tempat,
                'peserta' => $request->peserta,
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'tanggal' => $request->tanggal,
                'pembahasan' => $request->pembahasan,
                'tindak_lanjut' => $request->tindak_lanjut,
                'pimpinan_rapat_nama' => $request->pimpinan_rapat_nama,
                'notulis_nama' => $request->notulis_nama,
                'tanggal_tanda_tangan' => $request->tanggal_tanda_tangan
            ]);

            // Save the draft
            $draft->save();

            Log::info($draft->wasRecentlyCreated ?
                'New draft created: ' . $request->temp_notulen_id :
                'Draft updated: ' . $request->temp_notulen_id);

            return response()->json([
                'success' => true,
                'message' => 'Draft saved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save draft: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save draft',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function load($tempNotulenId)
    {
        try {
            $draft = DraftNotulen::where('temp_notulen_id', $tempNotulenId)->first();

            if (!$draft) {
                return response()->json([
                    'success' => false,
                    'message' => 'Draft not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'draft' => $draft
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load draft: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load draft',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete($tempNotulenId)
    {
        try {
            $draft = DraftNotulen::where('temp_notulen_id', $tempNotulenId)->first();

            if ($draft) {
                $draft->delete();
                Log::info('Draft deleted: ' . $tempNotulenId);
            }

            return response()->json([
                'success' => true,
                'message' => 'Draft deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete draft: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete draft',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
