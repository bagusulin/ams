<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetCategory;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        /*
        |--------------------------------------------------------------------------
        | Statistik Asset
        |--------------------------------------------------------------------------
        */

        $totalAssets = Asset::count();

        $availableAssets = Asset::where(
            'status',
            'available'
        )->count();

        $assignedAssets = Asset::where(
            'status',
            'assigned'
        )->count();

        $maintenanceAssets = Asset::where(
            'status',
            'maintenance'
        )->count();


        /*
        |--------------------------------------------------------------------------
        | Kategori Asset
        |--------------------------------------------------------------------------
        */

        $totalCategories = AssetCategory::where(
            'is_active',
            true
        )->count();


        /*
        |--------------------------------------------------------------------------
        | Asset yang akan habis masa sewa
        | 0 - 30 hari dari hari ini
        |--------------------------------------------------------------------------
        */

        $expiringAssets = Asset::with([
            'category',
            'location',
            'vendor',
        ])
            ->whereNotNull('rental_end_date')
            ->whereBetween(
                'rental_end_date',
                [
                    now()->startOfDay(),
                    now()->addDays(30)->endOfDay(),
                ]
            )
            ->orderBy('rental_end_date')
            ->take(10)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Jumlah asset yang masa sewanya <= 30 hari
        |--------------------------------------------------------------------------
        */

        $expiringAssetsCount = Asset::query()
            ->whereNotNull('rental_end_date')
            ->whereBetween(
                'rental_end_date',
                [
                    now()->startOfDay(),
                    now()->addDays(30)->endOfDay(),
                ]
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | Asset dengan masa sewa sudah habis
        |--------------------------------------------------------------------------
        */

        $expiredAssetsCount = Asset::query()
            ->whereNotNull('rental_end_date')
            ->where(
                'rental_end_date',
                '<',
                now()->startOfDay()
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | Asset terbaru
        |--------------------------------------------------------------------------
        */

        $recentAssets = Asset::with([
            'category',
            'location',
            'vendor',
        ])
            ->latest('created_at')
            ->take(10)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Ringkasan asset berdasarkan kategori
        |--------------------------------------------------------------------------
        */

        $categorySummary = AssetCategory::query()
            ->where('is_active', true)
            ->withCount('assets')
            ->orderByDesc('assets_count')
            ->take(6)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Kirim data ke View
        |--------------------------------------------------------------------------
        */

        return view('dashboard', [
            'totalAssets' => $totalAssets,
            'availableAssets' => $availableAssets,
            'assignedAssets' => $assignedAssets,
            'maintenanceAssets' => $maintenanceAssets,
            'totalCategories' => $totalCategories,

            'expiringAssets' => $expiringAssets,
            'expiringAssetsCount' => $expiringAssetsCount,
            'expiredAssetsCount' => $expiredAssetsCount,

            'recentAssets' => $recentAssets,

            'categorySummary' => $categorySummary,
        ]);
    }
}