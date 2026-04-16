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
                    <ul class="mb-0 ps-3">
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
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-0 text-dark">Staff Dashboard</h2>
                <p class="text-muted mb-0">Track today's orders and manage deliveries.</p>
            </div>
            <div class="d-flex flex-grow-1 flex-md-grow-0 gap-2">
                <button type="button" class="btn btn-light border shadow-sm flex-fill px-md-3" data-bs-toggle="offcanvas" data-bs-target="#historyDrawer" onclick="loadHistory()">
                    <i class="bi bi-clock-history me-1"></i> Recent History
                </button>
                <button type="button" class="btn btn-primary shadow-sm flex-fill px-md-3 text-nowrap" data-bs-toggle="modal" data-bs-target="#walkInModal">
                    <i class="bi bi-plus-lg me-1"></i> New Order
                </button>
            </div>
        </div>

        <!-- Staff Operational View -->
        @include('admin.partials.dashboard_stats')
        @include('admin.partials.staff_queues')
    </div>
</div>

<!-- Order Details Modal -->
@include('admin.partials.order_details_modal')
<!-- Walk-in Modal Partial -->
@include('admin.partials.walkin_modal')

@push('scripts')
<script>
    const PRICE_PER_GALLON = {{ $unitPrice }};

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

    document.addEventListener('DOMContentLoaded', () => {
        updateTotal();
        
        @if($errors->any())
            const walkInModalElement = document.getElementById('walkInModal');
            if (walkInModalElement) {
                const walkInModal = new bootstrap.Modal(walkInModalElement);
                walkInModal.show();
            }
        @endif
    });
</script>
@endpush

<!-- History Drawer -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="historyDrawer" style="width: 700px; max-width: 90vw;">
    <div class="offcanvas-header bg-light border-bottom">
        <h5 class="offcanvas-title fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Recent History</h5>
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
