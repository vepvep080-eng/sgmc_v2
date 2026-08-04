<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourrierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoutingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'splash');

Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::resource('courriers', CourrierController::class);

    Route::post('/courriers/{courrier}/archive', [CourrierController::class, 'archive'])
        ->name('courriers.archive');

    Route::post('/courriers/{courrier}/routings', [RoutingController::class, 'store'])
        ->name('courriers.routings.store');

    Route::post('/routings/{routing}/accept', [RoutingController::class, 'accept'])
        ->name('routings.accept');

    Route::post('/routings/{routing}/reject', [RoutingController::class, 'reject'])
        ->name('routings.reject');

    Route::post('/routings/{routing}/process', [RoutingController::class, 'process'])
        ->name('routings.process');

    Route::middleware('permission:manage_users')->group(function () {
        Route::resource('users', UserController::class);
    });
});
