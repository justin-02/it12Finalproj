@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-shield-check"></i> Admin Dashboard</h1>
    <div></div>
</div>

<style>
.dashboard-card {
    border: none;
    border-radius: 14px;
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
    background: linear-gradient(135deg, #E8FFD7 0%, #D6F5C3 100%);
    box-shadow: 0 4px 12px rgba(232, 255, 215, 0.2);
    border: 1px solid rgba(232, 255, 215, 0.3);
    height: 140px;
}

.dashboard-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 18px rgba(232, 255, 215, 0.3);
    background: linear-gradient(135deg, #F0FFE0 0%, #DFF8CF 100%);
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
    color: #1B5E20; /* Changed from #0D47A1 to match peso sign */
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

/* Smaller charts */
.chart-container {
    height: 200px;
    margin-bottom: 1rem;
}

/* Adjust table sizes */
.table-responsive {
    max-height: 400px;
    overflow-y: auto;
}

.table th, .table td {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}

/* Adjust header sizes */
.card-header h5 {
    font-size: 1rem;
    margin-bottom: 0;
}
</style>

<div class="row">
    <!-- Sales Overview -->
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="card-content">
                    <div class="text-content">
                        <div class="card-title">Today's Sales</div>
                        <h2 class="card-value">
                            <span class="currency">₱</span>{{ number_format($todaySales, 2) }}
                        </h2>
                        <div class="trend-indicator">
                            <i class="bi bi-arrow-up-short"></i>
                            <span>Daily Revenue</span>
                        </div>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-currency-exchange"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Sales -->
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="card-content">
                    <div class="text-content">
                        <div class="card-title">Weekly Sales</div>
                        <h2 class="card-value">
                            <span class="currency">₱</span>{{ number_format($weeklySales, 2) }}
                        </h2>
                        <div class="trend-indicator">
                            <i class="bi bi-graph-up-arrow"></i>
                            <span>Weekly Total</span>
                        </div>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-graph-up"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Critical Stock Alerts -->
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="card-content">
                    <div class="text-content">
                        <div class="card-title">Critical Stocks</div>
                        <h2 class="card-value">{{ $criticalProductsCount }}</h2>
                        <div class="trend-indicator">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span>Needs Attention</span>
                        </div>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Products -->
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="card-content">
                    <div class="text-content">
                        <div class="card-title">Total Products</div>
                        <h2 class="card-value">{{ $totalProducts }}</h2>
                        <div class="trend-indicator">
                            <i class="bi bi-box-fill"></i>
                            <span>Inventory Count</span>
                        </div>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts: Bar (left) and Line (right) -->
<div class="my-6 px-2">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold mb-2">Sales This Week (Bar)</h3>
            <div class="relative" style="height:180px; max-height:240px;">
                <canvas id="adminBarChart" class="w-full h-full block" style="width:100% !important; height:100% !important; max-height:240px;"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold mb-2">Sales Trend (Line)</h3>
            <div class="relative" style="height:180px; max-height:240px;">
                <canvas id="adminLineChart" class="w-full h-full block" style="width:100% !important; height:100% !important; max-height:240px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Stock  Alerts -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm animate__animated animate__fadeIn">
            <div class="card-header py-3 bg-light border-bottom">
                <h5 class="card-title mb-0 fw-bold text-dark">Recent Stock Alerts</h5>
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
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm animate__animated animate__fadeIn">
            <div class="card-header py-3 bg-light border-bottom">
                <h5 class="card-title mb-0 fw-bold text-dark">Recent Transactions</h5>
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
                                    <th class="py-3 px-3 text-end">Action</th>
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
                                <td class="small text-center">
    <button type="button" class="btn btn-sm btn-info" 
            data-bs-toggle="modal" 
            data-bs-target="#viewSaleModal"
            onclick="viewSale({{ $order->id }})"
            style="background: linear-gradient(135deg, #1E88E5, #1565C0); border: none; border-radius: 8px; font-weight: 500; color: white;">
        <i class="bi bi-eye" style="color: white;"></i> View
    </button>
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

<script>
// Initialize admin charts (uses backend-provided arrays if present, otherwise falls back to sample data)
function initAdminCharts() {
    const labels = {!! isset($chartLabels) ? json_encode($chartLabels) : json_encode(['Mon','Tue','Wed','Thu','Fri','Sat','Sun']) !!};
    const barData = {!! isset($barData) ? json_encode($barData) : json_encode([1200, 1500, 900, 1800, 1600, 1400, 1950]) !!};
    const lineData = {!! isset($lineData) ? json_encode($lineData) : json_encode([800, 1100, 950, 1300, 1700, 1250, 2000]) !!};

    // Bar chart
    const barCtx = document.getElementById('adminBarChart');
    if (barCtx) {
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Sales (₱)',
                    data: barData,
                    backgroundColor: 'rgba(45, 90, 61, 0.85)',
                    borderColor: 'rgba(45, 90, 61, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: { legend: { display: false } }
            }
        });
    }

    // Line chart
    const lineCtx = document.getElementById('adminLineChart');
    if (lineCtx) {
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue (₱)',
                    data: lineData,
                    fill: true,
                    backgroundColor: 'rgba(45, 90, 61, 0.12)',
                    borderColor: 'rgba(45, 90, 61, 0.95)',
                    tension: 0.3,
                    pointRadius: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: false }
                }
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', initAdminCharts, { passive: true });

function viewSale(orderId) {
    // Show loading spinner
    $('#saleDetailsContent').html(`
        <div class="text-center py-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted small">Loading sale details...</p>
        </div>
    `);

    // Fetch sale data from Laravel route
    $.ajax({
        url: `/admin/sales/${orderId}`,
        method: 'GET',
        success: function(response) {
            $('#saleDetailsContent').html(response);
        },
        error: function() {
            $('#saleDetailsContent').html(`
                <div class="alert alert-danger small">Failed to load sale details. Please try again.</div>
            `);
        }
    });
}
</script>
<!-- View Sale Modal -->
<div class="modal fade" id="viewSaleModal" tabindex="-1" aria-labelledby="viewSaleLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" id="saleDetailsContent">
      <!-- AJAX will load sale details here -->
    </div>
  </div>
</div>
@endsection
