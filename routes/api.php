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
    try {
        // Query langsung untuk debugging
        $sections = Section::where('department_id', $department)->get();
        $result = [];

        foreach ($sections as $section) {
            $pics = Pic::where('section_id', $section->id)->get();
            $result[] = [
                'id' => $section->id,
                'name' => $section->name,
                'pics' => $pics->map(function ($pic) {
                    return [
                        'id' => $pic->id,
                        'name' => $pic->name,
                        'position' => $pic->position
                    ];
                })
            ];
        }

        return response()->json($result);
    } catch (\Exception $e) {
        \Log::error('Error in sections-with-pics:', [
            'department_id' => $department,
            'error' => $e->getMessage()
        ]);
        return response()->json(['error' => $e->getMessage()], 500);
    }
}); 