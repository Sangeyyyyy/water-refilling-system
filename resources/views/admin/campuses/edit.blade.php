@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('campuses.index') }}" class="btn btn-link text-decoration-none me-3">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h2 class="fw-bold text-primary mb-0">Edit Campus</h2>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <form action="{{ route('campuses.update', $campus) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="name" class="form-label fw-semibold">Campus Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $campus->name) }}" placeholder="Enter campus name" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                            Update Campus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
