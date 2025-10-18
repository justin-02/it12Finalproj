@extends('layouts.app')

@section('title', 'Helper Dashboard')

@section('content')
<div class="page-header">
    <h1 class="h2"><i class="bi bi-people"></i> Helper Dashboard</h1>
    <div></div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Current Order -->
@if(isset($currentOrder) && $currentOrder)
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-cart-check"></i> Current Order ({{ $currentOrder->order_number }})
                </h5>
                @if($currentOrder->items->count() > 0)
                <button type="button" class="btn btn-light btn-sm" id="submitOrderBtn" data-order-id="{{ $currentOrder->id }}" onclick="submitOrder({{ $currentOrder->id }})" title="Order ID: {{ $currentOrder->id }}">
                    <i class="bi bi-send"></i> Send to Cashier
                </button>
                @else
                <span class="text-light">No items in order</span>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Unit</th>
                                <th>Quantity</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($currentOrder->items->count() > 0)
                            @foreach($currentOrder->items as $item)
                            <tr>
                                <td>{{ $item->product->product_name }} - {{ $item->product->brand }}</td>
                                <td>
                                    <span class="badge bg-info text-dark">{{ ucfirst($item->unit) }}</span>
                                </td>
                                <td>
                                    @php
                                        $isWhole = fmod((float)$item->quantity, 1.0) == 0.0;
                                    @endphp
                                    {{ $isWhole ? number_format($item->quantity, 0) : rtrim(rtrim(number_format($item->quantity, 2, '.', ''), '0'), '.') }}
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('helper.remove-order-item', $item->id) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this item from order?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    <i class="bi bi-cart-x"></i> No items in this order yet
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<!-- No Current Order Message -->
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-info">
            <h5><i class="bi bi-info-circle"></i> No Active Order</h5>
            <p class="mb-0">You don't have any active order. Click "Add to Order" to start preparing products for the cashier.</p>
        </div>
    </div>
</div>
@endif

<!-- Available Products -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Available Products</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="productsTable">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Brand</th>
                                <th class="text-end">Stock (Sacks)</th>
                                <th class="text-end">Stock (Pieces)</th>
                                <th class="text-end">Price per Sack</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr class="{{ $product->is_critical ? 'table-danger' : '' }}">
                                <td>{{ $product->product_name }}</td>
                                <td>{{ $product->brand }}</td>
                                <td class="{{ $product->current_stock_sacks > 0 && $product->current_stock_sacks <= $product->critical_level_sacks ? 'text-danger fw-bold' : '' }} text-end">
                                    @php
                                        $isWholeSacks = fmod((float)$product->current_stock_sacks, 1.0) == 0.0;
                                    @endphp
                                    {{ $isWholeSacks ? number_format($product->current_stock_sacks, 0) : rtrim(rtrim(number_format($product->current_stock_sacks, 2, '.', ''), '0'), '.') }}
                                    <small class="text-muted">({{ number_format($product->current_stock_sacks * 50, 0) }} kg)</small>
                                    @if($product->current_stock_sacks > 0 && $product->current_stock_sacks <= $product->critical_level_sacks)
                                        <i class="bi bi-exclamation-triangle text-danger"></i>
                                    @endif
                                </td>
                                <td class="{{ $product->current_stock_pieces > 0 && $product->current_stock_pieces <= $product->critical_level_pieces ? 'text-danger fw-bold' : '' }} text-end">
                                    {{ $product->current_stock_pieces }}
                                    @if($product->current_stock_pieces > 0 && $product->current_stock_pieces <= $product->critical_level_pieces)
                                        <i class="bi bi-exclamation-triangle text-danger"></i>
                                    @endif
                                </td>
                                <td class="text-end">₱{{ number_format($product->price, 2) }}</td>
                                <td>
                                    @if($product->is_active)
                                        @if($product->is_critical)
                                            <span class="badge bg-warning">Low Stock</span>
                                        @else
                                            <span class="badge bg-success">In Stock</span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">Out of Stock</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" 
                                            data-bs-target="#prepareOrderModal" 
                                            onclick="setProductForOrder({{ $product->id }}, '{{ $product->product_name }} - {{ $product->brand }}', {{ $product->current_stock_sacks }}, {{ $product->current_stock_pieces }}, {{ $product->price }})">
                                        <i class="bi bi-cart-plus"></i> Add to Order
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Prepare Order Modal -->
<div class="modal fade" id="prepareOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Prepare Order Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('helper.prepare-order') }}" id="orderForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="order_product_id" class="form-label">Product</label>
                        <select class="form-control" id="order_product_id" name="product_id" required onchange="updateStockInfo()">
                            <option value="">Select Product</option>
                            @foreach($products->where('is_active', true) as $product)
                            <option value="{{ $product->id }}" 
                                    data-sacks="{{ $product->current_stock_sacks }}"
                                    data-pieces="{{ $product->current_stock_pieces }}"
                                    data-price="{{ $product->price }}">
                                {{ $product->product_name }} - {{ $product->brand }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="unit" class="form-label">Unit</label>
                        <select class="form-control" id="unit" name="unit" required onchange="updateStockInfo()">
                            <option value="">Select Unit</option>
                            <option value="sack">Sack (50 kilos per sack)</option>
                            <option value="kilo">Kilo (1 sack = 50 kilos)</option>
                            <option value="piece">Piece</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" step="0.01" class="form-control" id="quantity" name="quantity" required min="0.1">
                    </div>
                    
                    <div class="alert alert-info" id="stockInfo">
                        Please select a product and unit to see available stock.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add to Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#productsTable').DataTable({
            "order": [[0, "asc"]]
        });
    });

    function setProductForOrder(productId, productName, sacks, pieces, price) {
        document.getElementById('order_product_id').value = productId;
        updateStockInfo();
    }

    function updateStockInfo() {
        const productSelect = document.getElementById('order_product_id');
        const unitSelect = document.getElementById('unit');
        const stockInfo = document.getElementById('stockInfo');
        const quantityInput = document.getElementById('quantity');
        
        if (productSelect.value && unitSelect.value) {
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const sacks = parseFloat(selectedOption.getAttribute('data-sacks'));
            const pieces = parseInt(selectedOption.getAttribute('data-pieces'));
            const price = parseFloat(selectedOption.getAttribute('data-price'));
            const unit = unitSelect.value;
            
            let availableStock = 0;
            let message = '';
            let priceInfo = '';
            
            switch(unit) {
                case 'sack':
                    availableStock = sacks;
                    const totalKilos = sacks * 50;
                    message = `<strong>Available:</strong> ${sacks.toFixed(2)} sacks (${totalKilos.toFixed(0)} kg)`;
                    priceInfo = `<br><strong>Price:</strong> ₱${price.toFixed(2)} per sack`;
                    break;
                case 'kilo':
                    availableStock = sacks * 50;
                    const pricePerKilo = price / 50;
                    message = `<strong>Available:</strong> ${availableStock.toFixed(0)} kilos (${sacks.toFixed(2)} sacks)<br>`;
                    message += `<small class="text-muted">Note: 1 sack = 50 kilos. System will deduct exact sack equivalent (25kg = 0.5 sacks)</small>`;
                    priceInfo = `<br><strong>Price:</strong> ₱${pricePerKilo.toFixed(2)} per kilo`;
                    break;
                case 'piece':
                    availableStock = pieces;
                    message = `<strong>Available:</strong> ${pieces} pieces`;
                    priceInfo = `<br><strong>Price:</strong> ₱${price.toFixed(2)} per piece`;
                    break;
            }
            
            stockInfo.innerHTML = message + priceInfo;
            
            if (availableStock > 0) {
                stockInfo.className = 'alert alert-success';
            } else {
                stockInfo.className = 'alert alert-danger';
                stockInfo.innerHTML += '<br><strong class="text-danger">OUT OF STOCK</strong>';
            }
            
            // Set max quantity
            quantityInput.setAttribute('max', availableStock);
            
        } else {
            stockInfo.innerHTML = 'Please select a product and unit to see available stock.';
            stockInfo.className = 'alert alert-info';
            quantityInput.value = '';
        }
    }

    // Submit order to cashier
    function submitOrder(orderId) {
        console.log('Submitting order:', orderId);
        
        if (!orderId) {
            alert('Error: Order ID is missing!');
            return;
        }

        if (confirm('Are you sure you want to send this order to cashier?')) {
            // Show loading state
            const btn = document.getElementById('submitOrderBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Sending...';
            }

            // Make AJAX request
            const url = '{{ route("helper.submit-order", ":id") }}'.replace(':id', orderId);
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({})
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(`HTTP ${response.status}: ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Order sent to cashier successfully!');
                    window.location.href = '{{ route("helper.dashboard") }}';
                } else {
                    throw new Error(data.message || 'Unknown error occurred');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error submitting order: ' + error.message);
                
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-send"></i> Send to Cashier';
                }
            });
        }
    }
</script>

<style>
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>
@endpush