<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::post('/tasks/generate-description', [TaskController::class, 'generateDescription']);
Route::post('/tasks/suggest-status', [TaskController::class, 'suggestStatus']);
Route::apiResource('tasks', TaskController::class);

