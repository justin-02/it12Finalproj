<div class="modal-header">
    <h5 class="modal-title"><i class="bi bi-receipt"></i> Sale Details - Order #{{ $order->order_number }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body small">
    <p><strong>Cashier:</strong> {{ $order->cashier->name }}</p>
    <p><strong>Helper:</strong> {{ $order->helper->name ?? 'N/A' }}</p>
    <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y h:i A') }}</p>
    <hr>

    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end">Price</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product->product_name}}</td>
                        <td class="text-end">{{ $item->quantity }}</td>
                        <td class="text-end">₱{{ number_format($item->price, 2) }}</td>
                        <td class="text-end">₱{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <hr>
    <p><strong>Total Amount:</strong> ₱{{ number_format($order->total_amount, 2) }}</p>
    <p><strong>Cash Received:</strong> ₱{{ number_format($order->cash_received, 2) }}</p>
    <p><strong>Change:</strong> ₱{{ number_format($order->change, 2) }}</p>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
</div>
