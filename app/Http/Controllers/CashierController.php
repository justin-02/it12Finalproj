<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CashierController extends Controller
{
    public function dashboard()
    {
        // Get orders with status 'ready' (sent by helper)
        $pendingOrders = Order::where('status', 'ready')
            ->with(['items.product', 'helper'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate counts and totals
        $pendingOrdersCount = $pendingOrders->count();

        $todaySales = Order::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('total_amount');

        $todayTransactions = Order::where('status', 'completed')
            ->whereDate('created_at', today())
            ->count();

        $weeklySales = Order::where('status', 'completed')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('total_amount');

        $recentTransactions = Order::where('status', 'completed')
            ->with(['items.product', 'helper', 'cashier'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('cashier.dashboard', compact(
            'pendingOrders',
            'pendingOrdersCount',
            'todaySales',
            'todayTransactions',
            'weeklySales',
            'recentTransactions'
        ));
    }

    public function transactionHistory()
    {
        $transactions = Order::where('status', 'completed')
            ->with(['items.product', 'helper', 'cashier'])
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('cashier.transactions', compact('transactions'));
    }

    public function receipt(Order $order)
    {
        // Only allow viewing receipts for completed orders
        if ($order->status !== 'completed') {
            return redirect()->route('cashier.dashboard')
                ->with('error', 'Order is not completed yet.');
        }

        $order->load(['items.product', 'helper', 'cashier']);

        return view('cashier.receipt', compact('order'));
    }

    public function processOrder(Order $order)
    {
        // Only allow processing orders that are ready
        if ($order->status !== 'ready') {
            return redirect()->route('cashier.dashboard')
                ->with('error', 'Order is not ready for processing.');
        }

        $order->load(['items.product', 'helper']);

        // Calculate prices and subtotals for each item
        foreach ($order->items as $item) {
            if (is_null($item->price)) {
                $item->price = $this->calculateUnitPrice($item->product, $item->unit);
            }
            $item->subtotal = round($item->price * $item->quantity, 2);
            $item->save();
        }

        $totalAmount = $order->items->sum('subtotal');

        return view('cashier.process-order', compact('order', 'totalAmount'));
    }

    public function completeOrder(Request $request, Order $order)
    {
        // Only allow completing orders that are ready
        if ($order->status !== 'ready') {
            return redirect()->route('cashier.dashboard')
                ->with('error', 'Order is not ready for completion.');
        }

        $order->load(['items.product']);

        $request->validate([
            'cash_received' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($order, $request) {
            // Recalculate prices/subtotals
            foreach ($order->items as $item) {
                if (is_null($item->price)) {
                    $item->price = $this->calculateUnitPrice($item->product, $item->unit);
                }
                $item->subtotal = round($item->price * $item->quantity, 2);

                // Double-check stock availability before deduction
                if (!$this->hasSufficientStock($item->product, $item->unit, $item->quantity)) {
                    abort(400, "Insufficient stock for {$item->product->product_name}.");
                }

                $item->save();
            }

            $totalAmount = $order->items->sum('subtotal');
            $cashReceived = (float) $request->input('cash_received');
            $change = round($cashReceived - $totalAmount, 2);

            if ($change < 0) {
                abort(400, 'Cash received is insufficient.');
            }

            // Deduct inventory and record transactions
            foreach ($order->items as $item) {
                $this->deductInventoryAndLog($item->product, $item->unit, $item->quantity, $order);
            }

            // Update order
            $order->update([
                'cashier_id' => auth()->id(),
                'status' => 'completed',
                'total_amount' => $totalAmount,
                'cash_received' => $cashReceived,
                'change' => $change,
            ]);

            Log::info('Order completed', [
                'order_id' => $order->id,
                'total_amount' => $totalAmount,
                'cashier_id' => auth()->id(),
                'items_count' => $order->items->count()
            ]);

            return redirect()->route('cashier.receipt', $order->id)
                ->with('success', 'Order completed and inventory updated successfully.');
        });
    }

    private function calculateUnitPrice(Product $product, string $unit): float
    {
        return $product->calculatePrice($unit, 1);
    }

    private function hasSufficientStock(Product $product, string $unit, float $quantity): bool
    {
        return $product->hasSufficientStock($unit, $quantity);
    }

    private function deductInventoryAndLog(Product $product, string $unit, float $quantity, Order $order): void
    {
        $sacksToDeduct = $product->calculateSacksRequired($unit, $quantity);
        $oldStockSacks = $product->current_stock_sacks;
        $oldStockPieces = $product->current_stock_pieces;

        if ($unit === 'sack') {
            $product->current_stock_sacks = max(0, $product->current_stock_sacks - $quantity);
            $product->save();

            InventoryTransaction::create([
                'product_id' => $product->id,
                'type' => 'sale',
                'quantity_sacks' => $quantity,
                'quantity_pieces' => 0,
                'quantity_kilos' => 0,
                'reference_type' => 'order',
                'reference_id' => $order->id,
                'notes' => 'Sold via cashier (sacks)',
                'user_id' => auth()->id(),
            ]);
        } elseif ($unit === 'kilo') {
            // Precise decimal deduction for kilos
            $product->current_stock_sacks = max(0, $product->current_stock_sacks - $sacksToDeduct);
            $product->save();

            Log::info('Kilo inventory deduction', [
                'product_id' => $product->id,
                'quantity_kilos' => $quantity,
                'sacks_to_deduct' => $sacksToDeduct,
                'old_stock_sacks' => $oldStockSacks,
                'new_stock_sacks' => $product->current_stock_sacks
            ]);

            InventoryTransaction::create([
                'product_id' => $product->id,
                'type' => 'sale',
                'quantity_sacks' => $sacksToDeduct,
                'quantity_pieces' => 0,
                'quantity_kilos' => $quantity,
                'reference_type' => 'order',
                'reference_id' => $order->id,
                'notes' => "Sold via cashier (kilos: {$quantity}, sacks: {$sacksToDeduct})",
                'user_id' => auth()->id(),
            ]);
        } elseif ($unit === 'piece') {
            $product->current_stock_pieces = max(0, $product->current_stock_pieces - $quantity);
            $product->save();

            InventoryTransaction::create([
                'product_id' => $product->id,
                'type' => 'sale',
                'quantity_sacks' => 0,
                'quantity_pieces' => $quantity,
                'quantity_kilos' => 0,
                'reference_type' => 'order',
                'reference_id' => $order->id,
                'notes' => 'Sold via cashier (pieces)',
                'user_id' => auth()->id(),
            ]);
        }
    }
}