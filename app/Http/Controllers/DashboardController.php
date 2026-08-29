<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $timeframe = $request->input('timeframe', 'all_time');

        // Determine Start & End Dates based on selected timeframe
        $startDate = null;
        $endDate = null;

        switch ($timeframe) {
            case 'today':
                $startDate = Carbon::today()->startOfDay();
                $endDate = Carbon::today()->endOfDay();
                break;
            case 'yesterday':
                $startDate = Carbon::yesterday()->startOfDay();
                $endDate = Carbon::yesterday()->endOfDay();
                break;
            case 'last_7_days':
                $startDate = Carbon::now()->subDays(7)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                break;
            case 'last_30_days':
                $startDate = Carbon::now()->subDays(30)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'last_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'all_time':
            default:
                $timeframe = 'all_time';
                break;
        }

        // 1. Current Real-time Warehouse Inventory Metrics (Always Live)
        $totalProducts = Product::where('is_active', true)->count();

        $lowStockCount = Product::where('is_active', true)
            ->where(function ($q) {
                $q->whereColumn('stock_quantity', '<=', 'min_stock_level')
                  ->orWhere('stock_quantity', '<', 10);
            })
            ->count();

        $outOfStockCount = Product::where('is_active', true)
            ->where('stock_quantity', 0)
            ->count();

        $totalCostValue = Product::where('is_active', true)
            ->selectRaw('SUM(stock_quantity * cost_price) as cost_val')
            ->value('cost_val') ?? 0;

        $totalRetailValue = Product::where('is_active', true)
            ->selectRaw('SUM(stock_quantity * COALESCE(selling_price, cost_price)) as retail_val')
            ->value('retail_val') ?? 0;

        $totalStaffCount = User::whereIn('role', ['super_admin', 'admin', 'staff'])->count();

        // 2. Timeframe-Filtered Orders Analytics
        $orderQuery = Order::query();
        if ($startDate && $endDate) {
            $orderQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $totalOrdersCount = (clone $orderQuery)->count();
        $pendingOrdersCount = (clone $orderQuery)->where('status', 'pending')->count();
        $completedOrdersCount = (clone $orderQuery)->where('status', 'completed')->count();

        $orderRevenue = (clone $orderQuery)
            ->where(function ($q) {
                $q->whereIn('status', ['completed', 'processing'])
                  ->orWhere('payment_status', 'paid');
            })
            ->sum('total_amount') ?? 0;

        // 3. Timeframe-Filtered Stock Movements & Revenue
        $movementQuery = StockMovement::query();
        if ($startDate && $endDate) {
            $movementQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $salesMovementRevenue = (clone $movementQuery)
            ->where('type', 'out_sale')
            ->selectRaw('SUM(quantity * unit_cost) as sales_val')
            ->value('sales_val') ?? 0;

        $totalSalesRevenue = max($orderRevenue, $salesMovementRevenue);

        $movementCounts = (clone $movementQuery)
            ->select('type', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('type')
            ->pluck('total_qty', 'type');

        // 4. Critical Low Stock Items (< 10 units)
        $lowStockProducts = Product::with(['category', 'supplier'])
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereColumn('stock_quantity', '<=', 'min_stock_level')
                  ->orWhere('stock_quantity', '<', 10);
            })
            ->orderBy('stock_quantity', 'asc')
            ->take(6)
            ->get();

        // 5. Recent Stock Ledger Movements (Filtered by Date if selected)
        $recentMovements = (clone $movementQuery)
            ->with(['product.category', 'user'])
            ->latest()
            ->take(8)
            ->get();

        $timeframeLabels = [
            'all_time'     => 'All Time',
            'today'        => 'Today',
            'yesterday'    => 'Yesterday',
            'last_7_days'  => 'Last 7 Days',
            'last_30_days' => 'Last 30 Days',
            'this_month'   => 'This Month',
            'last_month'   => 'Last Month',
        ];

        return view('dashboard', compact(
            'timeframe',
            'timeframeLabels',
            'totalProducts',
            'lowStockCount',
            'outOfStockCount',
            'totalCostValue',
            'totalRetailValue',
            'totalSalesRevenue',
            'totalOrdersCount',
            'pendingOrdersCount',
            'completedOrdersCount',
            'totalStaffCount',
            'movementCounts',
            'lowStockProducts',
            'recentMovements'
        ));
    }

    public function clearCache(Request $request)
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('route:clear');
            \Illuminate\Support\Facades\Artisan::call('config:clear');

            $previousUrl = url()->previous();
            $redirectUrl = \Illuminate\Support\Str::contains($previousUrl, 'cache_cleared=1')
                ? $previousUrl
                : (\Illuminate\Support\Str::contains($previousUrl, '?') ? $previousUrl . '&cache_cleared=1' : $previousUrl . '?cache_cleared=1');

            return redirect($redirectUrl)
                ->with('success', 'System cache cleared successfully! (Application, View, Route, and Config caches flushed).');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to clear system cache: ' . $e->getMessage());
        }
    }
}
