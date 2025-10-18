@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-shield-check"></i> Admin Dashboard</h1>
    <div></div>
</div>

<div class="row">
    <!-- Sales Overview -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1 text-start">
                            Today's Sales
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 text-end">
                            ₱{{ number_format($todaySales, 2) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-currency fa-2x text-gray-300">₱</i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Sales -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1 text-start">
                            Weekly Sales
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 text-end">
                            ₱{{ number_format($weeklySales, 2) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-graph-up fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Critical Stock Alerts -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1 text-start">
                            Critical Stocks
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 text-end">
                            {{ $criticalProductsCount }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Products -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1 text-start">
                            Total Products
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 text-end">
                            {{ $totalProducts }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-box-seam fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Stock  Alerts -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Recent Stock Alerts</h5>
            </div>
            <div class="card-body">
                @if($recentAlerts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th class="text-start px-3 py-3">Product</th>
                                    <th class="text-start px-3 py-3">Alert Type</th>
                                    <th class="text-start px-3 py-3">Message</th>
                                    <th class="text-end px-3 py-3">Date</th>
                                    <th class="text-start px-3 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAlerts as $alert)
                                <tr class="{{ $alert->resolved ? '' : 'table-warning' }}">
                                    <td class="text-start px-3 py-3">
                                        {{ $alert->product->product_name }} - {{ $alert->product->brand }}
                                    </td>
                                    <td class="text-start px-3 py-3">
                                        <span class="badge bg-{{ $alert->alert_type == 'critical' ? 'warning' : 'danger' }}">
                                            {{ ucfirst($alert->alert_type) }}
                                        </span>
                                    </td>
                                    <td class="text-start px-3 py-3">
                                        {{ $alert->message }}
                                    </td>
                                    <td class="text-end px-3 py-3">
                                        {{ $alert->created_at ? \Carbon\Carbon::parse($alert->created_at)->format('M d, Y H:i') : 'N/A' }}
                                    </td>
                                    <td class="text-start px-3 py-3">
                                        @if($alert->resolved)
                                            <span class="badge bg-success">Resolved</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No recent alerts.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm animate__animated animate__fadeIn">
            <div class="card-header py-3 bg-light border-bottom">
                <h5 class="card-title mb-0 fw-bold text-secondary">Recent Transactions</h5>
            </div>
            <div class="card-body p-4">
                @if($recentTransactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th class="py-3 px-3 text-start">Order #</th>
                                    <th class="py-3 px-3 text-start">Cashier</th>
                                    <th class="py-3 px-3 text-end">Total Amount</th>
                                    <th class="py-3 px-3 text-center">Items</th>
                                    <th class="py-3 px-3 text-end">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $order)
                                <tr>
                                    <td class="py-3 px-3 text-start fw-semibold text-dark">
                                        {{ $order->order_number }}
                                    </td>
                                    <td class="py-3 px-3 text-start">{{ $order->cashier->name }}</td>
                                    <td class="py-3 px-3 text-end fw-bold text-success">
                                        ₱{{ number_format($order->total_amount, 2) }}
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="badge bg-primary-subtle text-primary">
                                            {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-end text-muted">
                                        <small>{{ $order->created_at->format('M d, Y') }}</small><br>
                                        <small>{{ $order->created_at->format('H:i') }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-receipt display-5"></i>
                        <p class="mt-2 mb-0">No recent transactions.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
