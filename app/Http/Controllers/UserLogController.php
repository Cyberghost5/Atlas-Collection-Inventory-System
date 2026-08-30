<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserLog;
use Illuminate\Http\Request;

class UserLogController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Only Admins can access the User Activity Logs.');
        }

        $query = UserLog::with('user');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        // Search in description or IP
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->latest()->paginate(25);
        $users = User::whereIn('role', ['super_admin', 'admin', 'staff'])->orderBy('name')->get();

        $actionTypes = [
            'login'            => 'User Login',
            'logout'           => 'User Logout',
            'order_created'    => 'Order Created',
            'product_created'  => 'Product Added',
            'product_updated'  => 'Product Updated',
            'product_deleted'  => 'Product Deleted',
            'stock_adjusted'   => 'Stock Adjusted',
            'cache_cleared'    => 'Cache Cleared',
        ];

        return view('user_logs.index', compact('logs', 'users', 'actionTypes'));
    }
}
