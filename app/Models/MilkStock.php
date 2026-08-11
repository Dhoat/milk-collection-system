<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MilkStock extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_date',
        'type',
        'item_type',
        'quantity',
        'fat',
        'snf',
        'milk_receiving_id',
        'created_by',
        'source_or_reason',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'transaction_date' => 'date',
        'quantity' => 'float',
        'fat' => 'float',
        'snf' => 'float',
    ];

    /**
     * Get the associated milk receiving record, if any.
     */
    public function milkReceiving(): BelongsTo
    {
        return $this->belongsTo(MilkReceiving::class, 'milk_receiving_id');
    }

    /**
     * Get the user who logged this transaction.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Calculate total current available milk stock.
     */
    public static function getAvailableStock(): float
    {
        $in = static::where('type', 'in')->sum('quantity');
        $out = static::where('type', 'out')->sum('quantity');

        return round($in - $out, 2);
    }

    /**
     * Calculate opening stock prior to a given date.
     */
    public static function getOpeningStock(?string $date = null): float
    {
        $targetDate = $date ?: today()->toDateString();

        $in = static::where('transaction_date', '<', $targetDate)->where('type', 'in')->sum('quantity');
        $out = static::where('transaction_date', '<', $targetDate)->where('type', 'out')->sum('quantity');

        return round($in - $out, 2);
    }

    /**
     * Get today's total milk received (Stock IN).
     */
    public static function getTodayReceived(?string $date = null): float
    {
        $targetDate = $date ?: today()->toDateString();

        return round(static::where('type', 'in')->whereDate('transaction_date', $targetDate)->sum('quantity'), 2);
    }

    /**
     * Get today's total milk stock issued (Stock OUT).
     */
    public static function getTodayStockOut(?string $date = null): float
    {
        $targetDate = $date ?: today()->toDateString();

        return round(static::where('type', 'out')->whereDate('transaction_date', $targetDate)->sum('quantity'), 2);
    }
}
