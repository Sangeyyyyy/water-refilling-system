@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">
            Manage Divisions
            @if(!in_array(auth()->user()->role, ['admin']))
                <span class="badge bg-secondary fs-6 align-middle ms-2">Read-Only</span>
            @endif
        </h2>
        @if(in_array(auth()->user()->role, ['admin']))
            <div class="d-flex gap-2">
                <button type="button" id="bulkDeleteBtn" class="btn btn-outline-danger rounded-pill px-4 d-none" onclick="confirmBulkDelete()">
                    <i class="bi bi-trash me-2"></i>Delete Selected (<span id="selectedCount">0</span>)
                </button>
                <a href="{{ route('divisions.create') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-plus-lg me-2"></i>Add Division
                </a>
            </div>
        @endif
    </div>

    <!-- Search and Filter -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('divisions.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <select name="campus_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Campuses</option>
                        @foreach($campuses as $campus)
                            <option value="{{ $campus->id }}" {{ request('campus_id') == $campus->id ? 'selected' : '' }}>
                                {{ $campus->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="college_office_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Offices</option>
                        @foreach($collegeOffices as $co)
                            <option value="{{ $co->id }}" {{ request('college_office_id') == $co->id ? 'selected' : '' }}>
                                {{ $co->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search divisions..." value="{{ request('search') }}">
                        <button class="btn btn-primary px-4" type="submit">Search</button>
                        @if(request('search') || request('campus_id') || request('college_office_id'))
                            <a href="{{ route('divisions.index') }}" class="btn btn-light border px-3" title="Clear Filters"><i class="bi bi-x-lg"></i></a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        @if(in_array(auth()->user()->role, ['admin']))
                            <th class="ps-4 py-3" style="width: 40px;">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                        @endif
                        <th class="{{ !in_array(auth()->user()->role, ['admin']) ? 'ps-4' : '' }} py-3">Division Name</th>
                        <th class="py-3">Office</th>
                        <th class="py-3">Campus</th>
                        <th class="py-3">Units</th>
                        @if(in_array(auth()->user()->role, ['admin']))
                            <th class="px-4 py-3 text-end">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($divisions as $division)
                        <tr>
                            @if(in_array(auth()->user()->role, ['admin']))
                                <td class="ps-4">
                                    <input type="checkbox" class="form-check-input division-checkbox" value="{{ $division->id }}">
                                </td>
                            @endif
                            <td class="{{ !in_array(auth()->user()->role, ['admin']) ? 'ps-4' : '' }} fw-medium">{{ $division->name }}</td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3">
                                    {{ $division->collegeOffice->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">
                                    {{ $division->collegeOffice->campus->name ?? ($division->campus->name ?? 'N/A') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info rounded-pill px-3">
                                    {{ $division->offices_count }} Units
                                </span>
                            </td>
                            @if(in_array(auth()->user()->role, ['admin']))
                                <td class="px-4 text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('divisions.edit', $division) }}" class="btn btn-sm btn-outline-primary rounded-start-pill px-3">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('divisions.destroy', $division) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this division? All associated units will be deleted.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-end-pill px-3">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ in_array(auth()->user()->role, ['admin']) ? '5' : '3' }}" class="text-center py-5 text-muted">
                                <i class="bi bi-diagram-3 fs-1 d-block mb-3"></i>
                                No divisions found. 
                                @if(in_array(auth()->user()->role, ['admin']))
                                    Start by adding one.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($divisions->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $divisions->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.division-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectedCount = document.getElementById('selectedCount');

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkDeleteBtn();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkDeleteBtn);
        });
    }

    function updateBulkDeleteBtn() {
        const checkedCount = document.querySelectorAll('.division-checkbox:checked').length;
        selectedCount.innerText = checkedCount;
        if (checkedCount > 0) {
            bulkDeleteBtn.classList.remove('d-none');
        } else {
            bulkDeleteBtn.classList.add('d-none');
            selectAll.checked = false;
        }
        
        if (checkedCount === checkboxes.length && checkboxes.length > 0) {
            selectAll.checked = true;
        } else {
            selectAll.checked = false;
        }
    }

    function confirmBulkDelete() {
        const checkedIds = Array.from(document.querySelectorAll('.division-checkbox:checked')).map(cb => cb.value);
        if (checkedIds.length === 0) return;

        if (confirm(`Are you sure you want to delete ${checkedIds.length} selected divisions? This will also delete all associated units.`)) {
            fetch("{{ route('divisions.bulk-delete') }}", {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ids: checkedIds })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'An error occurred while deleting.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }
    }
</script>
@endpush
@endsection
