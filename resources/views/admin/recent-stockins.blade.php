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
            <div class="col-md-3 d-flex justify-content-start align-items-center">
                <button type="button" id="applyFilter" class="btn btn-success me-2 px-3"><i class="bi bi-funnel"></i> Filter</button>
                <button type="button" id="resetFilter" class="btn btn-secondary me-2 px-3"><i class="bi bi-arrow-clockwise"></i> Reset</button>
                <button type="button" id="printReport" class="btn btn-outline-success px-3"><i class="bi bi-printer"></i> Print</button>
            </div>
        </form>
    </div>
</div>

<!-- Stock-In Table -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white py-2">
                <h5 class="card-title mb-0 small fw-bold text-black" >📦 Records</h5>
            </div>
            <div class="card-body p-3">
                @if($stockIns->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover align-middle" id="stockInTable">
                            <thead class="table-success">
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
    const table = $('#stockInTable').DataTable({
        "pageLength": 25,
        "order": [[7, "desc"]],
        "language": {
            "search": "",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "paginate": {
                "previous": "<i class='bi bi-chevron-left'></i>",
                "next": "<i class='bi bi-chevron-right'></i>"
            }
        },
        "dom": '<"row"<"col-sm-12"tr>>'
    });

    // Filter logic
    $('#applyFilter').on('click', function() {
        const product = $('#filterProduct').val().trim();
        const startDate = $('#filterDateFrom').val();
        const endDate = $('#filterDateTo').val();

        // Product name filter
        table.column(0).search(product);

        // Date range filter
        $.fn.dataTable.ext.search.push(function(settings, data) {
            const date = new Date(data[7]);
            if ((startDate === "" && endDate === "") ||
                (startDate === "" && date <= new Date(endDate)) ||
                (new Date(startDate) <= date && endDate === "") ||
                (new Date(startDate) <= date && date <= new Date(endDate))) {
                return true;
            }
            return false;
        });

        table.draw();
        $.fn.dataTable.ext.search.pop();
    });

    // Reset filter
    $('#resetFilter').on('click', function() {
        $('#filterForm')[0].reset();
        table.search('').columns().search('').draw();
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

.table-success th {
    background-color: #1a531b !important;
    color: #fff !important;
}

.card-header.bg-success {
    background-color: #ffffffff !important;
}

.form-control-sm {
    border-radius: 0.4rem;
    font-size: 0.875rem;
}
</style>
@endpush
@endsection
