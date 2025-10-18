@extends('layouts.app')

@section('title', 'Cashier Dashboard')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-cash-coin"></i> Cashier Dashboard</h1>
    <div></div>
</div>

<!-- Sales Overview -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Today's Sales</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 text-end">₱{{ number_format($todaySales, 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-currency fa-2x text-gray-300">₱</i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Today's Transactions</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 text-end">{{ $todayTransactions }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-receipt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pending Orders</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 text-end">{{ $pendingOrdersCount }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Weekly Sales</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 text-end">₱{{ number_format($weeklySales, 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-graph-up fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
                                        <button class="btn btn-sm btn-outline-info" type="button" data-bs-toggle="collapse" data-bs-target="#orderItems{{ $order->id }}">
                                            {{ $order->items->count() }} items
                                        </button>
                                        <div class="collapse mt-2" id="orderItems{{ $order->id }}">
                                            <div class="card card-body">
                                                @foreach($order->items as $item)
                                                <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                                    <span>
                                                        {{ $item->product->product_name }}<br>
                                                        <small class="text-muted ">{{ $item->product->brand }}</small>
                                                    </span>
                                                    <span>
                                                        @php
                                                            $isWhole = fmod((float)$item->quantity, 1.0) == 0.0;
                                                        @endphp
                                                        {{ $isWhole ? number_format($item->quantity, 0) : rtrim(rtrim(number_format($item->quantity, 2, '.', ''), '0'), '.') }} 
                                                        <span class="badge bg-secondary">{{ $item->unit }}</span>
                                                        @if($item->price)
                                                            @ ₱{{ number_format($item->price, 2) }}
                                                        @else
                                                            (Price not set)
                                                        @endif
                                                        @if($item->unit == 'kilo')
                                                            <br><small class="text-muted">({{ number_format($item->quantity / 50, 2) }} sacks)</small>
                                                        @endif
                                                    </span>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($order->items->where('price')->count() > 0)
                                            ₱{{ number_format($order->items->sum('subtotal'), 2) }}
                                        @else
                                            <span class="text-muted">Not calculated</span>
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

