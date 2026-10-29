<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'organization_id',
    'crane_type_id',
    'owner_reference',
    'manufacturer',
    'model',
    'manufacture_year',
    'capacity_kg',
    'boom_length_mm',
    'description',
    'status',
    'is_featured',
])]
class Crane extends Model
{
    /**
     * Organization that owns this crane.
     *
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Type assigned to this crane.
     *
     * @return BelongsTo<CraneType, $this>
     */
    public function craneType(): BelongsTo
    {
        return $this->belongsTo(CraneType::class);
    }

    /**
     * Areas served by this crane.
     *
     * @return HasMany<CraneServiceArea, $this>
     */
    public function serviceAreas(): HasMany
    {
        return $this->hasMany(CraneServiceArea::class);
    }

    /**
     * Images attached to this crane.
     *
     * @return HasMany<CraneImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(CraneImage::class);
    }

    /**
     * Quotes submitted for this crane.
     *
     * @return HasMany<RentalQuote, $this>
     */
    public function rentalQuotes(): HasMany
    {
        return $this->hasMany(RentalQuote::class);
    }

    /**
     * Bookings made for this crane.
     *
     * @return HasMany<RentalBooking, $this>
     */
    public function rentalBookings(): HasMany
    {
        return $this->hasMany(RentalBooking::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
        ];
    }
}
