<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\LowStockNotificationService;
use Illuminate\Console\Command;

class NotifyLowStockCommand extends Command
{
    protected $signature = 'stock:notify-low';
    protected $description = 'Scan apparel inventory and dispatch SMS & Email notifications for items with stock below 10 units';

    public function handle(LowStockNotificationService $notificationService)
    {
        $this->info("Scanning Atlas Collection inventory for low stock (< 10 units)...");

        $lowStockProducts = Product::where('is_active', true)
            ->where('stock_quantity', '<', 10)
            ->get();

        if ($lowStockProducts->isEmpty()) {
            $this->info("All products are currently adequately stocked (>= 10 units).");
            return 0;
        }

        $count = 0;
        foreach ($lowStockProducts as $product) {
            $this->line("Processing low stock alert for: {$product->name} (SKU: {$product->sku}, Stock: {$product->stock_quantity})");
            if ($notificationService->checkAndNotify($product)) {
                $count++;
            }
        }

        $this->info("Dispatched low stock SMS & Email notifications for {$count} item(s).");
        return 0;
    }
}
