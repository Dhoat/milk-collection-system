<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Delivery extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'delivery_number',
        'shop_order_id',
        'shop_id',
        'delivery_date',
        'status',
        'delivery_address',
        'contact_person',
        'contact_phone',
        'assigned_to',
        'created_by',
        'notes',
        'dispatched_at',
        'delivered_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'delivery_date' => 'date',
        'dispatched_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Get the shop order associated with this delivery.
     */
    public function shopOrder(): BelongsTo
    {
        return $this->belongsTo(ShopOrder::class, 'shop_order_id');
    }

    /**
     * Get the shop outlet for this delivery.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    /**
     * Get the assigned delivery staff user.
     */
    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who created this delivery record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate a unique delivery number (e.g. DEL-2026-0001).
     */
    public static function generateDeliveryNumber(): string
    {
        $year = date('Y');
        $prefix = "DEL-{$year}-";

        $latest = static::where('delivery_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->delivery_number, strlen($prefix));
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
                $sub->where('delivery_number', 'like', "%{$search}%")
                    ->orWhereHas('shopOrder', function ($sq) use ($search) {
                        $sq->where('order_number', 'like', "%{$search}%");
                    })->orWhereHas('shop', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                          ->orWhere('shop_code', 'like', "%{$search}%");
                    });
            });
        })->when($filters['status'] ?? null, function ($q, $status) {
            $q->where('status', $status);
        })->when($filters['shop_id'] ?? null, function ($q, $shopId) {
            $q->where('shop_id', $shopId);
        })->when($filters['assigned_to'] ?? null, function ($q, $staffId) {
            $q->where('assigned_to', $staffId);
        })->when($filters['date'] ?? null, function ($q, $date) {
            $q->whereDate('delivery_date', $date);
        });
    }
}
