<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Task;
use App\Models\EmployeeLog;
use App\Services\EmployeeMetricsService;

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

    public function transactionHistory(Request $request)
    {
        $query = Order::where('status', 'completed')
            ->with(['items.product', 'helper', 'cashier'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('cashier_id')) {
            $query->where('cashier_id', $request->cashier_id);
        }
        
        if ($request->filled('helper_id')) {
            $query->where('helper_id', $request->helper_id);
        }
        
        if ($request->filled('min_amount')) {
            $query->where('total_amount', '>=', $request->min_amount);
        }
        
        if ($request->filled('max_amount')) {
            $query->where('total_amount', '<=', $request->max_amount);
        }

        $transactions = $query->paginate(25);
        
        // Get cashiers and helpers for filters
        $cashiers = User::where('role', 'cashier')->orderBy('name')->get();
        $helpers = User::where('role', 'helper')->orderBy('name')->get();
        
        // Calculate statistics
        $totalAmount = $transactions->sum('total_amount');
        $todaySales = Order::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('total_amount');
        $todayCount = Order::where('status', 'completed')
            ->whereDate('created_at', today())
            ->count();
        $weekSales = Order::where('status', 'completed')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('total_amount');
        $weekCount = Order::where('status', 'completed')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();
        $monthSales = Order::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');
        $monthCount = Order::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->count();
        $averageTransaction = $transactions->count() > 0 ? $transactions->avg('total_amount') : 0;

        return view('cashier.transactions', compact(
            'transactions',
            'cashiers',
            'helpers',
            'totalAmount',
            'todaySales',
            'todayCount',
            'weekSales',
            'weekCount',
            'monthSales',
            'monthCount',
            'averageTransaction'
        ));
    }

    public function transactionDetails(Order $order)
    {
        // Verify the order belongs to this cashier or is accessible
        if (auth()->user()->role === 'cashier' && $order->cashier_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view this transaction'
            ], 403);
        }

        $order->load(['items.product', 'cashier', 'helper']);
        
        return response()->json([
            'success' => true,
            'transaction' => $order
        ]);
    }

    public function exportTransactions(Request $request)
    {
        $query = Order::where('status', 'completed')
            ->with(['cashier', 'helper', 'items.product'])
            ->orderBy('created_at', 'desc');

        // Apply same filters as transaction history
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('cashier_id')) {
            $query->where('cashier_id', $request->cashier_id);
        }
        
        if ($request->filled('helper_id')) {
            $query->where('helper_id', $request->helper_id);
        }

        $transactions = $query->get();

        $filename = 'transactions_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Order Number', 
                'Date', 
                'Cashier', 
                'Helper', 
                'Items Count', 
                'Subtotal', 
                'Discount', 
                'Total Amount', 
                'Cash Received', 
                'Change',
                'Status'
            ]);

            // Data rows
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->order_number,
                    $transaction->created_at->format('Y-m-d H:i:s'),
                    $transaction->cashier->name ?? 'N/A',
                    $transaction->helper->name ?? 'N/A',
                    $transaction->items->count(),
                    $transaction->subtotal ?? 0,
                    $transaction->discount_amount ?? 0,
                    $transaction->total_amount ?? 0,
                    $transaction->cash_received ?? 0,
                    $transaction->change ?? 0,
                    $transaction->status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function receipt(Order $order)
    {
        // Only allow viewing receipts for completed orders
        if ($order->status !== 'completed') {
            return redirect()->route('cashier.dashboard')
                ->with('error', 'Order is not completed yet.');
        }

        // Verify the order belongs to this cashier
        if (auth()->user()->role === 'cashier' && $order->cashier_id !== auth()->id()) {
            return redirect()->route('cashier.dashboard')
                ->with('error', 'Unauthorized to view this receipt.');
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

        // Create a task record for processing
        if (auth()->check()) {
            $task = Task::safeCreate([
                'user_id' => auth()->id(),
                'task_type' => 'process_order',
                'context' => ['order_id' => $order->id],
                'started_at' => now(),
            ]);

            \App\Models\EmployeeLog::safeCreate([
                'user_id' => auth()->id(),
                'event' => 'process_order:start',
                'logged_at' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'session_id' => request()->session() ? request()->session()->getId() : null,
                'context' => ['task_id' => $task ? $task->id : null]
            ]);
        }

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
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
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

            $subtotal = $order->items->sum('subtotal');
            $discountAmount = (float) ($request->input('discount_amount') ?? 0);
            $totalAmount = round($subtotal - $discountAmount, 2);
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
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'cash_received' => $cashReceived,
                'change' => $change,
                'notes' => $request->input('notes'),
            ]);

            Log::info('Order completed', [
                'order_id' => $order->id,
                'subtotal' => $subtotal,
                'discount' => $discountAmount,
                'total_amount' => $totalAmount,
                'cashier_id' => auth()->id(),
                'items_count' => $order->items->count()
            ]);

            // mark task completed and record metrics
            if (auth()->check()) {
                $task = Task::where('user_id', auth()->id())
                    ->where('task_type', 'process_order')
                    ->where('context->order_id', $order->id)
                    ->latest()
                    ->first();

                if ($task) {
                    $task->completed_at = now();
                    $task->duration_seconds = now()->diffInSeconds($task->started_at);
                    $task->save();
                    EmployeeMetricsService::recordTaskCompleted(auth()->id(), 'process_order', $task->duration_seconds);
                }

                // record transaction metric
                EmployeeMetricsService::recordTransaction(auth()->id(), $totalAmount);

                \App\Models\EmployeeLog::safeCreate([
                    'user_id' => auth()->id(),
                    'event' => 'process_order:complete',
                    'logged_at' => now(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'session_id' => request()->session() ? request()->session()->getId() : null,
                    'context' => ['order_id' => $order->id]
                ]);
            }

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