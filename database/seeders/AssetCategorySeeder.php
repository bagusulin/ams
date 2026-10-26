<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use Illuminate\Database\Seeder;

class AssetCategorySeeder extends Seeder
{
    public function run(): void
    {
        AssetCategory::create([
            'name' => 'Laptop',
            'code' => 'LPT',
            'default_rental_years' => 3,
        ]);

        AssetCategory::create([
            'name' => 'Handphone',
            'code' => 'HPN',
            'default_rental_years' => 2,
        ]);

        AssetCategory::create([
            'name' => 'Sepeda Motor',
            'code' => 'MTR',
            'default_rental_years' => 3,
        ]);

        AssetCategory::create([
            'name' => 'Mobil',
            'code' => 'MBL',
            'default_rental_years' => 4,
        ]);

        AssetCategory::create([
            'name' => 'Printer',
            'code' => 'PRN',
            'default_rental_years' => 2,
        ]);

        AssetCategory::create([
            'name' => 'Scanner',
            'code' => 'SCN',
            'default_rental_years' => 3,
        ]);

        AssetCategory::create([
            'name' => 'CCTV',
            'code' => 'CTV',
            'default_rental_years' => 2,
        ]);
    }
}