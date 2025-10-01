@extends('layouts.app')

@section('title', 'Process Order')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1><i class="bi bi-cash-coin"></i> Process Order #{{ $order->order_number }}</h1>
                <a href="{{ route('cashier.dashboard') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="row gx-3">
        <!-- Order Details Card - Made more compact -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-2">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-cart-check"></i> Order Details
                    </h5>
                </div>
                <div class="card-body p-3">
                    <!-- Order Items - Made more compact -->
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Unit</th>
                                    <th>Qty</th>
                                    <th>Price/Unit</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->product->product_name }}</strong>
                                        <br><small class="text-muted">{{ $item->product->brand }}</small>
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @if($item->unit == 'sack') bg-warning text-dark
                                            @elseif($item->unit == 'kilo') bg-info
                                            @else bg-secondary @endif">
                                            {{ ucfirst($item->unit) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $isWhole = fmod((float)$item->quantity, 1.0) == 0.0;
                                        @endphp
                                        {{ $isWhole ? number_format($item->quantity, 0) : rtrim(rtrim(number_format($item->quantity, 2, '.', ''), '0'), '.') }}
                                        @if($item->unit == 'kilo')
                                            <br><small class="text-muted">({{ number_format($item->quantity / 50, 2) }} sacks)</small>
                                        @endif
                                    </td>
                                    <td>₱{{ number_format($item->price, 2) }}</td>
                                    <td><strong>₱{{ number_format($item->subtotal, 2) }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-success">
                                    <td colspan="4" class="text-end"><strong>Total Amount:</strong></td>
                                    <td><strong>₱{{ number_format($totalAmount, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Payment Form - Made more compact -->
                    <form method="POST" action="{{ route('cashier.complete-order', $order->id) }}" id="paymentForm">
                        @csrf
                        
                        <div class="row gx-2 mt-3">
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label for="total_amount" class="form-label small fw-bold">Total Amount</label>
                                    <input type="text" class="form-control form-control-sm bg-light" id="total_amount" value="₱{{ number_format($totalAmount, 2) }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label for="cash_received" class="form-label small fw-bold">Cash Received *</label>
                                    <input type="number" step="0.01" class="form-control form-control-sm" id="cash_received" name="cash_received" required min="0" oninput="calculateChange()" autofocus>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label for="change" class="form-label small fw-bold">Change</label>
                                    <input type="text" class="form-control form-control-sm bg-light" id="change" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info p-2 mt-2 mb-3">
                            <small>
                                <strong>Helper:</strong> {{ $order->helper->name }} | 
                                <strong>Prepared:</strong> {{ $order->created_at->format('M d, Y H:i') }} | 
                                <strong>Items:</strong> {{ $order->items->count() }}
                            </small>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-sm w-100" id="completeBtn" disabled>
                            <i class="bi bi-check-circle"></i> Complete Transaction & Update Inventory
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Cards -->
        <div class="col-xl-4 col-lg-5">
            <!-- Inventory Impact Preview -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-info text-white py-2">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-box-arrow-down"></i> Inventory Impact
                    </h5>
                </div>
                <div class="card-body p-3">
                    <h6 class="small fw-bold">After this transaction, inventory will deduct:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless">
                            <thead>
                                <tr>
                                    <th class="small">Product</th>
                                    <th class="small">Unit</th>
                                    <th class="small">Qty</th>
                                    <th class="small">Effect</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td><small>{{ $item->product->product_name }}</small></td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $item->unit }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $isWhole = fmod((float)$item->quantity, 1.0) == 0.0;
                                        @endphp
                                        <small>{{ $isWhole ? number_format($item->quantity, 0) : rtrim(rtrim(number_format($item->quantity, 2, '.', ''), '0'), '.') }}</small>
                                    </td>
                                    <td>
                                        <small>
                                            @if($item->unit == 'sack')
                                                @php
                                                    $isWholeSacks = fmod((float)$item->quantity, 1.0) == 0.0;
                                                @endphp
                                                -{{ $isWholeSacks ? number_format($item->quantity, 0) : rtrim(rtrim(number_format($item->quantity, 2, '.', ''), '0'), '.') }} sacks
                                            @elseif($item->unit == 'piece')
                                                -{{ $item->quantity }} pieces
                                            @elseif($item->unit == 'kilo')
                                                @php
                                                    $sacksDeducted = $item->quantity / 50;
                                                @endphp
                                                -{{ number_format($sacksDeducted, 2) }} sacks
                                            @endif
                                        </small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="alert alert-warning p-2 mt-2">
                        <small>
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Note:</strong> 1 Sack = 50 Kilos
                        </small>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm">
                <div class="card-header py-2">
                    <h5 class="card-title mb-0 small fw-bold">Quick Actions</h5>
                </div>
                <div class="card-body p-3">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="setExactAmount()">
                            <i class="bi bi-currency-dollar"></i> Exact Amount
                        </button>
                        <div class="row gx-1">
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-success btn-sm w-100" onclick="setCommonAmount(1000)">
                                    ₱1,000
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-success btn-sm w-100" onclick="setCommonAmount(500)">
                                    ₱500
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function calculateChange() {
        const totalAmount = {{ $totalAmount }};
        const cashReceived = parseFloat(document.getElementById('cash_received').value) || 0;
        const change = cashReceived - totalAmount;
        
        document.getElementById('change').value = '₱' + change.toFixed(2);
        
        // Enable/disable complete button
        const completeBtn = document.getElementById('completeBtn');
        if (change >= 0) {
            completeBtn.disabled = false;
            document.getElementById('change').classList.remove('is-invalid');
            document.getElementById('change').classList.add('is-valid');
        } else {
            completeBtn.disabled = true;
            document.getElementById('change').classList.remove('is-valid');
            document.getElementById('change').classList.add('is-invalid');
        }
    }

    function setExactAmount() {
        document.getElementById('cash_received').value = {{ $totalAmount }};
        calculateChange();
    }

    function setCommonAmount(amount) {
        const currentCash = parseFloat(document.getElementById('cash_received').value) || 0;
        document.getElementById('cash_received').value = currentCash + amount;
        calculateChange();
    }

    // Auto-focus on cash received input
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('cash_received').focus();
        calculateChange();
    });

    // Prevent form submission if change is negative
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        const change = parseFloat(document.getElementById('change').value.replace('₱', '')) || 0;
        if (change < 0) {
            e.preventDefault();
            alert('Please enter sufficient cash received amount.');
            document.getElementById('cash_received').focus();
        }
    });
</script>

<style>
    .is-valid {
        border-color: #198754;
        background-color: #f8fff8;
    }
    .is-invalid {
        border-color: #dc3545;
        background-color: #fff8f8;
    }
    .page-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 1rem;
    }
    .page-header h1 {
        margin: 0;
        font-size: 1.5rem;
    }
</style>
@endpush
@endsection