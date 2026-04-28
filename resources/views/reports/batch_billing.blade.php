<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch Billing Statements</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            font-size: 13px;
            background: #e0e0e0;
        }
        .a4-container {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            page-break-after: always;
        }
        .a4-container:last-child {
            page-break-after: auto;
        }
        @media print {
            body { background: #fff; margin: 0; }
            .a4-container { margin: 0; box-shadow: none; border: none; }
            .no-print { display: none; }
        }
        
        .header-section, .footer-section {
            width: 100%;
        }
        .header-section img, .footer-section img {
            width: 100%;
            display: block;
        }

        .content {
            padding: 0 40px 20px 40px;
            flex: 1;
        }

        .title-section {
            text-align: center;
            margin: 15px 0 25px 0;
        }
        .title-section .line1 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .title-section .line2 {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11pt;
            font-family: 'Arial', sans-serif;
        }
        .info-table td {
            padding: 5px 0;
            vertical-align: bottom;
        }
        .info-label {
            white-space: nowrap;
            padding-right: 5px;
        }
        .info-value-line {
            border-bottom: 1px solid black;
            font-weight: bold;
            padding: 0 5px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid black;
            margin-bottom: 25px;
            font-size: 10.5pt;
        }
        .items-table th, .items-table td {
            border: 1px solid black;
            padding: 6px;
        }
        .items-table th {
            text-align: center;
            font-weight: bold;
        }
        .items-table td {
            text-align: center;
        }
        .items-table td.desc-col {
            text-align: left;
        }
        .items-table tr.item-row td {
            height: 22px;
            padding: 4px 6px;
        }
        .items-table .total-row td {
            border-top: 2px solid black;
            font-weight: bold;
        }

        .signatures {
            width: 100%;
            margin-top: 20px;
            font-size: 11pt;
        }
        .signatures table {
            width: 100%;
            border-collapse: collapse;
        }
        .signatures table td {
            padding-bottom: 25px;
            vertical-align: top;
        }
        .sign-title {
            margin-bottom: 40px;
        }
        .sign-name {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11pt;
        }
        .sign-line {
            border-top: 1px solid black;
            width: 90%;
            padding-top: 3px;
            font-style: italic;
            font-size: 10pt;
        }

        .coa-notice {
            font-size: 9pt;
            font-style: italic;
            margin-top: 30px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin: 20px 0;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer; background: #1e4d2b; color: white; border: none; border-radius: 5px;">Print All Billing Statements</button>
    </div>

    @foreach($orders as $order)
    <div class="a4-container">
        <div class="header-section">
            <img src="{{ asset('img/HEADER.png') }}" alt="Header">
        </div>

        <div class="content">
            <div class="title-section">
                <div class="line1">DNSC WATER REFILLING STATION</div>
                <div class="line2">BILLING STATEMENT</div>
            </div>

            <table class="info-table">
                <tr>
                    <td class="info-label" style="width: 15%;">Billing Statement</td>
                    <td class="info-value-line" style="width: 35%;">{{ $order->reference_number ?? str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="info-label" style="width: 10%; text-align: right; padding-right: 5px;">Date:</td>
                    <td class="info-value-line" style="width: 40%;">{{ $order->created_at->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <td class="info-label">Bill To:</td>
                    <td colspan="3" class="info-value-line">{{ $order->client_name }}</td>
                </tr>
                <tr>
                    <td class="info-label">Address:</td>
                    <td class="info-value-line">
                        @if($order->office)
                            {{ $order->office->name }} {{ $order->office->building ? '(' . $order->office->building . ')' : '' }}
                        @else
                            {{ $order->other_location ?? 'N/A' }}
                        @endif
                    </td>
                    <td class="info-label" style="text-align: right; padding-right: 5px;">PO No.</td>
                    <td class="info-value-line"></td>
                </tr>
            </table>

            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 12%;">Date</th>
                        <th style="width: 30%;">Description</th>
                        <th style="width: 15%;">DR Number</th>
                        <th style="width: 10%;">Quantity</th>
                        <th style="width: 8%;">Unit</th>
                        <th style="width: 10%;">Cost</th>
                        <th style="width: 15%;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="item-row">
                        <td>{{ $order->created_at->format('Y-m-d') }}</td>
                        <td class="desc-col">Purified Drinking Water</td>
                        <td></td>
                        <td>{{ $order->quantity }}</td>
                        <td>gal</td>
                        <td>25.00</td>
                        <td>{{ number_format($order->total_amount, 2) }}</td>
                    </tr>
                    @for ($i = 0; $i < 5; $i++)
                    <tr class="item-row">
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    @endfor
                    <tr class="total-row">
                        <td colspan="6" style="text-align: right; padding-right: 15px;">TOTAL AMOUNT</td>
                        <td>{{ number_format($order->total_amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="signatures">
                <table>
                    <tr>
                        <td style="width: 50%;">
                            <div class="sign-title">Prepared by:</div>
                            <div class="sign-name">JAMES M. JADRAQUE, MBA / {{ ($order->delivered_at ?? $order->delivery_date ?? $order->created_at)->format('M d, Y') }}</div>
                            <div class="sign-line">Name / Signature / Date</div>
                        </td>
                        <td style="width: 50%;">
                            <div class="sign-title">Received by:</div>
                            <div class="sign-name" style="visibility: hidden;">SPACE</div>
                            <div class="sign-line">Name / Signature / Date</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; padding-top: 15px;">
                            <div class="sign-title">Approved by:</div>
                            <div class="sign-name">CHARLO BIANCI M. GURAY, PHD</div>
                            <div class="sign-line">BASD Director</div>
                        </td>
                        <td></td>
                    </tr>
                </table>
            </div>

            <div class="coa-notice">
                This billing statement is issued in support of collection and accounting of income pursuant to COA rules and government accounting standards.
            </div>
        </div>

        <div class="footer-section">
            <img src="{{ asset('img/FOOTER.png') }}" alt="Footer">
        </div>
    </div>
    @endforeach
</body>
</html>
