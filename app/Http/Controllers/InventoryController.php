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

        // Get unique brands for filter
        $brands = Product::distinct()->pluck('brand')->sort();

        // Get pending admin messages
        $pendingMessages = AdminMessage::with(['product', 'admin'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('inventory.products', compact(
            'products',
            'totalProducts',
            'activeProducts',
            'criticalProducts',
            'outOfStockProducts',
            'brands',
            'pendingMessages'
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
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::findOrFail($validated['product_id']);
            
            $sacksToAdd = (float) $validated['quantity_sacks'];
            $kilosLogged = $sacksToAdd * 50; // for audit trail only

            // Update stock
            $product->current_stock_sacks += $sacksToAdd;
            $product->current_stock_pieces += $validated['quantity_pieces'];
            $product->save();

            // Create transaction record
            InventoryTransaction::create([
                'product_id' => $product->id,
                'type' => 'stock-in',
                'quantity_sacks' => $sacksToAdd,
                'quantity_pieces' => $validated['quantity_pieces'],
                'quantity_kilos' => $kilosLogged,
                'notes' => $validated['notes'] ?? null,
                'user_id' => auth()->id()
            ]);
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
    

}