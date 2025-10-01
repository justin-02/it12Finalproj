<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-card">
            <div class="modal-header modal-header-green">
                <h5 class="modal-title text-white fw-semibold">
                    <i class="bi bi-plus-circle me-2"></i>Add New Product
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('inventory.products.store') }}">
                @csrf
                <div class="modal-body modal-body-spaced">
                    <div class="form-group-enhanced">
                        <label for="product_name" class="form-label-enhanced">Product Name <span class="text-danger"></span></label>
                        <input type="text" class="form-control-enhanced" id="product_name" name="product_name" required placeholder="Enter product name">
                    </div>
                    
                    <div class="form-group-enhanced">
                        <label for="brand" class="form-label-enhanced">Brand <span class="text-danger"></span></label>
                        <input type="text" class="form-control-enhanced" id="brand" name="brand" required placeholder="Enter brand name">
                    </div>
                    
                    <div class="form-group-enhanced">
                        <label for="price" class="form-label-enhanced">Price (₱) <span class="text-danger"></span></label>
                        <input type="number" step="0.01" class="form-control-enhanced" id="price" name="price" required placeholder="0.00">
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="current_stock_kilos" class="form-label-enhanced">Initial Stock (Kilos)</label>
                                <input type="number" step="0.1" class="form-control-enhanced" id="current_stock_kilos" name="current_stock_kilos" value="0" min="0" placeholder="0.0">
                                <small class="form-hint"><i class="bi bi-info-circle me-1"></i>Decimal values allowed</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="current_stock_pieces" class="form-label-enhanced">Initial Stock (Pieces)</label>
                                <input type="number" class="form-control-enhanced" id="current_stock_pieces" name="current_stock_pieces" value="0" min="0" placeholder="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="critical_level_sacks" class="form-label-enhanced">Critical Level (Sacks) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control-enhanced" id="critical_level_sacks" name="critical_level_sacks" value="2" min="1" required placeholder="2">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="critical_level_pieces" class="form-label-enhanced">Critical Level (Pieces) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control-enhanced" id="critical_level_pieces" name="critical_level_pieces" value="10" min="1" required placeholder="10">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer modal-footer-enhanced">
                    <button type="button" class="btn btn-secondary-enhanced" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-green">
                        <i class="bi bi-plus-circle me-2"></i>Add Product
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
</style>