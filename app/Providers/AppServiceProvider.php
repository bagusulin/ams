<?php

namespace App\Providers;
use App\Models\Asset;
use Illuminate\Support\Facades\View;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use App\Support\Assets\AssetAccess;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.admin', function ($view) {
            $user = Auth::user();

            if (! $user) {
                $view->with([
                    'expiredRentalNotificationCount' => 0,
                    'upcomingRentalNotificationCount' => 0,
                    'rentalExpiryNotificationCount' => 0,
                    'expiredRentalNotifications' => collect(),
                    'upcomingRentalNotifications' => collect(),
                ]);

                return;
            }

            $today = today();
            $expiryLimit = today()->addDays(60)->endOfDay();

            $scopedAssets = fn () => AssetAccess::assetsQuery(
                $user,
                Asset::query()
            );

            $expiredRentalNotificationCount = $scopedAssets()
                ->whereNotNull('rental_end_date')
                ->whereDate('rental_end_date', '<', $today)
                ->count();

            $upcomingRentalNotificationCount = $scopedAssets()
                ->whereNotNull('rental_end_date')
                ->whereDate('rental_end_date', '>=', $today)
                ->whereDate('rental_end_date', '<=', $expiryLimit)
                ->count();

            $expiredRentalNotifications = $scopedAssets()
                ->with('category')
                ->whereNotNull('rental_end_date')
                ->whereDate('rental_end_date', '<', $today)
                ->orderByDesc('rental_end_date')
                ->take(5)
                ->get();

            $upcomingRentalNotifications = $scopedAssets()
                ->with('category')
                ->whereNotNull('rental_end_date')
                ->whereDate('rental_end_date', '>=', $today)
                ->whereDate('rental_end_date', '<=', $expiryLimit)
                ->orderBy('rental_end_date')
                ->take(5)
                ->get();

            $view->with([
                'expiredRentalNotificationCount' =>
                    $expiredRentalNotificationCount,

                'upcomingRentalNotificationCount' =>
                    $upcomingRentalNotificationCount,

                'rentalExpiryNotificationCount' =>
                    $expiredRentalNotificationCount
                    + $upcomingRentalNotificationCount,

                'expiredRentalNotifications' =>
                    $expiredRentalNotifications,

                'upcomingRentalNotifications' =>
                    $upcomingRentalNotifications,
            ]);
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
