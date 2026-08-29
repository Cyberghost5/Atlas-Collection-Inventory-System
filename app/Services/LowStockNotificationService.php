<?php

namespace App\Services;

use App\Mail\LowStockAlertMail;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LowStockNotificationService
{
    protected BulkSmsNigeriaService $smsService;

    public function __construct(BulkSmsNigeriaService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Check if product stock is below 10 units and dispatch SMS + Email alerts.
     */
    public function checkAndNotify(Product $product): bool
    {
        // Re-evaluate stock level
        $product->refresh();

        if ($product->stock_quantity >= 10) {
            return false;
        }

        Log::info("Low stock threshold (<10) reached for {$product->name} (SKU: {$product->sku}). Current stock: {$product->stock_quantity}");

        // 1. Send SMS via BulkSMS Nigeria API
        $smsMessage = "ALERT: {$product->name} (Size {$product->size}) stock is low! Only {$product->stock_quantity} {$product->unit}(s) remaining (Below 10). Restock now. - Atlas Collection";
        $smsSuccess = $this->smsService->sendSms($smsMessage);

        // 2. Send Email via SMTP PHP Mailer
        $emailSuccess = false;
        try {
            $storeEmail = config('services.store.email', 'atlascollection6@gmail.com');
            Mail::to($storeEmail)->send(new LowStockAlertMail($product));
            $emailSuccess = true;
            Log::info("Low stock email alert dispatched via SMTP to {$storeEmail}");
        } catch (\Exception $e) {
            Log::error("Failed to send low stock SMTP email alert: " . $e->getMessage());
        }

        return $smsSuccess || $emailSuccess;
    }
}
