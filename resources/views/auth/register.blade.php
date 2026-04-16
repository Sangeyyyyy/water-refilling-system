@extends('layouts.app')

@section('content')
<div class="container d-flex align-items-center justify-content-center py-5" style="min-height: 80vh;">
    <div class="glass-card p-5 animate-fade-in" style="max-width: 600px; width: 100%;">
        <div class="text-center mb-4">
            <div class="d-flex justify-content-center align-items-center mb-3">
                <img src="{{ asset('img/dnsc-logo.png') }}" alt="DNSC" height="65" class="me-2 hover-lift">
                <img src="{{ asset('img/basd-logo.png') }}" alt="BASD" height="65" class="hover-lift">
            </div>
            <h2 class="fw-bold text-primary mb-1">{{ __('Create Account') }}</h2>
            <p class="text-muted small">Join the <span class="text-dnsc-green">DNSC</span> Water Refilling Community</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="row g-3">
                <!-- First Name -->
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label fw-bold small text-uppercase text-muted">{{ __('First Name') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-person text-primary"></i></span>
                        <input id="first_name" type="text" class="form-control border-start-0 ps-0 @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" required autocomplete="given-name" autofocus placeholder="Juan">
                    </div>
                    @error('first_name')
                        <span class="invalid-feedback d-block mt-1" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <!-- Last Name -->
                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label fw-bold small text-uppercase text-muted">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-person text-primary"></i></span>
                        <input id="last_name" type="text" class="form-control border-start-0 ps-0 @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" required autocomplete="family-name" placeholder="dela Cruz">
                    </div>
                    @error('last_name')
                        <span class="invalid-feedback d-block mt-1" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>

            <!-- Organizational Structure Selection -->
            <!-- Campus Selection -->
            <div class="mb-3">
                <label for="campus_id" class="form-label fw-bold small text-uppercase text-muted">{{ __('Campus') }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-geo text-primary"></i></span>
                    <select id="campus_id" class="form-select border-start-0 ps-0" name="campus_id">
                        <option value="">Select Campus</option>
                        @foreach($campuses as $campus)
                            <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- College Office Selection (New Level) -->
            <div class="mb-3">
                <label for="college_office_id" class="form-label fw-bold small text-uppercase text-muted">{{ __('Office') }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-building-gear text-primary"></i></span>
                    <select id="college_office_id" class="form-select border-start-0 ps-0" name="college_office_id" disabled>
                        <option value="">Select Office</option>
                        @foreach($college_offices as $co)
                            <option value="{{ $co->id }}" data-campus-id="{{ $co->campus_id }}">{{ $co->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Division Selection -->
            <div class="mb-3">
                <label for="division_id" class="form-label fw-bold small text-uppercase text-muted">{{ __('Division') }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-diagram-3 text-primary"></i></span>
                    <select id="division_id" class="form-select border-start-0 ps-0" name="division_id" disabled>
                        <option value="">Select Division</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" data-college-office-id="{{ $division->college_office_id }}">{{ $division->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Unit Selection -->
            <div class="mb-3">
                <label for="office_id" class="form-label fw-bold small text-uppercase text-muted">{{ __('Unit') }} <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-house text-primary"></i></span>
                    <select id="office_id" class="form-select border-start-0 ps-0 @error('office_id') is-invalid @enderror" name="office_id" required disabled>
                        <option value="">Select Unit</option>
                        @foreach($offices as $office)
                            <option value="{{ $office->id }}" data-division-id="{{ $office->division_id }}">{{ $office->name }}</option>
                        @endforeach
                    </select>
                </div>
                @error('office_id')
                    <span class="invalid-feedback d-block mt-1" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const campusSelect = document.getElementById('campus_id');
                    const collegeOfficeSelect = document.getElementById('college_office_id');
                    const divisionSelect = document.getElementById('division_id');
                    const officeSelect = document.getElementById('office_id');

                    // Store all options initially skip the placeholder/first option
                    const collegeOfficeOptions = Array.from(collegeOfficeSelect.options).slice(1);
                    const divisionOptions = Array.from(divisionSelect.options).slice(1);
                    const officeOptions = Array.from(officeSelect.options).slice(1);

                    // Campus dynamic change
                    campusSelect.addEventListener('change', function() {
                        const selectedCampusId = this.value;
                        
                        // Reset and disable dependent dropdowns
                        collegeOfficeSelect.value = "";
                        collegeOfficeSelect.disabled = !selectedCampusId;
                        divisionSelect.value = "";
                        divisionSelect.disabled = true;
                        officeSelect.value = "";
                        officeSelect.disabled = true;

                        // Clear current options except placeholder
                        while (collegeOfficeSelect.options.length > 1) {
                            collegeOfficeSelect.remove(1);
                        }

                        // Filter and add relevant offices
                        if (selectedCampusId) {
                            const filtered = collegeOfficeOptions.filter(opt => opt.getAttribute('data-campus-id') === selectedCampusId);
                            filtered.forEach(opt => collegeOfficeSelect.add(opt.cloneNode(true)));
                        }
                    });

                    // College Office dynamic change
                    collegeOfficeSelect.addEventListener('change', function() {
                        const selectedCollegeOfficeId = this.value;

                        divisionSelect.value = "";
                        divisionSelect.disabled = !selectedCollegeOfficeId;
                        officeSelect.value = "";
                        officeSelect.disabled = true;

                        while (divisionSelect.options.length > 1) {
                            divisionSelect.remove(1);
                        }

                        if (selectedCollegeOfficeId) {
                            const filtered = divisionOptions.filter(opt => opt.getAttribute('data-college-office-id') === selectedCollegeOfficeId);
                            filtered.forEach(opt => divisionSelect.add(opt.cloneNode(true)));
                        }
                    });

                    // Division dynamic change
                    divisionSelect.addEventListener('change', function() {
                        const selectedDivisionId = this.value;

                        officeSelect.value = "";
                        officeSelect.disabled = !selectedDivisionId;

                        while (officeSelect.options.length > 1) {
                            officeSelect.remove(1);
                        }

                        if (selectedDivisionId) {
                            const filtered = officeOptions.filter(opt => opt.getAttribute('data-division-id') === selectedDivisionId);
                            filtered.forEach(opt => officeSelect.add(opt.cloneNode(true)));
                        }
                    });
                });
            </script>

            <!-- Contact Number -->
            <div class="mb-3">
                <label for="contact_number" class="form-label fw-bold small text-uppercase text-muted">{{ __('Contact Number') }} <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-telephone text-primary"></i></span>
                    <input id="contact_number" type="text" class="form-control border-start-0 ps-0 @error('contact_number') is-invalid @enderror" name="contact_number" value="{{ old('contact_number') }}" required placeholder="09123456789">
                </div>
                @error('contact_number')
                    <span class="invalid-feedback d-block mt-1" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="mb-3">
                <label for="email" class="form-label fw-bold small text-uppercase text-muted">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-envelope text-primary"></i></span>
                    <input id="email" type="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="name@example.com">
                </div>
                @error('email')
                    <span class="invalid-feedback d-block mt-1" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="row g-3">
                <!-- Password -->
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label fw-bold small text-uppercase text-muted">{{ __('Password') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-lock text-primary"></i></span>
                        <input id="password" type="password" class="form-control border-start-0 border-end-0 ps-0 @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="••••••••">
                        <span class="input-group-text bg-transparent border-start-0 toggle-password" style="cursor: pointer;" data-target="#password">
                            <i class="bi bi-eye text-muted"></i>
                        </span>
                    </div>
                    <div class="text-muted tiny mt-1" style="font-size: 0.7rem;">Minimum 8 characters, must include letters and numbers.</div>
                    @error('password')
                        <span class="invalid-feedback d-block mt-1" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="col-md-6 mb-4">
                    <label for="password-confirm" class="form-label fw-bold small text-uppercase text-muted">{{ __('Confirm Password') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-shield-lock text-primary"></i></span>
                        <input id="password-confirm" type="password" class="form-control border-start-0 border-end-0 ps-0" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
                        <span class="input-group-text bg-transparent border-start-0 toggle-password" style="cursor: pointer;" data-target="#password-confirm">
                            <i class="bi bi-eye text-muted"></i>
                        </span>
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

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                    {{ __('Register Now') }}
                </button>
            </div>

            <div class="text-center">
                <p class="text-muted small mb-0">Already have an account? 
                    <a href="{{ route('login') }}" class="fw-bold text-primary text-decoration-none">Sign In</a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
