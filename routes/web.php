<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
});

Route::get('/app', function () {
    return view('app');
});

Route::get('/dashboard', function () {
    return view('app');
});

Route::view('/docs', 'docs');

Route::get('/landing', function () {
    return view('welcome');
});
