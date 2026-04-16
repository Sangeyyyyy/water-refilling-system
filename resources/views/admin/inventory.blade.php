@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 animate-fade-in">
        <div>
            <h2 class="fw-bold mb-0 text-dark">Inventory Management</h2>
            <p class="text-muted mb-0">Monitor stock levels and manage inventory items.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">
                <i class="bi bi-plus-lg me-2"></i>Add New Item
            </button>
        </div>
    </div>

    <!-- Inventory Table Card -->
    <div class="card border-0 shadow-sm animate-fade-in" style="animation-delay: 0.2s;">
        <div class="card-header bg-white py-3 border-0 d-flex flex-wrap gap-3 justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-primary">Current Inventory</h5>
            <div class="input-group w-100" style="max-width: 300px;">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" class="form-control bg-light border-start-0 ps-0" id="inventorySearch" placeholder="Search items...">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="inventoryTable">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4" style="width: 30%;">Item Name</th>
                        <th class="d-none d-md-table-cell" style="width: 15%;">Last Updated</th>
                        <th style="width: 30%;">Stock Level</th>
                        <th class="text-center d-none d-sm-table-cell" style="width: 20%;">Status</th>
                        <th class="text-end pe-4" style="width: 20%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td class="ps-4">
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $item->item_name }}</h6>
                                <div class="d-sm-none mt-1">
                                    @if($item->stock_level <= $item->low_stock_threshold)
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2">Low</span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">OK</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="small text-muted d-none d-md-table-cell">
                            {{ $item->updated_at->diffForHumans() }}
                        </td>
                        <td>
                            <div class="d-flex flex-column" style="max-width: 200px;">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-bold">{{ number_format($item->stock_level) }}</span>
                                    <span class="text-muted small">{{ $item->unit }}</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    @php
                                        // Simple visual percentage logic
                                        $percent = ($item->stock_level > 0 && $item->low_stock_threshold > 0) 
                                            ? min(100, ($item->stock_level / ($item->low_stock_threshold * 3)) * 100) 
                                            : 0;
                                        $color = $item->stock_level <= $item->low_stock_threshold ? 'bg-danger' : 'bg-success';
                                    @endphp
                                    <div class="progress-bar {{ $color }}" role="progressbar" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center d-none d-sm-table-cell">
                            @if($item->stock_level <= $item->low_stock_threshold)
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> Low Stock
                                </span>
                            @else
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">
                                    <i class="bi bi-check-circle-fill me-1"></i> Optimal
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-light border me-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#adjustStockModal" 
                                        onclick="setAdjustModal('{{ $item->item_name }}', {{ $item->id }}, {{ $item->stock_level }}, {{ $item->low_stock_threshold }}, '{{ $item->unit }}')"
                                        title="Adjust Stock">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                @if(auth()->user()->role === 'admin')
                                <button type="button" class="btn btn-sm btn-light border text-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteItemModal" 
                                        onclick="setDeleteModal('{{ $item->item_name }}', {{ $item->id }})"
                                        title="Delete Item">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                            No inventory items found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top py-3">
             <small class="text-muted">Showing {{ $items->count() }} items</small>
        </div>
    </div>

    <!-- Gallon Distribution Card -->
    <div class="card border-0 shadow-sm animate-fade-in mb-4" style="animation-delay: 0.3s;">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold text-primary">Gallon Distribution</h5>
                <p class="text-muted small mb-0">Tracking units currently held.</p>
            </div>
            <div class="text-end">
                <div class="h4 mb-0 fw-bold text-primary">{{ number_format($total_in_circulation) }}</div>
                <div class="small text-muted text-uppercase fw-bold ls-1 d-none d-sm-block">In Circulation</div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Entity / Name</th>
                        <th class="d-none d-md-table-cell">Type</th>
                        <th class="text-end pe-4">Gallons Held</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $all_entities = collect();
                        foreach($office_distribution as $office) {
                            $all_entities->push([
                                'name' => $office->name, 
                                'hierarchy' => $office->hierarchy,
                                'type' => 'Office', 
                                'count' => $office->gallon_count
                            ]);
                        }
                        foreach($client_distribution as $client) {
                            $all_entities->push([
                                'name' => $client->first_name . ' ' . $client->last_name, 
                                'hierarchy' => $client->office ? $client->office->hierarchy : 'Individual',
                                'type' => 'Individual (Client)', 
                                'count' => $client->gallon_count
                            ]);
                        }
                        foreach($user_distribution as $user) {
                            $all_entities->push([
                                'name' => $user->name, 
                                'hierarchy' => $user->office ? $user->office->hierarchy : 'Individual',
                                'type' => 'Individual (User)', 
                                'count' => $user->gallon_count
                            ]);
                        }
                        $sorted_entities = $all_entities->sortByDesc('count');
                    @endphp

                    @forelse($sorted_entities as $entity)
                    <tr>
                        <td class="ps-4">
                            <h6 class="mb-0 fw-bold">{{ $entity['name'] }}</h6>
                            <div class="text-muted small d-none d-sm-block">{{ $entity['hierarchy'] }}</div>
                        </td>
                        <td class="d-none d-md-table-cell">
                            @if($entity['type'] === 'Office')
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">Office</span>
                            @else
                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill">Individual</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <span class="fw-bold fs-5">{{ number_format($entity['count']) }}</span>
                            <span class="text-muted small ms-1">units</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-5 text-muted">
                            <i class="bi bi-geo-alt fs-1 d-block mb-3"></i>
                            No gallons are currently distributed.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Adjust Stock Modal -->
<div class="modal fade" id="adjustStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form id="adjustStockForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold">Adjust Stock</h5>
                        <p class="text-muted small mb-0">Update inventory levels for <span id="modal_item_name" class="fw-bold text-primary"></span></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Current Stock Display -->
                    <div class="bg-light p-3 rounded-3 mb-4 d-flex justify-content-between align-items-center border">
                        <span class="text-muted small text-uppercase fw-bold">Current Stock</span>
                        <div class="d-flex align-items-baseline">
                            <h2 class="mb-0 fw-bold me-2" id="current_stock_display">0</h2>
                            <span class="text-muted" id="modal_unit_display">pcs</span>
                        </div>
                    </div>

                    <!-- Action Type -->
                    <div class="mb-3">
                        <label class="form-label small text-muted text-uppercase fw-bold">Action</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="action" id="action_add" value="add" checked>
                            <label class="btn btn-outline-primary" for="action_add">
                                <i class="bi bi-plus-lg me-1"></i> Add Stock
                            </label>

                            <input type="radio" class="btn-check" name="action" id="action_set" value="set">
                            <label class="btn btn-outline-secondary" for="action_set">
                                <i class="bi bi-pencil me-1"></i> Set Total
                            </label>
                        </div>
                    </div>

                    <!-- Quantity Input -->
                    <div class="mb-4">
                        <label for="stock_level" class="form-label small text-muted text-uppercase fw-bold">Quantity / New Total</label>
                        <div class="input-group input-group-lg">
                            <input type="number" class="form-control fw-bold text-primary" id="stock_level_input" name="stock_level" required min="0" value="0">
                            <span class="input-group-text bg-light text-muted" id="input_unit_label">pcs</span>
                        </div>
                    </div>

                    <!-- Threshold Input -->
                    <div class="mb-2">
                         <div class="accordion accordion-flush" id="advancedOptions">
                            <div class="accordion-item bg-transparent">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed py-2 px-0 bg-transparent shadow-none text-muted small" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne">
                                        <i class="bi bi-gear-fill me-2"></i> Advanced Options
                                    </button>
                                </h2>
                                <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#advancedOptions">
                                    <div class="accordion-body px-0 pt-2">
                                        <label for="low_stock_threshold" class="form-label small text-muted text-uppercase fw-bold">Low Stock Warning Threshold</label>
                                        <input type="number" class="form-control" id="low_stock_threshold" name="low_stock_threshold" min="0">
                                        <div class="form-text small">Using a custom threshold overrides the system default for this item.</div>
                                    </div>
                                </div>
                            </div>
                         </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light rounded-bottom-4 px-4 py-3">
                    <button type="button" class="btn btn-link text-muted text-decoration-none me-auto" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm">
                        <i class="bi bi-check-lg me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('inventory.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold">Add New Item</h5>
                        <p class="text-muted small mb-0">Create a new inventory resource.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small text-muted text-uppercase fw-bold">Item Name</label>
                        <input type="text" class="form-control" name="item_name" required placeholder="e.g. 500ml Bottle">
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small text-muted text-uppercase fw-bold">Initial Stock</label>
                            <input type="number" class="form-control" name="stock_level" required min="0" value="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted text-uppercase fw-bold">Unit</label>
                            <input type="text" class="form-control" name="unit" required placeholder="e.g. pcs, units, box">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small text-muted text-uppercase fw-bold">Low Stock Threshold</label>
                        <input type="number" class="form-control" name="low_stock_threshold" value="100" min="0">
                        <div class="form-text small">Alert triggers when stock falls below this.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light rounded-bottom-4 px-4 py-3">
                    <button type="button" class="btn btn-link text-muted text-decoration-none me-auto" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm">
                        <i class="bi bi-plus-lg me-2"></i>Create Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body p-4 text-center">
                <div class="mb-3 text-danger bg-danger bg-opacity-10 p-3 rounded-circle d-inline-block">
                    <i class="bi bi-exclamation-triangle-fill fs-3"></i>
                </div>
                <h5 class="fw-bold mb-2">Delete Item?</h5>
                <p class="text-muted small mb-4">Are you sure you want to delete <span id="delete_item_name" class="fw-bold text-dark"></span>? This action cannot be undone.</p>
                
                <form id="deleteItemForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function setAdjustModal(itemName, id, currentStock, threshold, unit) {
        document.getElementById('modal_item_name').innerText = itemName;
        document.getElementById('modal_unit_display').innerText = unit;
        document.getElementById('input_unit_label').innerText = unit;
        document.getElementById('current_stock_display').innerText = currentStock;
        document.getElementById('stock_level_input').value = 0; // Reset input
        document.getElementById('low_stock_threshold').value = threshold;
        
        // Update form action
        const form = document.getElementById('adjustStockForm');
        form.action = `/inventory/${id}`;
        
        // Default to 'add'
        document.getElementById('action_add').checked = true;
    }

    function setDeleteModal(itemName, id) {
        document.getElementById('delete_item_name').innerText = itemName;
        const form = document.getElementById('deleteItemForm');
        form.action = `/inventory/${id}`;
    }

    // Simple Table Search
    document.getElementById('inventorySearch').addEventListener('keyup', function() {
        let searchText = this.value.toLowerCase();
        let tableRows = document.querySelectorAll('#inventoryTable tbody tr');

        tableRows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    });
</script>
<style>
    .hover-shadow:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        transform: translateY(-2px);
        transition: all 0.2s ease-in-out;
    }
</style>
@endpush
@endsection
