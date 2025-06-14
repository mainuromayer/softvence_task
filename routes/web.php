<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ContentController;

Route::get('/', function () {
    return redirect()->route('courses.index');
});


Route::resource('courses', CourseController::class);
Route::resource('modules', ModuleController::class);
Route::resource('contents', ContentController::class);