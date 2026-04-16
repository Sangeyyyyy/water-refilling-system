<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Report — {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</title>
    <style>
        @page { size: A4; margin: 0; }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            margin: 0;
            padding: 0;
            background: #e0e0e0;
            color: #000;
        }
        .a4-container {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }
        @media print {
            body { background: #fff; margin: 0; }
            .a4-container { margin: 0; box-shadow: none; }
            .no-print { display: none !important; }
        }
        .header-section img, .footer-section img { width: 100%; display: block; }
        .content { padding: 10px 40px 20px 40px; flex: 1; }

        .title-section { text-align: center; margin: 10px 0 20px 0; }
        .title-section .line1 { font-size: 13pt; font-weight: bold; text-transform: uppercase; }
        .title-section .line2 { font-size: 15pt; font-weight: bold; text-transform: uppercase; }
        .title-section .period { font-size: 10pt; color: #555; margin-top: 4px; }

        .summary-table { width: 100%; border-collapse: collapse; margin-bottom: 18px; font-size: 10.5pt; }
        .summary-table th { background: #1e4d2b; color: #fff; padding: 7px 10px; text-align: left; }
        .summary-table td { padding: 6px 10px; border-bottom: 1px solid #ddd; }
        .summary-table tr:nth-child(even) td { background: #f7f7f7; }
        .summary-table .val { font-weight: bold; text-align: right; }

        .section-title { font-size: 10.5pt; font-weight: bold; text-transform: uppercase;
            color: #1e4d2b; border-bottom: 2px solid #1e4d2b; padding-bottom: 3px; margin: 18px 0 8px 0; }

        .data-table { width: 100%; border-collapse: collapse; font-size: 9.5pt; margin-bottom: 18px; }
        .data-table th { background: #1e4d2b; color: #fff; padding: 6px 8px; text-align: center; border: 1px solid #155626; }
        .data-table td { padding: 5px 8px; border: 1px solid #ccc; text-align: center; }
        .data-table td.left { text-align: left; }
        .data-table tr:nth-child(even) td { background: #f9fdf9; }
        .data-table tfoot td { background: #1e4d2b; color: #fff; font-weight: bold; border: 1px solid #155626; }

        .signatures { width: 100%; margin-top: 30px; }
        .signatures table { width: 100%; border-collapse: collapse; }
        .signatures table td { padding-bottom: 30px; vertical-align: top; width: 33%; }
        .sign-title { margin-bottom: 45px; font-size: 10pt; }
        .sign-name { font-weight: bold; text-transform: uppercase; font-size: 10pt; }
        .sign-line { border-top: 1px solid black; width: 85%; padding-top: 3px; font-style: italic; font-size: 9pt; color: #444; }
        .generated-note { font-size: 8pt; color: #777; text-align: right; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="no-print" style="text-align:center; padding:15px 0;">
        <button onclick="window.print()" style="padding:10px 25px; font-size:15px; cursor:pointer; background:#1e4d2b; color:white; border:none; border-radius:5px;">
            Print Financial Report
        </button>
    </div>

    <div class="a4-container">
        <div class="header-section">
            <img src="{{ asset('img/HEADER.png') }}" alt="DNSC Header">
        </div>

        <div class="content">
            <div class="title-section">
                <div class="line1">DNSC Water Refilling Station</div>
                <div class="line2">Financial Report</div>
                <div class="period">Period: {{ $startDate->format('M d, Y') }} — {{ $endDate->format('M d, Y') }}</div>
            </div>

            {{-- Summary KPI Table --}}
            <div class="section-title">Summary</div>
            <table class="summary-table">
                <tr>
                    <td>Total Revenue</td>
                    <td class="val">₱{{ number_format($totalSales, 2) }}</td>
                    <td>Total Orders</td>
                    <td class="val">{{ number_format($orders->count()) }}</td>
                </tr>
                <tr>
                    <td>Total Gallons Sold</td>
                    <td class="val">{{ number_format($orders->sum('quantity')) }} gal</td>
                    <td>Average Order Value</td>
                    <td class="val">₱{{ number_format($summary['average_order'], 2) }}</td>
                </tr>
                <tr>
                    <td>Refill Orders (qty)</td>
                    <td class="val">{{ number_format($summary['refills']) }} gal</td>
                    <td>New Gallon Orders (qty)</td>
                    <td class="val">{{ number_format($summary['new_gallons']) }} gal</td>
                </tr>
                <tr>
                    <td>Office Orders</td>
                    <td class="val">{{ number_format($summary['office']) }}</td>
                    <td>Individual Orders</td>
                    <td class="val">{{ number_format($summary['individual']) }}</td>
                </tr>
            </table>

            {{-- Transaction Log Table --}}
            <div class="section-title">Detailed Transaction Log</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Date</th>
                        <th>Client / Office</th>
                        <th>Type</th>
                        <th>Qty (gal)</th>
                        <th>Amount (₱)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td class="left">{{ $order->reference_number ?? '#'.str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="left">
                            {{ $order->client_name }}
                            @if($order->office)
                                <br><small>{{ $order->office->name }}</small>
                            @endif
                        </td>
                        <td>{{ $order->is_refill ? 'Refill' : 'New Gallon' }}</td>
                        <td>{{ $order->quantity }}</td>
                        <td>{{ number_format($order->total_amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">TOTALS</td>
                        <td>{{ $orders->sum('quantity') }} gal</td>
                        <td>₱{{ number_format($totalSales, 2) }}</td>
                    </tr>
                </tfoot>
            </table>

            {{-- Signatures --}}
            <div class="signatures">
                <table>
                    <tr>
                        <td>
                            <div class="sign-title">Prepared by:</div>
                            <div class="sign-name">JAMES M. JADRAQUE, MBA</div>
                            <div class="sign-line">Name / Signature / Date</div>
                        </td>
                        <td>
                            <div class="sign-title">Reviewed by:</div>
                            <div class="sign-name" style="visibility:hidden;">SPACE</div>
                            <div class="sign-line">Name / Signature / Date</div>
                        </td>
                        <td>
                            <div class="sign-title">Approved by:</div>
                            <div class="sign-name">CHARLO BIANCI M. GURAY, PHD</div>
                            <div class="sign-line">BASD Director</div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="generated-note">
                Generated: {{ now()->format('M d, Y h:i A') }} &nbsp;|&nbsp;
                Prepared by: {{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})
            </div>
        </div>

        <div class="footer-section">
            <img src="{{ asset('img/FOOTER.png') }}" alt="DNSC Footer">
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            // Small delay so the page fully renders before print dialog
            setTimeout(() => window.print(), 500);
        });
    </script>
</body>
</html>
