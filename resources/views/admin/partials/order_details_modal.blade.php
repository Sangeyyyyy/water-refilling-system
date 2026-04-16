<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg modal-fullscreen-sm-down">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-primary">Order Details Overview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                    <i class="bi bi-receipt text-primary fs-3"></i>
                </div>
                <div>
                    <div id="modal_order_id" class="h4 fw-bold mb-0 text-dark">#00000</div>
                    <div class="text-muted small">Reference ID</div>
                </div>
            </div>
            <div id="modal_status_badge" class="shadow-sm"></div>
        </div>

        <div class="row g-2 g-md-3 mb-4">
            <!-- First Row: Client & Location (Horizontal) -->
            <div class="col-12 col-lg-6">
                <div class="px-3 py-3 bg-light rounded-4 border shadow-sm border-2 border-white h-100">
                    <div class="row g-0 align-items-center mb-0 mb-md-3">
                        <div class="col-12 col-sm-7 mb-2 mb-sm-0">
                            <label class="text-muted small text-uppercase fw-bold d-block mb-1">Client Info</label>
                            <div id="modal_client_name" class="fw-bold fs-5 text-dark mb-1 text-truncate">Name</div>
                            <div id="modal_contact" class="text-secondary small d-flex align-items-center">
                                <i class="bi bi-telephone me-1 text-primary"></i>000-000-0000
                            </div>
                        </div>
                        <div class="col-12 col-sm-5 border-start-sm ps-sm-3 pt-2 pt-sm-0 border-top-sm-none border-top">
                            <label class="text-muted small text-uppercase fw-bold d-block mb-1">Placed</label>
                            <div id="modal_created_at" class="fw-bold text-dark small">Feb 22, 2026</div>
                            <div id="modal_scheduled_at" class="text-muted small">8:30 AM</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="px-3 py-3 bg-light rounded-4 border shadow-sm border-2 border-white h-100">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-geo-alt-fill text-danger me-2 mt-1 fs-5"></i>
                        <div class="overflow-hidden">
                            <label class="text-muted small text-uppercase fw-bold d-block mb-1">Location Details</label>
                            <div id="modal_unit" class="fw-bold text-dark mb-1 text-truncate">Unit Name</div>
                            <div id="modal_hierarchy" class="text-muted small text-truncate">Campus > Division</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Row: Schedule, Specs, Billing (Horizontal) -->
            <div class="col-12 col-md-4">
                <div class="p-3 bg-white rounded-4 border h-100 shadow-sm text-center border-2">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-2">Delivery Timeline</label>
                    <div id="modal_delivery_date" class="fw-bold text-dark fs-5 mb-1">Date</div>
                    
                    <div id="modal_delivered_at_wrapper" class="d-none border-top pt-2 mt-2">
                        <small class="text-muted d-block text-uppercase mb-1" style="font-size: 0.6rem;">Delivered On</small>
                        <span id="modal_delivered_at" class="fw-bold text-success small">Time</span>
                    </div>

                    <div id="modal_scheduled_badge" class="mt-2 pt-2 border-top">
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 small">
                            Scheduled
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-4">
                <div class="p-3 bg-white rounded-4 border h-100 shadow-sm text-center border-2">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Quantity & Total</label>
                    <div class="d-flex justify-content-center align-items-baseline mb-1">
                        <span id="modal_qty" class="fw-bold text-dark fs-4 me-1">0</span>
                        <span class="text-muted small">Gal</span>
                    </div>
                    <div class="text-primary fw-bold fs-5 mb-2">₱<span id="modal_total">0.00</span></div>
                    <div class="px-1">
                        <span id="modal_refill_badge" class="badge rounded-pill px-2 py-1 small w-100 text-truncate">
                            Refill
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-4">
                <div class="px-3 py-3 bg-white rounded-4 border h-100 shadow-sm text-center border-2 d-flex flex-column justify-content-center">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-2">Billing</label>
                    <div class="mb-2">
                        <small class="text-muted d-block text-uppercase mb-0" style="font-size: 0.6rem;">PR Number</small>
                        <span id="modal_pr" class="fw-bold text-dark small text-truncate d-block">N/A</span>
                    </div>
                    <div>
                        <small class="text-muted d-block text-uppercase mb-0" style="font-size: 0.6rem;">Budget Code</small>
                        <span id="modal_budget" class="fw-bold text-dark small text-truncate d-block">N/A</span>
                    </div>
                </div>
            </div>

            <!-- Third Row: Remarks -->
            <div class="col-12">
                <div class="bg-light p-3 rounded-4 border shadow-sm border-2 border-white">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">
                        <i class="bi bi-chat-left-dots-fill me-2 text-primary"></i>Remarks
                    </label>
                    <div id="modal_remarks" class="text-dark bg-white p-2 px-3 rounded-3 border-start border-primary border-4 small" style="min-height: 50px; line-height: 1.4;">
                        No remarks provided.
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0 pb-4 d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-secondary px-4 rounded-pill flex-grow-1" data-bs-dismiss="modal">Close</button>
        
        @if(in_array(auth()->user()->role, ['admin', 'manager', 'director', 'staff']))
            @if(auth()->user()->role === 'staff')
                <a id="modal_print_receipt_btn" href="#" target="_blank" class="btn btn-primary px-4 rounded-pill flex-grow-1">
                    <i class="bi bi-printer me-2"></i>Print Receipt
                </a>
            @else
                <a id="modal_print_receipt_btn" href="#" target="_blank" class="btn btn-outline-primary px-4 rounded-pill flex-grow-1">
                    <i class="bi bi-printer me-2"></i>Receipt
                </a>
                <a id="modal_print_billing_btn" href="#" target="_blank" class="btn btn-primary px-4 rounded-pill flex-grow-1">
                    <i class="bi bi-file-earmark-text me-2"></i>Billing
                </a>
            @endif
        @endif
      </div>
    </div>
  </div>
</div>


<script>
    window.showOrderDetails = function(order, campus, division, unit) {
        // Populate Modal Fields
        // Show actual reference number if available, otherwise padded ID
        document.getElementById('modal_order_id').innerText = order.reference_number || ('#' + String(order.id).padStart(5, '0'));
        document.getElementById('modal_client_name').innerText = order.client_name;
        document.getElementById('modal_contact').innerHTML = '<i class="bi bi-telephone me-1"></i>' + (order.contact_number || 'N/A');
        
        document.getElementById('modal_unit').innerText = unit;

        // Placed Date
        if (order.created_at) {
            const createdAt = new Date(order.created_at);
            document.getElementById('modal_created_at').innerText = createdAt.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            document.getElementById('modal_scheduled_at').innerText = createdAt.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        }
        
        // Build hierarchy smartly - skip if division is a placeholder
        const placeholders = ['no division', 'n/a', 'general', 'none', 'default'];
        const isPlaceholder = division && placeholders.includes(division.toLowerCase());
        
        let hierarchyParts = [campus];
        if (division && !isPlaceholder) {
            hierarchyParts.push(division);
        }
        
        document.getElementById('modal_hierarchy').innerText = hierarchyParts.join(' > ');
        
        document.getElementById('modal_pr').innerText = order.pr_number || 'N/A';
        document.getElementById('modal_budget').innerText = order.budget_code || 'N/A';
        
        // Schedule
        if (order.delivery_date) {
            const date = new Date(order.delivery_date);
            document.getElementById('modal_delivery_date').innerText = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        } else {
            document.getElementById('modal_delivery_date').innerText = 'Immediately';
        }

        // Delivered At
        const deliveredAtWrapper = document.getElementById('modal_delivered_at_wrapper');
        const scheduledBadge = document.getElementById('modal_scheduled_badge');
        
        if (order.status === 'completed' && order.delivered_at) {
            const deliveredDate = new Date(order.delivered_at);
            document.getElementById('modal_delivered_at').innerText = deliveredDate.toLocaleString('en-US', { 
                month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' 
            });
            deliveredAtWrapper.classList.remove('d-none');
            scheduledBadge.classList.add('d-none');
        } else {
            deliveredAtWrapper.classList.add('d-none');
            scheduledBadge.classList.remove('d-none');
        }

        // Order Details
        document.getElementById('modal_qty').innerText = order.quantity;
        document.getElementById('modal_total').innerText = parseFloat(order.total_amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        
        // Refill Badge
        const refillBadge = document.getElementById('modal_refill_badge');
        if (order.is_refill) {
            let label = '<i class="bi bi-recycle me-1"></i> Refill';
            if (order.missing_caps_count > 0) {
                label += ` <span class="ms-1 text-danger fw-bold">(${order.missing_caps_count})</span>`;
            }
            refillBadge.innerHTML = label;
            refillBadge.className = 'badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 px-2 py-1 w-100';
        } else {
            refillBadge.innerHTML = '<i class="bi bi-box-seam me-1"></i> New';
            refillBadge.className = 'badge rounded-pill bg-info bg-opacity-10 text-info border border-info border-opacity-10 px-2 py-1 w-100';
        }

        // Status Badge
        const statusBadge = document.getElementById('modal_status_badge');
        let statusClass = 'bg-secondary';
        switch(order.status) {
            case 'pending': statusClass = 'badge-soft-pending'; break;
            case 'confirmed': statusClass = 'badge-soft-confirmed'; break;
            case 'completed': statusClass = 'badge-soft-completed'; break;
            case 'rejected':
            case 'cancelled': statusClass = 'badge-soft-rejected'; break;
        }
        statusBadge.className = 'badge rounded-pill ' + statusClass + ' px-3 py-2 text-uppercase';
        statusBadge.innerText = order.status;
        statusBadge.style.fontSize = '0.7rem';

        // Print Button Logic
        const receiptBtn = document.getElementById('modal_print_receipt_btn');
        const billingBtn = document.getElementById('modal_print_billing_btn');
        
        const receiptUrl = "{{ route('reports.delivery-receipt', ':id') }}".replace(':id', order.id);
        if (receiptBtn) receiptBtn.href = receiptUrl;
        
        if (billingBtn) {
            const billingUrl = "{{ route('reports.billing', ':id') }}".replace(':id', order.id);
            billingBtn.href = billingUrl;
        }

        // Show Modal
        const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
        modal.show();
    };
</script>

