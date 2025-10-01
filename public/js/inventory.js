// AgriSupply Inventory Management JavaScript
// This file contains common JavaScript functions for inventory management

$(document).ready(function() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Initialize popovers
    $('[data-bs-toggle="popover"]').popover();
    
    // Auto-hide alerts after 5 seconds
    $('.alert').each(function() {
        const alert = $(this);
        if (!alert.hasClass('alert-danger')) {
            setTimeout(function() {
                alert.fadeOut();
            }, 5000);
        }
    });
    
    // Confirm delete actions
    $('.btn-delete').on('click', function(e) {
        if (!confirm('Are you sure you want to delete this item?')) {
            e.preventDefault();
        }
    });
    
    // Form validation
    $('form').on('submit', function() {
        const requiredFields = $(this).find('[required]');
        let isValid = true;
        
        requiredFields.each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        return isValid;
    });
    
    // Real-time form validation
    $('input[required]').on('blur', function() {
        if (!$(this).val()) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    
    // Number input formatting
    $('input[type="number"]').on('input', function() {
        const value = parseFloat($(this).val());
        if (isNaN(value) || value < 0) {
            $(this).val(0);
        }
    });
    
    // Stock level indicators
    $('.stock-level').each(function() {
        const level = parseFloat($(this).text());
        const critical = parseFloat($(this).data('critical'));
        
        if (level <= critical) {
            $(this).addClass('text-danger fw-bold');
        } else if (level <= critical * 1.5) {
            $(this).addClass('text-warning');
        }
    });
});

// Utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(amount);
}

function formatNumber(number, decimals = 2) {
    return new Intl.NumberFormat('en-PH', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(number);
}

function showAlert(message, type = 'info') {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('.page-header').after(alertHtml);
    
    // Auto-hide after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}

function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// AJAX helper functions
function makeAjaxRequest(url, method = 'GET', data = null, successCallback = null, errorCallback = null) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $.ajax({
        url: url,
        method: method,
        data: data,
        dataType: 'json',
        success: function(response) {
            if (successCallback) {
                successCallback(response);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            if (errorCallback) {
                errorCallback(xhr, status, error);
            } else {
                showAlert('An error occurred. Please try again.', 'danger');
            }
        }
    });
}

// Stock management functions
function updateStockDisplay() {
    $('.stock-display').each(function() {
        const sacks = parseFloat($(this).data('sacks'));
        const pieces = parseInt($(this).data('pieces'));
        
        let display = '';
        if (sacks > 0) {
            display += `${formatNumber(sacks)} sacks (${formatNumber(sacks * 50, 0)} kg)`;
        }
        if (pieces > 0) {
            if (display) display += '<br>';
            display += `${pieces} pieces`;
        }
        
        $(this).html(display);
    });
}

// Order management functions
function calculateOrderTotal() {
    let total = 0;
    $('.order-item').each(function() {
        const quantity = parseFloat($(this).find('.quantity').val()) || 0;
        const price = parseFloat($(this).find('.price').text().replace(/[^\d.-]/g, '')) || 0;
        const subtotal = quantity * price;
        
        $(this).find('.subtotal').text(formatCurrency(subtotal));
        total += subtotal;
    });
    
    $('.order-total').text(formatCurrency(total));
    return total;
}

// Export functions for global use
window.AgriSupply = {
    formatCurrency,
    formatNumber,
    showAlert,
    confirmAction,
    makeAjaxRequest,
    updateStockDisplay,
    calculateOrderTotal
};
