<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Receipt - {{ $order->reference_number }}</title>
    <style>
        @page {
            size: A6 landscape;
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            width: 148.5mm;
            height: 105mm;
            box-sizing: border-box;
            font-size: 9pt;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            width: 138.5mm; /* 148.5 - 10mm padding equivalent */
            height: 95mm;  /* 105 - 10mm padding equivalent */
            border: 1px solid #000;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5mm;
            border-bottom: 2px solid #000;
            text-align: center;
        }
        .header img {
            height: 10mm;
            margin-right: 3mm;
        }
        .header-text h1 {
            font-size: 11pt;
            margin: 0;
            text-transform: uppercase;
        }
        .header-text p {
            font-size: 7.5pt;
            margin: 0;
        }
        .info-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .info-grid td {
            border: 1px solid #000;
            padding: 1mm 2mm;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            font-size: 7.5pt;
            display: block;
            margin-bottom: 0.5mm;
        }
        .value {
            font-size: 9pt;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            flex-grow: 1;
        }
        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 1mm 2mm;
            text-align: center;
        }
        .items-table th {
            background-color: #f2f2f2;
            font-size: 7.5pt;
            text-transform: uppercase;
        }
        .items-table .desc-col { text-align: left; width: 40%; }
        .footer {
            width: 100%;
            border-collapse: collapse;
        }
        .footer td {
            border: 1px solid #000;
            padding: 1.5mm;
        }
        .sign-area {
            display: flex;
            justify-content: space-between;
            padding: 2mm;
            font-size: 7.5pt;
        }
        .sign-line {
            border-bottom: 1px solid #000;
            width: 45mm;
            margin-bottom: 0.5mm;
        }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        
        @media print {
            body { margin: 0; padding: 0; }
            .container { border: 1px solid #000; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <img src="{{ asset('img/dnsc-logo.png') }}" alt="Logo">
            <div class="header-text">
                <h1>DNSC - Water Refilling Station</h1>
                <p>Davao del Norte State College</p>
                <p>New Visayas, Panabo City</p>
            </div>
        </div>

        <!-- Info Grid -->
        <table class="info-grid">
            <tr>
                <td colspan="2"><span class="label">DELIVERY RECEIPT</span></td>
                <td colspan="2">
                    <span class="label">Date:</span>
                    <span class="value">{{ $order->delivery_date ? $order->delivery_date->format('M d, Y') : now()->format('M d, Y') }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <span class="label">Delivery Receipt No:</span>
                    <span class="value fw-bold">{{ $order->reference_number }}</span>
                </td>
                <td colspan="2">
                    <span class="label">PO No.</span>
                    <span class="value">{{ $order->pr_number ?? 'N/A' }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <span class="label">Delivered To:</span>
                    <span class="value">{{ $order->full_client_name }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <span class="label">Address:</span>
                    <span class="value">{{ $order->office->name ?? $order->other_location }}</span>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="desc-col">Item Description</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Amount</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="desc-col">Purified Water</td>
                    <td>{{ $order->quantity }}</td>
                    <td>Gallon</td>
                    <td>{{ number_format($order->total_amount / $order->quantity, 2) }}</td>
                    <td>{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                {{-- Reduced empty rows to save space --}}
                @for ($i = 0; $i < 1; $i++)
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                @endfor
                <tr>
                    <td colspan="4" class="text-right fw-bold" style="padding: 1mm 2mm;">Grand Total</td>
                    <td class="fw-bold" style="padding: 1mm 2mm;">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Signatures Section -->
        <div class="sign-area">
            <div>
                <span class="label">Received By:</span>
                <div class="value fw-bold" style="border-bottom: 1px solid #000; width: 50mm; padding-bottom: 0.5mm;">
                    {{ $order->full_client_name }}
                </div>
                <div style="margin-top: 2mm;">
                    <span class="label" style="font-size: 7pt; display: inline-block;">Date Received:</span>
                    <span class="value" style="border-bottom: 1px solid #000; width: 30mm; display: inline-block; padding-bottom: 0.5mm; margin-left: 2mm;">
                        {{ $order->delivery_date ? $order->delivery_date->format('M d, Y') : now()->format('M d, Y') }}
                    </span>
                </div>
            </div>
            <div style="text-align: right;">
                <span class="label">Signature:</span>
                <div class="sign-line" style="margin-top: 4mm; width: 50mm; margin-left: auto;"></div>
            </div>
        </div>
    </div>
</body>
</html>
