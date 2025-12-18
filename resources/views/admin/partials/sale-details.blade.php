<div class="modal-header" style="background-color: #2d5a3d; border-bottom: 3px solid #1e3a29;" class="text-white">
    <h5 class="modal-title text-white fw-bold"><i class="bi bi-receipt me-2"></i>Sale Details - Order #{{ $order->order_number }}</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body small p-0">
    <!-- Header Section -->
    <div class="bg-light p-3 border-bottom">
        <div class="row">
            <div class="col-6">
                <p class="mb-1"><strong><i class="bi bi-person-circle me-2"></i>Cashier:</strong></p>
                <p class="text-muted">{{ $order->cashier->name }}</p>
            </div>
            <div class="col-6">
                <p class="mb-1"><strong><i class="bi bi-people me-2"></i>Helper:</strong></p>
                <p class="text-muted">{{ $order->helper->name ?? 'N/A' }}</p>
            </div>
        </div>
        <div class="mt-2">
            <p class="mb-1"><strong><i class="bi bi-calendar me-2"></i>Date & Time:</strong></p>
            <p class="text-muted">{{ $order->created_at->format('M d, Y h:i A') }}</p>
        </div>
    </div>

    <!-- Products Table -->
    <div class="p-3">
        <h6 class="border-bottom pb-2 mb-3"><i class="bi bi-cart me-2"></i>Order Items</h6>
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-3">Product</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Price</th>
                        <th class="text-end pe-3">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td class="ps-3">{{ $item->product->product_name }}</td>
                            <td class="text-end">{{ $item->quantity }}</td>
                            <td class="text-end">₱{{ number_format($item->price, 2) }}</td>
                            <td class="text-end fw-bold pe-3">₱{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="bg-light p-3 border-top">
        <div class="row">
            <div class="col-md-4">
                <div class="summary-card bg-white p-3 rounded border text-center">
                    <p class="text-muted mb-1 small">Total Amount</p>
                    <h5 class="text-dark mb-0">₱{{ number_format($order->total_amount, 2) }}</h5>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card bg-white p-3 rounded border text-center">
                    <p class="text-muted mb-1 small">Cash Received</p>
                    <h5 class="text-success mb-0">₱{{ number_format($order->cash_received, 2) }}</h5>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card bg-white p-3 rounded border text-center">
                    <p class="text-muted mb-1 small">Change</p>
                    <h5 class="text-primary mb-0">₱{{ number_format($order->change, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer bg-light">
    <button type="button" class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">
        <i class="bi bi-x-circle me-1"></i>Close
    </button>
    <button type="button" class="btn btn-primary btn-sm no-hover">
    <i class="bi bi-printer me-1"></i>Print Receipt
</button>
</div>

<style>
    /* Add these styles to your CSS file */
.summary-card {
    transition: transform 0.2s;
}

.summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.table-dark th {
    background-color: #2c3e50;
    border-color: #2c3e50;
}

.bg-light {
    background-color: #f8f9fa !important;
}

.text-muted {
    color: #6c757d !important;
}

.text-success {
    color: #27ae60 !important;
}

.text-primary {
    color: #3498db !important;
}

.btn-outline-dark:hover {
    background-color: #2c3e50;
    color: white;
}

</style>