@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 text-dark no-print">
        <div>
            <h2 class="fw-bold mb-0 text-dark">Unified Reports Hub</h2>
            <p class="text-muted mb-0">Centralized intelligence for financial, operational, and inventory tracking.</p>
        </div>
        <form action="{{ route('admin.reports.hub') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
            <input type="hidden" name="tab" value="{{ request('tab', 'overview') }}">
            <div class="input-group" style="width: auto;">
                <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3"><i class="bi bi-calendar3"></i></span>
                <input type="date" name="start_date" class="form-control border-start-0 shadow-none" value="{{ $startDate->format('Y-m-d') }}" style="min-width: 130px; max-width: 160px;">
                <input type="date" name="end_date" class="form-control border-start-0 rounded-end-pill pe-3 shadow-none" value="{{ $endDate->format('Y-m-d') }}" style="min-width: 130px; max-width: 160px;">
            </div>
            <button type="submit" class="btn btn-primary rounded-pill px-4 text-nowrap shadow-sm">Apply Filters</button>
            <button onclick="window.print()" class="btn btn-light border shadow-sm rounded-pill px-4 text-nowrap d-none" id="legacyPrintBtn" type="button">
                <i class="bi bi-printer me-2"></i>Print
            </button>
            <div class="dropdown">
                <button class="btn btn-light border shadow-sm rounded-pill px-4 text-nowrap dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-printer me-2"></i>Print Report
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                    <li>
                        <a class="dropdown-item py-2" target="_blank"
                           href="{{ route('admin.reports.print.financial', request()->only(['start_date','end_date'])) }}">
                            <i class="bi bi-cash-stack me-2 text-primary"></i>Financial Report
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2" target="_blank"
                           href="{{ route('admin.reports.print.operational', request()->only(['start_date','end_date'])) }}">
                            <i class="bi bi-truck me-2 text-success"></i>Operational Report
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2" target="_blank"
                           href="{{ route('admin.reports.print.inventory') }}">
                            <i class="bi bi-box-seam me-2 text-warning"></i>Inventory Report
                        </a>
                    </li>
                </ul>
            </div>

        </form>
    </div>

    <!-- Tab Navigation -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 no-print">
        <div class="card-body p-2">
            <ul class="nav nav-pills nav-fill gap-2" id="reportTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill py-2 {{ request('tab', 'overview') == 'overview' ? 'active' : '' }}" 
                            data-bs-toggle="pill" data-bs-target="#overview" type="button" role="tab">
                        <i class="bi bi-grid-fill me-2"></i>Overview
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill py-2 {{ request('tab') == 'financial' ? 'active' : '' }}" 
                            data-bs-toggle="pill" data-bs-target="#financial" type="button" role="tab">
                        <i class="bi bi-cash-stack me-2"></i>Financial
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill py-2 {{ request('tab') == 'operational' ? 'active' : '' }}" 
                            data-bs-toggle="pill" data-bs-target="#operational" type="button" role="tab">
                        <i class="bi bi-truck me-2"></i>Operations
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill py-2 {{ request('tab') == 'inventory' ? 'active' : '' }}" 
                            data-bs-toggle="pill" data-bs-target="#inventory" type="button" role="tab">
                        <i class="bi bi-box-seam me-2"></i>Inventory
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="reportTabsContent">
        <!-- Overview Tab -->
        <div class="tab-pane fade {{ request('tab', 'overview') == 'overview' ? 'show active' : '' }}" id="overview" role="tabpanel">
            @include('reports.partials.overview')
        </div>

        <!-- Financial Tab -->
        <div class="tab-pane fade {{ request('tab') == 'financial' ? 'show active' : '' }}" id="financial" role="tabpanel">
            @include('reports.partials.financial')
        </div>

        <!-- Operational Tab -->
        <div class="tab-pane fade {{ request('tab') == 'operational' ? 'show active' : '' }}" id="operational" role="tabpanel">
            @include('reports.partials.operational')
        </div>

        <!-- Inventory Tab -->
        <div class="tab-pane fade {{ request('tab') == 'inventory' ? 'show active' : '' }}" id="inventory" role="tabpanel">
            @include('reports.partials.inventory')
        </div>
    </div>
</div>

@push('styles')
<style>
    .nav-pills .nav-link {
        color: var(--bs-secondary);
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .nav-pills .nav-link.active {
        background-color: var(--bs-primary);
        color: white;
        box-shadow: 0 4px 15px rgba(78, 115, 223, 0.2);
    }
    .nav-pills .nav-link:hover:not(.active) {
        background-color: var(--bs-light);
    }
    
    @media print {
        .admin-sidebar, .admin-topbar, .no-print, .btn, .nav-pills { display: none !important; }
        .admin-content-wrapper { margin-left: 0 !important; padding: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #eee !important; page-break-inside: avoid; margin-bottom: 2rem !important; }
        .bg-light { background-color: #f8f9fa !important; border: 1px solid #ddd !important; -webkit-print-color-adjust: exact; }
        .tab-pane { display: block !important; opacity: 1 !important; visibility: visible !important; }
        .tab-content > .tab-pane { display: block !important; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Handle tab persistence in URL (optional but good for UX)
    document.addEventListener('DOMContentLoaded', function() {
        const triggerTabList = document.querySelectorAll('#reportTabs button');
        triggerTabList.forEach(triggerEl => {
            triggerEl.addEventListener('click', event => {
                const tabName = event.target.getAttribute('data-bs-target').replace('#', '');
                const url = new URL(window.location);
                url.searchParams.set('tab', tabName);
                window.history.replaceState({}, '', url);
                
                // Update hidden input in filter form
                document.querySelector('input[name="tab"]').value = tabName;
            });
        });
    });
</script>
@endpush
@endsection
