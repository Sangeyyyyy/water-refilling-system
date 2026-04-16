@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-0 text-dark">Executive Overview</h2>
                <p class="text-muted mb-0">Welcome back, Director {{ Auth::user()->last_name }}. Here is the system's strategic performance.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.reports.hub') }}" class="btn btn-primary shadow-sm">
                    <i class="bi bi-file-earmark-bar-graph me-2"></i>Reports Hub
                </a>
            </div>
        </div>

        <!-- Strategic Monitoring Content -->
        <div id="monitoring-content" class="animate-fade-in">
            @include('admin.partials.dashboard_stats')
            @include('admin.partials.dashboard_charts')
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Charts Implementation
    document.addEventListener('DOMContentLoaded', function() {
        const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim() || '#4e73df';
        const accentColor = getComputedStyle(document.documentElement).getPropertyValue('--accent-color').trim() || '#1cc88a';

        // Sales Trend Chart
        const salesCtx = document.getElementById('salesTrendChart')?.getContext('2d');
        if (salesCtx) {
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($dashboardData['sales_trend']['labels']) !!},
                    datasets: [{
                        label: 'Daily Revenue',
                        data: {!! json_encode($dashboardData['sales_trend']['data']) !!},
                        borderColor: primaryColor,
                        backgroundColor: primaryColor + '15',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: primaryColor,
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { callback: value => '₱' + value.toLocaleString() } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // Campus Consumption Chart
        const campusCtx = document.getElementById('campusConsumptionChart')?.getContext('2d');
        if (campusCtx) {
            new Chart(campusCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($dashboardData['campus_consumption']['labels']) !!},
                    datasets: [{
                        data: {!! json_encode($dashboardData['campus_consumption']['data']) !!},
                        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: { legend: { display: false } }
                }
            });
        }
    });
</script>
@endpush

@endsection
