@extends('layouts.app')

@section('title', 'Inventory Monitor')

@section('content')
<div class="page-header">
    <h1 class="h2"><i class="bi bi-shield-check"></i> Inventory Monitor</h1>
    <div></div>
</div>

<!-- Summary Cards -->
<div class="row mb-3">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Products</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $products->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-box-seam fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Normal Stock</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $products->where('is_active', true)->where('is_critical', false)->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Critical Stock</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $products->where('is_critical', true)->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Out of Stock</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $products->where('current_stock_sacks', '<=', 0)->where('current_stock_pieces', '<=', 0)->where('is_active', true)->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-x-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Critical Stock Alerts -->
@if($products->where('is_critical', true)->count() > 0)
<div class="row mb-3">
    <div class="col-12">
        <div class="card border-warning shadow-sm">
            <div class="card-header bg-warning text-dark py-2">
                <h5 class="card-title mb-0 small fw-bold">
                    <i class="bi bi-exclamation-triangle"></i> Critical Stock Alerts
                </h5>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-sm table-borderless mb-0">
                        <thead>
                            <tr>
                                <th class="small">Product</th>
                                <th class="small">Brand</th>
                                <th class="small">Stock (Sacks)</th>
                                <th class="small">Stock (Pieces)</th>
                                <th class="small">Critical Level</th>
                                <th class="small">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products->where('is_critical', true) as $product)
                            <tr class="critical-stock">
                                <td class="small">
                                    <strong>{{ $product->product_name }}</strong>
                                    <i class="bi bi-exclamation-triangle text-warning ms-1" title="Critical Stock"></i>
                                </td>
                                <td class="small">{{ $product->brand }}</td>
                                <td class="small text-danger fw-bold">
                                    @php
                                        $isWholeSacks = fmod((float)$product->current_stock_sacks, 1.0) == 0.0;
                                    @endphp
                                    {{ $isWholeSacks ? number_format($product->current_stock_sacks, 0) : rtrim(rtrim(number_format($product->current_stock_sacks, 2, '.', ''), '0'), '.') }}
                                    <br><small class="text-muted">({{ number_format($product->current_stock_sacks * 50, 0) }} kg)</small>
                                </td>
                                <td class="small text-danger fw-bold">
                                    {{ $product->current_stock_pieces }}
                                </td>
                                <td class="small">
                                    <small>Sacks: <span class="fw-bold">{{ $product->critical_level_sacks }}</span></small><br>
                                    <small>Pieces: <span class="fw-bold">{{ $product->critical_level_pieces }}</span></small>
                                </td>
                                <td class="small">
                                    <span class="badge bg-warning">Critical</span>
                                    <br>
                                    <small class="text-muted">
                                        @php
                                            $criticalUnits = [];
                                            if($product->current_stock_sacks > 0 && $product->current_stock_sacks <= $product->critical_level_sacks) {
                                                $criticalUnits[] = 'sacks';
                                            }
                                            if($product->current_stock_pieces > 0 && $product->current_stock_pieces <= $product->critical_level_pieces) {
                                                $criticalUnits[] = 'pieces';
                                            }
                                        @endphp
                                        Low {{ implode(', ', $criticalUnits) }}
                                    </small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- All Products Inventory -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header py-2">
                <h5 class="card-title mb-0 small fw-bold">All Products Inventory</h5>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-hover" id="inventoryTable">
                        <thead class="table-dark">
                            <tr>
                                <th class="small">Product Name</th>
                                <th class="small">Brand</th>
                                <th class="small">Price</th>
                                <th class="small">Stock (Sacks)</th>
                                <th class="small">Stock (Pieces)</th>
                                <th class="small">Critical Level</th>
                                <th class="small">Status</th>
                                <th class="small">Last Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr class="{{ $product->is_critical ? 'table-warning' : '' }} {{ !$product->is_active ? 'table-secondary' : '' }}">
                                <td class="small">
                                    <strong>{{ $product->product_name }}</strong>
                                    @if($product->is_critical)
                                        <i class="bi bi-exclamation-triangle text-warning ms-1" title="Critical Stock"></i>
                                    @endif
                                </td>
                                <td class="small">{{ $product->brand }}</td>
                                <td class="small">₱{{ number_format($product->price, 2) }}</td>
                                <td class="small {{ $product->current_stock_sacks > 0 && $product->current_stock_sacks <= $product->critical_level_sacks ? 'text-danger fw-bold' : '' }}">
                                    @php
                                        $isWholeSacks = fmod((float)$product->current_stock_sacks, 1.0) == 0.0;
                                    @endphp
                                    {{ $isWholeSacks ? number_format($product->current_stock_sacks, 0) : rtrim(rtrim(number_format($product->current_stock_sacks, 2, '.', ''), '0'), '.') }}
                                    <br><small class="text-muted">({{ number_format($product->current_stock_sacks * 50, 0) }} kg)</small>
                                    @if($product->current_stock_sacks > 0 && $product->current_stock_sacks <= $product->critical_level_sacks)
                                        <i class="bi bi-exclamation-triangle text-danger" title="Critical Sacks"></i>
                                    @endif
                                </td>
                                <td class="small {{ $product->current_stock_pieces > 0 && $product->current_stock_pieces <= $product->critical_level_pieces ? 'text-danger fw-bold' : '' }}">
                                    {{ $product->current_stock_pieces }}
                                    @if($product->current_stock_pieces > 0 && $product->current_stock_pieces <= $product->critical_level_pieces)
                                        <i class="bi bi-exclamation-triangle text-danger" title="Critical Pieces"></i>
                                    @endif
                                </td>
                                <td class="small">
                                    <small>Sacks: <span class="fw-bold">{{ $product->critical_level_sacks }}</span></small><br>
                                    <small>Pieces: <span class="fw-bold">{{ $product->critical_level_pieces }}</span></small>
                                </td>
                                <td class="small">
                                    @if($product->is_active)
                                        @if($product->current_stock_sacks <= 0 && $product->current_stock_pieces <= 0)
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @elseif($product->is_critical)
                                            <span class="badge bg-warning">Critical</span>
                                        @else
                                            <span class="badge bg-success">Normal</span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="small">
                                    <small>{{ $product->updated_at ? \Carbon\Carbon::parse($product->updated_at)->format('M d, Y') : 'N/A' }}</small>
                                    <br>
                                    <small class="text-muted">{{ $product->updated_at ? \Carbon\Carbon::parse($product->updated_at)->format('H:i') : '' }}</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#inventoryTable').DataTable({
            "pageLength": 25,
            "order": [[0, "asc"]],
            "language": {
                "search": "Search products:",
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

        // Add export functionality
        function exportToExcel() {
            const table = $('#inventoryTable').DataTable();
            const data = table.rows({ search: 'applied' }).data();
            let csvContent = "data:text/csv;charset=utf-8,";
            
            // Add headers
            const headers = ['Product Name', 'Brand', 'Price', 'Stock (Sacks)', 'Stock (Pieces)', 'Critical Level', 'Status'];
            csvContent += headers.join(',') + '\r\n';
            
            // Add data
            data.each(function(value, index) {
                const row = [
                    value[0], // Product Name
                    value[1], // Brand
                    value[2].replace('₱', ''), // Price
                    value[3].split('<')[0].trim(), // Stock Sacks
                    value[4].split('<')[0].trim(), // Stock Pieces
                    value[5].replace(/<[^>]*>/g, ''), // Critical Level
                    value[6]  // Status
                ];
                csvContent += row.join(',') + '\r\n';
            });
            
            // Download
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "inventory_monitor.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Add print functionality
        function printInventory() {
            const table = $('#inventoryTable').DataTable();
            const data = table.rows({ search: 'applied' }).data();
            
            let printContent = `
                <html>
                <head>
                    <title>Inventory Monitor Report</title>
                    <style>
                        body { font-family: Arial, sans-serif; font-size: 12px; }
                        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
                        th { background-color: #f2f2f2; }
                        .critical { background-color: #fff3cd; }
                        .inactive { background-color: #e9ecef; }
                        .text-center { text-align: center; }
                    </style>
                </head>
                <body>
                    <h2>Inventory Monitor Report</h2>
                    <p>Generated on: ${new Date().toLocaleString()}</p>
                    <table>
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Brand</th>
                                <th>Price</th>
                                <th>Stock (Sacks)</th>
                                <th>Stock (Pieces)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            data.each(function(value, index) {
                const isCritical = $(value[0]).find('.bi-exclamation-triangle').length > 0;
                const isInactive = value[6].includes('Inactive');
                printContent += `
                    <tr class="${isCritical ? 'critical' : ''} ${isInactive ? 'inactive' : ''}">
                        <td>${$(value[0]).text().trim()}</td>
                        <td>${value[1]}</td>
                        <td>${value[2]}</td>
                        <td>${$(value[3]).text().split('(')[0].trim()}</td>
                        <td>${$(value[4]).text().split('<')[0].trim()}</td>
                        <td>${value[6]}</td>
                    </tr>
                `;
            });
            
            printContent += `
                        </tbody>
                    </table>
                </body>
                </html>
            `;
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write(printContent);
            printWindow.document.close();
            printWindow.print();
        }

        // Add buttons to the table
        $(document).ready(function() {
            $('.dataTables_length').before(`
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-success" onclick="exportToExcel()">
                                <i class="bi bi-file-earmark-excel"></i> Export
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="printInventory()">
                                <i class="bi bi-printer"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
            `);
        });
    });
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
    .critical-stock {
        background-color: #fff3cd !important;
    }
</style>
@endpush
@endsection