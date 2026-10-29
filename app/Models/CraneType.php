<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description', 'sort_order', 'is_active'])]
class CraneType extends Model
{
    /**
     * Cranes assigned to this type.
     *
     * @return HasMany<Crane, $this>
     */
    public function cranes(): HasMany
    {
        return $this->hasMany(Crane::class);
    }

    /**
     * Rental requests that specify this type.
     *
     * @return HasMany<RentalRequest, $this>
     */
    public function rentalRequests(): HasMany
    {
        return $this->hasMany(RentalRequest::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
