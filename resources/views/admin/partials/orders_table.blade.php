@if(auth()->user()->role !== 'staff')
    <!-- Manager/Admin: Order Management -->
    <div class="glass-card p-4 mb-4 animate-fade-in shadow-sm border-0">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Order Management</h5>
            <a href="{{ route('admin.history') }}" class="btn btn-sm btn-light border small text-muted">View History</a>
        </div>

        <form method="GET" action="{{ route('home') }}" id="filterFormManager" class="row g-2 align-items-end">
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
                    <i class="bi bi-activity me-1"></i>Status
                </label>
                <select name="status" class="form-select form-select-sm rounded-pill px-3 shadow-none border-light" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="out_for_delivery" {{ request('status') == 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Delivered</option>
                </select>
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
                    <input type="text" name="search" class="form-control rounded-pill px-3 shadow-none border-light" placeholder="Ref #, Client, Office..." value="{{ request('search') }}" onkeypress="if(event.key === 'Enter') this.form.submit()">
                </div>
            </div>

            <div class="col-lg-auto col-md-auto ms-auto">
                <a href="{{ route('home') }}" class="btn btn-light btn-sm border rounded-circle" title="Reset filters" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                @if(in_array(auth()->user()->role, ['manager', 'director']))
                    <thead class="bg-light">
                        <tr class="text-secondary small text-uppercase">
                            <th class="ps-4 text-nowrap" style="width: 140px;">Reference</th>
                            <th>Date / Schedule</th>
                            <th>Client / Office</th>
                            <th>Qty / Amount</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($orders as $order)
                            <tr>
                                <td class="ps-4 fw-bold text-primary font-monospace text-nowrap">
                                    {{ $order->reference_number ?? '#' . str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="text-nowrap">
                                    <span class="d-block text-dark fw-medium">{{ $order->created_at->format('M d, Y') }}</span>
                                    <div class="small text-muted">
                                        @if($order->status === 'completed' && $order->delivered_at)
                                            <span class="text-success"><i class="bi bi-check-all me-1"></i>{{ $order->delivered_at->format('M d, h:i A') }}</span>
                                        @elseif($order->delivery_date)
                                            <i class="bi bi-calendar-event me-1"></i>{{ $order->delivery_date->format('M d') }}
                                        @else
                                            <i class="bi bi-person-walking me-1"></i>Walk-in
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark">{{ $order->full_client_name }}</span>
                                        <div class="small">
                                            <span class="badge {{ $order->customer_type == 'Office' ? 'bg-info bg-opacity-10 text-info border-info border-opacity-25' : 'bg-secondary bg-opacity-10 text-secondary border-secondary border-opacity-25' }} border" style="font-size: 0.6rem;">
                                                {{ $order->customer_type }}
                                            </span>
                                            @if($order->office)
                                                <span class="text-muted ms-1">{{ $order->office->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $order->quantity }} {{ $order->quantity > 1 ? 'Gallons' : 'Gallon' }}</span>
                                    <span class="text-muted small ms-1">(₱{{ number_format($order->total_amount, 2) }})</span>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match($order->status) {
                                            'pending' => 'badge-soft-pending',
                                            'confirmed' => 'badge-soft-confirmed',
                                            'out_for_delivery' => 'bg-info bg-opacity-10 text-info border-info',
                                            'completed' => 'badge-soft-completed',
                                            default => 'badge-soft-rejected'
                                        };
                                    @endphp
                                    <span class="badge rounded-pill {{ $statusClass }} px-2 py-1 text-uppercase" style="font-size: 0.65rem;">
                                        {{ $order->status === 'completed' ? 'Delivered' : ucfirst(str_replace('_', ' ', $order->status)) }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-light border" title="Details" onclick="showOrderDetails({{ $order->toJson() }}, '{{ addslashes($order->office?->division?->campus?->name ?? 'OTHERS') }}', '{{ addslashes($order->office?->division?->display_name ?? '') }}', '{{ addslashes($order->office?->name ?? $order->other_location) }}')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @if(auth()->user()->role === 'staff')
                                            <a href="{{ route('reports.delivery-receipt', $order->id) }}" target="_blank" class="btn btn-sm btn-light border ms-1" title="Print Receipt">
                                                <i class="bi bi-receipt"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('reports.billing', $order->id) }}" target="_blank" class="btn btn-sm btn-light border ms-1" title="Print Billing">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                @else
                    <!-- Full Table for Admin/Director -->
                    <thead class="bg-light">
                        <tr class="text-secondary small text-uppercase">
                            <th class="ps-4 text-nowrap" style="width: 140px;">Reference</th>
                            <th>Date</th>
                            <th>Schedule</th>
                            <th>Client / Contact</th>
                            <th>Location / Office</th>
                            <th>Qty / Amount</th>
                            <th>Info</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($orders as $order)
                            <tr>
                                <td class="ps-4 fw-bold text-primary font-monospace text-nowrap">
                                    {{ $order->reference_number ?? '#' . str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="text-nowrap">
                                    <span class="d-block text-dark fw-medium">{{ $order->created_at->format('M d, Y') }}</span>
                                </td>
                                <td class="text-nowrap">
                                    @if($order->status === 'completed' && $order->delivered_at)
                                        <span class="d-block text-success fw-bold">{{ $order->delivered_at->format('M d, Y') }}</span>
                                        <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $order->delivered_at->format('h:i A') }}</small>
                                    @elseif($order->delivery_date)
                                        <span class="d-block text-dark fw-medium">{{ $order->delivery_date->format('M d, Y') }}</span>
                                    @else
                                        <span class="text-muted small italic">Pick up Now (Walk-in)</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark text-nowrap">{{ $order->full_client_name }}</span>
                                        <small class="text-muted"><i class="bi bi-telephone small me-1"></i>{{ $order->contact_number }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <div class="mb-1">
                                            <span class="badge {{ $order->customer_type == 'Office' ? 'bg-info bg-opacity-10 text-info border-info border-opacity-25' : 'bg-secondary bg-opacity-10 text-secondary border-secondary border-opacity-25' }} border small" style="font-size: 0.65rem;">
                                                {{ $order->customer_type }}
                                            </span>
                                            @if($order->office)
                                                <span class="fw-bold text-dark ms-1">{{ $order->office->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark me-2">{{ $order->quantity }} {{ $order->quantity > 1 ? 'Gallons' : 'Gallon' }}</span>
                                        <span class="fw-bold text-primary">₱{{ number_format($order->total_amount, 2) }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column small">
                                        @if($order->pr_number)
                                            <div><span class="text-muted">PR:</span> <span class="fw-medium">{{ $order->pr_number }}</span></div>
                                        @endif
                                        @if($order->budget_code)
                                            <div><span class="text-muted">BC:</span> <span class="fw-medium">{{ $order->budget_code }}</span></div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match($order->status) {
                                            'pending' => 'badge-soft-pending',
                                            'confirmed' => 'badge-soft-confirmed',
                                            'out_for_delivery' => 'bg-info bg-opacity-10 text-info border-info',
                                            'completed' => 'badge-soft-completed',
                                            default => 'badge-soft-rejected'
                                        };
                                    @endphp
                                    <span class="badge rounded-pill {{ $statusClass }} px-2 py-1 text-uppercase" style="font-size: 0.65rem;">
                                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-light border" title="Details" onclick="showOrderDetails({{ $order->toJson() }}, '{{ addslashes($order->office?->division?->campus?->name ?? 'OTHERS') }}', '{{ addslashes($order->office?->division?->display_name ?? '') }}', '{{ addslashes($order->office?->name ?? $order->other_location) }}')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @if($order->status == 'pending')
                                            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="d-inline ms-1">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status" value="confirmed">
                                                <button class="btn btn-sm btn-light border text-success"><i class="bi bi-check-lg"></i></button>
                                            </form>
                                        @endif
                                        @if(auth()->user()->role === 'staff')
                                            <a href="{{ route('reports.delivery-receipt', $order->id) }}" target="_blank" class="btn btn-sm btn-light border ms-1" title="Print Receipt">
                                                <i class="bi bi-receipt"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('reports.billing', $order->id) }}" target="_blank" class="btn btn-sm btn-light border ms-1" title="Print Billing">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">No orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                @endif
            </table>
        </div>
        @if($orders instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="p-3 border-top">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endif
