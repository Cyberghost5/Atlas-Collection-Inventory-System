<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class Seed100ProductsAndCustomersSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('en_NG');

        // 1. Ensure categories exist
        $categories = Category::all();
        if ($categories->isEmpty()) {
            $categoriesList = [
                ['name' => 'Clothes & Apparel', 'description' => 'Unisex hoodies, tees, trousers, & streetwear'],
                ['name' => 'Perfumes & Fragrances', 'description' => 'Luxury ouds, mists, & French perfumes'],
                ['name' => 'Shoes & Footwear', 'description' => 'Sneakers, loafers, boots, & slides'],
                ['name' => 'Bags & Luggage', 'description' => 'Crossbody, duffles, backpacks, & clutches'],
                ['name' => 'Watches & Timepieces', 'description' => 'Chronographs, automatic, & minimalist watches'],
                ['name' => 'Jewelry & Chains', 'description' => 'Chains, rings, bracelets, & pendants'],
                ['name' => 'Accessories', 'description' => 'Belts, hats, sunglasses, & wallets'],
            ];
            foreach ($categoriesList as $cat) {
                Category::create([
                    'name' => $cat['name'],
                    'slug' => Str::slug($cat['name']),
                    'description' => $cat['description']
                ]);
            }
            $categories = Category::all();
        }

        // 2. Ensure suppliers exist
        $suppliers = Supplier::all();
        if ($suppliers->isEmpty()) {
            $supplierData = [
                ['name' => 'Lagos Apparel Wholesalers', 'contact_name' => 'Chief Okonkwo', 'phone' => '08031234567', 'city' => 'Lagos'],
                ['name' => 'Dubai Luxury Imports Direct', 'contact_name' => 'Tariq Al-Mansoor', 'phone' => '08059876543', 'city' => 'Dubai / Kano'],
                ['name' => 'Bauchi Local Textile Craft', 'contact_name' => 'Mallam Isa', 'phone' => '08103996947', 'city' => 'Bauchi'],
                ['name' => 'Istanbul Leather Goods Ltd', 'contact_name' => 'Mehmet Yilmaz', 'phone' => '08145558899', 'city' => 'Istanbul / Abuja'],
            ];
            foreach ($supplierData as $sup) {
                Supplier::create([
                    'name' => $sup['name'],
                    'contact_name' => $sup['contact_name'],
                    'phone' => $sup['phone'],
                    'city' => $sup['city'],
                    'address' => 'Trade Complex, ' . $sup['city']
                ]);
            }
            $suppliers = Supplier::all();
        }

        // 3. SEED 100 CUSTOMERS
        $this->command->info('Seeding 100 Customer profiles...');
        $password = Hash::make('password');

        for ($i = 1; $i <= 100; $i++) {
            $firstName = $faker->firstName();
            $lastName = $faker->lastName();
            $name = "{$firstName} {$lastName}";
            $phone = '080' . $faker->unique()->numberBetween(10000000, 99999999);
            $email = Str::slug("{$firstName}.{$lastName}.{$i}") . '@gmail.com';
            $address = $faker->streetAddress() . ', ' . $faker->city() . ', Nigeria';

            User::create([
                'name'     => $name,
                'email'    => $email,
                'phone'    => $phone,
                'address'  => $address,
                'password' => $password,
                'role'     => 'customer',
            ]);
        }
        $this->command->info('Successfully seeded 100 customers!');

        // 4. SEED 100 PRODUCTS
        $this->command->info('Seeding 100 Inventory items...');

        $apparelCatalog = [
            'Clothes & Apparel' => [
                'items' => [
                    'Heavyweight Boxy Hoodie', 'Oversized Vintage Graphic Tee', 'Slim Fit Cargo Trousers',
                    'Tactical Utility Vest', 'Designer Denim Jacket', 'Unisex Fleece Sweatpants',
                    'Luxury Silk Button-Down Shirt', 'Athletic Tracksuit Set', 'Embroidered Hausa Kaftan',
                    'Bespoke Agbada Masterpiece', 'Ribbed Knit Sweater', 'Distressed Skinny Jeans',
                    'Corduroy Overshirt', 'Puffer Winter Jacket', 'Cropped Acid Wash Tee'
                ],
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'units' => 'piece',
                'colors' => ['Black', 'Cream', 'Charcoal', 'Olive Green', 'Navy Blue', 'Mustard', 'White']
            ],
            'Perfumes & Fragrances' => [
                'items' => [
                    'Sauvage Royal Oud', 'Midnight Velvet Rose EDP', 'Imperial Amber Fragrance Oil',
                    'Sultan Wood Concentrated Perfume', 'Smokey Vanilla & Tobacco', 'Ocean Breeze Aqua Mist',
                    'French Musk Intense', 'Arabian Cedarwood Extract', 'Golden Lotus Pure Oil',
                    'Crystal Citrus Parfum', 'Sweet Black Oud', 'Spiced Tonka Intense'
                ],
                'sizes' => ['50ml', '100ml', '200ml', '12ml Oil'],
                'units' => 'bottle',
                'colors' => ['Amber Gold', 'Deep Ruby', 'Crystal Clear', 'Smokey Black']
            ],
            'Shoes & Footwear' => [
                'items' => [
                    'Italian Leather Oxford Shoes', 'Chunky Retro Street Sneakers', 'Suede Chelsea Ankle Boots',
                    'Handmade Tassel Loafers', 'High-Top Canvas Sneakers', 'Luxury Designer Leather Slides',
                    'Double Monk Strap Dress Shoes', 'Running Performance Trainers', 'All-Black Formal Derby Shoes'
                ],
                'sizes' => ['EU 40', 'EU 41', 'EU 42', 'EU 43', 'EU 44', 'EU 45'],
                'units' => 'pair',
                'colors' => ['Tan Brown', 'Black Leather', 'White/Red', 'Navy Suede', 'Burgundy']
            ],
            'Bags & Luggage' => [
                'items' => [
                    'Crossbody Tactical Bag', 'Weekender Leather Duffle Bag', 'Canvas Utility Tote Bag',
                    'Designer Leather Urban Backpack', 'Chest Rig Utility Pouch', 'Luxury Envelope Leather Clutch',
                    'Mini Belt Waist Pack', 'Laptop Sleeve Messenger Bag'
                ],
                'sizes' => ['Standard', 'Large', 'Medium'],
                'units' => 'piece',
                'colors' => ['Black', 'Dark Brown', 'Khaki', 'Monogram Pattern', 'Camouflage']
            ],
            'Watches & Timepieces' => [
                'items' => [
                    'Chronograph Steel Gold Watch', 'Minimalist Leather Strap Watch', 'Automatic Skeleton Dial Watch',
                    'Iced Diamond Quartz Timepiece', 'Luxury Square Retro Watch', 'Diver Waterproof Watch'
                ],
                'sizes' => ['40mm', '42mm', '44mm'],
                'units' => 'piece',
                'colors' => ['Gold', 'Silver Steel', 'Rose Gold', 'Matte Black', 'Brown Leather']
            ],
            'Jewelry & Chains' => [
                'items' => [
                    'Heavy Cuban Link Chain', 'Iced Out Ankh Pendant Necklace', 'Stainless Steel Bangle Bracelet',
                    'Sterling Silver Signet Ring', 'Pearl Drop Unisex Necklace', 'Cuban Link Bracelet'
                ],
                'sizes' => ['18 inch', '22 inch', '24 inch', 'Standard'],
                'units' => 'set',
                'colors' => ['18k Gold Finish', 'Silver Chrome', 'Two-Tone']
            ],
            'Accessories' => [
                'items' => [
                    'Reversible Designer Leather Belt', 'Streetwear Bucket Hat', 'Polarized Retro Sunglasses',
                    'Knit Beanie Cap', 'Luxury Leather Cardholder Wallet', 'Silk Pocket Square'
                ],
                'sizes' => ['One Size', 'Standard'],
                'units' => 'pack',
                'colors' => ['Black', 'Brown', 'Gold Accent', 'Multi-Color']
            ]
        ];

        $usageTypes = ['retail', 'display_sample', 'both'];

        for ($j = 1; $j <= 100; $j++) {
            // Pick a random category name from our list
            $catName = array_rand($apparelCatalog);
            $catMeta = $apparelCatalog[$catName];
            
            // Find corresponding DB category
            $category = $categories->first(function($c) use ($catName) {
                return Str::contains($c->name, explode(' ', $catName)[0]);
            }) ?? $categories->random();

            $supplier = $suppliers->random();
            $baseName = $faker->randomElement($catMeta['items']);
            $size = $faker->randomElement($catMeta['sizes']);
            $color = $faker->randomElement($catMeta['colors']);
            $name = "{$baseName} - {$color}";

            $costPrice = $faker->numberBetween(3500, 45000);
            $markupMultiplier = $faker->randomFloat(2, 1.25, 1.65);
            $sellingPrice = round(($costPrice * $markupMultiplier) / 100) * 100;

            $stockQty = $faker->numberBetween(2, 45);
            $minStock = $faker->numberBetween(3, 8);
            $usageType = $faker->randomElement($usageTypes);

            $skuPrefix = strtoupper(substr($category->name, 0, 3));
            $sku = "AUC-{$skuPrefix}-" . strtoupper(Str::random(5));
            while (Product::where('sku', $sku)->exists()) {
                $sku = "AUC-{$skuPrefix}-" . strtoupper(Str::random(5));
            }

            Product::create([
                'category_id'     => $category->id,
                'supplier_id'     => $supplier->id,
                'name'            => $name,
                'sku'             => $sku,
                'size'            => $size,
                'color'           => $color,
                'description'     => "Authentic luxury {$category->name} item. Imported & curated for Atlas Collection Bauchi.",
                'usage_type'      => $usageType,
                'unit'            => $catMeta['units'],
                'cost_price'      => $costPrice,
                'selling_price'   => $sellingPrice,
                'stock_quantity'  => $stockQty,
                'min_stock_level' => $minStock,
                'is_active'       => true,
            ]);
        }

        $this->command->info('Successfully seeded 100 products!');
    }
}
