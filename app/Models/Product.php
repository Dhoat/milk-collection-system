<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_code',
        'name',
        'category',
        'unit',
        'unit_price',
        'stock_quantity',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'unit_price' => 'float',
        'stock_quantity' => 'float',
        'status' => 'boolean',
    ];

    /**
     * Get the order items associated with this product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(ShopOrderItem::class);
    }

    /**
     * Get the effective available stock.
     * For raw milk, dynamically reads MilkStock::getAvailableStock().
     * For other products, reads stock_quantity attribute.
     */
    public function getAvailableStockAttribute(): float
    {
        if ($this->category === 'raw_milk') {
            return MilkStock::getAvailableStock();
        }

        return (float) $this->stock_quantity;
    }
}
