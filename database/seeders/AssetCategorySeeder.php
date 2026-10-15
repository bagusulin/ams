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
        ]);

        AssetCategory::create([
            'name' => 'Handphone',
            'code' => 'HPN',
        ]);

        AssetCategory::create([
            'name' => 'Sepeda Motor',
            'code' => 'MTR',
        ]);

        AssetCategory::create([
            'name' => 'Mobil',
            'code' => 'MBL',
        ]);

        AssetCategory::create([
            'name' => 'Printer',
            'code' => 'PRN',
        ]);

        AssetCategory::create([
            'name' => 'Scanner',
            'code' => 'SCN',
        ]);

        AssetCategory::create([
            'name' => 'CCTV',
            'code' => 'CTV',
        ]);
    }
}