@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('divisions.index') }}" class="btn btn-link text-decoration-none me-3">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h2 class="fw-bold text-primary mb-0">Edit Division</h2>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <form action="{{ route('divisions.update', $division) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="college_office_id" class="form-label fw-semibold">Office</label>
                        <select class="form-select @error('college_office_id') is-invalid @enderror" id="college_office_id" name="college_office_id" required>
                            <option value="">Select Office</option>
                            @foreach($collegeOffices as $co)
                                <option value="{{ $co->id }}" {{ (old('college_office_id') ?? $division->college_office_id) == $co->id ? 'selected' : '' }}>
                                    {{ $co->name }} ({{ $co->campus->name ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @error('college_office_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="name" class="form-label fw-semibold">Division Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $division->name) }}" placeholder="Enter division name" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                            Update Division
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
