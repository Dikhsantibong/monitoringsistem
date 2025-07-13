<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NotulenDocumentationController;
use App\Http\Controllers\Api\NotulenAttendanceController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\OtherDiscussionController;
use App\Http\Controllers\Api\NotulenDraftController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Documentation upload endpoint
Route::group(['prefix' => 'public/api'], function () {
    Route::post('/notulen-documentation', [NotulenDocumentationController::class, 'store'])
        ->name('api.notulen.documentation.store');

    // Late attendance routes
    Route::post('/late-attendance/{notulen}', [NotulenAttendanceController::class, 'storeLateAttendance'])
        ->name('api.notulen.late-attendance.store');

    // Notulen Draft Routes
    Route::get('/notulen-draft/list', [NotulenDraftController::class, 'list']);
    Route::post('/notulen-draft/save', [NotulenDraftController::class, 'save']);
    Route::get('/notulen-draft/load/{tempNotulenId}', [NotulenDraftController::class, 'load']);
    Route::delete('/notulen-draft/delete/{tempNotulenId}', [NotulenDraftController::class, 'delete']);
});

// Existing routes
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
