@extends('layouts.app')

@section('title', 'All Products - Inventory Management')

@section('content')
<div class="page-header">
    <h1 class="h2">
        <i class="bi bi-box-seam"></i> All Products
    </h1>
    <div>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="bi bi-plus-circle"></i> Add Product
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#stockInModal">
            <i class="bi bi-arrow-down-circle"></i> Stock In
        </button>
        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#stockOutModal">
            <i class="bi bi-arrow-up-circle"></i> Stock Out
        </button>
    </div>
</div>

<!-- Products Summary - Made more compact -->
<div class="row mb-3">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Products</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProducts }}</div>
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
                            Active Products</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeProducts }}</div>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $criticalProducts }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Out of Stock</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $outOfStockProducts }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-x-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Products Table - Made more compact -->
<div class="card shadow-sm">
    <div class="card-header py-2">
        <h5 class="card-title mb-0 small fw-bold">
            <i class="bi bi-list-ul"></i> Product List
        </h5>
    </div>
    <div class="card-body p-3">
        <!-- Search and Filter - Made more compact -->
        <div class="row gx-2 mb-3">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control form-control-sm" placeholder="Search products..." id="searchInput">
                    <button class="btn btn-outline-primary btn-sm" type="button" id="searchButton">
                        <i class="bi bi-search"></i>
                    </button>
                    <button class="btn btn-outline-danger btn-sm" type="button" id="resetFilters" title="Reset filters">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-2">
                <select class="form-select form-select-sm" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="critical">Critical</option>
                    <option value="out_of_stock">Out of Stock</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select form-select-sm" id="brandFilter">
                    <option value="">All Brands</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand }}">{{ $brand }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-success" onclick="exportToCSV()" title="Export to CSV">
                        <i class="bi bi-file-earmark-excel"></i> Export
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="printTable()" title="Print">
                        <i class="bi bi-printer"></i> Print
                    </button>
                </div>
            </div>
        </div>

        <!-- Table - Made more compact -->
        <div class="table-responsive">
            <table class="table table-sm table-striped table-hover" id="productsTable">
                <thead class="table-dark">
                    <tr>
                        <th class="small">#</th>
                        <th class="small">Product Name</th>
                        <th class="small">Brand</th>
                        <th class="small">Price</th>
                        <th class="small">Stock (Sacks)</th>
                        <th class="small">Stock (Pieces)</th>
                        <th class="small">Critical Level</th>
                        <th class="small">Status</th>
                        <th class="small">Last Updated</th>
                        <th class="small">Actions</th>    
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr class="{{ $product->is_critical ? 'table-warning' : '' }} {{ !$product->is_active ? 'table-secondary' : '' }}">
                        <td class="small">{{ $loop->iteration }}</td>
                        <td class="small">
                            <strong>{{ $product->product_name }}</strong>
                            @if($product->is_critical)
                                <i class="bi bi-exclamation-triangle text-warning" title="Critical Stock"></i>
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
                            <small>
                                Sacks: <span class="fw-bold">{{ $product->critical_level_sacks }}</span><br>
                                Pieces: <span class="fw-bold">{{ $product->critical_level_pieces }}</span>
                            </small>
                        </td>
                        <td class="small">
                            @if($product->is_active)
                                @if($product->current_stock_sacks <= 0 && $product->current_stock_pieces <= 0)
                                    <span class="badge bg-danger">Out of Stock</span>
                                @elseif($product->is_critical)
                                    <span class="badge bg-warning">Critical</span>
                                @else
                                    <span class="badge bg-success">Active</span>
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
                        <td class="small">
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" 
                                        data-bs-target="#editProductModal" 
                                        onclick="editProduct({{ $product }})"
                                        title="Edit Product">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" 
                                        data-bs-target="#stockInModal" 
                                        onclick="setProductForStockIn({{ $product->id }})"
                                        title="Add Stock">
                                    <i class="bi bi-arrow-down-circle"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" 
                                        data-bs-target="#stockOutModal" 
                                        onclick="setProductForStockOut({{ $product->id }})"
                                        title="Deduct Stock">
                                    <i class="bi bi-arrow-up-circle"></i>
                                </button>
                                @if($product->is_critical)
                                <button class="btn btn-outline-warning btn-sm" onclick="reportCriticalLevel({{ $product->id }})"
                                        title="Report Critical Stock">
                                    <i class="bi bi-megaphone"></i>
                                </button>
                                @endif
                                <form method="POST" action="{{ route('inventory.products.toggle-status', $product->id) }}" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-{{ $product->is_active ? 'secondary' : 'success' }} btn-sm"
                                            title="{{ $product->is_active ? 'Deactivate' : 'Activate' }} Product"
                                            onclick="return confirm('Are you sure you want to {{ $product->is_active ? 'deactivate' : 'activate' }} this product?')">
                                        <i class="bi bi-{{ $product->is_active ? 'x-circle' : 'check-circle' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination - Made more compact -->
        @if($products->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} entries
            </div>
            <nav>
                {{ $products->links('pagination::bootstrap-5') }}
            </nav>
        </div>
        @endif
    </div>
</div>

<!-- Include Modals -->
@include('inventory.partials.add-product-modal')
@include('inventory.partials.stock-in-modal')
@include('inventory.partials.stock-out-modal')
@include('inventory.partials.edit-product-modal')

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#productsTable').DataTable({
            "pageLength": 25,
            "order": [[1, "asc"]],
            "language": {
                "search": "Search products:",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "paginate": {
                    "previous": "<i class='bi bi-chevron-left'></i>",
                    "next": "<i class='bi bi-chevron-right'></i>"
                }
            },
            "columnDefs": [
                { "orderable": false, "targets": [0, 9] }, // Disable sorting on # and Actions columns
                { "searchable": false, "targets": [0, 3, 6, 8, 9] } // Disable search on some columns
            ],
            "dom": '<"row"<"col-sm-12"tr>>',
            "initComplete": function() {
                // Add custom styling after initialization
                $('.dataTables_length').addClass('mt-2');
                $('.dataTables_info').addClass('mt-2');
            }
        });

        // Real-time search functionality
        $('#searchInput').on('input', function() {
            table.search($(this).val()).draw();
        });

        // Search button for manual search
        $('#searchButton').click(function() {
            table.search($('#searchInput').val()).draw();
        });

        // Filter by status
        $('#statusFilter').change(function() {
            const status = this.value;
            
            if (status === '') {
                table.columns().search('').draw();
                return;
            }

            // Custom filtering for status
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    if (status === '') return true;
                    
                    const statusText = data[7].toLowerCase(); // Status is in column 7
                    
                    switch(status) {
                        case 'active':
                            return statusText.includes('active') && 
                                   !statusText.includes('critical') && 
                                   !statusText.includes('out of stock');
                        case 'inactive':
                            return statusText.includes('inactive');
                        case 'critical':
                            return statusText.includes('critical');
                        case 'out_of_stock':
                            return statusText.includes('out of stock');
                        default:
                            return true;
                    }
                }
            );
            
            table.draw();
            $.fn.dataTable.ext.search.pop(); // Remove the filter function after drawing
        });

        // Filter by brand
        $('#brandFilter').change(function() {
            const brand = this.value;
            
            if (brand === '') {
                table.columns(2).search('').draw();
            } else {
                // Exact match for brand
                table.columns(2).search('^' + brand + '$', true, false).draw();
            }
        });

        // Reset all filters
        function resetFilters() {
            $('#searchInput').val('');
            $('#statusFilter').val('');
            $('#brandFilter').val('');
            table.search('').columns().search('').draw();
        }

        $('#resetFilters').click(function() {
            resetFilters();
        });

        // Auto-focus on search input
        $('#searchInput').focus();
    });

    function editProduct(product) {
        document.getElementById('edit_product_name').value = product.product_name;
        document.getElementById('edit_brand').value = product.brand;
        document.getElementById('edit_price').value = product.price;
        document.getElementById('edit_current_stock_sacks').value = product.current_stock_sacks;
        document.getElementById('edit_current_stock_pieces').value = product.current_stock_pieces;
        document.getElementById('edit_critical_level_sacks').value = product.critical_level_sacks;
        document.getElementById('edit_critical_level_pieces').value = product.critical_level_pieces;
        document.getElementById('edit_is_active').checked = product.is_active;
        
        // Set form action
        document.getElementById('editProductForm').action = `/inventory/products/${product.id}`;
    }

    function setProductForStockIn(productId) {
        document.getElementById('stock_product_id').value = productId;
    }

    function setProductForStockOut(productId) {
        const select = document.getElementById('stock_out_product_id');
        if (select) select.value = productId;
    }

    function reportCriticalLevel(productId) {
        if (confirm('Report this product as critical stock to admin?')) {
            fetch(`/inventory/report-critical/${productId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                alert('Critical level reported to admin!');
                location.reload();
            })
            .catch(error => {
                alert('Error reporting critical level');
            });
        }
    }

    function exportToCSV() {
        const table = $('#productsTable').DataTable();
        const data = table.rows({ search: 'applied' }).data();
        let csvContent = "data:text/csv;charset=utf-8,";
        
        // Add headers
        const headers = ['Product Name', 'Brand', 'Price', 'Stock (Sacks)', 'Stock (Pieces)', 'Critical Level', 'Status'];
        csvContent += headers.join(',') + '\r\n';
        
        // Add data
        data.each(function(value, index) {
            const row = [
                value[1], // Product Name
                value[2], // Brand
                value[3].replace('₱', ''), // Price
                value[4].split('<')[0].trim(), // Stock Sacks (remove HTML)
                value[5].split('<')[0].trim(), // Stock Pieces (remove HTML)
                value[6].replace(/<[^>]*>/g, ''), // Critical Level (remove HTML)
                value[7]  // Status
            ];
            csvContent += row.join(',') + '\r\n';
        });
        
        // Download
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "products_inventory.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function printTable() {
        const table = $('#productsTable').DataTable();
        const data = table.rows({ search: 'applied' }).data();
        
        let printContent = `
            <html>
            <head>
                <title>Products Inventory Report</title>
                <style>
                    body { font-family: Arial, sans-serif; font-size: 12px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
                    th { background-color: #f2f2f2; }
                    .critical { background-color: #fff3cd; }
                    .text-center { text-align: center; }
                </style>
            </head>
            <body>
                <h2>Products Inventory Report</h2>
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
            const isCritical = value[7].toLowerCase().includes('critical');
            printContent += `
                <tr class="${isCritical ? 'critical' : ''}">
                    <td>${$(value[1]).text().trim()}</td>
                    <td>${value[2]}</td>
                    <td>${value[3]}</td>
                    <td>${$(value[4]).text().split('(')[0].trim()}</td>
                    <td>${$(value[5]).text().split('<')[0].trim()}</td>
                    <td>${value[7]}</td>
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
    .btn-group-sm > .btn, .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
</style>
@endpush
@endsection