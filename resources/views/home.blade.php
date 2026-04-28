@extends('layouts.app')

@section('content')
<div class="container">
    @php
        $webUser = auth('web')->user();
        $clientUser = auth('client')->user();
        $currentUser = $webUser ?? $clientUser;
        $isClient = $clientUser || ($webUser && $webUser->role === 'client');
    @endphp

    @if($isClient)
        <!-- Client Dashboard Greeting -->


        <!-- Active Orders Section -->
        <div class="mb-5 animate-fade-in" style="animation-delay: 0.1s;">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                    <i class="bi bi-box-seam fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">Active Orders</h4>
                    <p class="text-muted small mb-0">Track your current water refills</p>
                </div>
            </div>

            @forelse($activeOrders as $order)
                <div class="glass-card mb-4 border-0 shadow-sm overflow-hidden">
                    <div class="card-header bg-white border-0 p-4 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill text-uppercase fw-bold small mb-2">
                                    {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                </span>
                                <h5 class="fw-bold text-dark mb-1">Order {{ $order->reference_number ?? '#' . str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h5>
                                <div class="text-muted small">
                                    <i class="bi bi-calendar-event me-1"></i> Placed on {{ $order->created_at->format('M d, Y') }}
                                </div>
                            </div>
                            <div class="text-end d-none d-md-block">
                                <h4 class="fw-bold text-primary mb-0">₱{{ number_format($order->total_amount, 2) }}</h4>
                                <div class="text-muted small">{{ $order->quantity }} {{ $order->quantity > 1 ? 'Gallons' : 'Gallon' }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body p-4">
                        <!-- Stepper -->
                        <div class="position-relative my-4">
                            <div class="progress" style="height: 2px;">
                                @php
                                    $progress = 0;
                                    if ($order->status == 'pending') $progress = 0;
                                    elseif ($order->status == 'confirmed') $progress = 33;
                                    elseif ($order->status == 'out_for_delivery') $progress = 66;
                                    elseif ($order->status == 'completed') $progress = 100;
                                @endphp
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between position-absolute top-0 start-0 w-100 mt-n2">
                                <!-- Step 1: Pending -->
                                <div class="text-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 {{ in_array($order->status, ['pending', 'confirmed', 'out_for_delivery', 'completed']) ? 'bg-primary text-white shadow' : 'bg-light text-muted border' }}" style="width: 32px; height: 32px;">
                                        <i class="bi bi-hourglass-split small"></i>
                                    </div>
                                    <div class="small fw-bold {{ in_array($order->status, ['pending', 'confirmed', 'out_for_delivery', 'completed']) ? 'text-primary' : 'text-muted opacity-50' }}">Pending</div>
                                </div>

                                <!-- Step 2: Confirmed -->
                                <div class="text-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 {{ in_array($order->status, ['confirmed', 'out_for_delivery', 'completed']) ? 'bg-primary text-white shadow' : 'bg-light text-muted border' }}" style="width: 32px; height: 32px;">
                                        <i class="bi bi-check-lg small"></i>
                                    </div>
                                    <div class="small fw-bold {{ in_array($order->status, ['confirmed', 'out_for_delivery', 'completed']) ? 'text-primary' : 'text-muted opacity-50' }}">Confirmed</div>
                                </div>
                                
                                <!-- Step 3: Out for Delivery -->
                                <div class="text-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 {{ in_array($order->status, ['out_for_delivery', 'completed']) ? 'bg-primary text-white shadow' : 'bg-light text-muted border' }}" style="width: 32px; height: 32px;">
                                        <i class="bi bi-truck small"></i>
                                    </div>
                                    <div class="small fw-bold {{ in_array($order->status, ['out_for_delivery', 'completed']) ? 'text-primary' : 'text-muted opacity-50' }}">On the Way</div>
                                </div>

                                <!-- Step 4: Delivered -->
                                <div class="text-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 {{ $order->status == 'completed' ? 'bg-primary text-white shadow' : 'bg-light text-muted border' }}" style="width: 32px; height: 32px;">
                                        <i class="bi bi-house-door-fill small"></i>
                                    </div>
                                    <div class="small fw-bold {{ $order->status == 'completed' ? 'text-primary' : 'text-muted opacity-50' }}">Delivered</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-light border-0 p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                @if($order->status == 'out_for_delivery')
                                    <i class="bi bi-info-circle me-1"></i> Check your updates frequently.
                                @else
                                    <i class="bi bi-clock me-1"></i> Delivery scheduled for <span class="fw-bold text-dark">{{ $order->delivery_date ? $order->delivery_date->format('M d, Y') : 'Immediate' }}</span>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                @if(in_array($order->status, ['pending', 'confirmed']))
                                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST" id="cancel-form-{{ $order->id }}" class="d-none">
                                        @csrf
                                    </form>
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-4"
                                            onclick="if(confirm('Are you sure you want to cancel order {{ $order->reference_number ?? $order->id }}?')) document.getElementById('cancel-form-{{ $order->id }}').submit();">
                                        Cancel Order
                                    </button>
                                @endif
                                <button class="btn btn-sm btn-outline-primary rounded-pill px-4"
                                        onclick="showOrderDetails({{ $order->toJson() }}, '{{ addslashes($order->office?->division?->campus?->name ?? 'OTHERS') }}', '{{ addslashes($order->office?->division?->display_name ?? '') }}', '{{ addslashes($order->office?->name ?? $order->other_location) }}')">
                                    View Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="glass-card mb-4 border-0 shadow-sm p-4 text-center">
                    <p class="text-muted mb-0">No active orders at the moment.</p>
                </div>
            @endforelse
        </div>

        <!-- Order History Section -->
        <div class="animate-fade-in" style="animation-delay: 0.2s;">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle p-2 me-3">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">Order History</h4>
                        <p class="text-muted small mb-0">Your past orders and deliveries</p>
                    </div>
                </div>
                @if($orders->total() > 0)
                <div class="d-flex gap-2 d-none d-md-flex">
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill small">
                        <i class="bi bi-receipt me-1 text-primary"></i> {{ $orders->total() }} {{ Str::plural('order', $orders->total()) }}
                    </span>
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill small">
                        <i class="bi bi-droplet-fill me-1 text-primary"></i> {{ $orders->sum('quantity') }} Gallons
                    </span>
                </div>
                @endif
            </div>

            <!-- Search & Filter -->
            <form action="{{ route('home') }}" method="GET" class="mb-4">
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search by order number..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="From" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="To" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                         <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                         <a href="{{ route('home') }}" class="btn btn-light border text-secondary" title="Clear Filters"><i class="bi bi-x-lg"></i></a>
                    </div>
                </div>
            </form>

            <!-- Card-based Order History List -->
            @forelse($orders as $order)
                @php
                    $statusConfig = [
                        'completed' => ['bg' => 'success', 'icon' => 'bi-check-circle-fill', 'label' => 'Delivered'],
                        'cancelled' => ['bg' => 'secondary', 'icon' => 'bi-x-circle-fill', 'label' => 'Cancelled'],
                        'rejected'  => ['bg' => 'danger', 'icon' => 'bi-x-circle-fill', 'label' => 'Rejected'],
                    ];
                    $cfg = $statusConfig[$order->status] ?? ['bg' => 'secondary', 'icon' => 'bi-circle', 'label' => ucfirst($order->status)];
                    $location = $order->office?->name ?? $order->other_location ?? '—';
                @endphp
                <div class="glass-card mb-3 border-0 shadow-sm overflow-hidden border-start border-3 border-{{ $cfg['bg'] }}">
                    <div class="card-body p-3 p-md-4">
                        <div class="row align-items-center g-3">
                            {{-- Left: Status icon --}}
                            <div class="col-auto d-none d-md-block">
                                <div class="rounded-circle bg-{{ $cfg['bg'] }} bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="bi {{ $cfg['icon'] }} text-{{ $cfg['bg'] }} fs-5"></i>
                                </div>
                            </div>

                            {{-- Center: Order details --}}
                            <div class="col">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <span class="fw-bold text-dark">Order {{ $order->reference_number ?? '#' . str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                                    <span class="badge bg-{{ $cfg['bg'] }} bg-opacity-10 text-{{ $cfg['bg'] }} rounded-pill px-2 py-1 text-uppercase small fw-bold">
                                        <i class="bi {{ $cfg['icon'] }} me-1"></i>{{ $cfg['label'] }}
                                    </span>
                                </div>

                                <div class="d-flex flex-wrap gap-3 text-muted small">
                                    {{-- Quantity --}}
                                    <span title="Quantity">
                                        <i class="bi bi-droplet-fill text-primary me-1"></i>{{ $order->quantity }} {{ Str::plural('Gallon', $order->quantity) }}
                                    </span>

                                    {{-- Location --}}
                                    <span title="Delivery Location">
                                        <i class="bi bi-geo-alt text-danger me-1"></i>{{ Str::limit($location, 30) }}
                                    </span>

                                    {{-- Date placed --}}
                                    <span title="Date Placed">
                                        <i class="bi bi-calendar-event me-1"></i>Placed {{ $order->created_at->format('M d, Y') }}
                                    </span>

                                    {{-- Completion / Resolution date --}}
                                    @if($order->status === 'completed' && $order->delivered_at)
                                        <span class="text-success" title="Delivered On">
                                            <i class="bi bi-truck me-1"></i>Delivered {{ $order->delivered_at->format('M d, Y') }}
                                        </span>
                                    @elseif(in_array($order->status, ['cancelled', 'rejected']))
                                        <span class="text-{{ $cfg['bg'] }}" title="{{ ucfirst($order->status) }} On">
                                            <i class="bi bi-clock me-1"></i>{{ ucfirst($order->status) }} {{ $order->updated_at->format('M d, Y') }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Rejection / Cancellation reason --}}
                                @if(in_array($order->status, ['cancelled', 'rejected']) && $order->remarks)
                                    <div class="mt-2 small">
                                        <span class="text-{{ $cfg['bg'] }}">
                                            <i class="bi bi-info-circle me-1"></i>Reason: {{ $order->remarks }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- Right: Amount & action --}}
                            <div class="col-auto text-end">
                                <div class="fw-bold text-dark fs-6 mb-1">₱{{ number_format($order->total_amount, 2) }}</div>
                                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                                        onclick="showOrderDetails({{ $order->toJson() }}, '{{ addslashes($order->office?->division?->campus?->name ?? 'OTHERS') }}', '{{ addslashes($order->office?->division?->display_name ?? '') }}', '{{ addslashes($order->office?->name ?? $order->other_location) }}')">
                                    <i class="bi bi-eye me-1"></i>Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="glass-card border-0 shadow-sm p-5 text-center">
                    <div class="mb-3">
                        <i class="bi bi-bag-check display-4 text-muted opacity-25"></i>
                    </div>
                    <h6 class="fw-bold text-muted mb-1">No past orders found</h6>
                    <p class="text-muted small mb-3">Your completed and past orders will appear here.</p>
                    <a href="{{ route('orders.create') }}" class="btn btn-sm btn-primary rounded-pill px-4">
                        <i class="bi bi-plus-lg me-1"></i>Place an Order
                    </a>
                </div>
            @endforelse

            @if($orders->hasPages())
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
            @endif
        </div>

    @else
        <!-- Admin/Staff Dashboard Stats -->
        <div class="row mb-4 g-4 animate-fade-in">
            <div class="col-md-4">
                <div class="glass-card h-100 p-4 border-0 position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 p-3 opacity-25">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="var(--primary-color)" class="bi bi-clock-history" viewBox="0 0 16 16">
                            <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022l-.074.997zm2.004.45a7.003 7.003 0 0 0-.985-.299l.219-.976c.383.086.76.2 1.126.342l-.36.933zm1.37.71a7.01 7.01 0 0 0-.439-.27l.493-.87a8.025 8.025 0 0 1 .979.654l-.615.789a6.996 6.996 0 0 0-.418-.302zm1.834 1.79a6.99 6.99 0 0 0-.653-.796l.724-.69c.27.285.52.59.747.91l-.818.576zm.744 1.352a7.08 7.08 0 0 0-.214-.468l.893-.45a7.976 7.976 0 0 1 .45 1.088l-.95.313a7.023 7.023 0 0 0-.179-.483zm.53 2.507a6.991 6.991 0 0 0-.1-1.025l.985-.17c.067.386.106.778.116 1.17l-1 .025zm-.131 1.538c.033-.17.06-.339.081-.51l.993.123a7.957 7.957 0 0 1-.23 1.155l-.964-.267c.046-.165.086-.332.12-.501zm-.952 2.379c.184-.29.346-.594.486-.908l.914.405c-.16.36-.345.706-.555 1.038l-.845-.535zm-.964 1.205c.122-.122.239-.248.35-.378l.758.653a8.073 8.073 0 0 1-.401.432l-.707-.707z"/>
                            <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0v1z"/>
                            <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5z"/>
                        </svg>
                    </div>
                    <h6 class="text-uppercase text-muted fw-bold mb-2">Pending Orders</h6>
                    <h2 class="display-4 fw-bold text-primary mb-0">{{ $stats['pending'] }}</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-card h-100 p-4 border-0 position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 p-3 opacity-25">
                         <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="var(--accent-color)" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                    </div>
                    <h6 class="text-uppercase text-muted fw-bold mb-2">Confirmed</h6>
                    <h2 class="display-4 fw-bold" style="color: var(--accent-color)">{{ $stats['confirmed'] }}</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-card h-100 p-4 border-0 position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 p-3 opacity-25">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="var(--secondary-color)" class="bi bi-calendar-check-fill" viewBox="0 0 16 16">
                            <path d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4V.5zM16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2zM3.332 9.21a.5.5 0 0 1 .73-.311l.823.442.946-1.55a.5.5 0 1 1 .865.503l-1.35 2.214a.5.5 0 0 1-.806.075L3.332 9.21z"/>
                        </svg>
                    </div>
                    <h6 class="text-uppercase text-muted fw-bold mb-2">Orders Today</h6>
                    <h2 class="display-4 fw-bold" style="color: var(--secondary-color)">{{ $stats['today_orders'] }}</h2>
                </div>
            </div>
        </div>

        <!-- Order Management Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 animate-fade-in" style="animation-delay: 0.1s;">
            <h3 class="fw-bold mb-0">Order Management</h3>
            @if(in_array(auth()->user()->role, ['admin', 'manager', 'staff']))
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#walkInModal">
                    <i class="bi bi-plus-lg me-1"></i> New Walk-in Order
                </button>
                <a href="{{ route('admin.reports.hub') }}" class="btn btn-light border shadow-sm">
                    <i class="bi bi-file-earmark-bar-graph me-2"></i>Reports Hub
                </a>
            </div>
            @endif
        </div>

        @if (session('status') || session('success'))
            <div class="alert alert-success alert-dismissible bg-success bg-opacity-10 border-success border-opacity-25 text-success fade show d-flex align-items-center rounded-3 shadow-sm py-2 px-3" role="alert">
                <i class="bi bi-check-circle-fill me-2 mt-1 align-self-start"></i>
                <div class="small fw-medium flex-grow-1">{{ session('status') ?? session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="padding: 0.75rem;"></button>
            </div>
        @endif

        <!-- Orders Table -->
        <div class="glass-card border-0 animate-fade-in" style="animation-delay: 0.2s;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-secondary small text-uppercase">
                                <th class="ps-4">ID</th>
                                <th>
                                    <a href="{{ route('home', ['sort' => ($sort == 'desc' ? 'asc' : 'desc')]) }}" class="text-decoration-none text-secondary" title="Click to sort by date">
                                        Date
                                        <i class="bi bi-sort-{{ $sort == 'desc' ? 'down' : 'up' }} ms-1"></i>
                                    </a>
                                </th>
                                <th class="d-none d-lg-table-cell">Schedule</th>
                                @if(in_array(auth()->user()->role, ['admin', 'unit_admin', 'manager', 'staff']))
                                <th>Client / Contact</th>
                                @endif
                                <th class="d-none d-md-table-cell">Location / Office</th>
                                <th>Order Details</th>
                                <th class="d-none d-xl-table-cell">Billing Info</th>
                                <th class="d-none d-sm-table-cell">Status / Remarks</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse($orders as $order)
                                <tr>
                                    <td class="ps-4 fw-bold text-muted">{{ $order->reference_number ?? '#' . str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td class="text-nowrap">
                                        <span class="d-block text-dark fw-medium">{{ $order->created_at->format('M d, Y') }}</span>
                                        <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td class="text-nowrap d-none d-lg-table-cell">
                                        @if($order->status === 'completed' && $order->delivered_at)
                                            <span class="d-block text-success fw-bold">{{ $order->delivered_at->format('M d, Y') }}</span>
                                            <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $order->delivered_at->format('h:i A') }}</small>
                                        @elseif($order->delivery_date)
                                            <span class="d-block text-dark fw-medium">{{ $order->delivery_date->format('M d, Y') }}</span>
                                        @else
                                            <span class="text-muted small fst-italic">Immediately (Walk-in)</span>
                                        @endif
                                    </td>
                                    @if(in_array(auth()->user()->role, ['admin', 'unit_admin', 'manager', 'staff']))
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-dark text-nowrap">{{ $order->client_name }}</span>
                                            <small class="text-muted d-none d-md-block"><i class="bi bi-telephone small me-1"></i>{{ $order->contact_number }}</small>
                                        </div>
                                    </td>
                                    @endif
                                    <td class="d-none d-md-table-cell">
                                        <div class="d-flex flex-column">
                                            <div class="mb-1">
                                                <span class="badge {{ $order->customer_type == 'Office' ? 'bg-info bg-opacity-10 text-info border-info border-opacity-25' : 'bg-secondary bg-opacity-10 text-secondary border-secondary border-opacity-25' }} border small" style="font-size: 0.65rem;">
                                                    {{ $order->customer_type }}
                                                </span>
                                                @if($order->office)
                                                    <span class="fw-bold text-dark ms-1">{{ $order->office->name }}</span>
                                                @else
                                                    <span class="badge bg-warning bg-opacity-10 text-warning border-warning border-opacity-25 border ms-1">{{ $order->other_location }}</span>
                                                @endif
                                            </div>
                                            @if($order->office)
                                                <small class="text-muted d-none d-lg-block" style="font-size: 0.75rem;">
                                                    @php
                                                        $campus = $order->office->division->campus->name ?? 'N/A';
                                                        $division = $order->office->division->display_name ?? null;
                                                    @endphp
                                                    {{ $campus }}
                                                    @if($division)
                                                        <i class="bi bi-chevron-right small mx-1"></i> {{ $division }}
                                                    @endif
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="fw-bold text-dark me-2">{{ $order->quantity }} {{ $order->quantity > 1 ? 'Gallons' : 'Gallon' }}</span>
                                                @if($order->order_type == 'walk-in')
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 d-none d-md-inline-block" style="font-size: 0.65rem;">Walk-in</span>
                                                @else
                                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 d-none d-md-inline-block" style="font-size: 0.65rem;">Online</span>
                                                @endif
                                            </div>
                                            <span class="fw-bold text-primary">₱{{ number_format($order->total_amount, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="d-none d-xl-table-cell">
                                        <div class="d-flex flex-column small">
                                            @if($order->pr_number)
                                                <div class="text-nowrap"><span class="text-muted">PR:</span> <span class="fw-medium">{{ $order->pr_number }}</span></div>
                                            @endif
                                            @if($order->budget_code)
                                                <div class="text-nowrap"><span class="text-muted">BC:</span> <span class="fw-medium">{{ $order->budget_code }}</span></div>
                                            @endif
                                            @if(!$order->pr_number && !$order->budget_code)
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <div class="d-flex flex-column">
                                            @php
                                                $statusClass = match($order->status) {
                                                    'pending' => 'badge-soft-pending',
                                                    'confirmed' => 'badge-soft-confirmed',
                                                    'completed' => 'badge-soft-completed',
                                                    'rejected', 'cancelled' => 'badge-soft-rejected',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge rounded-pill {{ $statusClass }} px-2 py-1 text-uppercase mb-1" style="font-size: 0.65rem; width: fit-content;">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                            @if($order->remarks)
                                                <small class="text-muted text-truncate d-none d-lg-block" style="max-width: 120px;" title="{{ $order->remarks }}">
                                                    {{ $order->remarks }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-light border text-dark me-1" 
                                                    title="View Details"
                                                    aria-label="View Details"
                                                    onclick="showOrderDetails({{ $order->toJson() }}, '{{ addslashes($order->office?->division?->campus?->name ?? 'OTHERS') }}', '{{ addslashes($order->office?->division?->display_name ?? '') }}', '{{ addslashes($order->office?->name ?? $order->other_location) }}')">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            @if($order->status == 'pending')
                                                <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="d-inline">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="status" value="confirmed">
                                                    <button class="btn btn-sm btn-light border text-success" title="Confirm" aria-label="Confirm" data-bs-toggle="tooltip" data-bs-placement="top">
                                                         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16"><path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/></svg>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="d-inline ms-1">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button class="btn btn-sm btn-light border text-danger" title="Reject" aria-label="Reject">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16"><path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/></svg>
                                                    </button>
                                                </form>
                                            @elseif($order->status == 'confirmed')
                                                <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="d-inline">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="status" value="completed">
                                                    <button class="btn btn-sm btn-light border text-primary fw-bold" title="Mark Completed">Complete</button>
                                                </form>
                                            @endif
                                            @if(auth()->user()->role === 'staff')
                                                <a href="{{ route('reports.delivery-receipt', $order->id) }}" target="_blank" class="btn btn-sm btn-light border ms-1" title="Print Receipt" aria-label="Print Receipt">
                                                    <i class="bi bi-receipt"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('reports.billing', $order->id) }}" target="_blank" class="btn btn-sm btn-light border ms-1" title="Print Billing" aria-label="Print Billing">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
                                                        <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                                                        <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="text-muted">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-inbox opacity-25" viewBox="0 0 16 16">
                                                <path d="M4.98 4a.5.5 0 0 0-.39.188L1.54 8H6a.5.5 0 0 1 .5.5 1.5 1.5 0 1 0 3 0A.5.5 0 0 1 10 8h4.46l-3.05-3.812A.5.5 0 0 0 11.02 4H4.98zm9.954 5H10.45a2.5 2.5 0 0 1-4.9 0H1.066l.32 2.562a.5.5 0 0 0 .497.438h12.234a.5.5 0 0 0 .496-.438L14.933 9zM3.809 3.563A1.5 1.5 0 0 1 4.981 3h6.038a1.5 1.5 0 0 1 1.172.563l3.7 4.625a.5.5 0 0 1 .105.374l-.39 3.124A1.5 1.5 0 0 1 15.117 13H.883a1.5 1.5 0 0 1-1.49-1.314l-.39-3.124a.5.5 0 0 1 .106-.374l3.7-4.625z"/>
                                            </svg>
                                            <p class="mt-2 text-uppercase fw-bold small">No orders found</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="px-3 pt-3">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    @endif
</div></div>

<!-- Walk-in Modal -->
<div class="modal fade" id="walkInModal" tabindex="-1" aria-labelledby="walkInModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-sm-down">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="walkInModalLabel">Record Walk-in Order</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.walkin.store') }}" method="POST" onsubmit="const btn = this.querySelector('button[type=submit]'); btn.disabled = true; btn.innerHTML = '<span class=\'spinner-border spinner-border-sm me-1\'></span> Processing...';">
          @csrf
          <input type="hidden" name="is_walkin" value="1">
          <div class="modal-body p-4">
                @if($errors->any() && old('is_walkin'))
                    <div class="alert alert-danger py-2">
                        <ul class="mb-0 small">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="row g-3 mb-3">
                    <div class="col-md-12">
                        <label class="form-label text-muted small text-uppercase fw-bold">Client Name</label>
                        <input type="text" name="client_name" class="form-control" placeholder="Optional" maxlength="255">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small text-uppercase fw-bold">Ordering As</label>
                    <div class="d-flex gap-2">
                        <div class="form-check custom-option-check flex-fill">
                            <input class="form-check-input d-none" type="radio" name="customer_type" id="walkin_type_individual" value="Individual" checked onchange="toggleWalkInOfficeFields()">
                            <label class="form-check-label w-100 p-2 rounded-3 border text-center cursor-pointer small" for="walkin_type_individual">
                                Individual
                            </label>
                        </div>
                        <div class="form-check custom-option-check flex-fill">
                            <input class="form-check-input d-none" type="radio" name="customer_type" id="walkin_type_office" value="Office" onchange="toggleWalkInOfficeFields()">
                            <label class="form-check-label w-100 p-2 rounded-3 border text-center cursor-pointer small" for="walkin_type_office">
                                Office
                            </label>
                        </div>
                    </div>
                </div>

                <div id="walkin_office_fields" style="display: none;">
                    <div class="mb-3">
                        <label for="walkin_select_campus" class="form-label text-muted small text-uppercase fw-bold">Select Campus</label>
                        <select class="form-select" id="walkin_select_campus" onchange="updateWalkInDivisions()">
                            <option value="" selected disabled>Choose Campus...</option>
                            @foreach($offices->pluck('campus')->unique() as $campus)
                                <option value="{{ $campus }}">{{ $campus }}</option>
                            @endforeach
                            <option value="Others">Others (Manual Entry)</option>
                        </select>
                    </div>

                    <div class="mb-3" id="walkin_other_location_container" style="display: none;">
                        <label for="walkin_other_location" class="form-label text-muted small text-uppercase fw-bold">Manual Location Details</label>
                        <input type="text" class="form-control" id="walkin_other_location" name="other_location" placeholder="e.g. Administration Building, Room 101">
                    </div>

                    <div class="mb-3" id="walkin_division_container" style="display: none;">
                        <label for="walkin_select_division" class="form-label text-muted small text-uppercase fw-bold">Select Division</label>
                        <select class="form-select" id="walkin_select_division" onchange="updateWalkInUnits()">
                            <option value="" selected disabled>Choose Division...</option>
                        </select>
                    </div>

                    <div class="mb-3" id="walkin_unit_container" style="display: none;">
                        <label for="walkin_office_id" class="form-label text-muted small text-uppercase fw-bold">Select Unit / Section</label>
                        <select class="form-select" name="office_id" id="walkin_office_id">
                            <option value="" selected disabled>Choose Unit...</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label text-muted small text-uppercase fw-bold">PR Number</label>
                            <input type="text" name="pr_number" id="walkin_pr_number" class="form-control form-control-sm" placeholder="Optional">
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted small text-uppercase fw-bold">Budget Code</label>
                            <input type="text" name="budget_code" id="walkin_budget_code" class="form-control form-control-sm" placeholder="Optional">
                        </div>
                    </div>
                </div>

                <div class="text-center mb-4">
                    <label class="form-label text-muted small text-uppercase fw-bold">How many containers?</label>
                    <div class="d-flex justify-content-center align-items-center">
                        <button type="button" class="btn btn-outline-primary rounded-circle p-2" onclick="adjustQty(-1)" style="width: 45px; height: 45px;">
                            <i class="bi bi-dash-lg"></i>
                        </button>
                        <input type="number" id="walkin_quantity" name="quantity" class="form-control form-control-lg text-center border-0 fw-bold mx-3" style="font-size: 2rem; width: 100px;" min="1" value="1" oninput="updateTotal()" required>
                        <button type="button" class="btn btn-outline-primary rounded-circle p-2" onclick="adjustQty(1)" style="width: 45px; height: 45px;">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>

                <div class="text-center mb-4 bg-light rounded-4 py-3 border">
                    <div class="text-muted small text-uppercase fw-bold mb-1">Total Amount</div>
                    <div class="h3 fw-bold text-primary mb-0">₱<span id="walkin_total">{{ number_format($unitPrice, 2) }}</span></div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small text-uppercase fw-bold d-block text-center mb-3">When will it be picked up?</label>
                    <div class="row g-3">
                        <div class="col-6">
                            <div id="pickup_now" class="slot-option active" onclick="selectPickup('now', this)">
                                <i class="bi bi-lightning-charge-fill text-primary mb-2 d-xl-block h4"></i>
                                <div class="fw-bold">Pick up Now</div>
                                <div class="small opacity-75">Fulfilled</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div id="pickup_later" class="slot-option" onclick="selectPickup('later', this)">
                                <i class="bi bi-clock-history text-primary mb-2 d-xl-block h4"></i>
                                <div class="fw-bold">Pick up Later</div>
                                <div class="small opacity-75">Scheduled</div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="pickup_type" name="pickup_type" value="now">
                </div>
          </div>
          <div class="modal-footer border-0 pb-4">
            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary px-5 shadow-sm">Process Order</button>
          </div>
      </form>
    </div>
  </div>
</div>
@include('admin.partials.order_details_modal')

@push('scripts')
<script>
    const PRICE_PER_GALLON = {{ $unitPrice }};
    const officesData = @json($offices);


    function toggleWalkInOfficeFields() {
        const isOffice = document.getElementById('walkin_type_office').checked;
        const fields = document.getElementById('walkin_office_fields');
        fields.style.display = isOffice ? 'block' : 'none';
        
        const campusSelect = document.getElementById('walkin_select_campus');
        const officeSelect = document.getElementById('walkin_office_id');
        const otherLocationInput = document.getElementById('walkin_other_location');

        if (isOffice) {
            campusSelect.setAttribute('required', 'required');
            if (campusSelect.value === 'Others') {
                otherLocationInput.setAttribute('required', 'required');
                officeSelect.removeAttribute('required');
            } else if (campusSelect.value) {
                officeSelect.setAttribute('required', 'required');
                otherLocationInput.removeAttribute('required');
            }
        } else {
            campusSelect.removeAttribute('required');
            officeSelect.removeAttribute('required');
            otherLocationInput.removeAttribute('required');
        }
    }

    function updateWalkInDivisions() {
        const campus = document.getElementById('walkin_select_campus').value;
        const divisionSelect = document.getElementById('walkin_select_division');
        const unitSelect = document.getElementById('walkin_office_id');
        const divisionContainer = document.getElementById('walkin_division_container');
        const unitContainer = document.getElementById('walkin_unit_container');
        const otherLocationContainer = document.getElementById('walkin_other_location_container');
        const otherLocationInput = document.getElementById('walkin_other_location');

        divisionSelect.innerHTML = '<option value="" selected disabled>Choose Division...</option>';
        unitSelect.innerHTML = '<option value="" selected disabled>Choose Unit...</option>';
        unitContainer.style.display = 'none';

        if (campus === 'Others') {
            divisionContainer.style.display = 'none';
            unitContainer.style.display = 'none';
            otherLocationContainer.style.display = 'block';
            otherLocationInput.setAttribute('required', 'required');
            unitSelect.removeAttribute('required');
        } else if (campus) {
            const divisions = [...new Set(officesData.filter(o => o.campus === campus).map(o => o.division))];
            divisions.forEach(div => {
                const opt = document.createElement('option');
                opt.value = div;
                opt.textContent = div;
                divisionSelect.appendChild(opt);
            });
            divisionContainer.style.display = 'block';
            otherLocationContainer.style.display = 'none';
            otherLocationInput.removeAttribute('required');
            unitSelect.setAttribute('required', 'required');
        } else {
            divisionContainer.style.display = 'none';
            otherLocationContainer.style.display = 'none';
        }
    }

    function updateWalkInUnits() {
        const campus = document.getElementById('walkin_select_campus').value;
        const division = document.getElementById('walkin_select_division').value;
        const unitSelect = document.getElementById('walkin_office_id');
        const unitContainer = document.getElementById('walkin_unit_container');

        unitSelect.innerHTML = '<option value="" selected disabled>Choose Unit...</option>';

        if (campus && campus !== 'Others' && division) {
            const units = officesData.filter(o => o.campus === campus && o.division === division);
            units.forEach(unit => {
                const opt = document.createElement('option');
                opt.value = unit.id;
                opt.textContent = unit.name;
                unitSelect.appendChild(opt);
            });
            unitContainer.style.display = 'block';
        } else {
            unitContainer.style.display = 'none';
        }
    }

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

    // Initialize total on load
    document.addEventListener('DOMContentLoaded', function() {
        updateTotal();
        @if($errors->any() && old('is_walkin'))
            var walkInModal = new bootstrap.Modal(document.getElementById('walkInModal'));
            walkInModal.show();
            // Re-trigger toggles to restore state
            toggleWalkInOfficeFields();
            if (document.getElementById('walkin_select_campus').value) {
                updateWalkInDivisions();
            }
        @endif
    });
</script>
@endpush
@endsection
