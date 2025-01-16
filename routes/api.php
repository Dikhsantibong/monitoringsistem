<?php

use App\Http\Controllers\Api\SectionController;
use Illuminate\Support\Facades\Route;

Route::get('sections/{department}', [SectionController::class, 'getSections'])
    ->name('api.sections.index'); 

Route::get('/sections/{department}', function ($department) {
    return \App\Models\Section::where('department_id', $department)
        ->orderBy('name')
        ->get(['id', 'name']);
}); 

Route::get('/pics/{section}', function ($section) {
    return \App\Models\Pic::where('section_id', $section)
        ->orderBy('name')
        ->get(['id', 'name', 'position']);
}); 