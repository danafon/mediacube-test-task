<?php

use App\Http\Controllers\RevokeTokenController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/sanctum/token', TokenController::class);
Route::post('/users', [UserController::class, 'store']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/sanctum/token/revoke', RevokeTokenController::class);

    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index');
        Route::get('/users/{user}', 'show');
        Route::patch('/users/{user}', 'update');
        Route::delete('/users/{user}', 'destroy');
    });

    Route::controller(RoleController::class)->group(function () {
        Route::post('/roles', 'store');
        Route::get('/roles', 'index');
        Route::get('/roles/{role}', 'show');
        Route::patch('/roles/{role}', 'update');
        Route::delete('/roles/{role}', 'destroy');
    });
});
