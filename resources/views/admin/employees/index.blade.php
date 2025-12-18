@extends('layouts.app')

@section('title', 'Employees')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-person-badge"></i> Employees</h1>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row mb-3">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header py-2">
                <h5 class="card-title mb-0 fw-bold fs-5">Employee Filter</h5>
            </div>
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin.employees.index') }}" class="row gx-2 align-items-end">
                    <div class="col-md-3">
                        <label for="search" class="form-label small fw-bold">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               class="form-control form-control-sm" id="search" placeholder="Search employees...">
                    </div>
                    <div class="col-md-2">
                        <label for="role" class="form-label small fw-bold">Role</label>
                        <select name="role" class="form-select form-select-sm" id="role">
                            <option value="">All roles</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="cashier" {{ request('role') == 'cashier' ? 'selected' : '' }}>Cashier</option>
                            <option value="inventory" {{ request('role') == 'inventory' ? 'selected' : '' }}>Inventory</option>
                            <option value="helper" {{ request('role') == 'helper' ? 'selected' : '' }}>Helper</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label small fw-bold">Status</label>
                        <select name="status" class="form-select form-select-sm" id="status">
                            <option value="">Any status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            <button type="submit" class="btn btn-sm btn-primary px-3" 
                                    style="background: linear-gradient(135deg, #1E88E5, #1565C0); border: none; border-radius: 8px; font-weight: 500; white-space: nowrap;">
                                 Filter
                            </button>
                            <a href="{{ route('admin.employees.index') }}" class="btn btn-sm btn-secondary px-3"
                               style="background: linear-gradient(135deg, #6c757d, #495057); border: none; border-radius: 8px; font-weight: 500; white-space: nowrap;">
                                Reset
                            </a>
                            <button type="button" class="btn btn-sm btn-success px-3"
                                    style="background: linear-gradient(135deg, #1E88E5, #1E88E5); border: none; border-radius: 8px; font-weight: 500; white-space: nowrap;"
                                    data-bs-toggle="modal" data-bs-target="#createEmployeeModal">
                                <i class="bi bi-plus-lg me-1"></i>Create Employee
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        @if($employees->count())
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Employee</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                    <tr>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->email }}</td>
                        <td>{{ ucfirst($employee->role) }}</td>
                        <td>
                            <span class="badge bg-{{ $employee->is_active ? 'success' : 'secondary' }}">{{ $employee->is_active ? 'Active' : 'Inactive' }}</span>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editEmployeeModal{{ $employee->id }}">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            
                            <form action="{{ route('admin.employees.toggle-status', $employee) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-outline-warning">
    <i class="bi bi-toggle-on"></i> Toggle
</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $employees->withQueryString()->links() }}
        </div>
        @else
        <div class="text-center py-4 text-muted">
            No employees found.
        </div>
        @endif
    </div>
</div>

<!-- CREATE EMPLOYEE MODAL -->
<div class="modal fade" id="createEmployeeModal" tabindex="-1" aria-labelledby="createEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-card">
            <div class="modal-header modal-header-green">
                <h5 class="modal-title text-white fw-semibold">
                    <i class="bi bi-person-plus me-2"></i>Create New Employee
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.employees.store') }}" id="createEmployeeForm">
                @csrf
                <div class="modal-body modal-body-spaced">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="name" class="form-label-enhanced">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control-enhanced" id="name" name="name" required placeholder="Enter employee's full name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="email" class="form-label-enhanced">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control-enhanced" id="email" name="email" required placeholder="employee@example.com">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="phone" class="form-label-enhanced">Phone Number</label>
                                <input type="tel" class="form-control-enhanced" id="phone" name="phone" 
                                       placeholder="09123456789" pattern="[0-9]*" inputmode="numeric"
                                       maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                <small class="form-hint"><i class="bi bi-info-circle me-1"></i>Numbers only (e.g., 09123456789)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="role" class="form-label-enhanced">Role <span class="text-danger">*</span></label>
                                <select class="form-control-enhanced" id="role" name="role" required>
                                    <option value="">Select a role</option>
                                    <option value="cashier">Cashier</option>
                                    <option value="inventory">Inventory Manager</option>
                                    <option value="helper">Helper</option>
                                    <option value="admin">Administrator</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="create_password" class="form-label-enhanced">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control-enhanced" id="create_password" name="password" required placeholder="Enter password">
                                <small class="form-hint"><i class="bi bi-info-circle me-1"></i>Minimum 6 characters</small>
                                <div class="password-error text-danger small mt-1" style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="create_password_confirmation" class="form-label-enhanced">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control-enhanced" id="create_password_confirmation" name="password_confirmation" required placeholder="Confirm password">
                                <div class="confirm-error text-danger small mt-1" style="display: none;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="position" class="form-label-enhanced">Position</label>
                                <input type="text" class="form-control-enhanced" id="position" name="position" placeholder="e.g., Senior Cashier">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="department" class="form-label-enhanced">Department</label>
                                <input type="text" class="form-control-enhanced" id="department" name="department" placeholder="e.g., Sales Department">
                            </div>
                        </div>
                    </div>

                    <div class="form-group-enhanced">
                        <label for="hire_date" class="form-label-enhanced">Hire Date</label>
                        <input type="date" class="form-control-enhanced" id="hire_date" name="hire_date">
                        <small class="form-hint"><i class="bi bi-info-circle me-1"></i>Date when employee started working</small>
                    </div>
                </div>
                <div class="modal-footer modal-footer-enhanced">
                    <button type="button" class="btn btn-secondary-enhanced" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-green">
                        <i class="bi bi-person-plus me-2"></i>Create Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT EMPLOYEE MODALS (One for each employee) -->
@foreach($employees as $employee)
<div class="modal fade" id="editEmployeeModal{{ $employee->id }}" tabindex="-1" aria-labelledby="editEmployeeModalLabel{{ $employee->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-card">
            <div class="modal-header modal-header-green">
                <h5 class="modal-title text-white fw-semibold">
                    <i class="bi bi-person-gear me-2"></i>Edit Employee: {{ $employee->name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-body-spaced">
                <!-- Edit Employee Form -->
                <form method="POST" action="{{ route('admin.employees.update', $employee) }}" id="editForm{{ $employee->id }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="name{{ $employee->id }}" class="form-label-enhanced">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control-enhanced" id="name{{ $employee->id }}" 
                                       name="name" value="{{ $employee->name }}" required 
                                       placeholder="Enter employee's full name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="email{{ $employee->id }}" class="form-label-enhanced">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control-enhanced" id="email{{ $employee->id }}" 
                                       name="email" value="{{ $employee->email }}" required 
                                       placeholder="employee@example.com">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="phone{{ $employee->id }}" class="form-label-enhanced">Phone Number</label>
                                <input type="tel" class="form-control-enhanced phone-input" id="phone{{ $employee->id }}" 
                                       name="phone" value="{{ $employee->phone }}" 
                                       placeholder="09123456789" pattern="[0-9]*" inputmode="numeric"
                                       maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                <small class="form-hint"><i class="bi bi-info-circle me-1"></i>Numbers only (e.g., 09123456789)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="role{{ $employee->id }}" class="form-label-enhanced">Role <span class="text-danger">*</span></label>
                                <select class="form-control-enhanced" id="role{{ $employee->id }}" name="role" required>
                                    <option value="">Select a role</option>
                                    <option value="cashier" {{ $employee->role == 'cashier' ? 'selected' : '' }}>Cashier</option>
                                    <option value="inventory" {{ $employee->role == 'inventory' ? 'selected' : '' }}>Inventory Manager</option>
                                    <option value="helper" {{ $employee->role == 'helper' ? 'selected' : '' }}>Helper</option>
                                    <option value="admin" {{ $employee->role == 'admin' ? 'selected' : '' }}>Administrator</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="position{{ $employee->id }}" class="form-label-enhanced">Position</label>
                                <input type="text" class="form-control-enhanced" id="position{{ $employee->id }}" 
                                       name="position" value="{{ $employee->position }}" 
                                       placeholder="e.g., Senior Cashier">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="department{{ $employee->id }}" class="form-label-enhanced">Department</label>
                                <input type="text" class="form-control-enhanced" id="department{{ $employee->id }}" 
                                       name="department" value="{{ $employee->department }}" 
                                       placeholder="e.g., Sales Department">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="hire_date{{ $employee->id }}" class="form-label-enhanced">Hire Date</label>
                                <input type="date" class="form-control-enhanced" id="hire_date{{ $employee->id }}" 
                                       name="hire_date" value="{{ optional($employee->hire_date)->format('Y-m-d') }}">
                                <small class="form-hint"><i class="bi bi-info-circle me-1"></i>Date when employee started working</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <div class="form-check-enhanced mt-4">
                                    <input class="form-check-input-enhanced" type="checkbox" 
                                           id="is_active{{ $employee->id }}" name="is_active" value="1" 
                                           {{ $employee->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label-enhanced" for="is_active{{ $employee->id }}">Employee is active</label>
                                </div>
                                <small class="form-hint"><i class="bi bi-info-circle me-1"></i>Inactive employees won't be able to login.</small>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Reset Password Form -->
                <hr class="my-4">
                <h6 class="fw-semibold mb-3"><i class="bi bi-key me-2"></i>Reset Password</h6>
                <div class="alert alert-info mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Note:</strong> Password will be hashed and employee can login immediately.
                </div>
                <form method="POST" action="{{ route('admin.employees.reset-password', $employee) }}" id="resetPasswordForm{{ $employee->id }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="password{{ $employee->id }}" class="form-label-enhanced">New Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control-enhanced password-input" 
                                       id="password{{ $employee->id }}" name="password" required 
                                       placeholder="Enter new password" data-employee-id="{{ $employee->id }}">
                                <small class="form-hint"><i class="bi bi-info-circle me-1"></i>Minimum 6 characters</small>
                                <div class="password-error text-danger small mt-1" style="display: none;" id="passwordError{{ $employee->id }}"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label for="password_confirmation{{ $employee->id }}" class="form-label-enhanced">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control-enhanced confirm-input" 
                                       id="password_confirmation{{ $employee->id }}" name="password_confirmation" required 
                                       placeholder="Confirm new password" data-employee-id="{{ $employee->id }}">
                                <div class="confirm-error text-danger small mt-1" style="display: none;" id="confirmError{{ $employee->id }}"></div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer modal-footer-enhanced">
                <button type="button" class="btn btn-secondary-enhanced" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="resetPasswordForm{{ $employee->id }}" class="btn btn-warning btn-reset-password" data-employee-id="{{ $employee->id }}" onclick="return validatePasswordReset({{ $employee->id }})">
                    <i class="bi bi-key me-2"></i>Reset Password
                </button>
                <button type="submit" form="editForm{{ $employee->id }}" class="btn btn-primary-blue">
                    <i class="bi bi-check-lg me-2"></i>Update Employee
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach

<style>
    /* Modal Container Classes */
    .modal-card {
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        border: none;
        overflow: hidden;
    }

    .modal-header-green {
        background: linear-gradient(135deg, #2d5a3d 0%, #4a7c59 100%);
        border-bottom: none;
        padding: 1.25rem 1.5rem;
    }

    .modal-header-blue {
        background: linear-gradient(135deg, #1E88E5 0%, #1565C0 100%);
        border-bottom: none;
        padding: 1.25rem 1.5rem;
    }

    .modal-body-spaced {
        padding: 2rem 1.5rem;
    }

    .modal-footer-enhanced {
        background-color: #f8f9fa;
        border-top: 1px solid #e9ecef;
        padding: 1.25rem 1.5rem;
        border-bottom-left-radius: 16px;
        border-bottom-right-radius: 16px;
    }

    /* Form Component Classes */
    .form-group-enhanced {
        margin-bottom: 1.5rem;
        position: relative;
    }

    .form-label-enhanced {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        display: block;
    }

    .form-control-enhanced {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        background-color: #ffffff;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-control-enhanced:focus {
        border-color: #1E88E5;
        box-shadow: 0 0 0 3px rgba(30, 136, 229, 0.1);
        transform: translateY(-2px);
    }

    .form-control-enhanced.error {
        border-color: #dc3545;
        background-color: #fff5f5;
    }

    .form-control-enhanced.error:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
    }

    .form-hint {
        color: #718096;
        font-size: 0.8rem;
        margin-top: 0.5rem;
        display: block;
    }

    /* Button Classes */
    .btn-primary-green {
        background: linear-gradient(135deg, #2d5a3d 0%, #4a7c59 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .btn-primary-blue {
        background: linear-gradient(135deg, #1E88E5 0%, #1565C0 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .btn-warning {
        background: linear-gradient(135deg, #FFA726 0%, #FB8C00 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .btn-primary-green:hover,
    .btn-primary-blue:hover,
    .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        color: white;
    }

    .btn-secondary-enhanced {
        background-color: #ffffff;
        border: 2px solid #e2e8f0;
        color: #4a5568;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .btn-secondary-enhanced:hover {
        background-color: #f7fafc;
        border-color: #cbd5e0;
        transform: translateY(-2px);
        color: #4a5568;
    }

    /* Form Check Classes */
    .form-check-enhanced {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .form-check-input-enhanced {
        width: 1.2em;
        height: 1.2em;
        margin-right: 0.75rem;
        border: 2px solid #cbd5e0;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .form-check-input-enhanced:checked {
        background-color: #1E88E5;
        border-color: #1E88E5;
    }

    .form-check-label-enhanced {
        font-weight: 600;
        color: #2d3748;
        font-size: 0.9rem;
    }

    /* Error Message Styling */
    .password-error, .confirm-error {
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Modal Animations */
    .modal.fade .modal-card {
        transform: translateY(-30px) scale(0.95);
        opacity: 0;
        transition: all 0.3s ease;
    }

    .modal.show .modal-card {
        transform: translateY(0) scale(1);
        opacity: 1;
    }

    /* Responsive Design */
    @media (max-width: 576px) {
        .modal-body-spaced {
            padding: 1.5rem 1rem;
        }
        
        .modal-header-green,
        .modal-header-blue,
        .modal-footer-enhanced {
            padding: 1rem 1rem;
        }
        
        .form-control-enhanced {
            padding: 0.625rem 0.875rem;
        }
    }

    /* Focus States for Accessibility */
    .form-control-enhanced:focus-visible {
        outline: 2px solid #1E88E5;
        outline-offset: 2px;
    }

    .btn-primary-green:focus-visible,
    .btn-primary-blue:focus-visible,
    .btn-warning:focus-visible,
    .btn-secondary-enhanced:focus-visible {
        outline: 2px solid #1E88E5;
        outline-offset: 2px;
    }

    .form-check-input-enhanced:focus-visible {
        outline: 2px solid #1E88E5;
        outline-offset: 2px;
    }

    /* HR Styling */
    hr {
        border: none;
        height: 1px;
        background: linear-gradient(to right, transparent, #e2e8f0, transparent);
        margin: 2rem 0;
    }
    /* Match the Edit button hover behavior exactly */
.btn.btn-sm.btn-outline-warning:hover {
    background-color: #ffc107;
    border-color: #ffc107;
    color: white !important;
}

.btn.btn-sm.btn-outline-warning:hover i {
    color: white !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Restrict phone inputs to numbers only
    const phoneInputs = document.querySelectorAll('.phone-input, input[name="phone"]');
    phoneInputs.forEach(input => {
        // Prevent non-numeric input
        input.addEventListener('keydown', function(e) {
            // Allow: backspace, delete, tab, escape, enter
            if ([46, 8, 9, 27, 13].indexOf(e.keyCode) !== -1 ||
                // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (e.keyCode === 65 && e.ctrlKey === true) ||
                (e.keyCode === 67 && e.ctrlKey === true) ||
                (e.keyCode === 86 && e.ctrlKey === true) ||
                (e.keyCode === 88 && e.ctrlKey === true) ||
                // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            
            // Ensure it's a number
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
        
        // Format phone number as user types (optional)
        input.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            
            // Limit to 11 digits for Philippine numbers
            if (value.length > 11) {
                value = value.substring(0, 11);
            }
            
            this.value = value;
        });
        
        // Clear any non-numeric characters on blur
        input.addEventListener('blur', function() {
            this.value = this.value.replace(/\D/g, '');
        });
    });

    // Password validation for create employee modal
    const createPassword = document.getElementById('create_password');
    const createConfirmPassword = document.getElementById('create_password_confirmation');
    const createForm = document.getElementById('createEmployeeForm');
    
    function validateCreatePassword() {
        const password = createPassword.value;
        const confirmPassword = createConfirmPassword.value;
        let isValid = true;
        
        // Clear previous errors
        document.querySelector('.password-error').style.display = 'none';
        document.querySelector('.confirm-error').style.display = 'none';
        createPassword.classList.remove('error');
        createConfirmPassword.classList.remove('error');
        
        // Password validation (minimum 6 characters)
        if (password.length < 6) {
            document.querySelector('.password-error').textContent = 'Password must be at least 6 characters long';
            document.querySelector('.password-error').style.display = 'block';
            createPassword.classList.add('error');
            isValid = false;
        }
        
        // Confirm password validation
        if (confirmPassword && password !== confirmPassword) {
            document.querySelector('.confirm-error').textContent = 'Passwords do not match';
            document.querySelector('.confirm-error').style.display = 'block';
            createConfirmPassword.classList.add('error');
            isValid = false;
        }
        
        return isValid;
    }
    
    if (createPassword && createConfirmPassword) {
        createPassword.addEventListener('input', validateCreatePassword);
        createConfirmPassword.addEventListener('input', validateCreatePassword);
        
        createForm.addEventListener('submit', function(e) {
            if (!validateCreatePassword()) {
                e.preventDefault();
                return false;
            }
        });
    }

    // Clear validation errors when modal is closed
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function() {
            // Clear all error messages and styles
            this.querySelectorAll('.password-error, .confirm-error').forEach(error => {
                error.style.display = 'none';
                error.textContent = '';
            });
            
            this.querySelectorAll('.form-control-enhanced.error').forEach(input => {
                input.classList.remove('error');
            });
        });
    });
});

// Function to validate password reset
function validatePasswordReset(employeeId) {
    const passwordInput = document.getElementById(`password${employeeId}`);
    const confirmInput = document.getElementById(`password_confirmation${employeeId}`);
    const passwordError = document.getElementById(`passwordError${employeeId}`);
    const confirmError = document.getElementById(`confirmError${employeeId}`);
    const form = document.getElementById(`resetPasswordForm${employeeId}`);
    
    const password = passwordInput ? passwordInput.value : '';
    const confirmPassword = confirmInput ? confirmInput.value : '';
    let isValid = true;
    
    // Clear previous errors
    if (passwordError) {
        passwordError.style.display = 'none';
        passwordError.textContent = '';
    }
    if (confirmError) {
        confirmError.style.display = 'none';
        confirmError.textContent = '';
    }
    if (passwordInput) passwordInput.classList.remove('error');
    if (confirmInput) confirmInput.classList.remove('error');
    
    // Password validation (minimum 6 characters)
    if (password.length < 6) {
        if (passwordError) {
            passwordError.textContent = 'Password must be at least 6 characters long';
            passwordError.style.display = 'block';
        }
        if (passwordInput) passwordInput.classList.add('error');
        isValid = false;
    }
    
    // Confirm password validation
    if (confirmPassword && password !== confirmPassword) {
        if (confirmError) {
            confirmError.textContent = 'Passwords do not match';
            confirmError.style.display = 'block';
        }
        if (confirmInput) confirmInput.classList.add('error');
        isValid = false;
    }
    
    // If valid, submit the form
    if (isValid && form) {
        // Submit the form - this will use your controller's Hash::make()
        form.submit();
        // Close the modal after a short delay
        setTimeout(() => {
            const modal = bootstrap.Modal.getInstance(document.getElementById(`editEmployeeModal${employeeId}`));
            if (modal) modal.hide();
        }, 500);
    }
    
    return false; // Prevent default button behavior
}

// Real-time validation for password inputs
document.querySelectorAll('.password-input, .confirm-input').forEach(input => {
    input.addEventListener('input', function() {
        const employeeId = this.dataset.employeeId;
        if (employeeId) {
            // Just clear errors on input, validation will happen on submit
            const passwordError = document.getElementById(`passwordError${employeeId}`);
            const confirmError = document.getElementById(`confirmError${employeeId}`);
            
            if (passwordError) {
                passwordError.style.display = 'none';
                passwordError.textContent = '';
            }
            if (confirmError) {
                confirmError.style.display = 'none';
                confirmError.textContent = '';
            }
            
            this.classList.remove('error');
        }
    });
});

</script>
@endsection