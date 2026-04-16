@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-0 text-dark">Create New User</h2>
                    <p class="text-muted">Register a new system account with designated permissions.</p>
                </div>
                <a href="{{ $tab === 'client' ? route('clients.index') : route('users.index') }}" class="btn btn-light border shadow-sm">
                    <i class="bi bi-arrow-left me-2"></i>Back to List
                </a>
            </div>

            <div class="glass-card p-4 p-lg-5 border-0 shadow animate-fade-in">
                <form action="{{ $tab === 'client' ? route('clients.store') : route('users.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small text-uppercase fw-bold">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required placeholder="e.g. Maria">
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small text-uppercase fw-bold">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required placeholder="e.g. Santos">
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label text-muted small text-uppercase fw-bold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="maria.santos@dnsc.edu.ph">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($tab !== 'client')
                        <div class="col-md-6">
                            <label class="form-label text-muted small text-uppercase fw-bold">System Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required id="user_role">
                                <option value="" selected disabled>Select a role...</option>
                                <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff (Operational - Basic)</option>
                                <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager (Operational - Specialized)</option>
                                <option value="director" {{ old('role') == 'director' ? 'selected' : '' }}>Director (Charlo)</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (System Management)</option>
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
                                    <option value="{{ $office->id }}" {{ old('office_id') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                @endforeach
                            </select>
                            @error('office_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label text-muted small text-uppercase fw-bold">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control @error('contact_number') is-invalid @enderror" value="{{ old('contact_number') }}" placeholder="09XXXXXXXXX">
                            @error('contact_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small text-uppercase fw-bold">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Minimum 8 characters">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#password">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small text-uppercase fw-bold">Confirm Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="Retype password">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#password_confirmation">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-12 mt-5">
                            <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm py-3 rounded-pill fw-bold">
                                <i class="bi bi-person-check me-2"></i>Create Account
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', function() {
        const targetSelector = this.getAttribute('data-target');
        const input = document.querySelector(targetSelector);
        const icon = this.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });
});
</script>
@endsection
