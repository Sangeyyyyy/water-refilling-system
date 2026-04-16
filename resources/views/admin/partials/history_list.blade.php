<!-- Offcanvas Filters (Matched with Main Dashboard) -->
<div class="px-3 py-3 bg-light border-bottom">
    <form id="offcanvasFilterForm" onsubmit="event.preventDefault(); applyOffcanvasFilters();" class="row g-2">
        <div class="col-6">
            <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.65rem;">From</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" onchange="applyOffcanvasFilters()">
        </div>
        <div class="col-6">
            <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.65rem;">To</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" onchange="applyOffcanvasFilters()">
        </div>
        <div class="col-6">
            <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.65rem;">Customer</label>
            <select name="customer_type" class="form-select form-select-sm" onchange="applyOffcanvasFilters()">
                <option value="">All Types</option>
                <option value="Office" {{ request('customer_type') == 'Office' ? 'selected' : '' }}>Office</option>
                <option value="Individual" {{ request('customer_type') == 'Individual' ? 'selected' : '' }}>Individual</option>
            </select>
        </div>
        <div class="col-6">
            <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.65rem;">Status</label>
            <select name="status" class="form-select form-select-sm" onchange="applyOffcanvasFilters()">
                <option value="">All History</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.65rem;">Search History</label>
            <div class="input-group input-group-sm">
                <input type="text" name="search" class="form-control" placeholder="Ref #, Client, Office..." value="{{ request('search') }}">
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-search"></i>
                </button>
                <button class="btn btn-outline-secondary" type="button" onclick="resetOffcanvasFilters()" title="Reset Filters">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- History Table -->
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0 responsive-table" style="font-size: 0.85rem;">
        <thead class="bg-light">
            <tr class="text-secondary small text-uppercase">
                <th class="ps-3 text-nowrap" style="width: 120px;">Ref #</th>
                <th>Client</th>
                <th>Details</th>
                <th>Status</th>
                <th class="text-end pe-3">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td class="ps-3 fw-bold text-primary text-nowrap" data-label="Ref #">{{ $order->reference_number ?? '#' . str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td data-label="Client">
                    <div class="d-flex flex-column">
                        <span class="fw-bold text-dark">{{ $order->full_client_name }}</span>
                        @if($order->status === 'completed' && $order->delivered_at)
                            <small class="text-success fw-bold" style="font-size: 0.7rem;">Delivered: {{ $order->delivered_at->format('M d, h:i A') }}</small>
                        @else
                            <small class="text-muted" style="font-size: 0.7rem;">{{ $order->created_at->format('M d, h:i A') }}</small>
                        @endif
                    </div>
                </td>
                <td data-label="Details">
                    <div class="d-flex flex-column">
                        <span class="fw-medium">{{ $order->quantity }} {{ $order->quantity > 1 ? 'Gallons' : 'Gallon' }}</span>
                        <span class="text-primary fw-bold">₱{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </td>
                <td data-label="Status">
                    @php
                        $statusClass = match($order->status) {
                            'completed' => 'badge-soft-completed',
                            'cancelled' => 'badge-soft-rejected text-danger',
                            'rejected' => 'badge-soft-rejected',
                            default => 'bg-secondary bg-opacity-10 text-secondary'
                        };
                    @endphp
                    <span class="badge rounded-pill {{ $statusClass }} px-2 py-1 text-uppercase" style="font-size: 0.6rem;">
                        {{ $order->status }}
                    </span>
                </td>
                <td class="text-end pe-3" data-label="Action">
                    <button class="btn btn-sm btn-light border p-0 d-flex align-items-center justify-content-center" 
                            style="width: 28px; height: 28px;"
                            onclick="showOrderDetails({{ $order->toJson() }}, '{{ addslashes($order->office?->division?->campus?->name ?? 'OTHERS') }}', '{{ addslashes($order->office?->division?->display_name ?? '') }}', '{{ addslashes($order->office?->name ?? $order->other_location) }}')">
                        <i class="bi bi-eye"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-5 text-muted">
                    <i class="bi bi-search fs-2 d-block mb-2 opacity-25"></i>
                    <small>No historical orders match your filters.</small>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($orders->hasPages())
<div class="p-3 border-top d-flex justify-content-center" id="historyPagination">
    {{ $orders->links('pagination::bootstrap-4') }}
</div>
@endif
