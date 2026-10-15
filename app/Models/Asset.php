<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use SoftDeletes;

    protected $table = 'assets';

    protected $fillable = [
        'asset_code',
        'category_id',
        'location_id',
        'vendor_id',
        'brand',
        'model',
        'serial_number',
        'status',
        'condition',
        'purchase_date',
        'rental_start_date',
        'rental_end_date',
        'rental_cost',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'rental_start_date' => 'date',
        'rental_end_date' => 'date',
        'rental_cost' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Category
    |--------------------------------------------------------------------------
    */

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            AssetCategory::class,
            'category_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Location
    |--------------------------------------------------------------------------
    */

    public function location(): BelongsTo
    {
        return $this->belongsTo(
            Location::class,
            'location_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Vendor
    |--------------------------------------------------------------------------
    */

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(
            Vendor::class,
            'vendor_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Assignment
    |--------------------------------------------------------------------------
    */

    public function assignments(): HasMany
    {
        return $this->hasMany(
            AssetAssignment::class,
            'asset_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | History
    |--------------------------------------------------------------------------
    */

    public function histories(): HasMany
    {
        return $this->hasMany(
            AssetHistory::class,
            'asset_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Maintenance
    |--------------------------------------------------------------------------
    */

    public function maintenances(): HasMany
    {
        return $this->hasMany(
            AssetMaintenance::class,
            'asset_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Rental Days Remaining
    |--------------------------------------------------------------------------
    */

    public function getRentalDaysRemainingAttribute(): ?int
    {
        if (!$this->rental_end_date) {
            return null;
        }

        return now()
            ->startOfDay()
            ->diffInDays(
                $this->rental_end_date,
                false
            );
    }
}