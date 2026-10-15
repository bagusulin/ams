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
    ];

    protected $casts = [
        'is_active' => 'boolean',
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