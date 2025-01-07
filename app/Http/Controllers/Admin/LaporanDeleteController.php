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
    public function destroy($type, $id)
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
            
            return redirect()
                ->route('admin.laporan.manage')
                ->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Error deleting ' . $type . ': ' . $e->getMessage());
            
            return redirect()
                ->route('admin.laporan.manage')
                ->with('error', 'Terjadi kesalahan saat menghapus data');
        }
    }
} 