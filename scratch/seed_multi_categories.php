<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use Illuminate\Support\Str;

$categories = [
    [
        'name' => 'Clothing & Apparel',
        'description' => 'Men & Women Shirts, Trousers, Suits, Dresses, Hoodies & Traditional Wear',
    ],
    [
        'name' => 'Shoes & Footwear',
        'description' => 'Leather Shoes, Sneakers, Loafers, Slides, Sandals & Boots',
    ],
    [
        'name' => 'Perfumes & Fragrances',
        'description' => 'Designer Perfumes, Eau de Parfum, Colognes, Body Mists & Arabian Oud Oils',
    ],
    [
        'name' => 'Bags & Leather Goods',
        'description' => 'Handbags, Totes, Clutch Bags, Backpacks, Travel Bags & Wallets',
    ],
    [
        'name' => 'Watches & Timepieces',
        'description' => 'Luxury Wristwatches, Chronographs, Automatic Watches & Chains',
    ],
    [
        'name' => 'Jewelry & Ornaments',
        'description' => 'Rings, Necklaces, Bracelets, Pendants, Earrings & Gold/Silver Chains',
    ],
    [
        'name' => 'Fashion Accessories',
        'description' => 'Sunglasses, Leather Belts, Caps, Hats, Scarves & Cufflinks',
    ],
];

foreach ($categories as $cat) {
    Category::firstOrCreate(
        ['name' => $cat['name']],
        [
            'slug' => Str::slug($cat['name']),
            'description' => $cat['description'],
        ]
    );
}

echo "Multi-category catalog categories seeded successfully!\n";
