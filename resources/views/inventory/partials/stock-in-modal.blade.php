<!-- Stock In Modal with Pieces Support -->
<div class="modal fade" id="stockInModal" tabindex="-1" aria-labelledby="stockInModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-card">
            <div class="modal-header modal-header-green">
                <h5 class="modal-title text-white fw-semibold">
                    <i class="bi bi-arrow-down-circle me-2"></i>Stock In - Add Inventory
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('inventory.stock-in') }}">
                @csrf
                <div class="modal-body modal-body-spaced">
                    <div class="form-group-enhanced">
                        <label for="stock_product_id" class="form-label-enhanced">Product <span class="text-danger">*</span></label>
                        <select class="form-control-enhanced" id="stock_product_id" name="product_id" required onchange="updateExpirationDays()">
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}" 
                                    data-expiration-days="{{ $product->expiration_days }}"
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
                                <label for="quantity_sacks" class="form-label-enhanced">Quantity (Sacks) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control-enhanced" id="quantity_sacks" name="quantity_sacks" value="0" min="0" step="0.01" required placeholder="0.00">
                                <small class="form-hint"><i class="bi bi-info-circle me-1"></i>Supports decimal (e.g., 2.5 sacks)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="quantity_pieces" class="form-label-enhanced">Quantity (Pieces)</label>
                                <input type="number" class="form-control-enhanced" id="quantity_pieces" name="quantity_pieces" value="0" min="0" placeholder="0">
                                <small class="form-hint"><i class="bi bi-info-circle me-1"></i>Individual pieces count</small>
                            </div>
                        </div>
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
                                <small class="text-muted" id="total_kilos_display">0 kg</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="restock_date" class="form-label-enhanced">Restock Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control-enhanced" id="restock_date" name="restock_date" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required onchange="autoCalculateExpiry()">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="expiry_date" class="form-label-enhanced">Expiry Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control-enhanced" id="expiry_date" name="expiry_date" required readonly tabindex="-1">
                                <small class="form-hint" id="expiry_hint"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group-enhanced">
                        <label for="supplier" class="form-label-enhanced">Supplier</label>
                        <input type="text" class="form-control-enhanced" id="supplier" name="supplier" placeholder="Optional">
                    </div>
                    
                    <div class="form-group-enhanced">
                        <label for="notes" class="form-label-enhanced">Notes</label>
                        <textarea class="form-control-enhanced" id="notes" name="notes" rows="2" placeholder="Add notes about this stock in..."></textarea>
                    </div>
                    
                    <!-- New Stock Summary -->
                    <div class="alert-enhanced alert-success-enhanced mt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-plus-circle me-2"></i>
                                <small class="fw-medium">
                                    <strong>New Stock After Add:</strong> 
                                    <span id="new_sacks_display">0</span> sacks, 
                                    <span id="new_pieces_display">0</span> pieces
                                </small>
                            </div>
                            <div>
                                <small class="text-muted" id="new_kilos_display">0 kg</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer modal-footer-enhanced">
                    <button type="button" class="btn btn-secondary-enhanced" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-green">
                        <i class="bi bi-arrow-down-circle me-2"></i>Add Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize when page loads
    updateExpirationDays();
    updateCurrentStockDisplay();
    
    // Add event listeners for quantity inputs
    document.getElementById('quantity_sacks').addEventListener('input', updateStockCalculations);
    document.getElementById('quantity_pieces').addEventListener('input', updateStockCalculations);
});

function updateExpirationDays() {
    const select = document.getElementById('stock_product_id');
    const selected = select.options[select.selectedIndex];
    const expirationDays = selected.getAttribute('data-expiration-days');
    document.getElementById('restock_date').setAttribute('data-expiration-days', expirationDays);
    autoCalculateExpiry();
    updateCurrentStockDisplay();
}

function updateCurrentStockDisplay() {
    const select = document.getElementById('stock_product_id');
    const selected = select.options[select.selectedIndex];
    
    if (selected.value) {
        const currentSacks = parseFloat(selected.getAttribute('data-sacks') || 0);
        const currentPieces = parseInt(selected.getAttribute('data-pieces') || 0);
        
        // Display current stock
        document.getElementById('current_sacks_display').textContent = currentSacks.toFixed(2);
        document.getElementById('current_pieces_display').textContent = currentPieces;
        
        // Calculate total kilos (50kg per sack)
        const totalKilos = (currentSacks * 50).toFixed(1);
        document.getElementById('total_kilos_display').textContent = totalKilos + ' kg';
        
        // Update new stock calculations
        updateStockCalculations();
    } else {
        // Reset displays if no product selected
        document.getElementById('current_sacks_display').textContent = '0';
        document.getElementById('current_pieces_display').textContent = '0';
        document.getElementById('total_kilos_display').textContent = '0 kg';
        document.getElementById('new_sacks_display').textContent = '0';
        document.getElementById('new_pieces_display').textContent = '0';
        document.getElementById('new_kilos_display').textContent = '0 kg';
    }
}

function updateStockCalculations() {
    const select = document.getElementById('stock_product_id');
    const selected = select.options[select.selectedIndex];
    
    if (selected.value) {
        const currentSacks = parseFloat(selected.getAttribute('data-sacks') || 0);
        const currentPieces = parseInt(selected.getAttribute('data-pieces') || 0);
        const addSacks = parseFloat(document.getElementById('quantity_sacks').value) || 0;
        const addPieces = parseInt(document.getElementById('quantity_pieces').value) || 0;
        
        // Calculate new totals
        const newSacks = currentSacks + addSacks;
        const newPieces = currentPieces + addPieces;
        
        // Display new stock
        document.getElementById('new_sacks_display').textContent = newSacks.toFixed(2);
        document.getElementById('new_pieces_display').textContent = newPieces;
        
        // Calculate new kilos
        const newKilos = (newSacks * 50).toFixed(1);
        document.getElementById('new_kilos_display').textContent = newKilos + ' kg';
    }
}

function autoCalculateExpiry() {
    const restockDate = document.getElementById('restock_date').value;
    const select = document.getElementById('stock_product_id');
    const selected = select.options[select.selectedIndex];
    const expirationDays = parseInt(selected.getAttribute('data-expiration-days') || '0');
    
    if (restockDate && expirationDays > 0) {
        const restock = new Date(restockDate);
        restock.setDate(restock.getDate() + expirationDays);
        
        // Format date as YYYY-MM-DD
        const yyyy = restock.getFullYear();
        const mm = String(restock.getMonth() + 1).padStart(2, '0');
        const dd = String(restock.getDate()).padStart(2, '0');
        
        document.getElementById('expiry_date').value = `${yyyy}-${mm}-${dd}`;
        document.getElementById('expiry_hint').innerText = `Auto: ${expirationDays} days from restock date.`;
    } else {
        document.getElementById('expiry_date').value = '';
        document.getElementById('expiry_hint').innerText = '';
    }
}
</script>

<style>
/* Additional styles for the new elements */

/* Alert enhancements */
.alert-enhanced {
    border-radius: 12px;
    padding: 0.75rem 1rem;
    border: none;
    margin-bottom: 0;
}

.alert-info-enhanced {
    background-color: #e8f4fd;
    color: #0c5460;
    border-left: 4px solid #17a2b8;
}

.alert-success-enhanced {
    background-color: #e8f5e8;
    color: #155724;
    border-left: 4px solid #28a745;
}

.alert-warning-enhanced {
    background-color: #fff3cd;
    color: #856404;
    border-left: 4px solid #ffc107;
}

/* Quantity input improvements */
.form-control-enhanced[type="number"]::-webkit-inner-spin-button,
.form-control-enhanced[type="number"]::-webkit-outer-spin-button {
    opacity: 1;
    height: 2em;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .row.g-3 {
        margin-left: -0.5rem;
        margin-right: -0.5rem;
    }
    
    .row.g-3 > [class*="col-"] {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    .alert-enhanced {
        padding: 0.625rem 0.75rem;
        font-size: 0.875rem;
    }
}

/* Animation for stock updates */
@keyframes pulseUpdate {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}

.pulse {
    animation: pulseUpdate 0.3s ease-in-out;
}
</style>