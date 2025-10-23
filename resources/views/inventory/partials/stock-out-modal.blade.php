<!-- Stock Out Modal -->
<div class="modal fade" id="stockOutModal" tabindex="-1" aria-labelledby="stockOutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-card">
            <div class="modal-header modal-header-green">
                <h5 class="modal-title text-white fw-semibold">
                    <i class="bi bi-arrow-up-circle me-2"></i>Stock Out - Deduct Inventory
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('inventory.stock-out') }}">
                @csrf
                <div class="modal-body modal-body-spaced">

                    <div class="form-group-enhanced">
                        <label for="stock_out_product_id" class="form-label-enhanced">Product <span class="text-danger">*</span></label>
                        <select class="form-control-enhanced" id="stock_out_product_id" name="product_id" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                            <option 
                                value="{{ $product->id }}" 
                                data-name="{{ $product->product_name }}"
                                data-sacks="{{ $product->current_stock_sacks }}" 
                                data-pieces="{{ $product->current_stock_pieces }}">
                                {{ $product->product_name }} - {{ $product->brand }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="quantity_out_sacks" class="form-label-enhanced">Quantity (Sacks)</label>
                                <input type="number" class="form-control-enhanced" id="quantity_out_sacks" name="quantity_sacks" value="0" min="0" step="0.01" placeholder="0.00">
                                <small class="form-hint"><i class="bi bi-info-circle me-1"></i>Supports decimal (e.g., 2.5 sacks)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="quantity_out_pieces" class="form-label-enhanced">Quantity (Pieces)</label>
                                <input type="number" class="form-control-enhanced" id="quantity_out_pieces" name="quantity_pieces" value="0" min="0" placeholder="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-group-enhanced">
                        <label for="notes_out" class="form-label-enhanced">Notes (Optional)</label>
                        <textarea class="form-control-enhanced" id="notes_out" name="notes" rows="2" placeholder="Add reason for stock deduction..."></textarea>
                    </div>

                    <!-- Static Reminder -->
                    <div class="alert-enhanced alert-warning-enhanced" id="staticReminder">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <small class="fw-medium"><strong>Reminder:</strong> Stock out cannot exceed current inventory.</small>
                        </div>
                    </div>

                    <!-- Dynamic Warning -->
                    <div id="quantityWarning" class="alert-enhanced alert-warning-enhanced d-none">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <small class="fw-medium"><strong>Warning:</strong> Entered quantity exceeds current stock.</small>
                        </div>
                    </div>

                </div>

                <div class="modal-footer modal-footer-enhanced">
                    <button type="button" class="btn btn-secondary-enhanced" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" id="submitStockOut" class="btn btn-danger btn-lg fw-semibold shadow-sm">
                        <i class="bi bi-arrow-up-circle me-2"></i>Deduct Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS VALIDATION SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const productSelect = document.getElementById('stock_out_product_id');
    const sackInput = document.getElementById('quantity_out_sacks');
    const pieceInput = document.getElementById('quantity_out_pieces');
    const warningAlert = document.getElementById('quantityWarning');
    const submitButton = document.getElementById('submitStockOut');

    let currentSacks = 0;
    let currentPieces = 0;

    // When product changes, get current stock
    productSelect.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        currentSacks = parseFloat(selected.dataset.sacks || 0);
        currentPieces = parseInt(selected.dataset.pieces || 0);
        hideWarning();
    });

    // Listen to input changes
    [sackInput, pieceInput].forEach(input => {
        input.addEventListener('input', validateStock);
    });

    function validateStock() {
        const sacks = parseFloat(sackInput.value) || 0;
        const pieces = parseInt(pieceInput.value) || 0;

        if (sacks > currentSacks || pieces > currentPieces) {
            showWarning();
        } else {
            hideWarning();
        }
    }

    function showWarning() {
        warningAlert.classList.remove('d-none');
        sackInput.classList.add('is-invalid');
        pieceInput.classList.add('is-invalid');
        submitButton.disabled = true;
    }

    function hideWarning() {
        warningAlert.classList.add('d-none');
        sackInput.classList.remove('is-invalid');
        pieceInput.classList.remove('is-invalid');
        submitButton.disabled = false;
    }
});
</script>

<style>
.is-invalid {
    border-color: #fa1414ff !important;
    box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.2) !important;
}
</style>
