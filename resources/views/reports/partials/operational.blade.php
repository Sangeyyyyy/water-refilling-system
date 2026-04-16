@php
    $volTrend = $operationalTrends['volume'] ?? 0;
    $delTrend = $operationalTrends['deliveries'] ?? 0;
    $unitTrend = $operationalTrends['active_units'] ?? 0;
    $trendLabel = $operationalTrends['period_label'] ?? 'vs prev period';
@endphp

<!-- KPI Row -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-primary border-5 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="text-muted text-uppercase small fw-bold mb-0">Total volume</h6>
                <i class="bi bi-droplet text-primary fs-4"></i>
            </div>
            <h3 class="fw-bold mb-0 text-dark">{{ number_format($operationalOrders->sum('quantity')) }} <small class="fs-6 fw-normal text-muted">gal</small></h3>
            <div class="mt-2 d-flex align-items-center">
                <span class="badge {{ $volTrend >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 text-{{ $volTrend >= 0 ? 'success' : 'danger' }} rounded-pill px-2 small">
                    <i class="bi bi-arrow-{{ $volTrend >= 0 ? 'up' : 'down' }}-right me-1"></i>
                    {{ number_format(abs($volTrend), 1) }}%
                </span>
                <span class="small text-muted ms-2">{{ $trendLabel }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-success border-5 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="text-muted text-uppercase small fw-bold mb-0">Total Deliveries</h6>
                <i class="bi bi-truck text-success fs-4"></i>
            </div>
            <h3 class="fw-bold mb-0 text-dark">{{ number_format($operationalOrders->count()) }}</h3>
            <div class="mt-2 d-flex align-items-center">
                <span class="badge {{ $delTrend >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 text-{{ $delTrend >= 0 ? 'success' : 'danger' }} rounded-pill px-2 small">
                    <i class="bi bi-arrow-{{ $delTrend >= 0 ? 'up' : 'down' }}-right me-1"></i>
                    {{ number_format(abs($delTrend), 1) }}%
                </span>
                <span class="small text-muted ms-2">{{ $trendLabel }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-info border-5 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="text-muted text-uppercase small fw-bold mb-0">Avg. Vol/Order</h6>
                <i class="bi bi-calculator text-info fs-4"></i>
            </div>
            <h3 class="fw-bold mb-0 text-dark">{{ $operationalOrders->count() > 0 ? number_format($operationalOrders->sum('quantity') / $operationalOrders->count(), 1) : 0 }} <small class="fs-6 fw-normal text-muted">gal</small></h3>
            <span class="small text-muted">Efficiency metric</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-warning border-5 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="text-muted text-uppercase small fw-bold mb-0">Active Units</h6>
                <i class="bi bi-building-check text-warning fs-4"></i>
            </div>
            <h3 class="fw-bold mb-0 text-dark">{{ $operationalOrders->whereNotNull('office_id')->pluck('office_id')->unique()->count() }}</h3>
            <div class="mt-2 d-flex align-items-center">
                <span class="badge {{ $unitTrend >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 text-{{ $unitTrend >= 0 ? 'success' : 'danger' }} rounded-pill px-2 small">
                    <i class="bi bi-arrow-{{ $unitTrend >= 0 ? 'up' : 'down' }}-right me-1"></i>
                    {{ number_format(abs($unitTrend), 1) }}%
                </span>
                <span class="small text-muted ms-2">{{ $trendLabel }}</span>
            </div>
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
                    This chart shows the volume distribution across different campuses.
                </p>
            </div>
            <div>
                @foreach($campusConsumption as $index => $campus)
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded-3 bg-light bg-opacity-50">
                    <span class="text-dark small"><i class="bi bi-circle-fill me-2" style="color: {{ ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'][$index % 5] }}; font-size: 0.6rem;"></i>{{ $campus->name }}</span>
                    <div class="text-end">
                        <span class="fw-bold text-dark">{{ number_format($campus->volume) }} gal</span>
                        <span class="ms-2 badge bg-white text-primary border border-primary border-opacity-10">{{ number_format(($campus->volume / max(1, $operationalOrders->sum('quantity'))) * 100, 1) }}%</span>
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
                <p class="small text-muted mb-0">Top consuming offices are ranked by total gallon volume.</p>
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
                                <span class="fw-bold text-dark small">{{ $index + 1 }}. {{ $item->office_name }}</span>
                            </td>
                            <td class="text-end fw-bold text-primary small">{{ number_format($item->volume) }} gal</td>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (!document.getElementById('campusConsumptionChart')) return;
        
        const colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'];

        new Chart(document.getElementById('campusConsumptionChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($operationalChartData['campus_consumption']['labels']) !!},
                datasets: [{
                    data: {!! json_encode($operationalChartData['campus_consumption']['data']) !!},
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

        new Chart(document.getElementById('topUnitsChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($operationalChartData['top_units']['labels']) !!},
                datasets: [{
                    label: 'Volume (Gallons)',
                    data: {!! json_encode($operationalChartData['top_units']['data']) !!},
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
