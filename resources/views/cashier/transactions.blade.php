@extends('layouts.app')

@section('title', 'Transaction History')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Transaction History</h1>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">All Completed Transactions</h5>
            </div>
            <div class="card-body">
                @if($transactions->count() > 0)
                    <!-- DataTables will add its own controls here -->
                    <div class="table-responsive" style="font-size: 0.875rem;">
                        <table class="table table-striped table-sm" id="transactionsTable">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Helper</th>
                                    <th>Cashier</th>
                                    <th>Items</th>
                                    <th class="text-end">Total Amount</th>
                                    <th class="text-end">Cash Received</th>
                                    <th class="text-end">Change</th>
                                    <th class="text-end">Date</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td><strong>{{ $transaction->order_number }}</strong></td>
                                    <td>{{ $transaction->helper->name }}</td>
                                    <td>{{ $transaction->cashier->name }}</td>
                                    <td>
                                        <!-- Using details/summary for reliable item display -->
                                        <details>
                                            <summary class="btn btn-sm btn-outline-info py-1">
                                                <i class="bi bi-eye"></i> {{ $transaction->items->count() }} items
                                            </summary>
                                            <div class="card card-body p-2 mt-2" style="font-size: 0.8rem;">
                                                @foreach($transaction->items as $item)
                                                <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                                    <span>
                                                        {{ $item->product->product_name }}
                                                        @if($item->product->brand)
                                                            <br><small class="text-muted">{{ $item->product->brand }}</small>
                                                        @endif
                                                    </span>
                                                    <span class="text-nowrap">
                                                        {{ number_format($item->quantity, 2) }} 
                                                        <span class="badge bg-secondary">{{ $item->unit }}</span>
                                                        @ ₱{{ number_format($item->price, 2) }}
                                                    </span>
                                                </div>
                                                @endforeach
                                                <div class="mt-2 pt-2 border-top">
                                                    <strong>Order Total: ₱{{ number_format($transaction->total_amount, 2) }}</strong>
                                                </div>
                                            </div>
                                        </details>
                                    </td>
                                    <td class="text-end">
                                        <strong>₱{{ number_format($transaction->total_amount, 2) }}</strong>
                                    </td>
                                    <td class="text-end">₱{{ number_format($transaction->cash_received, 2) }}</td>
                                    <td class="text-end">
                                        <span class="text-muted fw-semibold">
                                            ₱{{ number_format($transaction->change, 2) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        {{ $transaction->created_at->format('M d, Y') }}
                                        <br>
                                        <small class="text-muted">{{ $transaction->created_at->format('H:i') }}</small>
                                    </td>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No transaction history found.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
<style>
    /* DataTables customization */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        padding: 1rem;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 0.375rem 0.75rem;
    }
    
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 0.375rem 2.25rem 0.375rem 0.75rem;
    }
    
    /* Custom positioning for print button next to search */
    .dataTables_filter {
        position: relative;
    }
    

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .dt-print-button {
            position: static;
            transform: none;
            margin-top: 10px;
            margin-right: 20px;
            display: inline-block;
            

        }
        
        
        .dataTables_filter {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
    }
    
    /* Details/summary styling */
    details {
        margin: 0;
    }
    
    details summary {
        list-style: none;
        cursor: pointer;
        padding: 4px 8px;
        border: 1px solid #86b7fe;
        border-radius: 4px;
        background: white;
        color: #0d6efd;
        display: inline-block;
    }
    
    details summary:hover {
        background: #e7f1ff;
    }
    
    details summary::-webkit-details-marker {
        display: none;
    }
    
    details[open] summary {
        background: #e7f1ff;
        border-color: #0d6efd;
    }
    
    details .card {
        position: absolute;
        z-index: 1000;
        min-width: 300px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Table styling */
    #transactionsTable {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    #transactionsTable thead th {
        position: sticky;
        top: 0;
        background-color: #f8f9fa;
        z-index: 10;
        border-bottom: 2px solid #dee2e6;
    }
    
    /* Print styles */
    @media print {
        .dataTables_length,
        .dataTables_filter,
        .dataTables_info,
        .dataTables_paginate,
        .btn,
        .dt-print-button {
            display: none !important;
        }
        
        details summary {
            display: none !important;
        }
        
        details[open] .card {
            display: block !important;
            position: static !important;
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
            page-break-inside: avoid !important;
        }
    }
</style>
@endpush

@push('scripts')
<!-- DataTables JS -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable with all features
        const table = $('#transactionsTable').DataTable({
            "order": [[7, "desc"]],
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "responsive": true,
            "autoWidth": false,
            "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 d-flex justify-content-end align-items-center'fB>>" +
       "<'row'<'col-sm-12'tr>>" +
       "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",

            "language": {
                "emptyTable": "No transactions available",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "Showing 0 to 0 of 0 entries",
                "infoFiltered": "(filtered from _MAX_ total entries)",
                "lengthMenu": "Show _MENU_ entries",
                "loadingRecords": "Loading...",
                "processing": "Processing...",
                "search": "Search:",
                "zeroRecords": "No matching records found",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            },
            "buttons": [
    {
        extend: 'print',
        text: '<i class="bi bi-printer-fill me-1"></i> Print',
        className: 'btn btn-sm btn-primary fw-semibold shadow-sm',
        // Additional styling for a blue button
        init: function(api, node, config) {
            $(node)
                .css({
                    'background-color': '#0d6efd',
                    'border-color': '#0d6efd',
                    'color': 'white'
                })
                .hover(function() {
                    $(this).css('background-color', '#0b5ed7');
                }, function() {
                    $(this).css('background-color', '#0d6efd');
                });
        },
        customize: function (win) {
            $(win.document.body).find('table')
                .addClass('compact')
                .css('font-size', '10pt');

            $(win.document.body).find('h1')
                .css('text-align', 'center')
                .css('font-size', '14pt');
        }
    }
],
            "initComplete": function() {
                // Add export buttons to the table
                // Create and add a standalone print button next to the search box
        
                
                // Style the search box
                $('.dataTables_filter input').attr('placeholder', 'Search Transactions');
                
                // Add custom CSS classes
                $('.dataTables_length select').addClass('form-select form-select-sm');
                $('.dataTables_filter input').addClass('form-control form-control-sm');
                
                // Add tooltip to print button
                printButton.tooltip({
                    trigger: 'hover',
                    placement: 'bottom'
                });
            }
        });
        
        // Make sure Bootstrap collapse works with DataTables
        $('#transactionsTable').on('draw.dt', function() {
            $('[data-bs-toggle="collapse"]').off('click').on('click', function(e) {
                e.preventDefault();
                const target = $(this).data('bs-target');
                $(target).collapse('toggle');
            });
        });
        
        // Initialize tooltips
        $('[title]').tooltip({
            trigger: 'hover',
            placement: 'top'
        });
    });
    
    // Print receipt function
    function printReceipt(transactionId) {
        window.open(`/cashier/transaction/${transactionId}/print`, '_blank');
    }
    
    // Print entire table
    function printTable() {
        // Trigger DataTables print button
        const button = $('.buttons-print');
        if (button.length) {
            button[0].click();
        } else {
            // Fallback to browser print
            window.print();
        }
    }
    
    // Refresh data
    function refreshData() {
        const refreshBtn = $('[onclick="refreshData()"]');
        const originalHtml = refreshBtn.html();
        refreshBtn.html('<i class="bi bi-arrow-clockwise spin"></i> Refreshing...');
        refreshBtn.prop('disabled', true);
        
        setTimeout(() => {
            window.location.reload();
        }, 500);
    }
    
    // Auto-refresh every 60 seconds
    setInterval(() => {
        $.ajax({
            url: '{{ route("cashier.check-new-transactions") }}',
            method: 'GET',
            success: function(response) {
                if (response.has_new) {
                    // Show notification
                    showNotification(`${response.new_count} new transaction(s) available`, 'info');
                }
            }
        });
    }, 60000);
    
    // Show notification
    function showNotification(message, type = 'info') {
        const notification = $(`
            <div class="toast align-items-center text-bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-${type === 'info' ? 'info-circle' : 'bell'} me-2"></i>
                        ${message}
                        <button class="btn btn-sm btn-light ms-3" onclick="refreshData()">Refresh</button>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);
        
        $('#notificationContainer').append(notification);
        const bsToast = new bootstrap.Toast(notification[0]);
        bsToast.show();
        
        // Remove after hide
        notification.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }
</script>

<style>
    /* Spinning icon */
    .spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* DataTables custom styling */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border: 1px solid #dee2e6 !important;
        margin-left: 2px !important;
        border-radius: 0.375rem !important;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #6c757d !important;
        border-color: #6c757d !important;
        color: white !important;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f8f9fa !important;
        border-color: #dee2e6 !important;
    }
    .dt-buttons {
    margin-left: 10px;
}

    /* Button group spacing */
    .dt-buttons .btn {
        margin-right: 5px;
        margin-bottom: 5px;
    }
</style>

<!-- Notification container -->
<div id="notificationContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>
@endpush