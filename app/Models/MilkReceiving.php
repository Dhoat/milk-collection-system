<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MilkReceiving extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'village_id',
        'receiving_date',
        'shift',
        'expected_quantity',
        'received_quantity',
        'expected_fat',
        'received_fat',
        'expected_snf',
        'received_snf',
        'status',
        'verified_by',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'receiving_date' => 'date',
        'expected_quantity' => 'float',
        'received_quantity' => 'float',
        'expected_fat' => 'float',
        'received_fat' => 'float',
        'expected_snf' => 'float',
        'received_snf' => 'float',
    ];

    /**
     * Get the village that sent the milk batch.
     */
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    /**
     * Get the user who verified the receiving record.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the quantity difference/variance.
     */
    public function getQuantityVarianceAttribute(): float
    {
        return $this->received_quantity - $this->expected_quantity;
    }

    /**
     * Get the percentage variance.
     */
    public function getQuantityVariancePercentAttribute(): float
    {
        if ($this->expected_quantity <= 0) {
            return 0;
        }
        return ($this->quantity_variance / $this->expected_quantity) * 100;
    }
}
