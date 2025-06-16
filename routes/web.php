<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;

Route::get('/', function () {
    return redirect()->route('courses.index');
});


Route::resource('courses', CourseController::class);

