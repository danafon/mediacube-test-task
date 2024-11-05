<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LogoutController::class, 'logout']);
