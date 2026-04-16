<!-- Walk-in Modal -->
<div class="modal fade" id="walkInModal" tabindex="-1" aria-labelledby="walkInModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="walkInModalLabel">Record Walk-in Order</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.walkin.store') }}" method="POST" onsubmit="return confirmWalkIn();">
          @csrf
          <div class="modal-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small text-uppercase fw-bold">First Name</label>
                        <input type="text" name="first_name" class="form-control" placeholder="Optional" maxlength="255">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small text-uppercase fw-bold">Last Name</label>
                        <input type="text" name="last_name" class="form-control" placeholder="Optional" maxlength="255">
                    </div>
                </div>

                <input type="hidden" name="customer_type" value="Individual">

                <div class="text-center mb-4">
                    <label class="form-label text-muted small text-uppercase fw-bold">How many containers? <span class="text-danger">*</span></label>
                    <div class="d-flex justify-content-center align-items-center">
                        <button type="button" class="btn btn-outline-primary rounded-circle p-2" onclick="adjustQty(-1)" style="width: 45px; height: 45px;">
                            <i class="bi bi-dash-lg"></i>
                        </button>
                        <input type="number" id="walkin_quantity" name="quantity" class="form-control form-control-lg text-center border-0 fw-bold mx-3 @error('quantity') is-invalid @enderror" style="font-size: 2rem; width: 100px;" min="1" value="{{ old('quantity', 1) }}" oninput="updateTotal()" required>
                        <button type="button" class="btn btn-outline-primary rounded-circle p-2" onclick="adjustQty(1)" style="width: 45px; height: 45px;">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                    @error('quantity')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="text-center mb-4 bg-light rounded-4 py-3 border">
                    <div class="text-muted small text-uppercase fw-bold mb-1">Total Amount</div>
                    <div class="h3 fw-bold text-primary mb-0">₱<span id="walkin_total">{{ number_format($unitPrice, 2) }}</span></div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small text-uppercase fw-bold d-block text-center mb-3">When will it be picked up? <span class="text-danger">*</span></label>
                    <div class="row g-3">
                        <div class="col-6">
                            <div id="pickup_now" class="slot-option {{ old('pickup_type', 'now') == 'now' ? 'active' : '' }}" onclick="selectPickup('now', this)">
                                <i class="bi bi-check-circle-fill text-primary mb-2 d-xl-block h4"></i>
                                <div class="fw-bold">Pick up Now</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div id="pickup_later" class="slot-option {{ old('pickup_type') == 'later' ? 'active' : '' }}" onclick="selectPickup('later', this)">
                                <i class="bi bi-calendar2-event text-primary mb-2 d-xl-block h4"></i>
                                <div class="fw-bold">Pick up Later</div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="pickup_type" name="pickup_type" value="{{ old('pickup_type', 'now') }}">
                    @error('pickup_type')
                        <div class="invalid-feedback d-block text-center mt-2">{{ $message }}</div>
                    @enderror
                </div>

          </div>
          <div class="modal-footer border-0 pb-4">
            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary px-5 shadow-sm">Process Order</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
    function confirmWalkIn() {
        const pickupType = document.getElementById('pickup_type').value;
        if (pickupType === 'now') {
            return confirm('This will instantly complete the order and deduct inventory. Proceed?');
        }
        return true;
    }
</script>
