<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Web\TaskCommentController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// <--- THIS WAS MISSING

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Public Routes
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

    // Protected Routes (Require a valid Sanctum Token)
    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {

        // Task Statistics Route (Must precede apiResource)
        Route::get('/tasks/statistics', [TaskController::class, 'statistics']);

        // Task History Route (Must precede apiResource to ensure proper routing)
        Route::get('/tasks/{task}/history', [TaskController::class, 'history']);

        // Task Comments / Discussion Routes
        Route::get('/tasks/{task}/comments', [TaskCommentController::class, 'index']);
        Route::post('/tasks/{task}/comments', [TaskCommentController::class, 'store']);

        // Task CRUD Routes
        Route::apiResource('tasks', TaskController::class);

        // Authenticated user profile
        Route::get('/user', function (Request $request) {
            return $request->user()->load('roles:id,name');
        });

        // Users list for task assignment
        Route::get('/users', function () {
            return response()->json([
                'data' => User::with('roles:id,name')->get(['id', 'name', 'email']),
            ]);
        });

        // Logout
        Route::post('/logout', [AuthController::class, 'logout']);
    });

});
