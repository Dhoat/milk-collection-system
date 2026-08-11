<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Shop extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'village_id',
        'shop_code',
        'name',
        'owner_name',
        'phone',
        'email',
        'address',
        'area',
        'status',
        'credit_limit',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'boolean',
        'credit_limit' => 'float',
    ];

    /**
     * Get the village/location associated with the shop.
     */
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    /**
     * Scope a query to only include active shops.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * Scope a query to apply search and filters.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('shop_code', 'like', "%{$search}%")
                    ->orWhere('owner_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('area', 'like', "%{$search}%");
            });
        })->when(isset($filters['status']) && $filters['status'] !== '', function ($q) use ($filters) {
            $q->where('status', (bool) $filters['status']);
        })->when($filters['village_id'] ?? null, function ($q, $villageId) {
            $q->where('village_id', $villageId);
        });
    }

    /*
     |--------------------------------------------------------------------------
     | Future Module Compatibility Placeholders
     |--------------------------------------------------------------------------
     | In future phases, these relationship stubs can be enabled:
     |
     | public function orders(): HasMany { return $this->hasMany(ShopOrder::class); }
     | public function deliveries(): HasManyThrough { ... }
     | public function payments(): HasMany { return $this->hasMany(ShopPayment::class); }
     */
}
