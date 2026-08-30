<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Services\LowStockNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    protected LowStockNotificationService $notificationService;

    public function __construct(LowStockNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Only Admins have permission to access the Stock Audit Log.');
        }

        $perPage = (int) $request->input('per_page', 20);
        if (!in_array($perPage, [15, 20, 25, 50, 100, 250, 500])) {
            $perPage = 20;
        }

        $movements = StockMovement::with(['product.category', 'user'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('stock_movements.index', compact('movements'));
    }

    public function store(Request $request, Product $product)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'Only Admins have permission to log manual stock movements.');
        }

        $validated = $request->validate([
            'type'     => 'required|in:in,out_internal,out_sale,adjustment',
            'quantity' => 'required|integer|min:1',
            'notes'    => 'nullable|string|max:255',
        ]);

        $type = $validated['type'];
        $qty = $validated['quantity'];

        if (in_array($type, ['out_internal', 'out_sale']) && $product->stock_quantity < $qty) {
            return redirect()->back()
                ->withErrors(['quantity' => "Insufficient stock! Available collection stock is {$product->stock_quantity} {$product->unit}(s)."])
                ->withInput();
        }

        DB::transaction(function () use ($product, $type, $qty, $validated) {
            if ($type === 'in') {
                $product->increment('stock_quantity', $qty);
            } elseif (in_array($type, ['out_internal', 'out_sale'])) {
                $product->decrement('stock_quantity', $qty);
            } elseif ($type === 'adjustment') {
                $product->update(['stock_quantity' => $qty]);
            }

            StockMovement::create([
                'product_id' => $product->id,
                'user_id'    => auth()->id(),
                'type'       => $type,
                'quantity'   => $qty,
                'unit_cost'  => $product->cost_price,
                'notes'      => $validated['notes'] ?? null,
            ]);
        });

        // Trigger SMS & Email notification check if stock falls below 10
        $this->notificationService->checkAndNotify($product);

        $labels = [
            'in'           => 'Restocked batch of',
            'out_internal' => 'Allocated for display/seeding',
            'out_sale'     => 'Recorded retail sale of',
            'adjustment'   => 'Adjusted inventory count for',
        ];

        \App\Models\UserLog::log('stock_adjusted', "{$labels[$type]} {$qty} {$product->unit}(s) of '{$product->name}' (SKU: {$product->sku}).");

        return redirect()->back()
            ->with('success', "{$labels[$type]} {$qty} {$product->unit}(s) of {$product->name} (Size: {$product->size}).");
    }
}
