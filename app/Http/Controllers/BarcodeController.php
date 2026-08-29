<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\BarcodeService;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    /**
     * Display printable barcode & QR code price tag for a single product.
     */
    public function show(Product $product, Request $request)
    {
        $format = $request->input('format', 'a4'); // 'a4' grid sheet or 'thermal' 80mm roll
        $count = max(1, min(100, (int) $request->input('count', 1)));

        $barcodeSvg = BarcodeService::getCode128Svg($product->sku, 45, 1.6);
        $productUrl = route('shop.show', $product->slug);

        $labels = [];
        for ($i = 0; $i < $count; $i++) {
            $labels[] = [
                'product'    => $product,
                'barcodeSvg' => $barcodeSvg,
                'productUrl' => $productUrl,
            ];
        }

        return view('products.barcode', compact('product', 'format', 'count', 'labels'));
    }

    /**
     * Display printable bulk sticker sheet for multiple selected catalog items.
     */
    public function printBulk(Request $request)
    {
        $quantities = $request->input('quantities', []); // [product_id => qty]
        $selectedIds = $request->input('product_ids', []);
        $format = $request->input('format', 'a4');

        if (empty($selectedIds) && empty($quantities)) {
            return redirect()->route('products.index')->with('error', 'Please select at least one catalog item from inventory to generate price tag labels.');
        }

        if (empty($selectedIds)) {
            $selectedIds = array_keys($quantities);
        }

        $products = Product::with('category')->whereIn('id', $selectedIds)->get();

        if ($products->isEmpty()) {
            return redirect()->route('products.index')->with('error', 'No valid catalog items found for price tag label generation.');
        }

        $labels = [];
        foreach ($products as $product) {
            $qty = isset($quantities[$product->id]) ? max(1, (int)$quantities[$product->id]) : 1;
            $barcodeSvg = BarcodeService::getCode128Svg($product->sku, 45, 1.6);
            $productUrl = route('shop.show', $product->slug);

            for ($i = 0; $i < $qty; $i++) {
                $labels[] = [
                    'product'    => $product,
                    'barcodeSvg' => $barcodeSvg,
                    'productUrl' => $productUrl,
                ];
            }
        }

        return view('products.barcode', [
            'product' => $products->first(),
            'format'  => $format,
            'count'   => count($labels),
            'labels'  => $labels,
            'isBulk'  => true,
        ]);
    }
}
