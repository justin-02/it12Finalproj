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
                    
                    <div class="alert-enhanced alert-warning-enhanced">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <small class="fw-medium"><strong>Reminder:</strong> Stock out cannot exceed current inventory.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer modal-footer-enhanced">
                    <button type="button" class="btn btn-secondary-enhanced" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger-red">
                        <i class="bi bi-arrow-up-circle me-2"></i>Deduct Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Modal Container Classes */
    .modal-card {
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        border: none;
        overflow: hidden;
    }

    .modal-header-green {
        background: linear-gradient(135deg, #2d5a3d 0%, #4a7c59 100%);
        border-bottom: none;
        padding: 1.25rem 1.5rem;
    }

    .modal-body-spaced {
        padding: 2rem 1.5rem;
    }

    .modal-footer-enhanced {
        background-color: #f8f9fa;
        border-top: 1px solid #e9ecef;
        padding: 1.25rem 1.5rem;
        border-bottom-left-radius: 16px;
        border-bottom-right-radius: 16px;
    }

    /* Form Component Classes */
    .form-group-enhanced {
        margin-bottom: 1.5rem;
        position: relative;
    }

    .form-label-enhanced {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        display: block;
    }

    .form-control-enhanced {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        background-color: #ffffff;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-control-enhanced:focus {
        border-color: #4a7c59;
        box-shadow: 0 0 0 3px rgba(74, 124, 89, 0.1);
        transform: translateY(-2px);
    }

    /* Style specifically for select elements */
    .form-control-enhanced select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.75rem center;
        background-repeat: no-repeat;
        background-size: 16px 12px;
        padding-right: 2.5rem;
    }

    /* Style specifically for textarea */
    .form-control-enhanced textarea {
        resize: vertical;
        min-height: 80px;
    }

    .form-hint {
        color: #718096;
        font-size: 0.8rem;
        margin-top: 0.5rem;
        display: block;
    }

    /* Button Classes */
    .btn-primary-green {
        background: linear-gradient(135deg, #2d5a3d 0%, #4a7c59 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .btn-primary-green:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(45, 90, 61, 0.3);
        color: white;
    }

    .btn-secondary-enhanced {
        background-color: #ffffff;
        border: 2px solid #e2e8f0;
        color: #4a5568;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .btn-secondary-enhanced:hover {
        background-color: #f7fafc;
        border-color: #cbd5e0;
        transform: translateY(-2px);
        color: #4a5568;
    }

    .btn-danger-red {
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .btn-danger-red:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(229, 62, 62, 0.3);
        color: white;
    }

    /* Alert Classes */
    .alert-enhanced {
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin: 1rem 0;
        border: none;
    }

    .alert-warning-enhanced {
        background-color: rgba(255, 193, 7, 0.1);
        border-left: 4px solid #ecc94b;
        color: #744210;
    }

    .alert-warning-enhanced .bi-exclamation-triangle {
        color: #d69e2e;
        font-size: 1.1rem;
    }

    /* Modal Animations */
    .modal.fade .modal-card {
        transform: translateY(-30px) scale(0.95);
        opacity: 0;
        transition: all 0.3s ease;
    }

    .modal.show .modal-card {
        transform: translateY(0) scale(1);
        opacity: 1;
    }

    /* Responsive Design */A
    @media (max-width: 576px) {
        .modal-body-spaced {
            padding: 1.5rem 1rem;
        }
        
        .modal-header-green,
        .modal-footer-enhanced {
            padding: 1rem 1rem;
        }
        
        .form-control-enhanced {
            padding: 0.625rem 0.875rem;
        }
        
        .row.g-3 {
            margin-left: -0.5rem;
            margin-right: -0.5rem;
        }
        
        .row.g-3 > [class*="col-"] {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        .alert-enhanced {
            padding: 0.875rem 1rem;
        }
    }

    /* Focus States for Accessibility */
    .form-control-enhanced:focus-visible {
        outline: 2px solid #4a7c59;
        outline-offset: 2px;
    }

    .btn-primary-green:focus-visible,
    .btn-secondary-enhanced:focus-visible,
    .btn-danger-red:focus-visible {
        outline: 2px solid #4a7c59;
        outline-offset: 2px;
    }

    /* Enhanced select dropdown styling */
    .form-control-enhanced option {
        padding: 0.5rem;
        border-radius: 8px;
    }

    /* Smooth transitions for all interactive elements */
    .form-control-enhanced,
    .btn-primary-green,
    .btn-secondary-enhanced,
    .btn-danger-red {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Alert enhancement */
    .alert-enhanced small {
        line-height: 1.4;
    }

    /* Button icon spacing */
    .btn i.bi {
        font-size: 0.9em;
    }
</style>