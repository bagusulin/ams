<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        Maintenance Report
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
        MAINTENANCE REPORT
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

            <th>Location</th>

            <th>Tanggal</th>

            <th>Type</th>

            <th>Status</th>

            <th>Cost</th>

            <th>Action Taken</th>

        </tr>

    </thead>


    <tbody>

        @forelse ($maintenances as $index => $maintenance)

            <tr>

                <td>
                    {{ $index + 1 }}
                </td>

                <td>
                    {{ $maintenance->asset?->asset_code ?? '-' }}
                </td>

                <td>
                    {{ $maintenance->asset?->category?->name ?? '-' }}
                </td>

                <td>
                    {{ $maintenance->asset?->brand ?? '-' }}
                    {{ $maintenance->asset?->model ?? '-' }}
                </td>

                <td>
                    {{ $maintenance->asset?->serial_number ?? '-' }}
                </td>

                <td>
                    {{ $maintenance->asset?->location?->name ?? '-' }}
                </td>

                <td>
                    {{ $maintenance->maintenance_date?->format('d M Y') ?? '-' }}
                </td>

                <td>
                    {{ $maintenance->type }}
                </td>

                <td>
                    {{ ucfirst(str_replace('_', ' ', $maintenance->status)) }}
                </td>

                <td>
                    Rp {{ number_format((float) $maintenance->cost, 0, ',', '.') }}
                </td>

                <td>
                    {{ $maintenance->action_taken ?? '-' }}
                </td>

            </tr>

        @empty

            <tr>

                <td colspan="11">
                    Tidak ada data maintenance.
                </td>

            </tr>

        @endforelse

    </tbody>

</table>

</body>

</html>