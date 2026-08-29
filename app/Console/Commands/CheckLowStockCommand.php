<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckLowStockCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'atlas:check-low-stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan inventory stock levels and log/notify for items below reorder thresholds';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning Atlas Collection inventory for low stock items...');

        $lowStockItems = Product::with(['category', 'supplier'])
            ->where('is_active', true)
            ->lowStock()
            ->get();

        if ($lowStockItems->isEmpty()) {
            $this->info('✅ All catalog items are sufficiently stocked.');
            return 0;
        }

        $this->warn("⚠️ Found {$lowStockItems->count()} item(s) reaching critical reorder levels:");

        foreach ($lowStockItems as $item) {
            $supplierName = $item->supplier ? $item->supplier->name : 'Unassigned';
            $this->line("- [SKU: {$item->sku}] {$item->name} (Variant: {$item->size}) -> Current Stock: {$item->stock_quantity} {$item->unit} (Min Threshold: {$item->min_stock_level}) | Supplier: {$supplierName}");
        }

        Log::warning("Atlas Collection Low Stock Alert: {$lowStockItems->count()} items reaching threshold levels.", [
            'items_count' => $lowStockItems->count(),
            'skus' => $lowStockItems->pluck('sku')->toArray(),
        ]);

        $this->info('✨ Low stock audit check completed successfully.');
        return 0;
    }
}
