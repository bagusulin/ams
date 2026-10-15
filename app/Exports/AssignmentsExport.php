<?php

namespace App\Exports;

use App\Models\AssetAssignment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssignmentsExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
{
    protected array $filters;

    protected string $type;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;

        $this->type = $filters['type'] ?? 'active';

        if (!in_array($this->type, ['active', 'returned'], true)) {
            $this->type = 'active';
        }
    }

    public function collection(): Collection
    {
        $query = AssetAssignment::query()
            ->with([
                'asset.category',
                'asset.location',
                'user',
                'location',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Active / Returned
        |--------------------------------------------------------------------------
        */

        if ($this->type === 'active') {

            $query->whereNull('returned_at');

        } else {

            $query->whereNotNull('returned_at');

        }

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

                $q->whereHas('asset', function ($assetQuery) use ($search) {

                    $assetQuery
                        ->where('asset_code', 'like', $search)
                        ->orWhere('brand', 'like', $search)
                        ->orWhere('model', 'like', $search)
                        ->orWhere('serial_number', 'like', $search);

                });

                $q->orWhereHas('user', function ($userQuery) use ($search) {

                    $userQuery->where('name', 'like', $search);

                });

            });
        }

        /*
        |--------------------------------------------------------------------------
        | User
        |--------------------------------------------------------------------------
        */

        if (!empty($this->filters['user_id'])) {

            $query->where(
                'user_id',
                $this->filters['user_id']
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
        | Date
        |--------------------------------------------------------------------------
        */

        if (!empty($this->filters['date_from'])) {

            $dateColumn =
                $this->type === 'active'
                    ? 'assigned_at'
                    : 'returned_at';

            $query->whereDate(
                $dateColumn,
                '>=',
                $this->filters['date_from']
            );
        }

        if (!empty($this->filters['date_to'])) {

            $dateColumn =
                $this->type === 'active'
                    ? 'assigned_at'
                    : 'returned_at';

            $query->whereDate(
                $dateColumn,
                '<=',
                $this->filters['date_to']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Sort
        |--------------------------------------------------------------------------
        */

        if ($this->type === 'active') {

            $query
                ->latest('assigned_at')
                ->latest('id');

        } else {

            $query
                ->latest('returned_at')
                ->latest('id');
        }

        return $query->get();
    }

    public function headings(): array
    {
        if ($this->type === 'active') {

            return [
                'No',
                'Asset Code',
                'Category',
                'Brand',
                'Model',
                'Serial Number',
                'User',
                'Location',
                'Assigned Date',
                'Duration (Days)',
                'Notes',
            ];

        }

        return [
            'No',
            'Asset Code',
            'Category',
            'Brand',
            'Model',
            'Serial Number',
            'User',
            'Location',
            'Assigned Date',
            'Returned Date',
            'Duration (Days)',
            'Notes',
        ];
    }

    public function map($assignment): array
    {
        static $number = 0;

        $number++;

        $assignedDate = $assignment->assigned_at;

        if ($this->type === 'active') {

            $duration = $assignedDate
                ? $assignedDate->diffInDays(now())
                : 0;

            return [
                $number,

                $assignment->asset?->asset_code ?? '-',

                $assignment->asset?->category?->name ?? '-',

                $assignment->asset?->brand ?? '-',

                $assignment->asset?->model ?? '-',

                $assignment->asset?->serial_number ?? '-',

                $assignment->user?->name ?? '-',

                $assignment->location?->name
                    ?? $assignment->asset?->location?->name
                    ?? '-',

                $assignedDate?->format('Y-m-d'),

                $duration,

                $assignment->notes ?? '-',
            ];
        }

        $duration =
            $assignedDate && $assignment->returned_at
                ? $assignedDate->diffInDays($assignment->returned_at)
                : 0;

        return [
            $number,

            $assignment->asset?->asset_code ?? '-',

            $assignment->asset?->category?->name ?? '-',

            $assignment->asset?->brand ?? '-',

            $assignment->asset?->model ?? '-',

            $assignment->asset?->serial_number ?? '-',

            $assignment->user?->name ?? '-',

            $assignment->location?->name
                ?? $assignment->asset?->location?->name
                ?? '-',

            $assignedDate?->format('Y-m-d'),

            $assignment->returned_at?->format('Y-m-d'),

            $duration,

            $assignment->notes ?? '-',
        ];
    }
}