<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StorefrontController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->where('is_active', true)
            ->retail(); // Show retail and dual-usage items

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('color', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        } elseif ($request->filled('category')) {
            $cat = Category::where('slug', $request->category)->first();
            if ($cat) {
                $query->where('category_id', $cat->id);
            }
        }

        if ($request->filled('size')) {
            $query->where('size', $request->size);
        }

        if ($request->boolean('in_stock_only')) {
            $query->where('stock_quantity', '>', 0);
        }

        switch ($request->get('sort')) {
            case 'price_asc':
                $query->orderBy('selling_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('selling_price', 'desc');
                break;
            case 'stock_desc':
                $query->orderBy('stock_quantity', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $perPage = (int) $request->input('per_page', 12);
        if (!in_array($perPage, [12, 24, 48, 96])) {
            $perPage = 12;
        }

        $products = $query->paginate($perPage)->withQueryString();
        $categories = Category::withCount('products')->get();

        if ($request->ajax()) {
            return response()->json([
                'html'         => view('shop.partials.product_cards', compact('products'))->render(),
                'has_more'     => $products->hasMorePages(),
                'next_page'    => $products->currentPage() + 1,
                'current_page' => $products->currentPage(),
                'total'        => $products->total(),
                'count'        => $products->count(),
            ]);
        }

        return view('shop.index', compact('products', 'categories'));
    }

    public function categories(Request $request)
    {
        $categories = Category::withCount(['products' => function ($q) {
            $q->where('is_active', true)->retail();
        }])->get();

        $iconMap = [
            'clothes'     => 'fa-shirt',
            'perfumes'    => 'fa-spray-can-sparkles',
            'shoes'       => 'fa-shoe-prints',
            'bags'        => 'fa-bag-shopping',
            'watches'     => 'fa-clock',
            'jewelry'     => 'fa-gem',
            'accessories' => 'fa-glasses',
        ];

        return view('shop.categories', compact('categories', 'iconMap'));
    }

    public function categoryShow($slug, Request $request)
    {
        $category = Category::where('slug', $slug)
            ->orWhere('id', $slug)
            ->firstOrFail();

        $query = Product::with('category')
            ->where('is_active', true)
            ->where('category_id', $category->id)
            ->retail();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('color', 'like', "%{$search}%");
            });
        }

        if ($request->filled('size')) {
            $query->where('size', $request->size);
        }

        if ($request->boolean('in_stock_only')) {
            $query->where('stock_quantity', '>', 0);
        }

        switch ($request->get('sort')) {
            case 'price_asc':
                $query->orderBy('selling_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('selling_price', 'desc');
                break;
            case 'stock_desc':
                $query->orderBy('stock_quantity', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $perPage = (int) $request->input('per_page', 12);
        if (!in_array($perPage, [12, 24, 48, 96])) {
            $perPage = 12;
        }

        $products = $query->paginate($perPage)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html'         => view('shop.partials.product_cards', compact('products'))->render(),
                'has_more'     => $products->hasMorePages(),
                'next_page'    => $products->currentPage() + 1,
                'current_page' => $products->currentPage(),
                'total'        => $products->total(),
                'count'        => $products->count(),
            ]);
        }

        $categories = Category::withCount('products')->get();

        return view('shop.category_show', compact('category', 'products', 'categories'));
    }

    public function show($slug)
    {
        $product = Product::with(['category', 'supplier'])
            ->where('slug', $slug)
            ->orWhere('id', $slug)
            ->firstOrFail();
        
        // Find other size variants of the same product line
        $variants = Product::where('name', $product->name)
            ->where('id', '!=', $product->id)
            ->get();

        return view('shop.show', compact('product', 'variants'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'product_id'       => 'required|exists:products,id',
            'quantity'         => 'required|integer|min:1',
            'customer_name'    => 'required|string|max:255',
            'customer_email'   => 'required|email|max:255',
            'customer_phone'   => 'required|string|max:20',
            'shipping_address' => 'required|string|max:1000',
            'notes'            => 'nullable|string|max:500',
        ]);

        try {
            $orderNumber = 'AUC-ORD-' . date('Y') . '-' . strtoupper(Str::random(5));

            DB::transaction(function () use ($request, $orderNumber) {
                // 1. Lock product row for atomic stock check and decrement
                $product = Product::where('id', $request->product_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($product->stock_quantity < $request->quantity) {
                    throw new \InvalidArgumentException("Only {$product->stock_quantity} unit(s) available in stock for Size {$product->size}.");
                }

                $unitPrice = $product->selling_price ?? $product->cost_price;
                $subtotal = $unitPrice * $request->quantity;

                // 2. Create Order
                $order = Order::create([
                    'order_number'     => $orderNumber,
                    'user_id'          => Auth::check() && Auth::user()->isCustomer() ? Auth::id() : null,
                    'customer_name'    => $request->customer_name,
                    'customer_email'   => $request->customer_email,
                    'customer_phone'   => $request->customer_phone,
                    'shipping_address' => $request->shipping_address,
                    'total_amount'     => $subtotal,
                    'status'           => 'pending',
                    'payment_status'   => 'paid',
                    'notes'            => $request->notes,
                ]);

                // Create Payment Transaction Record
                \App\Models\Transaction::create([
                    'transaction_number' => 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
                    'order_id'           => $order->id,
                    'staff_id'           => null,
                    'customer_name'      => $request->customer_name,
                    'customer_phone'     => $request->customer_phone,
                    'customer_email'     => $request->customer_email,
                    'amount'             => $subtotal,
                    'payment_method'     => 'bank_transfer',
                    'payment_status'     => 'paid',
                    'notes'              => "Online storefront checkout for order #{$orderNumber}",
                ]);

                // 3. Create Line Item
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'quantity'   => $request->quantity,
                    'unit_price' => $unitPrice,
                    'subtotal'   => $subtotal,
                ]);

                // 4. Deduct Product Stock Level
                $product->decrement('stock_quantity', $request->quantity);

                // 5. Log Inventory Audit Trail
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id'    => Auth::id(),
                    'type'       => 'out_sale',
                    'quantity'   => $request->quantity,
                    'unit_cost'  => $product->cost_price,
                    'notes'      => "Customer E-Commerce Order #{$orderNumber}",
                ]);
            });

            return redirect()->route('shop.order-status', $orderNumber)
                ->with('success', 'Order placed successfully! Your order has been registered for delivery in Nigeria.');

        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->withErrors(['quantity' => $e->getMessage()])
                ->withInput();
        }
    }

    public function trackOrder($orderNumber)
    {
        $order = Order::with('orderItems.product.category')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('shop.order_status', compact('order'));
    }

    public function myOrders()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view your order history.');
        }

        $orders = Order::with('orderItems.product')
            ->where('user_id', Auth::id())
            ->orWhere('customer_phone', Auth::user()->phone)
            ->latest()
            ->paginate(10);

        return view('shop.my_orders', compact('orders'));
    }
}
