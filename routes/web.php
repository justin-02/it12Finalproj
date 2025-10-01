<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\HelperController;
use App\Http\Controllers\CashierController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
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
    });

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/sales-report', [AdminController::class, 'salesReport'])->name('admin.sales-report');
        Route::get('/inventory-monitor', [AdminController::class, 'inventoryMonitor'])->name('admin.inventory-monitor');
    });

    // Inventory Routes
Route::middleware(['role:inventory'])->prefix('inventory')->group(function () {
    Route::get('/dashboard', [InventoryController::class, 'dashboard'])->name('inventory.dashboard');
    Route::get('/products', [InventoryController::class, 'products'])->name('inventory.products');
    Route::post('/products', [InventoryController::class, 'storeProduct'])->name('inventory.products.store');
    Route::put('/products/{product}', [InventoryController::class, 'updateProduct'])->name('inventory.products.update');
    Route::patch('/products/{product}/toggle-status', [InventoryController::class, 'toggleProductStatus'])->name('inventory.products.toggle-status');
    Route::post('/stock-in', [InventoryController::class, 'stockIn'])->name('inventory.stock-in');
    Route::post('/stock-out', [InventoryController::class, 'stockOut'])->name('inventory.stock-out');
    Route::post('/report-critical/{product}', [InventoryController::class, 'reportCriticalLevel'])->name('inventory.report-critical');
});

   // Helper Routes
Route::middleware(['role:helper'])->prefix('helper')->group(function () {
    Route::get('/dashboard', [HelperController::class, 'dashboard'])->name('helper.dashboard');
    Route::post('/prepare-order', [HelperController::class, 'prepareOrder'])->name('helper.prepare-order');
    Route::delete('/order-items/{item}', [HelperController::class, 'removeOrderItem'])->name('helper.remove-order-item');
    Route::post('/submit-order/{order}', [HelperController::class, 'submitOrder'])->name('helper.submit-order');
});

// Cashier Routes
Route::middleware(['role:cashier'])->prefix('cashier')->group(function () {
    Route::get('/dashboard', [CashierController::class, 'dashboard'])->name('cashier.dashboard');
    Route::get('/transactions', [CashierController::class, 'transactionHistory'])->name('cashier.transactions');
    Route::get('/process-order/{order}', [CashierController::class, 'processOrder'])->name('cashier.process-order');
    Route::post('/complete-order/{order}', [CashierController::class, 'completeOrder'])->name('cashier.complete-order');
    Route::get('/receipt/{order}', [CashierController::class, 'receipt'])->name('cashier.receipt');
});
});