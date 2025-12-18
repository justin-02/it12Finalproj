<!-- Stock Out Modal - FIXED -->
<div class="modal fade" id="stockOutModal" tabindex="-1" aria-labelledby="stockOutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-card">
            <div class="modal-header modal-header-green">
                <h5 class="modal-title text-white fw-semibold">
                    <i class="bi bi-arrow-up-circle me-2"></i>Stock Out - Deduct Inventory
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('inventory.stock-out') }}" id="stockOutForm">
                @csrf
                <div class="modal-body modal-body-spaced">

                    <div class="form-group-enhanced">
                        <label for="stock_out_product_id" class="form-label-enhanced">Product <span class="text-danger">*</span></label>
                        <select class="form-control-enhanced" id="stock_out_product_id" name="product_id" required onchange="updateCurrentStock()">
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                            <option 
                                value="{{ $product->id }}" 
                                data-name="{{ $product->product_name }}"
                                data-sacks="{{ $product->current_stock_sacks }}" 
                                data-pieces="{{ $product->current_stock_pieces }}"
                                data-sacks-critical="{{ $product->critical_level_sacks }}"
                                data-pieces-critical="{{ $product->critical_level_pieces }}">
                                {{ $product->product_name }} - {{ $product->brand }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Current Stock Display -->
                    <div class="alert-enhanced alert-info-enhanced mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-box-seam me-2"></i>
                                <small class="fw-medium">
                                    <strong>Current Stock:</strong> 
                                    <span id="current_sacks_display">0</span> sacks, 
                                    <span id="current_pieces_display">0</span> pieces
                                </small>
                            </div>
                            <div>
                                <small class="text-muted" id="current_kilos_display">0 kg</small>
                            </div>
                        </div>
                        <div class="mt-1">
                            <small class="text-muted">
                                <strong>Critical Level:</strong> 
                                <span id="critical_sacks_display">0</span> sacks, 
                                <span id="critical_pieces_display">0</span> pieces
                            </small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="quantity_out_sacks" class="form-label-enhanced">Quantity (Sacks) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control-enhanced" id="quantity_out_sacks" name="quantity_sacks" value="0" min="0" step="0.01" required placeholder="0.00" oninput="validateStockOut()">
                                <small class="form-hint"><i class="bi bi-info-circle me-1"></i>Supports decimal (e.g., 2.5 sacks)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="quantity_out_pieces" class="form-label-enhanced">Quantity (Pieces) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control-enhanced" id="quantity_out_pieces" name="quantity_pieces" value="0" min="0" required placeholder="0" oninput="validateStockOut()">
                                <small class="form-hint"><i class="bi bi-info-circle me-1"></i>Whole numbers only</small>
                            </div>
                        </div>
                    </div>

                    <!-- New Stock After Deduction -->
                    <div class="alert-enhanced alert-warning-enhanced mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-dash-circle me-2"></i>
                                <small class="fw-medium">
                                    <strong>After Deduction:</strong> 
                                    <span id="new_sacks_display">0</span> sacks, 
                                    <span id="new_pieces_display">0</span> pieces
                                </small>
                            </div>
                            <div>
                                <small class="text-muted" id="new_kilos_display">0 kg</small>
                            </div>
                        </div>
                        <div class="mt-1" id="critical_warning" style="display: none;">
                            <small class="text-danger fw-bold">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Will reach critical level!
                            </small>
                        </div>
                    </div>

                    <div class="form-group-enhanced">
                        <label for="notes_out" class="form-label-enhanced">Notes (Optional)</label>
                        <textarea class="form-control-enhanced" id="notes_out" name="notes" rows="2" placeholder="Add reason for stock deduction..."></textarea>
                    </div>

                    <!-- Validation Messages -->
                    <div id="quantityWarning" class="alert-enhanced alert-danger-enhanced d-none">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-x-circle me-2"></i>
                            <small class="fw-medium"><strong>Error:</strong> <span id="errorMessage">Entered quantity exceeds current stock.</span></small>
                        </div>
                    </div>

                    <div id="zeroQuantityWarning" class="alert-enhanced alert-warning-enhanced d-none">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-circle me-2"></i>
                            <small class="fw-medium"><strong>Warning:</strong> Both quantities are zero. Please enter at least one quantity.</small>
                        </div>
                    </div>

                </div>

                <div class="modal-footer modal-footer-enhanced">
                    <button type="button" class="btn btn-secondary-enhanced" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" id="submitStockOut" class="btn btn-danger btn-lg fw-semibold shadow-sm" disabled>
                        <i class="bi bi-arrow-up-circle me-2"></i>Deduct Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialize on page load
    updateCurrentStock();
    
    // Add event listeners
    document.getElementById('stock_out_product_id').addEventListener('change', updateCurrentStock);
    document.getElementById('quantity_out_sacks').addEventListener('input', validateStockOut);
    document.getElementById('quantity_out_pieces').addEventListener('input', validateStockOut);
});

function updateCurrentStock() {
    const select = document.getElementById('stock_out_product_id');
    const selected = select.options[select.selectedIndex];
    
    if (selected.value) {
        const currentSacks = parseFloat(selected.dataset.sacks || 0);
        const currentPieces = parseInt(selected.dataset.pieces || 0);
        const criticalSacks = parseFloat(selected.dataset.sacksCritical || 0);
        const criticalPieces = parseInt(selected.dataset.piecesCritical || 0);
        
        // Display current stock
        document.getElementById('current_sacks_display').textContent = currentSacks.toFixed(2);
        document.getElementById('current_pieces_display').textContent = currentPieces;
        document.getElementById('critical_sacks_display').textContent = criticalSacks.toFixed(2);
        document.getElementById('critical_pieces_display').textContent = criticalPieces;
        
        // Calculate total kilos (50kg per sack)
        const totalKilos = (currentSacks * 50).toFixed(1);
        document.getElementById('current_kilos_display').textContent = totalKilos + ' kg';
        
        // Reset inputs and validation
        document.getElementById('quantity_out_sacks').value = 0;
        document.getElementById('quantity_out_pieces').value = 0;
        validateStockOut();
    } else {
        // Reset displays if no product selected
        resetDisplays();
    }
}

function validateStockOut() {
    const select = document.getElementById('stock_out_product_id');
    const selected = select.options[select.selectedIndex];
    const sackInput = document.getElementById('quantity_out_sacks');
    const pieceInput = document.getElementById('quantity_out_pieces');
    const warningAlert = document.getElementById('quantityWarning');
    const zeroWarningAlert = document.getElementById('zeroQuantityWarning');
    const submitButton = document.getElementById('submitStockOut');
    const criticalWarning = document.getElementById('critical_warning');
    
    // Get values
    const currentSacks = parseFloat(selected.dataset.sacks || 0);
    const currentPieces = parseInt(selected.dataset.pieces || 0);
    const criticalSacks = parseFloat(selected.dataset.sacksCritical || 0);
    const criticalPieces = parseInt(selected.dataset.piecesCritical || 0);
    const deductSacks = parseFloat(sackInput.value) || 0;
    const deductPieces = parseInt(pieceInput.value) || 0;
    
    // Reset all warnings
    warningAlert.classList.add('d-none');
    zeroWarningAlert.classList.add('d-none');
    sackInput.classList.remove('is-invalid');
    pieceInput.classList.remove('is-invalid');
    criticalWarning.style.display = 'none';
    submitButton.disabled = false;
    
    // Calculate new stock after deduction
    const newSacks = currentSacks - deductSacks;
    const newPieces = currentPieces - deductPieces;
    
    // Update new stock display
    document.getElementById('new_sacks_display').textContent = newSacks.toFixed(2);
    document.getElementById('new_pieces_display').textContent = newPieces;
    const newKilos = (newSacks * 50).toFixed(1);
    document.getElementById('new_kilos_display').textContent = newKilos + ' kg';
    
    // Check for zero quantities
    if (deductSacks === 0 && deductPieces === 0) {
        zeroWarningAlert.classList.remove('d-none');
        submitButton.disabled = true;
        return;
    }
    
    // Check for negative results (insufficient stock)
    let errorMessage = '';
    let hasError = false;
    
    if (newSacks < 0) {
        errorMessage += `Insufficient sacks. Max: ${currentSacks.toFixed(2)}. `;
        hasError = true;
        sackInput.classList.add('is-invalid');
    }
    
    if (newPieces < 0) {
        errorMessage += `Insufficient pieces. Max: ${currentPieces}.`;
        hasError = true;
        pieceInput.classList.add('is-invalid');
    }
    
    if (hasError) {
        warningAlert.classList.remove('d-none');
        document.getElementById('errorMessage').textContent = errorMessage;
        submitButton.disabled = true;
        return;
    }
    
    // Check if will reach critical level
    if (newSacks <= criticalSacks || newPieces <= criticalPieces) {
        criticalWarning.style.display = 'block';
    }
    
    // Enable submit button if all validations pass
    submitButton.disabled = false;
}

function resetDisplays() {
    document.getElementById('current_sacks_display').textContent = '0';
    document.getElementById('current_pieces_display').textContent = '0';
    document.getElementById('critical_sacks_display').textContent = '0';
    document.getElementById('critical_pieces_display').textContent = '0';
    document.getElementById('current_kilos_display').textContent = '0 kg';
    document.getElementById('new_sacks_display').textContent = '0';
    document.getElementById('new_pieces_display').textContent = '0';
    document.getElementById('new_kilos_display').textContent = '0 kg';
    
    // Disable submit button
    document.getElementById('submitStockOut').disabled = true;
}

// Form submission validation
document.getElementById('stockOutForm').addEventListener('submit', function(e) {
    const sackInput = document.getElementById('quantity_out_sacks');
    const pieceInput = document.getElementById('quantity_out_pieces');
    const deductSacks = parseFloat(sackInput.value) || 0;
    const deductPieces = parseInt(pieceInput.value) || 0;
    
    // Final check for zero quantities
    if (deductSacks === 0 && deductPieces === 0) {
        e.preventDefault();
        alert('Please enter at least one quantity (sacks or pieces) to deduct.');
        return false;
    }
    
    return true;
});
</script>

<style>
.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.2) !important;
}

.alert-danger-enhanced {
    background-color: #f8d7da;
    color: #721c24;
    border-left: 4px solid #dc3545;
}

.alert-warning-enhanced {
    background-color: #fff3cd;
    color: #856404;
    border-left: 4px solid #ffc107;
}

.alert-info-enhanced {
    background-color: #e8f4fd;
    color: #0c5460;
    border-left: 4px solid #17a2b8;
}

/* Button styles for disabled state */
.btn-danger:disabled {
    background-color: #6c757d;
    border-color: #6c757d;
    opacity: 0.65;
    cursor: not-allowed;
}
</style>