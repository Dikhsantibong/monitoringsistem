<?php

namespace App\Http\Controllers\Pemeliharaan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkOrder;

class JobcardController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));
        $normalizedName = \Illuminate\Support\Str::of(\Auth::user()->name)
            ->lower()
            ->replace(['-', ' '], '');

        $workOrders = WorkOrder::with(['powerPlant:id,name'])
            ->select([
                'id',
                'description',
                'document_path',
                'type',
                'priority',
                'schedule_start',
                'schedule_finish',
                'power_plant_id',
                'updated_at',
            ])
            ->where('status', 'Closed')
            ->whereNotNull('document_path')
            ->whereRaw(
                "LOWER(REPLACE(REPLACE(labor, '-', ''), ' ', '')) LIKE ?",
                ['%' . $normalizedName . '%']
            )
            ->when($q !== '', function ($query) use ($q) {
                $like = "%{$q}%";
                $query->where(function ($sub) use ($like) {
                    $sub->where('id', 'LIKE', $like)
                        ->orWhere('description', 'LIKE', $like)
                        ->orWhere('type', 'LIKE', $like)
                        ->orWhere('priority', 'LIKE', $like);
                });
            })
            ->orderByDesc('updated_at')
            ->paginate(25)
            ->withQueryString();

        return view('pemeliharaan.jobcard', compact('workOrders', 'q'));
    }
}


