<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Users for Roles
        $superAdmin = User::firstOrCreate(
            ['phone' => '08039990000'],
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@atlasunisex.ng',
                'password' => bcrypt('password'),
                'role' => 'super_admin',
            ]
        );

        $admin = User::firstOrCreate(
            ['phone' => '08038881111'],
            [
                'name' => 'Collection Admin',
                'email' => 'admin@atlasunisex.ng',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        $staff = User::firstOrCreate(
            ['phone' => '08037772222'],
            [
                'name' => 'Inventory Staff',
                'email' => 'staff@atlasunisex.ng',
                'password' => bcrypt('password'),
                'role' => 'staff',
            ]
        );


        // 2. Multi-Category Catalog Categories
        $categories = [
            ['name' => 'Clothing & Apparel', 'slug' => 'clothing-apparel', 'description' => 'Shirts, Trousers, Suits, Hoodies & Traditional Wear'],
            ['name' => 'Shoes & Footwear', 'slug' => 'shoes-footwear', 'description' => 'Leather Shoes, Sneakers, Loafers, Slides & Boots'],
            ['name' => 'Perfumes & Fragrances', 'slug' => 'perfumes-fragrances', 'description' => 'Designer Perfumes, Eau de Parfum, Colognes & Oud Oils'],
            ['name' => 'Bags & Leather Goods', 'slug' => 'bags-leather-goods', 'description' => 'Handbags, Totes, Backpacks, Travel Bags & Wallets'],
            ['name' => 'Watches & Timepieces', 'slug' => 'watches-timepieces', 'description' => 'Luxury Wristwatches, Chronographs & Smartwatches'],
            ['name' => 'Jewelry & Ornaments', 'slug' => 'jewelry-ornaments', 'description' => 'Rings, Necklaces, Bracelets, Chains & Earrings'],
            ['name' => 'Fashion Accessories', 'slug' => 'fashion-accessories', 'description' => 'Sunglasses, Leather Belts, Caps, Scarves & Cufflinks'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        $clothingCat = Category::where('slug', 'clothing-apparel')->first();
        $shoesCat = Category::where('slug', 'shoes-footwear')->first();
        $perfumesCat = Category::where('slug', 'perfumes-fragrances')->first();
        $bagsCat = Category::where('slug', 'bags-leather-goods')->first();
        $watchesCat = Category::where('slug', 'watches-timepieces')->first();
        $jewelryCat = Category::where('slug', 'jewelry-ornaments')->first();
        $accessoriesCat = Category::where('slug', 'fashion-accessories')->first();

    }
}
