<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\StockAlert;
use App\Models\AdminMessage;
use App\Models\User;
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

        // Employee monitoring data for dashboard
        $employees = User::where('role', '!=', 'admin')
            ->withCount([
                'sales as today_sales' => function($query) {
                    $query->where('status', 'completed')
                          ->whereDate('created_at', today());
                },
                'sales as today_transactions' => function($query) {
                    $query->where('status', 'completed')
                          ->whereDate('created_at', today());
                }
            ])
            ->latest()
            ->take(4) // Show only 4 employees on dashboard
            ->get();

        return view('admin.dashboard', compact(
            'todaySales', 
            'weeklySales', 
            'criticalProductsCount', 
            'totalProducts', 
            'recentAlerts', 
            'recentTransactions',
            'employees'
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
        // Use pagination so the view can use paginator helpers (total(), hasPages(), etc.)
        $productsQuery = Product::with(['inventoryTransactions', 'adminMessages'])->orderBy('product_name');
        $products = $productsQuery->paginate(10);

        // Critical products list for alerts (compute via model accessor to avoid DB column requirement)
        $allProductsForAlerts = Product::orderBy('product_name')->get();
        $criticalProducts = $allProductsForAlerts->filter(function($product) {
            return $product->is_critical;
        });

        // Card statistics (use queries where possible, but avoid is_critical DB column)
        $normalStockCount = Product::where('is_active', true)
            ->where(function($q) {
                $q->where('current_stock_sacks', '>', 0)
                  ->orWhere('current_stock_pieces', '>', 0);
            })
            ->get()
            ->filter(function($p) { return !$p->is_critical; })
            ->count();

        $criticalStockCount = $allProductsForAlerts->filter(function($p){ return $p->is_critical; })->count();

        $outOfStockCount = Product::where('is_active', true)
            ->where('current_stock_sacks', '<=', 0)
            ->where('current_stock_pieces', '<=', 0)
            ->count();

        return view('admin.inventory-monitor', compact(
            'products',
            'criticalProducts',
            'normalStockCount',
            'criticalStockCount',
            'outOfStockCount'
        ));
    }

    // Sessions and activity
    public function sessions()
    {
        $sessions = \App\Models\SessionTrack::with('user')
            ->orderBy('last_activity_at', 'desc')
            ->paginate(25);

        return view('admin.sessions', compact('sessions'));
    }

    public function exportSessions()
    {
        $sessions = \App\Models\SessionTrack::with('user')->orderBy('last_activity_at', 'desc')->get();
        $csv = "user_id,user,ip,user_agent,started_at,last_activity_at,ended_at,is_idle\n";
        foreach ($sessions as $s) {
            $csv .= "{$s->user_id},\"{$s->user?->name}\",\"{$s->ip_address}\",\"{$s->user_agent}\",{$s->started_at},{$s->last_activity_at},{$s->ended_at},{$s->is_idle}\n";
        }
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sessions.csv"'
        ]);
    }

    public function activityLogs(Request $request)
    {
        $query = \App\Models\EmployeeLog::with('user')->orderBy('logged_at', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('event')) {
            $query->where('event', 'like', "%{$request->event}%");
        }

        $logs = $query->paginate(10);

        return view('admin.activity-logs', compact('logs'));
    }

    public function productivityReport(Request $request)
    {
        $start = $request->get('start_date', now()->subDays(7)->toDateString());
        $end = $request->get('end_date', now()->toDateString());

        $metrics = \App\Models\ProductivityMetric::whereBetween('date', [$start, $end])
            ->with('user')
            ->orderBy('date', 'desc')
            ->get();

        return view('admin.sales-report', compact('metrics', 'start', 'end'));
    }

    public function exportProductivityReport(Request $request)
    {
        $start = $request->get('start_date', now()->subDays(7)->toDateString());
        $end = $request->get('end_date', now()->toDateString());

        $metrics = \App\Models\ProductivityMetric::whereBetween('date', [$start, $end])->with('user')->get();

        $csv = "date,user_id,user,metrics\n";
        foreach ($metrics as $m) {
            $csv .= "{$m->date},{$m->user_id},\"{$m->user?->name}\",\"".addslashes(json_encode($m->metrics))."\"\n";
        }

        $filename = "productivity-{$start}-to-{$end}.csv";
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'message' => 'required|string|max:1000'
        ]);

        AdminMessage::create([
            'product_id' => $request->product_id,
            'admin_id' => auth()->id(),
            'message' => $request->message,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Message sent to inventory team successfully!');
    }

    public function getMessages()
    {
        $messages = AdminMessage::with(['product', 'admin'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($messages);
    }

    // Employee Monitoring Methods
    public function employeeMonitoring()
    {
        $employees = User::where('role', '!=', 'admin')
            ->withCount([
                'sales as today_sales' => function($query) {
                    $query->where('status', 'completed')
                          ->whereDate('created_at', today());
                },
                'sales as today_transactions' => function($query) {
                    $query->where('status', 'completed')
                          ->whereDate('created_at', today());
                },
                'sales as weekly_sales' => function($query) {
                    $query->where('status', 'completed')
                          ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                },
                'sales as monthly_sales' => function($query) {
                    $query->where('status', 'completed')
                          ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                }
            ])
            ->with(['sales' => function($query) {
                $query->where('status', 'completed')
                      ->whereDate('created_at', today())
                      ->latest()
                      ->take(5);
            }])
            ->latest()
            ->get();

        return view('admin.employee-monitoring', compact('employees'));
    }

    public function getEmployeeDetails($id)
    {
        $employee = User::withCount([
                'sales as total_sales_count' => function($query) {
                    $query->where('status', 'completed');
                },
                'sales as today_sales_count' => function($query) {
                    $query->where('status', 'completed')
                          ->whereDate('created_at', today());
                },
                'sales as weekly_sales_count' => function($query) {
                    $query->where('status', 'completed')
                          ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                },
                'sales as monthly_sales_count' => function($query) {
                    $query->where('status', 'completed')
                          ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                }
            ])
            ->with(['sales' => function($query) {
                $query->where('status', 'completed')
                      ->latest()
                      ->take(10);
            }])
            ->findOrFail($id);

        // Calculate additional metrics
        $totalSalesAmount = $employee->sales->where('status', 'completed')->sum('total_amount');
        $todaySalesAmount = $employee->sales->where('status', 'completed')
            ->where('created_at', '>=', today())
            ->sum('total_amount');

        return view('admin.partials.employee-details', compact('employee', 'totalSalesAmount', 'todaySalesAmount'));
    }

    public function getEmployeePerformance($id)
    {
        $employee = User::findOrFail($id);
        
        // Get performance data for the last 30 days
        $performanceData = Order::where('cashier_id', $id)
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as transactions, SUM(total_amount) as total_sales')
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get();

        // Calculate performance metrics
        $totalSales30Days = $performanceData->sum('total_sales');
        $totalTransactions30Days = $performanceData->sum('transactions');
        $averageSale30Days = $totalTransactions30Days > 0 ? $totalSales30Days / $totalTransactions30Days : 0;
        $activeDays = $performanceData->count();

        return view('admin.partials.employee-performance', compact(
            'employee', 
            'performanceData',
            'totalSales30Days',
            'totalTransactions30Days',
            'averageSale30Days',
            'activeDays'
        ));
    }

    // Method to get employee's recent transactions (for AJAX)
    public function getEmployeeTransactions($id)
    {
        $transactions = Order::where('cashier_id', $id)
            ->where('status', 'completed')
            ->with(['items.product'])
            ->latest()
            ->take(20)
            ->get();

        return response()->json($transactions);
    }

    // Method to update employee status (if you want to track online/offline status)
    public function updateEmployeeStatus(Request $request)
    {
        $user = auth()->user();
        $user->last_seen = now();
        $user->save();

        return response()->json(['status' => 'success']);
    }
}