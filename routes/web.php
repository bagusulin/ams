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
    ->middleware(['auth', 'can:assets.view'])
    ->name('dashboard');

Route::view('/asset-categories', 'asset-categories.index')
    ->middleware(['auth', 'can:asset-categories.view'])
    ->name('asset-categories.index');

Route::view('/locations', 'locations.index')
    ->middleware(['auth', 'can:locations.view'])
    ->name('locations.index');

Route::view('/vendors', 'vendors.index')
    ->middleware(['auth', 'can:vendors.view'])
    ->name('vendors.index');

Route::view('/assets', 'assets.index')
    ->middleware(['auth', 'can:assets.view'])
    ->name('assets.index');

Route::view('/assignments', 'assignments.index')
    ->middleware(['auth', 'can:assignments.view'])
    ->name('assignments.index');

Route::view('/returns', 'returns.index')
    ->middleware(['auth', 'can:returns.view'])
    ->name('returns.index');

Route::view('/asset-history', 'asset-histories.index')
    ->middleware(['auth', 'can:asset-history.view'])
    ->name('asset-history.index');

Route::view('/maintenances', 'maintenances.index')
    ->middleware(['auth', 'can:maintenances.view'])
    ->name('maintenances.index');

Route::middleware(['auth'])->group(function () {

    Route::get('/reports/assets', [ReportController::class, 'assets'])
        ->middleware('can:reports.assets.view')
        ->name('reports.assets');

    Route::get('/reports/assets/excel', [ReportController::class, 'exportAssetsExcel'])
        ->middleware('can:reports.assets.export')
        ->name('reports.assets.excel');

    Route::get('/reports/assets/pdf', [ReportController::class, 'exportAssetsPdf'])
        ->middleware('can:reports.assets.export')
        ->name('reports.assets.pdf');

    Route::get('/reports/assignments', [ReportController::class,'assignments'])
        ->middleware('can:reports.assignments.export')
        ->name('reports.assignments');

    Route::get('/reports/assignments/excel', [ReportController::class, 'exportAssignmentsExcel'])
        ->middleware('can:reports.assignments.export')
        ->name('reports.assignments.excel');

    Route::get('/reports/assignments/pdf', [ReportController::class, 'exportAssignmentsPdf'])
        ->middleware('can:reports.assignments.export')
        ->name('reports.assignments.pdf');

    Route::get('/reports/maintenances', [ReportController::class, 'maintenances'])
        ->middleware('can:reports.maintenances.export')
        ->name('reports.maintenances');

    Route::get('/reports/maintenances/excel', [ReportController::class, 'exportMaintenancesExcel'])
        ->middleware('can:reports.maintenances.export')
        ->name('reports.maintenances.excel');

    Route::get('/reports/maintenances/pdf', [ReportController::class, 'exportMaintenancesPdf'])
        ->middleware('can:reports.maintenances.export')
        ->name('reports.maintenances.pdf');

    Route::get('/reports/rental-expiry', [ReportController::class, 'rentalExpiry',])
        ->middleware('can:reports.rental-expiry.view')
        ->name('reports.rental-expiry');

    Route::get('/reports/rental-expiry/excel', [ReportController::class,'exportRentalExpiryExcel',])
        ->middleware('can:reports.rental-expiry.export')
        ->name('reports.rental-expiry.excel');

    Route::get('/reports/rental-expiry/pdf', [
        ReportController::class,'exportRentalExpiryPdf',])
        ->middleware('can:reports.rental-expiry.export')
        ->name('reports.rental-expiry.pdf');

    Route::get('/asset-qr', [
        AssetQrController::class,'index',])
        ->middleware(['auth', 'can:asset-qr.view'])
        ->name('asset-qr.index');

    Route::get('/assets/{asset}/detail', [
        AssetQrController::class,'detail',])
        ->middleware(['auth', 'can:asset-qr.view'])
        ->name('asset-qr.detail');

    Route::get('/assets/{asset}/qr.svg', [
        AssetQrController::class,'image',])
        ->middleware(['auth', 'can:asset-qr.view'])
        ->name('asset-qr.image');

    Route::get('/assets/{asset}/qr/print', [
        AssetQrController::class,'printLabel',])
        ->middleware(['auth', 'can:asset-qr.view'])
        ->name('asset-qr.print');
});


require __DIR__.'/settings.php';