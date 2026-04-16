@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('offices.index') }}" class="btn btn-link text-decoration-none me-3">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h2 class="fw-bold text-primary mb-0">Add New Unit</h2>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <form action="{{ route('offices.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="division_id" class="form-label fw-semibold">Division <span class="text-danger">*</span></label>
                        <select class="form-select @error('division_id') is-invalid @enderror" id="division_id" name="division_id" required>
                            <option value="">Select Division</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>
                                    {{ $division->collegeOffice->campus->name ?? 'N/A' }} - {{ $division->collegeOffice->name ?? 'N/A' }} - {{ $division->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('division_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Unit Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Enter unit name" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4 mt-4 p-3 bg-light rounded-3 border">
                        <h6 class="fw-bold mb-3 d-flex align-items-center">
                            <i class="bi bi-wallet2 me-2 text-primary"></i>
                            Initial PPMP Allocation (Optional)
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="fiscal_year" class="form-label small text-muted text-uppercase fw-bold">Fiscal Year</label>
                                <input type="number" class="form-control" name="fiscal_year" id="fiscal_year" value="{{ date('Y') }}" min="2020">
                            </div>
                            <div class="col-md-6">
                                <label for="initial_budget" class="form-label small text-muted text-uppercase fw-bold">Initial Budget (₱)</label>
                                <input type="number" step="0.01" class="form-control" name="initial_budget" id="initial_budget" placeholder="0.00">
                            </div>
                        </div>
                        <div class="form-text small mt-2">
                            If provided, an approved PPMP will be created automatically for this unit.
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                            Create Unit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
