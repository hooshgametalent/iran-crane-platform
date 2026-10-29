<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'organization_id',
    'crane_type_id',
    'city_id',
    'title',
    'description',
    'required_capacity_kg',
    'starts_at',
    'ends_at',
    'submission_key',
    'status',
])]
class RentalRequest extends Model
{
    /**
     * User who created this request.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Requesting organization, if any.
     *
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Requested crane type, if specified.
     *
     * @return BelongsTo<CraneType, $this>
     */
    public function craneType(): BelongsTo
    {
        return $this->belongsTo(CraneType::class);
    }

    /**
     * City where the work will take place.
     *
     * @return BelongsTo<City, $this>
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Quotes submitted for this request.
     *
     * @return HasMany<RentalQuote, $this>
     */
    public function quotes(): HasMany
    {
        return $this->hasMany(RentalQuote::class);
    }

    /**
     * Bookings created from this request.
     *
     * @return HasMany<RentalBooking, $this>
     */
    public function bookings(): HasMany
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
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }
}
