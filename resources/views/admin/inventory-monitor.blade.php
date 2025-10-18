@extends('layouts.app')

@section('title', 'Inventory Monitor')

@section('content')
<div class="page-header">
    <h1 class="h2"><i class="bi bi-shield-check"></i> Inventory Monitor</h1>
    <div></div>
</div>

<!-- Summary Cards -->
<div class="row mb-3">
    @php
        $cardStats = [
            ['title' => 'Total Products', 'value' => $products->count(), 'icon' => 'bi-box-seam', 'color' => 'primary'],
            ['title' => 'Normal Stock', 'value' => $products->where('is_active', true)->where('is_critical', false)->count(), 'icon' => 'bi-check-circle', 'color' => 'success'],
            ['title' => 'Critical Stock', 'value' => $products->where('is_critical', true)->count(), 'icon' => 'bi-exclamation-triangle', 'color' => 'warning'],
            ['title' => 'Out of Stock', 'value' => $products->where('current_stock_sacks', '<=', 0)->where('current_stock_pieces', '<=', 0)->where('is_active', true)->count(), 'icon' => 'bi-x-circle', 'color' => 'danger']
        ];
    @endphp

    @foreach($cardStats as $card)
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-{{ $card['color'] }} shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-{{ $card['color'] }} text-uppercase mb-1 text-start">
                            {{ $card['title'] }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 text-end">
                            {{ $card['value'] }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi {{ $card['icon'] }} fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
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
                                <th class="small text-start">Product</th>
                                <th class="small text-start">Brand</th>
                                <th class="small text-end">Stock (Sacks)</th>
                                <th class="small text-end">Stock (Pieces)</th>
                                <th class="small text-end">Critical Level</th>
                                <th class="small text-start">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products->where('is_critical', true) as $product)
                            <tr class="critical-stock">
                                <td class="small text-start">
                                    <strong>{{ $product->product_name }}</strong>
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
                            <tr class="{{ $product->is_critical ? 'table-warning' : '' }} {{ !$product->is_active ? 'table-secondary' : '' }}">
                                <td class="small text-start">
                                    <strong>{{ $product->product_name }}</strong>
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
</style>
@endpush
@endsection