@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-primary">Edit Office</h2>
                <a href="{{ route('college-offices.index') }}" class="btn btn-light rounded-pill px-4">
                    <i class="bi bi-arrow-left me-2"></i>Back to List
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <form action="{{ route('college-offices.update', $collegeOffice) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="campus_id" class="form-label fw-bold">Campus</label>
                            <select name="campus_id" id="campus_id" class="form-select @error('campus_id') is-invalid @enderror" required>
                                <option value="">Select Campus</option>
                                @foreach($campuses as $campus)
                                    <option value="{{ $campus->id }}" {{ (old('campus_id') ?? $collegeOffice->campus_id) == $campus->id ? 'selected' : '' }}>
                                        {{ $campus->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('campus_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold">Office Name</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') ?? $collegeOffice->name }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill">Update Office</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
