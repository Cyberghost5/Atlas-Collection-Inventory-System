<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'supplier_id',
        'name',
        'slug',
        'sku',
        'size',
        'available_sizes',
        'color',
        'available_colors',
        'image',
        'barcode',
        'description',
        'usage_type',
        'unit',
        'cost_price',
        'selling_price',
        'stock_quantity',
        'display_stock_quantity',
        'min_stock_level',
        'is_active',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'display_stock_quantity' => 'integer',
        'min_stock_level' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'image_url',
        'is_low_stock',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $base = Str::slug($product->name . '-' . ($product->size ?? 'standard'));
                $slug = $base;
                $count = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = "{$base}-" . $count++;
                }
                $product->slug = $slug;
            }
        });

        static::updating(function ($product) {
            if (empty($product->slug) || $product->isDirty('name')) {
                $base = Str::slug($product->name . '-' . ($product->size ?? 'standard'));
                $slug = $base;
                $count = 1;
                while (static::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                    $slug = "{$base}-" . $count++;
                }
                $product->slug = $slug;
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_stock_level');
    }

    public function scopeRetail($query)
    {
        return $query->whereIn('usage_type', ['retail', 'both']);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock_quantity <= $this->min_stock_level;
    }

    public function getImageUrlAttribute(): string
    {
        if (!empty($this->image) && file_exists(public_path($this->image))) {
            return asset($this->image);
        }

        if (file_exists(public_path('placeholder.svg'))) {
            return asset('placeholder.svg');
        }

        return asset('placeholder.svg');
    }

    public function getFaviconUrlAttribute(): string
    {
        if (!empty($this->image) && file_exists(public_path($this->image))) {
            return asset($this->image);
        }

        return asset('logo.png');
    }

    public function getSizesArrayAttribute(): array
    {
        if (!empty($this->available_sizes)) {
            return array_values(array_filter(array_map('trim', explode(',', $this->available_sizes))));
        }
        return !empty($this->size) ? [$this->size] : ['Standard'];
    }

    public function getColorsArrayAttribute(): array
    {
        if (!empty($this->available_colors)) {
            return array_values(array_filter(array_map('trim', explode(',', $this->available_colors))));
        }
        return !empty($this->color) ? [$this->color] : [];
    }

    public function getPublicStockQuantityAttribute(): int
    {
        if (!is_null($this->display_stock_quantity)) {
            return (int) $this->display_stock_quantity;
        }
        return (int) $this->stock_quantity;
    }
}
