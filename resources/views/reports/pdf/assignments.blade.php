<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        {{ $type === 'active'
            ? 'Assignment Aktif Report'
            : 'Return History Report' }}
    </title>

    <style>

        @page {
            margin: 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #1e293b;
        }

        .header {
            margin-bottom: 15px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .subtitle {
            color: #64748b;
            font-size: 9px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #e2e8f0;
            border: 1px solid #cbd5e1;
            padding: 6px;
            text-align: left;
            font-size: 8px;
        }

        td {
            border: 1px solid #cbd5e1;
            padding: 5px;
            vertical-align: top;
        }

    </style>

</head>

<body>

<div class="header">

    <div class="title">

        {{ $type === 'active'
            ? 'ASSIGNMENT AKTIF REPORT'
            : 'RETURN HISTORY REPORT' }}

    </div>

    <div class="subtitle">

        Generated:
        {{ now()->format('d M Y H:i') }}

    </div>

</div>


<table>

    <thead>

        <tr>

            <th>No</th>

            <th>Asset Code</th>

            <th>Category</th>

            <th>Brand / Model</th>

            <th>Serial Number</th>

            <th>User</th>

            <th>Location</th>

            <th>Assigned</th>

            @if ($type === 'returned')

                <th>Returned</th>

            @endif

            <th>Durasi</th>

            <th>Catatan</th>

        </tr>

    </thead>


    <tbody>

        @forelse ($assignments as $index => $assignment)

            @php

                $assignedDate = $assignment->assigned_at;

                $duration = $assignedDate
                    ? (
                        $type === 'active'
                            ? $assignedDate->diffInDays(now())
                            : ($assignment->returned_at
                                ? $assignedDate->diffInDays($assignment->returned_at)
                                : 0)
                    )
                    : 0;

            @endphp

            <tr>

                <td>
                    {{ $index + 1 }}
                </td>

                <td>
                    {{ $assignment->asset?->asset_code ?? '-' }}
                </td>

                <td>
                    {{ $assignment->asset?->category?->name ?? '-' }}
                </td>

                <td>
                    {{ $assignment->asset?->brand ?? '-' }}
                    {{ $assignment->asset?->model ?? '-' }}
                </td>

                <td>
                    {{ $assignment->asset?->serial_number ?? '-' }}
                </td>

                <td>
                    {{ $assignment->user?->name ?? '-' }}
                </td>

                <td>
                    {{ $assignment->location?->name
                        ?? $assignment->asset?->location?->name
                        ?? '-' }}
                </td>

                <td>
                    {{ $assignment->assigned_at?->format('d M Y') ?? '-' }}
                </td>

                @if ($type === 'returned')

                    <td>
                        {{ $assignment->returned_at?->format('d M Y') ?? '-' }}
                    </td>

                @endif

                <td>
                    {{ $duration }} hari
                </td>

                <td>
                    {{ $assignment->notes ?? '-' }}
                </td>

            </tr>

        @empty

            <tr>

                <td
                    colspan="{{ $type === 'returned' ? 11 : 10 }}">

                    Tidak ada data.

                </td>

            </tr>

        @endforelse

    </tbody>

</table>

</body>

</html>