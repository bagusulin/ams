<?php

namespace App\Providers;
use App\Models\Asset;
use Illuminate\Support\Facades\View;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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

            $today = today();
            $expiryLimit = $today->copy()->addDays(60);

            /*
            |--------------------------------------------------------------------------
            | Count Expired Rentals
            |--------------------------------------------------------------------------
            */

            $expiredRentalNotificationCount = Asset::query()
                ->whereNotNull('rental_end_date')
                ->whereDate('rental_end_date', '<', $today)
                ->count();

            /*
            |--------------------------------------------------------------------------
            | Count Upcoming Rentals: 60 Days
            |--------------------------------------------------------------------------
            */

            $upcomingRentalNotificationCount = Asset::query()
                ->whereNotNull('rental_end_date')
                ->whereDate('rental_end_date', '>=', $today)
                ->whereDate('rental_end_date', '<=', $expiryLimit)
                ->count();

            /*
            |--------------------------------------------------------------------------
            | Five Latest Expired Rentals
            |--------------------------------------------------------------------------
            */

            $expiredRentalNotifications = Asset::query()
                ->with('category')
                ->whereNotNull('rental_end_date')
                ->whereDate('rental_end_date', '<', $today)
                ->orderByDesc('rental_end_date')
                ->take(5)
                ->get();

            /*
            |--------------------------------------------------------------------------
            | Five Nearest Expiry Dates
            |--------------------------------------------------------------------------
            */

            $upcomingRentalNotifications = Asset::query()
                ->with('category')
                ->whereNotNull('rental_end_date')
                ->whereDate('rental_end_date', '>=', $today)
                ->whereDate('rental_end_date', '<=', $expiryLimit)
                ->orderBy('rental_end_date')
                ->take(5)
                ->get();

            /*
            |--------------------------------------------------------------------------
            | Share Data With Admin Layout
            |--------------------------------------------------------------------------
            */

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
