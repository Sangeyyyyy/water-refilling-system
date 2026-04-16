@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('ppmps.index') }}">PPMP Management</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('ppmps.show', $ppmp->id) }}">PPMP Details</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit PPMP</li>
                </ol>
            </nav>

            <div class="glass-card p-4 p-md-5 shadow-sm">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="bi bi-pencil-square text-warning h3 mb-0"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold text-dark mb-0">Edit Procurement Plan</h3>
                        <p class="text-muted mb-0">Modify the details of the procurement plan and its items.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('ppmps.update', $ppmp->id) }}" id="ppmp-form">
                    @csrf
                    @method('PUT')

                    <div class="row g-4 mb-5">
                        <div class="col-md-4">
                            <label for="office_id" class="form-label text-muted small text-uppercase fw-bold">Office / Unit</label>
                            <select class="form-select border-0 bg-light" id="office_id" name="office_id" required>
                                @foreach($offices as $office)
                                    <option value="{{ $office->id }}" {{ $ppmp->office_id == $office->id ? 'selected' : '' }}>
                                        {{ $office->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="fiscal_year" class="form-label text-muted small text-uppercase fw-bold">Fiscal Year</label>
                            <input type="number" class="form-control border-0 bg-light" id="fiscal_year" name="fiscal_year" value="{{ $ppmp->fiscal_year }}" required>
                        </div>
                        <div class="col-md-3">
                            <label for="budget_code" class="form-label text-muted small text-uppercase fw-bold">Budget Code</label>
                            <input type="text" class="form-control border-0 bg-light" id="budget_code" name="budget_code" value="{{ $ppmp->budget_code }}" placeholder="e.g. 101-2026-2001">
                        </div>
                        <div class="col-md-3">
                            <label for="ppmp_type" class="form-label text-muted small text-uppercase fw-bold">PPMP Type</label>
                            <select class="form-select border-0 bg-light" id="ppmp_type" name="ppmp_type" required>
                                <option value="DBM" {{ $ppmp->ppmp_type == 'DBM' ? 'selected' : '' }}>DBM</option>
                                <option value="NON-DBM" {{ $ppmp->ppmp_type == 'NON-DBM' ? 'selected' : '' }}>NON-DBM</option>
                                <option value="LIB" {{ $ppmp->ppmp_type == 'LIB' ? 'selected' : '' }}>LIB</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="description" class="form-label text-muted small text-uppercase fw-bold">Plan Description / Purpose</label>
                            <input type="text" class="form-control border-0 bg-light" id="description" name="description" value="{{ $ppmp->description }}" placeholder="e.g. Supplies for TSSU daily operations">
                        </div>
                        <div class="col-md-3">
                            <label for="president_approved_date" class="form-label text-muted small text-uppercase fw-bold">President Approved Date</label>
                            <input type="datetime-local" class="form-control border-0 bg-light" id="president_approved_date" name="president_approved_date" value="{{ $ppmp->president_approved_date ? $ppmp->president_approved_date->format('Y-m-d\TH:i') : '' }}">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label text-muted small text-uppercase fw-bold">Status</label>
                            <select class="form-select border-0 bg-light" id="status" name="status" required>
                                <option value="draft" {{ $ppmp->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="approved" {{ $ppmp->status == 'approved' ? 'selected' : '' }}>Approved</option>
                            </select>
                        </div>

                        <div class="col-md-4 position-relative">
                            <label for="fund_manager" class="form-label text-muted small text-uppercase fw-bold">Fund Manager Name</label>
                            <input type="text" class="form-control border-0 bg-light" id="fund_manager" name="fund_manager" value="{{ $ppmp->fund_manager }}" placeholder="Full name of fund manager" autocomplete="off">
                            <div id="manager-suggestions" class="list-group position-absolute w-100 shadow-sm d-none" style="z-index: 1050; top: 100%;"></div>
                        </div>
                        <div class="col-md-4 position-relative">
                            <label for="fund_manager_email" class="form-label text-muted small text-uppercase fw-bold">Fund Manager Email</label>
                            <input type="email" class="form-control border-0 bg-light" id="fund_manager_email" name="fund_manager_email" value="{{ $ppmp->fund_manager_email }}" placeholder="Institutional email address" autocomplete="off">
                            <div id="email-suggestions" class="list-group position-absolute w-100 shadow-sm d-none" style="z-index: 1050; top: 100%;"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="total_budget" class="form-label text-muted small text-uppercase fw-bold">Total Budget Allocation (₱)</label>
                            <input type="number" step="0.01" class="form-control border-0 bg-light fw-bold text-primary" id="total_budget" name="total_budget" readonly value="{{ number_format($ppmp->total_budget, 2, '.', '') }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-list-check me-2"></i>Procurement Items</h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 shadow-sm d-none" id="delete-selected-btn" onclick="deleteSelectedRows()">
                                    <i class="bi bi-trash me-1"></i> Delete Selected
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm" onclick="addItemRow()">
                                    <i class="bi bi-plus-lg me-1"></i> Add Item
                                </button>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" id="items-table">
                                <thead class="bg-light sticky-top shadow-sm small text-uppercase fw-bold">
                                    <tr>
                                        <th class="text-center align-middle" width="40">
                                            <div class="form-check d-flex justify-content-center p-0 m-0">
                                                <input class="form-check-input" type="checkbox" id="select-all-checkbox" onclick="toggleSelectAll(this)">
                                            </div>
                                        </th>
                                        <th class="align-middle" style="min-width: 400px;">Item Description</th>
                                        <th class="align-middle text-center" width="100">Unit</th>
                                        <th class="align-middle text-center" width="180">Procurement Mode</th>
                                        <th class="align-middle text-center" width="100">Qty</th>
                                        <th class="text-center align-middle" width="150">Price (₱)</th>
                                        <th class="text-center align-middle" width="180">Total (₱)</th>
                                        <th class="text-center align-middle" width="50"></th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.9rem;">
                                    @foreach($ppmp->items as $index => $item)
                                    <tr class="item-row">
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center p-0 m-0">
                                                <input class="form-check-input item-checkbox" type="checkbox" onclick="updateBulkDeleteButton()">
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" name="items[{{ $index }}][description]" class="form-control form-control-sm border-0 bg-light" value="{{ $item->description }}" required>
                                        </td>
                                        <td>
                                            <input type="text" name="items[{{ $index }}][unit]" class="form-control form-control-sm border-0 bg-light text-center" value="{{ $item->unit }}">
                                        </td>
                                        <td>
                                            <input type="text" name="items[{{ $index }}][mode_of_procurement]" class="form-control form-control-sm border-0 bg-light text-center" value="{{ $item->mode_of_procurement }}" placeholder="e.g. Shopping">
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{ $index }}][quantity]" class="form-control form-control-sm border-0 bg-light qty-input text-center fw-bold" min="1" value="{{ $item->quantity }}" required oninput="calculateRowTotal(this)">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="items[{{ $index }}][unit_price]" class="form-control form-control-sm border-0 bg-light price-input text-center fw-medium" min="0" value="{{ $item->unit_price }}" required oninput="calculateRowTotal(this)">
                                        </td>
                                        <td class="bg-light bg-opacity-10 text-center">
                                            <input type="text" class="form-control form-control-sm row-total border-0 bg-transparent text-center fw-bold" value="{{ number_format($item->total_price, 2) }}" readonly>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-link text-danger p-0 delete-btn opacity-50 hover-opacity-100" onclick="removeItemRow(this)">
                                                <i class="bi bi-trash fs-6"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-5">
                        <a href="{{ route('ppmps.show', $ppmp->id) }}" class="btn btn-light px-5 py-3 rounded-pill me-3 border">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm px-5 py-3 rounded-pill">Update Procurement Plan</button>
                    </div>
                </form>
            </div>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    let rowCount = {{ $ppmp->items->count() }};

    // Initialize Tom Select for searchable office dropdown
    document.addEventListener('DOMContentLoaded', function() {
        new TomSelect("#office_id", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            placeholder: "Search for an Office or Unit...",
            allowEmptyOption: false,
        });
    });

    function addItemRow() {
        const tbody = document.querySelector('#items-table tbody');
        const newRow = document.createElement('tr');
        newRow.className = 'item-row';
        newRow.innerHTML = `
            <td class="text-center">
                <div class="form-check d-flex justify-content-center p-0">
                    <input class="form-check-input item-checkbox" type="checkbox" onclick="updateBulkDeleteButton()">
                </div>
            </td>
            <td>
                <input type="text" name="items[${rowCount}][description]" class="form-control form-control-sm border-0 bg-light" placeholder="Enter item name..." required>
            </td>
            <td>
                <input type="text" name="items[${rowCount}][unit]" class="form-control form-control-sm border-0 bg-light text-center" placeholder="pcs">
            </td>
            <td>
                <input type="text" name="items[${rowCount}][mode_of_procurement]" class="form-control form-control-sm border-0 bg-light text-center" placeholder="e.g. Shopping">
            </td>
            <td>
                <input type="number" name="items[${rowCount}][quantity]" class="form-control form-control-sm border-0 bg-light qty-input text-center fw-bold" min="1" value="0" required oninput="calculateRowTotal(this)">
            </td>
            <td>
                <input type="number" step="0.01" name="items[${rowCount}][unit_price]" class="form-control form-control-sm border-0 bg-light price-input text-center fw-medium" min="0" value="0.00" required oninput="calculateRowTotal(this)">
            </td>
            <td class="bg-light bg-opacity-10 text-center">
                <input type="text" class="form-control form-control-sm row-total border-0 bg-transparent text-center fw-bold" value="0.00" readonly>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-link text-danger p-0 delete-btn opacity-50 hover-opacity-100" onclick="removeItemRow(this)">
                    <i class="bi bi-trash fs-6"></i>
                </button>
            </td>
        `;
        tbody.appendChild(newRow);
        rowCount++;
        updateDeleteButtons();
        calculateGrandTotal();
        updateBulkDeleteButton();
    }

    function removeItemRow(button) {
        button.closest('tr').remove();
        updateDeleteButtons();
        calculateGrandTotal();
        updateBulkDeleteButton();
    }

    function toggleSelectAll(checkbox) {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(cb => cb.checked = checkbox.checked);
        updateBulkDeleteButton();
    }

    function updateBulkDeleteButton() {
        const checkboxes = document.querySelectorAll('.item-checkbox:checked');
        const deleteBtn = document.getElementById('delete-selected-btn');
        const selectAll = document.getElementById('select-all-checkbox');
        const totalItems = document.querySelectorAll('.item-checkbox').length;
        
        if (checkboxes.length > 0) {
            deleteBtn.classList.remove('d-none');
            deleteBtn.innerHTML = `<i class="bi bi-trash me-1"></i> Delete (${checkboxes.length})`;
        } else {
            deleteBtn.classList.add('d-none');
        }

        if (selectAll) {
            selectAll.checked = (checkboxes.length === totalItems && totalItems > 0);
        }
    }

    function deleteSelectedRows() {
        if (!confirm('Are you sure you want to delete the selected items?')) return;
        
        const checkboxes = document.querySelectorAll('.item-checkbox:checked');
        
        checkboxes.forEach(cb => {
            const row = cb.closest('tr');
            row.remove();
        });

        // Ensure at least one row exists to prevent validation errors
        if (document.querySelectorAll('.item-row').length === 0) {
            addItemRow();
        }
        
        updateDeleteButtons();
        calculateGrandTotal();
        updateBulkDeleteButton();
    }

    function updateDeleteButtons() {
        const rows = document.querySelectorAll('.item-row');
        document.querySelectorAll('.delete-btn').forEach(btn => btn.disabled = (rows.length === 1));
    }

    function calculateRowTotal(input) {
        const row = input.closest('tr');
        const qty = parseInt(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const total = qty * price;
        
        row.querySelector('.row-total').value = total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseInt(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            grandTotal += qty * price;
        });
        
        document.querySelector('[name="total_budget"]').value = grandTotal.toFixed(2);
    }

    // Manager Autocomplete logic
    const fundManagerInput = document.getElementById('fund_manager');
    const fundManagerEmailInput = document.getElementById('fund_manager_email');
    const nameSuggestions = document.getElementById('manager-suggestions');
    const emailSuggestions = document.getElementById('email-suggestions');

    function setupAutocomplete(input, suggestionBox, searchType) {
        let debounceTimer;
        input.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value;
            if (query.length < 2) {
                suggestionBox.classList.add('d-none');
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`{{ route('ppmps.managers') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        suggestionBox.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(manager => {
                                const item = document.createElement('button');
                                item.type = 'button';
                                item.className = 'list-group-item list-group-item-action border-0 py-2';
                                item.innerHTML = `
                                    <div class="fw-bold">${manager.fund_manager}</div>
                                    <div class="small text-muted">${manager.fund_manager_email}</div>
                                `;
                                item.onclick = () => {
                                    fundManagerInput.value = manager.fund_manager;
                                    fundManagerEmailInput.value = manager.fund_manager_email;
                                    suggestionBox.classList.add('d-none');
                                };
                                suggestionBox.appendChild(item);
                            });
                            suggestionBox.classList.remove('d-none');
                        } else {
                            suggestionBox.classList.add('d-none');
                        }
                    });
            }, 300);
        });

        // Close suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !suggestionBox.contains(e.target)) {
                suggestionBox.classList.add('d-none');
            }
        });
    }

    setupAutocomplete(fundManagerInput, nameSuggestions, 'name');
    setupAutocomplete(fundManagerEmailInput, emailSuggestions, 'email');

    // Initial calculation
    updateDeleteButtons();
    calculateGrandTotal();
    updateBulkDeleteButton();
</script>
@endpush
@endsection
