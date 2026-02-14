<?php

namespace App\Http\Controllers\Pemeliharaan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class JobcardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $q = trim((string) $request->input('q'));
            $normalizedName = \Illuminate\Support\Str::of(\Auth::user()->name)
                ->lower()
                ->replace(['-', ' '], '');

            // Query Oracle for Work Orders
            $query = DB::connection('oracle')
                ->table('WORKORDER')
                ->select([
                    'WONUM',
                    'DESCRIPTION',
                    'WORKTYPE',
                    'WOPRIORITY',
                    'SCHEDSTART',
                    'SCHEDFINISH',
                    'LOCATION',
                    'LEAD',
                    'STATUS',
                    'STATUSDATE',
                ])
                ->where('SITEID', 'KD');

            // Filter by labor name (case-insensitive, normalized)
            $query->whereRaw(
                "LOWER(REPLACE(REPLACE(LEAD, '-', ''), ' ', '')) LIKE ?",
                ['%' . $normalizedName . '%']
            );

            // Search filter
            if ($q !== '') {
                $like = "%" . strtoupper($q) . "%";
                $query->where(function ($sub) use ($like) {
                    $sub->where('WONUM', 'LIKE', $like)
                        ->orWhere('DESCRIPTION', 'LIKE', $like)
                        ->orWhere('WORKTYPE', 'LIKE', $like)
                        ->orWhere('WOPRIORITY', 'LIKE', $like);
                });
            }

            $allWorkOrders = $query->orderByDesc('STATUSDATE')->get();

            // Filter by jobcard file existence
            $workOrdersWithJobcard = collect($allWorkOrders)->filter(function ($wo) {
                $wo = (object) array_change_key_case((array) $wo, CASE_LOWER);
                $filename = 'jobcards/JOBCARD_' . $wo->wonum . '.pdf';
                return Storage::disk('public')->exists($filename);
            })->map(function ($wo) {
                $wo = (object) array_change_key_case((array) $wo, CASE_LOWER);
                return (object) [
                    'id' => $wo->wonum,
                    'wonum' => $wo->wonum,
                    'description' => $wo->description,
                    'worktype' => $wo->worktype,
                    'wopriority' => $wo->wopriority,
                    'schedule_start' => $wo->schedstart ? Carbon::parse($wo->schedstart)->format('d-m-Y H:i') : '-',
                    'schedule_finish' => $wo->schedfinish ? Carbon::parse($wo->schedfinish)->format('d-m-Y H:i') : '-',
                    'location' => $wo->location ?? '-',
                    'status' => $wo->status,
                    'jobcard_path' => 'jobcards/JOBCARD_' . $wo->wonum . '.pdf',
                ];
            });

            // Paginate manually
            $perPage = 25;
            $currentPage = $request->input('page', 1);
            $offset = ($currentPage - 1) * $perPage;
            $items = $workOrdersWithJobcard->slice($offset, $perPage)->values();
            
            $workOrders = new \Illuminate\Pagination\LengthAwarePaginator(
                $items,
                $workOrdersWithJobcard->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return view('pemeliharaan.jobcard', compact('workOrders', 'q'));

        } catch (\Exception $e) {
            Log::error('Error fetching jobcards from Oracle: ' . $e->getMessage());
            return view('pemeliharaan.jobcard', [
                'workOrders' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 25),
                'q' => $q ?? ''
            ])->with('error', 'Gagal mengambil data jobcard.');
        }
    }
}


