@extends('layouts.app')

@section('title', 'Inventory Monitor')

@section('content')
<div class="page-header">
    <h1 class="h2"><i class="bi bi-shield-check"></i> Inventory Monitor</h1>
    <div></div>
</div>

<style>
.product-summary-card {
    border: none;
    border-radius: 14px;
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(0, 0, 0, 0.04);
    height: 120px;
}

.product-summary-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
}

.product-summary-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
}

.product-summary-card .card-body {
    position: relative;
    z-index: 1;
    padding: 1rem !important;
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 100%;
}

.product-summary-card .card-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}

.product-summary-card .text-content {
    flex: 1;
    min-width: 0;
}

.product-summary-card .card-title {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    margin-bottom: 0.4rem;
    opacity: 0.95;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.product-summary-card .card-value {
    font-size: 1.5rem;
    font-weight: 800;
    margin: 0;
    line-height: 1.2;
    letter-spacing: -0.3px;
}

.product-summary-card .icon-wrapper {
    width: 45px;
    height: 45px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.4);
    flex-shrink: 0;
    margin-left: 8px;
}

.product-summary-card:hover .icon-wrapper {
    background: rgba(255, 255, 255, 0.4);
    transform: scale(1.08);
    border-color: rgba(255, 255, 255, 0.6);
}

.product-summary-card .icon-wrapper i {
    font-size: 1.3rem;
}

/* ALL CARDS HAVE THE SAME GREEN BACKGROUND */
.product-summary-card.total-products,
.product-summary-card.normal-stock,
.product-summary-card.critical-stock,
.product-summary-card.out-of-stock {
    background: linear-gradient(135deg, #E8FFD7 0%, #D6F5C3 100%);
    border-color: rgba(214, 245, 195, 0.3);
    box-shadow: 0 4px 12px rgba(214, 245, 195, 0.2);
}

.product-summary-card.total-products::before,
.product-summary-card.normal-stock::before,
.product-summary-card.critical-stock::before,
.product-summary-card.out-of-stock::before {
    background: linear-gradient(90deg, #8BC34A, #4CAF50);
}

/* Total Products - Green Text */
.product-summary-card.total-products .card-title,
.product-summary-card.total-products .card-value,
.product-summary-card.total-products .icon-wrapper i {
    color: #1B5E20;
}

/* Normal Stock - Green Text */
.product-summary-card.normal-stock .card-title,
.product-summary-card.normal-stock .card-value,
.product-summary-card.normal-stock .icon-wrapper i {
    color: #1B5E20;
}

/* Critical Stock - YELLOW Text */
.product-summary-card.critical-stock .card-title,
.product-summary-card.critical-stock .card-value {
    color: #FF8F00;
}

.product-summary-card.critical-stock .icon-wrapper i {
    color: #FF8F00;
}

/* Out of Stock - RED Text */
.product-summary-card.out-of-stock .card-title,
.product-summary-card.out-of-stock .card-value {
    color: #D32F2F;
}

.product-summary-card.out-of-stock .icon-wrapper i {
    color: #D32F2F;
}
</style>

<!-- Product Summary Cards -->
<div class="row mb-3">
    @php
        $cardStats = [
            [
                'title' => 'Total Products', 
                'value' => $products->total(), 
                'icon' => 'bi-box-seam', 
                'class' => 'total-products'
            ],
            [
                'title' => 'Normal Stock', 
                'value' => $normalStockCount, 
                'icon' => 'bi-check-circle', 
                'class' => 'normal-stock'
            ],
            [
                'title' => 'Critical Stock', 
                'value' => $criticalStockCount, 
                'icon' => 'bi-exclamation-triangle', 
                'class' => 'critical-stock'
            ],
            [
                'title' => 'Out of Stock', 
                'value' => $outOfStockCount, 
                'icon' => 'bi-x-circle', 
                'class' => 'out-of-stock'
            ]
        ];
    @endphp

    @foreach($cardStats as $card)
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card product-summary-card {{ $card['class'] }} h-100">
            <div class="card-body">
                <div class="card-content">
                    <div class="text-content">
                        <div class="card-title">{{ $card['title'] }}</div>
                        <div class="card-value">{{ $card['value'] }}</div>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi {{ $card['icon'] }}"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Critical Stock Alerts -->
@if($criticalProducts->count() > 0)
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
                                <th class="small text-start">Product</th>
                                <th class="small text-start">Brand</th>
                                <th class="small text-end">Stock (Sacks)</th>
                                <th class="small text-end">Stock (Pieces)</th>
                                <th class="small text-end">Critical Level</th>
                                <th class="small text-start">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($criticalProducts as $product)
                            <tr class="critical-stock">
                                <td class="small text-start">
                                    <strong class="fw-bold">{{ $product->product_name }}</strong>
                                    <i class="bi bi-exclamation-triangle text-warning ms-1" title="Critical Stock"></i>
                                </td>
                                <td class="small text-start">{{ $product->brand }}</td>
                                <td class="small text-danger fw-bold text-end">
                                    @php
                                        $isWholeSacks = fmod((float)$product->current_stock_sacks, 1.0) == 0.0;
                                    @endphp
                                    {{ $isWholeSacks ? number_format($product->current_stock_sacks, 0) : rtrim(rtrim(number_format($product->current_stock_sacks, 2, '.', ''), '0'), '.') }}
                                    <br><small class="text-muted">({{ number_format($product->current_stock_sacks * 50, 0) }} kg)</small>
                                </td>
                                <td class="small text-danger fw-bold text-end">
                                    {{ $product->current_stock_pieces }}
                                </td>
                                <td class="small text-end">
                                    <small>Sacks: <span class="fw-bold">{{ $product->critical_level_sacks }}</span></small><br>
                                    <small>Pieces: <span class="fw-bold">{{ $product->critical_level_pieces }}</span></small>
                                </td>
                                <td class="small text-start">
                                    <span class="badge bg-warning">Critical</span><br>
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
                                    </small><br>
                                    <button class="btn btn-outline-primary btn-sm mt-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#sendMessageModal"
                                            onclick="setProductForMessage({{ $product->id }}, '{{ $product->product_name }}', '{{ $product->brand }}')">
                                        <i class="bi bi-chat-dots"></i> Message
                                    </button>
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
                                <th class="small text-start">Product Name</th>
                                <th class="small text-start">Brand</th>
                                <th class="small text-end">Price</th>
                                <th class="small text-end">Stock (Sacks)</th>
                                <th class="small text-end">Stock (Pieces)</th>
                                <th class="small text-end">Critical Level</th>
                                <th class="small text-start">Status</th>
                                <th class="small text-end">Last Updated</th>
                                <th class="small text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr class="{{ $product->is_critical ? 'table-warning' : '' }}  {{ !$product->is_active ? 'table-secondary' : '' }} {{ ($product->current_stock_sacks <= 0 && $product->current_stock_pieces <= 0 && $product->is_active) ? 'table-danger' : '' }}">
                                <td class="small text-start">
                                    <strong class="fw-bold">{{ $product->product_name }}</strong>
                                    @if($product->is_critical)
                                        <i class="bi bi-exclamation-triangle text-warning ms-1"></i>
                                    @endif
                                </td>
                                <td class="small text-start">{{ $product->brand }}</td>
                                <td class="small text-end">₱{{ number_format($product->price, 2) }}</td>
                                <td class="small text-end {{ $product->current_stock_sacks > 0 && $product->current_stock_sacks <= $product->critical_level_sacks ? 'text-danger fw-bold' : '' }}">
                                    @php
                                        $isWholeSacks = fmod((float)$product->current_stock_sacks, 1.0) == 0.0;
                                    @endphp
                                    {{ $isWholeSacks ? number_format($product->current_stock_sacks, 0) : rtrim(rtrim(number_format($product->current_stock_sacks, 2, '.', ''), '0'), '.') }}
                                    <br><small class="text-muted">({{ number_format($product->current_stock_sacks * 50, 0) }} kg)</small>
                                </td>
                                <td class="small text-end {{ $product->current_stock_pieces > 0 && $product->current_stock_pieces <= $product->critical_level_pieces ? 'text-danger fw-bold' : '' }}">
                                    {{ $product->current_stock_pieces }}
                                </td>
                                <td class="small text-end">
                                    <small>Sacks: <span class="fw-bold">{{ $product->critical_level_sacks }}</span></small><br>
                                    <small>Pieces: <span class="fw-bold">{{ $product->critical_level_pieces }}</span></small>
                                </td>
                                <td class="small text-start">
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
                                <td class="small text-end">
                                    <small>{{ $product->updated_at ? \Carbon\Carbon::parse($product->updated_at)->format('M d, Y') : 'N/A' }}</small><br>
                                    <small class="text-muted">{{ $product->updated_at ? \Carbon\Carbon::parse($product->updated_at)->format('H:i') : '' }}</small>
                                </td>
                                <td class="small text-center">
                                    <button class="btn btn-outline-primary btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#sendMessageModal"
                                            onclick="setProductForMessage({{ $product->id }}, '{{ $product->product_name }}', '{{ $product->brand }}')">
                                        <i class="bi bi-chat-dots"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($products->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="small text-muted">
                        Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} products
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            {{-- Previous Page Link --}}
                            @if ($products->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">&laquo;</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $products->previousPageUrl() }}" rel="prev">&laquo;</a>
                                </li>
                            @endif
                            
                            {{-- Pagination Elements --}}
                            @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                                @if ($page == $products->currentPage())
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
                            @if ($products->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $products->nextPageUrl() }}" rel="next">&raquo;</a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">&raquo;</span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Send Message Modal -->
<div class="modal fade" id="sendMessageModal" tabindex="-1" aria-labelledby="sendMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendMessageModalLabel">
                    <i class="bi bi-chat-dots"></i> Send Message to Inventory
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.send-message') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="message_product_id" name="product_id">
                    
                    <div class="mb-3">
                        <label for="product_info" class="form-label">Product</label>
                        <input type="text" class="form-control" id="product_info" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="message" name="message" rows="4" 
                                  placeholder="Enter your message to the inventory team about this product..." 
                                  required maxlength="1000"></textarea>
                        <div class="form-text">Maximum 1000 characters</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i> Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Disable DataTables pagination since we're using Laravel pagination
        $('#inventoryTable').DataTable({
            "pageLength": 10,
            "ordering": false,
            "searching": false,
            "info": false,
            "paging": false,
            "order": [],
            "language": {
                "search": "Search products:",
                "emptyTable": "No data available in table"
            },
            "dom": '<"row"<"col-sm-12"tr>>'
        });
    });

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

    function setProductForMessage(productId, productName, brand) {
        document.getElementById('message_product_id').value = productId;
        document.getElementById('product_info').value = `${productName} (${brand})`;
        document.getElementById('message').value = '';
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
    .critical-stock {
        background-color: #fff3cd !important;
    }
    
    /* Fixed font size for product names - added fw-bold */
    #inventoryTable tbody td:first-child strong {
        font-weight: bold !important;
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
</style>
@endpush
@endsection