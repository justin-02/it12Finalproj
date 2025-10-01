<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HelperController extends Controller
{
    public function dashboard()
    {
        $products = Product::where('is_active', true)->get();
        
        // Get the current order for the logged-in helper
        $currentOrder = Order::where('helper_id', auth()->id())
            ->where('status', 'preparing')
            ->with('items.product')
            ->first();

        return view('helper.dashboard', compact('products', 'currentOrder'));
    }

    public function prepareOrder(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'unit' => 'required|in:sack,kilo,piece',
            'quantity' => 'required|numeric|min:0.1',
        ]);

        // Get the product to check stock
        $product = Product::findOrFail($validated['product_id']);

        // Check stock availability
        if (!$this->checkStockAvailability($product, $validated['unit'], $validated['quantity'])) {
            $availableInfo = $this->getAvailableStockInfo($product, $validated['unit']);
            $sacksRequired = $product->calculateSacksRequired($validated['unit'], $validated['quantity']);
            $errorMessage = "Insufficient stock! Requested: {$validated['quantity']} {$validated['unit']}";
            
            if ($validated['unit'] === 'kilo' && $sacksRequired > 0) {
                $errorMessage .= " (requires {$sacksRequired} sacks)";
            }
            
            $errorMessage .= ". {$availableInfo}";
            return redirect()->back()->with('error', $errorMessage);
        }

        // Get or create current order
        $order = Order::where('helper_id', auth()->id())
            ->where('status', 'preparing')
            ->first();

        if (!$order) {
            $order = Order::create([
                'order_number' => 'ORD-' . time(),
                'helper_id' => auth()->id(),
                'status' => 'preparing'
            ]);
            Log::info('Order created', ['order_id' => $order->id, 'helper_id' => auth()->id()]);
        }

        // Add item to order (price will be set by cashier)
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $validated['product_id'],
            'unit' => $validated['unit'],
            'quantity' => $validated['quantity'],
            'price' => null,
            'subtotal' => null
        ]);

        Log::info('Order item added', ['order_id' => $order->id, 'product_id' => $validated['product_id'], 'unit' => $validated['unit'], 'quantity' => $validated['quantity']]);

        return redirect()->route('helper.dashboard')->with('success', 'Product added to order!');
    }

    private function checkStockAvailability(Product $product, $unit, $quantity)
    {
        return $product->hasSufficientStock($unit, $quantity);
    }

    private function getAvailableStockInfo(Product $product, $unit)
    {
        return $product->getStockInfo($unit);
    }

    public function removeOrderItem(OrderItem $item)
    {
        // Check if the item belongs to the current user's order
        if ($item->order->helper_id === auth()->id() && $item->order->status === 'preparing') {
            $item->delete();
            
            // Delete order if no items left
            if ($item->order->items->count() === 0) {
                $item->order->delete();
            }
            
            return redirect()->route('helper.dashboard')->with('success', 'Item removed from order!');
        }

        return redirect()->route('helper.dashboard')->with('error', 'Cannot remove item!');
    }

    public function submitOrder(Request $request, Order $order)
    {
        Log::info('Submit order called', [
            'order_id' => $order->id,
            'helper_id' => $order->helper_id,
            'auth_id' => auth()->id(),
            'status' => $order->status
        ]);
        
        // Check if the order belongs to the current user and is in preparing status
        if ($order->helper_id === auth()->id() && $order->status === 'preparing') {
            // Ensure items relation is loaded and confirm it has items
            $order->loadMissing('items.product');
            if ($order->items->count() === 0) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Cannot submit empty order!'
                ], 400);
            }

            // Double-check stock availability for all items
            foreach ($order->items as $item) {
                if (!$this->checkStockAvailability($item->product, $item->unit, $item->quantity)) {
                    $availableInfo = $this->getAvailableStockInfo($item->product, $item->unit);
                    $sacksRequired = $item->product->calculateSacksRequired($item->unit, $item->quantity);
                    $errorMessage = "Insufficient stock for {$item->product->product_name}! Requested: {$item->quantity} {$item->unit}";
                    
                    if ($item->unit === 'kilo' && $sacksRequired > 0) {
                        $errorMessage .= " (requires {$sacksRequired} sacks)";
                    }
                    
                    $errorMessage .= ". {$availableInfo}";
                    
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 400);
                }
            }

            // Update order status to 'ready' so cashier can see it
            $order->update(['status' => 'ready']);

            Log::info('Order submitted to cashier', ['order_id' => $order->id, 'items_count' => $order->items->count()]);

            return response()->json([
                'success' => true, 
                'message' => 'Order sent to cashier successfully!'
            ]);
        }

        Log::error('Submit order failed', [
            'order_id' => $order->id,
            'helper_id' => $order->helper_id,
            'auth_id' => auth()->id(),
            'status' => $order->status,
            'reason' => 'Order does not belong to user or wrong status'
        ]);

        return response()->json([
            'success' => false, 
            'message' => 'Cannot submit order! Order does not belong to you or is not in preparing status.'
        ], 403);
    }
}