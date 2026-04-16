@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark">System Reports Center</h2>
            <p class="text-muted mb-0">High-level system health and activity monitoring.</p>
        </div>
        <div class="d-flex gap-3">
            <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="bi bi-download me-2"></i>Export Raw Data
            </button>
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill d-flex align-items-center">
                <i class="bi bi-shield-check me-2"></i>Admin Access
            </span>
        </div>
    </div>

    <!-- Admin KPI Row -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 border-bottom border-primary border-5">
                <h6 class="text-muted text-uppercase small fw-bold mb-3">Total Orders</h6>
                <h3 class="fw-bold mb-1 text-dark">{{ number_format($stats['total_orders']) }}</h3>
                <span class="small text-muted">All-time life volume</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 border-bottom border-success border-5">
                <h6 class="text-muted text-uppercase small fw-bold mb-3">Revenue (MTD)</h6>
                <h3 class="fw-bold mb-1 text-dark">₱{{ number_format($stats['month_revenue'], 2) }}</h3>
                <span class="small text-muted">Current month sales</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 border-bottom border-info border-5">
                <h6 class="text-muted text-uppercase small fw-bold mb-3">Internal Users</h6>
                <h3 class="fw-bold mb-1 text-dark">{{ number_format($stats['total_users']) }}</h3>
                <span class="small text-muted">Staff, Managers, Directors</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 border-bottom border-warning border-5">
                <h6 class="text-muted text-uppercase small fw-bold mb-3">External Clients</h6>
                <h3 class="fw-bold mb-1 text-dark">{{ number_format($stats['total_clients']) }}</h3>
                <span class="small text-muted">Registered unit accounts</span>
            </div>
        </div>
    </div>

    <!-- Tabs Content -->
    <ul class="nav nav-pills mb-4 gap-2 p-1 bg-light rounded-4 d-inline-flex" id="adminReportTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 py-2 fw-bold" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">System Overview</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 py-2 fw-bold" id="segmentation-tab" data-bs-toggle="tab" data-bs-target="#segmentation" type="button" role="tab">Segmentation & Roles</button>
        </li>
    </ul>

    <div class="tab-content" id="adminReportTabsContent">
        <!-- Overview Tab -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel">
            <div class="row g-4 mb-5">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold mb-4">Total Order Volume Trend <small class="text-muted fw-normal ms-2">(Last 30 Days)</small></h5>
                        <div style="height: 350px;">
                            <canvas id="orderTrendChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold mb-4">Order Status Distribution</h5>
                        <div style="height: 250px;">
                            <canvas id="statusDistributionChart"></canvas>
                        </div>
                        <div class="mt-4">
                            @foreach($chartData['status_distribution']['labels'] as $index => $label)
                            <div class="d-flex justify-content-between align-items-center small mb-2 p-2 rounded-3 bg-light bg-opacity-50">
                                <span><i class="bi bi-circle-fill me-2" style="color: {{ ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'][$index % 6] }}; font-size: 0.6rem;"></i>{{ $label }}</span>
                                <span class="fw-bold">{{ number_format($chartData['status_distribution']['data'][$index]) }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Segmentation Tab -->
        <div class="tab-pane fade" id="segmentation" role="tabpanel">
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold mb-4">By Customer Type</h5>
                        <div style="height: 200px;">
                            <canvas id="customerTypeChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold mb-4">By Order Channel</h5>
                        <div style="height: 200px;">
                            <canvas id="orderTypeChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold mb-4">User Roles Count</h5>
                        <div class="role-list">
                            @foreach($chartData['user_roles'] as $role)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted fw-bold">{{ ucfirst($role->role) }}s</span>
                                <span class="badge bg-primary rounded-pill px-3">{{ $role->count }}</span>
                            </div>
                            @endforeach
                        </div>
                        <hr>
                        <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-primary w-100 rounded-pill">Manage Users</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links Row -->
    <div class="mb-5">
        <h5 class="fw-bold text-dark mb-4 d-flex align-items-center">
            <span class="bg-primary p-1 rounded-2 me-2" style="width: 8px; height: 24px; display: inline-block;"></span>
            Available System Downloads
        </h5>
        <div class="row g-4">
            <div class="col-md-4">
                <a href="{{ route('admin.reports.hub', ['tab' => 'financial']) }}" class="card border-0 shadow-sm rounded-4 text-decoration-none hover-lift transition-all">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-4 me-3">
                            <i class="bi bi-file-earmark-spreadsheet fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Financial Snapshot</h6>
                            <small class="text-muted small">Current month billing audit</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('admin.logs') }}" class="card border-0 shadow-sm rounded-4 text-decoration-none hover-lift transition-all">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 text-info p-3 rounded-4 me-3">
                            <i class="bi bi-journal-text fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">System Activity Logs</h6>
                            <small class="text-muted small">Audit trails & actions</small>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal (Shared) -->
@include('admin.reports.partials.export_modal')

<!-- Charts Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Shared Config
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        };

        // Order Trend Chart
        const trendCtx = document.getElementById('orderTrendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData['order_trend']['labels']) !!},
                datasets: [{
                    label: 'Daily Orders',
                    data: {!! json_encode($chartData['order_trend']['data']) !!},
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4e73df',
                }]
            },
            options: {
                ...chartOptions,
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { borderDash: [5, 5] }, ticks: { stepSize: 1 } }
                }
            }
        });

        // Status Distribution
        const statusCtx = document.getElementById('statusDistributionChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartData['status_distribution']['labels']) !!},
                datasets: [{
                    data: {!! json_encode($chartData['status_distribution']['data']) !!},
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                ...chartOptions,
                cutout: '70%'
            }
        });

        // Customer Type
        const customerCtx = document.getElementById('customerTypeChart').getContext('2d');
        new Chart(customerCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($chartData['customer_type']['labels']) !!},
                datasets: [{
                    data: {!! json_encode($chartData['customer_type']['data']) !!},
                    backgroundColor: ['#4e73df', '#1cc88a']
                }]
            },
            options: chartOptions
        });

        // Order Type
        const typeCtx = document.getElementById('orderTypeChart').getContext('2d');
        new Chart(typeCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($chartData['order_type']['labels']) !!},
                datasets: [{
                    data: {!! json_encode($chartData['order_type']['data']) !!},
                    backgroundColor: ['#36b9cc', '#f6c23e']
                }]
            },
            options: chartOptions
        });
    });
</script>

<style>
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.1) !important;
    }
    .transition-all { transition: all 0.3s ease; }
    .nav-pills .nav-link.active { background-color: #4e73df; }
    .nav-pills .nav-link { color: #858796; }
</style>
@endsection
