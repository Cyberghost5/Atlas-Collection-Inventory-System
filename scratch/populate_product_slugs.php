<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use Illuminate\Support\Str;

$products = Product::all();
$updated = 0;

foreach ($products as $prod) {
    if (empty($prod->slug)) {
        $base = Str::slug($prod->name . '-' . ($prod->size ?? 'standard'));
        $slug = $base;
        $count = 1;
        while (Product::where('slug', $slug)->where('id', '!=', $prod->id)->exists()) {
            $slug = "{$base}-" . $count++;
        }
        $prod->slug = $slug;
        $prod->save();
        $updated++;
    }
}

echo "Successfully populated slugs for {$updated} products!\n";
