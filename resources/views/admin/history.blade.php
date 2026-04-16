@extends(request()->ajax() ? 'layouts.ajax' : 'layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <!-- Header removed per user request -->
    <div class="glass-card p-0 border-0 shadow-sm overflow-hidden mb-5">
        <!-- Filters Section -->
        <div class="p-3 bg-white bg-opacity-50 border-bottom">
            <form method="GET" action="{{ route('admin.history') }}" id="filterForm" class="d-flex flex-wrap gap-2 align-items-end">
                <!-- Date Range Filter -->
                <div style="min-width: 140px; flex: 1;">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.65rem;">Date Range</label>
                    <select name="date_range" class="form-select form-select-sm" onchange="handleDateRangeChange(this)">
                        <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ request('date_range') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ request('date_range', 'month') == 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="all" {{ request('date_range') == 'all' ? 'selected' : '' }}>All Time</option>
                        <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>

                <!-- Custom Date Range -->
                <div style="min-width: 140px; flex: 1; display: {{ request('date_range') == 'custom' ? 'block' : 'none' }};" id="date_from_container">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.65rem;">From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" onchange="this.form.submit()">
                </div>
                <div style="min-width: 140px; flex: 1; display: {{ request('date_range') == 'custom' ? 'block' : 'none' }};" id="date_to_container">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.65rem;">To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" onchange="this.form.submit()">
                </div>

                <!-- Status Filter -->
                <div style="min-width: 140px; flex: 1;">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.65rem;">Status</label>
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All History</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <!-- Customer Type Filter -->
                <div style="min-width: 140px; flex: 1;">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.65rem;">Customer</label>
                    <select name="customer_type" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All</option>
                        <option value="Office" {{ request('customer_type') == 'Office' ? 'selected' : '' }}>Office</option>
                        <option value="Individual" {{ request('customer_type') == 'Individual' ? 'selected' : '' }}>Individual</option>
                    </select>
                </div>

                <!-- Order Type Filter -->
                <div style="min-width: 140px; flex: 1;">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.65rem;">Order Type</label>
                    <select name="order_type" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All</option>
                        <option value="online" {{ request('order_type') == 'online' ? 'selected' : '' }}>Online</option>
                        <option value="walk-in" {{ request('order_type') == 'walk-in' ? 'selected' : '' }}>Walk-in</option>
                    </select>
                </div>

                <!-- Search Box -->
                <div style="min-width: 200px; flex: 2;">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.65rem;">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Order ID, Client, PR#" value="{{ request('search') }}" onkeypress="if(event.key === 'Enter') this.form.submit()">
                </div>

                <!-- Reset Button -->
                <div class="ms-md-auto mt-2 mt-md-0">
                    <a href="{{ route('admin.history') }}" class="btn btn-light btn-sm border" title="Reset filters">
                        <i class="bi bi-arrow-clockwise me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-secondary small text-uppercase">
                        <th class="ps-4 d-none d-sm-table-cell">Ref #</th>
                        <th>Ordering Date</th>
                        <th>Client</th>
                        <th class="d-none d-md-table-cell">Items</th>
                        <th class="d-none d-sm-table-cell">Total</th>
                        <th>Final Status</th>
                        <th class="text-end pe-4">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td class="ps-4 fw-bold text-primary d-none d-sm-table-cell">{{ $order->reference_number ?? '#' . str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <div class="small">
                                <span class="d-block text-dark fw-medium">{{ $order->created_at->format('M d, Y') }}</span>
                                <span class="text-muted d-none d-lg-inline">{{ $order->created_at->format('h:i A') }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark">{{ $order->full_client_name }}</span>
                                <small class="text-muted d-none d-md-block">{{ $order->office ? $order->office->name : $order->customer_type }}</small>
                            </div>
                        </td>
                        <td class="d-none d-md-table-cell"><span class="fw-medium text-dark">{{ $order->quantity }} {{ $order->quantity > 1 ? 'Gallons' : 'Gallon' }}</span></td>
                        <td class="d-none d-sm-table-cell"><span class="fw-bold text-primary">₱{{ number_format($order->total_amount, 2) }}</span></td>
                        <td>
                            @php
                                $statusClass = match($order->status) {
                                    'completed' => 'badge-soft-completed',
                                    'cancelled' => 'badge-soft-rejected text-danger', // Treating cancelled similar to rejected visually but distinct text
                                    'rejected' => 'badge-soft-rejected',
                                    default => 'bg-secondary bg-opacity-10 text-secondary'
                                };
                            @endphp
                            <span class="badge rounded-pill {{ $statusClass }} px-2 py-1 text-uppercase" style="font-size: 0.65rem;">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-icon btn-light border rounded-circle" 
                                    onclick="showOrderDetails({{ $order->toJson() }}, '{{ addslashes($order->office?->division?->campus?->name ?? 'OTHERS') }}', '{{ addslashes($order->office?->division?->display_name ?? '') }}', '{{ addslashes($order->office?->name ?? $order->other_location) }}')">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                            No historical orders found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="p-3 border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="small text-muted">
                Showing <span class="fw-bold">{{ $orders->firstItem() }}</span> to <span class="fw-bold">{{ $orders->lastItem() }}</span> of <span class="fw-bold">{{ $orders->total() }}</span> entries
            </div>
            <div>
                {{ $orders->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Order Details Modal -->
@include('admin.partials.order_details_modal')

@push('scripts')
<script>
    function handleDateRangeChange(select) {
        const dateFromContainer = document.getElementById('date_from_container');
        const dateToContainer = document.getElementById('date_to_container');
        
        if (select.value === 'custom') {
            dateFromContainer.style.display = 'block';
            dateToContainer.style.display = 'block';
        } else {
            dateFromContainer.style.display = 'none';
            dateToContainer.style.display = 'none';
            select.form.submit();
        }
    }

</script>
@endpush
@endsection
