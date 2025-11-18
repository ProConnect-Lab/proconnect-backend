<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminSearchController;
use App\Http\Controllers\Admin\AdminStatsController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    Route::get('/companies', [CompanyController::class, 'index']);
    Route::post('/companies', [CompanyController::class, 'store']);
    Route::put('/companies/{company}', [CompanyController::class, 'update']);
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy']);

    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);
});

Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout']);
        Route::get('/me', [AdminAuthController::class, 'profile']);

        Route::get('/admins', [AdminUserController::class, 'index']);
        Route::post('/admins', [AdminUserController::class, 'store']);

        Route::get('/users', [AdminSearchController::class, 'users']);
        Route::delete('/users/{user}', [AdminSearchController::class, 'deleteUser']);

        Route::get('/companies', [AdminSearchController::class, 'companies']);
        Route::get('/companies/all', [AdminSearchController::class, 'allCompanies']);
        Route::delete('/companies/{company}', [AdminSearchController::class, 'deleteCompany']);

        Route::get('/posts', [AdminSearchController::class, 'posts']);
        Route::post('/posts', [AdminSearchController::class, 'createPost']);
        Route::put('/posts/{post}', [AdminSearchController::class, 'updatePost']);
        Route::delete('/posts/{post}', [AdminSearchController::class, 'deletePost']);

        Route::get('/stats', AdminStatsController::class);
    });
});
