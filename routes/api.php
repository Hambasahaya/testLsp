<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes - supports both sanctum tokens and session cookies
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth routes
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Product routes
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/stats/sales', [ProductController::class, 'salesStats']);

    // Admin only - create and update product
    Route::post('/products', [ProductController::class, 'store'])
        ->middleware('role:Admin');
    Route::put('/products/{product}', [ProductController::class, 'update'])
        ->middleware('role:Admin');
    Route::post('/products/{product}', [ProductController::class, 'update'])
        ->middleware('role:Admin');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])
        ->middleware('role:Admin');

    // Admin & Seller - sell product
    Route::post('/products/{product}/sell', [ProductController::class, 'sell'])
        ->middleware('roles:Admin,Seller');

    // User routes - Admin only
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('role:Admin');
    Route::put('/users/{user}/change-role', [UserController::class, 'changeRole'])
        ->middleware('role:Admin');
    Route::put('/users/{user}', [UserController::class, 'update'])
        ->middleware('role:Admin');
});
