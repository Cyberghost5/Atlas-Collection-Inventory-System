<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Only Admins have permission to access the Payment Transactions Ledger.');
        }

        $query = Transaction::with(['order', 'staff']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($oq) use ($search) {
                      $oq->where('order_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('staff', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();

        $totalTransactionsCount = Transaction::count();
        $totalVolumeAmount = Transaction::where('payment_status', 'paid')->sum('amount');
        $todayVolumeAmount = Transaction::where('payment_status', 'paid')->whereDate('created_at', today())->sum('amount');

        return view('transactions.index', compact('transactions', 'totalTransactionsCount', 'totalVolumeAmount', 'todayVolumeAmount'));
    }

    public function show($identifier)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Only Admins have permission to view detailed payment transactions.');
        }

        $transaction = Transaction::with(['order.orderItems.product', 'staff'])
            ->where('transaction_number', $identifier)
            ->orWhere('id', $identifier)
            ->firstOrFail();

        return view('transactions.show', compact('transaction'));
    }
}
