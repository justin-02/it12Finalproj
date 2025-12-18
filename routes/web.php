<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\HelperController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalesController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth','log.activity'])->group(function () {
    // Home redirect based on role
    Route::get('/', function () {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isInventory()) {
            return redirect()->route('inventory.dashboard');
        } elseif ($user->isCashier()) {
            return redirect()->route('cashier.dashboard');
        } elseif ($user->isHelper()) {
            return redirect()->route('helper.dashboard');
        }
        return redirect('/login');
    })->name('home');

    // Heartbeat endpoint (for all authenticated users)
    Route::post('/heartbeat', function(\Illuminate\Http\Request $request) {
        if (auth()->check()) {
            $sessionId = $request->session() ? $request->session()->getId() : null;
            if (\Illuminate\Support\Facades\Schema::hasTable('session_tracks') && $sessionId) {
                \App\Models\SessionTrack::where('session_id', $sessionId)
                    ->update(['last_activity_at' => now(), 'is_idle' => false]);
            }
            auth()->user()->last_seen = now();
            auth()->user()->saveQuietly();
        }
        return response()->json(['status' => 'ok']);
    })->name('heartbeat');

    // ========== ADMIN ROUTES ==========
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/sales-report', [AdminController::class, 'salesReport'])->name('admin.sales-report');
        Route::get('/inventory-monitor', [AdminController::class, 'inventoryMonitor'])->name('admin.inventory-monitor');
        Route::post('/send-message', [AdminController::class, 'sendMessage'])->name('admin.send-message');
        Route::get('/messages', [AdminController::class, 'getMessages'])->name('admin.messages');
        Route::get('/sales/{id}', [SalesController::class, 'show'])->name('admin.sales.show');
        Route::get('/admin/recent-stockins', [InventoryController::class, 'recentStockIns'])->name('admin.recent-stockins');
        Route::get('/batches', [InventoryController::class, 'batches'])->name('admin.batches');
        Route::get('/admin/employees', [EmployeeController::class, 'index'])->name('admin.employees.index');
        Route::post('/admin/employees', [EmployeeController::class, 'store'])->name('admin.employees.store');
        Route::get('/employee-monitoring', [AdminController::class, 'employeeMonitoring'])->name('admin.employee-monitoring');
        Route::get('/employees/{id}/details', [AdminController::class, 'getEmployeeDetails'])->name('admin.employee.details');
        Route::get('/employees/{id}/performance', [AdminController::class, 'getEmployeePerformance'])->name('admin.employee.performance');
        Route::get('/sessions', [AdminController::class, 'sessions'])->name('admin.sessions');
        Route::get('/sessions/export', [AdminController::class, 'exportSessions'])->name('admin.sessions.export');
        Route::get('/activity-logs', [AdminController::class, 'activityLogs'])->name('admin.activity-logs');
        Route::get('/productivity-report', [AdminController::class, 'productivityReport'])->name('admin.productivity-report');
        Route::get('/productivity-report/export', [AdminController::class, 'exportProductivityReport'])->name('admin.productivity-report.export');
        
        // Employee management
        Route::get('/employees', [\App\Http\Controllers\EmployeeController::class, 'index'])->name('admin.employees.index');
        Route::get('/employees/export', [\App\Http\Controllers\EmployeeController::class, 'export'])->name('admin.employees.export');
        Route::get('/employees/create', [\App\Http\Controllers\EmployeeController::class, 'create'])->name('admin.employees.create');
        Route::post('/employees', [\App\Http\Controllers\EmployeeController::class, 'store'])->name('admin.employees.store');
        Route::get('/employees/{employee}/edit', [\App\Http\Controllers\EmployeeController::class, 'edit'])->name('admin.employees.edit');
        Route::put('/employees/{employee}', [\App\Http\Controllers\EmployeeController::class, 'update'])->name('admin.employees.update');
        Route::patch('/employees/{employee}/toggle-status', [\App\Http\Controllers\EmployeeController::class, 'toggleStatus'])->name('admin.employees.toggle-status');
        Route::post('/employees/{employee}/reset-password', [\App\Http\Controllers\EmployeeController::class, 'resetPassword'])->name('admin.employees.reset-password');
        Route::get('/employees/{employee}', [\App\Http\Controllers\EmployeeController::class, 'show'])->name('admin.employees.show');
    });

    // ========== INVENTORY ROUTES ==========
    Route::middleware(['role:inventory'])->prefix('inventory')->group(function () {
        Route::get('/dashboard', [InventoryController::class, 'dashboard'])->name('inventory.dashboard');
        Route::get('/products', [InventoryController::class, 'products'])->name('inventory.products');
        Route::post('/products', [InventoryController::class, 'storeProduct'])->name('inventory.products.store');
        Route::put('/products/{product}', [InventoryController::class, 'updateProduct'])->name('inventory.products.update');
        Route::patch('/products/{product}/toggle-status', [InventoryController::class, 'toggleProductStatus'])->name('inventory.products.toggle-status');
        Route::post('/stock-in', [InventoryController::class, 'stockIn'])->name('inventory.stock-in');
        Route::post('/stock-out', [InventoryController::class, 'stockOut'])->name('inventory.stock-out');
        Route::post('/report-critical/{product}', [InventoryController::class, 'reportCriticalLevel'])->name('inventory.report-critical');
        Route::patch('/messages/{message}/read', [InventoryController::class, 'markMessageAsRead'])->name('inventory.message.read');
        Route::patch('/messages/{message}/complete', [InventoryController::class, 'markMessageAsCompleted'])->name('inventory.message.complete');
        Route::get('/messages', [InventoryController::class, 'getMessages'])->name('inventory.messages');
        Route::get('/batches', [InventoryController::class, 'batches'])->name('inventory.batches');
        Route::get('/batches/create', [InventoryController::class, 'createBatch'])->name('inventory.batches.create');
    });

    // ========== HELPER ROUTES ==========
    Route::middleware(['role:helper'])->prefix('helper')->group(function () {
        Route::get('/dashboard', [HelperController::class, 'dashboard'])->name('helper.dashboard');
        Route::post('/prepare-order', [HelperController::class, 'prepareOrder'])->name('helper.prepare-order');
        Route::delete('/order-items/{item}', [HelperController::class, 'removeOrderItem'])->name('helper.remove-order-item');
        Route::post('/submit-order/{order}', [HelperController::class, 'submitOrder'])->name('helper.submit-order');
    });

    // ========== CASHIER ROUTES ==========
    Route::middleware(['role:cashier'])->prefix('cashier')->group(function () {
        Route::get('/dashboard', [CashierController::class, 'dashboard'])->name('cashier.dashboard');
        Route::get('/transactions', [CashierController::class, 'transactionHistory'])->name('cashier.transactions');
        Route::get('/process-order/{order}', [CashierController::class, 'processOrder'])->name('cashier.process-order');
        Route::post('/complete-order/{order}', [CashierController::class, 'completeOrder'])->name('cashier.complete-order');
        Route::get('/receipt/{order}', [CashierController::class, 'receipt'])->name('cashier.receipt');
Route::middleware(['auth', 'role:cashier'])->prefix('cashier')->group(function () {
    Route::get('/transactions-ajax', [CashierController::class, 'transactionsAjax'])->name('cashier.transactions-ajax');
    Route::get('/check-new-transactions', [CashierController::class, 'checkNewTransactions'])->name('cashier.check-new-transactions');

});
    });
});