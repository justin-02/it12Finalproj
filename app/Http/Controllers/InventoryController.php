<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\InventoryTransaction;
use App\Models\StockAlert;
use App\Models\AdminMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Task;
use App\Models\EmployeeLog;
use App\Services\EmployeeMetricsService;
class InventoryController extends Controller
{
    public function dashboard()
    {
        $products = Product::with(['inventoryTransactions'])->get();

        $totalProducts = Product::count();
        
        // Critical Products: Only count products where the unit with stock is below critical level
        $criticalProducts = Product::where('is_active', true)
            ->get()
            ->filter(function($product) {
                return $product->is_critical;
            })->count();

        // In Stock Products: Products that have at least one unit in stock
        $inStockProducts = Product::where('is_active', true)
            ->where(function($query) {
                $query->where('current_stock_sacks', '>', 0)
                      ->orWhere('current_stock_pieces', '>', 0);
            })->count();

        // Low Stock Products: Products where stock is low (1.5x critical level) for units that have stock
        $lowStockProducts = Product::where('is_active', true)
            ->get()
            ->filter(function($product) {
                $sacksLow = $product->current_stock_sacks > 0 && 
                           $product->current_stock_sacks <= ($product->critical_level_sacks * 1.5);
                $piecesLow = $product->current_stock_pieces > 0 && 
                            $product->current_stock_pieces <= ($product->critical_level_pieces * 1.5);
                return $sacksLow || $piecesLow;
            })->count();

        $recentTransactions = InventoryTransaction::with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('inventory.dashboard', compact(
            'products',
            'totalProducts',
            'criticalProducts',
            'inStockProducts',
            'lowStockProducts',
            'recentTransactions'
        ));
    }

    public function products()
{
    $products = Product::with(['inventoryTransactions', 'adminMessages'])
        ->orderBy('product_name')
        ->paginate(10);

    $totalProducts = Product::count();
    $activeProducts = Product::where('is_active', true)->count();
    $criticalProducts = Product::where('is_active', true)
        ->where(function($query) {
            $query->where(function($q) {
                $q->where('current_stock_sacks', '>', 0)
                  ->where('current_stock_sacks', '<=', DB::raw('critical_level_sacks'));
            })->orWhere(function($q) {
                $q->where('current_stock_pieces', '>', 0)
                  ->where('current_stock_pieces', '<=', DB::raw('critical_level_pieces'));
            });
        })->count();
    $outOfStockProducts = Product::where('is_active', true)
        ->where('current_stock_sacks', '<=', 0)
        ->where('current_stock_pieces', '<=', 0)
        ->count();
    $brands = Product::distinct()->pluck('brand')->sort();
    $pendingMessages = AdminMessage::with(['product', 'admin'])
        ->where('status', 'pending')
        ->orderBy('created_at', 'desc')
        ->get();
    
    // FIX: Temporarily disable soft deletes or handle missing column
    try {
        // Option 1: Use DB query directly (no soft deletes)
        $batches = DB::table('batches')
            ->orderBy('restock_date', 'desc')
            ->take(10)
            ->get();
            
       
            
    } catch (\Exception $e) {
        // If table doesn't exist, use empty collection
        $batches = collect();
        \Log::warning('Batches query failed: ' . $e->getMessage());
    }
    
    return view('inventory.products', compact(
        'products',
        'totalProducts',
        'activeProducts',
        'criticalProducts',
        'outOfStockProducts',
        'brands',
        'pendingMessages',
        'batches'
    ));
}

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'current_stock_sacks' => 'required|numeric|min:0',
            'current_stock_pieces' => 'required|integer|min:0',
            'critical_level_sacks' => 'required|numeric|min:0.01',
            'critical_level_pieces' => 'required|integer|min:1',
            'expiration_days' => 'required|integer|min:1',
        ]);

        // Set is_active to true by default for new products
        $validated['is_active'] = true;

        try {
            Product::create($validated);
            return redirect()->route('inventory.products')->with('success', 'Product added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add product: ' . $e->getMessage())->withInput();
        }
    }

    public function updateProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'current_stock_sacks' => 'required|numeric|min:0',
            'current_stock_pieces' => 'required|integer|min:0',
            'critical_level_sacks' => 'required|numeric|min:0.01',
            'critical_level_pieces' => 'required|integer|min:1',
            'expiration_days' => 'required|integer|min:1',
            'is_active' => 'sometimes|boolean',
        ]);

        $product->update($validated);

        return redirect()->route('inventory.products')->with('success', 'Product updated successfully!');
    }

    public function toggleProductStatus(Product $product)
    {
        $product->update([
            'is_active' => !$product->is_active
        ]);

        $status = $product->is_active ? 'activated' : 'deactivated';

        return redirect()->route('inventory.products')->with('success', "Product {$status} successfully!");
    }

    public function stockIn(Request $request)
{
    $validated = $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity_sacks' => 'required|numeric|min:0',
        'quantity_pieces' => 'required|integer|min:0',
        'restock_date' => 'required|date',
        'expiry_date' => 'nullable|date|after_or_equal:restock_date',
        'supplier' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
    ]);

    DB::transaction(function () use ($validated) {
        $product = Product::findOrFail($validated['product_id']);

        if (auth()->check()) {
            $task = Task::safeCreate([
                'user_id' => auth()->id(),
                'task_type' => 'stock_in',
                'context' => [
                    'product_id' => $product->id, 
                    'quantity_sacks' => $validated['quantity_sacks'],
                    'quantity_pieces' => $validated['quantity_pieces']
                ],
                'started_at' => now(),
            ]);

            EmployeeLog::safeCreate([
                'user_id' => auth()->id(),
                'event' => 'stock_in:start',
                'logged_at' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'session_id' => request()->session() ? request()->session()->getId() : null,
                'context' => ['task_id' => $task ? $task->id : null]
            ]);
        }

        // Update stock for both sacks and pieces
        $product->current_stock_sacks += $validated['quantity_sacks'];
        $product->current_stock_pieces += $validated['quantity_pieces'];
        $product->save();

        // Create transaction record
        InventoryTransaction::create([
            'product_id' => $product->id,
            'type' => 'stock-in',
            'quantity_sacks' => $validated['quantity_sacks'],
            'quantity_pieces' => $validated['quantity_pieces'],
            'quantity_kilos' => $validated['quantity_sacks'] * 50,
            'notes' => $validated['notes'] ?? null,
            'user_id' => auth()->id()
        ]);

        // Also create a batch record if needed
        // (You might want to link this to the batch_product table)
        $batch = \App\Models\Batch::create([
            'batch_code' => 'BATCH-' . strtoupper(uniqid()),
            'restock_date' => $validated['restock_date'],
            'expiry_date' => $validated['expiry_date'] ?? null,
            'supplier' => $validated['supplier'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Link product to batch
        if (Schema::hasTable('batch_product')) {
            $batch->products()->attach($product->id, [
                'quantity' => $validated['quantity_sacks']
            ]);
        }

        // Mark task completed and update metrics
        if (auth()->check()) {
            if (isset($task)) {
                $task->completed_at = now();
                $task->duration_seconds = now()->diffInSeconds($task->started_at);
                $task->save();
                EmployeeMetricsService::recordTaskCompleted(auth()->id(), 'stock_in', $task->duration_seconds);
            }
            
            // Record total quantity for metrics (sacks + pieces converted)
            $totalQuantity = $validated['quantity_sacks'] + ($validated['quantity_pieces'] / 100); // Assuming 100 pieces per sack
            EmployeeMetricsService::recordStockIn(auth()->id(), $totalQuantity);

            EmployeeLog::safeCreate([
                'user_id' => auth()->id(),
                'event' => 'stock_in:complete',
                'logged_at' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'session_id' => request()->session() ? request()->session()->getId() : null,
                'context' => [
                    'product_id' => $product->id, 
                    'quantity_sacks' => $validated['quantity_sacks'],
                    'quantity_pieces' => $validated['quantity_pieces']
                ]
            ]);
        }
    });

    return redirect()->back()->with('success', 'Stock added successfully!');
}
    public function stockOut(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity_sacks' => 'required|numeric|min:0',
            'quantity_pieces' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::findOrFail($validated['product_id']);

            if (auth()->check()) {
                $task = Task::safeCreate([
                    'user_id' => auth()->id(),
                    'task_type' => 'stock_out',
                    'context' => ['product_id' => $product->id, 'quantity_sacks' => $validated['quantity_sacks'], 'quantity_pieces' => $validated['quantity_pieces']],
                    'started_at' => now(),
                ]);

                \App\Models\EmployeeLog::safeCreate([
                    'user_id' => auth()->id(),
                    'event' => 'stock_out:start',
                    'logged_at' => now(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'session_id' => request()->session() ? request()->session()->getId() : null,
                    'context' => ['task_id' => $task ? $task->id : null]
                ]);
            }

            $sacksToDeduct = (float) $validated['quantity_sacks'];
            $piecesToDeduct = (int) $validated['quantity_pieces'];

            // Guard against negative inventory
            if ($sacksToDeduct > $product->current_stock_sacks || $piecesToDeduct > $product->current_stock_pieces) {
                return redirect()->back()->with('error', 
                    "⚠️ Insufficient stock for {$product->product_name}! ".
                    "Requested: {$sacksToDeduct} sacks, {$piecesToDeduct} pieces. ".
                    "Available: {$product->current_stock_sacks} sacks, {$product->current_stock_pieces} pieces."
                );
            }


            $product->current_stock_sacks -= $sacksToDeduct;
            $product->current_stock_pieces -= $piecesToDeduct;
            $product->save();

            InventoryTransaction::create([
                'product_id' => $product->id,
                'type' => 'stock-out',
                'quantity_sacks' => $sacksToDeduct,
                'quantity_pieces' => $piecesToDeduct,
                'quantity_kilos' => $sacksToDeduct * 50,
                'notes' => $validated['notes'] ?? null,
                'user_id' => auth()->id()
            ]);

            if (auth()->check()) {
                if (isset($task)) {
                    $task->completed_at = now();
                    $task->duration_seconds = now()->diffInSeconds($task->started_at);
                    $task->save();
                    EmployeeMetricsService::recordTaskCompleted(auth()->id(), 'stock_out', $task->duration_seconds);
                }
                EmployeeMetricsService::recordStockOut(auth()->id(), $sacksToDeduct);

                \App\Models\EmployeeLog::safeCreate([
                    'user_id' => auth()->id(),
                    'event' => 'stock_out:complete',
                    'logged_at' => now(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'session_id' => request()->session() ? request()->session()->getId() : null,
                    'context' => ['product_id' => $product->id, 'quantity_sacks' => $sacksToDeduct, 'quantity_pieces' => $piecesToDeduct]
                ]);
            }
        });

        return redirect()->back()->with('success', 'Stock deducted successfully!');
    }

    public function reportCriticalLevel(Product $product)
    {
        StockAlert::create([
            'product_id' => $product->id,
            'alert_type' => 'critical',
            'message' => "Product {$product->product_name} ({$product->brand}) is at critical level. Sacks: {$product->current_stock_sacks}, Pieces: {$product->current_stock_pieces}"
        ]);

        return response()->json(['success' => true]);
    }

    public function markMessageAsRead(AdminMessage $message)
    {
        $message->markAsRead();
        return response()->json(['success' => true]);
    }

    public function markMessageAsCompleted(AdminMessage $message)
    {
        $message->markAsCompleted();
        return response()->json(['success' => true]);
    }

    public function getMessages()
    {
        $messages = AdminMessage::with(['product', 'admin'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($messages);
    }
    public function recentStockIns()
    {
        $stockIns = InventoryTransaction::with(['product', 'user'])
            ->where('type', 'stock-in')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.recent-stockins', compact('stockIns'));
    }
    // In InventoryController.php
public function createBatch()
{
    $products = Product::where('is_active', true)
        ->orderBy('product_name')
        ->get();
    
    return view('inventory.batches', compact('products'));
}

public function storeBatch(Request $request)
{
    $validated = $request->validate([
        'batch_code' => 'required|string|max:100|unique:batches,batch_code',
        'restock_date' => 'required|date',
        'expiry_date' => 'nullable|date|after:restock_date',
        'supplier' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
        'products' => 'required|array|min:1',
        'products.*.product_id' => 'required|exists:products,id',
        'products.*.quantity' => 'required|numeric|min:0.01',
    ]);
    
    DB::transaction(function () use ($validated) {
        $batch = Batch::create([
            'batch_code' => $validated['batch_code'],
            'restock_date' => $validated['restock_date'],
            'expiry_date' => $validated['expiry_date'],
            'supplier' => $validated['supplier'],
            'notes' => $validated['notes'],
        ]);
        
        foreach ($validated['products'] as $productData) {
            $batch->products()->attach($productData['product_id'], [
                'quantity' => $productData['quantity']
            ]);
        }
    });
    
    return redirect()->route('inventory.batches')->with('success', 'Batch created successfully!');
}
public function batches()
{
    // Get products for the filter dropdown
    $products = Product::where('is_active', true)
        ->orderBy('product_name')
        ->get();
    
    // Check if batches table exists
    if (!Schema::hasTable('batches')) {
        return view('inventory.batches', [
            'batches' => collect(),
            'products' => $products,
            'error' => 'Batches table not found. Please run migrations.'
        ]);
    }
    
    // Simple query - no joins to non-existent tables
    $batches = DB::table('batches')
        ->orderBy('restock_date', 'desc')
        ->paginate(10);
    
    // Check if pivot table exists
    $hasPivotTable = Schema::hasTable('batch_product');
    
    // If pivot table exists, try to get product data
    if ($hasPivotTable) {
        // You could add logic here to fetch products for each batch
        // For now, we'll just pass the flag to the view
    }
    
    return view('inventory.batches', compact('batches', 'products', 'hasPivotTable'));
}
}