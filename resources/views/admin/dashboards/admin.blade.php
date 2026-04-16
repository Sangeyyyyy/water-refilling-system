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
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <div>
                <h2 class="fw-bold mb-0 text-dark">System Administrator</h2>
                <p class="text-muted mb-0">Total system oversight and operational management.</p>
            </div>
        </div>

        <div class="mb-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-speedometer2 me-2"></i>Strategic Monitoring</h5>
            @include('admin.partials.dashboard_stats')
            @include('admin.partials.dashboard_charts')
        </div>



</div>

<!-- Order Details Modal -->
@include('admin.partials.order_details_modal')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const PRICE_PER_GALLON = {{ $unitPrice }};

    // Charts Implementation
    document.addEventListener('DOMContentLoaded', function() {
        const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim() || '#4e73df';
        
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
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { callback: value => '₱' + value.toLocaleString() } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

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

    // History loading removed for Admin dash as requested
    function loadHistory() { /* Removed */ }

    function selectPickup(type, element) { /* Removed */ }

    // No tab persistence needed for single-view Admin dash
    document.addEventListener('DOMContentLoaded', updateTotal);
</script>
@endpush



@endsection
