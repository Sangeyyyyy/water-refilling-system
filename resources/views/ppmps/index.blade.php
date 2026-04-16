@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-0">PPMP Management</h2>
            <p class="text-muted">Manage annual procurement plans, budgets, and fund managers.</p>
        </div>
        <a href="{{ route('ppmps.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-plus-circle me-2"></i>Create New PPMP
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    <!-- Filters & Search -->
    <div class="glass-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3 d-none" id="bulk-actions-bar">
            <div class="d-flex align-items-center">
                <span class="badge bg-primary rounded-pill px-3 me-3" id="selected-count">0 Selected</span>
                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-4" onclick="confirmBulkDelete()">
                    <i class="bi bi-trash me-2"></i>Delete Selected
                </button>
            </div>
            <button type="button" class="btn btn-link btn-sm text-secondary text-decoration-none" onclick="deselectAll()">
                <i class="bi bi-x-lg me-1"></i>Deselect All
            </button>
        </div>

        <form action="{{ route('ppmps.index') }}" method="GET" class="row g-3 align-items-end" id="filter-form">
            <div class="col-md-3">
                <label class="form-label text-muted small text-uppercase fw-bold">Fiscal Year</label>
                <select name="fiscal_year" class="form-select border-0 bg-light">
                    <option value="">All Years</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ request('fiscal_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted small text-uppercase fw-bold">PPMP Type</label>
                <select name="ppmp_type" class="form-select border-0 bg-light">
                    <option value="">All Types</option>
                    <option value="DBM" {{ request('ppmp_type') == 'DBM' ? 'selected' : '' }}>DBM</option>
                    <option value="NON-DBM" {{ request('ppmp_type') == 'NON-DBM' ? 'selected' : '' }}>NON-DBM</option>
                    <option value="LIB" {{ request('ppmp_type') == 'LIB' ? 'selected' : '' }}>LIB</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label text-muted small text-uppercase fw-bold">Search</label>
                <div class="input-group">
                    <span class="input-group-text border-0 bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-0 bg-light" placeholder="Budget Code, Office, or Manager..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-dark w-100 rounded-pill">Filter</button>
                    <a href="{{ route('ppmps.index') }}" class="btn btn-light w-100 rounded-pill border">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <div class="glass-card p-0 overflow-hidden shadow-sm">
        <form id="bulk-delete-form" action="{{ route('ppmps.bulk-delete') }}" method="POST">
            @csrf
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 40px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="select-all">
                                </div>
                            </th>
                            <th style="width: 150px;">Budget Code</th>
                            <th>Office / Unit</th>
                            <th>Type</th>
                            <th>Fiscal Year</th>
                            <th>Fund Manager</th>
                            <th>Total Budget</th>
                            <th>Remaining</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ppmps as $ppmp)
                            <tr>
                                <td class="ps-4">
                                    <div class="form-check">
                                        <input class="form-check-input ppmp-checkbox" type="checkbox" name="ids[]" value="{{ $ppmp->id }}">
                                    </div>
                                </td>
                                <td class="fw-bold text-dark">{{ $ppmp->budget_code ?? 'N/A' }}</td>
                                <td>
                                    <div class="fw-bold text-primary">{{ $ppmp->office->name }}</div>
                                    <div class="small text-muted">{{ $ppmp->office->division->name ?? 'N/A' }}</div>
                                </td>
                                <td>
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
                                </td>
                                <td class="fw-bold">{{ $ppmp->fiscal_year }}</td>
                                <td>
                                    <div class="fw-bold">{{ $ppmp->fund_manager ?? 'N/A' }}</div>
                                    <div class="small text-muted">{{ $ppmp->fund_manager_email }}</div>
                                </td>
                                <td class="fw-bold">₱{{ number_format($ppmp->total_budget, 2) }}</td>
                                <td>
                                    <span class="fw-bold {{ $ppmp->remaining_budget < ($ppmp->total_budget * 0.2) ? 'text-danger' : 'text-success' }}">
                                        ₱{{ number_format($ppmp->remaining_budget, 2) }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="{{ route('ppmps.show', $ppmp->id) }}" class="btn btn-sm btn-outline-primary" title="Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('ppmps.edit', $ppmp->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" onclick="confirmSingleDelete({{ $ppmp->id }}, '{{ $ppmp->office->name }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted mb-3">
                                        <i class="bi bi-file-earmark-text display-4"></i>
                                    </div>
                                    <h5>No PPMPs found</h5>
                                    <p>Start by creating a new procurement plan for an office.</p>
                                    <a href="{{ route('ppmps.create') }}" class="btn btn-primary btn-sm rounded-pill px-4 mt-2 shadow-sm">Create Now</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
        @if($ppmps->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $ppmps->links() }}
            </div>
        @endif
    </div>

    <!-- Single Delete Form -->
    <form id="single-delete-form" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.ppmp-checkbox');
        const bulkActionsBar = document.getElementById('bulk-actions-bar');
        const filterForm = document.getElementById('filter-form');
        const selectedCount = document.getElementById('selected-count');

        function updateBulkActions() {
            const checkedCount = document.querySelectorAll('.ppmp-checkbox:checked').length;
            if (checkedCount > 0) {
                bulkActionsBar.classList.remove('d-none');
                filterForm.classList.add('d-none');
                selectedCount.textContent = checkedCount + ' Selected';
            } else {
                bulkActionsBar.classList.add('d-none');
                filterForm.classList.remove('d-none');
            }
        }

        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBulkActions();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                updateBulkActions();
                selectAll.checked = document.querySelectorAll('.ppmp-checkbox:checked').length === checkboxes.length;
            });
        });

        window.deselectAll = function() {
            selectAll.checked = false;
            checkboxes.forEach(cb => cb.checked = false);
            updateBulkActions();
        };

        window.confirmBulkDelete = function() {
            if (confirm('Are you sure you want to delete the selected PPMPs? This action cannot be undone.')) {
                document.getElementById('bulk-delete-form').submit();
            }
        };

        window.confirmSingleDelete = function(id, officeName) {
            if (confirm('Are you sure you want to delete the PPMP for "' + officeName + '"?')) {
                const form = document.getElementById('single-delete-form');
                form.action = '/ppmps/' + id;
                form.submit();
            }
        };
    });
</script>
        @if($ppmps->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $ppmps->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    .text-purple { color: #7e22ce ! suppressed; }
    .bg-purple-subtle { background-color: #f3e8ff; }
    .border-purple-subtle { border-color: #e9d5ff; }
</style>
@endsection

