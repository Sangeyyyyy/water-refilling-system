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

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <div>
                @if(in_array(auth()->user()->role, ['staff', 'manager', 'admin']))
                    <h2 class="fw-bold mb-0 text-dark">{{ auth()->user()->role === 'staff' ? 'Staff Dashboard' : 'Operational Hub' }}</h2>
                    <p class="text-muted mb-0">Manage orders, refills, and deliveries.</p>
                @elseif(auth()->user()->role === 'director')
                    <h2 class="fw-bold mb-0 text-dark">Executive Overview</h2>
                    <p class="text-muted mb-0">Welcome back, Director {{ Auth::user()->last_name }}. Here is the system's strategic performance.</p>
                @endif
            </div>
            <div class="d-flex flex-grow-1 flex-md-grow-0 gap-2">
                @if(in_array(auth()->user()->role, ['staff', 'manager', 'admin']))
                    <button type="button" class="btn btn-light border shadow-sm flex-fill px-md-3" data-bs-toggle="offcanvas" data-bs-target="#historyDrawer" onclick="loadHistory()">
                        <i class="bi bi-clock-history me-1"></i> History
                    </button>
                    <button type="button" class="btn btn-primary shadow-sm flex-fill px-md-3 text-nowrap" data-bs-toggle="modal" data-bs-target="#walkInModal">
                        <i class="bi bi-plus-lg me-1"></i> New Order
                    </button>
                @endif
                @if(!in_array(auth()->user()->role, ['staff']))
                <a href="{{ route('admin.reports.hub') }}" class="btn btn-light border shadow-sm">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Generate Reports
                </a>
                @endif
            </div>
        </div>

        @if(in_array(auth()->user()->role, ['admin', 'manager', 'director']))
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
        @endif
    </div>

    @if(in_array(auth()->user()->role, ['admin', 'manager', 'director']))
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
    @else
        {{-- Staff only view: Directly show operations --}}
        @include('admin.partials.dashboard_stats')
        @include('admin.partials.staff_queues')
    @endif

    {{-- Redundant general table hidden in favor of operational queues --}}
    {{-- @include('admin.partials.orders_table') --}}

<!-- Order Details Modal (Same as home.blade.php for consistency) -->
@include('admin.partials.order_details_modal')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const PRICE_PER_GALLON = {{ $unitPrice }};

    // Charts Implementation
    @if(in_array(auth()->user()->role, ['admin', 'director', 'manager']))
    document.addEventListener('DOMContentLoaded', function() {
        const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim() || '#4e73df';
        const accentColor = getComputedStyle(document.documentElement).getPropertyValue('--accent-color').trim() || '#1cc88a';

        // Sales Trend Chart
        const salesCtx = document.getElementById('salesTrendChart').getContext('2d');
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
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { callback: value => '₱' + value.toLocaleString() }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        // Campus Consumption Chart
        const campusCtx = document.getElementById('campusConsumptionChart').getContext('2d');
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
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
    @endif

    // History Drawer Loading
    function loadHistory(url = "{{ route('admin.history') }}") {
        const container = document.getElementById('historyContent');
        container.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted small">Fetching history...</p>
            </div>
        `;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;

            // Re-attach pagination handlers
            const paginationLinks = container.querySelectorAll('.pagination a');
            paginationLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    loadHistory(this.href);
                });
            });
        })
        .catch(error => {
            container.innerHTML = `
                <div class="alert alert-danger m-3" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i> Failed to load history.
                </div>
            `;
        });
    }

    function applyOffcanvasFilters() {
        const form = document.getElementById('offcanvasFilterForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        const url = `{{ route('admin.history') }}?${params.toString()}`;
        loadHistory(url);
    }

    function resetOffcanvasFilters() {
        const form = document.getElementById('offcanvasFilterForm');
        form.reset();
        loadHistory();
    }

    function handleDateRangeChange(select) {
        const form = select.closest('form');
        const dateFromContainer = form.querySelector('[id^="date_from_container"]')?.closest('.col-lg-2') || document.getElementById('date_from_container');
        const dateToContainer = form.querySelector('[id^="date_to_container"]')?.closest('.col-lg-2') || document.getElementById('date_to_container');

        if (select.value === 'custom') {
            // Show custom date fields but don't submit yet
            if (dateFromContainer) dateFromContainer.style.display = 'block';
            if (dateToContainer) dateToContainer.style.display = 'block';
        } else {
            // Hide custom fields and auto-submit for preset ranges
            if (dateFromContainer) dateFromContainer.style.display = 'none';
            if (dateToContainer) dateToContainer.style.display = 'none';
            select.form.submit();
        }
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

        const tabButtons = document.querySelectorAll('#managerDashboardTabs button[data-bs-toggle="tab"]');
        tabButtons.forEach(btn => {
            btn.addEventListener('shown.bs.tab', function(e) {
                localStorage.setItem('activeDashboardTab', e.target.getAttribute('data-bs-target'));
            });
        });
    });


    // Walk-in Modal Scripts
    function adjustQty(amount) {
        const input = document.getElementById('walkin_quantity');
        let val = parseInt(input.value) + amount;
        if (isNaN(val) || val < 1) val = 1;
        input.value = val;
        updateTotal();
    }

    function updateTotal() {
        const input = document.getElementById('walkin_quantity');
        const display = document.getElementById('walkin_total');
        let val = parseInt(input.value);
        if (isNaN(val) || val < 1) val = 1;

        const total = val * PRICE_PER_GALLON;
        display.innerText = total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function selectPickup(type, element) {
        document.getElementById('pickup_type').value = type;
        document.querySelectorAll('.slot-option').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
    }


    // Batch Selection Logic
    const selectAllCheckbox = document.getElementById('selectAllPending');
    const orderCheckboxes = document.querySelectorAll('.order-checkbox');
    const batchBar = document.getElementById('batchActionBar');
    const selectedCountSpan = document.getElementById('selectedCount');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            orderCheckboxes.forEach(cb => cb.checked = this.checked);
            updateBatchBar();
        });
    }

    orderCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBatchBar);
    });

    function updateBatchBar() {
        const checkedCount = document.querySelectorAll('.order-checkbox:checked').length;
        selectedCountSpan.innerText = checkedCount;

        if (checkedCount > 0) {
            batchBar.style.display = 'block';
        } else {
            batchBar.style.display = 'none';
        }
    }

    function clearBatch() {
        if (selectAllCheckbox) selectAllCheckbox.checked = false;
        orderCheckboxes.forEach(cb => cb.checked = false);
        updateBatchBar();
    }

    function singleDispatch(id) {
        showConfirmModal('Dispatch Order?', 'Are you sure you want to dispatch this order?', () => {
            const form = document.getElementById('batchDispatchForm');
            form.action = "{{ route('admin.orders.batch-status') }}";
            document.querySelector('#batchDispatchForm input[name="status"]').value = "out_for_delivery";

            // Uncheck all first
            document.querySelectorAll('.order-checkbox').forEach(cb => cb.checked = false);
            // Check only the requested one
            const cb = document.querySelector(`.order-checkbox[value="${id}"]`);
            if (cb) cb.checked = true;

            // Bypass the batch alert and submit directly
            form.submit();
        }, 'confirm');
    }

    function singleComplete(id) {
        showConfirmModal('Complete Walk-in?', 'Mark this walk-in order as completed?', () => {
            const form = document.getElementById('batchDispatchForm');
            // Change the form target to the single update route
            form.action = `/admin/orders/${id}/status`;
            document.querySelector('#batchDispatchForm input[name="status"]').value = "completed";

            // Uncheck all first
            document.querySelectorAll('.order-checkbox').forEach(cb => cb.checked = false);
            // Check only the requested one
            const cb = document.querySelector(`.order-checkbox[value="${id}"]`);
            if (cb) cb.checked = true;

            form.submit();
        }, 'success');
    }

    function submitBatchDispatch() {
        const checkedCount = document.querySelectorAll('.order-checkbox:checked').length;
        showConfirmModal('Batch Dispatch?', `Are you sure you want to dispatch ${checkedCount} orders?`, () => {
            document.getElementById('batchDispatchForm').submit();
        }, 'confirm');
    }

    let confirmCallback = null;
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmActionModal'));

    function showConfirmModal(title, message, callback, type = 'confirm') {
        document.getElementById('confirmModalTitle').innerText = title;
        document.getElementById('confirmModalMessage').innerText = message;
        
        const iconContainer = document.getElementById('confirmModalIcon');
        const confirmBtn = document.getElementById('confirmModalActionBtn');
        
        // Reset classes
        iconContainer.className = 'rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto';
        confirmBtn.className = 'btn px-4 fw-bold';
        
        if (type === 'success') {
            iconContainer.classList.add('bg-success', 'bg-opacity-10', 'text-success');
            iconContainer.innerHTML = '<i class="bi bi-check2-circle fs-1"></i>';
            confirmBtn.classList.add('btn-success');
            confirmBtn.innerText = 'Complete';
        } else {
            iconContainer.classList.add('bg-primary', 'bg-opacity-10', 'text-primary');
            iconContainer.innerHTML = '<i class="bi bi-question-circle fs-1"></i>';
            confirmBtn.classList.add('btn-primary');
            confirmBtn.innerText = 'Confirm';
        }
        
        confirmCallback = callback;
        confirmModal.show();
    }

    document.getElementById('confirmModalActionBtn').addEventListener('click', function() {
        if (confirmCallback) {
            confirmCallback();
            confirmModal.hide();
        }
    });

    // Initialize total on load
    document.addEventListener('DOMContentLoaded', updateTotal);

    // Batch Print logic
    const selectAllDeliveries = document.getElementById('selectAllDeliveries');
    const batchPrintBtn = document.getElementById('batchPrintBtn');
    const printSelectedCount = document.getElementById('printSelectedCount');

    if (selectAllDeliveries) {
        selectAllDeliveries.addEventListener('change', function() {
            const deliveryCheckboxes = document.querySelectorAll('.delivery-checkbox');
            deliveryCheckboxes.forEach(cb => cb.checked = this.checked);
            updateBatchPrintUI();
        });

        // Delegate listener for individual checkboxes (Attached to document for robustness)
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('delivery-checkbox')) {
                updateBatchPrintUI();

                // Update select all state
                const allChecked = document.querySelectorAll('.delivery-checkbox:checked').length === document.querySelectorAll('.delivery-checkbox').length;
                if (selectAllDeliveries) selectAllDeliveries.checked = allChecked;
            }
        });
    }

    function updateBatchPrintUI() {
        const checkedCount = document.querySelectorAll('.delivery-checkbox:checked').length;
        const printBar = document.getElementById('batchPrintActionBar');
        const printBarCount = document.getElementById('printBarCount');

        if (checkedCount > 0) {
            printBar.style.display = 'block';
            printBarCount.textContent = checkedCount;
        } else {
            printBar.style.display = 'none';
            if (selectAllDeliveries) selectAllDeliveries.checked = false;
        }
    }

    function clearPrintBatch() {
        if (selectAllDeliveries) selectAllDeliveries.checked = false;
        document.querySelectorAll('.delivery-checkbox').forEach(cb => cb.checked = false);
        updateBatchPrintUI();
    }

    function batchPrintReceipts() {
        const checkedBoxes = document.querySelectorAll('.delivery-checkbox:checked');
        if (checkedBoxes.length === 0) return;

        if (checkedBoxes.length > 4) {
            alert('You can only print a maximum of 4 receipts at a time to fit on one A4 page.');
            return;
        }

        const ids = Array.from(checkedBoxes).map(cb => cb.value).join(',');
        window.open(`/reports/delivery-receipts/batch?ids=${ids}`, '_blank');

        // Optionally clear selection after printing to hide the bar
        // clearPrintBatch();
    }

    function setAdjustModal(name, id, stock, threshold) {
        document.getElementById('inventory_item_name').innerText = name;
        document.getElementById('inventory_id').value = id;
        document.getElementById('stock_level_display').value = stock;
        document.getElementById('low_stock_threshold').value = threshold;

        // Correct the form action URL
        const form = document.getElementById('adjustStockForm');
        form.action = `/inventory/${id}`;
    }
</script>

<style>
    /* Custom Pills Styling */
    .custom-pills .nav-link {
        background: #fff;
        color: #6c757d;
        border-color: #eee !important;
        font-size: 0.9rem;
    }
    .custom-pills .nav-link.active {
        background: var(--bs-primary) !important;
        color: #fff !important;
        border-color: var(--bs-primary) !important;
    }
    .custom-pills .nav-link:hover:not(.active) {
        background: #f8f9fa;
        border-color: #ddd !important;
    }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

    .animate-slide-up {
        animation: slideUp 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    @keyframes slideUp {
        from { transform: translateY(100px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .cursor-pointer { cursor: pointer; }

    /* Card Flip Table for Mobile */
    @media (max-width: 768px) {
        .responsive-table tr:not(.group-header) {
            display: flex;
            flex-direction: column;
            margin-bottom: 0.75rem;
            border: 1px solid #eee !important;
            border-radius: 12px;
            padding: 0.5rem;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .responsive-table thead { display: none; }
        .responsive-table tr:not(.group-header) td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: none !important;
            padding: 0.4rem 0.5rem !important;
            text-align: right;
            width: 100%;
        }
        .responsive-table tr:not(.group-header) td::before {
            content: attr(data-label);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.65rem;
            color: #888;
            margin-right: auto;
            text-align: left;
        }
        .responsive-table td:last-child {
            justify-content: flex-end;
            border-top: 1px solid #eee !important;
            margin-top: 0.5rem;
            padding-top: 0.75rem !important;
        }
    }
</style>
@endpush

<!-- Generic Confirmation Modal -->
<div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-body p-4 text-center">
                <div id="confirmModalIcon" class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 64px; height: 64px;">
                    <i class="bi bi-question-circle fs-1"></i>
                </div>
                <h5 class="fw-bold mb-2" id="confirmModalTitle">Confirm Action</h5>
                <p class="text-muted mb-4" id="confirmModalMessage">Are you sure you want to proceed with this action?</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light border-0 text-muted fw-medium px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmModalActionBtn" class="btn btn-primary px-4 fw-bold" style="min-width: 120px;">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- History Offcanvas Drawer -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="historyDrawer" aria-labelledby="historyDrawerLabel" style="width: 700px; max-width: 90vw;">
    <div class="offcanvas-header bg-light border-bottom">
        <h5 class="offcanvas-title fw-bold" id="historyDrawerLabel">
            <i class="bi bi-clock-history me-2 text-primary"></i>Order History
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div id="historyContent">
            <!-- Content loaded via AJAX -->
        </div>
    </div>
    <div class="offcanvas-footer p-3 border-top bg-light">
        <a href="{{ route('admin.history') }}" class="btn btn-sm btn-outline-primary w-100">
            View Full History Page <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
</div>

<!-- Order Details Modal (Same as home.blade.php for consistency) -->
@include('admin.partials.order_details_modal')

<!-- Walk-in Modal Partial -->
@include('admin.partials.walkin_modal')

<!-- FLOATING ACTION BARS (Placed at root for visibility) -->
<!-- Batch Refill Bar -->
<div id="batchActionBar" class="position-fixed bottom-0 start-50 translate-middle-x mb-4 animate-slide-up" style="display: none; min-width: 380px; z-index: 9999;">
    <div class="glass-card bg-white p-3 shadow-lg border d-flex justify-content-between align-items-center" style="border-radius: 16px;">
        <div class="d-flex align-items-center ps-2">
            <span class="badge bg-primary rounded-pill me-2" id="selectedCount">0</span>
            <span class="small fw-bold text-dark">Orders Selected</span>
        </div>
        <div class="d-flex gap-3 pe-2 ms-4">
            <button type="button" class="btn btn-sm btn-light border px-3" onclick="clearBatch()">Cancel</button>
            <button type="button" class="btn btn-sm btn-primary px-3 fw-bold shadow-sm" onclick="submitBatchDispatch()">
                <i class="bi bi-check-circle me-1"></i> Mark as Refilled
            </button>
        </div>
    </div>
</div>

<!-- Batch Print Bar -->
<div id="batchPrintActionBar" class="position-fixed bottom-0 start-50 translate-middle-x mb-4 animate-slide-up" style="display: none; min-width: 380px; z-index: 9999;">
    <div class="glass-card bg-white p-3 shadow-lg border d-flex justify-content-between align-items-center" style="border-radius: 16px;">
        <div class="d-flex align-items-center ps-2">
            <span class="badge bg-primary rounded-pill me-2" id="printBarCount">0</span>
            <span class="small fw-bold text-dark">Deliveries Selected</span>
        </div>
        <div class="d-flex gap-3 pe-2 ms-4">
            <button type="button" class="btn btn-sm btn-light border px-3" onclick="clearPrintBatch()">Cancel</button>
            <button type="button" class="btn btn-sm btn-primary px-3 fw-bold shadow-sm" onclick="batchPrintReceipts()">
                <i class="bi bi-printer-fill me-1"></i> Print Receipts
            </button>
        </div>
    </div>
</div>
</div>

@endsection
