@if(in_array(auth()->user()->role, ['admin', 'director', 'manager', 'staff']))
    <!-- STAFF: Filters Card -->
    <div class="glass-card p-4 mb-4 animate-fade-in shadow-sm border-0">
        <form method="GET" action="{{ route('home') }}" id="filterForm" class="row g-2 align-items-end">
            <div class="col-6 col-lg-2 col-md-3">
                <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.65rem;">
                    <i class="bi bi-calendar-event me-1"></i>From
                </label>
                <input type="date" name="date_from" class="form-control form-control-sm rounded-pill px-3 shadow-none border-light" value="{{ request('date_from') }}" onchange="this.form.submit()">
            </div>
            <div class="col-6 col-lg-2 col-md-3">
                <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.65rem;">
                    <i class="bi bi-calendar-check me-1"></i>To
                </label>
                <input type="date" name="date_to" class="form-control form-control-sm rounded-pill px-3 shadow-none border-light" value="{{ request('date_to') }}" onchange="this.form.submit()">
            </div>

            <div class="col-6 col-lg-2 col-md-3">
                <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.65rem;">
                    <i class="bi bi-person-badge me-1"></i>Customer
                </label>
                <select name="customer_type" class="form-select form-select-sm rounded-pill px-3 shadow-none border-light" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="Office" {{ request('customer_type') == 'Office' ? 'selected' : '' }}>Office</option>
                    <option value="Individual" {{ request('customer_type') == 'Individual' ? 'selected' : '' }}>Individual</option>
                </select>
            </div>

            <div class="col-12 col-lg-3 col-md-4">
                <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.65rem;">
                    <i class="bi bi-search me-1"></i>Search Orders
                </label>
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control rounded-pill px-3 shadow-none border-light" placeholder="Ref #, Client, Office, Phone..." value="{{ request('search') }}" onkeypress="if(event.key === 'Enter') this.form.submit()">
                </div>
            </div>

            <div class="col-lg-auto col-md-auto ms-auto">
                <a href="{{ route('home') }}" class="btn btn-light btn-sm border rounded-circle" title="Reset filters" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- STAFF: Pending Orders Card -->
    <div class="card border-0 shadow-sm overflow-hidden mb-4 animate-fade-in">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-2 text-warning">
                    <i class="bi bi-hourglass-split fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0">To Be Refilled</h6>
                </div>
                <span class="badge bg-warning text-dark rounded-pill ms-2">{{ $pendingOrders->total() }}</span>
            </div>
        </div>
        <div class="bg-light border-bottom p-2 px-4 small text-muted d-flex gap-3">
            <span><i class="bi bi-info-circle me-1"></i> Showing {{ $pendingOrders->count() }} items</span>
            <span><span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 ms-1" style="font-size: 0.6rem;">LEFTOVER</span> indicates past due</span>
        </div>

            {{-- Redundant form removed as it is handled by hidden form in order_status_scripts.blade.php --}}


            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 responsive-table">
                    <thead class="bg-light">
                        <tr class="text-secondary small text-uppercase">
                            <th class="ps-4" style="width: 40px;">
                                <input type="checkbox" class="form-check-input" id="selectAllPending">
                            </th>
                            <th style="width: 140px;" class="text-nowrap">Reference</th>
                            <th>Schedule</th>
                            <th>Client / Contact</th>
                            <th>Location / Office</th>
                            <th>Qty / Amount</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $currentCampus = null; @endphp
                        @forelse($pendingOrders as $order)
                            @php
                                $orderCampus = $order->campus_name ?? 'OTHERS';
                            @endphp

                            {{-- Group by Campus --}}
                            @if($currentCampus !== $orderCampus)
                                <tr class="bg-light bg-opacity-50 group-header">
                                    <td colspan="7" class="ps-4 py-2 text-start">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3">
                                            <i class="bi bi-building me-1"></i> {{ $orderCampus }}
                                        </span>
                                    </td>
                                </tr>
                                @php $currentCampus = $orderCampus; @endphp
                            @endif

                            <tr>
                                <td class="ps-4" data-label="Select">
                                    <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" class="form-check-input order-checkbox">
                                </td>
                                <td class="ps-4 fw-bold text-primary font-monospace text-nowrap" data-label="Reference">
                                    {{ $order->reference_number ?? '#' . str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="text-nowrap" data-label="Schedule">
                                    @if($order->delivery_date)
                                        <span class="d-block text-dark fw-medium">
                                            {{ $order->delivery_date->format('M d, Y') }}
                                            @if($order->delivery_date->isPast() && !$order->delivery_date->isToday())
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 ms-1" style="font-size: 0.6rem;">LEFTOVER</span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-muted small italic">Pick up Now (Walk-in)</span>
                                    @endif
                                </td>
                                <td data-label="Client / Contact">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark text-nowrap">{{ $order->full_client_name }}</span>
                                        <small class="text-muted"><i class="bi bi-telephone small me-1"></i>{{ $order->contact_number }}</small>
                                    </div>
                                </td>
                                <td data-label="Location / Office">
                                    <div class="d-flex flex-column">
                                        <div class="mb-1">
                                            <span class="badge {{ $order->customer_type == 'Office' ? 'bg-info bg-opacity-10 text-info border-info border-opacity-25' : 'bg-secondary bg-opacity-10 text-secondary border-secondary border-opacity-25' }} border small" style="font-size: 0.65rem;">
                                                {{ $order->customer_type }}
                                            </span>
                                            @if($order->office)
                                                <span class="fw-bold text-dark ms-1">{{ $order->office->name }}</span>
                                            @endif
                                        </div>
                                        <small class="text-muted text-truncate" style="max-width: 150px;" title="{{ $order->other_location ?? 'N/A' }}">
                                            {{ $order->other_location ?? '' }}
                                        </small>
                                    </div>
                                </td>
                                <td data-label="Qty / Amount">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark">{{ $order->quantity }} {{ $order->quantity > 1 ? 'Gallons' : 'Gallon' }}</span>
                                        <span class="fw-bold text-primary">₱{{ number_format($order->total_amount, 2) }}</span>
                                    </div>
                                </td>
                                <td class="text-center" data-label="Actions">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <button type="button" class="btn btn-sm btn-light border text-secondary p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="View Details" aria-label="View Details" onclick="showOrderDetails({{ $order->toJson() }}, '{{ addslashes($order->office?->division?->campus?->name ?? 'OTHERS') }}', '{{ addslashes($order->office?->division?->display_name ?? '') }}', '{{ addslashes($order->office?->name ?? $order->other_location) }}')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        
                                        @if(in_array(auth()->user()->role, ['admin', 'manager', 'director']))
                                            <a href="{{ route('reports.billing', $order->id) }}" target="_blank" class="btn btn-sm btn-light border text-primary p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Print Billing Statement" aria-label="Print Billing Statement">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </a>
                                        @endif

                                        @if($order->order_type === 'walk-in')
                                            <button type="button" class="btn btn-success btn-sm text-white p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Mark as Delivered (Walk-in)" aria-label="Mark as Delivered (Walk-in)" onclick="singleComplete({{ $order->id }})">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-primary p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Mark as Refilled" aria-label="Mark as Refilled" onclick="singleDispatch({{ $order->id }})">
                                                <i class="bi bi-droplet-fill"></i>
                                            </button>
                                        @endif

                                        <button type="button" class="btn btn-sm btn-light border text-danger p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Delete Order" aria-label="Delete Order" onclick="singleDelete({{ $order->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox display-4 opacity-25"></i>
                                    <p class="mt-2 text-uppercase small fw-bold ls-1">
                                        No pending orders.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pendingOrders->hasPages())
                <div class="p-3 border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="small text-muted">
                        Showing <span class="fw-bold">{{ $pendingOrders->firstItem() }}</span> to <span class="fw-bold">{{ $pendingOrders->lastItem() }}</span> of <span class="fw-bold">{{ $pendingOrders->total() }}</span> entries
                    </div>
                    <div>
                        {{ $pendingOrders->links() }}
                    </div>
                </div>
            @endif

    </div>

    <!-- STAFF: Active Deliveries -->
    <div class="card border-0 shadow-sm overflow-hidden mb-4 animate-fade-in">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2 text-primary">
                        <i class="bi bi-truck fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">To Be Delivered</h6>
                    </div>
                    <span class="badge bg-primary rounded-pill ms-2">{{ $deliveryQueue->total() }}</span>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 responsive-table">
                <thead class="bg-light">
                    <tr class="text-secondary small text-uppercase">
                        <th class="ps-4" style="width: 40px;">
                            <input type="checkbox" class="form-check-input" id="selectAllDeliveries">
                        </th>
                        <th style="width: 140px;" class="text-nowrap">Reference</th>
                        <th>Schedule</th>
                        <th>Client / Contact</th>
                        <th>Location / Office</th>
                        <th>Qty / Amount</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deliveryQueue as $order)
                        <tr class="{{ $order->status == 'completed' ? 'bg-light' : '' }}">
                            <td class="ps-4" data-label="Select">
                                <input type="checkbox" name="delivery_ids[]" value="{{ $order->id }}" class="form-check-input delivery-checkbox">
                            </td>
                            <td class="fw-bold text-primary font-monospace text-nowrap" data-label="Reference">
                                {{ $order->reference_number ?? '#' . str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="text-nowrap" data-label="Schedule">
                                @if($order->delivery_date)
                                    <span class="d-block text-dark fw-medium">
                                        {{ $order->delivery_date->format('M d, Y') }}
                                        @if($order->delivery_date->isPast() && !$order->delivery_date->isToday())
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 ms-1" style="font-size: 0.6rem;">LEFTOVER</span>
                                        @endif
                                    </span>
                                @else
                                    <span class="text-muted small italic">Pick up Now (Walk-in)</span>
                                @endif
                            </td>
                            <td data-label="Client / Contact">
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark text-nowrap">{{ $order->full_client_name }}</span>
                                    <small class="text-muted"><i class="bi bi-telephone small me-1"></i>{{ $order->contact_number }}</small>
                                </div>
                            </td>
                            <td data-label="Location / Office">
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark">{{ $order->office->name ?? $order->other_location }}</span>
                                    <span class="small text-muted">{{ $order->customer_type }}</span>
                                </div>
                            </td>
                            <td data-label="Qty / Amount">
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark">{{ $order->quantity }} {{ $order->quantity > 1 ? 'Gallons' : 'Gallon' }}</span>
                                    <span class="fw-bold text-primary">₱{{ number_format($order->total_amount, 2) }}</span>
                                </div>
                            </td>
                            <td class="text-center" data-label="Actions">
                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button" class="btn btn-sm btn-light border text-secondary p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="View Details" onclick="showOrderDetails({{ $order->toJson() }}, '{{ addslashes($order->office?->division?->campus?->name ?? 'OTHERS') }}', '{{ addslashes($order->office?->division?->display_name ?? '') }}', '{{ addslashes($order->office?->name ?? $order->other_location) }}')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @if(in_array(auth()->user()->role, ['admin', 'manager', 'director']))
                                        <a href="{{ route('reports.billing', $order->id) }}" target="_blank" class="btn btn-sm btn-light border text-primary p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Print Billing Statement">
                                            <i class="bi bi-file-earmark-text"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('reports.delivery-receipt', $order->id) }}" target="_blank" class="btn btn-sm btn-light border text-secondary p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Print Delivery Receipt">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                    <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="completed">
                                        <button class="btn btn-success btn-sm text-white p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Mark as Delivered">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-light border text-danger p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Delete Order" onclick="singleDelete({{ $order->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-truck display-4 opacity-25"></i>
                                <p class="mt-2 text-uppercase small fw-bold ls-1">
                                    No active deliveries.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($deliveryQueue instanceof \Illuminate\Pagination\LengthAwarePaginator && $deliveryQueue->hasPages())
            <div class="p-3 border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="small text-muted">
                    Showing <span class="fw-bold">{{ $deliveryQueue->firstItem() }}</span> to <span class="fw-bold">{{ $deliveryQueue->lastItem() }}</span> of <span class="fw-bold">{{ $deliveryQueue->total() }}</span> entries
                </div>
                <div>
                    {{ $deliveryQueue->links() }}
                </div>
            </div>
        @endif
    </div>
    <!-- STAFF: Recently Completed (Today) -->
    <div class="card border-0 shadow-sm overflow-hidden mb-4 animate-fade-in">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex align-items-center">
                <div class="bg-success bg-opacity-10 rounded-circle p-2 me-2 text-success">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0">Recently Completed (Today)</h6>
                </div>
                <span class="badge bg-success rounded-pill ms-2">{{ $completedToday->total() }}</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 responsive-table">
                <thead class="bg-light">
                    <tr class="text-secondary small text-uppercase">
                        <th class="ps-4 text-nowrap" style="width: 140px;">Reference</th>
                        <th>Client / Contact</th>
                        <th>Location / Office</th>
                        <th>Qty / Amount</th>
                        <th class="text-center">Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($completedToday as $order)
                        <tr>
                            <td class="ps-4 fw-bold text-success font-monospace text-nowrap" data-label="Reference">
                                {{ $order->reference_number ?? '#' . str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td data-label="Client / Contact">
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark text-nowrap">{{ $order->full_client_name }}</span>
                                    <small class="text-muted"><i class="bi bi-telephone small me-1"></i>{{ $order->contact_number }}</small>
                                </div>
                            </td>
                            <td data-label="Location / Office">
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark">{{ $order->office->name ?? $order->other_location }}</span>
                                    <span class="small text-muted">{{ $order->customer_type }}</span>
                                </div>
                            </td>
                            <td data-label="Qty / Amount">
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark">{{ $order->quantity }} {{ $order->quantity > 1 ? 'Gallons' : 'Gallon' }}</span>
                                    <span class="fw-bold text-primary">₱{{ number_format($order->total_amount, 2) }}</span>
                                </div>
                            </td>
                            <td class="text-center text-muted small" data-label="Time">
                                {{ $order->updated_at->format('h:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-check-circle display-4 opacity-25"></i>
                                <p class="mt-2 text-uppercase small fw-bold ls-1">
                                    No orders completed yet today.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($completedToday instanceof \Illuminate\Pagination\LengthAwarePaginator && $completedToday->hasPages())
            <div class="p-3 border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="small text-muted">
                    Showing <span class="fw-bold">{{ $completedToday->firstItem() }}</span> to <span class="fw-bold">{{ $completedToday->lastItem() }}</span> of <span class="fw-bold">{{ $completedToday->total() }}</span> entries
                </div>
                <div>
                    {{ $completedToday->links() }}
                </div>
            </div>
        @endif
    </div>
@endif
