@php
    $isAdmin = auth()->check() && in_array(auth()->user()->role, ['admin', 'director', 'manager', 'staff']);
    $layout = $isAdmin ? 'layouts.admin' : 'layouts.app';
@endphp

@extends($layout)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                    <i class="bi bi-person-gear fs-4"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0">Profile Settings</h3>
                    <p class="text-muted mb-0">Manage your account information and security</p>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Account Information Card -->
            <div class="glass-card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-0">Account Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small text-uppercase fw-bold">Full Name</label>
                            <div class="form-control-plaintext fw-bold fs-5">{{ $user->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small text-uppercase fw-bold">Email Address</label>
                            <div class="form-control-plaintext fw-bold fs-5">{{ $user->email }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small text-uppercase fw-bold">Account Type</label>
                            <div>
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill text-uppercase fw-bold" style="font-size: 0.7rem;">
                                    {{ isset($user->role) ? ucfirst($user->role) : 'Client' }}
                                </span>
                            </div>
                        </div>
                        @if($user->contact_number)
                        <div class="col-md-6">
                            <label class="form-label text-muted small text-uppercase fw-bold">Contact Number</label>
                            <div class="form-control-plaintext fw-bold fs-5">{{ $user->contact_number }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Change Password Card -->
            <div class="glass-card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-shield-lock me-2 text-primary"></i>
                        <h5 class="fw-bold mb-0">Security & Password</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <div class="form-floating position-relative">
                                <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Current Password" required>
                                <label for="current_password" class="text-muted">Current Password</label>
                                @error('current_password')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4 opacity-50">

                        <x-password-input name="password" label="New Password" :required="true" :withConfirmation="true" />

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold py-3 shadow-sm">
                                <i class="bi bi-shield-check me-2"></i>Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .glass-card {
        background: rgba(var(--bs-body-bg-rgb), 0.8) !important;
        backdrop-filter: blur(10px);
        transition: transform 0.2s ease;
    }
</style>
@endsection
