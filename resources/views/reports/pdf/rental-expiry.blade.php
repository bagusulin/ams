<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>Rental Expiry Report</title>

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
            margin-bottom: 16px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .subtitle {
            color: #64748b;
            font-size: 9px;
            margin-bottom: 3px;
        }

        .summary {
            margin-top: 10px;
            margin-bottom: 12px;
            font-size: 9px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
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

        .footer {
            margin-top: 12px;
            color: #64748b;
            font-size: 8px;
        }

        .expired {
            font-weight: bold;
            color: #be123c;
        }

        .due-soon {
            font-weight: bold;
            color: #b45309;
        }

        .normal {
            color: #047857;
        }
    </style>
</head>

<body>

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="header">

        <div class="title">
            ASSET MANAGEMENT SYSTEM
        </div>

        <div class="title">
            RENTAL EXPIRY REPORT
        </div>

        <div class="subtitle">
            Tanggal laporan:
            {{ now()->format('d M Y H:i') }}
        </div>

        <div class="subtitle">
            Kategori masa sewa:

            @switch($bucket)

                @case('expired')
                    Expired
                    @break

                @case('due_7')
                    Jatuh tempo ≤7 hari
                    @break

                @case('due_8_60')
                    Jatuh tempo 8–60 hari
                    @break

                @case('over_60')
                    Lebih dari 60 hari
                    @break

                @default
                    Semua masa sewa

            @endswitch

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- TABLE --}}
    {{-- ========================================================= --}}

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
                <th>Rental Start</th>
                <th>Rental End</th>
                <th>Sisa Hari</th>
                <th>Status Sewa</th>
                <th>Status Asset</th>
                <th>Rental Cost</th>
            </tr>

        </thead>

        <tbody>

            @forelse ($assets as $index => $asset)

                @php
                    $daysRemaining = (int) today()->diffInDays(
                        $asset->rental_end_date,
                        false
                    );

                    $expiryLabel = \App\Support\Reports\RentalExpiryQuery::bucketLabel(
                        $asset->rental_end_date
                    );
                @endphp

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
                        {{ $asset->model ?? '' }}
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
                        {{ $asset->rental_start_date?->format('d M Y') ?? '-' }}
                    </td>

                    <td>
                        {{ $asset->rental_end_date?->format('d M Y') ?? '-' }}
                    </td>

                    <td>
                        @if ($daysRemaining < 0)

                            <span class="expired">
                                Lewat {{ abs($daysRemaining) }} hari
                            </span>

                        @elseif ($daysRemaining === 0)

                            <span class="due-soon">
                                Hari ini
                            </span>

                        @else

                            {{ $daysRemaining }} hari

                        @endif
                    </td>

                    <td>

                        @if ($daysRemaining < 0)

                            <span class="expired">
                                {{ $expiryLabel }}
                            </span>

                        @elseif ($daysRemaining <= 7)

                            <span class="due-soon">
                                {{ $expiryLabel }}
                            </span>

                        @elseif ($daysRemaining <= 60)

                            <span class="due-soon">
                                {{ $expiryLabel }}
                            </span>

                        @else

                            <span class="normal">
                                {{ $expiryLabel }}
                            </span>

                        @endif

                    </td>

                    <td>
                        {{ ucfirst($asset->status) }}
                    </td>

                    <td>
                        Rp {{ number_format((float) $asset->rental_cost, 0, ',', '.') }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="13">
                        Tidak ada data rental expiry yang sesuai dengan filter.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>


    {{-- ========================================================= --}}
    {{-- FOOTER --}}
    {{-- ========================================================= --}}

    <div class="footer">
        Jumlah asset dalam laporan ini: {{ $assets->count() }}
    </div>

</body>

</html>