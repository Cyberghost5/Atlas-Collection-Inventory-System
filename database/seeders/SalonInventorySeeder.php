<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SalonInventorySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Admin User
        $user = User::firstOrCreate(
            ['email' => 'admin@velvetblade.com'],
            [
                'name' => 'Salon Manager',
                'password' => bcrypt('password'),
            ]
        );

        // 2. Create Categories
        $categories = [
            ['name' => 'Hair Care & Shampoos', 'slug' => 'hair-care-shampoos', 'description' => 'Shampoos, conditioners, and hair treatments'],
            ['name' => 'Styling Tools & Clippers', 'slug' => 'styling-tools-clippers', 'description' => 'Barber clippers, trimmers, blow dryers, straighteners'],
            ['name' => 'Hair Extensions & Weaves', 'slug' => 'hair-extensions-weaves', 'description' => 'Human & synthetic hair extensions, wigs, braiding hair'],
            ['name' => 'Chemicals & Color Dyes', 'slug' => 'chemicals-color-dyes', 'description' => 'Hair color kits, bleaches, developers, perm solutions'],
            ['name' => 'Beard & Skincare', 'slug' => 'beard-skincare', 'description' => 'Beard oils, balm, facial scrubs, aftershaves'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        // 3. Create Suppliers
        $suppliers = [
            ['name' => "L'Oréal Professional", 'contact_person' => 'Sarah Connor', 'phone' => '+1 (555) 019-2834', 'email' => 'orders@lorealpro.com'],
            ['name' => 'Wahl Barber Supplies', 'contact_person' => 'Marcus Vance', 'phone' => '+1 (555) 438-9921', 'email' => 'sales@wahlbarber.com'],
            ['name' => 'Crown Hair Extensions Co.', 'contact_person' => 'Aisha Bellamy', 'phone' => '+1 (555) 882-1049', 'email' => 'contact@crownhair.com'],
        ];

        foreach ($suppliers as $sup) {
            Supplier::firstOrCreate(['name' => $sup['name']], $sup);
        }

        $loreal = Supplier::where('name', "L'Oréal Professional")->first();
        $wahl = Supplier::where('name', 'Wahl Barber Supplies')->first();
        $crown = Supplier::where('name', 'Crown Hair Extensions Co.')->first();

        $hairCare = Category::where('slug', 'hair-care-shampoos')->first();
        $clippers = Category::where('slug', 'styling-tools-clippers')->first();
        $extensions = Category::where('slug', 'hair-extensions-weaves')->first();
        $chemicals = Category::where('slug', 'chemicals-color-dyes')->first();
        $beard = Category::where('slug', 'beard-skincare')->first();

        // 4. Sample Salon Products
        $productsData = [
            [
                'name' => 'Argan Oil Moisture Shampoo 500ml',
                'sku' => 'HC-SHMP-001',
                'category_id' => $hairCare->id,
                'supplier_id' => $loreal->id,
                'usage_type' => 'both',
                'unit' => 'bottle',
                'cost_price' => 12.50,
                'selling_price' => 24.99,
                'stock_quantity' => 18,
                'min_stock_level' => 5,
            ],
            [
                'name' => 'Keratin Deep Repair Conditioner 1L',
                'sku' => 'HC-COND-002',
                'category_id' => $hairCare->id,
                'supplier_id' => $loreal->id,
                'usage_type' => 'internal_use',
                'unit' => 'bottle',
                'cost_price' => 28.00,
                'selling_price' => null,
                'stock_quantity' => 3, // LOW STOCK ALERT
                'min_stock_level' => 5,
            ],
            [
                'name' => 'Wahl Magic Clip Cordless Clipper',
                'sku' => 'TOOL-WHL-001',
                'category_id' => $clippers->id,
                'supplier_id' => $wahl->id,
                'usage_type' => 'internal_use',
                'unit' => 'piece',
                'cost_price' => 85.00,
                'selling_price' => 129.99,
                'stock_quantity' => 4,
                'min_stock_level' => 2,
            ],
            [
                'name' => 'Brazilian Body Wave 20" Bundle',
                'sku' => 'EXT-BW20-001',
                'category_id' => $extensions->id,
                'supplier_id' => $crown->id,
                'usage_type' => 'retail',
                'unit' => 'pack',
                'cost_price' => 45.00,
                'selling_price' => 89.99,
                'stock_quantity' => 2, // LOW STOCK ALERT
                'min_stock_level' => 4,
            ],
            [
                'name' => 'Professional Blonde Developer 20 Vol',
                'sku' => 'CHEM-DEV20-01',
                'category_id' => $chemicals->id,
                'supplier_id' => $loreal->id,
                'usage_type' => 'internal_use',
                'unit' => 'bottle',
                'cost_price' => 8.50,
                'selling_price' => null,
                'stock_quantity' => 12,
                'min_stock_level' => 3,
            ],
            [
                'name' => 'Cedarwood & Vanilla Beard Oil 50ml',
                'sku' => 'SKIN-BOIL-001',
                'category_id' => $beard->id,
                'supplier_id' => null,
                'usage_type' => 'retail',
                'unit' => 'bottle',
                'cost_price' => 6.00,
                'selling_price' => 18.00,
                'stock_quantity' => 25,
                'min_stock_level' => 6,
            ],
        ];

        foreach ($productsData as $data) {
            $product = Product::firstOrCreate(['sku' => $data['sku']], $data);

            if ($product->wasRecentlyCreated && $product->stock_quantity > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'type' => 'in',
                    'quantity' => $product->stock_quantity,
                    'unit_cost' => $product->cost_price,
                    'notes' => 'Initial inventory seed',
                ]);
            }
        }
    }
}
