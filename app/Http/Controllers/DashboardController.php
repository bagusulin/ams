<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Support\Assets\AssetAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        abort_unless($user, 403);

        Gate::authorize('dashboard.view');

        // Semua query aset dashboard mengikuti cakupan role.
        $assetsQuery = fn () => AssetAccess::assetsQuery(
            $user,
            Asset::query()
        );

        $today = today();
        $expiryLimit = today()->addDays(60)->endOfDay();

        $totalAssets = $assetsQuery()->count();

        $availableAssets = $assetsQuery()
            ->where('status', 'available')
            ->count();

        $assignedAssets = $assetsQuery()
            ->where('status', 'assigned')
            ->count();

        $maintenanceAssets = $assetsQuery()
            ->where('status', 'maintenance')
            ->count();

        $totalCategories = AssetAccess::categoriesQueryFor($user)
            ->where('is_active', true)
            ->count();

        $expiringAssets = $assetsQuery()
            ->with(['category', 'location', 'vendor'])
            ->whereNotNull('rental_end_date')
            ->whereBetween('rental_end_date', [
                $today,
                $expiryLimit,
            ])
            ->orderBy('rental_end_date')
            ->take(10)
            ->get();

        $expiringAssetsCount = $assetsQuery()
            ->whereNotNull('rental_end_date')
            ->whereBetween('rental_end_date', [
                $today,
                $expiryLimit,
            ])
            ->count();

        $expiredAssetsCount = $assetsQuery()
            ->whereNotNull('rental_end_date')
            ->whereDate('rental_end_date', '<', $today)
            ->count();

        $recentAssets = $assetsQuery()
            ->with(['category', 'location', 'vendor'])
            ->latest('created_at')
            ->take(10)
            ->get();

        $categorySummary = AssetAccess::categoriesQueryFor($user)
            ->where('is_active', true)
            ->withCount([
                'assets' => fn ($query) =>
                    AssetAccess::assetsQuery($user, $query),
            ])
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