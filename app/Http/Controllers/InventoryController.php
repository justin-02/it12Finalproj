<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\InventoryTransaction;
use App\Models\StockAlert;
use App\Models\AdminMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProductBatch;



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
            ->paginate(25);

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
        $batches = \App\Models\Batch::with('product')->orderBy('restock_date', 'desc')->take(10)->get();
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
            'quantity' => 'required|numeric|min:1',
            'restock_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:restock_date',
            'supplier' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::findOrFail($validated['product_id']);

            // Update stock (assume quantity is in sacks for now)
            $product->current_stock_sacks += $validated['quantity'];
            $product->save();

            // Auto-increment batch code globally
            $lastBatch = \App\Models\Batch::orderByDesc('id')->first();
            $nextNumber = 1;
            if ($lastBatch && preg_match('/^BATCH-(\d+)$/', $lastBatch->batch_code, $matches)) {
                $nextNumber = intval($matches[1]) + 1;
            }
            $batchCode = 'BATCH-' . $nextNumber;

            // Compute expiry date if not provided
            $expiryDate = $validated['expiry_date'] ?? null;
            if (!$expiryDate) {
                $restockDate = \Carbon\Carbon::parse($validated['restock_date']);
                $expiryDate = $restockDate->copy()->addDays($product->expiration_days)->format('Y-m-d');
            }

            // Create batch record
            \App\Models\Batch::create([
                'product_id' => $product->id,
                'batch_code' => $batchCode,
                'quantity' => $validated['quantity'],
                'restock_date' => $validated['restock_date'],
                'expiry_date' => $expiryDate,
                'supplier' => $validated['supplier'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create transaction record
            InventoryTransaction::create([
                'product_id' => $product->id,
                'type' => 'stock-in',
                'quantity_sacks' => $validated['quantity'],
                'quantity_pieces' => 0,
                'quantity_kilos' => $validated['quantity'] * 50,
                'notes' => $validated['notes'] ?? null,
                'user_id' => auth()->id()
            ]);
        });

        return redirect()->back()->with('success', 'Stock and batch added successfully!');
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
    public function getProductBatches($productId)
    {
        $batches = ProductBatch::where('product_id', $productId)
            ->orderBy('date_received', 'desc')
            ->get(['batch_number', 'quantity_sacks', 'quantity_pieces', 'date_received', 'expiry_date', 'supplier']);

        return response()->json($batches);
    }

    /**
     * Show all batches for batch list UI.
     */
    public function batches(Request $request)
    {
        $query = \App\Models\Batch::with('product');
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('status')) {
            $today = now()->toDateString();
            if ($request->status === 'expired') {
                $query->where('expiry_date', '<', $today);
            } elseif ($request->status === 'active') {
                $query->where('expiry_date', '>=', $today);
            }
        }
        // Always order by latest restock_date first
        $batches = $query->orderByDesc('restock_date')->paginate(25);
        $products = Product::orderBy('product_name')->get();
        return view('inventory.batches', compact('batches', 'products'));
    }
}