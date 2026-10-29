<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'rental_request_id',
    'crane_id',
    'organization_id',
    'submitted_by_user_id',
    'amount',
    'currency',
    'billing_unit',
    'transport_fee',
    'terms',
    'expires_at',
    'submission_key',
    'status',
])]
class RentalQuote extends Model
{
    /**
     * Request this quote answers.
     *
     * @return BelongsTo<RentalRequest, $this>
     */
    public function rentalRequest(): BelongsTo
    {
        return $this->belongsTo(RentalRequest::class);
    }

    /**
     * Crane offered for this request.
     *
     * @return BelongsTo<Crane, $this>
     */
    public function crane(): BelongsTo
    {
        return $this->belongsTo(Crane::class);
    }

    /**
     * Organization submitting this quote.
     *
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * User who submitted the quote, if retained.
     *
     * @return BelongsTo<User, $this>
     */
    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by_user_id');
    }

    /**
     * Booking produced when this quote is accepted.
     *
     * @return HasOne<RentalBooking, $this>
     */
    public function booking(): HasOne
    {
        return $this->hasOne(RentalBooking::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }
}
