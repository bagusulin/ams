<?php

namespace App\Support\Reports;
use Carbon\CarbonInterface;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Builder;
use App\Support\Assets\AssetAccess;
use Illuminate\Support\Facades\Auth;

class RentalExpiryQuery
{
    public static function build(
        array $filters = [],
        bool $applyBucket = true
    ): Builder {
        $user = Auth::user();
        abort_unless($user, 403);

        $query = AssetAccess::assetsQuery(
            $user,
            Asset::query()->with([
                'category',
                'location',
                'vendor',
            ])
        )->whereNotNull('rental_end_date');

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['search'])) {
            $search = '%' . trim($filters['search']) . '%';

            $query->where(function ($q) use ($search) {
                $q->where('asset_code', 'like', $search)
                    ->orWhere('brand', 'like', $search)
                    ->orWhere('model', 'like', $search)
                    ->orWhere('serial_number', 'like', $search);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Category
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['category_id'])) {
            $query->where(
                'category_id',
                $filters['category_id']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Location
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['location_id'])) {
            $query->where(
                'location_id',
                $filters['location_id']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Asset Status
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['asset_status'])) {
            $query->where(
                'status',
                $filters['asset_status']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Rental End Date Range
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['date_from'])) {
            $query->whereDate(
                'rental_end_date',
                '>=',
                $filters['date_from']
            );
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate(
                'rental_end_date',
                '<=',
                $filters['date_to']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Expiry Bucket
        |--------------------------------------------------------------------------
        |
        | expired : Sebelum hari ini
        | due_7   : Hari ini sampai dengan 7 hari ke depan
        | due_8_30: Lebih dari 7 sampai dengan 30 hari
        | over_30 : Lebih dari 30 hari
        |
        */

        if ($applyBucket) {
            $bucket = $filters['bucket'] ?? 'all';

            $today = today();
            $day7 = $today->copy()->addDays(7);
            $day30 = $today->copy()->addDays(30);

            switch ($bucket) {
                case 'expired':
                    $query->whereDate(
                        'rental_end_date',
                        '<',
                        $today
                    );
                    break;

                case 'due_7':
                    $query->whereDate(
                        'rental_end_date',
                        '>=',
                        $today
                    )->whereDate(
                        'rental_end_date',
                        '<=',
                        $day7
                    );
                    break;

                case 'due_8_30':
                    $query->whereDate(
                        'rental_end_date',
                        '>',
                        $day7
                    )->whereDate(
                        'rental_end_date',
                        '<=',
                        $day30
                    );
                    break;

                case 'over_30':
                    $query->whereDate(
                        'rental_end_date',
                        '>',
                        $day30
                    );
                    break;
            }
        }

        return $query;
    }

    public static function bucketLabel(?CarbonInterface $rentalEndDate): string
    {
        if (!$rentalEndDate) {
            return 'Tidak ada tanggal sewa';
        }

        $daysRemaining = (int) today()->diffInDays(
            $rentalEndDate,
            false
        );

        if ($daysRemaining < 0) {
            return 'Expired';
        }

        if ($daysRemaining <= 7) {
            return 'Jatuh tempo ≤7 hari';
        }

        if ($daysRemaining <= 30) {
            return 'Jatuh tempo 8–30 hari';
        }

        return 'Lebih dari 30 hari';
    }
}