<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-card">
            <div class="modal-header modal-header-green">
                <h5 class="modal-title text-white fw-semibold">
                    <i class="bi bi-pencil-square me-2"></i>Edit Product
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" id="editProductForm">
                @csrf
                @method('PUT')
                <div class="modal-body modal-body-spaced">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="edit_product_name" class="form-label-enhanced">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control-enhanced" id="edit_product_name" name="product_name" required placeholder="Enter product name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="edit_brand" class="form-label-enhanced">Brand <span class="text-danger">*</span></label>
                                <input type="text" class="form-control-enhanced" id="edit_brand" name="brand" required placeholder="Enter brand name">
                            </div>
                        </div>
                    </div>
                    <div class="form-group-enhanced">
                        <label for="edit_expiration_days" class="form-label-enhanced">Expiration Days <span class="text-danger">*</span></label>
                        <input type="number" class="form-control-enhanced" id="edit_expiration_days" name="expiration_days" min="1" required placeholder="e.g. 365" value="{{ old('expiration_days', isset($product) ? $product->expiration_days : '') }}">
                        <small class="form-hint"><i class="bi bi-info-circle me-1"></i>Number of days before product expires (e.g. 365 for 1 year)</small>
                    </div>
                    
                    <div class="form-group-enhanced">
                        <label for="edit_price" class="form-label-enhanced">Price (₱) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control-enhanced" id="edit_price" name="price" required placeholder="0.00">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="edit_current_stock_sacks" class="form-label-enhanced">Current Stock (Sacks)</label>
                                <input type="number" step="0.1" class="form-control-enhanced" id="edit_current_stock_sacks" name="current_stock_sacks" min="0" placeholder="0.0">
                                <small class="form-hint"><i class="bi bi-info-circle me-1"></i>Decimal values allowed</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="edit_current_stock_pieces" class="form-label-enhanced">Current Stock (Pieces)</label>
                                <input type="number" class="form-control-enhanced" id="edit_current_stock_pieces" name="current_stock_pieces" min="0" placeholder="0">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="edit_critical_level_sacks" class="form-label-enhanced">Critical Level (Sacks) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control-enhanced" id="edit_critical_level_sacks" name="critical_level_sacks" min="1" required placeholder="2">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="edit_critical_level_pieces" class="form-label-enhanced">Critical Level (Pieces) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control-enhanced" id="edit_critical_level_pieces" name="critical_level_pieces" min="1" required placeholder="10">
                            </div>
                        </div>
                    </div>

                    <div class="form-group-enhanced">
                        <div class="form-check-enhanced">
                            <input class="form-check-input-enhanced" type="checkbox" id="edit_is_active" name="is_active" value="1">
                            <label class="form-check-label-enhanced" for="edit_is_active">Product is active</label>
                        </div>
                        <small class="form-hint"><i class="bi bi-info-circle me-1"></i>Inactive products won't be available for sales.</small>
                    </div>
                </div>
                <div class="modal-footer modal-footer-enhanced">
                    <button type="button" class="btn btn-secondary-enhanced" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-green">
                        <i class="bi bi-check-circle me-2"></i>Update Product
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

    /* Form Check Classes */
    .form-check-enhanced {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .form-check-input-enhanced {
        width: 1.2em;
        height: 1.2em;
        margin-right: 0.75rem;
        border: 2px solid #cbd5e0;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .form-check-input-enhanced:checked {
        background-color: #4a7c59;
        border-color: #4a7c59;
    }

    .form-check-label-enhanced {
        font-weight: 600;
        color: #2d3748;
        font-size: 0.9rem;
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

    /* Responsive Design */
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
    }

    /* Focus States for Accessibility */
    .form-control-enhanced:focus-visible {
        outline: 2px solid #4a7c59;
        outline-offset: 2px;
    }

    .btn-primary-green:focus-visible,
    .btn-secondary-enhanced:focus-visible {
        outline: 2px solid #4a7c59;
        outline-offset: 2px;
    }

    .form-check-input-enhanced:focus-visible {
        outline: 2px solid #4a7c59;
        outline-offset: 2px;
    }
</style>