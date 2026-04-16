<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory & Assets Report — {{ now()->format('M d, Y') }}</title>
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

        .badge-ok { color: #155724; font-weight: bold; }
        .badge-low { color: #856404; font-weight: bold; }
        .badge-critical { color: #842029; font-weight: bold; }

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
            Print Inventory Report
        </button>
    </div>

    <div class="a4-container">
        <div class="header-section">
            <img src="{{ asset('img/HEADER.png') }}" alt="DNSC Header">
        </div>

        <div class="content">
            <div class="title-section">
                <div class="line1">DNSC Water Refilling Station</div>
                <div class="line2">Inventory &amp; Assets Report</div>
                <div class="period">As of: {{ now()->format('M d, Y h:i A') }}</div>
            </div>

            {{-- Summary --}}
            <div class="section-title">Circulation Summary</div>
            <table class="summary-table">
                <tr>
                    <td>Total Gallons In Circulation</td>
                    <td class="val">{{ number_format($total_in_circulation) }} units</td>
                    <td>Held by Offices</td>
                    <td class="val">{{ number_format($office_distribution->sum('gallon_count')) }} units</td>
                </tr>
                <tr>
                    <td>Held by Staff / Users</td>
                    <td class="val">{{ number_format($user_distribution->sum('gallon_count')) }} units</td>
                    <td>Held by Clients</td>
                    <td class="val">{{ number_format($client_distribution->sum('gallon_count')) }} units</td>
                </tr>
            </table>

            {{-- Stock Status Table --}}
            <div class="section-title">Stock Status</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="left" style="text-align:left;">Item Name</th>
                        <th>Current Stock</th>
                        <th>Unit</th>
                        <th>Low Threshold</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td class="left">{{ $item->item_name }}</td>
                        <td>{{ number_format($item->stock_level) }}</td>
                        <td>{{ $item->unit }}</td>
                        <td>{{ number_format($item->low_stock_threshold) }}</td>
                        <td>
                            @if($item->stock_level <= $item->low_stock_threshold)
                                <span class="badge-critical">Critical</span>
                            @elseif($item->stock_level <= $item->low_stock_threshold * 2)
                                <span class="badge-low">Low</span>
                            @else
                                <span class="badge-ok">High</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Gallon Circulation Table --}}
            <div class="section-title">Gallon Circulation — Office Holdings</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="left" style="text-align:left;">Office / Unit</th>
                        <th>Division</th>
                        <th>Gallons Held</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($office_distribution as $office)
                    <tr>
                        <td class="left">{{ $office->name }}</td>
                        <td>{{ $office->division->name ?? 'N/A' }}</td>
                        <td>{{ number_format($office->gallon_count) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3">No office holdings.</td></tr>
                    @endforelse
                </tbody>
            </table>

            @if($client_distribution->isNotEmpty() || $user_distribution->isNotEmpty())
            <div class="section-title">Gallon Circulation — Individual Holdings</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="left" style="text-align:left;">Name</th>
                        <th>Type</th>
                        <th>Gallons Held</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($client_distribution as $client)
                    <tr>
                        <td class="left">{{ $client->first_name }} {{ $client->last_name }}</td>
                        <td>Client</td>
                        <td>{{ number_format($client->gallon_count) }}</td>
                    </tr>
                    @endforeach
                    @foreach($user_distribution as $user)
                    <tr>
                        <td class="left">{{ $user->name }}</td>
                        <td>Staff/User</td>
                        <td>{{ number_format($user->gallon_count) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

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
            setTimeout(() => window.print(), 500);
        });
    </script>
</body>
</html>
