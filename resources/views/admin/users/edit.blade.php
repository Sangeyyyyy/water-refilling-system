@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-0 text-dark">Edit User Account</h2>
                    <p class="text-muted">Modify account details and adjust system permissions for {{ $user->name }}.</p>
                </div>
                <a href="{{ $tab === 'client' ? route('clients.index') : route('users.index') }}" class="btn btn-light border shadow-sm">
                    <i class="bi bi-arrow-left me-2"></i>Back to List
                </a>
            </div>

            <div class="glass-card p-4 p-lg-5 border-0 shadow animate-fade-in">
                <form action="{{ $tab === 'client' ? route('clients.update', $user->id) : route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small text-uppercase fw-bold">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $user->first_name) }}" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small text-uppercase fw-bold">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $user->last_name) }}" required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label text-muted small text-uppercase fw-bold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($tab !== 'client')
                        <div class="col-md-6">
                            <label class="form-label text-muted small text-uppercase fw-bold">System Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Staff (Operational - Basic)</option>
                                <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Manager (Operational - Specialized)</option>
                                <option value="director" {{ old('role', $user->role) == 'director' ? 'selected' : '' }}>Director (Charlo)</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin (System Management)</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @else
                            <input type="hidden" name="role" value="client">
                        @endif

                        <div class="col-md-6">
                            <label class="form-label text-muted small text-uppercase fw-bold">Office/Unit</label>
                            <select name="office_id" class="form-select @error('office_id') is-invalid @enderror">
                                <option value="" selected disabled>Select Office (Optional for Admin)</option>
                                @foreach($offices as $office)
                                    <option value="{{ $office->id }}" {{ old('office_id', $user->office_id) == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                @endforeach
                            </select>
                            @error('office_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label text-muted small text-uppercase fw-bold">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control @error('contact_number') is-invalid @enderror" value="{{ old('contact_number', $user->contact_number) }}" placeholder="09XXXXXXXXX">
                            @error('contact_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <div class="p-3 bg-light rounded-3 border mb-3">
                                <div class="d-flex align-items-center text-primary mb-2">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    <span class="fw-bold small text-uppercase">Security Note</span>
                                </div>
                                <p class="small text-muted mb-0">Leave the password fields empty if you do not wish to change the current password.</p>
                            </div>
                        </div>

                        <div class="col-12 text-start mt-2">
                            <x-password-input name="password" label="New Password (Optional)" :required="false" :withConfirmation="true" />
                        </div>

                        <div class="col-12 mt-5">
                            <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm py-3 rounded-pill fw-bold">
                                <i class="bi bi-check-circle me-2"></i>Update Account
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection
