<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/ai-demo');
});

Route::get('/ai-demo', function () {
    return view('ai-demo');
});
