@extends('layouts.admin')

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('ppmps.index') }}">PPMP Management</a></li>
        <li class="breadcrumb-item active" aria-current="page">PPMP Details</li>
    </ol>
</nav>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-4">
        <div class="glass-card p-4 mb-4 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <h4 class="fw-bold text-dark mb-0">Plan Summary</h4>
                @php
                    $badgeClass = match($ppmp->ppmp_type) {
                        'DBM' => 'bg-info-subtle text-info border-info-subtle',
                        'NON-DBM' => 'bg-primary-subtle text-primary border-primary-subtle',
                        'LIB' => 'bg-purple-subtle text-purple border-purple-subtle',
                        default => 'bg-secondary-subtle text-secondary border-secondary-subtle'
                    };
                @endphp
                <span class="badge {{ $badgeClass }} border px-3 rounded-pill" style="{{ $ppmp->ppmp_type == 'LIB' ? 'background-color: #f3e8ff; color: #7e22ce; border-color: #e9d5ff;' : '' }}">
                    {{ $ppmp->ppmp_type }}
                </span>
            </div>

            <div class="mb-4">
                <label class="text-muted small text-uppercase fw-bold d-block mb-1">Budget Code</label>
                <span class="h5 fw-bold text-primary">{{ $ppmp->budget_code ?? 'N/A' }}</span>
            </div>

            <div class="mb-4">
                <label class="text-muted small text-uppercase fw-bold d-block mb-1">Office / Unit</label>
                <span class="h5 d-block">{{ $ppmp->office->name }}</span>
                <span class="small text-muted">{{ $ppmp->office->division->name ?? 'N/A' }}</span>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Fiscal Year</label>
                    <span class="h6 fw-bold">{{ $ppmp->fiscal_year }}</span>
                </div>
                <div class="col-6">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Approval Date</label>
                    <span class="small text-dark">{{ $ppmp->president_approved_date ? $ppmp->president_approved_date->format('M d, Y h:i A') : 'Pending' }}</span>
                </div>
            </div>

            <hr class="my-4 opacity-10">

            <div class="mb-4">
                <label class="text-muted small text-uppercase fw-bold d-block mb-1">Total Allocation</label>
                <span class="h4 fw-bold text-dark">₱{{ number_format($ppmp->total_budget, 2) }}</span>
            </div>

            <div class="mb-4">
                <label class="text-muted small text-uppercase fw-bold d-block mb-1">Remaining Budget</label>
                <span class="h4 fw-bold {{ $ppmp->remaining_budget < ($ppmp->total_budget * 0.2) ? 'text-danger' : 'text-success' }}">
                    ₱{{ number_format($ppmp->remaining_budget, 2) }}
                </span>
                <div class="progress mt-2 rounded-pill" style="height: 10px; background-color: #f1f5f9;">
                    @php $percent = $ppmp->total_budget > 0 ? ($ppmp->remaining_budget / $ppmp->total_budget) * 100 : 0; @endphp
                    <div class="progress-bar rounded-pill {{ $percent < 20 ? 'bg-danger' : 'bg-success' }}" role="progressbar" style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="d-flex justify-content-between mt-1 small">
                    <span class="text-muted">{{ number_format($percent, 1) }}% Available</span>
                    <span class="text-muted">₱{{ number_format($ppmp->total_budget - $ppmp->remaining_budget, 2) }} Used</span>
                </div>
            </div>

            <hr class="my-4 opacity-10">

            <div class="mb-4">
                <label class="text-muted small text-uppercase fw-bold d-block mb-1">Fund Manager</label>
                <div class="d-flex align-items-center">
                    <div class="bg-light rounded-circle p-2 me-3">
                        <i class="bi bi-person text-secondary"></i>
                    </div>
                    <div>
                        <span class="h6 fw-bold mb-0 d-block">{{ $ppmp->fund_manager ?? 'N/A' }}</span>
                        <span class="small text-muted">{{ $ppmp->fund_manager_email }}</span>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <a href="{{ route('ppmps.edit', $ppmp->id) }}" class="btn btn-primary shadow-sm py-2 rounded-pill">
                    <i class="bi bi-pencil me-2"></i>Edit Plan
                </a>
            </div>
        </div>
        
        <div class="glass-card p-4 shadow-sm border-0">
            <h5 class="fw-bold mb-3">Plan Description</h5>
            <p class="text-dark mb-0">{{ $ppmp->description ?? 'No description provided.' }}</p>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="glass-card p-4 shadow-sm border-0 overflow-hidden">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-list-stars me-2"></i>Procurement Items</h5>
                <span class="badge bg-light text-dark border px-3 rounded-pill">{{ $ppmp->items->count() }} Items</span>
            </div>
            
            <div class="table-responsive">
                <table class="table align-middle table-hover">
                        <thead class="bg-light shadow-sm small text-uppercase">
                            <tr>
                                <th class="align-middle px-3 py-3">Item Description</th>
                                <th class="align-middle text-center" width="100">Unit</th>
                                <th class="align-middle text-center" width="150">Procurement Mode</th>
                                <th class="align-middle text-center" width="100">Qty</th>
                                <th class="text-center align-middle" width="150">Price</th>
                                <th class="text-center align-middle" width="180">Total</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 0.95rem;">
                            @foreach($ppmp->items as $item)
                            <tr>
                                <td class="fw-medium py-3 px-3">
                                    {{ $item->description }}
                                </td>
                                <td class="text-center text-muted small">{{ $item->unit ?? '---' }}</td>
                                <td class="text-center text-muted small">{{ $item->mode_of_procurement ?? '---' }}</td>
                                <td class="text-center fw-bold">{{ $item->quantity }}</td>
                                <td class="text-center text-muted small">₱{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-center fw-bold text-primary">₱{{ number_format($item->total_price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    <tfoot class="bg-light bg-opacity-50 border-0">
                        <tr>
                            <th colspan="8" class="text-end py-4 border-0">GRAND TOTAL:</th>
                            <th class="text-end py-4 h5 mb-0 fw-bold text-primary border-0">₱{{ number_format($ppmp->total_budget, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .text-purple { color: #7e22ce !important; }
    .bg-purple-subtle { background-color: #f3e8ff; }
    .border-purple-subtle { border-color: #e9d5ff; }
</style>
@endsection
