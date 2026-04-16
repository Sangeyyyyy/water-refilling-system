@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('campuses.index') }}" class="btn btn-link text-decoration-none me-3">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h2 class="fw-bold text-primary mb-0">{{ isset($campus) ? 'Edit Campus' : 'Add New Campus' }}</h2>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <form action="{{ isset($campus) ? route('campuses.update', $campus) : route('campuses.store') }}" method="POST">
                    @csrf
                    @if(isset($campus))
                        @method('PUT')
                    @endif

                    <div class="mb-4">
                        <label for="name" class="form-label fw-semibold">Campus Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $campus->name ?? '') }}" placeholder="Enter campus name" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                            {{ isset($campus) ? 'Update Campus' : 'Create Campus' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
