<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'farmer_id',
    'collection_date',
    'shift',
    'milk_quantity',
    'fat',
    'snf',
    'rate',
    'amount',
    'notes'
])]
class MilkCollection extends Model
{
    /** @use HasFactory<\Database\Factories\MilkCollectionFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'collection_date' => 'date',
            'milk_quantity' => 'float',
            'fat' => 'float',
            'snf' => 'float',
            'rate' => 'float',
            'amount' => 'float',
        ];
    }

    /**
     * Get the farmer that belongs to the milk collection.
     */
    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }
}
