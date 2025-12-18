@extends('layouts.app')

@section('title', 'Edit Employee')

@section('content')
<div class="page-header">
    <h1>Edit Employee</h1>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.employees.update', $employee) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input name="name" value="{{ $employee->name }}" class="form-control" required />
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input name="email" value="{{ $employee->email }}" class="form-control" required type="email" />
            </div>
            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input name="phone" value="{{ $employee->phone }}" class="form-control" />
            </div>
            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-select" required>
                    <option value="cashier" {{ $employee->role=='cashier' ? 'selected' : '' }}>Cashier</option>
                    <option value="inventory" {{ $employee->role=='inventory' ? 'selected' : '' }}>Inventory</option>
                    <option value="helper" {{ $employee->role=='helper' ? 'selected' : '' }}>Helper</option>
                    <option value="admin" {{ $employee->role=='admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Hire Date</label>
                <input name="hire_date" value="{{ optional($employee->hire_date)->format('Y-m-d') }}" class="form-control" type="date" />
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="is_active" value="1" class="form-check-input" {{ $employee->is_active ? 'checked' : '' }}>
                <label class="form-check-label">Active</label>
            </div>
            <button class="btn btn-primary">Update</button>
        </form>

        <hr />
        <h5>Reset Password</h5>
        <form method="POST" action="{{ route('admin.employees.reset-password', $employee) }}">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">New Password</label>
                    <input name="password" class="form-control" type="password" required />
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Confirm</label>
                    <input name="password_confirmation" class="form-control" type="password" required />
                </div>
            </div>
            <button class="btn btn-warning">Reset Password</button>
        </form>
    </div>
</div>
@endsection
