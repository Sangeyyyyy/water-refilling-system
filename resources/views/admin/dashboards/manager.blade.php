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
                <h2 class="fw-bold mb-0 text-dark">Operational Hub</h2>
                <p class="text-muted mb-0">Manage orders, refills, and deliveries.</p>
            </div>
            <div class="d-flex flex-grow-1 flex-md-grow-0 gap-2">
                <button type="button" class="btn btn-light border shadow-sm flex-fill px-md-3" data-bs-toggle="offcanvas" data-bs-target="#historyDrawer" onclick="loadHistory()">
                    <i class="bi bi-clock-history me-1"></i> History
                </button>
                <button type="button" class="btn btn-primary shadow-sm flex-fill px-md-3 text-nowrap" data-bs-toggle="modal" data-bs-target="#walkInModal">
                    <i class="bi bi-plus-lg me-1"></i> New Order
                </button>
                <a href="{{ route('admin.reports.hub') }}" class="btn btn-light border shadow-sm">
                    <i class="bi bi-file-earmark-bar-graph me-2"></i>Reports Hub
                </a>
            </div>
        </div>

        <div class="d-flex border-bottom mb-4 overflow-auto scrollbar-hide">
            <ul class="nav nav-pills custom-pills gap-2 py-1" id="managerDashboardTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill px-4 fw-bold shadow-sm border transition-all" id="monitoring-tab" data-bs-toggle="tab" data-bs-target="#monitoring-pane" type="button" role="tab" aria-controls="monitoring-pane" aria-selected="true">
                        <i class="bi bi-speedometer2 me-2"></i>Monitoring
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-4 fw-bold shadow-sm border transition-all" id="operations-tab" data-bs-toggle="tab" data-bs-target="#operations-pane" type="button" role="tab" aria-controls="operations-pane" aria-selected="false">
                        <i class="bi bi-truck me-2"></i>Operations
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <div class="tab-content" id="dashboardTabContent">
        <!-- Strategic Monitoring Tab -->
        <div class="tab-pane fade show active" id="monitoring-pane" role="tabpanel" aria-labelledby="monitoring-tab" tabindex="0">
            @include('admin.partials.dashboard_stats')
            @include('admin.partials.dashboard_charts')
        </div>

        <!-- Operational Hub Tab -->
        <div class="tab-pane fade" id="operations-pane" role="tabpanel" aria-labelledby="operations-tab" tabindex="0">
            @include('admin.partials.staff_queues')
        </div>
    </div>

</div>

<!-- Order Details Modal -->
@include('admin.partials.order_details_modal')
<!-- Walk-in Modal Partial -->
@include('admin.partials.walkin_modal')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const PRICE_PER_GALLON = {{ $unitPrice }};

    // Charts Implementation
    document.addEventListener('DOMContentLoaded', function() {
        const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim() || '#4e73df';
        
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
                        y: { beginAtZero: true, ticks: { callback: value => '₱' + value.toLocaleString() } },
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

    // History Drawer Loading
    function loadHistory(url = "{{ route('admin.history') }}") {
        const container = document.getElementById('historyContent');
        if (!container) return;
        container.innerHTML = `<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted small">Fetching history...</p></div>`;

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;
            const paginationLinks = container.querySelectorAll('.pagination a');
            paginationLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    loadHistory(this.href);
                });
            });
        });
    }

    // Walk-in total logic
    function updateTotal() {
        const input = document.getElementById('walkin_quantity');
        const display = document.getElementById('walkin_total');
        if (!input || !display) return;
        let val = parseInt(input.value) || 1;
        display.innerText = (val * PRICE_PER_GALLON).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function adjustQty(amount) {
        const input = document.getElementById('walkin_quantity');
        if (!input) return;
        let val = (parseInt(input.value) || 0) + amount;
        input.value = val < 1 ? 1 : val;
        updateTotal();
    }

    function selectPickup(type, element) {
        const input = document.getElementById('pickup_type');
        if (!input) return;
        input.value = type;
        document.querySelectorAll('.slot-option').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
    }

    // Tab Persistence
    document.addEventListener('DOMContentLoaded', function() {
        const activeTab = localStorage.getItem('activeDashboardTab');
        if (activeTab) {
            const tabEl = document.querySelector(`#managerDashboardTabs button[data-bs-target="${activeTab}"]`);
            if (tabEl) {
                const tab = new bootstrap.Tab(tabEl);
                tab.show();
            }
        }
        document.querySelectorAll('#managerDashboardTabs button[data-bs-toggle="tab"]').forEach(btn => {
            btn.addEventListener('shown.bs.tab', e => localStorage.setItem('activeDashboardTab', e.target.getAttribute('data-bs-target')));
        });
    });


    document.addEventListener('DOMContentLoaded', updateTotal);
</script>

<style>
    .custom-pills .nav-link { background: #fff; color: #6c757d; border-color: #eee !important; font-size: 0.9rem; }
    .custom-pills .nav-link.active { background: var(--bs-primary) !important; color: #fff !important; border-color: var(--bs-primary) !important; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush

<!-- History Drawer -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="historyDrawer" style="width: 700px; max-width: 90vw;">
    <div class="offcanvas-header bg-light border-bottom">
        <h5 class="offcanvas-title fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Order History</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div id="historyContent"></div>
    </div>
    <div class="offcanvas-footer p-3 border-top bg-light">
        <a href="{{ route('admin.history') }}" class="btn btn-sm btn-outline-primary w-100">View Full History <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
</div>

@include('admin.partials.order_status_scripts')
@endsection
