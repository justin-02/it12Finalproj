@extends('layouts.app')

@section('title', 'Employee Monitoring')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-people-fill"></i> Employee Monitoring</h1>
</div>

<style>
.employee-summary-card {
    border: none;
    border-radius: 14px;
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
    box-shadow: 0 4px 12px rgba(214, 245, 195, 0.2);
    border: 1px solid rgba(214, 245, 195, 0.3);
    height: 120px;
}

.employee-summary-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 18px rgba(214, 245, 195, 0.3);
    background: linear-gradient(135deg, #F0FFE0 0%, #DFF8CF 100%);
}

.employee-summary-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #8BC34A, #4CAF50);
}

.employee-summary-card .card-body {
    position: relative;
    z-index: 1;
    padding: 1rem !important;
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 100%;
}

.employee-summary-card .card-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}

.employee-summary-card .text-content {
    flex: 1;
    min-width: 0;
}

.employee-summary-card .card-title {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    color: #1B5E20;
    margin-bottom: 0.4rem;
    opacity: 0.95;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.employee-summary-card .card-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: #1B5E20;
    margin: 0;
    line-height: 1.2;
    letter-spacing: -0.3px;
}

.employee-summary-card .currency {
    color: #1B5E20;
    font-weight: 700;
    font-size: 1.2rem;
    opacity: 0.9;
}

.employee-summary-card .icon-wrapper {
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

.employee-summary-card:hover .icon-wrapper {
    background: rgba(255, 255, 255, 0.4);
    transform: scale(1.08);
    border-color: rgba(255, 255, 255, 0.6);
}

.employee-summary-card .icon-wrapper i {
    font-size: 1.3rem;
    color: #2E7D32;
}

/* All employee cards use the same green background */
.employee-summary-card.total-employees,
.employee-summary-card.today-sales,
.employee-summary-card.today-transactions,
.employee-summary-card.active-now {
    background: linear-gradient(135deg, #E8FFD7 0%, #D6F5C3 100%);
}
</style>

<div class="row mb-3">
    <!-- Total Employees Card -->
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card employee-summary-card total-employees h-100">
            <div class="card-body">
                <div class="card-content">
                    <div class="text-content">
                        <div class="card-title">Total Employees</div>
                        <div class="card-value">{{ $employees->count() }}</div>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Total Sales Card -->
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card employee-summary-card today-sales h-100">
            <div class="card-body">
                <div class="card-content">
                    <div class="text-content">
                        <div class="card-title">Today's Total Sales</div>
                        <div class="card-value">
                            <span class="currency">₱</span>{{ number_format($employees->sum('today_sales'), 2) }}
                        </div>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Transactions Card -->
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card employee-summary-card today-transactions h-100">
            <div class="card-body">
                <div class="card-content">
                    <div class="text-content">
                        <div class="card-title">Today's Transactions</div>
                        <div class="card-value">{{ $employees->sum('today_transactions') }}</div>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Now Card -->
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card employee-summary-card active-now h-100">
            <div class="card-body">
                <div class="card-content">
                    <div class="text-content">
                        <div class="card-title">Active Now</div>
                        <div class="card-value">{{ $employees->where('last_seen', '>=', now()->subMinutes(5))->count() }}</div>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Employee List -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm animate__animated animate__fadeIn">
            <div class="card-header py-3 bg-light border-bottom">
                <h5 class="card-title mb-0 fw-bold text-dark">Employee List</h5>
            </div>
    <div class="card-body">
        @if($employees->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="px-3 py-2 text-start">Employee</th>
                            <th class="px-3 py-2 text-start">Role</th>
                            <th class="px-3 py-2 text-center">Status</th>
                            <th class="px-3 py-2 text-center">Last Activity</th>
                            <th class="px-3 py-2 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                        <tr>
                            <td class="px-3 py-2 text-start">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                        <span class="text-white fw-bold">
                                            {{ strtoupper(substr($employee->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $employee->name }}</h6>
                                        <small class="text-muted">{{ $employee->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-2 text-start">
                                <span class="badge bg-secondary">{{ ucfirst($employee->role) }}</span>
                            </td>
                            <td class="px-3 py-2 text-center">
                                @php
                                    $isOnline = $employee->last_seen && $employee->last_seen->diffInMinutes(now()) < 5;
                                @endphp
                                <span class="badge bg-{{ $isOnline ? 'success' : 'secondary' }}">
                                    <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>
                                    {{ $isOnline ? 'Online' : 'Offline' }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <small class="text-muted">
                                    @if($employee->last_seen)
                                        {{ $employee->last_seen->diffForHumans() }}
                                    @else
                                        Never
                                    @endif
                                </small>
                            </td>
                            <td class="px-3 py-2 text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#employeeDetailsModal"
                                            onclick="loadEmployeeDetails({{ $employee->id }})">
                                        <i class="bi bi-eye"></i> Details
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#employeePerformanceModal"
                                            onclick="loadEmployeePerformance({{ $employee->id }})">
                                        <i class="bi bi-graph-up"></i> Performance
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center text-muted py-4">
                <i class="bi bi-people display-4"></i>
                <p class="mt-2">No employees found.</p>
            </div>
        @endif
    </div>
</div>

<!-- Employee Details Modal -->
<div class="modal fade" id="employeeDetailsModal" tabindex="-1" aria-labelledby="employeeDetailsLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-custom">
        <div class="modal-content">
           <div class="modal-header" style="background-color: #2d5a3d; color: white;">
    <h5 class="modal-title" id="employeePerformanceLabel">Employee Details</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
            <div class="modal-body" id="employeeDetailsContent">
                <!-- AJAX content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Employee Performance Modal -->
<div class="modal fade" id="employeePerformanceModal" tabindex="-1" aria-labelledby="employeePerformanceLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
           <div class="modal-header" style="background-color: #2d5a3d; color: white;">
    <h5 class="modal-title" id="employeePerformanceLabel">Employee Performance</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
            <div class="modal-body" id="employeePerformanceContent">
                <!-- AJAX content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<style>
    /* When a modal is active we add `.wrapper.blurred` to disable interaction
       with background content but we DO NOT visually blur the whole page here
       because the frosted effect is provided by the backdrop's
       `backdrop-filter`. This avoids double-blurring which masks the effect. */
    .wrapper.blurred {
        transition: opacity 200ms ease;
        pointer-events: none;
        user-select: none;
        opacity: 0.95; /* slight dim only, not a blur */
    }

    /* Keep modal interactive and on top */
    .modal {
        z-index: 10550;
    }

    /* Slightly dim backdrop so modal stands out */
    .modal-backdrop.show {
        opacity: 0.45;
    }

    /* Apply a backdrop-filter (blur) only when the performance modal is shown.
       We toggle the class `.performance-blur` on the generated backdrop element
       so other modals are unaffected. */
    .modal-backdrop.performance-blur {
        -webkit-backdrop-filter: blur(6px);
        backdrop-filter: blur(6px);
        background-color: rgba(0, 0, 0, 0.35);
    }

    /* Constrain details modal width and ensure it's centered and visually distinct */
    .modal-dialog-custom {
        max-width: 920px;
        margin: 1.75rem auto;
    }

    .modal-content.shadow-lg {
        border-radius: 12px;
        overflow: hidden;
    }
</style>

<script>
function loadEmployeeDetails(employeeId) {
    $('#employeeDetailsContent').html(`
        <div class="text-center py-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted small">Loading employee details...</p>
        </div>
    `);

    $.ajax({
        url: `/admin/employees/${employeeId}/details`,
        method: 'GET',
        success: function(response) {
            $('#employeeDetailsContent').html(response);
        },
        error: function() {
            $('#employeeDetailsContent').html(`
                <div class="alert alert-danger small">Failed to load employee details. Please try again.</div>
            `);
        }
    });
}

function loadEmployeePerformance(employeeId) {
    $('#employeePerformanceContent').html(`
        <div class="text-center py-3">
            <div class="spinner-border text-info" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted small">Loading performance data...</p>
        </div>
    `);

    $.ajax({
        url: `/admin/employees/${employeeId}/performance`,
        method: 'GET',
        success: function(response) {
            $('#employeePerformanceContent').html(response);
        },
        error: function() {
            $('#employeePerformanceContent').html(`
                <div class="alert alert-danger small">Failed to load performance data. Please try again.</div>
            `);
        }
    });
}

// Blur page wrapper and apply backdrop filter while performance modal is visible
$(function(){
    var perfModal = $('#employeePerformanceModal');
    if (perfModal && perfModal.length) {
        // When modal is shown, blur page content and add the backdrop blur class
        perfModal.off('show.bs.modal.blur').on('show.bs.modal.blur', function(){
            document.querySelector('.wrapper')?.classList.add('blurred');

                // Bootstrap inserts the backdrop element; ensure it exists then add our class
                // Target the last backdrop in case multiple backdrops exist and avoid
                // accidentally touching other modal backdrops.
                setTimeout(function(){
                    $('.modal-backdrop').last().addClass('performance-blur');
                }, 0);
        });

        // When modal is hidden, remove the page blur and backdrop blur
        perfModal.off('hidden.bs.modal.blur').on('hidden.bs.modal.blur', function(){
            document.querySelector('.wrapper')?.classList.remove('blurred');
            $('.modal-backdrop').last().removeClass('performance-blur');
        });
    }
});
</script>
@endsection