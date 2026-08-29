<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>🚨 Low Stock - Atlas Collection</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        .header { background: #0f172a; color: #ffffff; padding: 24px; text-align: center; }
        .logo { font-size: 20px; font-weight: 900; letter-spacing: 2px; color: #f59e0b; margin: 0; }
        .motto { font-size: 11px; font-style: italic; color: #94a3b8; margin-top: 4px; }
        .badge { display: inline-block; background: #fee2e2; color: #b91c1c; font-size: 11px; font-weight: 800; padding: 4px 12px; border-radius: 9999px; text-transform: uppercase; margin-top: 12px; }
        .content { padding: 32px 24px; }
        .alert-box { background: #fff1f2; border: 1px solid #fecdd3; border-radius: 12px; padding: 16px; margin-bottom: 24px; color: #9f1239; font-size: 14px; line-height: 1.5; }
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .details-table th, .details-table td { padding: 12px; border-bottom: 1px solid #f1f5f9; text-align: left; font-size: 13px; }
        .details-table th { color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 11px; width: 40%; }
        .stock-highlight { color: #dc2626; font-weight: 900; font-size: 16px; }
        .btn { display: inline-block; background: #f59e0b; color: #0f172a; text-decoration: none; font-weight: 800; font-size: 13px; padding: 14px 24px; border-radius: 12px; text-align: center; box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.3); }
        .footer { background: #f8fafc; border-top: 1px solid #f1f5f9; padding: 16px; text-align: center; font-size: 11px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">ATLAS COLLECTION</div>
            <div class="motto">...your style, our identity</div>
            <div class="badge">🚨 LOW STOCK ALERT (BELOW 10 UNITS)</div>
        </div>

        <div class="content">
            <div class="alert-box">
                <strong>Attention Inventory Manager:</strong><br>
                The apparel item <strong>{{ $product->name }}</strong> has dropped below the threshold of <strong>10 units</strong>. Immediate re-ordering is recommended to avoid stockout in the Bauchi store.
            </div>

            <table class="details-table">
                <tr>
                    <th>Apparel Item:</th>
                    <td><strong>{{ $product->name }}</strong></td>
                </tr>
                <tr>
                    <th>SKU Code:</th>
                    <td><code>{{ $product->sku }}</code></td>
                </tr>
                <tr>
                    <th>Size & Color:</th>
                    <td>Size <strong>{{ $product->size }}</strong> ({{ $product->color ?? 'Standard' }})</td>
                </tr>
                <tr>
                    <th>Category:</th>
                    <td>{{ $product->category->name ?? 'Unisex' }}</td>
                </tr>
                <tr>
                    <th>Current Stock:</th>
                    <td class="stock-highlight">{{ $product->stock_quantity }} {{ $product->unit }}(s) remaining</td>
                </tr>
                <tr>
                    <th>Alert Threshold:</th>
                    <td>Below 10 units</td>
                </tr>
                @if($product->supplier)
                <tr>
                    <th>Assigned Supplier:</th>
                    <td>{{ $product->supplier->name }} ({{ $product->supplier->phone ?? 'N/A' }})</td>
                </tr>
                @endif
            </table>

            <div style="text-align: center; margin-top: 28px;">
                <a href="{{ route('products.show', $product) }}" class="btn">
                    📦 Manage Stock & Restock Item &rarr;
                </a>
            </div>
        </div>

        <div class="footer">
            📍 Wunti market, Bababa plaza, shop E7 Block E (Beside New Flyover), Bauchi, Nigeria<br>
            📞 Phone: 0810 399 6947 | Email: atlascollection6@gmail.com
        </div>
    </div>
</body>
</html>
