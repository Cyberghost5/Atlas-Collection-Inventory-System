<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LowStockAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public Product $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function build()
    {
        return $this->subject("LOW STOCK ALERT: {$this->product->name} (Only {$this->product->stock_quantity} Left)")
                    ->view('emails.low_stock_alert');
    }
}
