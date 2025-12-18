@extends('layouts.app')

@section('title', 'Delivered Products')

@section('content')
<div class="page-header">
    <h1 class="h2 text-white">
        <i class="bi bi-box-seam me-2"></i> Product Arrived Records
    </h1>
</div>

<!-- Filter Section -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body bg-light-green rounded-3">
        <form id="filterForm" class="row align-items-end g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold small mb-1 text-dark">Start Date</label>
                <input type="date" id="filterDateFrom" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small mb-1 text-dark">End Date</label>
                <input type="date" id="filterDateTo" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small mb-1 text-dark">Product</label>
                <input type="text" id="filterProduct" class="form-control form-control-sm" placeholder="Search product">
            </div>
            <div class="col-md-3 d-flex justify-content-center align-items-center gap-2">
                <button type="button" id="applyFilter" class="btn btn-sm px-3" 
                        style="background: linear-gradient(135deg, #1E88E5, #1565C0); border: none; border-radius: 8px; font-weight: 500; color: white;">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <button type="button" id="resetFilter" class="btn btn-sm px-3"
                        style="background: linear-gradient(135deg, #6c757d, #495057); border: none; border-radius: 8px; font-weight: 500; color: white;">
                    <i class="bi bi-arrow-clockwise me-1"></i> Reset
                </button>
                <button type="button" id="printReport" class="btn btn-sm px-3"
                        style="background: linear-gradient(135deg, #1E88E5, #1565C0); border: none; border-radius: 8px; font-weight: 500; color: white;">
                    <i class="bi bi-printer me-1"></i> Print
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Stock-In Table -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-2">
                <h5 class="card-title mb-0 fw-bold text-dark">Records</h5>
            </div>
            <div class="card-body p-3">
                @if($stockIns->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover align-middle" id="stockInTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="small">Product</th>
                                    <th class="small">Brand</th>
                                    <th class="small text-end">Qty (Sacks)</th>
                                    <th class="small text-end">Qty (Pieces)</th>
                                    <th class="small text-end">Total (Kilos)</th>
                                    <th class="small">Handled By</th>
                                    <th class="small">Delivered By</th>
                                    <th class="small text-end">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stockIns as $stock)
                                <tr>
                                    <td class="small fw-semibold">{{ $stock->product->product_name ?? 'N/A' }}</td>
                                    <td class="small">{{ $stock->product->brand ?? 'N/A' }}</td>
                                    <td class="text-end">{{ $stock->quantity_sacks }}</td>
                                    <td class="text-end">{{ $stock->quantity_pieces }}</td>
                                    <td class="text-end fw-semibold text-success">{{ number_format($stock->quantity_kilos, 2) }} kg</td>
                                    <td class="small">{{ $stock->user->name ?? 'System' }}</td>
                                    <td class="small text-muted">{{ $stock->notes ?? '—' }}</td>
                                    <td class="text-end">
                                        <small>{{ $stock->created_at->format('M d, Y') }}</small><br>
                                        <small class="text-muted">{{ $stock->created_at->format('h:i A') }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="small text-muted">
                            Showing {{ $stockIns->firstItem() }} to {{ $stockIns->lastItem() }} of {{ $stockIns->total() }} records
                        </div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                {{-- Previous Page Link --}}
                                @if ($stockIns->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">&laquo;</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $stockIns->previousPageUrl() }}" rel="prev">&laquo;</a>
                                    </li>
                                @endif
                                
                                {{-- Pagination Elements --}}
                                @foreach ($stockIns->getUrlRange(1, $stockIns->lastPage()) as $page => $url)
                                    @if ($page == $stockIns->currentPage())
                                        <li class="page-item active">
                                            <span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach
                                
                                {{-- Next Page Link --}}
                                @if ($stockIns->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $stockIns->nextPageUrl() }}" rel="next">&raquo;</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">&raquo;</span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-box-seam display-4"></i>
                        <p class="mt-2">No recent stock-in transactions found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable with minimal configuration for client-side filtering
    const table = $('#stockInTable').DataTable({
        "paging": false, // Disable DataTable pagination (using Laravel pagination)
        "searching": true, // Enable searching
        "info": false, // Hide "Showing X of Y entries"
        "ordering": true, // Enable column ordering
        "order": [[7, "desc"]], // Default sort by date (column 7) descending
        "language": {
            "emptyTable": "No data available in table",
            "search": "Search:",
            "zeroRecords": "No matching records found"
        },
        "dom": '<"row"<"col-sm-12"tr>>',
        "columnDefs": [
            {
                "targets": [7], // Date column (index 7)
                "type": "date", // Specify date type for proper sorting
                "render": function(data, type, row) {
                    // Extract date from the cell content for sorting
                    if (type === 'sort' || type === 'type') {
                        const dateText = $(data).find('small').first().text();
                        return new Date(dateText);
                    }
                    return data;
                }
            }
        ]
    });

    // Filter logic - FIXED: Proper client-side filtering
    $('#applyFilter').on('click', function() {
        const product = $('#filterProduct').val().trim().toLowerCase();
        const startDate = $('#filterDateFrom').val();
        const endDate = $('#filterDateTo').val();

        // Clear any existing search
        table.search('').draw();
        
        // Apply product filter to column 0 (Product column)
        if (product) {
            table.column(0).search(product, true, false).draw();
        } else {
            table.column(0).search('').draw();
        }

        // Apply date range filter
        if (startDate || endDate) {
            // Custom filtering function for date range
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                const rowDateText = table.cell(dataIndex, 7).nodes().to$().find('small').first().text();
                const rowDate = new Date(rowDateText);
                
                let match = true;
                
                if (startDate) {
                    const filterStartDate = new Date(startDate);
                    if (rowDate < filterStartDate) {
                        match = false;
                    }
                }
                
                if (endDate) {
                    const filterEndDate = new Date(endDate);
                    if (rowDate > filterEndDate) {
                        match = false;
                    }
                }
                
                return match;
            });
            
            table.draw();
            // Remove the custom filter function after drawing
            $.fn.dataTable.ext.search.pop();
        }
    });

    // Reset filter - FIXED: Proper reset of all filters
    $('#resetFilter').on('click', function() {
        // Reset form inputs
        $('#filterForm')[0].reset();
        
        // Clear all DataTable filters
        table.search('').columns().search('').draw();
        
        // Clear any custom filters
        $.fn.dataTable.ext.search = [];
        table.draw();
    });

    // Print button
    $('#printReport').on('click', function() {
        window.print();
    });
});
</script>


<style>
/* --- Custom Styling --- */
.bg-light-green {
    background-color: #e9f7ef;
}

.page-header h1 {
    font-size: 1.6rem;
    font-weight: 600;
    color: #1a531b;
}

.btn-success {
    background-color: #1a531b;
    border-color: #1a531b;
}
.btn-success:hover {
    background-color: #144015;
    border-color: #144015;
}

.btn-outline-success {
    border-color: #1a531b;
    color: #1a531b;
}
.btn-outline-success:hover {
    background-color: #1a531b;
    color: white;
}

.form-control-sm {
    border-radius: 0.4rem;
    font-size: 0.875rem;
}

/* Pagination Styles */
.pagination .page-item.active .page-link {
    background-color: #1E88E5;
    border-color: #1E88E5;
    color: white;
}

.pagination .page-link {
    color: #1E88E5;
    border: 1px solid #dee2e6;
}

.pagination .page-link:hover {
    background-color: #f8f9fa;
    color: #1565C0;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
}

/* Hide everything except the records table when printing */
@media print {
    /* Hide all elements except the table and its container */
    * {
        visibility: hidden !important;
    }
    
    /* Show only the table and its immediate container */
    .card.shadow-sm.border-0,
    .card.shadow-sm.border-0 * {
        visibility: visible !important;
    }
    
    /* Hide everything else on the page */
    body * {
        visibility: hidden !important;
    }
    
    /* Position the table at the top left corner */
    .card.shadow-sm.border-0 {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        border: none !important;
        box-shadow: none !important;
    }
    
    /* Hide the "Records" header in the card */
    .card-header.bg-white {
        display: none !important;
    }
    
    /* Hide pagination when printing */
    .d-flex.justify-content-between {
        display: none !important;
    }
    
    /* Ensure table displays properly */
    .table-responsive {
        overflow: visible !important;
    }
    
    table {
        width: 100% !important;
        border-collapse: collapse !important;
    }
    
    th, td {
        border: 1px solid #ddd !important;
        padding: 8px !important;
    }
    
    th {
        background-color: #f2f2f2 !important;
        font-weight: bold !important;
    }
    
    /* Add a print header */
    .card.shadow-sm.border-0::before {
        content: "Product Arrived Records Report";
        display: block;
        font-size: 18px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #333;
    }
    
    /* Add print date and time */
    .card.shadow-sm.border-0::after {
        content: "Printed on: " attr(data-print-date);
        display: block;
        font-size: 12px;
        text-align: right;
        margin-top: 15px;
        padding-top: 10px;
        border-top: 1px solid #ddd;
        color: #666;
    }
    
    /* Set data attribute for print date */
    .card.shadow-sm.border-0 {
        data-print-date: "";
    }
}

/* Set print date when printing is triggered */
@page {
    size: auto;
    margin: 20mm;
}
</style>

<script>
// Set print date when print button is clicked
document.getElementById('printReport').addEventListener('click', function() {
    const printDate = new Date().toLocaleDateString() + ' ' + new Date().toLocaleTimeString();
    document.querySelector('.card.shadow-sm.border-0').setAttribute('data-print-date', printDate);
});
</script>
@endpush
@endsection