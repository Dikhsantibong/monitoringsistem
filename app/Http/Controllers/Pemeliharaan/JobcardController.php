<?php

namespace App\Http\Controllers\Pemeliharaan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class JobcardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $q = trim((string) $request->input('q'));
            
            // 1. Get all jobcard files from storage
            $files = Storage::disk('public')->files('jobcards');
            
            // 2. Extract WONUMs from filenames (JOBCARD_{wonum}.pdf)
            $existingWonums = collect($files)->map(function ($file) {
                // filename example: jobcards/JOBCARD_12345.pdf
                if (preg_match('/JOBCARD_(\w+)\.pdf$/', $file, $matches)) {
                    return $matches[1];
                }
                return null;
            })->filter()->unique()->values()->toArray();

            if (empty($existingWonums)) {
                return view('pemeliharaan.jobcard', [
                    'workOrders' => new LengthAwarePaginator([], 0, 25),
                    'q' => $q ?? ''
                ]);
            }

            // 3. Query Oracle for these specific WONUMs
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
                ->whereIn('WONUM', $existingWonums)
                ->where('STATUS', 'APPR');

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

            // Get all matching records
            $allWorkOrders = $query->orderByDesc('STATUSDATE')->get();

            // Map to standard format
            $formattedWorkOrders = collect($allWorkOrders)->map(function ($wo) {
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
                    'statusdate' => $wo->statusdate
                ];
            });

            // Paginate manually
            $perPage = 25;
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $currentItems = $formattedWorkOrders->slice(($currentPage - 1) * $perPage, $perPage)->all();
            
            $workOrders = new LengthAwarePaginator(
                $currentItems,
                $formattedWorkOrders->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return view('pemeliharaan.jobcard', compact('workOrders', 'q'));

        } catch (\Exception $e) {
            Log::error('Error fetching jobcards from Oracle: ' . $e->getMessage());
            return view('pemeliharaan.jobcard', [
                'workOrders' => new LengthAwarePaginator([], 0, 25),
                'q' => $q ?? ''
            ])->with('error', 'Gagal mengambil data jobcard.');
        }
    }
}


