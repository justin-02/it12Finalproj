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
<div class="card shadow-sm">
    <div class="card-header py-2">
        <h5 class="card-title mb-0 small fw-bold">
            <i class="bi bi-list-ul"></i> Batch Records
        </h5>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-sm table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Batch Code</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Restock Date</th>
                        <th>Expiry Date</th>
                        <th>Supplier</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($batches as $batch)
                    <tr class="{{ \Carbon\Carbon::parse($batch->expiry_date)->isPast() ? 'table-danger' : (\Carbon\Carbon::parse($batch->expiry_date)->diffInDays(now()) <= 30 ? 'table-warning' : '') }}">
                        <td>{{ $loop->iteration + ($batches->currentPage() - 1) * $batches->perPage() }}</td>
                        <td>{{ $batch->batch_code }}</td>
                        <td>{{ $batch->product->product_name }} - {{ $batch->product->brand }}</td>
                        <td>{{ $batch->quantity }}</td>
                        <td>{{ \Carbon\Carbon::parse($batch->restock_date)->format('M d, Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($batch->expiry_date)->format('M d, Y') }}</td>
                        <td>{{ $batch->supplier ?? '-' }}</td>
                        <td>
                            @if(\Carbon\Carbon::parse($batch->expiry_date)->isPast())
                                <span class="badge bg-danger">Expired</span>
                            @elseif(\Carbon\Carbon::parse($batch->expiry_date)->diffInDays(now()) <= 30)
                                <span class="badge bg-warning text-dark">Expiring Soon</span>
                            @else
                                <span class="badge bg-success">Active</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
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
@endsection