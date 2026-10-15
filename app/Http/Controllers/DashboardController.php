<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetCategory;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->startOfDay();
        $expiryLimit = now()->addDays(60)->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | Asset Summary
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

        $totalCategories = AssetCategory::where(
            'is_active',
            true
        )->count();

        /*
        |--------------------------------------------------------------------------
        | Rental Expiry: Hari Ini - 60 Hari ke Depan
        |--------------------------------------------------------------------------
        */

        $expiringAssets = Asset::with([
            'category',
            'location',
            'vendor',
        ])
            ->whereNotNull('rental_end_date')
            ->whereBetween('rental_end_date', [
                $today,
                $expiryLimit,
            ])
            ->orderBy('rental_end_date')
            ->take(10)
            ->get();

        $expiringAssetsCount = Asset::query()
            ->whereNotNull('rental_end_date')
            ->whereBetween('rental_end_date', [
                $today,
                $expiryLimit,
            ])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Expired Rental
        |--------------------------------------------------------------------------
        */

        $expiredAssetsCount = Asset::query()
            ->whereNotNull('rental_end_date')
            ->where('rental_end_date', '<', $today)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Recently Added Assets
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
        | Category Summary
        |--------------------------------------------------------------------------
        */

        $categorySummary = AssetCategory::where(
            'is_active',
            true
        )
            ->withCount('assets')
            ->orderByDesc('assets_count')
            ->take(6)
            ->get();

        return view('dashboard', compact(
            'totalAssets',
            'availableAssets',
            'assignedAssets',
            'maintenanceAssets',
            'totalCategories',
            'expiringAssets',
            'expiringAssetsCount',
            'expiredAssetsCount',
            'recentAssets',
            'categorySummary'
        ));
    }
}