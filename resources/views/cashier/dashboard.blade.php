@extends('layouts.app')

@section('title', 'Cashier Dashboard')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-cash-coin"></i> Cashier Dashboard</h1>
    <div></div>
</div>

<!-- Sales Overview -->
<div class="row">
    <!-- Today's Sales -->
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card dashboard-card" style="background: linear-gradient(135deg, #E8FFD7 0%, #D6F5C3 100%); border: 1px solid rgba(232, 255, 215, 0.3);">
            <div class="card-body">
                <div class="card-content">
                    <div class="text-content">
                        <div class="card-title">TODAY'S SALES</div>
                        <h2 class="card-value">
                            <span class="currency">₱</span>{{ number_format($todaySales, 2) }}
                        </h2>
                        <div class="trend-indicator">
                            <i class="bi bi-calendar-day me-1"></i>
                            <span>Daily Revenue</span>
                        </div>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-currency-dollar" style="color: #2E7D32;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Sales -->
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card dashboard-card" style="background: linear-gradient(135deg, #E8FFD7 0%, #D6F5C3 100%); border: 1px solid rgba(232, 255, 215, 0.3);">
            <div class="card-body">
                <div class="card-content">
                    <div class="text-content">
                        <div class="card-title">WEEKLY SALES</div>
                        <h2 class="card-value">
                            <span class="currency">₱</span>{{ number_format($weeklySales, 2) }}
                        </h2>
                        <div class="trend-indicator">
                            <i class="bi bi-calendar-week me-1"></i>
                            <span>Weekly Total</span>
                        </div>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-graph-up" style="color: #2E7D32;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Critical Stocks -->
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card dashboard-card" style="background: linear-gradient(135deg, #E8FFD7 0%, #D6F5C3 100%); border: 1px solid rgba(232, 255, 215, 0.3);">
            <div class="card-body">
                <div class="card-content">
                    <div class="text-content">
                        <div class="card-title" style="color: #DC3545 !important;">CRITICAL STOCKS</div>
                        <h2 class="card-value" style="color: #DC3545 !important;">{{ $pendingOrdersCount }}</h2>
                        <div class="trend-indicator" style="color: #DC3545 !important;">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            <span>Needs Attention</span>
                        </div>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-exclamation-triangle" style="color: #DC3545;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Products -->
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card dashboard-card" style="background: linear-gradient(135deg, #E8FFD7 0%, #D6F5C3 100%); border: 1px solid rgba(232, 255, 215, 0.3);">
            <div class="card-body">
                <div class="card-content">
                    <div class="text-content">
                        <div class="card-title">TOTAL PRODUCTS</div>
                        <h2 class="card-value">{{ $todayTransactions }}</h2>
                        <div class="trend-indicator">
                            <i class="bi bi-grid-3x3-gap me-1"></i>
                            <span>Inventory Count</span>
                        </div>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-box-seam" style="color: #2E7D32;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard-card {
    border: none;
    border-radius: 14px;
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
    background: linear-gradient(135deg, #E8FFD7 0%, #D6F5C3 100%);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    height: 140px;
}

.dashboard-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
}

.dashboard-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #8BC34A, #4CAF50);
}

.dashboard-card .card-body {
    position: relative;
    z-index: 1;
    padding: 1.25rem !important;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
}

.dashboard-card .card-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex: 1;
}

.dashboard-card .icon-wrapper {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.4);
    flex-shrink: 0;
    margin-left: 10px;
}

.dashboard-card:hover .icon-wrapper {
    background: rgba(255, 255, 255, 0.4);
    transform: scale(1.08);
    border-color: rgba(255, 255, 255, 0.6);
}

.dashboard-card .icon-wrapper i {
    font-size: 1.5rem;
    color: #2E7D32;
}

.dashboard-card .text-content {
    flex: 1;
    min-width: 0;
}

.dashboard-card .card-title {
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 0.6px;
    color: #1B5E20;
    margin-bottom: 0.5rem;
    opacity: 0.95;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.dashboard-card .card-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: #1B5E20;
    margin: 0 0 0.5rem 0;
    line-height: 1.2;
    letter-spacing: -0.3px;
}

.dashboard-card .currency {
    color: #1B5E20;
    font-weight: 700;
    font-size: 1.4rem;
    opacity: 0.9;
}

.dashboard-card .trend-indicator {
    font-size: 0.7rem;
    padding: 4px 10px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.4);
    color: #1B5E20;
    font-weight: 600;
    border: 1px solid rgba(255, 255, 255, 0.3);
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
}

.dashboard-card .trend-indicator i {
    font-size: 0.8rem;
}
</style>

<!-- Pending Orders from Helper -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="bi bi-cart-check"></i> Pending Orders from Helper
                </h5>
            </div>
            <div class="card-body">
                @if($pendingOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Helper</th>
                                    <th>Items</th>
                                    <th>Total Amount</th>
                                    <th>Prepared At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingOrders as $order)
                                <tr>
                                    <td>
                                        <strong>{{ $order->order_number }}</strong>
                                    </td>
                                    <td>{{ $order->helper->name }}</td>
                                    <td>
                                        <!-- Using details/summary instead of collapse for reliability -->
                                        <details>
                                            <summary class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-eye"></i> View {{ $order->items->count() }} Items
                                            </summary>
                                            <div class="card card-body mt-2">
                                                <h6>Order Items:</h6>
                                                @foreach($order->items as $item)
                                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                                    <div>
                                                        <strong>{{ $item->product->product_name ?? 'Product Not Found' }}</strong><br>
                                                        <small class="text-muted">{{ $item->product->brand ?? 'N/A' }}</small>
                                                    </div>
                                                    <div class="text-end">
                                                        @php
                                                            $isWhole = fmod((float)$item->quantity, 1.0) == 0.0;
                                                            $displayQty = $isWhole ? number_format($item->quantity, 0) : rtrim(rtrim(number_format($item->quantity, 2, '.', ''), '0'), '.');
                                                        @endphp
                                                        <span class="badge bg-primary">{{ $displayQty }} {{ $item->unit }}</span><br>
                                                        @if($item->price)
                                                            ₱{{ number_format($item->price, 2) }} each<br>
                                                            <strong>Subtotal: ₱{{ number_format($item->subtotal, 2) }}</strong>
                                                        @else
                                                            <span class="text-danger">Price not set</span>
                                                        @endif
                                                        @if($item->unit == 'kilo' && $item->quantity > 0)
                                                            <br><small class="text-muted">({{ number_format($item->quantity / 50, 2) }} sacks)</small>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </details>
                                    </td>
                                    <td class="text-end">
                                        @if($order->items->where('price')->count() > 0)
                                            <strong>₱{{ number_format($order->items->sum('subtotal'), 2) }}</strong>
                                        @else
                                            <span class="text-danger">Price calculation needed</span>
                                        @endif
                                    </td>
                                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('cashier.process-order', $order->id) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-cash-coin"></i> Process Payment
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No pending orders from helper.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>


<!-- Recent Transactions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Recent Transactions</h5>
            </div>
            <div class="card-body">
                @if($recentTransactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Helper</th>
                                    <th>Cashier</th>
                                    <th class="text-end">Total Amount</th>
                                    <th class="text-end">Cash Received</th>
                                    <th class="text-end">Change</th>
                                    <th class="text-end">Date</th>
                                    <th class="text-center">Receipt</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->order_number }}</td>
                                    <td>{{ $transaction->helper->name }}</td>
                                    <td>{{ $transaction->cashier->name }}</td>
                                    <td class="text-end">₱{{ number_format($transaction->total_amount, 2) }}</td>
                                    <td class="text-end">₱{{ number_format($transaction->cash_received, 2) }}</td>
                                    <td class="text-end">₱{{ number_format($transaction->change, 2) }}</td>
                                    <td class="text-end">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('cashier.receipt', $transaction->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="bi bi-printer"></i> Print
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No recent transactions.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let directSaleCart = [];
    
    // Function to add product to direct sale cart
    function addToDirectSale(productId, productName, price, unit, maxStock) {
        // Check if already in cart
        const existingIndex = directSaleCart.findIndex(item => item.productId === productId);
        
        if (existingIndex > -1) {
            // If exists, increase quantity by 1
            if (directSaleCart[existingIndex].quantity < maxStock) {
                directSaleCart[existingIndex].quantity += 1;
            } else {
                alert('Cannot add more than available stock!');
                return;
            }
        } else {
            // Add new item with quantity 1
            directSaleCart.push({
                productId: productId,
                name: productName,
                price: price,
                unit: unit,
                quantity: 1,
                maxStock: maxStock
            });
        }
        
        updateDirectSaleCart();
    }
    
    // Function to update cart display
    function updateDirectSaleCart() {
        const cartContainer = document.getElementById('cartItemsContainer');
        const cartTotal = document.getElementById('cartTotal');
        const cartElement = document.getElementById('directSaleCart');
        
        if (directSaleCart.length === 0) {
            cartElement.style.display = 'none';
            return;
        }
        
        // Show cart
        cartElement.style.display = 'block';
        
        // Generate cart HTML
        let html = '<table class="table table-sm">';
        html += '<thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th><th>Action</th></tr></thead><tbody>';
        
        let total = 0;
        
        directSaleCart.forEach((item, index) => {
            const subtotal = item.price * item.quantity;
            total += subtotal;
            
            html += `
                <tr>
                    <td>${item.name} (${item.unit})</td>
                    <td>₱${item.price.toFixed(2)}</td>
                    <td>
                        <div class="input-group input-group-sm" style="width: 120px;">
                            <button class="btn btn-outline-secondary" onclick="updateCartQuantity(${index}, -1)">-</button>
                            <input type="number" class="form-control text-center" value="${item.quantity}" 
                                   min="1" max="${item.maxStock}" 
                                   onchange="setCartQuantity(${index}, this.value)">
                            <button class="btn btn-outline-secondary" onclick="updateCartQuantity(${index}, 1)">+</button>
                        </div>
                    </td>
                    <td>₱${subtotal.toFixed(2)}</td>
                    <td>
                        <button class="btn btn-sm btn-danger" onclick="removeFromCart(${index})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        html += '</tbody></table>';
        
        cartContainer.innerHTML = html;
        cartTotal.value = `₱${total.toFixed(2)}`;
        
        // Update change calculation
        calculateChange();
    }
    
    // Update quantity with +1/-1 buttons
    function updateCartQuantity(index, change) {
        const item = directSaleCart[index];
        const newQuantity = item.quantity + change;
        
        if (newQuantity >= 1 && newQuantity <= item.maxStock) {
            item.quantity = newQuantity;
            updateDirectSaleCart();
        }
    }
    
    // Set specific quantity
    function setCartQuantity(index, value) {
        const item = directSaleCart[index];
        const newQuantity = parseInt(value);
        
        if (!isNaN(newQuantity) && newQuantity >= 1 && newQuantity <= item.maxStock) {
            item.quantity = newQuantity;
            updateDirectSaleCart();
        } else {
            alert(`Quantity must be between 1 and ${item.maxStock}`);
            updateDirectSaleCart(); // Reset display
        }
    }
    
    // Remove item from cart
    function removeFromCart(index) {
        if (confirm('Remove this item from cart?')) {
            directSaleCart.splice(index, 1);
            updateDirectSaleCart();
        }
    }
    
    // Calculate change
    function calculateChange() {
        const cashInput = document.getElementById('cashReceived');
        const changeDisplay = document.getElementById('changeAmount');
        const total = parseFloat(document.getElementById('cartTotal').value.replace('₱', ''));
        
        if (cashInput.value) {
            const cash = parseFloat(cashInput.value);
            const change = cash - total;
            
            if (change >= 0) {
                changeDisplay.value = `₱${change.toFixed(2)}`;
            } else {
                changeDisplay.value = 'Insufficient';
            }
        } else {
            changeDisplay.value = '₱0.00';
        }
    }
    
    // Process direct sale
    function processDirectSale() {
        if (directSaleCart.length === 0) {
            alert('Cart is empty!');
            return;
        }
        
        const cashInput = document.getElementById('cashReceived').value;
        if (!cashInput || parseFloat(cashInput) <= 0) {
            alert('Please enter cash received amount');
            return;
        }
        
        const cash = parseFloat(cashInput);
        const total = parseFloat(document.getElementById('cartTotal').value.replace('₱', ''));
        
        if (cash < total) {
            alert(`Insufficient cash! Total is ₱${total.toFixed(2)}, received ₱${cash.toFixed(2)}`);
            return;
        }
        
        // Prepare data for AJAX request
        const saleData = {
            items: directSaleCart,
            total_amount: total,
            cash_received: cash,
            change: cash - total,
            _token: '{{ csrf_token() }}'
        };
        
        // Show confirmation
        if (confirm(`Process sale for ₱${total.toFixed(2)}?`)) {
            // AJAX request to process direct sale
            fetch('/cashier/process-direct-sale', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(saleData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Sale completed successfully!');
                    directSaleCart = [];
                    updateDirectSaleCart();
                    location.reload(); // Refresh to update dashboard
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }
    }
    
    // Event listener for cash input
    document.addEventListener('DOMContentLoaded', function() {
        const cashInput = document.getElementById('cashReceived');
        if (cashInput) {
            cashInput.addEventListener('input', calculateChange);
        }
    });
</script>
@endsection