<?php

use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\EmployeeController;
use App\Http\Controllers\Web\ManagerController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\TaskCommentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Multi-Page Application
|--------------------------------------------------------------------------
*/

// Public Landing Page & Docs
Route::get('/', function () {
    if (Auth::check()) {
        return (new AuthController)->redirectBasedOnRole(Auth::user());
    }

    return view('welcome');
})->name('home');

Route::view('/docs', 'docs')->name('docs');
Route::view('/spa', 'app')->name('spa');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    // Password Reset & Verification Routes
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->middleware('throttle:6,1')->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:6,1')->name('password.update');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Universal dashboard redirect
    Route::get('/dashboard', function () {
        return (new AuthController)->redirectBasedOnRole(Auth::user());
    })->name('dashboard');

    // User Profile & Account Settings
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // In-App Notification Actions
    Route::post('/notifications/mark-all-read', [ProfileController::class, 'markAllNotificationsAsRead'])->name('notifications.markAllRead');
    Route::post('/notifications/{id}/mark-read', [ProfileController::class, 'markNotificationAsRead'])->name('notifications.markRead');

    // Task Collaboration & Discussion (Comments)
    Route::get('/tasks/{task}/comments', [TaskCommentController::class, 'index'])->name('tasks.comments.index');
    Route::post('/tasks/{task}/comments', [TaskCommentController::class, 'store'])->name('tasks.comments.store');

    // -------------------------------------------------------------
    // 1. Admin Interface & Rights (Full Governance & Oversight)
    // -------------------------------------------------------------
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/tasks', [AdminController::class, 'tasks'])->name('tasks');
        Route::get('/tasks/export', [AdminController::class, 'export'])->name('tasks.export');
        Route::post('/tasks', [AdminController::class, 'storeTask'])->name('tasks.store');
        Route::put('/tasks/{task}', [AdminController::class, 'updateTask'])->name('tasks.update');
        Route::delete('/tasks/{task}', [AdminController::class, 'deleteTask'])->name('tasks.destroy');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::post('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('users.updateRole');
        Route::post('/users/{user}/send-reset-link', [AdminController::class, 'sendUserResetLink'])->name('users.sendResetLink');
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.destroy');
    });

    // -------------------------------------------------------------
    // 2. Manager Interface & Rights (Department Delegation & Team)
    // -------------------------------------------------------------
    Route::middleware('role:manager')->prefix('manager')->name('manager.')->group(function () {
        Route::get('/dashboard', [ManagerController::class, 'dashboard'])->name('dashboard');
        Route::get('/tasks', [ManagerController::class, 'tasks'])->name('tasks');
        Route::get('/tasks/export', [ManagerController::class, 'export'])->name('tasks.export');
        Route::post('/tasks', [ManagerController::class, 'storeTask'])->name('tasks.store');
        Route::put('/tasks/{task}', [ManagerController::class, 'updateTask'])->name('tasks.update');
        Route::delete('/tasks/{task}', [ManagerController::class, 'deleteTask'])->name('tasks.destroy');
    });

    // -------------------------------------------------------------
    // 3. Employee Interface & Rights (Personal Assigned Tasks Only)
    // -------------------------------------------------------------
    Route::middleware('role:employee')->prefix('employee')->name('employee.')->group(function () {
        Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('dashboard');
        Route::patch('/tasks/{task}/status', [EmployeeController::class, 'updateStatus'])->name('tasks.updateStatus');
    });
});
