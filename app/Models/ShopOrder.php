<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class ShopOrder extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_number',
        'shop_id',
        'order_date',
        'status',
        'subtotal',
        'discount',
        'total_amount',
        'stock_deducted',
        'created_by',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order_date' => 'date',
        'subtotal' => 'float',
        'discount' => 'float',
        'total_amount' => 'float',
        'stock_deducted' => 'boolean',
    ];

    /**
     * Get the shop that placed this order.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the user who created this order.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the items in this order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(ShopOrderItem::class, 'shop_order_id');
    }

    /**
     * Get the deliveries associated with this order.
     */
    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    /**
     * Get the latest delivery for this order.
     */
    public function latestDelivery()
    {
        return $this->hasOne(Delivery::class)->latestOfMany();
    }

    /**
     * Generate a unique order number (e.g. ORD-2026-0001).
     */
    public static function generateOrderNumber(): string
    {
        $year = date('Y');
        $prefix = "ORD-{$year}-";

        $latest = static::where('order_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->order_number, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Scope a query to apply search and filters.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('shop', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                          ->orWhere('shop_code', 'like', "%{$search}%")
                          ->orWhere('owner_name', 'like', "%{$search}%");
                    });
            });
        })->when($filters['status'] ?? null, function ($q, $status) {
            $q->where('status', $status);
        })->when($filters['shop_id'] ?? null, function ($q, $shopId) {
            $q->where('shop_id', $shopId);
        })->when($filters['date'] ?? null, function ($q, $date) {
            $q->whereDate('order_date', $date);
        });
    }

    /*
     |--------------------------------------------------------------------------
     | Future Module Compatibility Placeholders
     |--------------------------------------------------------------------------
     | In future phases, these relationship stubs can be enabled:
     |
     | public function delivery(): HasOne { return $this->hasOne(Delivery::class); }
     | public function payments(): HasMany { return $this->hasMany(Payment::class); }
     */
}
