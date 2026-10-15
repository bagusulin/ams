
<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Label QR - {{ $asset->asset_code }}</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 20px;
            background: #f1f5f9;
            color: #0f172a;
            font-family: Arial, Helvetica, sans-serif;
        }

        .toolbar {
            margin-bottom: 20px;
            text-align: center;
        }

        .button {
            display: inline-block;
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            background: #2563eb;
            color: white;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }

        .button.secondary {
            background: #475569;
        }

        .label {
            display: flex;
            align-items: center;
            gap: 5mm;
            width: 100mm;
            min-height: 64mm;
            margin: 0 auto;
            padding: 5mm;
            border: 1px solid #cbd5e1;
            border-radius: 3mm;
            background: white;
        }

        .qr {
            flex: 0 0 35mm;
            text-align: center;
        }

        .qr img {
            display: block;
            width: 35mm;
            height: 35mm;
            object-fit: contain;
        }

        .qr-code {
            margin-top: 2mm;
            font-size: 8pt;
            font-weight: bold;
            text-align: center;
            overflow-wrap: anywhere;
        }

        .info {
            min-width: 0;
            flex: 1;
        }

        .company {
            margin-bottom: 3mm;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #475569;
        }

        .asset-code {
            margin-bottom: 2mm;
            font-size: 13pt;
            font-weight: bold;
            overflow-wrap: anywhere;
        }

        .name {
            margin-bottom: 3mm;
            font-size: 9pt;
            font-weight: bold;
            overflow-wrap: anywhere;
        }

        .detail {
            margin-bottom: 1.5mm;
            font-size: 7.5pt;
            overflow-wrap: anywhere;
        }

        .footer {
            margin-top: 3mm;
            border-top: 1px solid #e2e8f0;
            padding-top: 2mm;
            font-size: 6pt;
            color: #64748b;
        }

        @page {
            size: 100mm 70mm;
            margin: 3mm;
        }

        @media print {
            body {
                padding: 0;
                background: white;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }

            .label {
                width: 94mm;
                min-height: 62mm;
                margin: 0;
                border: 1px solid #94a3b8;
                border-radius: 2mm;
            }
        }
    </style>

</head>

<body>

    <div class="toolbar no-print">

        <button
            type="button"
            class="button"
            onclick="window.print()">

            Cetak Label QR

        </button>

        <a
            href="{{ route('asset-qr.index') }}"
            class="button secondary">

            Kembali

        </a>

    </div>


    <section class="label">

        <div class="qr">

            <img
                src="{{ route('asset-qr.image', $asset) }}"
                alt="QR Code {{ $asset->asset_code }}">

            <div class="qr-code">
                {{ $asset->asset_code }}
            </div>

        </div>


        <div class="info">

            <div class="company">
                Asset Management System
            </div>

            <div class="asset-code">
                {{ $asset->asset_code }}
            </div>

            <div class="name">
                {{ $asset->brand }} {{ $asset->model }}
            </div>

            <div class="detail">
                Category:
                {{ $asset->category?->name ?? '-' }}
            </div>

            <div class="detail">
                Serial:
                {{ $asset->serial_number ?: '-' }}
            </div>

            <div class="detail">
                Location:
                {{ $asset->location?->name ?? '-' }}
            </div>

            <div class="detail">
                Status:
                {{ ucfirst($asset->status) }}
            </div>

            <div class="footer">
                Scan QR untuk membuka detail asset.
            </div>

        </div>

    </section>

</body>

</html>
