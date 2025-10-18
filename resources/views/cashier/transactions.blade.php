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
                <h5 class="card-title">All Completed Transactions</h5>
            </div>
            <div class="card-body">
                @if($transactions->count() > 0)
                    <div class="table-responsive" style="font-size: 0.875rem;">
                        <table class="table table-striped table-sm" id="transactionsTable">
                            <thead>
                                <tr>
                                    <th width="8%">Order #</th>
                                    <th width="12%">Helper</th>
                                    <th width="12%">Cashier</th>
                                    <th width="15%">Items</th>
                                    <th class="text-end" width="10%">Total Amount</th>
                                    <th class="text-end" width="12%">Cash Received</th>
                                    <th class="text-end" width="8%">Change</th>
                                    <th class="text-end" width="15%">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->order_number }}</td>
                                    <td>{{ $transaction->helper->name }}</td>
                                    <td>{{ $transaction->cashier->name }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info py-1" type="button" data-bs-toggle="collapse" data-bs-target="#items{{ $transaction->id }}">
                                            {{ $transaction->items->count() }} items
                                        </button>
                                        <div class="collapse mt-2" id="items{{ $transaction->id }}">
                                            <div class="card card-body p-2" style="font-size: 0.8rem;">
                                                @foreach($transaction->items as $item)
                                                <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                                    <span class="text-truncate" style="max-width: 120px;">{{ $item->product->product_name }}</span>
                                                    <span class="text-nowrap">
                                                        {{ $item->quantity }} 
                                                        <span class="badge bg-secondary">{{ $item->unit }}</span>
                                                        @ ₱{{ number_format($item->price, 2) }}
                                                    </span>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-nowrap text-end">₱{{ number_format($transaction->total_amount, 2) }}</td>
                                    <td class="text-nowrap text-end">₱{{ number_format($transaction->cash_received, 2) }}</td>
                                    <td class="text-nowrap text-end">₱{{ number_format($transaction->change, 2) }}</td>
                                    <td class="text-end">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
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

@push('scripts')
<script>
    $(document).ready(function() {
        $('#transactionsTable').DataTable({
            "order": [[7, "desc"]],
            "pageLength": 25,
            "responsive": true,
            "autoWidth": false,
            "language": {
                "emptyTable": "No transactions available"
            }
        });
    });
</script>
@endpush
@endsection