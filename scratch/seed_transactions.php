<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Support\Str;

$orders = Order::all();
$seeded = 0;

foreach ($orders as $order) {
    if (!Transaction::where('order_id', $order->id)->exists()) {
        Transaction::create([
            'transaction_number' => 'TRX-' . $order->created_at->format('Ymd') . '-' . strtoupper(Str::random(4)),
            'order_id'           => $order->id,
            'staff_id'           => $order->user_id,
            'customer_name'      => $order->customer_name,
            'customer_phone'     => $order->customer_phone,
            'customer_email'     => $order->customer_email,
            'amount'             => $order->total_amount,
            'payment_method'     => $order->payment_method ?? 'cash',
            'payment_status'     => $order->payment_status ?? 'paid',
            'payment_proof'      => $order->payment_proof ?? null,
            'notes'              => "Payment for order #{$order->order_number}",
            'created_at'         => $order->created_at,
            'updated_at'         => $order->updated_at,
        ]);
        $seeded++;
    }
}

echo "Successfully seeded {$seeded} transaction records for existing orders!\n";
