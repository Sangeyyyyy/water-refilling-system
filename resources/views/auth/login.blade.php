@extends('layouts.app')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="glass-card p-5 animate-fade-in" style="max-width: 450px; width: 100%;">
        <div class="text-center mb-4">
            <div class="d-flex justify-content-center align-items-center mb-3">
                <img src="{{ asset('img/dnsc-logo.png') }}" alt="DNSC" height="70" class="me-2 hover-lift">
                <img src="{{ asset('img/basd-logo.png') }}" alt="BASD" height="70" class="hover-lift">
            </div>
            <h2 class="fw-bold text-primary mb-1">Welcome Back!</h2>
            <p class="text-muted small">Sign in to access <span class="text-dnsc-green">DNSC</span> Water Refilling Station</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="form-label fw-bold small text-uppercase text-muted">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-envelope text-primary"></i></span>
                    <input id="email" type="email" class="form-control form-control-lg border-start-0 ps-0 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="name@example.com">
                </div>
                @error('email')
                    <span class="invalid-feedback d-block mt-1" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="form-label fw-bold small text-uppercase text-muted mb-0">{{ __('Password') }} <span class="text-danger">*</span></label>
                    @if (Route::has('password.request'))
                        <a class="btn btn-link btn-sm text-decoration-none p-0" href="{{ route('password.request') }}">
                            {{ __('Forgot Password?') }}
                        </a>
                    @endif
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-lock text-primary"></i></span>
                    <input id="password" type="password" class="form-control form-control-lg border-start-0 border-end-0 ps-0 @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="••••••••">
                    <span class="input-group-text bg-transparent border-start-0 toggle-password" style="cursor: pointer;" data-target="#password">
                        <i class="bi bi-eye text-muted"></i>
                    </span>
                </div>
                @error('password')
                    <span class="invalid-feedback d-block mt-1" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="mb-4 text-center">
                <hr class="mt-0">
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

            <div class="mb-4">
                <div class="form-check custom-option-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label small" for="remember">
                        {{ __('Remember Me') }}
                    </label>
                </div>
            </div>

            <div class="d-grid mb-4">
                <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                    {{ __('Sign In') }}
                </button>
            </div>


        </form>
    </div>
</div>
@endsection
