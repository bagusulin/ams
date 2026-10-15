<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::view('/asset-categories', 'asset-categories.index')
    ->middleware(['auth'])
    ->name('asset-categories.index');

Route::view('/locations', 'locations.index')
    ->middleware(['auth'])
    ->name('locations.index');

Route::view('/vendors', 'vendors.index')
    ->middleware(['auth'])
    ->name('vendors.index');

Route::view('/assets', 'assets.index')
    ->middleware(['auth'])
    ->name('assets.index');

require __DIR__.'/settings.php';