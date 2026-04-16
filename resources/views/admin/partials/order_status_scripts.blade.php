<!-- Shared Order Status Scripts & Modals -->

<!-- Generic Confirmation Modal -->
<div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true" style="z-index: 9999;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-body p-4 text-center">
                <div id="confirmModalIcon" class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 64px; height: 64px;">
                    <i class="bi bi-question-circle fs-1"></i>
                </div>
                <h5 class="fw-bold mb-2" id="confirmModalTitle">Confirm Action</h5>
                <p class="text-muted mb-4" id="confirmModalMessage">Are you sure you want to proceed with this action?</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light border-0 text-muted fw-medium px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmModalActionBtn" class="btn btn-primary px-4 fw-bold" style="min-width: 120px;">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FLOATING ACTION BARS -->
<!-- Batch Refill Bar -->
<div id="batchActionBar" class="position-fixed bottom-0 start-50 translate-middle-x mb-4 animate-slide-up w-100 px-3" style="display: none; max-width: 500px; z-index: 9998;">
    <div class="glass-card bg-white p-3 shadow-lg border d-flex flex-wrap justify-content-between align-items-center gap-2" style="border-radius: 16px;">
        <div class="d-flex align-items-center ps-2 flex-grow-1">
            <span class="badge bg-primary rounded-pill me-2" id="selectedCount">0</span>
            <span class="small fw-bold text-dark text-nowrap">Orders Selected</span>
        </div>
        <div class="d-flex flex-wrap gap-2 justify-content-end ms-auto w-100">
            <button type="button" class="btn btn-sm btn-light border px-3 flex-grow-1" onclick="clearBatch()">Cancel</button>
            <button type="button" class="btn btn-sm btn-primary px-3 fw-bold shadow-sm flex-grow-1" onclick="submitBatchDispatch()">
                <i class="bi bi-check-circle me-1"></i> Mark as Refilled
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger px-3 fw-bold shadow-sm flex-grow-1" onclick="submitBatchDelete()">
                <i class="bi bi-trash me-1"></i> Delete All
            </button>
        </div>
    </div>
</div>

<!-- Batch Print Bar -->
<div id="batchPrintActionBar" class="position-fixed bottom-0 start-50 translate-middle-x mb-4 animate-slide-up w-100 px-3" style="display: none; max-width: 500px; z-index: 9998;">
    <div class="glass-card bg-white p-3 shadow-lg border d-flex flex-wrap justify-content-between align-items-center gap-2" style="border-radius: 16px;">
        <div class="d-flex align-items-center ps-2 flex-grow-1">
            <span class="badge bg-primary rounded-pill me-2" id="printBarCount">0</span>
            <span class="small fw-bold text-dark text-nowrap">Deliveries Selected</span>
        </div>
        <div class="d-flex flex-wrap gap-2 justify-content-end ms-auto w-100">
            <button type="button" class="btn btn-sm btn-light border px-3 flex-grow-1" onclick="clearPrintBatch()">Cancel</button>
            <button type="button" class="btn btn-sm btn-primary px-3 fw-bold shadow-sm flex-grow-1" onclick="batchPrintReceipts()">
                <i class="bi bi-printer-fill me-1"></i> Print Receipts
            </button>
        </div>
    </div>
</div>

<form id="batchDispatchForm" method="POST" action="{{ route('admin.orders.batch-status') }}" style="display: none;">
    @csrf
    @method('PUT')
    <input type="hidden" name="status" value="out_for_delivery">
    <div id="batchOrderInputs"></div>
</form>

<form id="deleteOrderForm" method="POST" action="" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<form id="batchDeleteForm" method="POST" action="{{ route('admin.orders.batch-delete') }}" style="display: none;">
    @csrf
    @method('DELETE')
    <div id="batchDeleteOrderInputs"></div>
</form>

@push('scripts')
<script>
    // Global Confirmation Logic
    let confirmCallback = null;
    const confirmModalEl = document.getElementById('confirmActionModal');
    let confirmModal = null;
    if (confirmModalEl) {
        confirmModal = new bootstrap.Modal(confirmModalEl);
    }

    function showConfirmModal(title, message, callback, type = 'confirm') {
        if (!confirmModal) return;
        
        document.getElementById('confirmModalTitle').innerText = title;
        document.getElementById('confirmModalMessage').innerText = message;
        
        const iconContainer = document.getElementById('confirmModalIcon');
        const confirmBtn = document.getElementById('confirmModalActionBtn');
        
        iconContainer.className = 'rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto';
        confirmBtn.className = 'btn px-4 fw-bold';
        
        if (type === 'success') {
            iconContainer.classList.add('bg-success', 'bg-opacity-10', 'text-success');
            iconContainer.innerHTML = '<i class="bi bi-check2-circle fs-1"></i>';
            confirmBtn.classList.add('btn-success');
            confirmBtn.innerText = 'Complete';
        } else if (type === 'danger') {
            iconContainer.classList.add('bg-danger', 'bg-opacity-10', 'text-danger');
            iconContainer.innerHTML = '<i class="bi bi-exclamation-circle fs-1"></i>';
            confirmBtn.classList.add('btn-danger');
            confirmBtn.innerText = 'Confirm';
        } else {
            iconContainer.classList.add('bg-primary', 'bg-opacity-10', 'text-primary');
            iconContainer.innerHTML = '<i class="bi bi-question-circle fs-1"></i>';
            confirmBtn.classList.add('btn-primary');
            confirmBtn.innerText = 'Confirm';
        }
        
        confirmCallback = callback;
        confirmModal.show();
    }

    const confirmActionBtn = document.getElementById('confirmModalActionBtn');
    if (confirmActionBtn) {
        confirmActionBtn.addEventListener('click', function() {
            if (confirmCallback) {
                confirmCallback();
                confirmModal.hide();
            }
        });
    }

    // Batch Selection Logic
    const selectAllCheckbox = document.getElementById('selectAllPending') || document.getElementById('selectAllOrders');
    const orderCheckboxes = document.querySelectorAll('.order-checkbox');
    const batchBar = document.getElementById('batchActionBar');
    const selectedCountSpan = document.getElementById('selectedCount');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            document.querySelectorAll('.order-checkbox').forEach(cb => cb.checked = this.checked);
            updateBatchBar();
        });
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('order-checkbox')) {
            updateBatchBar();
        }
    });

    function updateBatchBar() {
        if (!batchBar) return;
        const checkedCount = document.querySelectorAll('.order-checkbox:checked').length;
        if (selectedCountSpan) selectedCountSpan.innerText = checkedCount;
        batchBar.style.display = checkedCount > 0 ? 'block' : 'none';
    }

    function clearBatch() {
        if (selectAllCheckbox) selectAllCheckbox.checked = false;
        document.querySelectorAll('.order-checkbox').forEach(cb => cb.checked = false);
        updateBatchBar();
    }

    function singleDispatch(id) {
        showConfirmModal('Mark as Refilled?', 'Are you sure you want to mark this order as refilled?', () => {
            const form = document.getElementById('batchDispatchForm');
            form.action = "{{ route('admin.orders.batch-status') }}";
            document.querySelector('#batchDispatchForm input[name="status"]').value = "out_for_delivery";
            
            const inputsContainer = document.getElementById('batchOrderInputs');
            inputsContainer.innerHTML = `<input type="hidden" name="order_ids[]" value="${id}">`;
            form.submit();
        }, 'confirm');
    }

    function singleComplete(id) {
        showConfirmModal('Complete Walk-in?', 'Mark this walk-in order as completed?', () => {
            const form = document.getElementById('batchDispatchForm');
            form.action = `/admin/orders/${id}/status`;
            document.querySelector('#batchDispatchForm input[name="status"]').value = "completed";
            form.submit();
        }, 'success');
    }

    function singleDelete(id) {
        showConfirmModal('Delete Order?', 'Are you sure you want to delete this order? This action cannot be undone and will restore inventory/budget.', () => {
            const form = document.getElementById('deleteOrderForm');
            form.action = `/admin/orders/${id}`;
            form.submit();
        }, 'danger');
    }

    function submitBatchDispatch() {
        const checkedBoxes = document.querySelectorAll('.order-checkbox:checked');
        if (checkedBoxes.length === 0) return;
        
        showConfirmModal('Batch Dispatch?', `Are you sure you want to dispatch ${checkedBoxes.length} orders?`, () => {
            const form = document.getElementById('batchDispatchForm');
            const inputsContainer = document.getElementById('batchOrderInputs');
            inputsContainer.innerHTML = '';
            checkedBoxes.forEach(cb => {
                inputsContainer.innerHTML += `<input type="hidden" name="order_ids[]" value="${cb.value}">`;
            });
            form.submit();
        }, 'confirm');
    }

    function submitBatchDelete() {
        const checkedBoxes = document.querySelectorAll('.order-checkbox:checked');
        if (checkedBoxes.length === 0) return;
        
        showConfirmModal('Batch Delete?', `Are you sure you want to delete ${checkedBoxes.length} orders? This action cannot be undone and will restore inventory/budget.`, () => {
            const form = document.getElementById('batchDeleteForm');
            const inputsContainer = document.getElementById('batchDeleteOrderInputs');
            inputsContainer.innerHTML = '';
            checkedBoxes.forEach(cb => {
                inputsContainer.innerHTML += `<input type="hidden" name="order_ids[]" value="${cb.value}">`;
            });
            form.submit();
        }, 'danger');
    }

    // Batch Print Logic
    const selectAllDeliveries = document.getElementById('selectAllDeliveries');
    const printBar = document.getElementById('batchPrintActionBar');
    const printBarCount = document.getElementById('printBarCount');

    if (selectAllDeliveries) {
        selectAllDeliveries.addEventListener('change', function() {
            document.querySelectorAll('.delivery-checkbox').forEach(cb => cb.checked = this.checked);
            updateBatchPrintUI();
        });
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('delivery-checkbox')) {
            updateBatchPrintUI();
        }
    });

    function updateBatchPrintUI() {
        if (!printBar) return;
        const checkedCount = document.querySelectorAll('.delivery-checkbox:checked').length;
        if (printBarCount) printBarCount.textContent = checkedCount;
        printBar.style.display = checkedCount > 0 ? 'block' : 'none';
    }

    function clearPrintBatch() {
        if (selectAllDeliveries) selectAllDeliveries.checked = false;
        document.querySelectorAll('.delivery-checkbox').forEach(cb => cb.checked = false);
        updateBatchPrintUI();
    }

    function batchPrintReceipts() {
        const checkedBoxes = document.querySelectorAll('.delivery-checkbox:checked');
        if (checkedBoxes.length === 0) return;

        if (checkedBoxes.length > 4) {
            alert('You can only print a maximum of 4 receipts at a time.');
            return;
        }

        const ids = Array.from(checkedBoxes).map(cb => cb.value).join(',');
        window.open(`/reports/delivery-receipts/batch?ids=${ids}`, '_blank');
    }
</script>
@endpush
