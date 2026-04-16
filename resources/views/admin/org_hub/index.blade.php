@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark">Organization Hub</h2>
            <p class="text-muted mb-0">Manage campuses, offices, divisions, and units in a single view.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addCampusModal">
                <i class="bi bi-plus-lg me-2"></i>New Campus
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <div class="org-tree-container p-4">
                    @forelse($campuses as $campus)
                        <div class="tree-item campus-item mb-3">
                            <div class="tree-header d-flex align-items-center p-3 bg-light rounded-3 border">
                                <i class="bi bi-geo-alt-fill text-primary fs-4 me-3"></i>
                                <div class="flex-grow-1">
                                    <h5 class="mb-0 fw-bold">{{ $campus->name }}</h5>
                                    <span class="badge bg-primary-subtle text-primary small">Campus</span>
                                </div>
                                <div class="tree-actions d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3" 
                                            onclick="openAddOfficeModal({{ $campus->id }}, '{{ $campus->name }}')">
                                        <i class="bi bi-plus-lg me-1"></i> Add Office
                                    </button>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light rounded-circle" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                            <li><a class="dropdown-item" href="{{ route('campuses.edit', $campus->id) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('campuses.destroy', $campus->id) }}" method="POST" onsubmit="return confirm('Delete this campus and all its contents?')">
                                                    @csrf @method('DELETE')
                                                    <input type="hidden" name="redirect_to" value="{{ route('admin.org-hub.index') }}">
                                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Delete</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                    <button class="btn btn-sm btn-light collapse-toggle" data-bs-toggle="collapse" data-bs-target="#campus-{{ $campus->id }}">
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="collapse show ms-4 border-start ps-4 mt-2" id="campus-{{ $campus->id }}">
                                @forelse($campus->collegeOffices as $office)
                                    <div class="tree-item office-item mb-2">
                                        <div class="tree-header d-flex align-items-center p-3 rounded-3 hover-bg-light border mb-2">
                                            <i class="bi bi-building text-info fs-5 me-3"></i>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0 fw-semibold">{{ $office->name }}</h6>
                                                <span class="badge bg-info-subtle text-info x-small">Office</span>
                                            </div>
                                            <div class="tree-actions d-flex gap-2">
                                                <button class="btn btn-sm btn-outline-info rounded-pill px-3"
                                                        onclick="openAddDivisionModal({{ $office->id }}, '{{ $office->name }}')">
                                                    <i class="bi bi-plus-lg me-1"></i> Add Division
                                                </button>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-light rounded-circle" data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                                        <li><a class="dropdown-item" href="{{ route('college-offices.edit', $office->id) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('college-offices.destroy', $office->id) }}" method="POST" onsubmit="return confirm('Delete this office?')">
                                                                @csrf @method('DELETE')
                                                                <input type="hidden" name="redirect_to" value="{{ route('admin.org-hub.index') }}">
                                                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Delete</button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <button class="btn btn-sm btn-light collapse-toggle" data-bs-toggle="collapse" data-bs-target="#office-{{ $office->id }}">
                                                    <i class="bi bi-chevron-down"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="collapse show ms-4 border-start ps-4" id="office-{{ $office->id }}">
                                            @forelse($office->divisions as $division)
                                                <div class="tree-item division-item mb-2">
                                                    <div class="tree-header d-flex align-items-center p-2 rounded-3 hover-bg-light border mb-1">
                                                        <i class="bi bi-diagram-3 text-success me-3"></i>
                                                        <div class="flex-grow-1">
                                                            <span class="mb-0 fw-medium">{{ $division->name }}</span>
                                                            <span class="badge bg-success-subtle text-success x-small ms-2">Division</span>
                                                        </div>
                                                        <div class="tree-actions d-flex gap-2">
                                                            <button class="btn btn-sm btn-outline-success rounded-pill px-2 py-0 small"
                                                                    onclick="openAddUnitModal({{ $division->id }}, '{{ $division->name }}')">
                                                                <i class="bi bi-plus-lg"></i> Unit
                                                            </button>
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-light rounded-circle p-0" style="width: 24px; height: 24px;" data-bs-toggle="dropdown">
                                                                    <i class="bi bi-three-dots-vertical small"></i>
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                                                    <li><a class="dropdown-item py-1 small" href="{{ route('divisions.edit', $division->id) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li>
                                                                        <form action="{{ route('divisions.destroy', $division->id) }}" method="POST" onsubmit="return confirm('Delete this division?')">
                                                                            @csrf @method('DELETE')
                                                                            <input type="hidden" name="redirect_to" value="{{ route('admin.org-hub.index') }}">
                                                                            <button type="submit" class="dropdown-item text-danger py-1 small"><i class="bi bi-trash me-2"></i>Delete</button>
                                                                        </form>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <button class="btn btn-sm btn-light collapse-toggle p-0" style="width: 24px; height: 24px;" data-bs-toggle="collapse" data-bs-target="#division-{{ $division->id }}">
                                                                <i class="bi bi-chevron-down small"></i>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div class="collapse show ms-4 border-start ps-4" id="division-{{ $division->id }}">
                                                        @forelse($division->offices as $unit)
                                                            <div class="tree-header d-flex align-items-center p-2 rounded-3 hover-bg-light border-bottom mb-1 bg-white">
                                                                <i class="bi bi-house-door text-secondary me-3"></i>
                                                                <div class="flex-grow-1">
                                                                    <span class="mb-0 small">{{ $unit->name }}</span>
                                                                    <span class="badge bg-light text-muted x-small ms-2 border">Unit</span>
                                                                </div>
                                                                <div class="tree-actions d-flex gap-2">
                                                                    <div class="dropdown">
                                                                        <button class="btn btn-sm btn-light rounded-circle p-0" style="width: 24px; height: 24px;" data-bs-toggle="dropdown">
                                                                            <i class="bi bi-three-dots-vertical small"></i>
                                                                        </button>
                                                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                                                            <li><a class="dropdown-item py-1 small" href="{{ route('offices.edit', $unit->id) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                                                            <li><hr class="dropdown-divider"></li>
                                                                            <li>
                                                                                <form action="{{ route('offices.destroy', $unit->id) }}" method="POST" onsubmit="return confirm('Delete this unit?')">
                                                                                    @csrf @method('DELETE')
                                                                                    <input type="hidden" name="redirect_to" value="{{ route('admin.org-hub.index') }}">
                                                                                    <button type="submit" class="dropdown-item text-danger py-1 small"><i class="bi bi-trash me-2"></i>Delete</button>
                                                                                </form>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="p-2 text-muted x-small fst-italic">No units registered</div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="p-2 text-muted x-small fst-italic">No divisions registered</div>
                                            @endforelse
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-2 text-muted x-small fst-italic">No offices registered for this campus</div>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="bi bi-diagram-3 text-muted display-1"></i>
                            <h4 class="mt-3 text-muted">No campus data found</h4>
                            <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addCampusModal">
                                Create Your First Campus
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->

<!-- Add Campus Modal -->
<div class="modal fade" id="addCampusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('campuses.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 bg-primary text-white p-4 rounded-top-4">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Add New Campus</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Campus Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g., Main Campus" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Create Campus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Office (CollegeOffice) Modal -->
<div class="modal fade" id="addOfficeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('college-offices.store') }}" method="POST">
                @csrf
                <input type="hidden" name="campus_id" id="modal_office_campus_id">
                <input type="hidden" name="redirect_to" value="{{ route('admin.org-hub.index') }}">
                <div class="modal-header border-0 bg-info text-white p-4 rounded-top-4">
                    <h5 class="modal-title fw-bold"><i class="bi bi-building me-2"></i>Add New Office</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase fw-bold">Campus</label>
                        <div id="modal_office_campus_name" class="fw-bold fs-5 text-dark"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Office Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g., College of Information Technology" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info text-white rounded-pill px-4">Create Office</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Division Modal -->
<div class="modal fade" id="addDivisionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('divisions.store') }}" method="POST">
                @csrf
                <input type="hidden" name="college_office_id" id="modal_division_office_id">
                <input type="hidden" name="redirect_to" value="{{ route('admin.org-hub.index') }}">
                <div class="modal-header border-0 bg-success text-white p-4 rounded-top-4">
                    <h5 class="modal-title fw-bold"><i class="bi bi-diagram-3 me-2"></i>Add New Division</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase fw-bold">Office</label>
                        <div id="modal_division_office_name" class="fw-bold fs-5 text-dark"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Division Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g., Administrative Services" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Create Division</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Unit (Office) Modal -->
<div class="modal fade" id="addUnitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('offices.store') }}" method="POST">
                @csrf
                <input type="hidden" name="division_id" id="modal_unit_division_id">
                <input type="hidden" name="redirect_to" value="{{ route('admin.org-hub.index') }}">
                <div class="modal-header border-0 bg-secondary text-white p-4 rounded-top-4">
                    <h5 class="modal-title fw-bold"><i class="bi bi-house-door me-2"></i>Add New Unit</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase fw-bold">Division</label>
                        <div id="modal_unit_division_name" class="fw-bold fs-5 text-dark"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Unit Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Enter unit name" required>
                    </div>
                    <div class="p-3 bg-light rounded-3 border">
                        <h6 class="fw-bold mb-3 small"><i class="bi bi-wallet2 me-2"></i>Initial PPMP Allocation (Optional)</h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label x-small text-muted text-uppercase fw-bold">Fiscal Year</label>
                                <input type="number" name="fiscal_year" class="form-control form-control-sm" value="{{ date('Y') }}" min="2020">
                            </div>
                            <div class="col-6">
                                <label class="form-label x-small text-muted text-uppercase fw-bold">Budget (₱)</label>
                                <input type="number" step="0.01" name="initial_budget" class="form-control form-control-sm" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-secondary rounded-pill px-4">Create Unit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .x-small { font-size: 0.7rem; }
    .hover-bg-light:hover { background-color: rgba(0,0,0,0.02); }
    .tree-item { transition: all 0.3s ease; }
    .collapse-toggle { 
        width: 30px; 
        height: 30px; 
        display: flex; 
        align-items: center; 
        justify-content: center;
        border-radius: 50%;
        transition: transform 0.3s ease;
    }
    .collapse-toggle[aria-expanded="false"] { transform: rotate(-90deg); }
    
    .border-start { border-left: 2px dashed #dee2e6 !important; }
    
    .campus-item > .tree-header { border-left: 4px solid var(--bs-primary) !important; }
    .office-item > .tree-header { border-left: 4px solid var(--bs-info) !important; }
    .division-item > .tree-header { border-left: 4px solid var(--bs-success) !important; }
</style>
@endpush

@push('scripts')
<script>
    function openAddOfficeModal(campusId, campusName) {
        document.getElementById('modal_office_campus_id').value = campusId;
        document.getElementById('modal_office_campus_name').innerText = campusName;
        new bootstrap.Modal(document.getElementById('addOfficeModal')).show();
    }

    function openAddDivisionModal(officeId, officeName) {
        document.getElementById('modal_division_office_id').value = officeId;
        document.getElementById('modal_division_office_name').innerText = officeName;
        new bootstrap.Modal(document.getElementById('addDivisionModal')).show();
    }

    function openAddUnitModal(divisionId, divisionName) {
        document.getElementById('modal_unit_division_id').value = divisionId;
        document.getElementById('modal_unit_division_name').innerText = divisionName;
        new bootstrap.Modal(document.getElementById('addUnitModal')).show();
    }
</script>
@endpush
@endsection
