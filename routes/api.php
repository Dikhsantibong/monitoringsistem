<?php

use App\Http\Controllers\Api\SectionController;
use Illuminate\Support\Facades\Route;

Route::get('sections/{department}', [SectionController::class, 'getSections'])
    ->name('api.sections.index'); 