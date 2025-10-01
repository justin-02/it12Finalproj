<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\StockAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Make sure to use ->sum() or ->count() to get integers, not collections
        $todaySales = Order::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('total_amount') ?? 0; // Ensure it returns a number

        $weeklySales = Order::where('status', 'completed')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('total_amount') ?? 0;

        $criticalProductsCount = Product::where('is_active', true)
            ->get()
            ->filter(function($product) {
                return $product->is_critical;
            })->count();

        $totalProducts = Product::count();

        $recentAlerts = StockAlert::where('resolved', false)
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentTransactions = Order::where('status', 'completed')
            ->with(['cashier', 'helper'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'todaySales', 
            'weeklySales', 
            'criticalProductsCount', 
            'totalProducts', 
            'recentAlerts', 
            'recentTransactions'
        ));
    }

    public function salesReport(Request $request)
    {
        $startDate = Carbon::parse($request->get('start_date', now()->startOfWeek()->format('Y-m-d')));
        $endDate = Carbon::parse($request->get('end_date', now()->endOfWeek()->format('Y-m-d')));

        $sales = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->with(['cashier', 'helper'])
            ->get();

        $totalSales = $sales->sum('total_amount') ?? 0;
        $totalTransactions = $sales->count();
        $averageSale = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        return view('admin.sales-report', compact(
            'sales',
            'startDate',
            'endDate',
            'totalSales',
            'totalTransactions',
            'averageSale'
        ));
    }

    public function inventoryMonitor()
    {
        $products = Product::with(['inventoryTransactions'])->get();

        // Filter critical products based on your rule: only check critical level for units that have stock
        $criticalProducts = $products->filter(function($product) {
            return $product->is_critical;
        });

        return view('admin.inventory-monitor', compact('products', 'criticalProducts'));
    }
}