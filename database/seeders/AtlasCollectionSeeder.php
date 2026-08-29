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

class AtlasCollectionSeeder extends Seeder
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

        $customer = User::firstOrCreate(
            ['phone' => '08033334444'],
            [
                'name' => 'Chidi Okonkwo',
                'email' => 'chidi.okonkwo@gmail.com',
                'password' => bcrypt('password'),
                'role' => 'customer',
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

        // 3. Nigerian Manufacturers & Luxury Suppliers
        $suppliers = [
            ['name' => 'Lagos Apparel Hub', 'contact_person' => 'Babatunde Adeleke', 'phone' => '08021112233', 'email' => 'orders@lagosapparel.ng', 'address' => 'Victoria Island, Lagos, Nigeria'],
            ['name' => 'Eko Textile & Luxury Mills', 'contact_person' => 'Ngozi Eze', 'phone' => '08034445566', 'email' => 'supply@ekotextiles.ng', 'address' => 'Ikeja Industrial Estate, Lagos, Nigeria'],
            ['name' => 'Abuja Fragrance & Horology', 'contact_person' => 'Usman Abubakar', 'phone' => '08057778899', 'email' => 'b2b@abujagood.ng', 'address' => 'Central Business District, Abuja, Nigeria'],
        ];

        foreach ($suppliers as $sup) {
            Supplier::firstOrCreate(['name' => $sup['name']], $sup);
        }

        $lagos = Supplier::where('name', 'Lagos Apparel Hub')->first();
        $eko = Supplier::where('name', 'Eko Textile & Luxury Mills')->first();
        $abuja = Supplier::where('name', 'Abuja Fragrance & Horology')->first();

        $clothingCat = Category::where('slug', 'clothing-apparel')->first();
        $shoesCat = Category::where('slug', 'shoes-footwear')->first();
        $perfumesCat = Category::where('slug', 'perfumes-fragrances')->first();
        $bagsCat = Category::where('slug', 'bags-leather-goods')->first();
        $watchesCat = Category::where('slug', 'watches-timepieces')->first();
        $jewelryCat = Category::where('slug', 'jewelry-ornaments')->first();
        $accessoriesCat = Category::where('slug', 'fashion-accessories')->first();

        // 4. Sample Multi-Category Inventory Items (Naira Prices)
        $productsData = [
            [
                'name' => 'Atlas Heavyweight Oversized Hoodie',
                'sku' => 'AUC-HD-BLK-M',
                'size' => 'M',
                'color' => 'Washed Black',
                'category_id' => $clothingCat->id,
                'supplier_id' => $lagos->id,
                'image' => null,
                'usage_type' => 'both',
                'unit' => 'piece',
                'cost_price' => 28000.00,
                'selling_price' => 55000.00,
                'stock_quantity' => 24,
                'min_stock_level' => 8,
                'description' => '450gsm organic terry cotton relaxed hoodie fit.',
            ],
            [
                'name' => 'Atlas Italian Oxford Leather Shoes',
                'sku' => 'AUC-SH-BRN-42',
                'size' => 'EU 42',
                'color' => 'Burnished Brown',
                'category_id' => $shoesCat->id,
                'supplier_id' => $eko->id,
                'image' => null,
                'usage_type' => 'retail',
                'unit' => 'pair',
                'cost_price' => 45000.00,
                'selling_price' => 95000.00,
                'stock_quantity' => 15,
                'min_stock_level' => 5,
                'description' => 'Handcrafted full-grain Italian leather formal oxford dress shoes.',
            ],
            [
                'name' => 'Atlas Oud Royal Eau de Parfum (100ml)',
                'sku' => 'AUC-PRF-OUD-100',
                'size' => '100ml',
                'color' => 'Woody Floral',
                'category_id' => $perfumesCat->id,
                'supplier_id' => $abuja->id,
                'image' => null,
                'usage_type' => 'both',
                'unit' => 'bottle',
                'cost_price' => 38000.00,
                'selling_price' => 85000.00,
                'stock_quantity' => 4, // LOW STOCK ALERT
                'min_stock_level' => 8,
                'description' => 'Rich Arabian Oud, Amber, Vanilla and Spicy Cedarwood EDP 100ml spray bottle.',
            ],
            [
                'name' => 'Atlas Executive Leather Tote Bag',
                'sku' => 'AUC-BG-BLK-MD',
                'size' => 'Medium',
                'color' => 'Matte Black',
                'category_id' => $bagsCat->id,
                'supplier_id' => $lagos->id,
                'image' => null,
                'usage_type' => 'retail',
                'unit' => 'piece',
                'cost_price' => 32000.00,
                'selling_price' => 72000.00,
                'stock_quantity' => 12,
                'min_stock_level' => 4,
                'description' => 'Genuine calfskin leather tote bag with padded laptop sleeve.',
            ],
            [
                'name' => 'Atlas Chronograph Gold Wristwatch',
                'sku' => 'AUC-WCH-GLD-40',
                'size' => '40mm',
                'color' => '18K Gold Finish',
                'category_id' => $watchesCat->id,
                'supplier_id' => $abuja->id,
                'image' => null,
                'usage_type' => 'display_sample',
                'unit' => 'piece',
                'cost_price' => 65000.00,
                'selling_price' => 140000.00,
                'stock_quantity' => 3, // LOW STOCK ALERT
                'min_stock_level' => 5,
                'description' => 'Japanese quartz chronograph movement with sapphire crystal glass and 18K gold plating.',
            ],
            [
                'name' => 'Atlas Cubano Cuban Link Gold Chain (22")',
                'sku' => 'AUC-JWL-GLD-22',
                'size' => '22 Inch',
                'color' => 'Yellow Gold',
                'category_id' => $jewelryCat->id,
                'supplier_id' => $eko->id,
                'image' => null,
                'usage_type' => 'retail',
                'unit' => 'piece',
                'cost_price' => 22000.00,
                'selling_price' => 48000.00,
                'stock_quantity' => 18,
                'min_stock_level' => 5,
                'description' => '12mm solid stainless steel 18K gold-plated Cuban link chain.',
            ],
            [
                'name' => 'Atlas Reversible Leather Belt',
                'sku' => 'AUC-ACC-BLT-STD',
                'size' => 'Standard',
                'color' => 'Black / Tan',
                'category_id' => $accessoriesCat->id,
                'supplier_id' => $lagos->id,
                'image' => null,
                'usage_type' => 'retail',
                'unit' => 'piece',
                'cost_price' => 9000.00,
                'selling_price' => 22000.00,
                'stock_quantity' => 30,
                'min_stock_level' => 10,
                'description' => 'Dual-sided reversible genuine leather belt with rotating brushed metal buckle.',
            ],
        ];

        foreach ($productsData as $data) {
            $product = Product::firstOrCreate(['sku' => $data['sku']], $data);

            if ($product->wasRecentlyCreated && $product->stock_quantity > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $admin->id,
                    'type' => 'in',
                    'quantity' => $product->stock_quantity,
                    'unit_cost' => $product->cost_price,
                    'notes' => 'Initial collection batch arrival',
                ]);
            }
        }

        // 5. Sample Customer Order
        $perfume = Product::where('sku', 'AUC-PRF-OUD-100')->first();
        $shoes = Product::where('sku', 'AUC-SH-BRN-42')->first();

        if ($perfume && $shoes) {
            $order = Order::firstOrCreate(
                ['order_number' => 'AUC-ORD-2026-9001'],
                [
                    'user_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'customer_email' => $customer->email,
                    'customer_phone' => $customer->phone,
                    'shipping_address' => '15 Admiralty Way, Lekki Phase 1, Lagos, Nigeria',
                    'total_amount' => 180000.00,
                    'status' => 'processing',
                    'payment_status' => 'paid',
                    'payment_method' => 'bank_transfer',
                    'notes' => 'Deliver via Gokada / GIG Logistics to Lekki',
                ]
            );

            if ($order->wasRecentlyCreated) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $perfume->id,
                    'quantity' => 1,
                    'unit_price' => 85000.00,
                    'subtotal' => 85000.00,
                ]);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $shoes->id,
                    'quantity' => 1,
                    'unit_price' => 95000.00,
                    'subtotal' => 95000.00,
                ]);

                // Create corresponding Transaction record
                Transaction::firstOrCreate(
                    ['order_id' => $order->id],
                    [
                        'transaction_number' => 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
                        'staff_id'           => $admin->id,
                        'customer_name'      => $customer->name,
                        'customer_phone'     => $customer->phone,
                        'customer_email'     => $customer->email,
                        'amount'             => 180000.00,
                        'payment_method'     => 'bank_transfer',
                        'payment_status'     => 'paid',
                        'notes'              => 'Payment for order #AUC-ORD-2026-9001',
                    ]
                );
            }
        }
    }
}
