@extends('layouts.app')

@section('title', 'Receipt - Order #' . $order->order_number)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">
                        <i class="bi bi-receipt"></i> RECEIPT
                    </h4>
                    <p class="mb-0">AgriSupply Store</p>
                </div>
                
                <div class="card-body">
                    <!-- Order Information -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Order #:</strong><br>
                            {{ $order->order_number }}
                        </div>
                        <div class="col-6 text-end">
                            <strong>Date:</strong><br>
                            {{ $order->updated_at->format('M d, Y H:i') }}
                        </div>
                    </div>

                    <!-- Staff Information -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Prepared by:</strong><br>
                            {{ $order->helper->name ?? 'N/A' }}
                        </div>
                        <div class="col-6 text-end">
                            <strong>Cashier:</strong><br>
                            {{ $order->cashier->name ?? 'N/A' }}
                        </div>
                    </div>

                    <hr>

                    <!-- Items -->
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Unit</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        {{ $item->product->product_name }}
                                        @if($item->unit === 'kilo')
                                            <br><small class="text-muted">({{ number_format($item->quantity / 50, 2) }} sacks deducted)</small>
                                        @endif
                                    </td>
                                    <td>{{ ucfirst($item->unit) }}</td>
                                    <td>
                                        @php
                                            $isWhole = fmod((float)$item->quantity, 1.0) == 0.0;
                                        @endphp
                                        {{ $isWhole ? number_format($item->quantity, 0) : rtrim(rtrim(number_format($item->quantity, 2, '.', ''), '0'), '.') }}
                                    </td>
                                    <td>₱{{ number_format($item->price, 2) }}</td>
                                    <td class="text-end">₱{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <!-- Totals -->
                    <div class="row">
                        <div class="col-8">
                            <strong>Total Amount:</strong>
                        </div>
                        <div class="col-4 text-end">
                            <strong>₱{{ number_format($order->total_amount, 2) }}</strong>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-8">
                            <strong>Cash Received:</strong>
                        </div>
                        <div class="col-4 text-end">
                            <strong>₱{{ number_format($order->cash_received, 2) }}</strong>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-8">
                            <strong>Change:</strong>
                        </div>
                        <div class="col-4 text-end">
                            <strong>₱{{ number_format($order->change, 2) }}</strong>
                        </div>
                    </div>

                    <hr>

                    <!-- Thank you message -->
                    <div class="text-center mt-4">
                        <p class="mb-0">Thank you for your purchase!</p>
                        <small class="text-muted">Please keep this receipt for your records.</small>
                    </div>
                </div>

                <div class="card-footer text-center">
                    <button onclick="window.print()" class="btn btn-primary me-2">
                        <i class="bi bi-printer"></i> Print Receipt
                    </button>
                    <a href="{{ route('cashier.dashboard') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    .card-footer {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    body {
        background: white !important;
    }
    
    .container-fluid {
        padding: 0 !important;
    }
}
</style>

@endsection
