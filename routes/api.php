<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::post('/tasks/generate-description', [TaskController::class, 'generateDescription']);
Route::apiResource('tasks', TaskController::class);

