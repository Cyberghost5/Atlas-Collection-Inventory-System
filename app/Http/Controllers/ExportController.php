<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Export Inventory Catalog & Stock Levels to CSV
     */
    public function exportProducts(Request $request): StreamedResponse
    {
        $fileName = 'atlas_inventory_export_' . date('Y-m-d_H-i') . '.csv';

        $query = Product::with(['category', 'supplier']);

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('sku', 'like', "%{$s}%")
                  ->orWhere('size', 'like', "%{$s}%")
                  ->orWhere('color', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('usage_type')) {
            $query->where('usage_type', $request->input('usage_type'));
        }

        $products = $query->latest()->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            // CSV Header Row
            fputcsv($file, [
                'SKU',
                'Product Name',
                'Category',
                'Variant Size',
                'Colorway',
                'Stock Type',
                'Cost Price (NGN)',
                'Selling Price (NGN)',
                'Stock Quantity',
                'Unit',
                'Low Stock Warning',
                'Supplier Name',
                'Created Date'
            ]);

            foreach ($products as $p) {
                fputcsv($file, [
                    $p->sku,
                    $p->name,
                    $p->category->name ?? 'Uncategorized',
                    $p->size ?? 'Standard',
                    $p->color ?? 'Standard',
                    str_replace('_', ' ', strtoupper($p->usage_type ?? 'retail')),
                    number_format($p->cost_price, 2, '.', ''),
                    number_format($p->selling_price ?? $p->cost_price, 2, '.', ''),
                    $p->stock_quantity,
                    $p->unit ?? 'unit',
                    $p->is_low_stock ? 'YES' : 'NO',
                    $p->supplier->name ?? 'N/A',
                    $p->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Orders Sales History Ledger to CSV
     */
    public function exportOrders(Request $request): StreamedResponse
    {
        $fileName = 'atlas_orders_ledger_export_' . date('Y-m-d_H-i') . '.csv';

        $query = Order::with(['orderItems.product']);

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('order_number', 'like', "%{$s}%")
                  ->orWhere('customer_name', 'like', "%{$s}%")
                  ->orWhere('customer_phone', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        $orders = $query->latest()->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'Order Ref Number',
                'Customer Name',
                'Customer Phone',
                'Customer Email',
                'Delivery Address',
                'Items Count',
                'Payment Method',
                'Payment Status',
                'Order Status',
                'Total Amount (NGN)',
                'Notes',
                'Order Date'
            ]);

            foreach ($orders as $o) {
                fputcsv($file, [
                    $o->order_number,
                    $o->customer_name,
                    $o->customer_phone,
                    $o->customer_email ?? 'N/A',
                    $o->shipping_address ?? 'Store Pickup',
                    $o->orderItems->count(),
                    str_replace('_', ' ', strtoupper($o->payment_method ?? 'cash')),
                    strtoupper($o->payment_status ?? 'paid'),
                    strtoupper($o->status ?? 'completed'),
                    number_format($o->total_amount, 2, '.', ''),
                    $o->notes ?? '',
                    $o->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Payment Transactions Ledger to CSV (Admin Only)
     */
    public function exportTransactions(Request $request): StreamedResponse
    {
        $fileName = 'atlas_transactions_ledger_export_' . date('Y-m-d_H-i') . '.csv';

        $query = Transaction::with(['order', 'user']);

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('transaction_number', 'like', "%{$s}%")
                  ->orWhere('customer_name', 'like', "%{$s}%")
                  ->orWhere('customer_phone', 'like', "%{$s}%");
            });
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        $transactions = $query->latest()->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'Transaction Ref',
                'Order Ref Number',
                'Customer Name',
                'Customer Phone',
                'Payment Method',
                'Amount Paid (NGN)',
                'Payment Status',
                'Processed By Staff',
                'Transaction Date'
            ]);

            foreach ($transactions as $t) {
                fputcsv($file, [
                    $t->transaction_number,
                    $t->order->order_number ?? 'N/A',
                    $t->customer_name,
                    $t->customer_phone,
                    str_replace('_', ' ', strtoupper($t->payment_method ?? 'cash')),
                    number_format($t->amount, 2, '.', ''),
                    strtoupper($t->status ?? 'completed'),
                    $t->user->name ?? 'System Admin',
                    $t->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Customer Directory & Order Lifetime Values to CSV
     */
    public function exportCustomers(Request $request): StreamedResponse
    {
        $fileName = 'atlas_customers_export_' . date('Y-m-d_H-i') . '.csv';

        $customers = User::where('role', 'customer')
            ->withCount('orders')
            ->withSum('orders as total_spent', 'total_amount')
            ->latest()
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () use ($customers) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'Customer ID',
                'Customer Name',
                'Contact Phone',
                'Email Address',
                'Shipping Address',
                'Total Orders Placed',
                'Total Lifetime Spent (NGN)',
                'Registration Date'
            ]);

            foreach ($customers as $c) {
                fputcsv($file, [
                    $c->id,
                    $c->name,
                    $c->phone ?? 'N/A',
                    $c->email ?? 'N/A',
                    $c->address ?? 'Store Pickup',
                    $c->orders_count ?? 0,
                    number_format($c->total_spent ?? 0, 2, '.', ''),
                    $c->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
