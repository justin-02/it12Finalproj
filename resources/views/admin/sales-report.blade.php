@extends('layouts.app')

@section('title', 'Sales Reports')

@section('content')
<div class="page-header">
    <h1 class="h2"><i class="bi bi-graph-up"></i> Sales Reports</h1>
</div>

<!-- Date Filter - Made more compact -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header py-2">
                <h5 class="card-title mb-0 small fw-bold">Date Filter</h5>
            </div>
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin.sales-report') }}">
                    <div class="row gx-2 align-items-end">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label small fw-bold">Start Date</label>
                            <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" 
                                   value="{{ $startDate->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label small fw-bold">End Date</label>
                            <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" 
                                   value="{{ $endDate->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <div class="d-grid gap-2 d-md-block">
                                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                <a href="{{ route('admin.sales-report') }}" class="btn btn-secondary btn-sm">Reset</a>
                                <button type="button" class="btn btn-success btn-sm" onclick="printReport()">Print</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Sales Summary - Made more compact -->
<div class="row mb-3">
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Sales</div>
                        <div class=" h5 mb-0 font-weight-bold text-gray-800 text-end">₱{{ number_format($totalSales, 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-currency fa-2x text-gray-300">₱</i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Transactions</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 text-end">{{ $totalTransactions }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-receipt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Average Sale</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 text-end">₱{{ number_format($averageSale, 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-graph-up fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sales Data - Made more compact -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header py-2">
                <h5 class="card-title mb-0 small fw-bold">Sales Details</h5>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-hover" id="salesTable">
                        <thead class="table-dark">
                            <tr>
                                <th class="small">Order #</th>
                                <th class="small">Cashier</th>
                                <th class="small">Helper</th>
                                <th class="text-end">Total Amount</th>
                                <th class="text-end">Cash Received</th>
                                <th class="text-end">Change</th>
                                <th class="text-end">Date</th>
                                <th class="small text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales as $order)
                            <tr>
                                <td class="small"><strong>{{ $order->order_number }}</strong></td>
                                <td class="small">{{ $order->cashier->name }}</td>
                                <td class="small">{{ $order->helper->name }}</td>
                                <td class="text-end">₱{{ number_format($order->total_amount, 2) }}</td>
                                <td class="text-end">₱{{ number_format($order->cash_received, 2) }}</td>
                                <td class="text-end">₱{{ number_format($order->change, 2) }}</td>
                                <td class="text-end">
                                    <small>{{ $order->created_at->format('M d, Y') }}</small><br>
                                    <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                </td>
                                <td class="small text-center">
                                    <button type="button" class="btn btn-sm btn-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#viewSaleModal"
                                            onclick="viewSale({{ $order->id }})">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>

                        @if($sales->count() > 0)
                        <tfoot>
                            <tr class="table-success">
                                <td colspan="3" class="text-end small fw-bold">Total:</td>
                                <td class="text-end small fw-bold">₱{{ number_format($totalSales, 2) }}</td>
                                <td class="text-end small fw-bold">₱{{ number_format($sales->sum('cash_received'), 2) }}</td>
                                <td class="text-end small fw-bold">₱{{ number_format($sales->sum('change'), 2) }}</td>
                                <td class="text-end small fw-bold">{{ $totalTransactions }} transactions</td>
                                <td class="text-end small fw-bold"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
                @if($sales->count() == 0)
                <div class="text-center text-muted py-4">
                    <i class="bi bi-receipt display-4"></i>
                    <p class="mt-2">No sales data found for the selected period.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#salesTable').DataTable({
            "pageLength": 25,
            "order": [[6, "desc"]],
            "language": {
                "search": "Search sales:",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "paginate": {
                    "previous": "<i class='bi bi-chevron-left'></i>",
                    "next": "<i class='bi bi-chevron-right'></i>"
                }
            },
            "dom": '<"row"<"col-sm-12"tr>>',
            "initComplete": function() {
                // Add custom styling after initialization
                $('.dataTables_length').addClass('mt-2');
                $('.dataTables_info').addClass('mt-2');
            }
        });

        // Set default date range to last 30 days if dates are not set
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        
        if (!startDate.value) {
            const thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
            startDate.value = thirtyDaysAgo.toISOString().split('T')[0];
        }
        
        if (!endDate.value) {
            const today = new Date();
            endDate.value = today.toISOString().split('T')[0];
        }
    });

    // Export functionality (optional)
    function exportToExcel() {
        const table = $('#salesTable').DataTable();
        const data = table.rows({ search: 'applied' }).data();
        let csvContent = "data:text/csv;charset=utf-8,";
        
        // Add headers
        const headers = ['Order #', 'Cashier', 'Helper', 'Total Amount', 'Cash Received', 'Change', 'Date'];
        csvContent += headers.join(',') + '\r\n';
        
        // Add data
        data.each(function(value, index) {
            const row = [
                value[0], // Order #
                value[1], // Cashier
                value[2], // Helper
                value[3].replace('₱', ''), // Total Amount
                value[4].replace('₱', ''), // Cash Received
                value[5].replace('₱', ''), // Change
                value[6]  // Date
            ];
            csvContent += row.join(',') + '\r\n';
        });
        
        // Download
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "sales_report_{{ $startDate->format('Y-m-d') }}_to_{{ $endDate->format('Y-m-d') }}.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function printReport() {
        const table = $('#salesTable').DataTable();
        const data = table.rows({ search: 'applied' }).data();
        
        let printContent = `
            <html>
            <head>
                <title>Sales Report - {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</title>
                <style>
                    body { font-family: Arial, sans-serif; font-size: 12px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                    th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
                    th { background-color: #f2f2f2; }
                    .total-row { background-color: #d4edda; font-weight: bold; }
                    .text-end { text-align: right; }
                    .header { text-align: center; margin-bottom: 20px; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h2>Sales Report</h2>
                    <p>Period: {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</p>
                    <p>Generated on: ${new Date().toLocaleString()}</p>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Cashier</th>
                            <th>Helper</th>
                            <th>Total Amount</th>
                            <th>Cash Received</th>
                            <th>Change</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        data.each(function(value, index) {
            printContent += `
                <tr>
                    <td>${value[0]}</td>
                    <td>${value[1]}</td>
                    <td>${value[2]}</td>
                    <td>${value[3]}</td>
                    <td>${value[4]}</td>
                    <td>${value[5]}</td>
                    <td>${value[6]}</td>
                </tr>
            `;
        });
        
        printContent += `
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="3" class="text-end">Total:</td>
                            <td>₱{{ number_format($totalSales, 2) }}</td>
                            <td>₱{{ number_format($sales->sum('cash_received'), 2) }}</td>
                            <td>₱{{ number_format($sales->sum('change'), 2) }}</td>
                            <td>{{ $totalTransactions }} transactions</td>
                        </tr>
                    </tfoot>
                </table>
            </body>
            </html>
        `;
        
        const printWindow = window.open('', '_blank');
        printWindow.document.write(printContent);
        printWindow.document.close();
        printWindow.print();
    }
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
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    .table-sm td, .table-sm th {
        padding: 0.3rem 0.5rem;
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
</style>
<!-- View Sale Modal -->
<!-- View Sale Modal -->
<div class="modal fade" id="viewSaleModal" tabindex="-1" aria-labelledby="viewSaleLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" id="saleDetailsContent">
      <!-- AJAX will load sale details here -->
    </div>
  </div>
</div>


@endpush
@endsection