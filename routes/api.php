<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TaskController; // <--- THIS WAS MISSING

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Public Routes
    Route::post('/login', [AuthController::class, 'login']);

    // Protected Routes (Require a valid Sanctum Token)
    Route::middleware('auth:sanctum')->group(function () {
        
        // Task CRUD Routes
        Route::apiResource('tasks', TaskController::class);
        
        // Test route to check if the token works
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });

});