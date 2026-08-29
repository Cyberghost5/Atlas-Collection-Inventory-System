<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MilestoneOneTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;
    protected Product $product;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name' => 'Hoodies',
            'slug' => 'hoodies',
        ]);

        $this->product = Product::create([
            'category_id'     => $this->category->id,
            'name'            => 'Atlas Oversized Hoodie',
            'sku'             => 'AUC-HOD-L-0001',
            'size'            => 'L',
            'color'           => 'Black',
            'usage_type'      => 'retail',
            'unit'            => 'pcs',
            'cost_price'      => 15000,
            'selling_price'   => 25000,
            'stock_quantity'  => 10,
            'min_stock_level' => 2,
            'is_active'        => true,
        ]);

        $this->admin = User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@atlas.com',
            'phone'    => '08012345678',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);
    }

    public function test_checkout_deducts_stock_and_creates_stock_movement(): void
    {
        $response = $this->post(route('shop.checkout'), [
            'product_id'       => $this->product->id,
            'quantity'         => 3,
            'customer_name'    => 'John Doe',
            'customer_email'   => 'john@example.com',
            'customer_phone'   => '08099998888',
            'shipping_address' => '123 Victoria Island, Lagos',
        ]);

        $this->product->refresh();
        $this->assertEquals(7, $this->product->stock_quantity);

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'John Doe',
            'total_amount'  => 75000,
            'status'        => 'pending',
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type'       => 'out_sale',
            'quantity'   => 3,
        ]);
    }

    public function test_checkout_fails_when_stock_is_insufficient(): void
    {
        $response = $this->post(route('shop.checkout'), [
            'product_id'       => $this->product->id,
            'quantity'         => 15,
            'customer_name'    => 'Jane Doe',
            'customer_email'   => 'jane@example.com',
            'customer_phone'   => '08011112222',
            'shipping_address' => '456 Ikeja, Lagos',
        ]);

        $response->assertSessionHasErrors('quantity');
        $this->product->refresh();
        $this->assertEquals(10, $this->product->stock_quantity);
    }

    public function test_order_cancellation_restores_product_stock_and_logs_movement(): void
    {
        $order = Order::create([
            'order_number'     => 'AUC-ORD-TEST-1',
            'customer_name'    => 'Customer Test',
            'customer_email'   => 'customer@test.com',
            'customer_phone'   => '08022223333',
            'shipping_address' => 'Abuja',
            'total_amount'     => 50000,
            'status'           => 'pending',
            'payment_status'   => 'paid',
        ]);

        OrderItem::create([
            'order_id'   => $order->id,
            'product_id' => $this->product->id,
            'quantity'   => 4,
            'unit_price' => 25000,
            'subtotal'   => 100000,
        ]);

        // Stock before cancellation is 10
        $response = $this->actingAs($this->admin)->patch(route('orders.update-status', $order->id), [
            'status' => 'cancelled',
            'notes'  => 'Customer requested cancellation',
        ]);

        $response->assertSessionHas('success');
        $this->product->refresh();
        $this->assertEquals(14, $this->product->stock_quantity);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type'       => 'in',
            'quantity'   => 4,
        ]);
    }

    public function test_order_reactivation_fails_when_insufficient_stock(): void
    {
        $order = Order::create([
            'order_number'     => 'AUC-ORD-TEST-2',
            'customer_name'    => 'Customer Test 2',
            'customer_email'   => 'customer2@test.com',
            'customer_phone'   => '08044445555',
            'shipping_address' => 'Abuja',
            'total_amount'     => 50000,
            'status'           => 'cancelled',
            'payment_status'   => 'paid',
        ]);

        OrderItem::create([
            'order_id'   => $order->id,
            'product_id' => $this->product->id,
            'quantity'   => 20, // More than product stock 10
            'unit_price' => 25000,
            'subtotal'   => 500000,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('orders.update-status', $order->id), [
            'status' => 'processing',
        ]);

        $response->assertSessionHasErrors('status');
        $order->refresh();
        $this->assertEquals('cancelled', $order->status);
    }
}
