@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark">System Settings</h2>
            <p class="text-muted">Manage global application configurations like water price and delivery schedule.</p>
        </div>
        <div>
            <a href="{{ route('home') }}" class="btn btn-light border shadow-sm rounded-pill px-4">
                <i class="bi bi-arrow-left me-2"></i>Dashboard
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        <div class="row g-4">
            <!-- Pricing Settings -->
            <div class="col-lg-6">
                <div class="glass-card border-0 shadow-sm p-4 h-100 animate-fade-in">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3 text-primary">
                            <i class="bi bi-currency-dollar fs-4"></i>
                        </div>
                        <h5 class="fw-bold mb-0">Pricing Configuration</h5>
                    </div>

                    <div class="mb-3">
                        <label for="unit_price" class="form-label text-muted small text-uppercase fw-bold ls-1">Water Price per Gallon (₱)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-end-0">₱</span>
                            <input type="number" step="0.01" class="form-control form-control-lg border-start-0 @error('unit_price') is-invalid @enderror" 
                                id="unit_price" name="unit_price" value="{{ old('unit_price', $settings['unit_price']) }}" placeholder="25.00" required>
                        </div>
                        @error('unit_price')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text mt-2 small text-muted"> This price will be used for all new online and walk-in orders.</div>
                    </div>

                    <hr class="my-4 opacity-10">

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="order_prefix" class="form-label text-muted small text-uppercase fw-bold ls-1">Order Prefix</label>
                            <input type="text" class="form-control @error('order_prefix') is-invalid @enderror" 
                                id="order_prefix" name="order_prefix" value="{{ old('order_prefix', $settings['order_prefix']) }}" placeholder="HST" maxlength="10">
                            @error('order_prefix')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <div class="form-text small text-muted">Format: <strong>PREFIX</strong>-MMDDYY-<strong>SEQUENCE</strong> (e.g., HST-021126-1 for first order of month)</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Settings -->
            <div class="col-lg-6">
                <div class="glass-card border-0 shadow-sm p-4 h-100 animate-fade-in" style="animation-delay: 0.1s;">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3 text-primary">
                            <i class="bi bi-calendar-check fs-4"></i>
                        </div>
                        <h5 class="fw-bold mb-0">Delivery Schedule</h5>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase fw-bold ls-1 d-block mb-3">Allowed Delivery Days</label>
                        <div class="row g-2">
                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-check custom-checkbox-pill p-0">
                                        <input class="form-check-input d-none" type="checkbox" name="delivery_days[]" value="{{ $day }}" 
                                            id="day_{{ $day }}" {{ in_array($day, $settings['delivery_days']) ? 'checked' : '' }}>
                                        <label class="form-check-label w-100 px-3 py-2 rounded-pill border text-center transition-all cursor-pointer hover-lift" for="day_{{ $day }}">
                                            {{ $day }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('delivery_days')
                            <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                        @enderror
                        <div class="form-text mt-3 small text-muted"> Clients will only be able to select these days on the order form.</div>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="col-12 text-end mt-4">
                <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm transition-all hover-scale">
                    <i class="bi bi-save me-2"></i> Save Settings
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    .custom-checkbox-pill input:checked + label {
        background-color: var(--bs-primary);
        color: white;
        border-color: var(--bs-primary);
        box-shadow: 0 4px 12px rgba(var(--bs-primary-rgb), 0.3);
    }
    .custom-checkbox-pill label:hover {
        background-color: #f8f9fa;
        border-color: var(--bs-primary);
    }
</style>
@endsection
