<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'customer')->withCount('orders')->withSum('orders as total_spent', 'total_amount');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $sort = $request->input('sort', 'newest');
        if ($sort === 'ltv_desc') {
            $query->orderByDesc('total_spent');
        } elseif ($sort === 'orders_desc') {
            $query->orderByDesc('orders_count');
        } else {
            $query->latest();
        }

        $perPage = (int) $request->input('per_page', 15);
        if (!in_array($perPage, [15, 25, 50, 100, 250, 500])) {
            $perPage = 15;
        }

        $customers = $query->paginate($perPage)->withQueryString();
        $totalCustomersCount = User::where('role', 'customer')->count();

        return view('customers.index', compact('customers', 'totalCustomersCount', 'sort'));
    }

    public function show($identifier)
    {
        $customer = User::where('role', 'customer')
            ->where(function ($q) use ($identifier) {
                $q->where('phone', $identifier)->orWhere('id', $identifier);
            })
            ->with(['orders.orderItems.product.category'])
            ->withCount('orders')
            ->withSum('orders as total_spent', 'total_amount')
            ->firstOrFail();

        // Calculate favorite category based on items bought
        $categoryCounts = [];
        foreach ($customer->orders as $order) {
            foreach ($order->orderItems as $item) {
                if ($item->product && $item->product->category) {
                    $catName = $item->product->category->name;
                    $categoryCounts[$catName] = ($categoryCounts[$catName] ?? 0) + $item->quantity;
                }
            }
        }

        arsort($categoryCounts);
        $favoriteCategory = !empty($categoryCounts) ? array_key_first($categoryCounts) : 'N/A';

        return view('customers.show', compact('customer', 'favoriteCategory'));
    }

    public function edit($identifier)
    {
        $customer = User::where('role', 'customer')
            ->where(function ($q) use ($identifier) {
                $q->where('phone', $identifier)->orWhere('id', $identifier);
            })
            ->firstOrFail();

        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, $identifier)
    {
        $customer = User::where('role', 'customer')
            ->where(function ($q) use ($identifier) {
                $q->where('phone', $identifier)->orWhere('id', $identifier);
            })
            ->firstOrFail();

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20|unique:users,phone,' . $customer->id,
            'email'   => 'nullable|email|max:255|unique:users,email,' . $customer->id,
            'address' => 'nullable|string|max:500',
        ]);

        $oldPhone = $customer->phone;
        $customer->update($validated);

        // If phone changed, sync customer_phone and customer_name on past order ledgers
        if ($oldPhone && $oldPhone !== $validated['phone']) {
            \App\Models\Order::where('customer_phone', $oldPhone)->update([
                'customer_phone' => $validated['phone'],
                'customer_name'  => $validated['name'],
            ]);
        }

        return redirect()->route('customers.show', $customer->phone ?? $customer->id)
            ->with('success', "Customer details for {$customer->name} updated successfully.");
    }

    public function destroy($identifier)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'Only Admins can delete customer records.');
        }

        $customer = User::where('role', 'customer')
            ->where(function ($q) use ($identifier) {
                $q->where('phone', $identifier)->orWhere('id', $identifier);
            })
            ->firstOrFail();
            
        $name = $customer->name;
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', "Customer profile for {$name} deleted successfully.");
    }
}
