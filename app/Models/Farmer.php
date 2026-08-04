<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'village_id',
    'farmer_code',
    'name',
    'father_name',
    'mobile',
    'alternate_mobile',
    'address',
    'gender',
    'joining_date',
    'bank_name',
    'account_number',
    'ifsc_code',
    'status'
])]
class Farmer extends Model
{
    /** @use HasFactory<\Database\Factories\FarmerFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'joining_date' => 'date',
        ];
    }

    /**
     * Get the village that the farmer belongs to.
     */
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    /**
     * Get the milk collections for the farmer.
     */
    public function milkCollections(): HasMany
    {
        return $this->hasMany(MilkCollection::class);
    }
}
