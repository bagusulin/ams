<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Asset Report</title>

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

        .summary {
            margin-bottom: 15px;
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

        .small {
            color: #64748b;
            font-size: 7px;
        }

    </style>

</head>

<body>

<div class="header">

    <div class="title">
        ASSET MANAGEMENT REPORT
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
            <th>Vendor</th>
            <th>Status</th>
            <th>Condition</th>
            <th>Rental End</th>

        </tr>

    </thead>


    <tbody>

        @forelse ($assets as $index => $asset)

            <tr>

                <td>
                    {{ $index + 1 }}
                </td>

                <td>
                    {{ $asset->asset_code }}
                </td>

                <td>
                    {{ $asset->category?->name ?? '-' }}
                </td>

                <td>
                    {{ $asset->brand ?? '-' }}
                    {{ $asset->model ?? '-' }}
                </td>

                <td>
                    {{ $asset->serial_number ?? '-' }}
                </td>

                <td>
                    {{ $asset->location?->name ?? '-' }}
                </td>

                <td>
                    {{ $asset->vendor?->name ?? '-' }}
                </td>

                <td>
                    {{ ucfirst($asset->status) }}
                </td>

                <td>
                    {{ ucfirst($asset->condition) }}
                </td>

                <td>
                    {{ $asset->rental_end_date?->format('d M Y') ?? '-' }}
                </td>

            </tr>

        @empty

            <tr>

                <td colspan="10">
                    Tidak ada data asset.
                </td>

            </tr>

        @endforelse

    </tbody>

</table>

</body>

</html>