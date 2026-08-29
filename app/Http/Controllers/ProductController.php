<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Services\LowStockNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected $notificationService;

    public function __construct(LowStockNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'supplier'])->where('is_active', true);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('usage_type')) {
            $query->where('usage_type', $request->usage_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('color', 'like', "%{$search}%")
                  ->orWhere('size', 'like', "%{$search}%");
            });
        }

        if ($request->boolean('low_stock')) {
            $query->lowStock();
        }

        $products = $query->orderBy('name', 'asc')->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'suppliers'));
    }

    public function lowStock()
    {
        $allLowStockProducts = Product::with(['category', 'supplier'])
            ->where('is_active', true)
            ->lowStock()
            ->get();

        $supplierGroups = $allLowStockProducts->groupBy(function ($product) {
            return $product->supplier ? $product->supplier->name : 'Unassigned / Direct Suppliers';
        });

        $products = Product::with(['category', 'supplier'])
            ->where('is_active', true)
            ->lowStock()
            ->paginate(15);

        return view('products.low_stock', [
            'products'       => $products,
            'supplierGroups' => $supplierGroups,
            'totalLowCount'  => $allLowStockProducts->count(),
        ]);
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('products.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'     => 'required|exists:categories,id',
            'supplier_id'     => 'nullable|exists:suppliers,id',
            'name'            => 'required|string|max:255',
            'sku'             => 'nullable|string|max:100|unique:products,sku',
            'size'            => 'nullable|string|max:50',
            'color'           => 'nullable|string|max:100',
            'barcode'         => 'nullable|string|max:100',
            'description'     => 'nullable|string',
            'usage_type'      => 'required|in:retail,display_sample,both',
            'unit'            => 'required|string|max:50',
            'cost_price'      => 'required|numeric|min:0',
            'selling_price'   => 'nullable|numeric|min:0',
            'stock_quantity'  => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'image'           => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        if (empty($validated['size'])) {
            $validated['size'] = 'Standard';
        }

        if (empty($validated['sku'])) {
            $category = Category::find($validated['category_id']);
            $prefix = strtoupper(substr($category->name ?? 'AUC', 0, 3));
            $sizeCode = strtoupper(substr($validated['size'], 0, 3));
            $validated['sku'] = "AUC-{$prefix}-{$sizeCode}-" . strtoupper(Str::random(4));
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = 'prod_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $fileName);
            $validated['image'] = 'uploads/products/' . $fileName;
        }

        DB::transaction(function () use ($validated) {
            $product = Product::create($validated);

            if ($product->stock_quantity > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id'    => auth()->id(),
                    'type'       => 'in',
                    'quantity'   => $product->stock_quantity,
                    'unit_cost'  => $product->cost_price,
                    'notes'      => 'Initial collection inventory setup',
                ]);
            }
        });

        return redirect()->route('products.index')
            ->with('success', 'Apparel item added to Atlas Collection!');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'supplier', 'stockMovements.user']);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id'     => 'required|exists:categories,id',
            'supplier_id'     => 'nullable|exists:suppliers,id',
            'name'            => 'required|string|max:255',
            'sku'             => 'required|string|max:100|unique:products,sku,' . $product->id,
            'size'            => 'nullable|string|max:50',
            'color'           => 'nullable|string|max:100',
            'barcode'         => 'nullable|string|max:100',
            'description'     => 'nullable|string',
            'usage_type'      => 'required|in:retail,display_sample,both',
            'unit'            => 'required|string|max:50',
            'cost_price'      => 'required|numeric|min:0',
            'selling_price'   => 'nullable|numeric|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'image'           => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image && file_exists(public_path($product->image))) {
                @unlink(public_path($product->image));
            }
            $file = $request->file('image');
            $fileName = 'prod_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $fileName);
            $validated['image'] = 'uploads/products/' . $fileName;
        }

        $product->update($validated);

        // Check if updated product stock is below 10 and notify
        $this->notificationService->checkAndNotify($product);

        return redirect()->route('products.index')
            ->with('success', 'Apparel item updated successfully!');
    }

    public function destroy(Product $product)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'Only Admins can delete apparel inventory items.');
        }

        if ($product->image && file_exists(public_path($product->image))) {
            @unlink(public_path($product->image));
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Apparel item removed from collection.');
    }
}
