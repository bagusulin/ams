<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AssetQrController;


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

Route::view('/assignments', 'assignments.index')
    ->middleware(['auth'])
    ->name('assignments.index');

Route::view('/returns', 'returns.index')
    ->middleware(['auth'])
    ->name('returns.index');

Route::view('/asset-history', 'asset-histories.index')
    ->middleware(['auth'])
    ->name('asset-history.index');

Route::view('/maintenances', 'maintenances.index')
    ->middleware(['auth'])
    ->name('maintenances.index');

Route::middleware(['auth'])->group(function () {

    Route::get('/reports/assets', [ReportController::class, 'assets'])
        ->name('reports.assets');

    Route::get('/reports/assets/excel', [ReportController::class, 'exportAssetsExcel'])
        ->name('reports.assets.excel');

    Route::get('/reports/assets/pdf', [ReportController::class, 'exportAssetsPdf'])
        ->name('reports.assets.pdf');

    Route::get('/reports/assignments', [ReportController::class,'assignments'])
        ->name('reports.assignments');

    Route::get('/reports/assignments/excel', [ReportController::class, 'exportAssignmentsExcel'])
        ->name('reports.assignments.excel');

    Route::get('/reports/assignments/pdf', [ReportController::class, 'exportAssignmentsPdf'])
        ->name('reports.assignments.pdf');

    Route::get('/reports/maintenances', [ReportController::class, 'maintenances'])
        ->name('reports.maintenances');

    Route::get('/reports/maintenances/excel', [ReportController::class, 'exportMaintenancesExcel'])
        ->name('reports.maintenances.excel');

    Route::get('/reports/maintenances/pdf', [ReportController::class, 'exportMaintenancesPdf'])
        ->name('reports.maintenances.pdf');

    Route::get('/reports/rental-expiry', [ReportController::class, 'rentalExpiry',])
        ->name('reports.rental-expiry');

    Route::get('/reports/rental-expiry/excel', [ReportController::class,'exportRentalExpiryExcel',])
        ->name('reports.rental-expiry.excel');

    Route::get('/reports/rental-expiry/pdf', [
        ReportController::class,'exportRentalExpiryPdf',])
        ->name('reports.rental-expiry.pdf');

    Route::get('/asset-qr', [
        AssetQrController::class,'index',])
        ->middleware(['auth'])
        ->name('asset-qr.index');

    Route::get('/assets/{asset}/detail', [
        AssetQrController::class,'detail',])
        ->middleware(['auth'])
        ->name('asset-qr.detail');

    Route::get('/assets/{asset}/qr.svg', [
        AssetQrController::class,'image',])
        ->middleware(['auth'])
        ->name('asset-qr.image');

    Route::get('/assets/{asset}/qr/print', [
        AssetQrController::class,'printLabel',])
        ->middleware(['auth'])
        ->name('asset-qr.print');
});


require __DIR__.'/settings.php';