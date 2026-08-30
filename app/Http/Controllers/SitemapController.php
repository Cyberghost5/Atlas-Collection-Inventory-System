<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate dynamic XML sitemap for search engines including all products & categories
     */
    public function index(): Response
    {
        $products = Product::where('is_active', true)->latest()->get();
        $categories = Category::orderBy('name')->get();

        $content = view('sitemap', compact('products', 'categories'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }
}
