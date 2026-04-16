@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 text-dark">
        <div>
            <h2 class="fw-bold mb-1">Inventory & Assets Report</h2>
            <p class="text-muted mb-0">Monitor stock health, gallon circulation, and asset distribution.</p>
        </div>
        <div class="d-flex gap-3 no-print">
            <a href="{{ route('inventory.index') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-box-seam me-2"></i>Manage Inventory
            </a>
            <button onclick="window.print()" class="btn btn-light border shadow-sm rounded-pill px-4">
                <i class="bi bi-printer me-2"></i>Print Report
            </button>
        </div>
    </div>

    <!-- Asset Summary KPI Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-dark text-white">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-white-50 text-uppercase small fw-bold mb-0">In Circulation</h6>
                    <i class="bi bi-arrow-repeat fs-4 text-primary"></i>
                </div>
                <h3 class="fw-bold mb-0">{{ number_format($total_in_circulation) }}</h3>
                <span class="small text-white-50">Total gallons deployed</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex justify-content-between align-items-center mb-2 text-dark">
                    <h6 class="text-muted text-uppercase small fw-bold mb-0">Office Assets</h6>
                    <i class="bi bi-building fs-4 text-info"></i>
                </div>
                <h3 class="fw-bold mb-0 text-dark">{{ number_format($office_distribution->sum('gallon_count')) }}</h3>
                <span class="small text-muted">Held by university units</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex justify-content-between align-items-center mb-2 text-dark">
                    <h6 class="text-muted text-uppercase small fw-bold mb-0">Staff/User Assets</h6>
                    <i class="bi bi-people fs-4 text-success"></i>
                </div>
                <h3 class="fw-bold mb-0 text-dark">{{ number_format($user_distribution->sum('gallon_count')) }}</h3>
                <span class="small text-muted">Allocated to employees</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-dark">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted text-uppercase small fw-bold mb-0">Client Assets</h6>
                    <i class="bi bi-person-badge fs-4 text-warning"></i>
                </div>
                <h3 class="fw-bold mb-0 text-dark">{{ number_format($client_distribution->sum('gallon_count')) }}</h3>
                <span class="small text-muted">Held by walk-in clients</span>
            </div>
        </div>
    </div>

    <!-- Stock Health Section -->
    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-4 text-dark">Stock Levels vs. Reorder Thresholds</h5>
                <div style="height: 300px;">
                    <canvas id="stockHealthChart"></canvas>
                </div>
                <div class="mt-4 p-3 bg-light rounded-3 border-start border-primary border-4 mb-3">
                    <h6 class="fw-bold mb-1 small text-uppercase text-primary">Stock Insight</h6>
                    <p class="small text-muted mb-0">
                        This chart compares current stock against safety thresholds. Bars approaching or falling below the red line indicate items that require urgent procurement to avoid operational downtime.
                    </p>
                </div>
                <div class="mt-2">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-3">Item</th>
                                    <th>Current</th>
                                    <th>Threshold</th>
                                    <th style="width: 30%;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                <tr>
                                    <td class="ps-3 py-3 fw-bold text-dark">{{ $item->item_name }}</td>
                                    <td>{{ $item->stock_level }} <small class="text-muted">{{ $item->unit }}</small></td>
                                    <td class="text-muted small">{{ $item->low_stock_threshold }}</td>
                                    <td>
                                        @if($item->stock_level <= $item->low_stock_threshold)
                                            <span class="badge bg-danger rounded-pill px-3">Reorder Immediately</span>
                                        @elseif($item->stock_level <= $item->low_stock_threshold * 1.5)
                                            <span class="badge bg-warning text-dark rounded-pill px-3">Low stock</span>
                                        @else
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 border-0">High</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-4 text-dark">Gallon Circulation Breakdwon</h5>
                <div style="height: 250px;">
                    <canvas id="circulationChart"></canvas>
                </div>
                <div class="mt-4 p-3 bg-light rounded-3 border-start border-info border-4">
                    <h6 class="fw-bold mb-1 small text-uppercase text-info">Asset Tracking</h6>
                    <p class="small text-muted mb-0">
                        Visualizing gallon distribution across sectors. This helps in auditing physical assets and planning for future container acquisitions based on where most units are held.
                    </p>
                </div>
                <div class="mt-4">
                    <h6 class="fw-bold mb-3 small text-muted text-uppercase border-bottom pb-2">Top Unit Holdings</h6>
                    @foreach($office_distribution->take(5) as $office)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-dark small fw-medium">{{ $office->name }}</span>
                        <span class="badge bg-light text-primary border rounded-pill">{{ $office->gallon_count }} gal</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Stock Health Chart (Bar grouped)
        new Chart(document.getElementById('stockHealthChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($stockData['labels']) !!},
                datasets: [
                    {
                        label: 'Current Stock',
                        data: {!! json_encode($stockData['current']) !!},
                        backgroundColor: '#4e73df',
                        borderRadius: 6
                    },
                    {
                        label: 'Low Threshold',
                        data: {!! json_encode($stockData['threshold']) !!},
                        backgroundColor: '#e74a3b40',
                        borderColor: '#e74a3b',
                        borderWidth: 1,
                        borderRadius: 6,
                        borderDash: [2, 2]
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
                },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Circulation Chart (Pie)
        new Chart(document.getElementById('circulationChart'), {
            type: 'pie',
            data: {
                labels: {!! json_encode($circulationData['labels']) !!},
                datasets: [{
                    data: {!! json_encode($circulationData['data']) !!},
                    backgroundColor: ['#36b9cc', '#1cc88a', '#f6c23e'],
                    borderWidth: 0,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 15 } }
                }
            }
        });
    });
</script>
@endpush

<style>
    @media print {
        .admin-sidebar, .admin-topbar, .no-print, .btn, .no-print * { display: none !important; }
        .admin-content-wrapper { margin-left: 0 !important; padding: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #eee !important; page-break-inside: avoid; }
        .bg-light { background-color: #f8f9fa !important; border: 1px solid #ddd !important; -webkit-print-color-adjust: exact; }
    }
</style>
@endsection
