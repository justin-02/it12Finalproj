<style>
/* Ensure vertically-centered modal stays within the viewport and its content scrolls when large */
.modal.show .modal-dialog.modal-dialog-centered {
    display: flex;
    align-items: center;
    min-height: calc(100vh - 2rem);
    padding: 1rem 0;
}

/* Constrain modal content height so it never exceeds viewport */
.modal-dialog .modal-content {
    max-height: calc(100vh - 3.5rem);
    overflow: hidden;
}

/* Make the modal body scroll when content is long (keeps header/footer visible).
   Disable scrolling on the inner `.modal-scrollable` wrapper to avoid duplicate
   vertical scrollbars (left). Keep scrolling only on the modal body (right scrollbar).
*/
.modal-dialog .modal-body {
    max-height: calc(100vh - 420px);
    overflow-y: auto;
}
.modal-scrollable {
    /* allow content to expand but do not show its own scrollbar */
    max-height: none !important;
    overflow-y: visible !important;
}

/* Hide horizontal scrollbars inside the modal but keep vertical scrolling */
.modal-dialog .modal-body,
.modal-scrollable,
.modal-dialog .table-responsive {
    overflow-x: hidden !important;
}

/* Ensure tables fit within modal width */
.modal-dialog .table-responsive table {
    width: 100%;
    table-layout: auto;
}

/* Hide visual scrollbar UI inside the modal while keeping scrolling functional */
.modal-scrollable,
.modal-dialog .modal-body,
.modal-dialog .table-responsive {
    -ms-overflow-style: none; /* IE/Edge */
    scrollbar-width: none; /* Firefox */
}
.modal-scrollable::-webkit-scrollbar,
.modal-dialog .modal-body::-webkit-scrollbar,
.modal-dialog .table-responsive::-webkit-scrollbar {
    display: none; /* Safari and Chrome */
    width: 0; height: 0;
}

/* On small screens, align to top and allow natural scrolling */
@media (max-width: 768px) {
    .modal.show .modal-dialog.modal-dialog-centered {
        align-items: flex-start;
        padding-top: 1rem;
    }
    .modal-dialog .modal-content {
        max-height: calc(100vh - 1.5rem);
    }
    .modal-dialog .modal-body,
    .modal-scrollable {
        max-height: calc(100vh - 200px);
    }
}

/* Ensure the employee details modal shows a visible vertical scrollbar and scrolls its body */
#employeeDetailsModal .modal-body {
    max-height: calc(80vh - 200px);
    overflow-y: auto;
}
#employeeDetailsModal .modal-scrollable {
    max-height: calc(80vh - 200px);
    overflow-y: auto;
    padding-right: 8px;
}

/* Restore visible scrollbar styling only for the employee details modal */
#employeeDetailsModal .modal-scrollable,
#employeeDetailsModal .modal-dialog .modal-body,
#employeeDetailsModal .modal-dialog .table-responsive {
    -ms-overflow-style: auto; /* IE/Edge */
    scrollbar-width: auto; /* Firefox */
}
#employeeDetailsModal .modal-scrollable::-webkit-scrollbar,
#employeeDetailsModal .modal-dialog .modal-body::-webkit-scrollbar,
#employeeDetailsModal .modal-dialog .table-responsive::-webkit-scrollbar {
    display: block;
    width: 8px; height: 8px;
}
#employeeDetailsModal .modal-scrollable::-webkit-scrollbar-thumb,
#employeeDetailsModal .modal-dialog .modal-body::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.12);
    border-radius: 6px;
}

/* Firefox scrollbar color (thumb then track) */
#employeeDetailsModal .modal-dialog .modal-body {
    scrollbar-color: rgba(0,0,0,0.12) transparent;
}

/* Responsive avatar sizing inside the details header */
.employee-details .avatar-xl { width:70px; height:70px; }
@media (max-width: 576px) {
    .employee-details .avatar-xl { width:54px; height:54px; font-size:1.05rem; }
}
</style>

<div class="employee-details">
    <div class="row mb-3 align-items-center">
        <div class="col-12">
            <div class="d-flex align-items-center p-3 rounded" style="background: linear-gradient(90deg, rgba(45,90,61,0.06), rgba(212,165,116,0.02));">
                <div class="me-3">
                    <div class="avatar-xl bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width:70px; height:70px;">
                        <span class="text-white fw-bold fs-3">{{ strtoupper(substr($employee->name, 0, 1)) }}</span>
                    </div>
                </div>

                <div class="flex-grow-1">
                    <h4 class="mb-0">{{ $employee->name }} <small class="text-muted">@if($employee->employee_code) • {{ $employee->employee_code }} @endif</small></h4>
                    <div class="mt-1 d-flex flex-wrap align-items-center gap-2">
                        <span class="badge bg-secondary">{{ ucfirst($employee->role) }}</span>
                        @if($employee->department)
                            <span class="badge bg-info text-dark">{{ $employee->department }}</span>
                        @endif
                        @if($employee->position)
                            <small class="text-muted">{{ $employee->position }}</small>
                        @endif
                    </div>
                    <div class="mt-2 d-flex align-items-center text-muted small">
                        <i class="bi bi-envelope me-2"></i>
                        <a href="mailto:{{ $employee->email }}" class="me-3">{{ $employee->email }}</a>
                        @php $isOnline = $employee->last_seen && $employee->last_seen->diffInMinutes(now()) < 5; @endphp
                        <i class="bi bi-circle-fill me-1" style="color: {{ $isOnline ? '#28a745' : '#6c757d' }}; font-size: .6rem;"></i>
                        <span class="me-2">{{ $isOnline ? 'Online' : 'Offline' }}</span>
                        @if($employee->last_seen)
                            <small class="text-muted">• last seen {{ $employee->last_seen->diffForHumans() }}</small>
                        @endif
                    </div>
                </div>

                <div class="text-end ms-3">
                    <small class="text-muted">Joined</small>
                    <div class="fw-semibold">{{ optional($employee->hire_date)->format('M d, Y') ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Make the rest of the modal content scrollable to prevent overlap when lists are long -->
    <div class="modal-scrollable" style="max-height: calc(100vh - 240px); overflow-y: auto; padding-right: 8px;">
    <div class="row">
        <div class="col-md-6">
            <h6 class="text-muted">Contact</h6>
            <p class="mb-1"><strong>Email:</strong> <br><span class="text-muted">{{ $employee->email }}</span></p>
            <p class="mb-1"><strong>Phone:</strong> <br><span class="text-muted">{{ $employee->phone ?? 'N/A' }}</span></p>
            <p class="mb-1"><strong>Address:</strong> <br><span class="text-muted">{{ $employee->address ?? 'N/A' }}</span></p>
        </div>

        <div class="col-md-6">
            <h6 class="text-muted">Employment</h6>
            <p class="mb-1"><strong>Department:</strong> <br><span class="text-muted">{{ $employee->department ?? 'N/A' }}</span></p>
            <p class="mb-1"><strong>Position:</strong> <br><span class="text-muted">{{ $employee->position ?? 'N/A' }}</span></p>
            <p class="mb-1"><strong>Hire Date:</strong> <br><span class="text-muted">{{ optional($employee->hire_date)->format('M d, Y') ?? 'N/A' }}</span></p>
            <p class="mb-0"><strong>Status:</strong>
                @php $isOnline = $employee->last_seen && $employee->last_seen->diffInMinutes(now()) < 5; @endphp
                <span class="badge bg-{{ $isOnline ? 'success' : 'secondary' }} ms-2">{{ $isOnline ? 'Online' : 'Offline' }}</span>
            </p>
        </div>
    </div>

    <!-- horizontal rule removed for cleaner UI -->

    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3">
            <div class="card bg-light h-100">
                <div class="card-body text-center py-2">
                    <div class="small text-muted">Today's Sales</div>
                    <div class="h6 mb-0 text-success">₱{{ number_format($employee->today_sales ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card bg-light h-100">
                <div class="card-body text-center py-2">
                    <div class="small text-muted">Today's Txns</div>
                    <div class="h6 mb-0 text-primary">{{ $employee->today_transactions ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card bg-light h-100">
                <div class="card-body text-center py-2">
                    <div class="small text-muted">Total Txns</div>
                    <div class="h6 mb-0 text-info">{{ $employee->total_sales_count ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card bg-light h-100">
                <div class="card-body text-center py-2">
                    <div class="small text-muted">Avg. Transaction</div>
                    <div class="h6 mb-0 text-warning">@if($employee->today_transactions > 0) ₱{{ number_format(($employee->today_sales ?? 0) / ($employee->today_transactions ?? 1), 2) }} @else ₱0.00 @endif</div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($employee->recent_activities) && $employee->recent_activities->count() > 0)
        <h6>Recent Activity</h6>
        <ul class="list-group mb-3">
            @foreach($employee->recent_activities as $act)
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="ms-2 me-auto small">
                        <div class="fw-bold">{{ $act->title ?? $act->action }}</div>
                        <div class="text-muted">{{ $act->meta ?? $act->description ?? '' }}</div>
                    </div>
                    <small class="text-muted">{{ optional($act->created_at)->diffForHumans() }}</small>
                </li>
            @endforeach
        </ul>
    @endif

    @if(isset($employee->sales) && $employee->sales->count() > 0)
        <h6>Recent Transactions</h6>
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employee->sales as $sale)
                        <tr>
                            <td>{{ $sale->order_number }}</td>
                            <td class="text-success">₱{{ number_format($sale->total_amount, 2) }}</td>
                            <td>{{ $sale->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>
</div>