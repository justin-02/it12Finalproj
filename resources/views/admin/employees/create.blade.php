@extends('layouts.app')

@section('title', 'Create Employee')

@section('content')
<div class="page-header">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-person-plus me-2"></i>Create Employee</h1>
</div>

<div class="card modal-card">
    <div class="card-header modal-header-green py-3">
        <h5 class="card-title mb-0 text-white fw-semibold">
            <i class="bi bi-person-plus me-2"></i>New Employee Information
        </h5>
    </div>
    <div class="card-body modal-body-spaced">
        <form method="POST" action="{{ route('admin.employees.store') }}">
            @csrf
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="form-group-enhanced">
                        <label for="name" class="form-label-enhanced">Full Name <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" class="form-control-enhanced" required 
                               placeholder="Enter employee's full name" autocomplete="off">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group-enhanced">
                        <label for="email" class="form-label-enhanced">Email Address <span class="text-danger">*</span></label>
                        <input type="email" id="email" name="email" class="form-control-enhanced" required 
                               placeholder="Enter Email Address" autocomplete="off">
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="form-group-enhanced">
                        <label for="phone" class="form-label-enhanced">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control-enhanced" 
                               placeholder="+63 912 345 6789" autocomplete="off">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group-enhanced">
                        <label for="role" class="form-label-enhanced">Role <span class="text-danger">*</span></label>
                        <select id="role" name="role" class="form-control-enhanced" required>
                            <option value="">Select a role</option>
                            <option value="cashier">Cashier</option>
                            <option value="inventory">Inventory Manager</option>
                            <option value="helper">Helper</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="form-group-enhanced">
                        <label for="password" class="form-label-enhanced">Password <span class="text-danger">*</span></label>
                        <div class="password-input-wrapper">
                            <input type="password" id="password" name="password" class="form-control-enhanced" required 
                                   placeholder="Enter password" autocomplete="new-password">
                            <button type="button" class="password-toggle-btn" onclick="togglePassword('password')">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <small class="form-hint"><i class="bi bi-info-circle me-1"></i>Minimum 8 characters with letters and numbers</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group-enhanced">
                        <label for="password_confirmation" class="form-label-enhanced">Confirm Password <span class="text-danger">*</span></label>
                        <div class="password-input-wrapper">
                            <input type="password" id="password_confirmation" name="password_confirmation" 
                                   class="form-control-enhanced" required placeholder="Confirm password" autocomplete="new-password">
                            <button type="button" class="password-toggle-btn" onclick="togglePassword('password_confirmation')">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="form-group-enhanced">
                    <label for="hire_date" class="form-label-enhanced">Hire Date</label>
                    <input type="date" id="hire_date" name="hire_date" class="form-control-enhanced" autocomplete="off">
                    <small class="form-hint"><i class="bi bi-info-circle me-1"></i>Date when employee started working</small>
                </div>
            </div>

            <div class="modal-footer-enhanced mt-4">
                <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary-enhanced">
                    <i class="bi bi-arrow-left me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary-green">
                    <i class="bi bi-person-plus me-2"></i>Create Employee
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Page Header */
    .page-header {
        margin-bottom: 2rem;
    }

    .page-header h1 {
        margin: 0;
        font-size: 1.75rem;
    }

    /* Card Container */
    .modal-card {
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        border: none;
        overflow: hidden;
        background-color: #ffffff;
    }

    .modal-header-green {
        background: linear-gradient(135deg, #2d5a3d 0%, #4a7c59 100%);
        border-bottom: none;
        padding: 1.25rem 1.5rem;
    }

    .modal-body-spaced {
        padding: 2rem 1.5rem;
    }

    .modal-footer-enhanced {
        background-color: #f8f9fa;
        border-top: 1px solid #e9ecef;
        padding: 1.5rem 0 0;
        margin-top: 1rem;
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
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
        border-color: #4a7c59;
        box-shadow: 0 0 0 3px rgba(74, 124, 89, 0.1);
        transform: translateY(-2px);
    }

    .form-hint {
        color: #718096;
        font-size: 0.8rem;
        margin-top: 0.5rem;
        display: block;
    }

    /* Password Input Wrapper */
    .password-input-wrapper {
        position: relative;
    }

    .password-toggle-btn {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #718096;
        cursor: pointer;
        padding: 0.25rem;
        z-index: 10;
    }

    .password-toggle-btn:hover {
        color: #4a7c59;
    }

    /* Add extra padding to password inputs for the toggle button */
    .password-input-wrapper .form-control-enhanced {
        padding-right: 3rem;
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
        display: inline-flex;
        align-items: center;
    }

    .btn-primary-green:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(45, 90, 61, 0.3);
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
        display: inline-flex;
        align-items: center;
    }

    .btn-secondary-enhanced:hover {
        background-color: #f7fafc;
        border-color: #cbd5e0;
        transform: translateY(-2px);
        color: #4a5568;
    }

    /* Focus States for Accessibility */
    .form-control-enhanced:focus-visible {
        outline: 2px solid #4a7c59;
        outline-offset: 2px;
    }

    .btn-primary-green:focus-visible,
    .btn-secondary-enhanced:focus-visible {
        outline: 2px solid #4a7c59;
        outline-offset: 2px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .modal-body-spaced {
            padding: 1.5rem 1rem;
        }
        
        .modal-header-green {
            padding: 1rem 1rem;
        }
        
        .form-control-enhanced {
            padding: 0.625rem 0.875rem;
        }

        .modal-footer-enhanced {
            flex-direction: column;
        }

        .modal-footer-enhanced .btn {
            width: 100%;
        }
    }

    /* Custom Select Styling */
    .form-control-enhanced[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(0.4);
        cursor: pointer;
        padding: 0.25rem;
    }

    .form-control-enhanced[type="date"]::-webkit-calendar-picker-indicator:hover {
        filter: invert(0.6);
    }
</style>

<script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const button = input.nextElementSibling;
        const icon = button.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    // Disable autocomplete and autofill for all inputs
    document.addEventListener('DOMContentLoaded', function() {
        // Prevent browser autofill
        document.querySelectorAll('input').forEach(input => {
            input.setAttribute('autocomplete', 'off');
            input.setAttribute('autocorrect', 'off');
            input.setAttribute('autocapitalize', 'off');
            input.setAttribute('spellcheck', 'false');
        });

        // Specifically for password fields
        document.getElementById('password').setAttribute('autocomplete', 'new-password');
        document.getElementById('password_confirmation').setAttribute('autocomplete', 'new-password');
        
        // Clear any pre-filled values
        document.getElementById('email').value = '';
        document.getElementById('password').value = '';
        document.getElementById('password_confirmation').value = '';
    });
</script>
@endsection