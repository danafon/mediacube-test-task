<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

Route::get('/users', function () {
    Gate::authorize('viewAny', User::class);
    $users = User::with('roles')->paginate(10);
    $roles = Role::all();

    return view('users.index', compact('users', 'roles'));
})->middleware('auth');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
