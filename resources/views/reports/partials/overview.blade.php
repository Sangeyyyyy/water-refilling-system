@php
    $revTrend = $overviewTrends['revenue'] ?? 0;
    $trendLabel = $overviewTrends['period_label'] ?? 'vs last month';
@endphp

<!-- KPI Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-primary text-white h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="text-white-50 text-uppercase small fw-bold mb-0">Total Orders</h6>
                <i class="bi bi-cart-fill fs-4"></i>
            </div>
            <h3 class="fw-bold mb-0">{{ number_format($stats['total_orders']) }}</h3>
            <span class="small text-white-50">System-wide count</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="text-muted text-uppercase small fw-bold mb-0">Monthly Revenue</h6>
                <i class="bi bi-currency-dollar text-success fs-4"></i>
            </div>
            <h3 class="fw-bold mb-0 text-dark">₱{{ number_format($stats['month_revenue'], 2) }}</h3>
            <div class="mt-2 d-flex align-items-center">
                <span class="badge {{ $revTrend >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 text-{{ $revTrend >= 0 ? 'success' : 'danger' }} rounded-pill px-2 small">
                    <i class="bi bi-arrow-{{ $revTrend >= 0 ? 'up' : 'down' }}-right me-1"></i>
                    {{ number_format(abs($revTrend), 1) }}%
                </span>
                <span class="small text-muted ms-2">{{ $trendLabel }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="text-muted text-uppercase small fw-bold mb-0">Total Employees</h6>
                <i class="bi bi-person-badge text-info fs-4"></i>
            </div>
            <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_clients']) }}</h3>

        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="text-muted text-uppercase small fw-bold mb-0">Total Users</h6>
                <i class="bi bi-people-fill text-warning fs-4"></i>
            </div>
            <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_users']) }}</h3>
            <span class="small text-muted">Staff & Administrators</span>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark">Recent Order Activity (30 Days)</h5>
            <div style="height: 350px;">
                <canvas id="orderTrendChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold mb-4 text-dark">Order Status Distribution</h5>
            <div style="height: 300px;">
                <canvas id="statusDistributionChart"></canvas>
            </div>
            <div class="mt-4 p-3 bg-light rounded-3 border-start border-primary border-4">
                <h6 class="fw-bold mb-1 small text-uppercase text-primary">System Health</h6>
                <p class="small text-muted mb-0">Overview of operational efficiency and fulfillment rates across the entire platform.</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (!document.getElementById('orderTrendChart')) return;

        new Chart(document.getElementById('orderTrendChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($adminChartData['order_trend']['labels']) !!},
                datasets: [{
                    label: 'Orders',
                    data: {!! json_encode($adminChartData['order_trend']['data']) !!},
                    borderColor: '#4e73df',
                    backgroundColor: '#4e73df10',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

        new Chart(document.getElementById('statusDistributionChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($adminChartData['status_distribution']['labels']) !!},
                datasets: [{
                    data: {!! json_encode($adminChartData['status_distribution']['data']) !!},
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'],
                    borderWidth: 0
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
    });
</script>
@endpush
