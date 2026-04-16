@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark">Operational Reports Hub</h2>
            <p class="text-muted mb-0">Business intelligence and production metrics oversight.</p>
        </div>
        <div class="d-flex gap-3">
            <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="bi bi-download me-2"></i>Export CSV
            </button>
            <button class="btn btn-outline-primary rounded-pill px-4 no-print" type="button" onclick="window.print()">
                <i class="bi bi-printer me-2"></i>Print Report
            </button>
        </div>
    </div>

    <!-- Date Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-5 bg-white no-print">
        <form action="{{ route('admin.reports.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted text-uppercase">Start Date</label>
                <input type="date" name="start_date" class="form-control border-light shadow-none rounded-3" value="{{ $startDate->format('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted text-uppercase">End Date</label>
                <input type="date" name="end_date" class="form-control border-light shadow-none rounded-3" value="{{ $endDate->format('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted text-uppercase d-block">Quick Select</label>
                <div class="btn-group w-100">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setRange('{{ now()->startOfMonth()->format('Y-m-d') }}', '{{ now()->format('Y-m-d') }}')">This Month</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setRange('{{ now()->subMonth()->startOfMonth()->format('Y-m-d') }}', '{{ now()->subMonth()->endOfMonth()->format('Y-m-d') }}')">Last Month</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setRange('{{ now()->startOfYear()->format('Y-m-d') }}', '{{ now()->format('Y-m-d') }}')">This Year</button>
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 rounded-pill">Apply Filter</button>
            </div>
        </form>
    </div>

    <!-- Operational KPI Row -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 border-bottom border-primary border-5">
                <h6 class="text-muted text-uppercase small fw-bold mb-3">Revenue (Selected)</h6>
                <h3 class="fw-bold mb-1 text-dark">₱{{ number_format($stats['revenue'], 2) }}</h3>
                <span class="small text-muted">Filtered total sales</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 border-bottom border-info border-5">
                <h6 class="text-muted text-uppercase small fw-bold mb-3">Volume (Selected)</h6>
                <h3 class="fw-bold mb-1 text-dark">{{ number_format($stats['volume']) }} gal</h3>
                <span class="small text-muted">Total production volume</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 border-bottom border-success border-5">
                <h6 class="text-muted text-uppercase small fw-bold mb-3">Orders (Selected)</h6>
                <h3 class="fw-bold mb-1 text-dark">{{ number_format($stats['orders']) }}</h3>
                <span class="small text-muted">Completed & Confirmed</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 border-bottom border-warning border-5">
                <h6 class="text-muted text-uppercase small fw-bold mb-3">Avg. Order Value</h6>
                <h3 class="fw-bold mb-1 text-dark">₱{{ number_format($stats['avg_order_value'], 2) }}</h3>
                <span class="small text-muted">Per transaction average</span>
            </div>
        </div>
    </div>

    <!-- Tabs Content -->
    <ul class="nav nav-pills mb-4 gap-2 p-1 bg-light rounded-4 d-inline-flex no-print" id="operationalHubTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 py-2 fw-bold" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">Overview</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 py-2 fw-bold" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial" type="button" role="tab">Financials</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 py-2 fw-bold" id="operational-tab" data-bs-toggle="tab" data-bs-target="#operational" type="button" role="tab">Operational</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 py-2 fw-bold" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button" role="tab">Inventory</button>
        </li>
    </ul>

    <div class="tab-content" id="operationalHubTabsContent">
        <!-- Overview Tab -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel">
            <div class="row g-4 mb-5">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold mb-4">Sales Performance Trend</h5>
                        <div style="height: 350px;">
                            <canvas id="salesTrendChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold mb-4">Campus Distribution</h5>
                        <div style="height: 250px;">
                            <canvas id="campusConsumptionChart"></canvas>
                        </div>
                        <div class="mt-4">
                            @foreach($chartData['campus_consumption']['labels'] as $index => $label)
                            <div class="d-flex justify-content-between align-items-center small mb-2 p-2 rounded-3 bg-light bg-opacity-50">
                                <span><i class="bi bi-circle-fill me-2" style="color: {{ ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'][$index % 5] }}; font-size: 0.6rem;"></i>{{ $label }}</span>
                                <span class="fw-bold">{{ number_format($chartData['campus_consumption']['data'][$index]) }} gal</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Tab -->
        <div class="tab-pane fade" id="financial" role="tabpanel">
            <div class="row g-4 mb-5">
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4">Revenue by Week <small class="text-muted fw-normal ms-2">(Last 8 Weeks)</small></h5>
                        <div style="height: 300px;">
                            <canvas id="weeklyRevenueChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="row g-4 h-100">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                                <h5 class="fw-bold mb-4">Refill vs New Gallon</h5>
                                <div style="height: 200px;">
                                    <canvas id="refillSplitChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                                <h5 class="fw-bold mb-4">Client vs Individual</h5>
                                <div style="height: 200px;">
                                    <canvas id="segmentSplitChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Audit Table -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mt-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-table me-2 text-primary"></i>Financial Transactions Log</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="bg-light text-uppercase small fw-bold text-muted">
                            <tr>
                                <th class="d-none d-sm-table-cell">Date</th>
                                <th>Client / Office</th>
                                <th class="d-none d-md-table-cell">Gallons</th>
                                <th>Amount</th>
                                <th class="d-none d-lg-table-cell">Source</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td class="d-none d-sm-table-cell small">{{ $order->created_at->format('M d, Y h:i A') }}</td>
                                <td class="fw-bold text-dark">
                                    @if($order->customer_type === 'Office' && $order->office)
                                        {{ $order->office->name }}
                                        <div class="small fw-normal text-muted d-none d-md-block">{{ $order->office->division->name ?? '' }}</div>
                                    @elseif($order->customer_type === 'Individual' && $order->client)
                                        {{ $order->client->name }}
                                        <div class="small fw-normal text-muted d-none d-md-block">Client Walk-in</div>
                                    @else
                                        Walk-in Guest
                                    @endif
                                </td>
                                <td class="d-none d-md-table-cell">{{ $order->quantity }} gal</td>
                                <td class="fw-bold text-primary">₱{{ number_format($order->total_amount, 2) }}</td>
                                <td class="d-none d-lg-table-cell">{{ $order->source ? ucfirst($order->source) : 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }} rounded-pill" style="font-size: 0.65rem;">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                            @if($orders->isEmpty())
                                <tr><td colspan="6" class="text-center py-4 text-muted">No transactions found for this period.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Operational Tab -->
        <div class="tab-pane fade" id="operational" role="tabpanel">
            <div class="row g-4 mb-5">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4">Top 5 Consuming Units</h5>
                        <div style="height: 350px;">
                            <canvas id="topUnitsChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4">Volume Breakdown per Campus</h5>
                        <div class="campus-list mt-2">
                            @foreach($chartData['campus_consumption']['labels'] as $index => $label)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-bold small">{{ $label }}</span>
                                    <span class="small text-primary fw-bold">{{ number_format($chartData['campus_consumption']['data'][$index]) }} gal</span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    @php
                                        $maxVol = max(1, count($chartData['campus_consumption']['data']) > 0 ? max($chartData['campus_consumption']['data']) : 1);
                                        $percent = ($chartData['campus_consumption']['data'][$index] / $maxVol) * 100;
                                    @endphp
                                    <div class="progress-bar bg-primary rounded-pill" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Division Breakdown Table -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mt-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-diagram-3 me-2 text-primary"></i>Detailed Division & Unit Benchmarks</h5>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light text-uppercase small fw-bold text-muted">
                            <tr>
                                <th>Division / Department</th>
                                <th class="d-none d-md-table-cell">Units / Offices</th>
                                <th class="text-end">Consumption Volume</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($division_benchmarks as $division => $units)
                                <tr>
                                    <td class="fw-bold text-dark fs-6" style="vertical-align: top;">{{ $division }}</td>
                                    <td class="d-none d-md-table-cell">
                                        <ul class="list-unstyled mb-0 ms-2">
                                            @foreach($units as $unit)
                                                <li class="mb-1 pb-1 border-bottom border-light d-flex justify-content-between">
                                                    <span><i class="bi bi-dash text-muted"></i> {{ $unit->office }}</span>
                                                    <span class="fw-bold text-primary">{{ number_format($unit->volume) }} gal</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="text-end fw-bold fs-5 text-dark" style="vertical-align: top;">
                                        {{ number_format($units->sum('volume')) }} gal
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center py-4 text-muted">No operational volume data found for this period.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Inventory Tab -->
        <div class="tab-pane fade" id="inventory" role="tabpanel">
            <div class="row g-4 mb-5">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold mb-4">Stock Health Monitor</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Current Stock</th>
                                        <th style="width: 40%">Health</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inventoryStats as $item)
                                    <tr>
                                        <td class="fw-bold">{{ $item->item_name }}</td>
                                        <td>{{ $item->stock_level }} <small class="text-muted d-none d-sm-inline">{{ $item->unit }}</small></td>
                                        <td class="d-none d-md-table-cell">
                                            @php
                                                $health = min(100, ($item->stock_level / max(1, $item->low_stock_threshold * 3)) * 100);
                                                $color = $item->stock_level <= $item->low_stock_threshold ? 'bg-danger' : ($item->stock_level <= $item->low_stock_threshold * 2 ? 'bg-warning' : 'bg-success');
                                            @endphp
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar {{ $color }} rounded-pill" style="width: {{ $health }}%"></div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($item->stock_level <= $item->low_stock_threshold)
                                                <span class="badge bg-danger rounded-pill" style="font-size: 0.65rem;">Critical</span>
                                            @elseif($item->stock_level <= $item->low_stock_threshold * 2)
                                                <span class="badge bg-warning text-dark rounded-pill" style="font-size: 0.65rem;">Low</span>
                                            @else
                                                <span class="badge bg-success rounded-pill" style="font-size: 0.65rem;">Healthy</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4">Gallon Circulation</h5>
                        <div style="height: 250px;">
                            <canvas id="circulationChart"></canvas>
                        </div>
                        <div class="mt-4 text-center">
                            <div class="display-6 fw-bold text-primary">{{ $total_in_circulation }}</div>
                            <div class="text-muted small">Total Gallons in Market</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Circulation Breakdown Table -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mt-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-geo-alt me-2 text-primary"></i>Top Holding Offices & Clients</h5>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded-4 h-100 border border-light">
                            <h6 class="fw-bold mb-3 text-muted text-uppercase small">Internal Offices (<span class="text-primary">{{ $office_distribution->sum('gallon_count') }}</span> gal)</h6>
                            <ul class="list-group list-group-flush bg-transparent">
                                @forelse($office_distribution->take(10) as $office)
                                <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0">
                                    <span>
                                        {{ $office->name }}
                                        <br><small class="text-muted">{{ $office->division->name ?? 'N/A' }}</small>
                                    </span>
                                    <span class="badge bg-primary rounded-pill px-3">{{ $office->gallon_count }}</span>
                                </li>
                                @empty
                                <li class="list-group-item bg-transparent text-muted px-0 border-0">No internal holding data.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded-4 h-100 border border-light">
                            <h6 class="fw-bold mb-3 text-muted text-uppercase small">Registered Clients (<span class="text-primary">{{ $client_distribution->sum('gallon_count') }}</span> gal)</h6>
                            <ul class="list-group list-group-flush bg-transparent">
                                @forelse($client_distribution->take(10) as $client)
                                <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0">
                                    <span>{{ $client->name }}</span>
                                    <span class="badge bg-primary rounded-pill px-3">{{ $client->gallon_count }}</span>
                                </li>
                                @empty
                                <li class="list-group-item bg-transparent text-muted px-0 border-0">No external client holding data.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.reports.partials.export_modal')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function setRange(start, end) {
        document.getElementsByName('start_date')[0].value = start;
        document.getElementsByName('end_date')[0].value = end;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        };

        // 1. Sales Trend
        const salesCtx = document.getElementById('salesTrendChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData['sales_trend']['labels']) !!},
                datasets: [{
                    data: {!! json_encode($chartData['sales_trend']['data']) !!},
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4e73df',
                }]
            },
            options: {
                ...chartOptions,
                scales: {
                    x: { grid: { display: false } },
                    y: { 
                        beginAtZero: true, 
                        grid: { borderDash: [5, 5] },
                        ticks: { callback: v => '₱' + v.toLocaleString() }
                    }
                }
            }
        });

        // 2. Campus Distribution
        new Chart(document.getElementById('campusConsumptionChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartData['campus_consumption']['labels']) !!},
                datasets: [{
                    data: {!! json_encode($chartData['campus_consumption']['data']) !!},
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                    borderWidth: 0
                }]
            },
            options: { ...chartOptions, cutout: '70%' }
        });

        // 3. Weekly Revenue
        new Chart(document.getElementById('weeklyRevenueChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartData['weekly_revenue']['labels']) !!},
                datasets: [{
                    data: {!! json_encode($chartData['weekly_revenue']['data']) !!},
                    backgroundColor: '#4e73df',
                    borderRadius: 8
                }]
            },
            options: {
                ...chartOptions,
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { borderDash: [5, 5] } }
                }
            }
        });

        // 4. Refill Split
        new Chart(document.getElementById('refillSplitChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartData['refill_split']['labels']) !!},
                datasets: [{
                    data: {!! json_encode($chartData['refill_split']['data']) !!},
                    backgroundColor: ['#1cc88a', '#36b9cc'],
                    borderWidth: 0
                }]
            },
            options: { ...chartOptions, cutout: '70%' }
        });

        // 5. Segment Split
        new Chart(document.getElementById('segmentSplitChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartData['segment_split']['labels']) !!},
                datasets: [{
                    data: {!! json_encode($chartData['segment_split']['data']) !!},
                    backgroundColor: ['#4e73df', '#f6c23e'],
                    borderWidth: 0
                }]
            },
            options: { ...chartOptions, cutout: '70%' }
        });

        // 6. Top Units
        new Chart(document.getElementById('topUnitsChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartData['top_units']['labels']) !!},
                datasets: [{
                    data: {!! json_encode($chartData['top_units']['data']) !!},
                    backgroundColor: '#4e73df',
                    borderRadius: 8
                }]
            },
            options: {
                ...chartOptions,
                indexAxis: 'y',
                scales: {
                    x: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                    y: { grid: { display: false } }
                }
            }
        });

        // 7. Circulation
        new Chart(document.getElementById('circulationChart'), {
            type: 'pie',
            data: {
                labels: {!! json_encode($chartData['circulation']['labels']) !!},
                datasets: [{
                    data: {!! json_encode($chartData['circulation']['data']) !!},
                    backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e']
                }]
            },
            options: chartOptions
        });
    });
</script>

<style>
    .nav-pills .nav-link.active { background-color: #4e73df; }
    .nav-pills .nav-link { color: #858796; }
    .nav-pills .nav-link:hover { background-color: #eaecf4; }
    .transition-all { transition: all 0.3s ease; }
    .progress { background-color: #f1f3f5; }

    @media print {
        body { background: white !important; font-size: 11pt; color: black; }
        .no-print, .admin-sidebar, .admin-topbar, .nav-pills, form, .btn, .modal { display: none !important; }
        .admin-content-wrapper, main { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        .container { max-width: 100% !important; width: 100% !important; margin: 0; padding: 0; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; break-inside: avoid; margin-bottom: 20px !important; }
        
        /* Show all tabs sequentially for printing */
        .tab-content > .tab-pane { 
            display: block !important; 
            opacity: 1 !important; 
            page-break-before: always; 
        }
        .tab-content > .tab-pane:first-child { 
            page-break-before: avoid; 
        }
        
        canvas, .chart-container { max-height: 250px !important; }
        .bg-light { background-color: #f8f9fa !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
@endsection
