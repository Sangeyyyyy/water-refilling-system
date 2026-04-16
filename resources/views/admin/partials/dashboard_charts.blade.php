@if(in_array(auth()->user()->role, ['admin', 'director', 'manager']))
<!-- Charts Section -->
<div class="row g-4 mb-5">
    <div class="col-lg-8 text-dark">
        <div class="glass-card p-4 border-0 h-100 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Sales Performance Trend</h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Last 30 Days
                    </button>
                </div>
            </div>
            <div style="height: 300px;">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4 text-dark">
        <div class="glass-card p-4 border-0 h-100 shadow-sm">
            <h5 class="fw-bold mb-4">Distribution by Campus</h5>
            <div style="height: 250px;">
                <canvas id="campusConsumptionChart"></canvas>
            </div>
            <div class="mt-3">
                @foreach($dashboardData['campus_consumption']['labels'] as $index => $label)
                <div class="d-flex justify-content-between align-items-center small mb-1">
                    <span><i class="bi bi-circle-fill me-2" style="color: {{ ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'][$index % 5] }}; font-size: 0.6rem;"></i>{{ $label }}</span>
                    <span class="fw-bold">{{ number_format($dashboardData['campus_consumption']['data'][$index]) }} {{ $dashboardData['campus_consumption']['data'][$index] > 1 ? 'Gallons' : 'Gallon' }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Analytics & Alerts -->
<div class="row g-4 mb-5">
    <!-- Low PPMP Alerts -->
    <div class="col-md-6 text-dark">
        <div class="glass-card p-4 border-0 h-100 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-danger"><i class="bi bi-shield-exclamation me-2"></i>Resource Alerts</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-outline-danger border-0 rounded-pill px-2"><i class="bi bi-box-seam me-1"></i> Inventory</a>
                    <span class="badge bg-danger rounded-pill">{{ count($dashboardData['low_ppmp_alerts']) + count($dashboardData['inventory_alerts']) }}</span>
                </div>
            </div>
            <div class="alert-list">
                @foreach($dashboardData['inventory_alerts'] as $alert)
                <div class="d-flex align-items-center p-3 rounded-3 bg-light bg-opacity-50 mb-2 border-start border-warning border-4 shadow-sm">
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold">{{ $alert->item_name }} Stock Low</h6>
                        <small class="text-muted">Current: {{ $alert->stock_level }} {{ $alert->unit }} | Threshold: {{ $alert->low_stock_threshold }}</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill">
                            Reorder Soon
                        </span>
                    </div>
                </div>
                @endforeach

                @forelse($dashboardData['low_ppmp_alerts'] as $alert)
                <div class="d-flex align-items-center p-3 rounded-3 bg-light bg-opacity-50 mb-2 border-start border-danger border-4 shadow-sm">
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold">{{ $alert->office->name }}</h6>
                        <small class="text-muted">Remaining: ₱{{ number_format($alert->remaining_budget, 2) }} / ₱{{ number_format($alert->total_budget, 2) }}</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">
                            {{ number_format(($alert->remaining_budget / $alert->total_budget) * 100, 1) }}% Left
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-check2-circle fs-1 d-block mb-2 text-success"></i>
                    <p class="small mb-0">All units have healthy PPMP balances.</p>
                </div>
                @endforelse
            </div>
            @if(count($dashboardData['low_ppmp_alerts']) > 0)
            <div class="mt-3 text-center">
                <a href="{{ route('ppmps.index') }}" class="btn btn-sm btn-link text-decoration-none p-0">Manage PPMPS <i class="bi bi-arrow-right small"></i></a>
            </div>
            @endif
        </div>
    </div>

    <!-- Top Units -->
    <div class="col-md-6 text-dark">
        <div class="glass-card p-4 border-0 h-100 shadow-sm">
            <h5 class="fw-bold mb-3"><i class="bi bi-trophy me-2 text-warning"></i>Top Consuming Units</h5>
            <p class="text-muted small mb-4">Highest volume consumption for {{ now()->format('F Y') }}</p>

            @forelse($dashboardData['top_units'] as $index => $unit)
            <div class="d-flex align-items-center mb-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle fw-bold d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-size: 0.8rem;">
                    {{ $index + 1 }}
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-bold">{{ $unit->office->name }}</span>
                        <span class="text-primary fw-bold">{{ number_format($unit->total_gallons) }} {{ $unit->total_gallons > 1 ? 'Gallons' : 'Gallon' }}</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        @php
                            $maxVolume = $dashboardData['top_units'][0]->total_gallons ?: 1;
                            $percentage = ($unit->total_gallons / $maxVolume) * 100;
                        @endphp
                        <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-4 text-muted">
                <p class="small">No consumption data recorded this month.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endif
