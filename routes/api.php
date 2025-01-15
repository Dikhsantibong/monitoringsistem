<?php

use App\Models\MachineOperation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Section;
use App\Models\Pic;
use Illuminate\Support\Facades\Log;

Route::get('/machine-operations', function (Request $request) {
    $query = MachineOperation::with(['machine.powerPlant'])
        ->orderBy('recorded_at', 'desc')
        ->limit(50);

    if ($request->machine_id) {
        $query->where('machine_id', $request->machine_id);
    }

    return $query->get();
});

Route::get('/sections/{department}', function ($department) {
    return response()->json(
        \App\Models\Section::where('department_id', $department)
            ->orderBy('name')
            ->get()
    );
});

Route::get('/pics/{section}', function ($section) {
    return response()->json(
        \App\Models\Pic::where('section_id', $section)
            ->orderBy('name')
            ->get()
    );
});

Route::get('/sections-with-pics/{department}', function ($department) {
    Log::info('API Request received for department:', ['department_id' => $department]);
    
    try {
        // Ambil sections dengan pics
        $sections = Section::where('department_id', $department)
            ->with('pics')
            ->get()
            ->map(function ($section) {
                return [
                    'id' => $section->id,
                    'name' => $section->name,
                    'pics' => $section->pics->map(function ($pic) {
                        return [
                            'id' => $pic->id,
                            'name' => $pic->name,
                            'position' => $pic->position
                        ];
                    })
                ];
            });

        Log::info('Sections data:', ['sections' => $sections->toArray()]);
        
        return response()->json($sections);
    } catch (\Exception $e) {
        Log::error('Error in API:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'error' => 'Terjadi kesalahan saat mengambil data',
            'message' => $e->getMessage()
        ], 500);
    }
})->name('api.sections-with-pics'); 