<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;

Route::get('/', function () {
    return redirect()->route('courses.index');
});


Route::resource('courses', CourseController::class);

// Course module routes
Route::post('/courses/{course}/modules', [CourseController::class, 'storeModule'])
     ->name('courses.module.store');
Route::delete('/courses/{course}/modules/{module}', [CourseController::class, 'destroyModule'])
     ->name('courses.module.destroy');

// Course content routes
Route::post('/courses/{module}/contents', [CourseController::class, 'storeContent'])
     ->name('courses.content.store');
Route::delete('/courses/{course}/contents/{content}', [CourseController::class, 'destroyContent'])
     ->name('courses.content.destroy');
