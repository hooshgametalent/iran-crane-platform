<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['province_id', 'name', 'slug'])]
class City extends Model
{
    /**
     * Province that contains this city.
     *
     * @return BelongsTo<Province, $this>
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * Rental requests located in this city.
     *
     * @return HasMany<RentalRequest, $this>
     */
    public function rentalRequests(): HasMany
    {
        return $this->hasMany(RentalRequest::class);
    }
}
