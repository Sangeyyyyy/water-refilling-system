<!-- Stats Grid -->
<div class="row g-3 g-md-4 mb-5 animate-fade-in">
    @if(auth()->user()->role === 'staff')
        <!-- Staff View: Static Cards -->
        <div class="col-6 col-md-4">
            <div class="glass-card h-100 p-4 border-0 position-relative overflow-hidden">
                <div class="position-absolute top-0 end-0 p-3 opacity-25">
                    <i class="bi bi-hourglass-split" style="font-size: 3.5rem; color: var(--primary-color);"></i>
                </div>
                <h6 class="text-uppercase text-muted fw-bold mb-2">Pending Orders</h6>
                <h2 class="display-4 fw-bold text-primary mb-0">{{ $stats['confirmed_only'] }}</h2>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="glass-card h-100 p-4 border-0 position-relative overflow-hidden">
                <div class="position-absolute top-0 end-0 p-3 opacity-25">
                    <i class="bi bi-truck" style="font-size: 3.5rem; color: var(--accent-color);"></i>
                </div>
                <h6 class="text-uppercase text-muted fw-bold mb-2">Out for Delivery</h6>
                <h2 class="display-4 fw-bold mb-0" style="color: var(--accent-color)">{{ $stats['out_for_delivery'] }}</h2>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="glass-card h-100 p-4 border-0 position-relative overflow-hidden">
                <div class="position-absolute top-0 end-0 p-3 opacity-25">
                    <i class="bi bi-check-circle-fill" style="font-size: 3.5rem; color: var(--secondary-color);"></i>
                </div>
                <h6 class="text-uppercase text-muted fw-bold mb-2">Delivered Today</h6>
                <h2 class="display-4 fw-bold mb-0" style="color: var(--secondary-color)">{{ $stats['completed_today'] }}</h2>
            </div>
        </div>
    @elseif(auth()->user()->role === 'director')
        <!-- Director View: 4 Executive KPI Monitoring Cards -->
        <div class="col-xl-3 col-md-6 text-dark">
            <div class="glass-card p-4 border-0 h-100 shadow-sm border-bottom border-primary border-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary fw-bold fs-4 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        ₱
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">Monthly</span>
                </div>
                <h6 class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Revenue (MTD)</h6>
                <h2 class="display-6 fw-bold text-dark mb-0">₱{{ number_format($stats['month_sales'], 2) }}</h2>
                <small class="text-muted">Targeted Performance</small>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 text-dark">
            <div class="glass-card p-4 border-0 h-100 shadow-sm border-bottom border-info border-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-info bg-opacity-10 p-2 rounded-3 text-info">
                        <i class="bi bi-cart-check-fill fs-4"></i>
                    </div>
                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2">Total</span>
                </div>
                <h6 class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Total Orders (MTD)</h6>
                <h2 class="display-6 fw-bold text-dark mb-0">{{ number_format($stats['month_orders']) }}</h2>
                <small class="text-muted">This Month</small>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 text-dark">
            <div class="glass-card p-4 border-0 h-100 shadow-sm border-bottom border-success border-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success">
                        <i class="bi bi-droplet-fill fs-4"></i>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">Volume</span>
                </div>
                <h6 class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Gallons Delivered (MTD)</h6>
                <h2 class="display-6 fw-bold text-dark mb-0">{{ number_format($stats['month_gallons']) }}</h2>
                <small class="text-muted">This Month</small>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 text-dark">
            <div class="glass-card p-4 border-0 h-100 shadow-sm border-bottom border-warning border-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2">Users</span>
                </div>
                <h6 class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">System Users</h6>
                <h2 class="display-6 fw-bold text-dark mb-0">{{ $dashboardData['user_counts']['total'] ?? 0 }}</h2>
                <div class="d-flex gap-2 mt-1">
                    <small class="text-muted">{{ $dashboardData['user_counts']['clients'] ?? 0 }} Clients</small>
                    <small class="text-muted">|</small>
                    <small class="text-muted">{{ $dashboardData['user_counts']['staff'] ?? 0 }} Staff</small>
                </div>
            </div>
        </div>
    @elseif(in_array(auth()->user()->role, ['manager']))
        <!-- Manager View: 4 KPI Monitoring Cards -->
        <div class="col-xl-3 col-md-6 text-dark">
            <div class="glass-card p-4 border-0 h-100 shadow-sm border-bottom border-primary border-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary fw-bold fs-4 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        ₱
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">Monthly</span>
                </div>
                <h6 class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Revenue (MTD)</h6>
                <h2 class="display-6 fw-bold text-dark mb-0">₱{{ number_format($stats['month_sales'], 2) }}</h2>
                <small class="text-muted">Confirmed & delivered orders</small>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 text-dark">
            <div class="glass-card p-4 border-0 h-100 shadow-sm border-bottom border-info border-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-info bg-opacity-10 p-2 rounded-3 text-info">
                        <i class="bi bi-droplet-fill fs-4"></i>
                    </div>
                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2">Production</span>
                </div>
                <h6 class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Gallons Delivered</h6>
                <h2 class="display-6 fw-bold text-dark mb-0">{{ number_format($stats['month_gallons']) }}</h2>
                <small class="text-muted">This month</small>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 text-dark">
            <div class="glass-card p-4 border-0 h-100 shadow-sm border-bottom border-success border-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">Delivered</span>
                </div>
                <h6 class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Delivered Orders</h6>
                <h2 class="display-6 fw-bold text-dark mb-0">{{ number_format($stats['month_completed']) }}</h2>
                <small class="text-muted">This month</small>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 text-dark">
            <div class="glass-card p-4 border-0 h-100 shadow-sm border-bottom border-warning border-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning">
                        <i class="bi bi-water fs-4"></i>
                    </div>
                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2">Daily</span>
                </div>
                <h6 class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Today's Gallons</h6>
                <h2 class="display-6 fw-bold text-dark mb-0">{{ number_format($stats['today_gallons']) }}</h2>
                <small class="text-muted">Production today</small>
            </div>
        </div>
    @else
        <!-- Admin View: Performance Metrics (4 Cards) -->
        <div class="col-xl-3 col-md-6 text-dark">
            <div class="glass-card p-4 border-0 h-100 shadow-sm border-bottom border-primary border-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary fw-bold fs-4 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        ₱
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">Monthly</span>
                </div>
                <h6 class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Revenue (MTD)</h6>
                <h2 class="display-6 fw-bold text-dark mb-0">₱{{ number_format($stats['month_sales'], 2) }}</h2>
                <small class="text-muted">Targeted Performance</small>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 text-dark">
            <div class="glass-card p-4 border-0 h-100 shadow-sm border-bottom border-danger border-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-danger bg-opacity-10 p-2 rounded-3 text-danger">
                        <i class="bi bi-exclamation-octagon fs-4"></i>
                    </div>
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2">Urgent</span>
                </div>
                <h6 class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Pending Orders</h6>
                <h2 class="display-6 fw-bold text-dark mb-0">{{ number_format($stats['pending']) }}</h2>
                <small class="text-muted">Requiring Attention</small>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 text-dark">
            <div class="glass-card p-4 border-0 h-100 shadow-sm border-bottom border-info border-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-info bg-opacity-10 p-2 rounded-3 text-info">
                        <i class="bi bi-droplet-fill fs-4"></i>
                    </div>
                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2">Production</span>
                </div>
                <h6 class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Gallons Today</h6>
                <h2 class="display-6 fw-bold text-dark mb-0">{{ number_format($stats['today_gallons']) }}</h2>
                <small class="text-muted">Confirmed Volume</small>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 text-dark">
            <div class="glass-card p-4 border-0 h-100 shadow-sm border-bottom border-warning border-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2">Users</span>
                </div>
                <h6 class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">System Users</h6>
                <h2 class="display-6 fw-bold text-dark mb-0">{{ $dashboardData['user_counts']['total'] ?? 0 }}</h2>
                <div class="d-flex gap-2 mt-1">
                    <small class="text-muted">{{ $dashboardData['user_counts']['clients'] ?? 0 }} Clients</small>
                    <small class="text-muted">|</small>
                    <small class="text-muted">{{ $dashboardData['user_counts']['staff'] ?? 0 }} Staff</small>
                </div>
            </div>
        </div>
    @endif
</div>
