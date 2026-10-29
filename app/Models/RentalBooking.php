<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'rental_quote_id',
    'rental_request_id',
    'crane_id',
    'customer_user_id',
    'customer_organization_id',
    'provider_organization_id',
    'starts_at',
    'ends_at',
    'quoted_amount',
    'transport_fee',
    'total_amount',
    'currency',
    'status',
    'cancelled_at',
])]
class RentalBooking extends Model
{
    /**
     * Accepted quote that created this booking.
     *
     * @return BelongsTo<RentalQuote, $this>
     */
    public function rentalQuote(): BelongsTo
    {
        return $this->belongsTo(RentalQuote::class);
    }

    /**
     * Original rental request.
     *
     * @return BelongsTo<RentalRequest, $this>
     */
    public function rentalRequest(): BelongsTo
    {
        return $this->belongsTo(RentalRequest::class);
    }

    /**
     * Crane reserved by this booking.
     *
     * @return BelongsTo<Crane, $this>
     */
    public function crane(): BelongsTo
    {
        return $this->belongsTo(Crane::class);
    }

    /**
     * User receiving the rental service.
     *
     * @return BelongsTo<User, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_user_id');
    }

    /**
     * Customer organization, if the booking is made for an organization.
     *
     * @return BelongsTo<Organization, $this>
     */
    public function customerOrganization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'customer_organization_id');
    }

    /**
     * Organization supplying the crane.
     *
     * @return BelongsTo<Organization, $this>
     */
    public function providerOrganization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'provider_organization_id');
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
            'cancelled_at' => 'datetime',
        ];
    }
}
