<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;

class SalesController extends Controller
{
    /**
     * Display the sales report page with filters.
     */
    public function index(Request $request)
    {
        // Default date range (last 30 days)
        $startDate = $request->get('start_date')
            ? Carbon::parse($request->get('start_date'))
            : Carbon::now()->subDays(30);

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        // Fetch sales within the date range
        $sales = Order::with(['cashier', 'helper'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        // Compute summary data
        $totalSales = $sales->sum('total_amount');
        $totalTransactions = $sales->count();
        $averageSale = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        return view('admin.sales-report', compact(
            'sales',
            'totalSales',
            'totalTransactions',
            'averageSale',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Fetch and display a single sale (for the modal view)
     */
    public function show($id)
    {
        $order = Order::with(['cashier', 'helper', 'items.product'])->findOrFail($id);

        return view('admin.partials.sale-details', compact('order'));
    }
}
