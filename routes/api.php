<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;

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
        
        // This is a test route to check if the token works
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // We will add Task CRUD routes here in Phase 8
    });

});