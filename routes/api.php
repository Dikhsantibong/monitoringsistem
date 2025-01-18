<?php

use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\OtherDiscussionController;
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

Route::get('/generate-no-pembahasan', [App\Http\Controllers\Admin\OtherDiscussionController::class, 'generateNoPembahasan']);

Route::get('/generate-numbers', [OtherDiscussionController::class, 'generateNumbers']); 
Route::get('/generate-no-pembahasan', [OtherDiscussionController::class, 'generateNoPembahasan'])
    ->name('api.generate-no-pembahasan');
    Route::get('/generate-no-pembahasan', [OtherDiscussionController::class, 'generateNoPembahasan'])
    ->name('api.generate-no-pembahasan');
