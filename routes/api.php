<?php

use App\Http\Controllers\RoleController;
use App\Http\Controllers\TokenController;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;

Route::post('/sanctum/token', TokenController::class);

Route::controller(RoleController::class)->group(function () {
    Route::post('/roles', 'store');
    Route::get('/roles', 'index');
    Route::get('/roles/{role}', 'index');
    Route::patch('/roles/{role}', 'update');
    Route::delete('/roles/{role}', 'destroy');
});

// Route::middleware(['auth:sanctum'])->group(function () {
// });
