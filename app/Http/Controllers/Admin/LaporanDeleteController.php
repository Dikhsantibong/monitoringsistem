<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\WorkOrder;
use App\Models\WoBacklog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LaporanDeleteController extends Controller
{
    public function destroy(Request $request, $type, $id)
    {
        try {
            switch ($type) {
                case 'sr':
                    $item = ServiceRequest::findOrFail($id);
                    $message = 'Service Request berhasil dihapus';
                    break;
                case 'wo':
                    $item = WorkOrder::findOrFail($id);
                    $message = 'Work Order berhasil dihapus';
                    break;
                case 'backlog':
                    $item = WoBacklog::findOrFail($id);
                    $message = 'WO Backlog berhasil dihapus';
                    break;
                default:
                    throw new \Exception('Tipe data tidak valid');
            }

            $item->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Error deleting ' . $type . ': ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus data'
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data');
        }
    }
} 