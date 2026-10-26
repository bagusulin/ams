<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetCategory extends Model
{
    protected $table = 'asset_categories';

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'default_rental_years',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'default_rental_years' => 'integer',
    ];

    /**
     * Relasi ke asset.
     */
    public function assets(): HasMany
    {
        return $this->hasMany(
            Asset::class,
            'category_id'
        );
    }
}