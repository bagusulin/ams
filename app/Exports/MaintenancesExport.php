<?php

namespace App\Exports;

use App\Models\AssetMaintenance;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Support\Assets\AssetAccess;
use Illuminate\Support\Facades\Auth;

class MaintenancesExport implements
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
        $query = AssetMaintenance::query()
            ->with([
                'asset.category',
                'asset.location',
                'asset.vendor',
            ]);

        $query->whereHas('asset', function ($assetQuery) use ($user) {
            AssetAccess::assetsQuery($user, $assetQuery);
        });

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

                $q->where(
                    'type',
                    'like',
                    $search
                )

                ->orWhere(
                    'description',
                    'like',
                    $search
                )

                ->orWhere(
                    'action_taken',
                    'like',
                    $search
                )

                ->orWhereHas(
                    'asset',
                    function ($assetQuery) use ($search) {

                        $assetQuery
                            ->where(
                                'asset_code',
                                'like',
                                $search
                            )

                            ->orWhere(
                                'brand',
                                'like',
                                $search
                            )

                            ->orWhere(
                                'model',
                                'like',
                                $search
                            )

                            ->orWhere(
                                'serial_number',
                                'like',
                                $search
                            );
                    }
                );
            });
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
        | Type
        |--------------------------------------------------------------------------
        */

        if (!empty($this->filters['type'])) {
            $query->where(
                'type',
                $this->filters['type']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Category
        |--------------------------------------------------------------------------
        */

        if (!empty($this->filters['category_id'])) {

            $query->whereHas(
                'asset',
                function ($assetQuery) {

                    $assetQuery->where(
                        'category_id',
                        $this->filters['category_id']
                    );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Location
        |--------------------------------------------------------------------------
        */

        if (!empty($this->filters['location_id'])) {

            $query->whereHas(
                'asset',
                function ($assetQuery) {

                    $assetQuery->where(
                        'location_id',
                        $this->filters['location_id']
                    );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date
        |--------------------------------------------------------------------------
        */

        if (!empty($this->filters['date_from'])) {

            $query->whereDate(
                'maintenance_date',
                '>=',
                $this->filters['date_from']
            );
        }

        if (!empty($this->filters['date_to'])) {

            $query->whereDate(
                'maintenance_date',
                '<=',
                $this->filters['date_to']
            );
        }

        return $query
            ->latest('maintenance_date')
            ->latest('id')
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
            'Maintenance Date',
            'Type',
            'Status',
            'Cost',
            'Description',
            'Action Taken',
        ];
    }

    public function map($maintenance): array
    {
        static $number = 0;

        $number++;

        return [
            $number,

            $maintenance->asset?->asset_code
                ?? '-',

            $maintenance->asset?->category?->name
                ?? '-',

            $maintenance->asset?->brand
                ?? '-',

            $maintenance->asset?->model
                ?? '-',

            $maintenance->asset?->serial_number
                ?? '-',

            $maintenance->asset?->location?->name
                ?? '-',

            $maintenance->asset?->vendor?->name
                ?? '-',

            $maintenance->maintenance_date?->format(
                'Y-m-d'
            ),

            $maintenance->type,

            ucfirst(
                str_replace(
                    '_',
                    ' ',
                    $maintenance->status
                )
            ),

            $maintenance->cost,

            $maintenance->description
                ?? '-',

            $maintenance->action_taken
                ?? '-',
        ];
    }
}