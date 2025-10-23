@extends('layouts.app')

@section('title', 'Inventory Dashboard')

@section('content')
<div class="page-header">
    <h1 class="h2"><i class="bi bi-boxes"></i> Inventory Dashboard</h1>
    <div>
        
    </div>
</div>

<!-- Stock Overview - Made more compact -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Products</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 text-end">{{ $totalProducts }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-box-seam fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Critical Stocks</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 text-end">{{ $criticalProducts }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            In Stock Items</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 text-end">{{ $inStockProducts }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Low Stock Items</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 text-end">{{ $lowStockProducts }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-exclamation-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Critical Stock Alerts - Made more compact -->
@if($criticalProducts > 0)
<div class="row">
    <div class="col-12">
        <div class="card border-warning shadow-sm mb-3">
            <div class="card-header bg-warning text-dark py-2">
                <h5 class="card-title mb-0 small fw-bold">
                    <i class="bi bi-exclamation-triangle"></i> Critical Stock Alerts
                </h5>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-sm table-borderless mb-0">
                        <thead>
                            <tr>
                                <th class="small">Product</th>
                                <th class="small">Brand</th>
                                <th class="small">Stock (Sacks)</th>
                                <th class="small">Stock (Pieces)</th>
                                <th class="small">Critical Level</th>
                                <th class="small">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                @if($product->is_critical)
                                <tr class="critical-stock">
                                    <td class="small">{{ $product->product_name }}</td>
                                    <td class="small">{{ $product->brand }}</td>
                                    <td class="small {{ $product->current_stock_sacks > 0 && $product->current_stock_sacks <= $product->critical_level_sacks ? 'text-danger fw-bold' : '' }}">
                                        @php
                                            $isWholeSacks = fmod((float)$product->current_stock_sacks, 1.0) == 0.0;
                                        @endphp
                                        {{ $isWholeSacks ? number_format($product->current_stock_sacks, 0) : rtrim(rtrim(number_format($product->current_stock_sacks, 2, '.', ''), '0'), '.') }}
                                        <small class="text-muted">({{ number_format($product->current_stock_sacks * 50, 0) }} kg)</small>
                                        @if($product->current_stock_sacks > 0 && $product->current_stock_sacks <= $product->critical_level_sacks)
                                            <i class="bi bi-exclamation-triangle text-danger"></i>
                                        @endif
                                    </td>
                                    <td class="small {{ $product->current_stock_pieces > 0 && $product->current_stock_pieces <= $product->critical_level_pieces ? 'text-danger fw-bold' : '' }}">
                                        {{ $product->current_stock_pieces }}
                                        @if($product->current_stock_pieces > 0 && $product->current_stock_pieces <= $product->critical_level_pieces)
                                            <i class="bi bi-exclamation-triangle text-danger"></i>
                                        @endif
                                    </td>
                                    <td class="small">
                                        <small>Sacks: {{ $product->critical_level_sacks }}</small><br>
                                        <small>Pieces: {{ $product->critical_level_pieces }}</small>
                                    </td>
                                    <td class="small">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-warning btn-sm" onclick="reportCriticalLevel({{ $product->id }})" title="Report to Admin">
                                                <i class="bi bi-megaphone"></i>
                                            </button>
                                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#stockInModal" 
                                                    onclick="setProductForStockIn({{ $product->id }})" title="Add Stock">
                                                <i class="bi bi-arrow-down-circle"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Recent Stock Movements - Made more compact -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header py-2">
                <h5 class="card-title mb-0 small fw-bold">Recent Stock Movements</h5>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th class="small">Product</th>
                                <th class="small">Type</th>
                                <th class="text-end">Sacks</th>
                                <th class="text-end">Pieces</th>
                                <th class="text-end">Kilos</th>
                                <th class="small">Date</th>
                                <th class="small">User</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTransactions as $transaction)
                            <tr>
                                <td class="text-start">{{ $transaction->product->product_name }}</td>
                                <td class="text-start">
                                    <span class="badge bg-{{ $transaction->type == 'stock-in' ? 'success' : ($transaction->type == 'stock-out' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($transaction->type) }}
                                    </span>
                                </td>
                                <td class="text-end">{{ $transaction->quantity_sacks }}</td>
                                <td class="text-end">{{ $transaction->quantity_pieces }}</td>
                                <td class="text-end">{{ $transaction->quantity_kilos }}</td>
                                <td class="text-start">
                                    <small>{{ $transaction->created_at->format('M d, Y') }}</small><br>
                                    <small class="text-muted">{{ $transaction->created_at->format('H:i') }}</small>
                                </td>
                                <td class="small">{{ $transaction->user->name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($recentTransactions->isEmpty())
                <div class="text-center text-muted py-3">
                    <i class="bi bi-inbox"></i><br>
                    <small>No recent stock movements</small>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title h6">Add New Product</h5>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('inventory.products.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label for="product_name" class="form-label small fw-bold">Product Name</label>
                                <input type="text" class="form-control form-control-sm" id="product_name" name="product_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label for="brand" class="form-label small fw-bold">Brand</label>
                                <input type="text" class="form-control form-control-sm" id="brand" name="brand" required>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label for="price" class="form-label small fw-bold">Price</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" id="price" name="price" required>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label for="current_stock_sacks" class="form-label small fw-bold">Initial Stock (Sacks)</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" id="current_stock_sacks" name="current_stock_sacks" value="0" min="0">
                                <small class="text-muted">Supports decimal values (e.g., 2.5 sacks)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label for="current_stock_pieces" class="form-label small fw-bold">Initial Stock (Pieces)</label>
                                <input type="number" class="form-control form-control-sm" id="current_stock_pieces" name="current_stock_pieces" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label for="critical_level_sacks" class="form-label small fw-bold">Critical Level (Sacks)</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" id="critical_level_sacks" name="critical_level_sacks" value="2" min="0.01" required>
                                <small class="text-muted">Supports decimal values (e.g., 1.5 sacks)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label for="critical_level_pieces" class="form-label small fw-bold">Critical Level (Pieces)</label>
                                <input type="number" class="form-control form-control-sm" id="critical_level_pieces" name="critical_level_pieces" value="10" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Stock In Modal -->
<div class="modal fade" id="stockInModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title h6">Stock In - Add Inventory</h5>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('inventory.stock-in') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-2">
                        <label for="stock_product_id" class="form-label small fw-bold">Product</label>
                        <select class="form-control form-control-sm" id="stock_product_id" name="product_id" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->product_name }} - {{ $product->brand }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label for="quantity_sacks" class="form-label small fw-bold">Quantity (Sacks)</label>
                                <input type="number" class="form-control form-control-sm" id="quantity_sacks" name="quantity_sacks" value="0" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label for="quantity_pieces" class="form-label small fw-bold">Quantity (Pieces)</label>
                                <input type="number" class="form-control form-control-sm" id="quantity_pieces" name="quantity_pieces" value="0" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label for="notes" class="form-label small fw-bold">Delivered by</label>
                        <textarea class="form-control form-control-sm" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Add Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Stock Out Modal -->
<!-- Stock Out Modal -->
<div class="modal fade" id="stockOutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title h6">Stock Out - Deduct Inventory</h5>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('inventory.stock-out') }}" id="stockOutForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-2">
                        <label for="stock_out_product_id" class="form-label small fw-bold">Product</label>
                        <select class="form-control form-control-sm" id="stock_out_product_id" name="product_id" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                            <option 
                                value="{{ $product->id }}"
                                data-stock-sacks="{{ $product->current_stock_sacks }}"
                                data-stock-pieces="{{ $product->current_stock_pieces }}"
                            >
                                {{ $product->product_name }} - {{ $product->brand }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="mb-2 position-relative">
                                <label for="quantity_out_sacks" class="form-label small fw-bold">Quantity (Sacks)</label>
                                <input type="number" class="form-control form-control-sm" id="quantity_out_sacks" name="quantity_sacks" value="0" min="0">
                                <div id="sackWarning" class="text-danger small mt-1 d-none">
                                    <i class="bi bi-exclamation-triangle"></i> Exceeds available stock!
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2 position-relative">
                                <label for="quantity_out_pieces" class="form-label small fw-bold">Quantity (Pieces)</label>
                                <input type="number" class="form-control form-control-sm" id="quantity_out_pieces" name="quantity_pieces" value="0" min="0">
                                <div id="pieceWarning" class="text-danger small mt-1 d-none">
                                    <i class="bi bi-exclamation-triangle"></i> Exceeds available stock!
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="notes_out" class="form-label small fw-bold">Notes (Optional)</label>
                        <textarea class="form-control form-control-sm" id="notes_out" name="notes" rows="2"></textarea>
                    </div>
                    <div class="alert alert-warning p-2 mt-2">
                        <small><i class="bi bi-exclamation-triangle"></i> <strong>Reminder:</strong> Stock out cannot exceed current inventory.</small>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm" id="submitStockOut">Deduct Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>


@push('scripts')
<script>
    // Stock Out live validation
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('stock_out_product_id');
    const sackInput = document.getElementById('quantity_out_sacks');
    const pieceInput = document.getElementById('quantity_out_pieces');
    const sackWarning = document.getElementById('sackWarning');
    const pieceWarning = document.getElementById('pieceWarning');
    const submitBtn = document.getElementById('submitStockOut');

    let currentSackStock = 0;
    let currentPieceStock = 0;

    // Update stock levels when a product is selected
    productSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        currentSackStock = parseFloat(selected.getAttribute('data-stock-sacks')) || 0;
        currentPieceStock = parseInt(selected.getAttribute('data-stock-pieces')) || 0;
        sackWarning.classList.add('d-none');
        pieceWarning.classList.add('d-none');
        sackInput.value = 0;
        pieceInput.value = 0;
    });

    // Validate sack quantity
    sackInput.addEventListener('input', function() {
        if (parseFloat(this.value) > currentSackStock) {
            sackWarning.classList.remove('d-none');
            submitBtn.disabled = true;
        } else {
            sackWarning.classList.add('d-none');
            if (pieceWarning.classList.contains('d-none')) submitBtn.disabled = false;
        }
    });

    // Validate piece quantity
    pieceInput.addEventListener('input', function() {
        if (parseInt(this.value) > currentPieceStock) {
            pieceWarning.classList.remove('d-none');
            submitBtn.disabled = true;
        } else {
            pieceWarning.classList.add('d-none');
            if (sackWarning.classList.contains('d-none')) submitBtn.disabled = false;
        }
    });
});

    function reportCriticalLevel(productId) {
        if (confirm('Report this product as critical stock to admin?')) {
            fetch(`/inventory/report-critical/${productId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                alert('Critical level reported to admin!');
                location.reload();
            })
            .catch(error => {
                alert('Error reporting critical level');
            });
        }
    }

    function setProductForStockIn(productId) {
        document.getElementById('stock_product_id').value = productId;
    }

    function setProductForStockOut(productId) {
        document.getElementById('stock_out_product_id').value = productId;
    }

    // Auto-focus on first input when modals open
    document.addEventListener('DOMContentLoaded', function() {
        $('#addProductModal').on('shown.bs.modal', function () {
            $('#product_name').focus();
        });
        $('#stockInModal').on('shown.bs.modal', function () {
            $('#stock_product_id').focus();
        });
        $('#stockOutModal').on('shown.bs.modal', function () {
            $('#stock_out_product_id').focus();
        });
    });
</script>

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        
    }
    .page-header h1 {
        margin: 0;
        font-size: 1.5rem;
        
    }
    .card-header {
        background-color: #f8f9faff;
        border-bottom: 1px solid #dee2e6;
    }
    .table-sm td, .table-sm th {
        padding: 0.3rem 0.5rem;
        
    }
    .btn-group-sm > .btn, .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        
    }
    .modal-header {
        background-color: #f8f9fa;
    }
    .critical-stock {
        background-color: #fff3cd !important;
    }
</style>
@endpush
@endsection