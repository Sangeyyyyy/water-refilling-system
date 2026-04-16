@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 text-dark">
        <div>
            <h2 class="fw-bold mb-1">Operational Analysis</h2>
            <p class="text-muted mb-0">Production, delivery volume, and consumption distribution across units.</p>
        </div>
        <div class="d-flex gap-3">
            <form action="{{ route('reports.operational') }}" method="GET" class="d-flex gap-2">
                <input type="date" name="start_date" class="form-control form-control-sm rounded-pill px-3" value="{{ $startDate->format('Y-m-d') }}">
                <input type="date" name="end_date" class="form-control form-control-sm rounded-pill px-3" value="{{ $endDate->format('Y-m-d') }}">
                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">Filter</button>
            </form>
            <button onclick="window.print()" class="btn btn-light border shadow-sm rounded-pill px-4 no-print">
                <i class="bi bi-printer me-2"></i>Print Report
            </button>
        </div>
    </div>

    <!-- KPI Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-primary border-5">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted text-uppercase small fw-bold mb-0">Total volume</h6>
                    <i class="bi bi-droplet text-primary fs-4"></i>
                </div>
                <h3 class="fw-bold mb-0 text-dark">{{ number_format($orders->sum('quantity')) }} <small class="fs-6 fw-normal text-muted">gal</small></h3>
                <span class="small text-muted">Total output</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-success border-5">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted text-uppercase small fw-bold mb-0">Total Deliveries</h6>
                    <i class="bi bi-truck text-success fs-4"></i>
                </div>
                <h3 class="fw-bold mb-0 text-dark">{{ number_format($orders->count()) }}</h3>
                <span class="small text-muted">Successful fulfillment</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-info border-5">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted text-uppercase small fw-bold mb-0">Avg. Vol/Order</h6>
                    <i class="bi bi-calculator text-info fs-4"></i>
                </div>
                <h3 class="fw-bold mb-0 text-dark">{{ $orders->count() > 0 ? number_format($orders->sum('quantity') / $orders->count(), 1) : 0 }} <small class="fs-6 fw-normal text-muted">gal</small></h3>
                <span class="small text-muted">Efficiency metric</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-warning border-5">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted text-uppercase small fw-bold mb-0">Active Units</h6>
                    <i class="bi bi-building-check text-warning fs-4"></i>
                </div>
                <h3 class="fw-bold mb-0 text-dark">{{ $orders->whereNotNull('office_id')->pluck('office_id')->unique()->count() }}</h3>
                <span class="small text-muted">Unique offices served</span>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-4 text-dark">Consumption Share by Campus</h5>
                <div style="height: 300px;">
                    <canvas id="campusConsumptionChart"></canvas>
                </div>
                <div class="mt-4 p-3 bg-light rounded-3 border-start border-info border-4 mb-4">
                    <h6 class="fw-bold mb-1 small text-uppercase text-info">Campus Insight</h6>
                    <p class="small text-muted mb-0">
                        This chart shows the volume distribution across different campuses. It helps the logistics team prioritize delivery routes and identify high-demand locations.
                    </p>
                </div>
                <div>
                    @foreach($campusConsumption as $index => $campus)
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded-3 bg-light bg-opacity-50">
                        <span><i class="bi bi-circle-fill me-2" style="color: {{ ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'][$index % 5] }}; font-size: 0.6rem;"></i>{{ $campus->name }}</span>
                        <div class="text-end">
                            <span class="fw-bold text-dark">{{ number_format($campus->volume) }} gal</span>
                            <span class="ms-2 badge bg-white text-primary border border-primary border-opacity-10">{{ number_format(($campus->volume / max(1, $orders->sum('quantity'))) * 100, 1) }}%</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-4 text-dark">Top Consuming Units / Offices</h5>
                <div style="height: 350px;">
                    <canvas id="topUnitsChart"></canvas>
                </div>
                <div class="mt-4 p-3 bg-light rounded-3 border-start border-primary border-4 mb-4">
                    <h6 class="fw-bold mb-1 small text-uppercase text-primary">Unit Performance</h6>
                    <p class="small text-muted mb-0">
                        Top consuming offices are ranked by total gallon volume. Monitoring these "VIP" units ensures they are never out of stock and receive consistent service.
                    </p>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-borderless align-middle mb-0">
                        <thead class="text-muted small text-uppercase">
                            <tr>
                                <th># Office</th>
                                <th class="text-end">Volume</th>
                                <th class="text-end" style="width: 40%;">Capacity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topOffices->take(5) as $index => $item)
                            <tr>
                                <td class="py-2">
                                    <span class="fw-bold text-dark">{{ $index + 1 }}. {{ $item->office->name }}</span>
                                </td>
                                <td class="text-end fw-bold text-primary">{{ number_format($item->volume) }} gal</td>
                                <td class="text-end">
                                    @php $max = $topOffices->first()->volume ?: 1; $perc = ($item->volume / $max) * 100; @endphp
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-primary rounded-pill" style="width: {{ $perc }}%"></div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'];

        // Campus Consumption (Doughnut)
        new Chart(document.getElementById('campusConsumptionChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartData['campus_consumption']['labels']) !!},
                datasets: [{
                    data: {!! json_encode($chartData['campus_consumption']['data']) !!},
                    backgroundColor: colors,
                    borderWidth: 0,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // Top Units (Bar)
        new Chart(document.getElementById('topUnitsChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartData['top_units']['labels']) !!},
                datasets: [{
                    label: 'Volume (Gallons)',
                    data: {!! json_encode($chartData['top_units']['data']) !!},
                    backgroundColor: '#4e73df20',
                    borderColor: '#4e73df',
                    borderWidth: 2,
                    borderRadius: 8,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endpush

<style>
    @media print {
        .admin-sidebar, .admin-topbar, .no-print, .btn { display: none !important; }
        .admin-content-wrapper { margin-left: 0 !important; padding: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #eee !important; page-break-inside: avoid; }
        .bg-light { background-color: #f8f9fa !important; border: 1px solid #ddd !important; -webkit-print-color-adjust: exact; }
    }
</style>
@endsection
