<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch Delivery Receipts</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            width: 297mm;
            height: 210mm;
            box-sizing: border-box;
            background: #f0f0f0; /* Light gray to see the pages in browser */
        }
        .a4-page {
            width: 297mm;
            height: 210mm;
            background: white;
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            page-break-after: always;
        }
        .receipt-slot {
            width: 148.5mm;
            height: 105mm;
            box-sizing: border-box;
            border: 0.1mm dashed #ccc; /* Cut lines */
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .container {
            width: 138.5mm;
            height: 94mm; /* Slightly smaller to ensure fit */
            border: 1px solid #000;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            background: white;
            overflow: hidden;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1mm; /* Reduced from 1.5mm */
            border-bottom: 2px solid #000;
            text-align: center;
        }
        .header img {
            height: 8mm; /* Reduced from 10mm */
            margin-right: 2mm;
        }
        .header-text h1 {
            font-size: 10pt; /* Reduced from 11pt */
            margin: 0;
            text-transform: uppercase;
        }
        .header-text p {
            font-size: 7pt; /* Reduced from 7.5pt */
            margin: 0;
        }
        .info-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .info-grid td {
            border: 1px solid #000;
            padding: 0.8mm 2mm; /* Reduced vertical padding */
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            font-size: 7pt; /* Reduced from 7.5pt */
            display: block;
            margin-bottom: 0.2mm;
        }
        .value {
            font-size: 8pt; /* Reduced from 8.5pt */
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            flex-grow: 1;
        }
        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 0.8mm 2mm; /* Reduced vertical padding */
            text-align: center;
        }
        .items-table th {
            background-color: #f2f2f2;
            font-size: 7pt;
            text-transform: uppercase;
        }
        .items-table .desc-col { text-align: left; width: 40%; }
        .sign-area {
            display: flex;
            justify-content: space-between;
            padding: 1.5mm 2mm; /* Reduced padding */
            font-size: 7pt; /* Reduced from 7.5pt */
        }
        .sign-line {
            border-bottom: 1px solid #000;
            width: 45mm;
            margin-bottom: 0.5mm;
        }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        
        @media print {
            body { background: transparent; }
            .receipt-slot { border: 0.1mm dashed #eee; }
        }
    </style>
</head>
<body onload="window.print()">
    @foreach($groupedOrders->chunk(4) as $chunk)
    <div class="a4-page">
        @foreach($chunk as $grouped)
        @php $primary = $grouped->primary; @endphp
        <div class="receipt-slot">
            <div class="container">
                <div class="header">
                    <img src="{{ asset('img/dnsc-logo.png') }}" alt="Logo">
                    <div class="header-text">
                        <h1>DNSC - Water Refilling Station</h1>
                        <p>Davao del Norte State College</p>
                        <p>New Visayas, Panabo City</p>
                    </div>
                </div>

                <table class="info-grid">
                    <tr>
                        <td colspan="2"><span class="label">DELIVERY RECEIPT</span></td>
                        <td colspan="2">
                            <span class="label">Date:</span>
                            <span class="value">{{ $grouped->delivery_date ? $grouped->delivery_date->format('M d, Y') : now()->format('M d, Y') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="label">Delivery Receipt No:</span>
                            <span class="value fw-bold" style="font-size: 7.5pt;">{{ $grouped->references }}</span>
                        </td>
                        <td colspan="2">
                            <span class="label">PO No.</span>
                            <span class="value">{{ $grouped->pr_numbers }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <span class="label">Delivered To:</span>
                            <span class="value">{{ $grouped->full_client_name }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <span class="label">Address:</span>
                            <span class="value">{{ $grouped->location }}</span>
                        </td>
                    </tr>
                </table>

                <table class="items-table">
                    <thead>
                        <tr>
                            <th class="desc-col">Item Description</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th>Amount</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalQty = $grouped->items->sum('quantity');
                            $avgPrice = $totalQty > 0 ? $grouped->total_amount / $totalQty : 0;
                        @endphp
                        <tr>
                            <td class="desc-col">
                                Purified Water
                            </td>
                            <td>{{ $totalQty }}</td>
                            <td>Gallon</td>
                            <td>{{ number_format($avgPrice, 2) }}</td>
                            <td>{{ number_format($grouped->total_amount, 2) }}</td>
                        </tr>
                        {{-- Spacing row --}}
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-right fw-bold" style="padding: 0.8mm 2mm;">Grand Total</td>
                            <td class="fw-bold" style="padding: 0.8mm 2mm;">₱{{ number_format($grouped->total_amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="sign-area">
                    <div>
                        <span class="label">Received By:</span>
                        <div class="value fw-bold" style="border-bottom: 1px solid #000; width: 45mm; padding-bottom: 0.2mm;">
                            {{ $grouped->full_client_name }}
                        </div>
                        <div style="margin-top: 1mm;">
                            <span class="label" style="font-size: 6pt; display: inline-block;">Date Received:</span>
                            <span class="value" style="border-bottom: 1px solid #000; width: 22mm; display: inline-block; padding-bottom: 0.2mm; margin-left: 1mm;">
                                {{ $grouped->delivery_date ? $grouped->delivery_date->format('M d, Y') : now()->format('M d, Y') }}
                            </span>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <span class="label">Signature:</span>
                        <div class="sign-line" style="margin-top: 3mm; width: 40mm; margin-left: auto;"></div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        
        {{-- Fill empty slots if less than 4 receipts in chunk --}}
        @for($i = 0; $i < (4 - $chunk->count()); $i++)
        <div class="receipt-slot"></div>
        @endfor
    </div>
    @endforeach
</body>
</html>
