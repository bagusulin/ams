<?php
namespace App\Exports;

use App\Models\Asset;
use App\Support\Reports\RentalExpiryQuery;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RentalExpiryExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
{
    protected array $filters;

    protected int $rowNumber = 0;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection(): Collection
    {
        return RentalExpiryQuery::build($this->filters)
            ->orderBy('rental_end_date')
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
            'Asset Status',
            'Rental Start',
            'Rental End',
            'Days Remaining',
            'Expiry Status',
            'Rental Cost',
        ];
    }

    public function map($asset): array
    {
        /** @var Asset $asset */
        $this->rowNumber++;

        $daysRemaining = (int) today()->diffInDays(
            $asset->rental_end_date,
            false
        );

        return [
            $this->rowNumber,
            $asset->asset_code,
            $asset->category?->name ?? '-',
            $asset->brand ?? '-',
            $asset->model ?? '-',
            $asset->serial_number ?? '-',
            $asset->location?->name ?? '-',
            $asset->vendor?->name ?? '-',
            ucfirst($asset->status),
            $asset->rental_start_date?->format('Y-m-d') ?? '-',
            $asset->rental_end_date?->format('Y-m-d') ?? '-',
            $daysRemaining,
            RentalExpiryQuery::bucketLabel(
                $asset->rental_end_date
            ),
            $asset->rental_cost,
        ];
    }
}
