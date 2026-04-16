@php
    $revTrend = $financialTrends['revenue'] ?? 0;
    $volTrend = $financialTrends['volume'] ?? 0;
    $ordTrend = $financialTrends['orders'] ?? 0;
    $trendLabel = $financialTrends['period_label'] ?? 'vs prev period';
@endphp

<!-- KPI Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-primary text-white h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="text-white-50 text-uppercase small fw-bold mb-0">Total Revenue</h6>
                <i class="bi bi-cash-stack fs-4"></i>
            </div>
            <h3 class="fw-bold mb-0">₱{{ number_format($totalSales, 2) }}</h3>
            <div class="mt-2 d-flex align-items-center">
                <span class="badge bg-white text-{{ $revTrend >= 0 ? 'success' : 'danger' }} rounded-pill px-2 small">
                    <i class="bi bi-arrow-{{ $revTrend >= 0 ? 'up' : 'down' }}-right me-1"></i>
                    {{ number_format(abs($revTrend), 1) }}%
                </span>
                <span class="small text-white-50 ms-2">{{ $trendLabel }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="text-muted text-uppercase small fw-bold mb-0">Total volume</h6>
                <i class="bi bi-droplet-fill text-info fs-4"></i>
            </div>
            <h3 class="fw-bold mb-0 text-dark">{{ number_format($orders->sum('quantity')) }} gal</h3>
            <div class="mt-2 d-flex align-items-center">
                <span class="badge {{ $volTrend >= 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} rounded-pill px-2 small">
                    <i class="bi bi-arrow-{{ $volTrend >= 0 ? 'up' : 'down' }}-right me-1"></i>
                    {{ number_format(abs($volTrend), 1) }}%
                </span>
                <span class="small text-muted ms-2">{{ $trendLabel }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="text-muted text-uppercase small fw-bold mb-0">Avg. Order Value</h6>
                <i class="bi bi-graph-up-arrow text-success fs-4"></i>
            </div>
            <h3 class="fw-bold mb-0 text-dark">₱{{ number_format($summary['average_order'], 2) }}</h3>
            <span class="small text-muted">Per transaction average</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="text-muted text-uppercase small fw-bold mb-0">Total Orders</h6>
                <i class="bi bi-cart-check-fill text-warning fs-4"></i>
            </div>
            <h3 class="fw-bold mb-0 text-dark">{{ number_format($orders->count()) }}</h3>
            <div class="mt-2 d-flex align-items-center">
                <span class="badge {{ $ordTrend >= 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} rounded-pill px-2 small">
                    <i class="bi bi-arrow-{{ $ordTrend >= 0 ? 'up' : 'down' }}-right me-1"></i>
                    {{ number_format(abs($ordTrend), 1) }}%
                </span>
                <span class="small text-muted ms-2">{{ $trendLabel }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-5">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark">Revenue Growth Trend</h5>
            <div style="height: 350px;">
                <canvas id="revenueTrendChart"></canvas>
            </div>
            <div class="mt-4 p-3 bg-light rounded-3 border-start border-primary border-4">
                <h6 class="fw-bold mb-1 small text-uppercase text-primary">Trend Insight</h6>
                <p class="small text-muted mb-0">
                    This chart visualizes the daily revenue fluctuations for the selected period. 
                    Upward spikes typically correlate with peak delivery days or large office bulk orders.
                </p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark">Sales Segmentation</h5>
            <div class="mb-4" style="height: 200px;">
                <canvas id="segmentSplitChart"></canvas>
            </div>
            <hr>
            <div class="mt-2">
                <h6 class="fw-bold mb-3 small text-muted text-uppercase">Volume breakdown</h6>
                <div style="height: 180px;">
                    <canvas id="sourceSplitChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark">Detailed Transaction Logs</h5>
        <span class="badge bg-light text-dark border rounded-pill px-3">{{ $orders->count() }} Records</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted small text-uppercase fw-bold">
                <tr>
                    <th class="ps-4">Reference</th>
                    <th>Date</th>
                    <th>Client / Office</th>
                    <th>Type</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end pe-4">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td class="ps-4 fw-bold text-muted small">{{ $order->reference_number ?? '#' . str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="small">{{ $order->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="fw-bold text-dark">{{ $order->client_name }}</div>
                        <small class="text-muted">{{ $order->office ? $order->office->name : $order->customer_type }}</small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="badge {{ $order->is_refill ? 'bg-info bg-opacity-10 text-info' : 'bg-primary bg-opacity-10 text-primary' }} rounded-pill px-2 border-0 small">
                                {{ $order->is_refill ? 'Refill' : 'New Gallon' }}
                            </span>
                        </div>
                    </td>
                    <td class="text-end fw-bold text-dark">{{ $order->quantity }}</td>
                    <td class="text-end fw-bold text-primary pe-4">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (!document.getElementById('revenueTrendChart')) return;
        
        const primaryColor = '#4e73df';
        const successColor = '#1cc88a';
        const infoColor = '#36b9cc';
        const warningColor = '#f6c23e';

        new Chart(document.getElementById('revenueTrendChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($financialChartData['revenue_trend']['labels']) !!},
                datasets: [{
                    label: 'Daily Revenue',
                    data: {!! json_encode($financialChartData['revenue_trend']['data']) !!},
                    borderColor: primaryColor,
                    backgroundColor: primaryColor + '10',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: primaryColor,
                    pointBorderColor: '#fff',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [5, 5] }, ticks: { callback: v => '₱' + v.toLocaleString() } },
                    x: { grid: { display: false } }
                }
            }
        });

        new Chart(document.getElementById('segmentSplitChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($financialChartData['segment_split']['labels']) !!},
                datasets: [{
                    data: {!! json_encode($financialChartData['segment_split']['data']) !!},
                    backgroundColor: [primaryColor, infoColor],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
                }
            }
        });

        new Chart(document.getElementById('sourceSplitChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($financialChartData['source_split']['labels']) !!},
                datasets: [{
                    data: {!! json_encode($financialChartData['source_split']['data']) !!},
                    backgroundColor: [successColor, warningColor],
                    borderRadius: 10,
                    barThickness: 25
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, grid: { display: false } },
                    y: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endpush
