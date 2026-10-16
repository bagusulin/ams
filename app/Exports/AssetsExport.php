<?php

namespace App\Exports;

use App\Models\Asset;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Support\Assets\AssetAccess;
use Illuminate\Support\Facades\Auth;

class AssetsExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection(): Collection
    {
        $user = Auth::user();
        abort_unless($user, 403);
        $query = AssetAccess::assetsQuery(
            $user,
            Asset::query()->with([
                'category',
                'location',
                'vendor',
            ])
        );

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */
        if (!empty($this->filters['search'])) {
            $search =
                '%' .
                trim($this->filters['search']) .
                '%';

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
        if (!empty($this->filters['category_id'])) {
            $query->where(
                'category_id',
                $this->filters['category_id']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Location
        |--------------------------------------------------------------------------
        */
        if (!empty($this->filters['location_id'])) {
            $query->where(
                'location_id',
                $this->filters['location_id']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */
        if (!empty($this->filters['status'])) {
            $query->where(
                'status',
                $this->filters['status']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Condition
        |--------------------------------------------------------------------------
        */
        if (!empty($this->filters['condition'])) {
            $query->where(
                'condition',
                $this->filters['condition']
            );
        }

        return $query
            ->orderBy('asset_code')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Asset Code',
            'Category',
            'Brand',
            'Model',
            'Serial Number',
            'Location',
            'Vendor',
            'Status',
            'Condition',
            'Purchase Date',
            'Rental Start',
            'Rental End',
            'Rental Cost',
            'Notes',
        ];
    }

    public function map($asset): array
    {
        static $number = 0;

        $number++;

        return [
            $number,

            $asset->asset_code,

            $asset->category?->name ?? '-',

            $asset->brand ?? '-',

            $asset->model ?? '-',

            $asset->serial_number ?? '-',

            $asset->location?->name ?? '-',

            $asset->vendor?->name ?? '-',

            ucfirst($asset->status),

            ucfirst($asset->condition),

            $asset->purchase_date?->format('Y-m-d'),

            $asset->rental_start_date?->format('Y-m-d'),

            $asset->rental_end_date?->format('Y-m-d'),

            $asset->rental_cost,

            $asset->notes ?? '-',
        ];
    }
}