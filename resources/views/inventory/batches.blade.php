@extends('layouts.app')

@section('title', 'Batch List - Inventory Management')

@section('content')
<div class="page-header mb-3">
    <h1 class="h2">
        <i class="bi bi-archive"></i> Batch List
    </h1>
    <form class="row g-2 align-items-center" method="GET" action="">
        <div class="col-auto">
            <select name="product_id" class="form-select form-select-sm">
                <option value="">All Products</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                        {{ $product->product_name }} - {{ $product->brand }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
        </div>
    </form>
</div>
<div class="col-12">
    <div class="card shadow-sm animate__animated animate__fadeIn">
        <div class="card-header py-3 bg-light border-bottom">
            <h5 class="card-title mb-0 fw-bold text-dark">Batch Records</h5>
        </div>
        <div class="card-body p-3">
            {{-- Show warning if no pivot table --}}
            @if(!Schema::hasTable('batch_product'))
                <div class="alert alert-warning mb-3">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Note:</strong> Batch product information is not available. 
                    Please run migrations to enable batch-product relationships.
                </div>
            @endif
            
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Batch Code</th>
                            <th>Products</th>
                            <th>Total Qty</th>
                            <th>Restock Date</th>
                            <th>Expiry Date</th>
                            <th>Supplier</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($batches->count() > 0)
                            @foreach($batches as $batch)
                            <tr class="{{ $batch->expiry_date && \Carbon\Carbon::parse($batch->expiry_date)->isPast() ? 'table-danger' : ($batch->expiry_date && \Carbon\Carbon::parse($batch->expiry_date)->diffInDays(now()) <= 30 ? 'table-warning' : '') }}">
                                <td>{{ $loop->iteration + ($batches->currentPage() - 1) * $batches->perPage() }}</td>
                                <td>{{ $batch->batch_code }}</td>
                                <td>
                                    @if(isset($batch->products) && is_iterable($batch->products) && count($batch->products) > 0)
                                        @foreach($batch->products as $product)
                                            <div class="small">
                                                {{ $product->product_name ?? 'Unknown' }} - {{ $product->brand ?? 'N/A' }}
                                                <span class="text-muted">(x{{ $product->pivot->quantity ?? 'N/A' }})</span>
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="text-muted">
                                            @if(!Schema::hasTable('batch_product'))
                                                N/A (Run migrations)
                                            @else
                                                No products attached
                                            @endif
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if(isset($batch->total_quantity) && $batch->total_quantity > 0)
                                        {{ number_format($batch->total_quantity, 0) }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $batch->restock_date ? \Carbon\Carbon::parse($batch->restock_date)->format('M d, Y') : 'N/A' }}</td>
                                <td>{{ $batch->expiry_date ? \Carbon\Carbon::parse($batch->expiry_date)->format('M d, Y') : 'N/A' }}</td>
                                <td>{{ $batch->supplier ?? '-' }}</td>
                                <td>
                                    @if($batch->expiry_date)
                                        @if(\Carbon\Carbon::parse($batch->expiry_date)->isPast())
                                            <span class="badge bg-danger">Expired</span>
                                        @elseif(\Carbon\Carbon::parse($batch->expiry_date)->diffInDays(now()) <= 30)
                                            <span class="badge bg-warning text-dark">Expiring Soon</span>
                                        @else
                                            <span class="badge bg-success">Active</span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">No Expiry</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No batches found. Create your first batch to get started.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if($batches->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    Showing {{ $batches->firstItem() }} to {{ $batches->lastItem() }} of {{ $batches->total() }} entries
                </div>
                <nav>
                    {{ $batches->links('pagination::bootstrap-5') }}
                </nav>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add any JavaScript for batch management here
    document.addEventListener('DOMContentLoaded', function() {
        // Example: Confirm before deleting a batch
        document.querySelectorAll('.btn-delete-batch').forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this batch?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endpush