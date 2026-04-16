<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="fw-bold">Export Raw Order History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('admin.reports.export') }}" method="GET">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Period</label>
                        <div class="input-group">
                            <input type="date" name="start_date" class="form-control" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                            <span class="input-group-text bg-light border-0">to</span>
                            <input type="date" name="end_date" class="form-control" value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Status Filter</label>
                        <select name="status" class="form-select border-0 bg-light">
                            <option value="">All Statuses</option>
                            <option value="completed">Completed</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success w-100 rounded-pill py-3 fw-bold">
                        <i class="bi bi-download me-2"></i>Generate CSV File
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
