@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 animate-fade-in">
        <div>
            <h2 class="fw-bold mb-0 text-dark">System Activity Logs</h2>
            <p class="text-muted mb-0">Track all critical actions and system events for auditing.</p>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card border-0 shadow-sm mb-4 animate-fade-in">
        <div class="card-body">
            <form action="{{ route('admin.logs') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Search</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0 ps-0" placeholder="Search user or description..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Action Type</label>
                    <select name="action" class="form-select bg-light">
                        <option value="">All Actions</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ $action }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Date From</label>
                    <input type="date" name="date_from" class="form-control bg-light" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Date To</label>
                    <input type="date" name="date_to" class="form-control bg-light" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel"></i> Filter</button>
                    <a href="{{ route('admin.logs') }}" class="btn btn-light"><i class="bi bi-arrow-counterclockwise"></i></a>
                    <button type="submit" name="export" value="1" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Export</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table Card -->
    <div class="card border-0 shadow-sm animate-fade-in" style="animation-delay: 0.1s;">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-primary">Activity History</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="logsTable">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4" style="width: 20%;">Timestamp</th>
                        <th style="width: 15%;">User</th>
                        <th style="width: 15%;">Action</th>
                        <th style="width: 35%;">Description</th>
                        <th class="text-end pe-4" style="width: 15%;">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark">{{ $log->created_at->format('M d, Y') }}</span>
                                <small class="text-muted">{{ $log->created_at->format('h:i A') }} ({{ $log->created_at->diffForHumans() }})</small>
                            </div>
                        </td>
                        <td>
                            @if($log->user)
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-2 small fw-bold">
                                        {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold small">{{ $log->user->name }}</span>
                                        <span class="badge bg-light text-dark border extra-small fw-normal">{{ ucfirst($log->user->role) }}</span>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted fst-italic small">System / Guest</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $badgeClass = 'bg-light text-dark border';
                                if (str_contains($log->action, 'Created') || str_contains($log->action, 'Added')) $badgeClass = 'bg-success bg-opacity-10 text-success';
                                if (str_contains($log->action, 'Deleted') || str_contains($log->action, 'Rejected') || str_contains($log->action, 'Cancelled')) $badgeClass = 'bg-danger bg-opacity-10 text-danger';
                                if (str_contains($log->action, 'Updated') || str_contains($log->action, 'Set')) $badgeClass = 'bg-info bg-opacity-10 text-info';
                                if (str_contains($log->action, 'Login')) $badgeClass = 'bg-primary bg-opacity-10 text-primary';
                            @endphp
                            <span class="badge {{ $badgeClass }} rounded-pill px-3">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td>
                            <span class="small text-dark">{{ $log->description }}</span>
                        </td>
                        <td class="text-end pe-4">
                            @if($log->details)
                                <button class="btn btn-sm btn-light border hover-shadow" 
                                        data-bs-toggle="popover" 
                                        title="Log Details" 
                                        data-bs-content="{{ json_encode($log->details) }}">
                                    <i class="bi bi-info-circle"></i>
                                </button>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-clock-history fs-1 d-block mb-3"></i>
                            No activity logs found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="card-footer bg-white border-top py-3">
             {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Server-side filtering is active, tableSearch JS removed.

    // Initialize Popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    })
</script>
<style>
    .extra-small {
        font-size: 0.65rem;
        padding: 0.15rem 0.4rem;
    }
    .hover-shadow:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        transform: translateY(-2px);
        transition: all 0.2s ease-in-out;
    }
</style>
@endpush
@endsection
