@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5 animate-fade-in">
        <h1 class="display-4 fw-bold text-primary">Stay Hydrated!</h1>
        <p class="lead text-muted">Order your refill online, anytime. Simple, fast, and fresh.</p>
    </div>

    @php
        $user = Auth::guard('web')->user() ?? Auth::guard('client')->user();
    @endphp

    <form method="POST" action="{{ route('orders.store') }}" id="orderForm">
        @csrf
        <div class="row g-4 justify-content-center">
            <!-- Left Column: Primary Inputs -->
            <div class="col-lg-7">
                <div class="glass-card p-4 p-md-5 animate-fade-in mb-4" style="animation-delay: 0.1s;">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary rounded-circle p-2 me-3 text-white">
                            <i class="bi bi-droplet-fill fs-4"></i>
                        </div>
                        <h4 class="mb-0 fw-bold">Order Details</h4>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Client Name -->
                    @if(!$user)
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label text-muted small text-uppercase fw-bold ls-1">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg rounded-3" id="first_name" name="first_name" placeholder="Juan" required value="{{ old('first_name') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label text-muted small text-uppercase fw-bold ls-1">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg rounded-3" id="last_name" name="last_name" placeholder="dela Cruz" required value="{{ old('last_name') }}">
                        </div>
                    </div>
                    @else
                    <input type="hidden" id="first_name" name="first_name" value="{{ $user->first_name }}">
                    <input type="hidden" id="last_name" name="last_name" value="{{ $user->last_name }}">
                    @endif

                    <!-- Customer Type -->
                    <div class="mb-4">
                        <label class="form-label text-muted small text-uppercase fw-bold ls-1">Ordering As</label>
                        <div class="d-flex gap-3">
                            <div class="form-check custom-option-check flex-fill">
                                <input class="form-check-input d-none" type="radio" name="customer_type" id="type_individual" value="Individual" {{ (old('customer_type', ($user && $user->office_id) ? 'Office' : 'Individual') == 'Individual') ? 'checked' : '' }} onchange="toggleOfficeFields()">
                                <label class="form-check-label w-100 p-3 rounded-4 border text-center cursor-pointer transition-all hover-lift" for="type_individual">
                                    <i class="bi bi-person fs-3 d-block mb-1"></i>
                                    An Individual
                                </label>
                            </div>
                            <div class="form-check custom-option-check flex-fill">
                                <input class="form-check-input d-none" type="radio" name="customer_type" id="type_office" value="Office" {{ old('customer_type', $user && $user->office_id ? 'Office' : '') == 'Office' ? 'checked' : '' }} onchange="toggleOfficeFields()">
                                <label class="form-check-label w-100 p-3 rounded-4 border text-center cursor-pointer transition-all hover-lift" for="type_office">
                                    <i class="bi bi-building fs-3 d-block mb-1"></i>
                                    An Office
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Office Fields (Conditional) -->
                    @if(!$user)
                    <div id="office_fields" style="display: {{ old('customer_type') == 'Office' ? 'block' : 'none' }};">
                        <!-- Hierarchical Office Selection -->
                        <div class="mb-4">
                            <label for="select_campus" class="form-label text-muted small text-uppercase fw-bold ls-1">Select Campus</label>
                            <select class="form-select rounded-3" id="select_campus" onchange="updateDivisions()">
                                <option value="" selected disabled>Choose Campus...</option>
                                @foreach($offices->map(fn($o) => $o->division->campus->name ?? null)->filter()->unique() as $campus)
                                    <option value="{{ $campus }}">{{ $campus }}</option>
                                @endforeach
                                <option value="Others">Others (Manual Entry)</option>
                            </select>
                        </div>

                        <div class="mb-4" id="division_container" style="display: none;">
                            <label for="select_division" class="form-label text-muted small text-uppercase fw-bold ls-1">Select Office / Division</label>
                            <select class="form-select rounded-3" id="select_division" onchange="updateUnits()">
                                <option value="" selected disabled>Choose Division...</option>
                            </select>
                        </div>

                        <div class="mb-4" id="unit_container" style="display: none;">
                            <label for="office_id" class="form-label text-muted small text-uppercase fw-bold ls-1">Select Unit / Section <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg rounded-3 border-primary border-opacity-25" id="office_id" name="office_id" required>
                                <option value="" selected disabled>Choose Unit...</option>
                            </select>
                        </div>

                        <div id="other_location_container" style="display: none;">
                            <div class="mb-4">
                                <label for="other_location" class="form-label text-muted small text-uppercase fw-bold ls-1">Delivery Location</label>
                                <input type="text" class="form-control form-control-lg rounded-3 location-input" id="other_location_guest" name="other_location" placeholder="e.g. Admin Bldg, 2nd Floor" value="{{ old('other_location') }}">
                            </div>
                        </div>
                    </div>
                    @else
                    <div id="office_fields" style="display: {{ (old('customer_type', $user->office_id ? 'Office' : 'Individual') == 'Office') ? 'block' : 'none' }};">
                        <input type="hidden" id="office_id" name="office_id" value="{{ $user->office_id }}">
                        <input type="hidden" id="select_campus" value="{{ $user->office?->division?->campus?->name }}">
                        <input type="hidden" id="select_division" value="{{ $user->office?->division?->name }}">
                    </div>

                    <div id="other_location_container_auth" style="display: {{ (old('customer_type', $user->office_id ? 'Office' : 'Individual') == 'Individual') ? 'block' : 'none' }};">
                        <div class="mb-4">
                            <label for="other_location" class="form-label text-muted small text-uppercase fw-bold ls-1">Delivery Location</label>
                            <input type="text" class="form-control form-control-lg rounded-3 location-input" id="other_location_auth" name="other_location" placeholder="e.g. Admin Bldg, 2nd Floor" value="{{ old('other_location') }}">
                        </div>
                    </div>

                    @endif

                    <!-- PPMP Budget Code Selection (Shared) -->
                    <div class="mb-4" id="budget_code_container" style="display: none;">
                        <label for="budget_code" class="form-label text-muted small text-uppercase fw-bold ls-1">Select Budget Code <span class="text-danger">*</span></label>
                        <select class="form-select form-select-lg rounded-3 border-primary border-opacity-25" id="ppmp_id" name="ppmp_id">
                            <option value="" selected disabled>Choose Budget Code...</option>
                        </select>
                        <input type="hidden" name="budget_code" id="budget_code_hidden">
                        <div class="form-text small mt-1">
                            <i class="bi bi-info-circle me-1"></i> Orders will be deducted from this specific PPMP budget.
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        @if(!$user)
                        <div class="col-md-6">
                            <label for="contact_number" class="form-label text-muted small text-uppercase fw-bold ls-1">Contact # <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg rounded-3" id="contact_number" name="contact_number" placeholder="0912..." required value="{{ old('contact_number') }}">
                        </div>
                        @else
                        <input type="hidden" id="contact_number" name="contact_number" value="{{ $user->contact_number }}">
                        @endif
                        <div class="col-md-6">
                            <label for="quantity" class="form-label text-muted small text-uppercase fw-bold ls-1">Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control form-control-lg rounded-3" id="quantity" name="quantity" min="2" value="{{ old('quantity', 2) }}" required oninput="calculateTotal()">
                            <div class="form-text text-danger small mt-1">
                                <i class="bi bi-info-circle me-1"></i> Minimum order is 2 gallons (Refill/New) to optimize our delivery logistics and routing.
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch bg-light p-3 rounded-4 border border-primary border-opacity-10 shadow-sm animate-fade-in" style="animation-delay: 0.15s;">
                            <input class="form-check-input ms-0 me-3" type="checkbox" id="is_refill" name="is_refill" checked value="1" onchange="toggleCapTracking()">
                            <label class="form-check-label fw-bold text-dark d-block" for="is_refill">
                                <i class="bi bi-recycle text-primary me-2"></i> Refill Only
                                <span class="d-block small text-muted fw-normal mt-1">I will provide my own empty gallon/s for refilling.</span>
                            </label>
                        </div>

                        <!-- Container Ownership -->
                        <div id="container_ownership_wrapper" class="mt-3 animate-fade-in" style="display: block;">
                            <div class="p-3 rounded-4 border bg-light mb-3">
                                <label class="form-label text-muted small text-uppercase fw-bold ls-1 mb-2">Which container are you refilling?</label>
                                <div class="d-flex gap-3 mb-0">
                                    <div class="form-check custom-option-check flex-fill">
                                        <input class="form-check-input d-none" type="radio" name="container_ownership" id="ownership_dnsc" value="dnsc" {{ old('container_ownership', 'dnsc') == 'dnsc' ? 'checked' : '' }}>
                                        <label class="form-check-label w-100 p-2 rounded-3 border text-center cursor-pointer small transition-all" for="ownership_dnsc">
                                            <i class="bi bi-shop me-1"></i> DNSC Container
                                        </label>
                                    </div>
                                    <div class="form-check custom-option-check flex-fill">
                                        <input class="form-check-input d-none" type="radio" name="container_ownership" id="ownership_personal" value="personal" {{ old('container_ownership') == 'personal' ? 'checked' : '' }}>
                                        <label class="form-check-label w-100 p-2 rounded-3 border text-center cursor-pointer small transition-all" for="ownership_personal">
                                            <i class="bi bi-person me-1"></i> Personal Container
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cap Tracking Logic -->
                        <div id="cap_tracking_container" class="animate-fade-in" style="display: block;">
                            <div class="p-3 rounded-4 border bg-light">
                                <label class="form-label text-muted small text-uppercase fw-bold ls-1 mb-2">Cap Status</label>
                                <div class="d-flex gap-3 mb-0">
                                    <div class="form-check custom-option-check flex-fill">
                                        <input class="form-check-input d-none" type="radio" name="caps_complete" id="caps_yes" value="yes" checked onchange="toggleMissingCapsInput()">
                                        <label class="form-check-label w-100 p-2 rounded-3 border text-center cursor-pointer small transition-all" for="caps_yes">
                                            <i class="bi bi-check-circle me-1"></i> Complete
                                        </label>
                                    </div>
                                    <div class="form-check custom-option-check flex-fill">
                                        <input class="form-check-input d-none" type="radio" name="caps_complete" id="caps_no" value="no" onchange="toggleMissingCapsInput()">
                                        <label class="form-check-label w-100 p-2 rounded-3 border text-center cursor-pointer small transition-all" for="caps_no">
                                            <i class="bi bi-exclamation-triangle me-1"></i> Missing Caps
                                        </label>
                                    </div>
                                </div>
                                
                                <div id="missing_caps_input_container" class="mt-2" style="display: none;">
                                    <div class="d-flex align-items-center gap-2">
                                        <label for="missing_caps_count" class="form-label mb-0 small text-muted">How many are missing?</label>
                                        <input type="number" class="form-control form-control-sm rounded-2 text-center" id="missing_caps_count" name="missing_caps_count" value="0" min="0" style="width: 80px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 opacity-25">

                    <!-- Delivery Date Selection -->
                    <div class="mb-4">
                        <label class="form-label text-muted small text-uppercase fw-bold ls-1 mb-3">Delivery Date <span class="text-danger">*</span></label>
                        <div class="date-selector-grid mb-3">
                            @foreach($deliveryDates as $index => $date)
                                <div class="date-card {{ $index === 0 ? 'active' : '' }}" 
                                     onclick="selectDate('{{ $date->format('Y-m-d') }}', this)">
                                    <div class="small fw-bold text-uppercase opacity-75">{{ $date->format('D') }}</div>
                                    <div class="h5 mb-0 fw-bold">{{ $date->format('j') }}</div>
                                    <div class="small text-muted">{{ $date->format('M') }}</div>
                                </div>
                            @endforeach
                        </div>
                        <input type="hidden" id="delivery_date" name="delivery_date" value="{{ $deliveryDates[0]->format('Y-m-d') }}" required>
                    </div>


                    <div class="mb-0">
                        <label for="remarks" class="form-label text-muted small text-uppercase fw-bold ls-1">Remarks (Optional)</label>
                        <textarea class="form-control rounded-4" id="remarks" name="remarks" rows="2" placeholder="Any special instructions?">{{ old('remarks') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Right Column: Sticky Summary Sidebar -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 100px; z-index: 10;">
                    <!-- Order Summary Card -->
                    <div class="glass-card p-4 animate-fade-in mb-4 border-primary border-opacity-10" style="animation-delay: 0.2s;">
                        <h5 class="fw-bold mb-4 d-flex align-items-center">
                            <i class="bi bi-cart3 me-2 text-primary"></i> Order Summary
                        </h5>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Unit Price</span>
                            <span id="unit_price_display">₱{{ number_format($unitPrice, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="text-muted">Quantity</span>
                            <span id="summary_quantity" class="fw-bold">1</span>
                        </div>

                        <div class="text-center py-4 bg-primary bg-opacity-10 rounded-4 mb-3">
                            <div class="small text-uppercase text-primary fw-bold mb-1">Total Amount</div>
                            <h2 class="mb-0 text-primary fw-bold">₱<span id="total_display">0.00</span></h2>
                            <div class="small text-muted mt-1" id="calculation_breakdown">0 x ₱0.00</div>
                        </div>

                        <!-- PPMP Balance (Inside Sidebar) -->
                        <div id="ppmp_balance_container" style="display: none;" class="mb-4">
                            <div class="p-3 rounded-4 border bg-light position-relative overflow-hidden">
                                <!-- Skeleton Overlay -->
                                <div id="ppmp_skeleton" class="position-absolute top-0 start-0 w-100 h-100 bg-light p-3" style="display: none; z-index: 5;">
                                    <div class="d-flex justify-content-between mb-2">
                                        <div class="skeleton skeleton-text w-50"></div>
                                        <div class="skeleton skeleton-badge"></div>
                                    </div>
                                    <div class="skeleton skeleton-text w-75 h-4"></div>
                                    <div class="skeleton skeleton-text w-100 mt-2" style="height: 4px;"></div>
                                </div>

                                <div class="ppmp-content">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="small text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">PPMP Balance (<span id="ppmp_year">----</span>)</div>
                                        <div id="ppmp_status_badge"></div>
                                    </div>
                                    <div class="h5 mb-0 fw-bold text-dark">₱<span id="ppmp_balance_amount">0.00</span></div>
                                    <div class="progress mt-2" style="height: 4px;">
                                        <div id="ppmp_progress_bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="button" onclick="showReviewModal()" class="btn btn-primary btn-lg rounded-pill shadow-lg py-3 transition-all hover-scale">
                                <i class="bi bi-check-circle-fill me-2"></i> Place Order Now
                            </button>
                        </div>
                        
                    </div>

                    <!-- Help Card -->
                    <div class="p-4 rounded-4 bg-white shadow-sm border animate-fade-in" style="animation-delay: 0.3s;">
                        <h6 class="fw-bold mb-2">Need help?</h6>
                        <p class="small text-muted mb-0">
                            Orders are typically delivered within the selected window on Tuesdays and Fridays.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="reviewOrderModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white border-0 py-4">
                <div class="d-flex align-items-center">
                    <div class="bg-white rounded-3 p-1 me-3 d-flex align-items-center shadow-sm">
                        <img src="{{ asset('img/dnsc-logo.png') }}" alt="DNSC" height="35" class="me-1">
                        <img src="{{ asset('img/basd-logo.png') }}" alt="BASD" height="35">
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Confirm Your Order</h5>
                        <p class="small mb-0 text-white-50">Please review your details before final submission.</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="order-summary-list">
                    <div class="summary-item mb-3">
                        <label class="text-muted small text-uppercase fw-bold ls-1 d-block mb-1">Customer</label>
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded p-2 me-2">
                                <i class="bi bi-person text-primary"></i>
                            </div>
                            <div>
                                <div id="modal_client_name" class="fw-bold">Juan Dela Cruz</div>
                                <div id="modal_contact" class="small text-muted">0912-345-6789</div>
                            </div>
                        </div>
                    </div>

                    <div class="summary-item mb-3">
                        <label class="text-muted small text-uppercase fw-bold ls-1 d-block mb-1">Delivery Location</label>
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded p-2 me-2">
                                <i class="bi bi-geo-alt text-primary"></i>
                            </div>
                            <div>
                                <div id="modal_location" class="fw-bold">Office Name</div>
                            </div>
                        </div>
                    </div>

                    <div class="summary-item mb-3" id="modal_budget_code_summary" style="display: none;">
                        <label class="text-muted small text-uppercase fw-bold ls-1 d-block mb-1">Budget Code</label>
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded p-2 me-2">
                                <i class="bi bi-hash text-primary"></i>
                            </div>
                            <div>
                                <div id="modal_budget_code" class="fw-bold">---</div>
                            </div>
                        </div>
                    </div>

                    <div class="summary-item mb-3">
                        <label class="text-muted small text-uppercase fw-bold ls-1 d-block mb-1">Order Type</label>
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded p-2 me-2">
                                <i class="bi bi-recycle text-primary" id="modal_type_icon"></i>
                            </div>
                            <div>
                                <div id="modal_refill_status" class="fw-bold text-primary">Refill Only</div>
                                <div id="modal_container_type" class="small text-muted" style="display: none;">DNSC Container</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="summary-item">
                                <label class="text-muted small text-uppercase fw-bold ls-1 d-block mb-1">Delivery Schedule</label>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded p-2 me-2">
                                        <i class="bi bi-calendar-event text-primary"></i>
                                    </div>
                                    <div>
                                        <div id="modal_delivery_date" class="fw-bold">Feb 10</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="summary-item">
                                <label class="text-muted small text-uppercase fw-bold ls-1 d-block mb-1">Order Volume</label>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded p-2 me-2">
                                        <i class="bi bi-bucket text-primary"></i>
                                    </div>
                                    <div>
                                        <div id="modal_quantity" class="fw-bold">1 Gallon</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-primary bg-opacity-10 rounded-4 p-3 mt-4 text-center">
                        <div class="text-primary small text-uppercase fw-bold mb-1">Total Amount Due</div>
                        <div class="h3 fw-bold text-primary mb-0">₱<span id="modal_total_amount">0.00</span></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 flex-grow-1" data-bs-dismiss="modal">Go Back</button>
                <button type="button" onclick="confirmAndSubmit()" class="btn btn-primary rounded-pill px-4 flex-grow-1">
                    Confirm & Order <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const UNIT_PRICE = {{ $unitPrice }};
    let currentPpmpBalance = Infinity;

    function calculateTotal() {
        let qtyInput = document.getElementById('quantity').value;
        let quantity = qtyInput ? parseInt(qtyInput) : 0;
        if(quantity < 2 && quantity !== 0) {
            // Provide visual feedback if they try to enter 1
            document.getElementById('quantity').classList.add('is-invalid');
        } else {
            document.getElementById('quantity').classList.remove('is-invalid');
        }
        
        let total = quantity * UNIT_PRICE;
        
        // Update Displays
        document.getElementById('total_display').innerText = total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('summary_quantity').innerText = quantity;
        document.getElementById('calculation_breakdown').innerText = `${quantity} x ₱${UNIT_PRICE.toFixed(2)}`;

        // Budget Validation Logic
        const isOffice = document.getElementById('type_office').checked;
        const submitBtn = document.querySelector('button[onclick="showReviewModal()"]');
        const budgetContainer = document.getElementById('ppmp_balance_container');
        const totalAmountText = document.querySelector('.text-primary.fw-bold h2');

        if (isOffice && total > currentPpmpBalance) {
            // Visual Over-budget State
            totalAmountText?.classList.remove('text-primary');
            totalAmountText?.classList.add('text-danger', 'animate-shake');

            const totalContainer = document.getElementById('calculation_breakdown').parentNode;
            totalContainer.classList.remove('bg-primary');
            totalContainer.classList.add('bg-danger');

            // Add subtle warning message if not exists
            if (!document.getElementById('budget_error_msg')) {
                const errorMsg = document.createElement('div');
                errorMsg.id = 'budget_error_msg';
                errorMsg.className = 'small text-danger fw-bold mt-2 animate-fade-in';
                errorMsg.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> Insufficient budget';
                totalContainer.appendChild(errorMsg);
            }
            
            // Highlight the balance card subtly
            budgetContainer?.classList.add('border-danger');
            
            submitBtn.disabled = true;
            submitBtn.classList.add('btn-secondary');
            submitBtn.classList.remove('btn-primary');
        } else {
            // Normal State
            totalAmountText?.classList.add('text-primary');
            totalAmountText?.classList.remove('text-danger', 'animate-shake');
            
            const totalContainer = document.getElementById('calculation_breakdown').parentNode;
            totalContainer.classList.add('bg-primary');
            totalContainer.classList.remove('bg-danger');

            const errorMsg = document.getElementById('budget_error_msg');
            if (errorMsg) errorMsg.remove();
            
            budgetContainer?.classList.remove('border-danger');
            
            submitBtn.disabled = false;
            submitBtn.classList.add('btn-primary');
            submitBtn.classList.remove('btn-secondary');
        }
    }

    // Toggle listener for budget code change
    document.getElementById('ppmp_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        document.getElementById('budget_code_hidden').value = selectedOption.getAttribute('data-code');
        checkPpmpBalance();
        saveToStorage();
    });

    function selectDate(date, element) {
        document.getElementById('delivery_date').value = date;
        document.querySelectorAll('.date-card').forEach(el => el.classList.remove('active'));
        element.classList.add('active');

    }


    function toggleOfficeFields() {
        const isOffice = document.getElementById('type_office').checked;
        const fields = document.getElementById('office_fields');
        const authLocContainer = document.getElementById('other_location_container_auth');
        const guestLocContainer = document.getElementById('other_location_container');
        
        const guestLocInp = document.getElementById('other_location_guest');
        const authLocInp = document.getElementById('other_location_auth');

        if (isOffice) {
            fields.style.display = 'block';
            if (authLocContainer) authLocContainer.style.display = 'none';
            if (guestLocContainer) guestLocContainer.style.display = 'none';
            
            if (guestLocInp) guestLocInp.removeAttribute('required');
            if (authLocInp) authLocInp.removeAttribute('required');

            const unitSelect = document.getElementById('office_id');
            if (unitSelect && unitSelect.tagName === 'SELECT') {
                unitSelect.setAttribute('required', 'required');
            }
            document.getElementById('budget_code_container').style.display = 'block';
            document.getElementById('ppmp_id').setAttribute('required', 'required');
            fetchBudgetCodes();
        } else {
            fields.style.display = 'none';
            const unitSelect = document.getElementById('office_id');
            if (unitSelect) unitSelect.removeAttribute('required');
            
            document.getElementById('budget_code_container').style.display = 'none';
            document.getElementById('ppmp_id').removeAttribute('required');

            if (authLocContainer) {
                authLocContainer.style.display = 'block';
                if (authLocInp) authLocInp.setAttribute('required', 'required');
            }
            if (guestLocContainer) {
                // Check if campus is Others or if it's purely Individual
                const campus = document.getElementById('select_campus')?.value;
                if (!campus || campus === 'Others') {
                    guestLocContainer.style.display = 'block';
                    if (guestLocInp) guestLocInp.setAttribute('required', 'required');
                }
            }
            document.getElementById('ppmp_balance_container').style.display = 'none';
        }

        // Always re-check balance and recalculate totals to clear stale validation messages
        checkPpmpBalance();
    }

    // Hierarchical Office Data
    const officesData = @json($offices);

    function updateDivisions() {
        const campusEl = document.getElementById('select_campus');
        const campus = campusEl ? campusEl.value : null;
        const divisionSelect = document.getElementById('select_division');
        const unitSelect = document.getElementById('office_id');
        const divisionContainer = document.getElementById('division_container');
        const unitContainer = document.getElementById('unit_container');

        if (divisionSelect && divisionSelect.tagName === 'SELECT') {
            divisionSelect.innerHTML = '<option value="" selected disabled>Choose Division...</option>';
            unitSelect.innerHTML = '<option value="" selected disabled>Choose Unit...</option>';
            if (unitContainer) unitContainer.style.display = 'none';

            if (campus === 'Others') {
                if (divisionContainer) divisionContainer.style.display = 'none';
                const guestOtherLoc = document.getElementById('other_location_container');
                if (guestOtherLoc) guestOtherLoc.style.display = 'block';
                
                const guestOtherLocInp = document.getElementById('other_location_guest');
                if (guestOtherLocInp) guestOtherLocInp.setAttribute('required', 'required');
                
                if (unitSelect) unitSelect.removeAttribute('required');
            } else if (campus) {
                const divisions = [...new Set(officesData
                    .filter(o => o.division && o.division.campus && o.division.campus.name === campus)
                    .map(o => o.division.name)
                )].sort();

                divisions.forEach(div => {
                    const opt = document.createElement('option');
                    opt.value = div;
                    opt.textContent = div;
                    divisionSelect.appendChild(opt);
                });
                if (divisionContainer) divisionContainer.style.display = 'block';
                const guestOtherLoc = document.getElementById('other_location_container');
                if (guestOtherLoc) guestOtherLoc.style.display = 'none';
                
                const guestOtherLocInp = document.getElementById('other_location_guest');
                if (guestOtherLocInp) guestOtherLocInp.removeAttribute('required');
                
                if (unitSelect) unitSelect.setAttribute('required', 'required');
            } else {
                if (divisionContainer) divisionContainer.style.display = 'none';
            }
        }
        checkPpmpBalance();
        saveToStorage();
    }

    function updateUnits() {
        const campusEl = document.getElementById('select_campus');
        const campus = campusEl ? campusEl.value : null;
        const divisionEl = document.getElementById('select_division');
        const division = divisionEl ? divisionEl.value : null;
        const unitSelect = document.getElementById('office_id');
        const unitContainer = document.getElementById('unit_container');

        if (unitSelect && unitSelect.tagName === 'SELECT') {
            unitSelect.innerHTML = '<option value="" selected disabled>Choose Unit...</option>';

            if (campus && campus !== 'Others' && division) {
                const units = officesData.filter(o => 
                    o.division && o.division.campus && o.division.campus.name === campus && o.division.name === division
                );
                units.forEach(unit => {
                    const opt = document.createElement('option');
                    opt.value = unit.id;
                    opt.textContent = unit.name;
                    unitSelect.appendChild(opt);
                });
                if (unitContainer) unitContainer.style.display = 'block';
            } else {
                if (unitContainer) unitContainer.style.display = 'none';
            }
        }
        fetchBudgetCodes();
        checkPpmpBalance();
        saveToStorage();
    }

    async function fetchBudgetCodes() {
        const officeId = document.getElementById('office_id').value;
        const ppmpSelect = document.getElementById('ppmp_id');
        const budgetCodeHidden = document.getElementById('budget_code_hidden');
        
        if (!officeId || officeId === 'Others') {
            ppmpSelect.innerHTML = '<option value="" selected disabled>Choose Budget Code...</option>';
            budgetCodeHidden.value = '';
            return;
        }

        try {
            const response = await fetch(`/api/offices/${officeId}/ppmp-budget-codes`);
            const data = await response.json();
            
            ppmpSelect.innerHTML = '<option value="" selected disabled>Choose Budget Code...</option>';
            let helperMessage = document.getElementById('ppmp_help_msg');
            
            if (data.budget_codes && data.budget_codes.length > 0) {
                if (helperMessage) helperMessage.remove();
                data.budget_codes.forEach(ppmp => {
                    const opt = document.createElement('option');
                    opt.value = ppmp.id;
                    opt.textContent = ppmp.display;
                    opt.setAttribute('data-code', ppmp.budget_code);
                    ppmpSelect.appendChild(opt);
                });
                
                // Try to restore saved budget code (now ID) if applicable
                const savedData = localStorage.getItem(storageKey);
                if (savedData) {
                    const parsed = JSON.parse(savedData);
                    const matchingPpmp = data.budget_codes.find(p => p.id == parsed.ppmp_id || p.budget_code == parsed.budget_code);
                    if (matchingPpmp) {
                        ppmpSelect.value = matchingPpmp.id;
                        budgetCodeHidden.value = matchingPpmp.budget_code;
                        checkPpmpBalance();
                    }
                }
            } else {
                const opt = document.createElement('option');
                opt.value = "";
                opt.textContent = "No approved PPMP found";
                opt.disabled = true;
                ppmpSelect.appendChild(opt);

                if (!helperMessage) {
                    helperMessage = document.createElement('div');
                    helperMessage.id = 'ppmp_help_msg';
                    helperMessage.className = 'text-danger small mt-1';
                    ppmpSelect.parentNode.appendChild(helperMessage);
                }
                helperMessage.textContent = "Contact your administrator to set up a PPMP budget for your office.";
            }
        } catch (error) {
            console.error('Error fetching budget codes:', error);
        }
    }

    function toggleCapTracking() {
        const isRefill = document.getElementById('is_refill').checked;
        const container = document.getElementById('cap_tracking_container');
        const ownershipWrapper = document.getElementById('container_ownership_wrapper');
        
        if (isRefill) {
            container.style.display = 'block';
            if(ownershipWrapper) ownershipWrapper.style.display = 'block';
        } else {
            container.style.display = 'none';
            if(ownershipWrapper) ownershipWrapper.style.display = 'none';
            // Reset values
            document.getElementById('caps_yes').checked = true;
            document.getElementById('missing_caps_count').value = 0;
            document.getElementById('missing_caps_input_container').style.display = 'none';
        }
    }

    function toggleMissingCapsInput() {
        const isMissing = document.getElementById('caps_no').checked;
        const inputContainer = document.getElementById('missing_caps_input_container');
        inputContainer.style.display = isMissing ? 'block' : 'none';
        if (!isMissing) {
            document.getElementById('missing_caps_count').value = 0;
        }
    }
    async function checkPpmpBalance() {
        const officeId = document.getElementById('office_id').value;
        const ppmpId = document.getElementById('ppmp_id').value;
        const container = document.getElementById('ppmp_balance_container');
        const balanceAmount = document.getElementById('ppmp_balance_amount');
        const yearSpan = document.getElementById('ppmp_year');
        const badgeContainer = document.getElementById('ppmp_status_badge');
        const progressBar = document.getElementById('ppmp_progress_bar');
        const isOffice = document.getElementById('type_office').checked;

        const skeleton = document.getElementById('ppmp_skeleton');

        if (!isOffice || !officeId || officeId === 'Others') {
            if (container) container.style.display = 'none';
            currentPpmpBalance = Infinity;
            calculateTotal();
            return;
        }

        try {
            // Show skeleton
            container.style.display = 'block';
            skeleton.style.display = 'block';

            const ppmpId = document.getElementById('ppmp_id').value;
            let url = `/api/offices/${officeId}/ppmp-balance`;
            if (ppmpId) {
                url += `?ppmp_id=${ppmpId}`;
            }

            const response = await fetch(url);
            const data = await response.json();

            // Hide skeleton after 300ms for smooth feel
            setTimeout(() => {
                skeleton.style.display = 'none';
            }, 300);
            if (data.has_ppmp) {
                currentPpmpBalance = data.remaining_budget;
                balanceAmount.innerText = data.remaining_budget.toLocaleString('en-US', {minimumFractionDigits: 2});
                yearSpan.innerText = data.fiscal_year;
                
                // Trigger recalculation to check budget
                calculateTotal();
                
                // Color and bar logic
                let percentage = 100;
                if (data.remaining_budget < 500) percentage = 30;
                if (data.remaining_budget <= 0) percentage = 0;

                progressBar.style.width = percentage + '%';
                
                if (data.remaining_budget <= 0) {
                    badgeContainer.innerHTML = '<span class="badge bg-danger rounded-pill">Critical</span>';
                    progressBar.className = 'progress-bar bg-danger';
                } else if (data.remaining_budget < 500) {
                    badgeContainer.innerHTML = '<span class="badge bg-warning text-dark rounded-pill">Low</span>';
                    progressBar.className = 'progress-bar bg-warning';
                } else {
                    badgeContainer.innerHTML = '<span class="badge bg-success rounded-pill">Healthy</span>';
                    progressBar.className = 'progress-bar bg-success';
                }
            } else {
                currentPpmpBalance = 0;
                balanceAmount.innerText = '0.00';
                yearSpan.innerText = '----';
                badgeContainer.innerHTML = '<span class="badge bg-secondary rounded-pill">No Plan</span>';
                progressBar.style.width = '0%';
                progressBar.className = 'progress-bar bg-secondary';
                calculateTotal();
            }
        } catch (error) {
            console.error('Error fetching PPMP balance:', error);
            container.style.display = 'none';
        }
    }

    // Persistance logic
    const storageKey = 'water_refill_client_data_v4';
    function saveToStorage() {
        const guestLoc = document.getElementById('other_location_guest');
        const authLoc = document.getElementById('other_location_auth');
        const locVal = guestLoc ? guestLoc.value : (authLoc ? authLoc.value : '');

        const data = {
            first_name: document.getElementById('first_name')?.value || '',
            last_name: document.getElementById('last_name')?.value || '',
            contact_number: document.getElementById('contact_number')?.value || '',
            campus: document.getElementById('select_campus')?.value || '',
            division: document.getElementById('select_division')?.value || '',
            office_id: document.getElementById('office_id')?.value || '',
            ppmp_id: document.getElementById('ppmp_id')?.value || '',
            budget_code: document.getElementById('budget_code_hidden')?.value || '',
            other_location: locVal,
        };
        localStorage.setItem(storageKey, JSON.stringify(data));
    }

    function loadFromStorage() {
        @if($user) return; @endif // Don't load storage if authenticated (use profile)
        
        const saved = localStorage.getItem(storageKey);
        if (saved) {
            const data = JSON.parse(saved);
            if (data.first_name && document.getElementById('first_name')) document.getElementById('first_name').value = data.first_name;
            if (data.last_name && document.getElementById('last_name')) document.getElementById('last_name').value = data.last_name;
            if (data.contact_number && document.getElementById('contact_number')) document.getElementById('contact_number').value = data.contact_number;
            
            const guestLoc = document.getElementById('other_location_guest');
            const authLoc = document.getElementById('other_location_auth');
            if (data.other_location) {
                if (guestLoc) guestLoc.value = data.other_location;
                if (authLoc) authLoc.value = data.other_location;
            }
            
            if (data.campus && document.getElementById('select_campus')) {
                document.getElementById('select_campus').value = data.campus;
                updateDivisions();
                if (data.campus !== 'Others' && data.division && document.getElementById('select_division')) {
                    document.getElementById('select_division').value = data.division;
                    updateUnits();
                    if (data.office_id && document.getElementById('office_id')) {
                        document.getElementById('office_id').value = data.office_id;
                        checkPpmpBalance();
                    }
                }
            }
        }
    }

    window.addEventListener('DOMContentLoaded', () => {
        loadFromStorage();
        
        // Overwrite storage if authenticated
        @if($user)
        try {
            const authOfficeId = '{{ $user->office_id }}';
            if (authOfficeId) {
                const office = officesData.find(o => o.id == authOfficeId);
                if (office) {
                    document.getElementById('type_office').checked = true;
                    if (office.division && office.division.campus) {
                        document.getElementById('select_campus').value = office.division.campus.name;
                        updateDivisions();
                        document.getElementById('select_division').value = office.division.name;
                        updateUnits();
                    }
                    document.getElementById('office_id').value = office.id;
                }
            }
        } catch (e) {
            console.error("Initialization error:", e);
        }
        @endif

        calculateTotal();
        toggleOfficeFields();

        // Listeners for persistence and calculation
        ['first_name', 'last_name', 'contact_number', 'select_campus', 'select_division', 'office_id', 'other_location_guest', 'other_location_auth', 'quantity'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                // Validation listener
                el.addEventListener('input', () => {
                    validateField(el);
                    if (id === 'quantity') calculateTotal();
                    if (id === 'contact_number') formatPhoneNumber(el);
                });
                
                el.addEventListener('change', () => {
                    saveToStorage();
                    if (id === 'office_id') checkPpmpBalance();
                    validateField(el);
                });
            }
        });
    });

    function validateField(el) {
        if (el.hasAttribute('required')) {
            if (el.value.trim() === '') {
                el.classList.add('is-invalid');
                el.classList.remove('is-valid');
            } else {
                el.classList.remove('is-invalid');
                el.classList.add('is-valid');
            }
        }
    }

    function formatPhoneNumber(el) {
        let value = el.value.replace(/\D/g, ''); // Remove non-digits
        
        if (value.length > 0) {
            if (value.length <= 4) {
                // do nothing
            } else if (value.length <= 7) {
                value = value.slice(0, 4) + '-' + value.slice(4);
            } else {
                value = value.slice(0, 4) + '-' + value.slice(4, 7) + '-' + value.slice(7, 11);
            }
        }
        el.value = value;
    }

    function showReviewModal() {
        try {
            const form = document.getElementById('orderForm');
            if (!form) throw new Error('Order form not found');
            
            // Trigger native validation
            if (!form.checkValidity()) {
                form.reportValidity();
                
                const requiredFields = form.querySelectorAll('[required]');
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                    }
                });
                return;
            }

            // 1. Basic Info
            const firstNameEl = document.getElementById('first_name');
            const lastNameEl = document.getElementById('last_name');
            const contactEl = document.getElementById('contact_number');
            
            if (!firstNameEl || !lastNameEl || !contactEl) {
                throw new Error('Identity fields (Name/Contact) missing from page.');
            }

            document.getElementById('modal_client_name').innerText = `${firstNameEl.value} ${lastNameEl.value}`;
            document.getElementById('modal_contact').innerText = contactEl.value;
            
            // 2. Quantity & Total
            const qtyEl = document.getElementById('quantity');
            const totalDisplayEl = document.getElementById('total_display');
            if (!qtyEl || !totalDisplayEl) throw new Error('Quantity or Total display missing');

            const qty = qtyEl.value;
            document.getElementById('modal_quantity').innerText = `${qty} ${qty > 1 ? 'Gallons' : 'Gallon'}`;
            document.getElementById('modal_total_amount').innerText = totalDisplayEl.innerText;

            // 3. Location Handling
            const typeOfficeEl = document.getElementById('type_office');
            const isOffice = typeOfficeEl ? typeOfficeEl.checked : false;
            const modalLocation = document.getElementById('modal_location');
            const budgetCodeSummary = document.getElementById('modal_budget_code_summary');
            const budgetCodeVal = document.getElementById('budget_code_hidden').value;

            if (isOffice) {
                const campusVal = document.getElementById('select_campus')?.value || 'N/A';
                const divisionVal = document.getElementById('select_division')?.value || 'N/A';
                
                let unitName = '';
                @if($user && $user->office)
                    unitName = "{{ $user->office->name }}";
                @else
                    const unitSelect = document.getElementById('office_id');
                    if (unitSelect) {
                        unitName = unitSelect.tagName === 'SELECT' ? 
                                 (unitSelect.options[unitSelect.selectedIndex]?.text || '') : 
                                 unitSelect.value;
                    }
                @endif
                
                const locParts = [unitName, divisionVal, campusVal].filter(p => p && p !== 'N/A' && p !== 'Unknown Unit');
                modalLocation.innerText = locParts.length > 0 ? locParts.join(', ') : 'Office Location Not Specified';
                
                // Show budget code in summary
                if (budgetCodeSummary) {
                    budgetCodeSummary.style.display = 'block';
                    document.getElementById('modal_budget_code').innerText = budgetCodeVal || '---';
                }
            } else {
                const locGuestEl = document.getElementById('other_location_guest');
                const locAuthEl = document.getElementById('other_location_auth');
                const locVal = (locGuestEl && locGuestEl.offsetParent !== null) ? locGuestEl.value : 
                              (locAuthEl && locAuthEl.offsetParent !== null) ? locAuthEl.value : '';
                
                modalLocation.innerText = locVal || 'Individual / Regular';
                if (budgetCodeSummary) budgetCodeSummary.style.display = 'none';
            }

            // 4. Schedule
            const drDateEl = document.getElementById('delivery_date');
            if (drDateEl) {
                const dateObj = new Date(drDateEl.value);
                if (!isNaN(dateObj.getTime())) {
                    document.getElementById('modal_delivery_date').innerText = dateObj.toLocaleDateString('en-PH', { weekday: 'long', month: 'long', day: 'numeric' });
                } else {
                    document.getElementById('modal_delivery_date').innerText = drDateEl.value;
                }
            }

            // 5. Refill Status
            const refillEl = document.getElementById('is_refill');
            const capsNoEl = document.getElementById('caps_no');
            const missingCapsEl = document.getElementById('missing_caps_count');
            const containerOwnershipEl = document.querySelector('input[name="container_ownership"]:checked');
            
            const isRefill = refillEl ? refillEl.checked : false;
            const isMissingCaps = capsNoEl ? capsNoEl.checked : false;
            const missingCount = (isRefill && isMissingCaps && missingCapsEl) ? (parseInt(missingCapsEl.value) || 0) : 0;
            const ownershipType = containerOwnershipEl ? containerOwnershipEl.value : 'dnsc';
            
            let refillText = isRefill ? 'Refill Only' : 'New Gallon (System Provided)';
            if (isRefill) {
                refillText += (ownershipType === 'personal') ? ' (Personal Container)' : ' (DNSC Container)';
                if (missingCount > 0) {
                    refillText += ` / ${missingCount} missing cap${missingCount > 1 ? 's' : ''}`;
                }
            }
            
            const refillStatusModal = document.getElementById('modal_refill_status');
            const typeIconModal = document.getElementById('modal_type_icon');
            if (refillStatusModal) {
                refillStatusModal.innerText = refillText;
                refillStatusModal.className = isRefill ? 'fw-bold text-primary' : 'fw-bold text-success';
            }
            if (typeIconModal) {
                typeIconModal.className = isRefill ? 'bi bi-recycle text-primary' : 'bi bi-droplet-fill text-success';
            }

            // 6. Show Modal via Bootstrap
            const modalElement = document.getElementById('reviewOrderModal');
            if (!modalElement) throw new Error('Modal element "reviewOrderModal" not found');
            
            const bs = window.bootstrap || (typeof bootstrap !== 'undefined' ? bootstrap : null);
            if (!bs || !bs.Modal) {
                throw new Error('Bootstrap Modal system is not initialized.');
            }

            const reviewModal = new bs.Modal(modalElement);
            reviewModal.show();

        } catch (error) {
            alert('CRITICAL ERROR: ' + error.message + '\nPlease check console for technical details.');
        }
    }

    function confirmAndSubmit() {
        const btn = document.querySelector('#reviewOrderModal .btn-primary');


        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Submitting...';
        
        document.getElementById('orderForm').submit();
    }
</script>
@endsection
