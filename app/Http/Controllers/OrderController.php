<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\LowStockNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    protected LowStockNotificationService $notificationService;

    public function __construct(LowStockNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $query = Order::with(['user', 'orderItems.product']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        $pendingCount = Order::where('status', 'pending')->count();
        $processingCount = Order::where('status', 'processing')->count();
        $completedCount = Order::where('status', 'completed')->count();
        $cancelledCount = Order::where('status', 'cancelled')->count();

        return view('orders.index', compact('orders', 'pendingCount', 'processingCount', 'completedCount', 'cancelledCount'));
    }

    public function create()
    {
        $existingCustomers = User::where('role', 'customer')
            ->orderBy('name')
            ->get();

        $products = Product::where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->with('category')
            ->orderBy('name')
            ->get();

        return view('orders.create', compact('existingCustomers', 'products'));
    }

    public function store(Request $request, LowStockNotificationService $notificationService)
    {
        $validated = $request->validate([
            'customer_type'    => 'required|in:new,existing',
            'customer_id'      => 'nullable|exists:users,id',
            'customer_name'    => 'required_if:customer_type,new|nullable|string|max:255',
            'customer_phone'   => 'required_if:customer_type,new|nullable|string|max:20',
            'customer_email'   => 'nullable|email|max:255',
            'shipping_address' => 'required|string|max:1000',
            'payment_method'   => 'required|in:cash,bank_transfer,pos,other',
            'payment_status'   => 'required|in:paid,unpaid',
            'status'           => 'required|in:pending,processing,completed,cancelled',
            'notes'            => 'nullable|string|max:500',
            'payment_proof'    => 'nullable|file|mimes:jpeg,jpg,png,pdf,webp|max:5120',
            'items'            => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $userId = null;
        $customerName = '';
        $customerPhone = '';
        $customerEmail = '';

        if ($validated['customer_type'] === 'existing' && !empty($validated['customer_id'])) {
            $user = User::findOrFail($validated['customer_id']);
            $userId = $user->id;
            $customerName = $user->name;
            $customerPhone = $user->phone;
            $customerEmail = $user->email;
        } else {
            $customerName = $validated['customer_name'];
            $customerPhone = $validated['customer_phone'];
            $customerEmail = $validated['customer_email'] ?? '';

            if (!empty($customerPhone)) {
                $existingUser = User::where('phone', $customerPhone)->first();
                if ($existingUser) {
                    $userId = $existingUser->id;
                } else {
                    $newUser = User::create([
                        'name'     => $customerName,
                        'phone'    => $customerPhone,
                        'email'    => !empty($validated['customer_email']) ? $validated['customer_email'] : 'cust_' . time() . rand(10, 99) . '@atlasunisex.com',
                        'password' => bcrypt('password123'),
                        'role'     => 'customer',
                    ]);
                    $userId = $newUser->id;
                }
            }
        }

        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $fileName = 'proof_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/payment_proofs'), $fileName);
            $proofPath = 'uploads/payment_proofs/' . $fileName;
        }

        $order = null;

        DB::transaction(function () use ($validated, $userId, $customerName, $customerPhone, $customerEmail, $proofPath, &$order, $notificationService) {
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $totalAmount += ($item['quantity'] * $item['unit_price']);
            }

            $orderNumber = 'AUC-ORD-' . date('Y') . '-' . strtoupper(Str::random(5));

            $order = Order::create([
                'order_number'     => $orderNumber,
                'user_id'          => $userId,
                'customer_name'    => $customerName,
                'customer_phone'   => $customerPhone,
                'customer_email'   => $customerEmail,
                'shipping_address' => $validated['shipping_address'],
                'total_amount'     => $totalAmount,
                'status'           => $validated['status'],
                'payment_status'   => $validated['payment_status'],
                'payment_method'   => $validated['payment_method'],
                'payment_proof'    => $proofPath,
                'notes'            => $validated['notes'] ?? null,
            ]);

            \App\Models\Transaction::create([
                'transaction_number' => 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
                'order_id'           => $order->id,
                'staff_id'           => auth()->id(),
                'customer_name'      => $customerName,
                'customer_phone'     => $customerPhone,
                'customer_email'     => $customerEmail,
                'amount'             => $totalAmount,
                'payment_method'     => $validated['payment_method'],
                'payment_status'     => $validated['payment_status'],
                'payment_proof'      => $proofPath,
                'notes'              => $validated['notes'] ?? "Payment for order #{$orderNumber}",
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $subtotal = $item['quantity'] * $item['unit_price'];

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal'   => $subtotal,
                ]);

                if ($validated['status'] !== 'cancelled') {
                    $product->decrement('stock_quantity', $item['quantity']);

                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id'    => auth()->id(),
                        'type'       => 'out_sale',
                        'quantity'   => $item['quantity'],
                        'unit_cost'  => $product->cost_price,
                        'notes'      => "Staff sale logged / Order #{$orderNumber}",
                    ]);

                    $notificationService->checkAndNotify($product);
                }
            }
        });

        return redirect()->route('orders.show', $order->order_number)
            ->with('success', "Sale order #{$order->order_number} logged successfully!");
    }

    public function show($identifier)
    {
        $order = Order::with(['user', 'orderItems.product.category'])
            ->where('order_number', $identifier)
            ->orWhere('id', $identifier)
            ->firstOrFail();

        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $identifier)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
            'notes'  => 'nullable|string|max:500',
        ]);

        $order = Order::with('orderItems.product')
            ->where('order_number', $identifier)
            ->orWhere('id', $identifier)
            ->firstOrFail();

        $oldStatus = $order->status;
        $newStatus = $request->status;

        if ($oldStatus === $newStatus) {
            if ($request->filled('notes')) {
                $order->update(['notes' => $request->notes]);
            }
            return redirect()->back()->with('success', "Order #{$order->order_number} notes updated.");
        }

        try {
            DB::transaction(function () use ($order, $oldStatus, $newStatus, $request) {
                if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                    foreach ($order->orderItems as $item) {
                        if ($item->product) {
                            $item->product->increment('stock_quantity', $item->quantity);

                            StockMovement::create([
                                'product_id' => $item->product_id,
                                'user_id'    => auth()->id(),
                                'type'       => 'in',
                                'quantity'   => $item->quantity,
                                'unit_cost'  => $item->product->cost_price,
                                'notes'      => "Order #{$order->order_number} cancelled (Stock Restored)",
                            ]);
                        }
                    }
                }

                if ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
                    foreach ($order->orderItems as $item) {
                        if ($item->product && $item->product->stock_quantity < $item->quantity) {
                            throw new \InvalidArgumentException("Cannot reactivate order #{$order->order_number}: Insufficient stock for product {$item->product->name} (Available: {$item->product->stock_quantity}, Needed: {$item->quantity}).");
                        }
                    }

                    foreach ($order->orderItems as $item) {
                        if ($item->product) {
                            $item->product->decrement('stock_quantity', $item->quantity);

                            StockMovement::create([
                                'product_id' => $item->product_id,
                                'user_id'    => auth()->id(),
                                'type'       => 'out_sale',
                                'quantity'   => $item->quantity,
                                'unit_cost'  => $item->product->cost_price,
                                'notes'      => "Order #{$order->order_number} reactivated from cancelled to {$newStatus}",
                            ]);
                        }
                    }
                }

                $order->update([
                    'status' => $newStatus,
                    'notes'  => $request->notes ?? $order->notes,
                ]);
            });

            return redirect()->back()
                ->with('success', "Order #{$order->order_number} status updated to " . strtoupper($newStatus) . "!");

        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->withErrors(['status' => $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy($identifier)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'Only Admins can delete order records.');
        }

        $order = Order::with('orderItems.product')
            ->where('order_number', $identifier)
            ->orWhere('id', $identifier)
            ->firstOrFail();

        DB::transaction(function () use ($order) {
            if ($order->status !== 'cancelled') {
                foreach ($order->orderItems as $item) {
                    if ($item->product) {
                        $item->product->increment('stock_quantity', $item->quantity);

                        StockMovement::create([
                            'product_id' => $item->product_id,
                            'user_id'    => auth()->id(),
                            'type'       => 'in',
                            'quantity'   => $item->quantity,
                            'unit_cost'  => $item->product->cost_price,
                            'notes'      => "Order #{$order->order_number} deleted by admin (Stock restored)",
                        ]);
                    }
                }
            }

            $order->orderItems()->delete();

            if ($order->payment_proof && file_exists(public_path($order->payment_proof))) {
                @unlink(public_path($order->payment_proof));
            }

            $order->delete();
        });

        return redirect()->route('orders.index')
            ->with('success', "Order #{$order->order_number} deleted and stock restored.");
    }

    public function receipt($identifier, Request $request)
    {
        $order = Order::with(['orderItems.product.category', 'user'])
            ->where('order_number', $identifier)
            ->orWhere('id', $identifier)
            ->firstOrFail();
        $format = $request->input('format', 'pos');

        return view('orders.receipt', compact('order', 'format'));
    }
}
