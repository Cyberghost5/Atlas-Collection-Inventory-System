<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Only Admins have permission to access Executive Reports & Analytics.');
        }

        $timeframe = $request->input('timeframe', 'all_time');
        $now = Carbon::now();
        $startDate = null;
        $endDate = null;

        switch ($timeframe) {
            case 'today':
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'yesterday':
                $startDate = $now->copy()->subDay()->startOfDay();
                $endDate = $now->copy()->subDay()->endOfDay();
                break;
            case 'last_7_days':
                $startDate = $now->copy()->subDays(6)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'last_30_days':
                $startDate = $now->copy()->subDays(29)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'this_month':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                break;
            case 'last_month':
                $startDate = $now->copy()->subMonth()->startOfMonth();
                $endDate = $now->copy()->subMonth()->endOfMonth();
                break;
            case 'this_year':
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                break;
            case 'all_time':
            default:
                $startDate = null;
                $endDate = null;
                break;
        }

        // Base Orders Query within Timeframe
        $ordersQuery = Order::query()->where('status', '!=', 'cancelled');
        $transactionsQuery = Transaction::query()->where('payment_status', 'paid');
        $orderItemsQuery = OrderItem::query()->whereHas('order', function ($q) {
            $q->where('status', '!=', 'cancelled');
        });

        if ($startDate && $endDate) {
            $ordersQuery->whereBetween('created_at', [$startDate, $endDate]);
            $transactionsQuery->whereBetween('created_at', [$startDate, $endDate]);
            $orderItemsQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        // 1. KPI Summary Metrics
        $totalOrdersCount = (clone $ordersQuery)->count();
        $totalRevenue = (float) (clone $ordersQuery)->sum('total_amount');

        // Calculate Cost of Goods Sold & Profit
        $totalCostOfGoods = 0;
        $orderItems = (clone $orderItemsQuery)->with('product')->get();
        foreach ($orderItems as $item) {
            $cost = $item->product->cost_price ?? 0;
            $totalCostOfGoods += ($item->quantity * $cost);
        }

        $netProfit = $totalRevenue - $totalCostOfGoods;
        $avgOrderValue = $totalOrdersCount > 0 ? ($totalRevenue / $totalOrdersCount) : 0;

        // 2. Trend Over Time (Bar/Line Chart Data)
        $trendData = [];
        if ($startDate && $endDate && $startDate->diffInDays($endDate) <= 31) {
            // Group by Day
            $salesByDate = (clone $ordersQuery)
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total_revenue'))
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->pluck('total_revenue', 'date')
                ->toArray();

            $period = new \DatePeriod(
                $startDate->copy(),
                new \DateInterval('P1D'),
                $endDate->copy()->addDay()
            );

            foreach ($period as $dt) {
                $dStr = $dt->format('Y-m-d');
                $dLabel = $dt->format('M d');
                $trendData[$dLabel] = (float) ($salesByDate[$dStr] ?? 0);
            }
        } else {
            // Group by Month
            $salesByMonth = (clone $ordersQuery)
                ->select(DB::raw("STRFTIME('%Y-%m', created_at) as month"), DB::raw('SUM(total_amount) as total_revenue'))
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->pluck('total_revenue', 'month')
                ->toArray();

            if (empty($salesByMonth)) {
                $trendData[date('M Y')] = 0;
            } else {
                foreach ($salesByMonth as $mKey => $mRev) {
                    $mLabel = Carbon::parse($mKey . '-01')->format('M Y');
                    $trendData[$mLabel] = (float) $mRev;
                }
            }
        }

        // 3. Payment Methods Breakdown (Pie Chart Data)
        $paymentMethodsData = (clone $transactionsQuery)
            ->select('payment_method', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as total_count'))
            ->groupBy('payment_method')
            ->get();

        $pmLabels = [];
        $pmAmounts = [];
        $pmTable = [];

        $methodNames = [
            'cash'          => 'Cash Payment',
            'bank_transfer' => 'Bank Transfer',
            'pos'           => 'POS / Card Machine',
            'other'         => 'Other Payment',
        ];

        foreach (['cash', 'bank_transfer', 'pos', 'other'] as $mKey) {
            $found = $paymentMethodsData->firstWhere('payment_method', $mKey);
            $amt = $found ? (float) $found->total_amount : 0;
            $cnt = $found ? (int) $found->total_count : 0;
            $share = $totalRevenue > 0 ? (($amt / $totalRevenue) * 100) : 0;

            $pmLabels[] = $methodNames[$mKey];
            $pmAmounts[] = $amt;

            $pmTable[] = [
                'key'        => $mKey,
                'name'       => $methodNames[$mKey],
                'count'      => $cnt,
                'amount'     => $amt,
                'percentage' => round($share, 1),
            ];
        }

        // 4. Top 10 Products by Revenue (Bar Chart & Table)
        $topProductsQuery = (clone $orderItemsQuery)
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_rev'))
            ->groupBy('product_id')
            ->orderBy('total_rev', 'desc')
            ->take(10)
            ->with('product.category')
            ->get();

        $topProductLabels = [];
        $topProductRevenues = [];
        $topProductTable = [];

        foreach ($topProductsQuery as $tProd) {
            if ($tProd->product) {
                $pName = $tProd->product->name . ' (' . $tProd->product->size . ')';
                $rev = (float) $tProd->total_rev;
                $cost = (float) ($tProd->product->cost_price * $tProd->total_qty);
                $profit = $rev - $cost;

                $topProductLabels[] = $pName;
                $topProductRevenues[] = $rev;

                $topProductTable[] = [
                    'product'     => $tProd->product,
                    'name'        => $pName,
                    'category'    => $tProd->product->category->name ?? 'General',
                    'units_sold'  => $tProd->total_qty,
                    'revenue'     => $rev,
                    'profit'      => $profit,
                ];
            }
        }

        // 5. Top 10 Customers by Order Value Table
        $topCustomersQuery = Order::query()
            ->where('status', '!=', 'cancelled')
            ->select('customer_name', 'customer_phone', 'customer_email', DB::raw('COUNT(*) as total_orders'), DB::raw('SUM(total_amount) as total_spent'));

        if ($startDate && $endDate) {
            $topCustomersQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $topCustomers = $topCustomersQuery
            ->groupBy('customer_name', 'customer_phone')
            ->orderBy('total_spent', 'desc')
            ->take(10)
            ->get();

        // 6. Recent Orders (Last 20 in Period)
        $recentOrdersQuery = Order::query()->with('orderItems');
        if ($startDate && $endDate) {
            $recentOrdersQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $recentOrders = $recentOrdersQuery->latest()->take(20)->get();

        return view('reports.index', compact(
            'timeframe',
            'totalOrdersCount',
            'totalRevenue',
            'totalCostOfGoods',
            'netProfit',
            'avgOrderValue',
            'trendData',
            'pmLabels',
            'pmAmounts',
            'pmTable',
            'topProductLabels',
            'topProductRevenues',
            'topProductTable',
            'topCustomers',
            'recentOrders'
        ));
    }
}
